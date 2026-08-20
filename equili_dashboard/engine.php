<?php
// 1. DATABASE CONNECTION
$host = 'sql113.infinityfree.com'; 
$db = 'if0_42705557_equili_db'; 
$u = 'if0_42705557'; 
$p = 'your_password'; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $u, $p);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

/* ==========================================================================
   SECTION 2: ACTION HANDLERS (POST/GET)
   ========================================================================== */

// --- EXPORT TO CSV ---
if (isset($_GET['export'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="Equili_Inventory_'.date('Y-m-d').'.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['SKU', 'Item Name', 'Category', 'Quantity on Hand', 'Min Threshold', 'Unit Price', 'Total Valuation']);
    $rows = $pdo->query("SELECT sku, item_name, category, expected_stock, min_threshold, unit_price, (expected_stock * unit_price) as val FROM inventory")->fetchAll();
    foreach ($rows as $row) fputcsv($output, $row);
    fclose($output);
    exit();
}

// --- ADD NEW ASSET ---
if (isset($_POST['add_item'])) {
    $packaging = (int)$_POST['packaging_unit'] ?: 1;
    $boxes = (int)$_POST['initial_boxes'] ?: 0;
    $loose = (int)$_POST['initial_loose'] ?: 0;
    $total_units = ($boxes * $packaging) + $loose;

    $sql = "INSERT INTO inventory (sku, item_name, category, expected_stock, amount_received, packaging_unit, min_threshold, unit_price) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_POST['sku'], $_POST['item_name'], $_POST['category'], $total_units, $total_units, $packaging, $_POST['min_threshold'], $_POST['unit_price']]);
    header("Location: inventory.php?success=added");
    exit();
}

// --- EDIT ASSET ---
if (isset($_POST['edit_item'])) {
    $id = $_POST['item_id'];
    $packaging = (int)$_POST['packaging_unit'] ?: 1;
    $total_units = (int)$_POST['current_stock'];

    $sql = "UPDATE inventory SET sku=?, item_name=?, category=?, expected_stock=?, packaging_unit=?, min_threshold=?, unit_price=? WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_POST['sku'], $_POST['item_name'], $_POST['category'], $total_units, $packaging, $_POST['min_threshold'], $_POST['unit_price'], $id]);
    header("Location: inventory.php?success=updated");
    exit();
}

// --- RECORD ITEM USAGE (ISSUANCE) ---
if (isset($_POST['issue_item'])) {
    $item_id = $_POST['item_id'];
    $qty = (int)$_POST['quantity_to_issue'];
    $date = !empty($_POST['issued_date']) ? $_POST['issued_date'] : date('Y-m-d');

    $check = $pdo->prepare("SELECT expected_stock FROM inventory WHERE id = ?");
    $check->execute([$item_id]);
    $current = $check->fetch();

    if ($current && $current['expected_stock'] >= $qty) {
        $pdo->prepare("UPDATE inventory SET expected_stock = expected_stock - ? WHERE id = ?")->execute([$qty, $item_id]);
        $pdo->prepare("INSERT INTO stock_issuances (item_id, quantity_issued, issued_at) VALUES (?, ?, ?)")->execute([$item_id, $qty, $date]);
        header("Location: index.php?success=issued");
    } else {
        header("Location: index.php?error=insufficient_stock");
    }
    exit();
}

// --- SUBMIT AUDIT RECONCILIATION ---
if (isset($_POST['submit_audit'])) {
    $item_id = $_POST['item_id'];
    $boxes = (int)$_POST['physical_boxes'];
    $loose = (int)$_POST['physical_loose'];
    
    $stmt = $pdo->prepare("SELECT expected_stock, packaging_unit, unit_price FROM inventory WHERE id = ?");
    $stmt->execute([$item_id]);
    $item = $stmt->fetch();

    $total_physical = ($boxes * $item['packaging_unit']) + $loose;
    $variance = $total_physical - $item['expected_stock'];
    $status = ($variance == 0) ? 'Balanced' : 'Discrepancy';

    $log = $pdo->prepare("INSERT INTO audit_logs (item_id, physical_count, variance, status) VALUES (?, ?, ?, ?)");
    $log->execute([$item_id, $total_physical, $variance, $status]);
    
    if(isset($_POST['sync_inventory'])) {
        $update = $pdo->prepare("UPDATE inventory SET expected_stock = ? WHERE id = ?");
        $update->execute([$total_physical, $item_id]);
    }
    header("Location: audit.php?success=audit_complete");
    exit();
}

// --- DELETE ASSET ---
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM inventory WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: inventory.php?success=deleted");
    exit();
}

/* ==========================================================================
   SECTION 3: GLOBAL DATA FETCHING (This runs on every page load)
   ========================================================================== */

// 1. Fetch all items
$items = $pdo->query("SELECT * FROM inventory ORDER BY item_name ASC")->fetchAll();

// 2. Dashboard Totals
$total_valuation = $pdo->query("SELECT SUM(expected_stock * unit_price) FROM inventory")->fetchColumn() ?: 0.00;
$low_stock_items = $pdo->query("SELECT * FROM inventory WHERE expected_stock <= min_threshold ORDER BY expected_stock ASC LIMIT 4")->fetchAll() ?: [];

// 3. THE MISSING VARIABLE: Total Stock on Hand
$total_stock_on_hand = $pdo->query("SELECT SUM(expected_stock) FROM inventory")->fetchColumn() ?: 0;

// 4. Current vs Previous Week Analysis
$this_week = $pdo->query("SELECT SUM(quantity_issued) FROM stock_issuances WHERE YEARWEEK(issued_at, 1) = YEARWEEK(CURDATE(), 1)")->fetchColumn() ?: 0;
$prev_week = $pdo->query("SELECT SUM(quantity_issued) FROM stock_issuances WHERE YEARWEEK(issued_at, 1) = YEARWEEK(CURDATE() - INTERVAL 1 WEEK, 1)")->fetchColumn() ?: 0;

// 5. Monthly Aggregates
$monthly_usage = $pdo->query("SELECT DATE_FORMAT(issued_at, '%M %Y') as month, DATE_FORMAT(issued_at, '%Y-%m') as month_val, SUM(quantity_issued) as total FROM stock_issuances GROUP BY month, month_val ORDER BY month_val DESC")->fetchAll() ?: [];

// 6. Audit Data
$current_month = date('Y-m');
$active_audits = $pdo->prepare("SELECT i.*, a.physical_count, a.variance, a.status as audit_status FROM inventory i LEFT JOIN audit_logs a ON i.id = a.item_id AND DATE_FORMAT(a.audit_date, '%Y-%m') = ? ORDER BY i.item_name ASC");
$active_audits->execute([$current_month]);
$active_list = $active_audits->fetchAll();

// 7. Audit History
$history_query = $pdo->prepare("SELECT DATE_FORMAT(audit_date, '%M %Y') as month_label, DATE_FORMAT(audit_date, '%Y-%m') as month_val FROM audit_logs WHERE DATE_FORMAT(audit_date, '%Y-%m') != ? GROUP BY month_val, month_label ORDER BY month_val DESC");
$history_query->execute([$current_month]);
$history_list = $history_query->fetchAll() ?: [];
?>
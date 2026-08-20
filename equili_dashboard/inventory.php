<?php include 'engine.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equili | Inventory Registry</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; } body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="flex flex-col lg:flex-row min-h-screen bg-[#020617]" x-data="{ 
    addModal: false, 
    editModal: false, 
    item: { id: '', name: '', sku: '', cat: '', stock: 0, packaging: 1, threshold: 0, price: 0 } 
}">

    <?php include 'sidebar.php'; ?>

    <main class="flex-1 p-6 lg:p-16 overflow-y-auto bg-slate-50 lg:rounded-l-[4rem] shadow-[-30px_0_60px_rgba(0,0,0,0.3)] mb-20 lg:mb-0 transition-all duration-500">
        <!-- Header -->
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-16 gap-8">
            <div>
                <h2 class="text-5xl font-black text-slate-900 tracking-tight italic">Asset Registry</h2>
                <p class="text-slate-500 font-medium mt-2">Manage asset identities, unit pricing, and thresholds.</p>
            </div>
            <button @click="addModal = true" class="w-full md:w-auto bg-indigo-600 text-white px-10 py-5 rounded-3xl font-black shadow-2xl shadow-indigo-100 hover:scale-105 active:scale-95 transition-all flex justify-center items-center space-x-3 text-lg">
                <i data-lucide="package-plus" class="w-6 h-6"></i>
                <span>Provision Asset</span>
            </button>
        </header>

        <!-- Responsive Table Wrapper -->
        <div class="bg-white rounded-[3.5rem] border border-slate-200 shadow-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[1000px]">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-10 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[4px]">Asset Identity</th>
                            <th class="px-10 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[4px] text-center">Packaging</th>
                            <th class="px-10 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[4px] text-center">Current Hand</th>
                            <th class="px-10 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[4px]">Valuation</th>
                            <th class="px-10 py-6 text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php foreach ($items as $row): 
                            $total = $row['expected_stock']; $pack = $row['packaging_unit'];
                            $boxes = floor($total / $pack); $loose = $total % $pack;
                            $isLow = $total <= $row['min_threshold'];
                        ?>
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-10 py-8">
                                <p class="font-black text-slate-900 text-xl tracking-tight"><?= htmlspecialchars($row['item_name']) ?></p>
                                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1"><?= $row['sku'] ?> • <?= $row['category'] ?></p>
                            </td>
                            <td class="px-10 py-8 text-center">
                                <span class="bg-slate-100 text-slate-600 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-wider border border-slate-200">
                                    <?= $pack ?> Units / Box
                                </span>
                            </td>
                            <td class="px-10 py-8 text-center">
                                <p class="font-black text-2xl tracking-tighter <?= $isLow ? 'text-rose-600' : 'text-slate-900' ?>">
                                    <?= $boxes ?>B + <?= $loose ?>L
                                </p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase mt-1 tracking-tighter">(Total: <?= $total ?> Units)</p>
                            </td>
                            <td class="px-10 py-8">
                                <p class="font-black text-slate-900 text-lg tracking-tighter italic">₵<?= number_format($row['unit_price'] * $total, 2) ?></p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Market Value</p>
                            </td>
                            <td class="px-10 py-8 text-right space-x-4">
                                <button @click="editModal = true; item = {id:'<?= $row['id'] ?>', name:'<?= addslashes($row['item_name']) ?>', sku:'<?= $row['sku'] ?>', cat:'<?= $row['category'] ?>', stock:'<?= $row['expected_stock'] ?>', packaging:'<?= $row['packaging_unit'] ?>', threshold:'<?= $row['min_threshold'] ?>', price:'<?= $row['unit_price'] ?>'}" 
                                        class="text-indigo-600 hover:scale-125 transition-transform inline-block">
                                    <i data-lucide="pencil-line" class="w-6 h-6"></i>
                                </button>
                                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Archive asset from registry?')" class="text-rose-400 hover:scale-125 transition-transform inline-block">
                                    <i data-lucide="trash-2" class="w-6 h-6"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- MODAL: PROVISION ASSET (ADD) -->
    <div x-show="addModal" x-transition x-cloak class="fixed inset-0 z-[200] flex items-center justify-center bg-slate-950/90 backdrop-blur-xl p-6">
        <div @click.away="addModal = false" class="bg-white rounded-[4rem] shadow-2xl w-full max-w-2xl overflow-hidden border border-white/10">
            <div class="bg-indigo-600 p-12 text-white">
                <h3 class="text-4xl font-black tracking-tight italic">Provision Asset</h3>
                <p class="text-indigo-100 text-sm mt-2 opacity-80 font-medium">Record a new physical asset class into the digital vault.</p>
            </div>
            <form action="engine.php" method="POST" class="p-12 grid grid-cols-2 gap-6">
                <input type="hidden" name="add_item" value="1">
                <div class="col-span-2">
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest block mb-2">Item Identity Name</label>
                    <input type="text" name="item_name" required placeholder="e.g. Visa Gold Debit Cards" class="w-full bg-slate-50 border-2 border-slate-100 rounded-3xl px-8 py-5 font-bold focus:border-indigo-600 outline-none text-slate-900">
                </div>
                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest block mb-2">SKU / Code</label>
                    <input type="text" name="sku" required placeholder="e.g. CRD-001" class="w-full bg-slate-50 border-2 border-slate-100 rounded-3xl px-8 py-5 font-bold outline-none text-slate-900">
                </div>
                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest block mb-2">Category</label>
                    <input type="text" name="category" required placeholder="e.g. Security Items" class="w-full bg-slate-50 border-2 border-slate-100 rounded-3xl px-8 py-5 font-bold outline-none text-slate-900">
                </div>
                
                <div class="col-span-2 bg-indigo-50/50 p-8 rounded-3xl grid grid-cols-3 gap-6 border border-indigo-100/50">
                    <div><label class="text-[10px] font-black text-indigo-400 uppercase block mb-1">Items/Box</label><input type="number" name="packaging_unit" value="1" placeholder="1" class="w-full rounded-2xl border-none py-3 px-4 font-bold text-slate-900 shadow-sm"></div>
                    <div><label class="text-[10px] font-black text-indigo-400 uppercase block mb-1">Boxes Rec.</label><input type="number" name="initial_boxes" value="0" placeholder="0" class="w-full rounded-2xl border-none py-3 px-4 font-bold text-slate-900 shadow-sm"></div>
                    <div><label class="text-[10px] font-black text-indigo-400 uppercase block mb-1">Loose Units</label><input type="number" name="initial_loose" value="0" placeholder="0" class="w-full rounded-2xl border-none py-3 px-4 font-bold text-slate-900 shadow-sm"></div>
                </div>

                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest block mb-2">Unit Price (₵)</label>
                    <input type="number" step="0.01" name="unit_price" required placeholder="0.00" class="w-full bg-slate-50 border-2 border-slate-100 rounded-3xl px-8 py-5 font-bold outline-none text-slate-900">
                </div>
                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest block mb-2">Min Threshold</label>
                    <input type="number" name="min_threshold" value="10" placeholder="10" class="w-full bg-slate-50 border-2 border-slate-100 rounded-3xl px-8 py-5 font-bold outline-none text-slate-900">
                </div>

                <div class="col-span-2 flex justify-end space-x-6 pt-6">
                    <button type="button" @click="addModal = false" class="text-slate-400 font-bold uppercase tracking-widest text-[11px]">Discard</button>
                    <button type="submit" class="bg-slate-950 text-white px-12 py-5 rounded-3xl font-black text-lg hover:bg-indigo-600 transition-all shadow-xl shadow-indigo-100">Authorize Provision</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: MODIFY RECORD (EDIT) -->
    <div x-show="editModal" x-transition x-cloak class="fixed inset-0 z-[200] flex items-center justify-center bg-slate-950/90 backdrop-blur-xl p-6">
        <div @click.away="editModal = false" class="bg-white rounded-[4rem] shadow-2xl w-full max-w-2xl overflow-hidden border border-white/10">
            <div class="bg-slate-900 p-12 text-white">
                <h3 class="text-3xl font-black tracking-tight italic">Modify Registry</h3>
                <p class="text-slate-400 text-sm mt-2 opacity-80 font-medium">Update identities, stock balances, or market pricing.</p>
            </div>
            <form action="engine.php" method="POST" class="p-12 space-y-6">
                <input type="hidden" name="edit_item" value="1">
                <input type="hidden" name="item_id" :value="item.id">
                
                <div class="grid grid-cols-2 gap-6">
                    <!-- Column 1: Name -->
                    <div class="col-span-2">
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest block mb-2">Asset Name</label>
                        <input type="text" name="item_name" x-model="item.name" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-3xl px-8 py-4 font-bold text-slate-900 outline-none focus:border-indigo-600">
                    </div>

                    <!-- Column 2: SKU & Category -->
                    <div>
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest block mb-2">SKU Code</label>
                        <input type="text" name="sku" x-model="item.sku" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-3xl px-8 py-4 font-bold text-slate-900 outline-none">
                    </div>
                    <div>
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest block mb-2">Category</label>
                        <input type="text" name="category" x-model="item.cat" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-3xl px-8 py-4 font-bold text-slate-900 outline-none">
                    </div>

                    <!-- Column 3: Stock & Threshold -->
                    <div>
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest block mb-2">Total Stock (Units)</label>
                        <input type="number" name="current_stock" x-model="item.stock" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-3xl px-8 py-4 font-bold text-indigo-600 outline-none">
                    </div>
                    <div>
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest block mb-2">Min. Threshold</label>
                        <input type="number" name="min_threshold" x-model="item.threshold" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-3xl px-8 py-4 font-bold text-rose-500 outline-none">
                    </div>

                    <!-- Column 4: Price & Packaging -->
                    <div>
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest block mb-2">Unit Price (₵)</label>
                        <input type="number" step="0.01" name="unit_price" x-model="item.price" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-3xl px-8 py-4 font-bold text-slate-900 outline-none focus:border-indigo-600">
                    </div>
                    <div>
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest block mb-2">Units Per Box</label>
                        <input type="number" name="packaging_unit" x-model="item.packaging" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-3xl px-8 py-4 font-bold text-slate-900 outline-none">
                    </div>
                </div>

                <div class="flex justify-end space-x-6 pt-6">
                    <button type="button" @click="editModal = false" class="text-slate-400 font-bold uppercase tracking-widest text-[11px]">Cancel</button>
                    <button type="submit" class="bg-indigo-600 text-white px-10 py-4 rounded-3xl font-black text-lg hover:bg-slate-900 transition-all shadow-xl shadow-indigo-100">Commit Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>window.addEventListener('DOMContentLoaded', () => { lucide.createIcons(); });</script>
</body>
</html>
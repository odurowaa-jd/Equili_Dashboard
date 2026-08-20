<?php include 'engine.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equili | Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; } 
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glow-cobalt { box-shadow: 0 20px 50px -12px rgba(79, 70, 229, 0.4); }
    </style>
</head>
<body class="flex flex-col lg:flex-row min-h-screen bg-[#020617]" x-data="{ issueModal: false, showHistory: false }">

    <?php include 'sidebar.php'; ?>

    <main class="flex-1 p-6 lg:p-16 overflow-y-auto bg-slate-50 lg:rounded-l-[4rem] shadow-[-30px_0_60px_rgba(0,0,0,0.3)] mb-20 lg:mb-0 transition-all duration-500">
        <!-- Header -->
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-16 gap-8">
            <div>
                <h2 class="text-5xl font-black text-slate-900 tracking-tight leading-none">System Pulse</h2>
                <p class="text-slate-500 font-medium mt-2">Inventory health and issuance velocity.</p>
            </div>
            <button @click="issueModal = true" class="w-full md:w-auto bg-indigo-600 text-white px-10 py-5 rounded-3xl font-black shadow-2xl shadow-indigo-200 hover:scale-105 active:scale-95 transition-all flex justify-center items-center space-x-3">
                <i data-lucide="plus" class="w-5 h-5"></i>
                <span>Record Usage</span>
            </button>
        </header>

        <!-- Dynamic Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-10">
            
            <!-- Analysis Card (Fixed Layout) -->
            <div class="xl:col-span-8 bg-[#020617] p-8 md:p-12 rounded-[4rem] text-white glow-cobalt relative overflow-hidden flex flex-col justify-between min-h-[500px]">
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <p class="text-indigo-400 text-[10px] font-black uppercase tracking-[4px] mb-2">Consumption Velocity</p>
                        <h3 class="text-4xl font-black italic tracking-tighter leading-none">Weekly Flow</h3>
                    </div>
                    <div class="text-right">
                        <?php 
                            $divisor = ($total_stock_on_hand + $this_week);
                            $usage_ratio = ($divisor > 0) ? ($this_week / $divisor) * 100 : 0;
                        ?>
                        <span class="block text-4xl font-black text-indigo-400">
                            <?= round($usage_ratio, 1) ?>%
                        </span>
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1">Vault Turnover</p>
                    </div>
                </div>

                <!-- The Progress Bar -->
                <div class="my-10 space-y-6">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                            Current Week: <?= $this_week ?> Units Out
                        </span>
                        <span class="text-xs font-black text-indigo-400 uppercase">
                            Stock Depletion Rate
                        </span>
                    </div>
                    <div class="w-full h-6 bg-slate-800 rounded-full overflow-hidden p-1.5 border border-white/5">
                        <div class="h-full bg-gradient-to-r from-indigo-600 via-indigo-400 to-violet-400 rounded-full transition-all duration-1000" 
                             style="width: <?= max(2, $usage_ratio) ?>%"></div> 
                    </div>
                </div>

                <!-- Monthly Summary Toggle -->
                <div x-data="{ show: false }">
                    <button @click="show = !show" class="text-[10px] font-black text-slate-500 uppercase tracking-widest flex items-center hover:text-white transition">
                        <i data-lucide="bar-chart-2" class="w-4 h-4 mr-2"></i>
                        Historical Usage Logs
                    </button>
                    <div x-show="show" x-transition class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4 border-t border-white/5 pt-6">
                        <?php foreach($monthly_usage as $m): ?>
                        <div class="bg-white/5 p-4 rounded-2xl border border-white/5">
                            <p class="text-[9px] text-slate-500 uppercase font-bold"><?= $m['month'] ?></p>
                            <p class="text-lg font-black"><?= $m['total'] ?> Units</p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Priority Alerts & Value (Right Side) -->
            <div class="xl:col-span-4 space-y-10">
                <div class="bg-white p-10 rounded-[3.5rem] border border-rose-100 shadow-xl shadow-rose-50 flex flex-col justify-between min-h-[300px]">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-[11px] font-black uppercase tracking-widest text-rose-500">Priority Restock</h3>
                        <div class="w-2 h-2 bg-rose-500 rounded-full animate-ping"></div>
                    </div>
                    <div class="space-y-4 mb-8">
                        <?php foreach($low_stock_items as $low): ?>
                        <div class="flex justify-between items-center p-5 bg-rose-50/50 rounded-2xl border border-rose-100/30">
                            <span class="text-sm font-bold text-slate-800"><?= htmlspecialchars($low['item_name']) ?></span>
                            <span class="text-xs font-black text-rose-600"><?= $low['expected_stock'] ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <a href="inventory.php" class="text-center text-[10px] font-black uppercase text-rose-600 tracking-widest py-4 bg-rose-50 rounded-2xl hover:bg-rose-600 hover:text-white transition-all">Manage Assets</a>
                </div>

                <div class="bg-white p-12 rounded-[3.5rem] border border-slate-200 shadow-xl shadow-slate-100">
                    <p class="text-slate-400 text-[11px] font-black uppercase tracking-[4px] mb-3">Total Value</p>
                    <h3 class="text-5xl font-black text-slate-900 tracking-tighter italic leading-none">₵<?= number_format($total_valuation, 2) ?></h3>
                </div>
            </div>
        </div>
    </main>

    <!-- RECORD USAGE MODAL -->
    <div x-show="issueModal" x-transition x-cloak class="fixed inset-0 z-[200] flex items-center justify-center bg-slate-950/90 backdrop-blur-xl p-6">
        <div @click.away="issueModal = false" class="bg-white rounded-[4rem] shadow-2xl w-full max-w-xl overflow-hidden">
            <div class="bg-indigo-600 p-12 text-white">
                <h3 class="text-3xl font-black tracking-tight leading-none">Record Issuance</h3>
                <p class="text-indigo-100 text-sm mt-2 opacity-80">Log a physical asset withdrawal.</p>
            </div>
            <form action="index.php" method="POST" class="p-12 space-y-8">
                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest block mb-3">Select Item</label>
                    <select name="item_id" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-3xl px-8 py-5 font-bold text-slate-900 outline-none focus:border-indigo-600">
                        <?php foreach($items as $i): ?>
                        <option value="<?= $i['id'] ?>"><?= $i['item_name'] ?> (Avail: <?= $i['expected_stock'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-8">
                    <div>
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest block mb-3">Qty</label>
                        <input type="number" name="quantity_to_issue" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-3xl px-8 py-5 text-2xl font-black outline-none focus:border-indigo-600">
                    </div>
                    <div>
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest block mb-3">Date</label>
                        <input type="date" name="issued_date" value="<?= date('Y-m-d') ?>" class="w-full bg-slate-50 border-2 border-slate-100 rounded-3xl px-6 py-5 font-bold outline-none">
                    </div>
                </div>
                <button type="submit" name="issue_item" class="w-full bg-slate-900 text-white py-6 rounded-3xl font-black text-xl hover:bg-indigo-600 transition-all shadow-xl shadow-indigo-100">
                    Verify & Issue
                </button>
            </form>
        </div>
    </div>

    <script>window.addEventListener('DOMContentLoaded', () => { lucide.createIcons(); });</script>
</body>
</html>
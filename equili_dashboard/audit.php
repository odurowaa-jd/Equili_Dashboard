<?php include 'engine.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equili | Reconciliation Ledger</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; } body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="flex flex-col lg:flex-row min-h-screen bg-[#020617]" x-data="{ 
    auditModal: false, 
    item: { id: '', name: '', expected: 0, packaging: 1 } 
}">

    <?php include 'sidebar.php'; ?>

    <main class="flex-1 p-6 lg:p-16 overflow-y-auto bg-slate-50 lg:rounded-l-[4rem] shadow-[-30px_0_60px_rgba(0,0,0,0.3)] mb-20 lg:mb-0">
        <!-- Header -->
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-16 gap-8">
            <div>
                <h2 class="text-5xl font-black text-slate-900 tracking-tight">Audit Terminal</h2>
                <p class="text-slate-500 font-medium mt-2">Physical vs. Digital vault reconciliation ledger.</p>
            </div>
            <div class="bg-white border border-slate-200 px-8 py-4 rounded-3xl shadow-xl flex items-center space-x-4">
                <span class="w-3 h-3 bg-indigo-600 rounded-full animate-pulse"></span>
                <span class="text-[11px] font-black uppercase tracking-widest text-slate-600">Reporting: <?= date('F Y') ?></span>
            </div>
        </header>

        <!-- Active Audit Cycle Table -->
        <section class="mb-20">
            <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-[4px] mb-8 px-4 flex items-center">
                <i data-lucide="zap" class="w-4 h-4 mr-2 text-indigo-500"></i> Active Reporting Cycle
            </h3>
            <div class="bg-white rounded-[3.5rem] border border-slate-200 shadow-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[900px]">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-10 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[4px]">Asset Details</th>
                                <th class="px-10 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[4px] text-center">System Log</th>
                                <th class="px-10 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[4px] text-center">Physical Count</th>
                                <th class="px-10 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[4px] text-center">Variance</th>
                                <th class="px-10 py-6 text-right"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php foreach($active_list as $row): 
                                $statusColor = ($row['audit_status'] == 'Balanced') ? 'text-emerald-500' : 'text-rose-500';
                                $statusBg = ($row['audit_status'] == 'Balanced') ? 'bg-emerald-50' : 'bg-rose-50';
                            ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-10 py-8">
                                    <p class="font-black text-slate-900 text-lg tracking-tight"><?= $row['item_name'] ?></p>
                                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest"><?= $row['sku'] ?></p>
                                </td>
                                <td class="px-10 py-8 text-center font-mono font-bold text-slate-400"><?= $row['expected_stock'] ?></td>
                                <td class="px-10 py-8 text-center font-mono font-black text-slate-900"><?= $row['physical_count'] ?? '---' ?></td>
                                <td class="px-10 py-8 text-center">
                                    <?php if($row['audit_status']): ?>
                                        <span class="font-black text-xl <?= $row['variance'] < 0 ? 'text-rose-600' : ($row['variance'] > 0 ? 'text-amber-600' : 'text-emerald-600') ?>">
                                            <?= ($row['variance'] > 0 ? '+' : '') . $row['variance'] ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-slate-300">--</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-10 py-8 text-right">
                                    <?php if(!$row['audit_status']): ?>
                                        <button @click="auditModal = true; item = { id: '<?= $row['id'] ?>', name: '<?= addslashes($row['item_name']) ?>', expected: '<?= $row['expected_stock'] ?>', packaging: '<?= $row['packaging_unit'] ?>' }"
                                                class="bg-slate-900 text-white px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 transition-all">
                                            Seal Audit
                                        </button>
                                    <?php else: ?>
                                        <div class="inline-flex items-center space-x-2 <?= $statusBg ?> <?= $statusColor ?> px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest">
                                            <i data-lucide="shield-check" class="w-4 h-4"></i>
                                            <span><?= $row['audit_status'] ?></span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Historical Archive (Collapsible) -->
        <section>
            <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-[4px] mb-8 px-4 flex items-center">
                <i data-lucide="archive" class="w-4 h-4 mr-2"></i> Vault Archives
            </h3>
            <div class="space-y-6">
                <?php foreach($history_list as $history): ?>
                <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full px-10 py-8 flex justify-between items-center hover:bg-slate-50 transition">
                        <div class="flex items-center space-x-6 text-left">
                            <div class="p-4 bg-slate-50 rounded-2xl text-slate-400"><i data-lucide="calendar" class="w-6 h-6"></i></div>
                            <div>
                                <h4 class="font-black text-slate-900 uppercase tracking-tighter text-2xl"><?= $history['month_label'] ?></h4>
                                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Closed Audit Session</p>
                            </div>
                        </div>
                        <i data-lucide="chevron-down" class="text-slate-300 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <!-- Log Table Inside History -->
                    <div x-show="open" x-transition class="border-t border-slate-100 p-10 bg-slate-50/30">
                  <table class="w-full text-left text-sm">
                      <tbody class="divide-y divide-slate-100">
                          <?php 
                          $m_val = $history['month_val'];
                          $logs = $pdo->query("SELECT i.item_name, a.variance, a.status FROM audit_logs a JOIN inventory i ON a.item_id = i.id WHERE DATE_FORMAT(a.audit_date, '%Y-%m') = '$m_val'")->fetchAll();
                          foreach($logs as $log): ?>
                          <tr class="bg-white">
                              <td class="py-4 px-6 font-bold text-slate-700"><?= $log['item_name'] ?></td>
                              <td class="py-4 px-6 text-center font-mono font-black <?= $log['variance'] == 0 ? 'text-emerald-500' : 'text-rose-500' ?>"><?= ($log['variance'] > 0 ? '+' : '') . $log['variance'] ?></td>
                              <td class="py-4 px-6 text-right text-[10px] font-black uppercase tracking-widest text-slate-400"><?= $log['status'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <!-- MODAL: SEAL AUDIT (Box + Loose) -->
    <div x-show="auditModal" x-transition x-cloak class="fixed inset-0 z-[200] flex items-center justify-center bg-slate-950/90 backdrop-blur-xl p-6">
        <div class="bg-white rounded-[4rem] shadow-2xl w-full max-w-xl overflow-hidden border border-white/10">
            <div class="bg-indigo-600 p-12 text-white relative">
                <p class="text-indigo-200 text-[10px] font-black uppercase tracking-widest mb-2">Vault Verification</p>
                <h3 class="text-4xl font-black italic tracking-tight leading-none" x-text="item.name"></h3>
                <div class="mt-8 flex space-x-12">
                    <div><p class="text-[10px] uppercase font-black text-indigo-300">Digital Expectation</p><p class="text-3xl font-black" x-text="item.expected"></p></div>
                    <div><p class="text-[10px] uppercase font-black text-indigo-300">Pack Logic</p><p class="text-3xl font-black" x-text="item.packaging + '/box'"></p></div>
                </div>
            </div>
            <form action="engine.php" method="POST" class="p-12 space-y-8">
                <input type="hidden" name="submit_audit" value="1">
                <input type="hidden" name="item_id" :value="item.id">
                <div class="grid grid-cols-2 gap-8">
                    <div class="bg-slate-50 p-6 rounded-3xl border-2 border-slate-100 focus-within:border-indigo-600 transition-all">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Physical Boxes</label>
                        <input type="number" name="physical_boxes" value="0" class="w-full bg-transparent text-4xl font-black outline-none text-slate-900">
                    </div>
                    <div class="bg-slate-50 p-6 rounded-3xl border-2 border-slate-100 focus-within:border-indigo-600 transition-all">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Loose Items</label>
                        <input type="number" name="physical_loose" value="0" class="w-full bg-transparent text-4xl font-black outline-none text-slate-900">
                    </div>
                </div>
                <div class="flex items-center space-x-4 p-6 bg-rose-50 rounded-3xl border border-rose-100">
                    <input type="checkbox" name="sync_inventory" id="sync" class="w-6 h-6 rounded-lg text-rose-600">
                    <label for="sync" class="text-xs font-bold text-rose-900 italic leading-tight">Authorize Sync: Update system stock levels to match physical counts instantly.</label>
                </div>
                <button type="submit" class="w-full bg-slate-950 text-white py-6 rounded-3xl font-black text-xl hover:bg-indigo-600 transition-all">Finalize Reconciliation</button>
            </form>
        </div>
    </div>

    <script>window.addEventListener('DOMContentLoaded', () => { lucide.createIcons(); });</script>
</body>
</html>
<?php $current_page = basename($_SERVER['PHP_SELF']); ?>
<!-- Fonts & Icons -->
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@800&family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
<script src="https://unpkg.com/lucide@latest"></script>

<aside class="hidden lg:flex w-80 bg-[#020617] text-slate-400 p-10 flex-col h-screen sticky top-0 border-r border-indigo-900/20 shrink-0">
    <div class="mb-16">
        <div class="flex items-center space-x-3 mb-2">
            <div class="bg-indigo-600 p-2 rounded-xl shadow-[0_0_20px_rgba(79,70,229,0.4)]">
                <i data-lucide="layers" class="text-white w-5 h-5"></i>
            </div>
            <!-- Exquisite Brand Font -->
            <h1 class="text-2xl font-[Montserrat] font-black text-white tracking-tighter uppercase italic">
                Equili<span class="text-indigo-500">.</span>
            </h1>
        </div>
        <p class="text-[10px] uppercase tracking-[4px] text-slate-600 font-bold ml-1">Asset Intelligence</p>
    </div>

    <nav class="space-y-4 flex-1">
        <a href="index.php" class="flex items-center space-x-4 px-6 py-4 rounded-2xl transition-all duration-300 <?= $current_page == 'index.php' ? 'bg-indigo-600 text-white shadow-[0_20px_40px_-10px_rgba(79,70,229,0.4)] scale-105' : 'hover:bg-slate-900 hover:text-white' ?>">
            <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
            <span class="font-bold text-sm">Dashboard</span>
        </a>
        
        <a href="inventory.php" class="flex items-center space-x-4 px-6 py-4 rounded-2xl transition-all duration-300 <?= $current_page == 'inventory.php' ? 'bg-indigo-600 text-white shadow-[0_20px_40px_-10px_rgba(79,70,229,0.4)] scale-105' : 'hover:bg-slate-900 hover:text-white' ?>">
            <i data-lucide="package" class="w-5 h-5"></i>
            <span class="font-bold text-sm">Inventory</span>
        </a>

        <a href="audit.php" class="flex items-center space-x-4 px-6 py-4 rounded-2xl transition-all duration-300 <?= $current_page == 'audit.php' ? 'bg-indigo-600 text-white shadow-[0_20px_40px_-10px_rgba(79,70,229,0.4)] scale-105' : 'hover:bg-slate-900 hover:text-white' ?>">
            <i data-lucide="clipboard-check" class="w-5 h-5"></i>
            <span class="font-bold text-sm">Reconciliation</span>
        </a>
    </nav>

    <div class="space-y-4">
        <a href="engine.php?export=true" class="flex items-center justify-center space-x-3 w-full py-4 rounded-2xl border border-slate-800 hover:border-indigo-500 hover:text-indigo-400 transition-all text-[10px] font-black uppercase tracking-[2px]">
            <i data-lucide="file-down" class="w-4 h-4"></i>
            <span>Export Registry</span>
        </a>
    </div>
</aside>

<!-- Mobile Navigation -->
<nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-[#020617] border-t border-slate-800 px-6 py-4 flex justify-around items-center z-[100] shadow-2xl">
    <a href="index.php" class="<?= $current_page == 'index.php' ? 'text-indigo-500 scale-125' : 'text-slate-500' ?> transition-all"><i data-lucide="layout-dashboard"></i></a>
    <a href="inventory.php" class="<?= $current_page == 'inventory.php' ? 'text-indigo-500 scale-125' : 'text-slate-500' ?> transition-all"><i data-lucide="package"></i></a>
    <a href="audit.php" class="<?= $current_page == 'audit.php' ? 'text-indigo-500 scale-125' : 'text-slate-500' ?> transition-all"><i data-lucide="clipboard-check"></i></a>
    <a href="engine.php?export=true" class="text-slate-500"><i data-lucide="file-down"></i></a>
</nav>
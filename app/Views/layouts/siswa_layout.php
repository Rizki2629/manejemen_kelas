<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> - Sistem Manajemen Kelas</title>
    
    <!-- DNS Prefetch -->
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Tailwind CSS - Compiled Production Build -->
    <link rel="stylesheet" href="<?= base_url('assets/css/tailwind.min.css') ?>">
    <?php // Precompute section flags early so x-data can use them
        $isKaihSection = strpos(current_url(), 'habits') !== false; 
        $isKaihMonthly = strpos(current_url(), 'monthly-report') !== false; 
    ?>
    
    <!-- Fonts & Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"></noscript>
    <!-- Roboto font for cleaner reading experience -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom Enhancements -->
    <style>
        :root { font-size: clamp(14px,0.85vw + 11px,17px); }
    body { font-family:'Roboto','Segoe UI', Tahoma, Geneva, Verdana, sans-serif; -webkit-font-smoothing:antialiased; min-height:100vh; background:radial-gradient(at 20% 20%,#eef2ff,transparent 60%), radial-gradient(at 90% 10%,#ffe8f5,transparent 55%), linear-gradient(135deg,#f5f7fa 0%,#d9e2ef 50%,#c3cfe2 100%); }
        [x-cloak]{display:none!important;}
        .sidebar-scroll::-webkit-scrollbar{width:6px;} .sidebar-scroll::-webkit-scrollbar-thumb{background:rgba(148,163,184,0.55);border-radius:9999px;}
        .glass { backdrop-filter: blur(14px) saturate(1.2); }
        /* Content container: fill width next to sidebar (consistent across pages) */
        .page-shell { width: 100%; max-width: none; margin: 0; }
        .page-shell .mx-auto { margin-left: 0 !important; margin-right: auto !important; }
        .page-shell .container,
        .page-shell .max-w-7xl,
        .page-shell .max-w-6xl,
        .page-shell .max-w-5xl,
        .page-shell .max-w-4xl,
        .page-shell .max-w-3xl {
            max-width: none !important;
            width: 100% !important;
        }
    /* Modern question card styling */
    .question-card { position:relative; background:linear-gradient(155deg,#ffffffcc,#f5f7ffcc); backdrop-filter:blur(4px); border:1px solid #e5e7eb; border-radius:1rem; padding:1.1rem 1.25rem; box-shadow:0 1px 2px rgba(0,0,0,0.04),0 0 0 1px rgba(255,255,255,0.4) inset; transition:box-shadow .25s,border-color .25s,transform .25s; }
    .question-card:hover { border-color:#c4b5fd; box-shadow:0 4px 14px -2px rgba(99,102,241,.18),0 0 0 1px rgba(139,92,246,.20); transform:translateY(-2px); }
    .question-card:focus-within { border-color:#a78bfa; box-shadow:0 0 0 3px rgba(167,139,250,.35); }
    .question-card .question-type { font-size:.60rem; letter-spacing:.08em; font-weight:600; display:inline-block; background:#ede9fe; color:#6d28d9; padding:3px 8px; border-radius:9999px; margin-right:.6rem; text-transform:uppercase; }
    .question-card .option-img, .question-card .zoom-img { border-radius:.75rem; }
    .question-card textarea { font-family:inherit; }
    .question-card .clear-answer { transition:background .2s,color .2s; }
    </style>
    </head>
<body x-data="{nav:false, openKaih: <?= $isKaihSection ? 'true':'false' ?>}">
    <!-- Mobile Overlay -->
    <div x-cloak x-show="nav" @click="nav=false" class="fixed inset-0 bg-slate-900/60 glass md:hidden z-40"></div>
    <!-- Sidebar -->
    <aside :class="nav ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" class="fixed inset-y-0 left-0 w-72 md:w-64 bg-white text-slate-700 border-r border-slate-200 shadow-xl flex flex-col transition-transform duration-200 ease-out z-50 rounded-r-2xl md:rounded-none overflow-hidden">
        <div class="px-6 pt-6 pb-4 flex items-center gap-3 border-b border-slate-200">
            <div class="w-12 h-12 rounded-2xl bg-violet-100 text-violet-700 flex items-center justify-center shadow-sm shrink-0">
                <i class="fas fa-graduation-cap text-xl"></i>
            </div>
            <div class="min-w-0">
                <h1 class="text-lg font-semibold tracking-wide leading-tight whitespace-nowrap text-slate-900">Portal Siswa</h1>
                <?php $nm = session('student_name') ?? 'Siswa'; ?>
                <p class="text-[11px] leading-snug uppercase tracking-wider text-slate-500 truncate max-w-[180px]" x-text="'<?= esc($nm) ?>'"></p>
            </div>
            <button @click="nav=false" class="md:hidden ml-auto text-slate-500 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-violet-300 rounded-lg p-1"><i class="fas fa-times text-sm"></i></button>
        </div>

    <nav class="flex-1 overflow-y-auto sidebar-scroll px-4 py-5 space-y-2 text-[15px]">
            <?php $isDash = (current_url() == base_url('siswa')); ?>
            <a href="<?= base_url('siswa') ?>" class="group flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-violet-300 <?= $isDash ? 'bg-violet-100 text-violet-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' ?>">
                <span class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 <?= $isDash ? 'bg-violet-200 text-violet-700' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-700' ?>"><i class="fas fa-home text-base"></i></span>
                <span class="truncate ml-1">Dashboard</span>
            </a>

            <div class="border-t border-slate-200 pt-4 mt-2"></div>
            <!-- Classroom moved above 7 KAIH -->
            <?php $isProfile = (strpos(current_url(),'profile')!==false); ?>
            <a href="<?= base_url('siswa/profile') ?>" class="group flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-violet-300 <?= $isProfile ? 'bg-violet-100 text-violet-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' ?>">
                <span class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 <?= $isProfile ? 'bg-violet-200 text-violet-700' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-700' ?>"><i class="fas fa-user text-base"></i></span>
                <span class="truncate ml-1">Profil Saya</span>
            </a>
            <!-- Classroom (Materi & Tugas) collapsible -->
            <?php $isClassroom = strpos(current_url(),'classroom')!==false; ?>
            <div x-data="{openClassroom: <?= $isClassroom ? 'true':'false' ?>}">
                <button type="button" @click="openClassroom=!openClassroom" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-violet-300 <?= $isClassroom ? 'bg-violet-100 text-violet-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' ?>">
                    <span class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 <?= $isClassroom ? 'bg-violet-200 text-violet-700' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-700' ?>"><i class="fas fa-chalkboard-teacher text-base"></i></span>
                    <span class="flex-1 text-left">Classroom</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200 text-slate-400" :class="openClassroom ? 'rotate-180' : ''"></i>
                </button>
                <div x-cloak x-show="openClassroom" x-transition.opacity.duration.150ms class="mt-2 pl-4 space-y-1 border-l border-slate-200">
                    <?php $isMateri = ($isClassroom && (strpos(current_url(),'assignments')===false)); ?>
                    <?php $isTugas = (strpos(current_url(),'classroom/assignments')!==false); ?>
                    <a href="<?= base_url('siswa/classroom?tab=materi') ?>" class="flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] font-medium transition-colors <?= $isMateri ? 'bg-violet-100 text-violet-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' ?>">
                        <i class="fas fa-book-open w-4 text-slate-400"></i><span class="truncate">Materi</span>
                    </a>
                    <a href="<?= base_url('classroom/assignments') ?>" class="flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] font-medium transition-colors <?= $isTugas ? 'bg-violet-100 text-violet-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' ?>">
                        <i class="fas fa-tasks w-4 text-slate-400"></i><span class="truncate">Tugas</span>
                    </a>
                </div>
            </div>
            <!-- 7 KAIH moved below Classroom -->
            <div>
                <button type="button" @click="openKaih=!openKaih" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-violet-300 <?= $isKaihSection ? 'bg-violet-100 text-violet-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' ?>">
                    <span class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 <?= $isKaihSection ? 'bg-violet-200 text-violet-700' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-700' ?>"><i class="fas fa-star text-base"></i></span>
                    <span class="flex-1 text-left">7 KAIH</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200 text-slate-400" :class="openKaih ? 'rotate-180' : ''"></i>
                </button>
                <div x-cloak x-show="openKaih" x-transition.opacity.duration.150ms class="mt-2 pl-4 space-y-1 border-l border-slate-200">
                    <a href="<?= base_url('siswa/habits') ?>" class="flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] font-medium transition-colors <?= ($isKaihSection && !$isKaihMonthly) ? 'bg-violet-100 text-violet-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' ?>">
                        <i class="fas fa-edit w-4 text-slate-400"></i><span class="truncate">Input</span>
                    </a>
                    <a href="<?= base_url('siswa/habits/monthly-report') ?>" class="flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] font-medium transition-colors <?= $isKaihMonthly ? 'bg-violet-100 text-violet-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' ?>">
                        <i class="fas fa-calendar w-4 text-slate-400"></i><span class="truncate">Rekap Bulanan</span>
                    </a>
                </div>
            </div>
            <div class="border-t border-slate-200 pt-4 mt-4"></div>
            <a href="<?= base_url('logout') ?>" class="group flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-colors text-slate-600 hover:text-rose-700 hover:bg-rose-50 focus:outline-none focus:ring-2 focus:ring-rose-200">
                <span class="w-9 h-9 rounded-lg flex items-center justify-center bg-slate-100 text-slate-500 group-hover:bg-rose-100 group-hover:text-rose-700 shrink-0"><i class="fas fa-sign-out-alt text-base"></i></span>
                <span class="truncate ml-1">Keluar</span>
            </a>
        </nav>
    <div class="px-6 pb-5 pt-3 text-[11px] text-slate-400 tracking-wide">© SDN Grogol Utara 09 . 2025</div>
    </aside>

    <!-- Main Section -->
    <div class="min-h-screen flex flex-col md:pl-64 pl-0">
        <!-- Top Navigation Bar -->
        <header class="sticky top-0 z-30 bg-white/80 glass backdrop-blur border-b border-slate-200/60 px-4 md:px-8 py-3 flex items-center gap-4 shadow-sm">
            <button @click="nav=!nav" class="md:hidden inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-600 to-fuchsia-600 text-white shadow hover:shadow-lg transition focus:outline-none focus:ring-4 focus:ring-indigo-400/40">
                <i class="fas fa-bars"></i>
            </button>
            <div class="flex-1 min-w-0">
                <h2 class="text-lg md:text-xl font-semibold tracking-tight text-slate-800 flex items-center gap-2">
                    <i class="fas fa-layer-group text-indigo-600"></i>
                    <span class="truncate"><?= $this->renderSection('title') ?></span>
                </h2>
            </div>
            <?php $displayName = session('student_name') ?? session('username') ?? 'Siswa'; $initial = strtoupper(substr($displayName,0,1)); ?>
            <div class="hidden sm:flex flex-col items-end leading-tight mr-2 max-w-[180px]">
                <span class="text-[11px] uppercase tracking-wide text-slate-400">Siswa</span>
                <span class="text-sm font-medium text-slate-700 truncate" title="<?= esc($displayName) ?>"><?= esc($displayName) ?></span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-600 to-fuchsia-600 text-white flex items-center justify-center font-semibold shadow ring-1 ring-white/40" title="<?= esc($displayName) ?>">
                <?= esc($initial) ?>
            </div>
        </header>
        <!-- Page Content Area -->
        <main class="flex-1 w-full px-0 py-6 md:py-10 space-y-8">
            <div class="page-shell">
                <?= $this->renderSection('content') ?>
            </div>
        </main>
    </div>

    <!-- Alpine.js -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        // Optional place for future interactive scripts.
    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>

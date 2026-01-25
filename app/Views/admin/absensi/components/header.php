<!-- Header Component -->
<div class="mb-6 fade-in-up">
    <div class="gradient-header rounded-2xl p-6 text-white shadow-2xl relative overflow-hidden">
        <!-- Decorative elements -->
        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 animate-pulse"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-12 -mb-12 animate-pulse"></div>
        
        <div class="relative z-10 text-center">
            <h1 class="text-2xl lg:text-4xl font-bold mb-2 drop-shadow-lg">
                📊 REKAP ABSENSI SISWA
            </h1>
            <p class="text-base lg:text-xl opacity-90 font-medium">
                SDN GROGOL UTARA 09
            </p>
            <div class="mt-3 text-xs lg:text-sm opacity-80 flex flex-wrap justify-center gap-2">
                <span class="inline-block bg-white/20 px-3 py-1 rounded-full backdrop-blur-sm">
                    <i class="fas fa-users mr-1"></i>
                    KELAS: <?= strtoupper($filterKelas ?: 'SEMUA') ?>
                </span>
                <span class="inline-block bg-white/20 px-3 py-1 rounded-full backdrop-blur-sm">
                    <i class="fas fa-calendar mr-1"></i>
                    BULAN: <?= strtoupper($bulan_nama) ?> <?= $tahun ?>
                </span>
                <span class="inline-block bg-white/20 px-3 py-1 rounded-full backdrop-blur-sm">
                    <i class="fas fa-graduation-cap mr-1"></i>
                    TP: <?= $tahun ?>/<?= ($tahun + 1) ?>
                </span>
            </div>
        </div>
    </div>
</div>

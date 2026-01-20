<?= $this->extend('admin/layout'); ?>

<?= $this->section('content'); ?>
<!-- Mobile Viewport Meta Tag -->
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

<!-- Mobile-specific styles -->
<style>
@media (max-width: 768px) {
    /* Remove any default margins and padding */
    * {
        box-sizing: border-box;
    }
    
    body {
        margin: 0 !important;
        padding: 0 !important;
        overflow-x: hidden;
        background: transparent !important; /* Ensure no background overlay */
    }
    
    /* Ensure no fixed overlays interfere */
    .content-area,
    .content-wrapper,
    main {
        background: transparent !important;
        z-index: auto !important;
        position: relative !important;
    }
    
    /* Remove any problematic background overlays */
    .content-area::before,
    .content-area::after,
    .content-wrapper::before,
    .content-wrapper::after {
        display: none !important;
    }
    
    /* Ensure full width on mobile */
    .container, .max-w-7xl, .mx-auto {
        max-width: 100% !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    /* Remove any unwanted spacing */
    .mb-8, .mb-6, .mb-4 {
        margin-bottom: 16px !important;
    }
    
    .p-4, .p-6, .p-8, .p-12 {
        padding: 16px !important;
    }
    
    /* Ensure cards take full width */
    .student-card {
        width: 100% !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
    
    /* Optimize for mobile performance */
    .student-card {
        will-change: transform;
        backface-visibility: hidden;
    }
    
    /* Ensure no white overlay blocks content */
    body::before,
    body::after,
    html::before,
    html::after {
        display: none !important;
    }
}
</style>

<!-- Page Header with Breadcrumb -->
<div class="bg-white shadow-sm border border-gray-200 rounded-xl p-4 mb-4 md:mb-8">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="<?= base_url('/admin') ?>" class="text-gray-500 hover:text-gray-700 transition-colors">
                            <i class="fas fa-home w-4 h-4"></i>
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 w-4 h-4"></i>
                            <span class="ml-1 text-sm font-medium text-gray-700">Nilai Siswa</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-500">
                <i class="fas fa-calendar-alt mr-1"></i>
                <?= date('d F Y') ?>
            </span>
            <span class="text-sm text-gray-400">•</span>
            <span class="text-sm text-gray-500">
                <i class="fas fa-clock mr-1"></i>
                <?= date('H:i') ?> WIB
            </span>
        </div>
    </div>
</div>

<!-- Page Title Section -->
<div class="mb-8">
    <div class="md:flex md:items-center md:justify-between">
        <div class="min-w-0 flex-1">
            <h1 class="text-3xl font-bold text-gray-900 sm:text-4xl">
                <i class="fas fa-chart-line text-blue-600 mr-3"></i>
                Manajemen Nilai Siswa
            </h1>
            <p class="mt-2 text-lg text-gray-600">
                Kelola dan monitor nilai siswa secara komprehensif
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="<?= base_url('/admin/nilai/create') ?>?kelas=<?= $selectedKelas ?>&mapel=<?= $selectedMapel ?>" 
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:-translate-y-0.5">
                <i class="fas fa-plus mr-2"></i>
                Tambah Nilai Baru
            </a>
        </div>
    </div>
</div>
        <!-- Desktop Filter Panel -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 mb-8 hidden md:block">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-filter text-blue-600 mr-2"></i>
                    Filter & Pencarian Data
                </h2>
            </div>
            
            <div class="p-6">
                <form method="GET" action="<?= base_url('/admin/nilai') ?>" class="space-y-6" id="filterForm">
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                        
                        <!-- Mata Pelajaran Selection -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-book text-blue-600 mr-1"></i>
                                Mata Pelajaran
                            </label>
                            <div class="relative">
                                <select name="mapel" class="w-full pl-4 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent rounded-lg shadow-sm transition-all duration-200 bg-white hover:border-gray-400">
                                    <?php foreach ($mataPelajaranList as $code => $name): ?>
                                        <option value="<?= $code ?>" <?= $selectedMapel === $code ? 'selected' : '' ?>>
                                            <?= $name ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Kelas Selection (Admin Only) -->
                        <?php if ($userRole === 'admin'): ?>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-users text-green-600 mr-1"></i>
                                Kelas
                            </label>
                            <div class="relative">
                                <select name="kelas" class="w-full pl-4 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent rounded-lg shadow-sm transition-all duration-200 bg-white hover:border-gray-400">
                                    <option value="">Semua Kelas</option>
                                    <?php foreach ($allKelas as $kelas): ?>
                                        <option value="<?= $kelas ?>" <?= $selectedKelas === $kelas ? 'selected' : '' ?>>
                                            Kelas <?= $kelas ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Quick Stats -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-chart-bar text-purple-600 mr-1"></i>
                                Statistik Cepat
                            </label>
                            <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg p-3 border border-purple-200">
                                <div class="text-sm text-purple-700">
                                    <div class="flex justify-between">
                                        <span>Total Siswa:</span>
                                        <span class="font-semibold"><?= count($nilaiRekap ?? []) ?></span>
                                    </div>
                                    <div class="flex justify-between mt-1">
                                        <span>Sudah Dinilai:</span>
                                        <span class="font-semibold"><?= count(array_filter($nilaiRekap ?? [], function($s) { return isset($s['nilai_akhir']) && $s['nilai_akhir'] !== null; })) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-cogs text-gray-600 mr-1"></i>
                                Aksi
                            </label>
                            <div class="flex flex-col space-y-2">
                                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white px-4 py-3 rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm hover:shadow-md transform hover:-translate-y-0.5 font-medium">
                                    <i class="fas fa-search mr-2"></i>
                                    Terapkan Filter
                                </button>
                                <button type="button" onclick="resetFilters()" class="w-full bg-gray-100 text-gray-700 px-4 py-3 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 font-medium">
                                    <i class="fas fa-undo mr-2"></i>
                                    Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
+        <!-- Mobile Filter Panel -->
+        <div class="mobile-filter md:hidden">
+            <div class="filter-header" onclick="toggleMobileFilter()">
+                <div class="filter-title">
+                    <i class="fas fa-filter mr-2"></i>
+                    Filter & Pencarian Data
+                </div>
+                <i class="fas fa-chevron-down filter-toggle" id="filterToggle"></i>
+            </div>
+            <div class="filter-content" id="filterContent">
+                <!-- Mobile Search -->
+                <div class="mobile-search">
+                    <input type="text" class="search-input" placeholder="Cari nama siswa..." id="mobileSearchInput" onkeyup="filterMobileCards()">
+                    <i class="fas fa-search search-icon"></i>
+                </div>
+
+                <!-- Mobile Stats -->
+                <div class="mobile-stats">
+                    <div class="stats-grid">
+                        <div class="stat-item">
+                            <div class="stat-value"><?= count($nilaiRekap ?? []) ?></div>
+                            <div class="stat-label">Total Siswa</div>
+                        </div>
+                        <div class="stat-item">
+                            <div class="stat-value"><?= count(array_filter($nilaiRekap ?? [], function($s) { return isset($s['nilai_akhir']) && $s['nilai_akhir'] !== null; })) ?></div>
+                            <div class="stat-label">Sudah Dinilai</div>
+                        </div>
+                    </div>
+                </div>
+
+                <form method="GET" action="<?= base_url('/admin/nilai') ?>" id="mobileFilterForm">
+                    <!-- Mata Pelajaran -->
+                    <select name="mapel" class="mobile-select" onchange="this.form.submit()">
+                        <?php foreach ($mataPelajaranList as $code => $name): ?>
+                            <option value="<?= $code ?>" <?= $selectedMapel === $code ? 'selected' : '' ?>>
+                                <?= $name ?>
+                            </option>
+                        <?php endforeach; ?>
+                    </select>
+
+                    <!-- Kelas (Admin Only) -->
+                    <?php if ($userRole === 'admin'): ?>
+                    <select name="kelas" class="mobile-select" onchange="this.form.submit()">
+                        <option value="">Semua Kelas</option>
+                        <?php foreach ($allKelas as $kelas): ?>
+                            <option value="<?= $kelas ?>" <?= $selectedKelas === $kelas ? 'selected' : '' ?>>
+                                Kelas <?= $kelas ?>
+                            </option>
+                        <?php endforeach; ?>
+                    </select>
+                    <?php endif; ?>
+
+                    <button type="button" onclick="resetMobileFilters()" class="mobile-btn-secondary">
+                        <i class="fas fa-undo"></i>
+                        Reset Filter
+                    </button>
+                </form>
+            </div>
+        </div>
+
+        <!-- Mobile Card Layout -->
+        <div class="mobile-cards md:hidden">
+            <?php if ($selectedKelas && !empty($nilaiRekap)): ?>
+                <?php $no = 1; ?>
+                <?php foreach ($nilaiRekap as $siswa): ?>
+                    <div class="student-card" data-name="<?= strtolower($siswa['nama'] ?? '') ?>">
+                        <div class="card-header">
+                            <div class="student-name"><?= $siswa['nama'] ?? 'Nama tidak tersedia' ?></div>
+                            <div class="student-id">NIPD: <?= $siswa['nipd'] ?? '-' ?></div>
+                            <div class="student-number"><?= $no++ ?></div>
+                        </div>
+                        <div class="card-body">
+                            <div class="grade-grid">
+                                <div class="grade-item">
+                                    <div class="grade-label">Harian</div>
+                                    <div class="grade-value"><?= isset($siswa['nilai_harian']) && $siswa['nilai_harian'] !== null ? number_format($siswa['nilai_harian'], 1) : '-' ?></div>
+                                    <div class="grade-count"><?= isset($siswa['jumlah_harian']) ? $siswa['jumlah_harian'] : 0 ?> nilai</div>
+                                </div>
+                                <div class="grade-item">
+                                    <div class="grade-label">PTS</div>
+                                    <div class="grade-value"><?= isset($siswa['nilai_pts']) && $siswa['nilai_pts'] !== null ? number_format($siswa['nilai_pts'], 1) : '-' ?></div>
+                                </div>
+                                <div class="grade-item">
+                                    <div class="grade-label">PAS</div>
+                                    <div class="grade-value"><?= isset($siswa['nilai_pas']) && $siswa['nilai_pas'] !== null ? number_format($siswa['nilai_pas'], 1) : '-' ?></div>
+                                </div>
                                <div class="grade-item final-grade
                                    <?php 
                                        if (isset($siswa['nilai_akhir']) && $siswa['nilai_akhir'] !== null) {
                                            $nilai = $siswa['nilai_akhir'];
                                            if ($nilai >= 85) {
                                                echo ' grade-excellent';
                                            } elseif ($nilai >= 75) {
                                                echo ' grade-good';
                                            } elseif ($nilai >= 65) {
                                                echo ' grade-fair';
                                            } else {
                                                echo ' grade-poor';
                                            }
                                        } else {
                                            echo ' bg-gray-100 text-gray-800';
                                        }
                                    ?>">
                                    <div class="grade-label">Akhir</div>
                                    <div class="grade-value"><?= isset($siswa['nilai_akhir']) && $siswa['nilai_akhir'] !== null ? number_format($siswa['nilai_akhir'], 1) : '-' ?></div>
                                </div>
                            </div>
                            <div class="card-actions">
                                <?php if(isset($siswa['id'])): ?>
                                    <a href="<?= base_url('/admin/nilai/detail/' . $siswa['id']) ?>?mapel=<?= $selectedMapel ?>" class="action-btn action-btn-primary" title="Lihat Detail">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                    <a href="<?= base_url('/admin/nilai/create') ?>?kelas=<?= $selectedKelas ?>&mapel=<?= $selectedMapel ?>&siswa_id=<?= $siswa['id'] ?>" class="action-btn action-btn-secondary" title="Tambah/Edit Nilai">
                                        <i class="fas fa-plus"></i> Edit
                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php elseif ($selectedKelas && empty($nilaiRekap)): ?>
                <div class="mobile-empty">
                    <div class="empty-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="empty-title">Belum Ada Data Nilai</div>
                    <div class="empty-description">
                        Belum ada nilai yang tercatat untuk kelas <strong><?= $selectedKelas ?></strong> pada mata pelajaran <strong><?= $mataPelajaranList[$selectedMapel] ?></strong>.
                    </div>
                    <a href="<?= base_url('/admin/nilai/create') ?>?kelas=<?= $selectedKelas ?>&mapel=<?= $selectedMapel ?>" class="mobile-btn mobile-btn-primary">
                        <i class="fas fa-plus mr-2"></i> Mulai Input Nilai
                    </a>
                </div>
            <?php else: ?>
                <div class="mobile-empty">
                    <div class="empty-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="empty-title">Pilih Filter untuk Memulai</div>
                    <div class="empty-description">
                        Silakan pilih mata pelajaran dan kelas untuk melihat data nilai siswa.
                    </div>
                </div>
            <?php endif; ?>
        </div>



        <!-- Data Section -->
        <?php if ($selectedKelas && !empty($nilaiRekap)): ?>
        
        <!-- Desktop Table -->
        <div class="desktop-table bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            
            <!-- Table Header -->
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-table text-white"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                Daftar Nilai Kelas <?= $selectedKelas ?>
                            </h3>
                            <p class="text-sm text-gray-500">
                                Mata Pelajaran: <?= $mataPelajaranList[$selectedMapel] ?>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-users mr-1"></i>
                            <?= count($nilaiRekap) ?> Siswa
                        </span>
                        <button class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 hover:bg-green-200 transition-colors">
                            <i class="fas fa-download mr-1"></i>
                            Export
                        </button>
                    </div>
                </div>
            </div>

            <!-- Desktop Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-1">
                                    <i class="fas fa-hashtag text-blue-600"></i>
                                    <span>No</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-1">
                                    <i class="fas fa-user text-green-600"></i>
                                    <span>Siswa</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center justify-center space-x-1">
                                    <i class="fas fa-calendar-day text-orange-600"></i>
                                    <span>Harian</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center justify-center space-x-1">
                                    <i class="fas fa-clipboard-check text-purple-600"></i>
                                    <span>PTS</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center justify-center space-x-1">
                                    <i class="fas fa-graduation-cap text-red-600"></i>
                                    <span>PAS</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center justify-center space-x-1">
                                    <i class="fas fa-trophy text-yellow-600"></i>
                                    <span>Akhir</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center justify-center space-x-1">
                                    <i class="fas fa-cogs text-gray-600"></i>
                                    <span>Aksi</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $no = 1; ?>
                        <?php foreach ($nilaiRekap as $siswa): ?>
                        <tr class="hover:bg-blue-50 transition-colors duration-200 group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-r from-blue-100 to-blue-200 rounded-full group-hover:from-blue-200 group-hover:to-blue-300 transition-all duration-200">
                                    <span class="text-sm font-bold text-blue-800"><?= $no++ ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    <?= isset($siswa['nama']) && !empty($siswa['nama']) ? $siswa['nama'] : 'Nama tidak tersedia' ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                    NIPD: <?= isset($siswa['nipd']) && !empty($siswa['nipd']) ? $siswa['nipd'] : '-' ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-lg font-bold text-gray-900">
                                        <?= isset($siswa['nilai_harian']) && $siswa['nilai_harian'] !== null ? number_format($siswa['nilai_harian'], 1) : '-' ?>
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 mt-1">
                                        <?= isset($siswa['jumlah_harian']) ? $siswa['jumlah_harian'] : 0 ?> nilai
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-lg font-bold text-gray-900">
                                    <?= isset($siswa['nilai_pts']) && $siswa['nilai_pts'] !== null ? number_format($siswa['nilai_pts'], 1) : '-' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-lg font-bold text-gray-900">
                                    <?= isset($siswa['nilai_pas']) && $siswa['nilai_pas'] !== null ? number_format($siswa['nilai_pas'], 1) : '-' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-lg font-bold 
                                    <?php 
                                        if (isset($siswa['nilai_akhir']) && $siswa['nilai_akhir'] !== null) {
                                            $nilai = $siswa['nilai_akhir'];
                                            if ($nilai >= 85) {
                                                echo 'text-green-600';
                                            } elseif ($nilai >= 75) {
                                                echo 'text-blue-600';
                                            } elseif ($nilai >= 65) {
                                                echo 'text-yellow-600';
                                            } else {
                                                echo 'text-red-600';
                                            }
                                        } else {
                                            echo 'text-gray-400';
                                        }
                                    ?>">
                                    <?= isset($siswa['nilai_akhir']) && $siswa['nilai_akhir'] !== null ? number_format($siswa['nilai_akhir'], 1) : '-' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <?php if(isset($siswa['id'])): ?>
                                        <a href="<?= base_url('/admin/nilai/detail/' . $siswa['id']) ?>?mapel=<?= $selectedMapel ?>" 
                                           class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium text-blue-700 bg-blue-100 hover:bg-blue-200 transition-colors" title="Lihat Detail">
                                            <i class="fas fa-eye mr-1"></i>
                                            Detail
                                        </a>
                                        <a href="<?= base_url('/admin/nilai/create') ?>?kelas=<?= $selectedKelas ?>&mapel=<?= $selectedMapel ?>&siswa_id=<?= $siswa['id'] ?>" 
                                           class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium text-green-700 bg-green-100 hover:bg-green-200 transition-colors" title="Tambah/Edit Nilai">
                                            <i class="fas fa-edit mr-1"></i>
                                            Edit
                                        </a>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Table Footer -->
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                        <i class="fas fa-info-circle"></i>
                        <span>Menampilkan <?= count($nilaiRekap) ?> dari <?= count($nilaiRekap) ?> siswa</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button class="px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                            <i class="fas fa-chevron-left mr-1"></i>
                            Sebelumnya
                        </button>
                        <button class="px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                            Selanjutnya
                            <i class="fas fa-chevron-right ml-1"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Empty States -->
        <?php elseif ($selectedKelas && empty($nilaiRekap)): ?>
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-12">
            <div class="text-center max-w-md mx-auto">
                <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-gray-100 mb-6">
                    <i class="fas fa-chart-line text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Belum Ada Data Nilai</h3>
                <p class="text-gray-600 text-lg mb-8">
                    Belum ada nilai yang tercatat untuk kelas 
                    <span class="font-semibold text-blue-600"><?= $selectedKelas ?></span> 
                    pada mata pelajaran 
                    <span class="font-semibold text-green-600"><?= $mataPelajaranList[$selectedMapel] ?></span>
                </p>
                <div class="space-y-4">
                    <a href="<?= base_url('/admin/nilai/create') ?>?kelas=<?= $selectedKelas ?>&mapel=<?= $selectedMapel ?>" 
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Mulai Input Nilai
                    </a>
                    <div class="text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Data akan otomatis tersimpan setelah input pertama
                    </div>
                </div>
            </div>
        </div>
        
        <?php else: ?>
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-12">
            <div class="text-center max-w-md mx-auto">
                <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-gray-100 mb-6">
                    <i class="fas fa-search text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Pilih Filter untuk Memulai</h3>
                <p class="text-gray-600 text-lg mb-8">
                    Silakan pilih mata pelajaran dan kelas untuk melihat data nilai siswa
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-book text-blue-600"></i>
                        <span>Pilih Mata Pelajaran</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-users text-green-600"></i>
                        <span>Pilih Kelas</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-search text-purple-600"></i>
                        <span>Tekan Filter</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-chart-line text-orange-600"></i>
                        <span>Lihat Hasil</span>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Toast Notifications -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2">
    <?php if (session()->getFlashdata('success')): ?>
    <div id="success-toast" class="bg-white rounded-lg shadow-lg border-l-4 border-green-400 p-4 max-w-md transform transition-all duration-300 translate-x-full">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-green-100">
                    <i class="fas fa-check text-green-600"></i>
                </div>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium text-gray-900">Berhasil!</p>
                <p class="text-sm text-gray-600"><?= session()->getFlashdata('success') ?></p>
            </div>
            <div class="ml-4 flex-shrink-0">
                <button onclick="hideToast('success-toast')" class="inline-flex text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
    <div id="error-toast" class="bg-white rounded-lg shadow-lg border-l-4 border-red-400 p-4 max-w-md transform transition-all duration-300 translate-x-full">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-red-100">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium text-gray-900">Error!</p>
                <p class="text-sm text-gray-600"><?= session()->getFlashdata('error') ?></p>
            </div>
            <div class="ml-4 flex-shrink-0">
                <button onclick="hideToast('error-toast')" class="inline-flex text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
        <span class="text-gray-700">Memuat data...</span>
    </div>
</div>

<script>
// Enhanced JavaScript for CI4 + Tailwind Integration
document.addEventListener('DOMContentLoaded', function() {
    
    // Initialize page
    initializeToasts();
    initializeFormHandlers();
    initializeTableEnhancements();
    
    // Toast Management
    function initializeToasts() {
        const toasts = document.querySelectorAll('[id$="-toast"]');
        toasts.forEach(toast => {
            // Slide in animation
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
                toast.classList.add('translate-x-0');
            }, 100);
            
            // Auto hide after 5 seconds
            setTimeout(() => {
                hideToast(toast.id);
            }, 5000);
        });
    }
    
    window.hideToast = function(toastId) {
        const toast = document.getElementById(toastId);
        if (toast) {
            toast.classList.add('translate-x-full');
            toast.classList.remove('translate-x-0');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }
    };
    
    // Form Handlers
    function initializeFormHandlers() {
        const filterForm = document.getElementById('filterForm');
        const selects = filterForm.querySelectorAll('select');
        
        // Auto-submit on select change
        selects.forEach(select => {
            select.addEventListener('change', function() {
                showLoading();
                filterForm.submit();
            });
        });
        
        // Reset filters function
        window.resetFilters = function() {
            const selects = filterForm.querySelectorAll('select');
            selects.forEach(select => {
                select.selectedIndex = 0;
            });
            showLoading();
            filterForm.submit();
        };
    }
    
    // Table Enhancements
    function initializeTableEnhancements() {
        // Add hover effects to table rows
        const tableRows = document.querySelectorAll('tbody tr');
        tableRows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.transform = 'translateX(2px)';
            });
            
            row.addEventListener('mouseleave', function() {
                this.style.transform = 'translateX(0)';
            });
        });
        
        // Add click handlers for action buttons
        const actionButtons = document.querySelectorAll('a[title]');
        actionButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                const title = this.getAttribute('title');
                if (title.includes('Detail') || title.includes('Tambah')) {
                    showLoading();
                }
            });
        });
    }
    
    // Loading Overlay
    function showLoading() {
        document.getElementById('loading-overlay').classList.remove('hidden');
    }
    
    function hideLoading() {
        document.getElementById('loading-overlay').classList.add('hidden');
    }
    
    // Hide loading on page load
    window.addEventListener('load', hideLoading);
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K to focus on search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            document.querySelector('select[name="mapel"]').focus();
        }
        
        // Escape to reset filters
        if (e.key === 'Escape') {
            resetFilters();
        }
    });
    
    // Add fade-in animation to main content
    const mainContent = document.querySelector('.max-w-7xl');
    if (mainContent) {
        mainContent.style.opacity = '0';
        mainContent.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            mainContent.style.transition = 'all 0.6s ease-out';
            mainContent.style.opacity = '1';
            mainContent.style.transform = 'translateY(0)';
        }, 100);
    }
});

// Export functionality (placeholder)
window.exportData = function() {
    alert('Export functionality will be implemented here');
};

// Print functionality
window.printTable = function() {
    window.print();
};
</script>

<!-- Print Styles -->
<style>
@media print {
    body * {
        visibility: hidden;
    }
    
    .print-section, .print-section * {
        visibility: visible;
    }
    
    .print-section {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    
    .no-print {
        display: none !important;
    }
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}

/* Smooth transitions for all interactive elements */
* {
    transition: all 0.2s ease-in-out;
}

/* Focus styles for accessibility */
.focus\:ring-2:focus {
    outline: 2px solid transparent;
    outline-offset: 2px;
}
</style>

<script>
function resetFilters() {
    // Reset all form fields to default values
    document.querySelector('select[name="mapel"]').value = 'IPAS';
    <?php if ($userRole === 'admin'): ?>
    document.querySelector('select[name="kelas"]').value = '';
    <?php endif; ?>
    
    // Submit the form to reload with default values
    document.getElementById('filterForm').submit();
}

// Auto-submit form when selections change
document.addEventListener('DOMContentLoaded', function() {
    const mapelSelect = document.querySelector('select[name="mapel"]');
    const kelasSelect = document.querySelector('select[name="kelas"]');
    
    if (mapelSelect) {
        mapelSelect.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    }
    
    if (kelasSelect) {
        kelasSelect.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    }
});

// Mobile filter functions
function toggleMobileFilter() {
    const filterContent = document.getElementById('filterContent');
    const filterToggle = document.getElementById('filterToggle');
    
    if (filterContent.style.display === 'none' || filterContent.style.display === '') {
        filterContent.style.display = 'block';
        filterToggle.classList.remove('fa-chevron-down');
        filterToggle.classList.add('fa-chevron-up');
        
        // Add smooth animation
        filterContent.style.opacity = '0';
        filterContent.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            filterContent.style.transition = 'all 0.3s ease';
            filterContent.style.opacity = '1';
            filterContent.style.transform = 'translateY(0)';
        }, 10);
    } else {
        filterContent.style.transition = 'all 0.3s ease';
        filterContent.style.opacity = '0';
        filterContent.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            filterContent.style.display = 'none';
        }, 300);
        filterToggle.classList.remove('fa-chevron-up');
        filterToggle.classList.add('fa-chevron-down');
    }
}

function filterMobileCards() {
    const searchInput = document.getElementById('mobileSearchInput');
    const searchTerm = searchInput.value.toLowerCase();
    const cards = document.querySelectorAll('.student-card');
    let visibleCount = 0;
    
    cards.forEach(card => {
        const studentName = card.getAttribute('data-name');
        if (studentName.includes(searchTerm)) {
            card.style.display = 'block';
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.style.transition = 'all 0.3s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, visibleCount * 50);
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Show no results message if needed
    const noResultsMsg = document.getElementById('no-results-message');
    if (visibleCount === 0 && searchTerm.length > 0) {
        if (!noResultsMsg) {
            const msg = document.createElement('div');
            msg.id = 'no-results-message';
            msg.className = 'mobile-empty';
            msg.innerHTML = `
                <div class="empty-icon">
                    <i class="fas fa-search"></i>
                </div>
                <div class="empty-title">Tidak Ada Hasil</div>
                <div class="empty-description">
                    Tidak ditemukan siswa dengan nama "${searchTerm}"
                </div>
            `;
            document.querySelector('.mobile-cards').appendChild(msg);
        }
    } else if (noResultsMsg) {
        noResultsMsg.remove();
    }
}

function resetMobileFilters() {
    const mobileFilterForm = document.getElementById('mobileFilterForm');
    const selects = mobileFilterForm.querySelectorAll('select');
    
    selects.forEach(select => {
        if (select.name === 'mapel') {
            select.value = 'IPAS';
        } else if (select.name === 'kelas') {
            select.value = '';
        }
    });
    
    // Clear search input
    const searchInput = document.getElementById('mobileSearchInput');
    if (searchInput) {
        searchInput.value = '';
        filterMobileCards();
    }
    
    mobileFilterForm.submit();
}

// Enhanced mobile experience
document.addEventListener('DOMContentLoaded', function() {
    // Add pull-to-refresh functionality
    let startY = 0;
    let currentY = 0;
    let pullDistance = 0;
    const pullThreshold = 80;
    
    document.addEventListener('touchstart', function(e) {
        if (window.scrollY === 0) {
            startY = e.touches[0].clientY;
        }
    });
    
    document.addEventListener('touchmove', function(e) {
        if (window.scrollY === 0 && startY > 0) {
            currentY = e.touches[0].clientY;
            pullDistance = currentY - startY;
            
            if (pullDistance > 0 && pullDistance < pullThreshold) {
                document.body.style.transform = `translateY(${pullDistance * 0.5}px)`;
            }
        }
    });
    
    document.addEventListener('touchend', function(e) {
        if (pullDistance > pullThreshold) {
            // Trigger refresh
            location.reload();
        }
        
        // Reset
        document.body.style.transform = '';
        startY = 0;
        currentY = 0;
        pullDistance = 0;
    });
    
    // Add haptic feedback for mobile
    if ('vibrate' in navigator) {
        const buttons = document.querySelectorAll('.action-btn, .mobile-btn, .filter-header');
        buttons.forEach(button => {
            button.addEventListener('touchstart', function() {
                navigator.vibrate(10);
            });
        });
    }
    
    // Optimize for mobile performance
    const cards = document.querySelectorAll('.student-card');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('fade-in');
    });
});

// Add CSS animation for fade-in effect
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .fade-in {
        animation: fadeInUp 0.6s ease-out forwards;
    }
    
    @media (max-width: 768px) {
        .student-card {
            opacity: 0;
        }
        
        .student-card.fade-in {
            opacity: 1;
        }
    }
`;
document.head.appendChild(style);
</script>

<!-- Floating Action Button for Mobile - FIXED TO RIGHT -->
<div class="fab-mobile md:hidden" onclick="scrollToTop()" style="position: fixed !important; bottom: 20px !important; right: 20px !important; z-index: 999999 !important;">
    <i class="fas fa-arrow-up"></i>
</div>

<style>
/* Floating Action Button - FORCE RIGHT POSITION */
.fab-mobile {
    position: fixed !important;
    bottom: 20px !important;
    right: 20px !important;
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 999999 !important;
    opacity: 1 !important;
    transform: scale(1) !important;
    left: auto !important; /* Force override any left positioning */
}

.fab-mobile:active {
    transform: scale(0.9) !important;
}

/* Force mobile FAB positioning override */
@media (max-width: 768px) {
    .fab-mobile {
        position: fixed !important;
        bottom: 20px !important;
        right: 20px !important;
        left: auto !important;
        top: auto !important;
        z-index: 999999 !important;
    }
}

/* Show FAB when scrolling */
@media (max-width: 768px) {
    .fab-mobile {
        animation: slideIn 0.3s ease forwards;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
}
</style>

<script>
// Show/hide FAB based on scroll position - FORCE RIGHT POSITION
window.addEventListener('scroll', function() {
    const fab = document.querySelector('.fab-mobile');
    
    if (fab) {
        // Always force correct positioning
        fab.style.position = 'fixed';
        fab.style.bottom = '20px';
        fab.style.right = '20px';
        fab.style.left = 'auto';
        fab.style.top = 'auto';
        fab.style.zIndex = '999999';
        
        if (window.scrollY > 200) {
            fab.style.opacity = '1';
            fab.style.transform = 'scale(1)';
        } else {
            fab.style.opacity = '1'; // Always show
            fab.style.transform = 'scale(1)';
        }
    }
});

// Scroll to top function
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Refresh data function
function refreshData() {
    location.reload();
}

// Print data function
function printData() {
    window.print();
}

// Enhanced mobile experience
document.addEventListener('DOMContentLoaded', function() {
    // Force FAB positioning on mobile
    const fab = document.querySelector('.fab-mobile');
    if (fab) {
        // Force correct positioning
        fab.style.position = 'fixed';
        fab.style.bottom = '20px';
        fab.style.right = '20px';
        fab.style.left = 'auto';
        fab.style.top = 'auto';
        fab.style.zIndex = '999999';
        fab.style.opacity = '1';
        fab.style.transform = 'scale(1)';
    }
    
    // Add swipe gestures for mobile
    let startX = 0;
    let startY = 0;
    
    document.addEventListener('touchstart', function(e) {
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
    });
    
    document.addEventListener('touchend', function(e) {
        const endX = e.changedTouches[0].clientX;
        const endY = e.changedTouches[0].clientY;
        const diffX = startX - endX;
        const diffY = startY - endY;
        
        // Swipe left to refresh
        if (diffX > 50 && Math.abs(diffY) < 50) {
            location.reload();
        }
        
        // Swipe right to go back
        if (diffX < -50 && Math.abs(diffY) < 50) {
            history.back();
        }
    });
    
    // Add loading states for better UX
    const actionButtons = document.querySelectorAll('.action-btn');
    actionButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.style.opacity = '0.7';
            this.style.pointerEvents = 'none';
            
            setTimeout(() => {
                this.style.opacity = '1';
                this.style.pointerEvents = 'auto';
            }, 1000);
        });
    });
});
</script>

<?= $this->endSection(); ?>

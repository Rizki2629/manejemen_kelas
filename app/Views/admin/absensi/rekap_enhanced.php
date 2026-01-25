<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<!-- Tailwind CSS CDN with latest version -->
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
    /* Enhanced Custom Styles */
    .table-sticky-left {
        position: sticky;
        left: 0;
        z-index: 20;
        box-shadow: 2px 0 8px rgba(0,0,0,0.15);
    }
    
    .table-sticky-second {
        position: sticky;
        left: 60px;
        z-index: 15;
        box-shadow: 2px 0 8px rgba(0,0,0,0.1);
    }
    
    .attendance-cell {
        min-width: 45px;
        width: 45px;
        max-width: 45px;
        transition: all 0.3s ease;
    }
    
    .attendance-cell:hover {
        transform: scale(1.2);
        z-index: 25;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    
    .gradient-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        background-size: 200% 200%;
        animation: gradientShift 3s ease infinite;
    }
    
    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    
    .scroll-shadow-left {
        box-shadow: inset 10px 0 8px -8px rgba(0,0,0,0.15);
    }
    
    .scroll-shadow-right {
        box-shadow: inset -10px 0 8px -8px rgba(0,0,0,0.15);
    }
    
    .fade-in {
        animation: fadeIn 0.8s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Mobile optimizations */
    @media (max-width: 768px) {
        .attendance-cell {
            min-width: 35px;
            width: 35px;
            max-width: 35px;
        }
        
        .table-sticky-second {
            left: 50px;
        }
    }
    
    /* Dark mode support */
    .dark .gradient-header {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    }
    
    /* Print styles */
    @media print {
        .no-print { display: none !important; }
        .table-sticky-left, .table-sticky-second {
            position: static;
            box-shadow: none;
        }
        .attendance-cell {
            min-width: 20px;
            width: 20px;
            font-size: 10px;
        }
    }
</style>

<?php
// Helper function untuk status warna
function getStatusColor($status, $isWeekend = false, $isHoliday = false) {
    if ($isHoliday || $isWeekend) {
        return 'bg-gray-200 text-gray-500';
    }
    
    return match($status) {
        'hadir' => 'bg-green-100 text-green-800 hover:bg-green-200',
        'izin' => 'bg-blue-100 text-blue-800 hover:bg-blue-200',
        'sakit' => 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200',
        'alpha' => 'bg-red-100 text-red-800 hover:bg-red-200',
        default => 'bg-gray-50 text-gray-400'
    };
}

// Helper function untuk status symbol
function getStatusSymbol($status, $isWeekend = false, $isHoliday = false) {
    if ($isHoliday || $isWeekend) {
        return '■';
    }
    
    return match($status) {
        'hadir' => '✓',
        'izin' => 'I',
        'sakit' => 'S',
        'alpha' => 'A',
        default => '-'
    };
}

// Helper function untuk header date color
function getDateHeaderColor($isWeekend = false, $isHoliday = false) {
    if ($isHoliday) return 'bg-red-500';
    if ($isWeekend) return 'bg-orange-500';
    return 'bg-blue-500';
}
?>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 p-2 lg:p-6">
    <!-- Enhanced Header Section -->
    <div class="mb-6 fade-in">
        <div class="gradient-header rounded-2xl p-6 text-white shadow-2xl relative overflow-hidden">
            <!-- Decorative elements -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-12 -mb-12"></div>
            
            <div class="relative z-10 text-center">
                <h1 class="text-2xl lg:text-4xl font-bold mb-2 drop-shadow-lg">
                    📊 REKAP ABSENSI SISWA
                </h1>
                <p class="text-base lg:text-xl opacity-90 font-medium">
                    SDN GROGOL UTARA 09
                </p>
                <div class="mt-3 text-xs lg:text-sm opacity-80">
                    <span class="inline-block bg-white/20 px-3 py-1 rounded-full mx-1">
                        KELAS: <?= strtoupper($filterKelas ?? 'SEMUA') ?>
                    </span>
                    <span class="inline-block bg-white/20 px-3 py-1 rounded-full mx-1">
                        BULAN: <?= strtoupper($bulan_nama ?? date('F')) ?> <?= $tahun ?? date('Y') ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Filter Section -->
    <div class="mb-6 fade-in">
        <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200 backdrop-blur-sm">
            <form id="filterForm" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    <!-- Kelas Filter -->
                    <div class="space-y-2">
                        <label for="kelas" class="block text-gray-700 font-semibold text-sm">
                            <i class="fas fa-users mr-2 text-blue-500"></i>Kelas
                        </label>
                        <?php if ($userRole === 'admin'): ?>
                            <select 
                                id="kelas" 
                                name="kelas" 
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 font-medium"
                                required
                            >
                                <option value="">Pilih Kelas</option>
                                <?php foreach ($allKelas as $kelasItem): ?>
                                    <option value="<?= $kelasItem['kelas'] ?>" 
                                            <?= $filterKelas === $kelasItem['kelas'] ? 'selected' : '' ?>>
                                        Kelas <?= $kelasItem['kelas'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <input 
                                type="text" 
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 shadow-sm font-medium" 
                                value="Kelas <?= $userKelas ?>" 
                                readonly
                            >
                            <input type="hidden" name="kelas" value="<?= $userKelas ?>">
                        <?php endif; ?>
                    </div>

                    <!-- Bulan Filter -->
                    <div class="space-y-2">
                        <label for="bulan" class="block text-gray-700 font-semibold text-sm">
                            <i class="fas fa-calendar mr-2 text-green-500"></i>Bulan & Tahun
                        </label>
                        <input 
                            type="month" 
                            id="bulan" 
                            name="bulan" 
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-300 font-medium"
                            value="<?= $filterBulan ?>" 
                            required
                        >
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-2">
                        <label class="block text-transparent text-sm">Action</label>
                        <button 
                            type="submit" 
                            class="w-full px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300 uppercase tracking-wide"
                        >
                            <i class="fas fa-search mr-2"></i>Tampilkan
                        </button>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-transparent text-sm">Export</label>
                        <button 
                            type="button" 
                            id="downloadExcel" 
                            class="w-full px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300 uppercase tracking-wide disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                            <?= empty($attendanceData) || empty($attendanceData['students']) ? 'disabled' : '' ?>
                        >
                            <i class="fas fa-file-excel mr-2"></i>Excel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Section -->
    <?php if (!empty($attendanceData) && !empty($attendanceData['students'])): ?>
        
        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6 fade-in">
            <div class="bg-white rounded-xl p-4 shadow-lg border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600">Total Siswa</p>
                        <p class="text-2xl font-bold text-gray-800"><?= count($attendanceData['students']) ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-4 shadow-lg border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-calendar-check text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600">Hari Aktif</p>
                        <p class="text-2xl font-bold text-gray-800"><?= count($attendanceData['days']) ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-4 shadow-lg border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <i class="fas fa-percentage text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600">Rata-rata Kehadiran</p>
                        <p class="text-2xl font-bold text-gray-800">
                            <?= number_format(array_sum(array_column($attendanceData['students'], 'percentage')) / count($attendanceData['students']), 1) ?>%
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-4 shadow-lg border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-full">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600">Total Alpha</p>
                        <p class="text-2xl font-bold text-gray-800">
                            <?= array_sum(array_map(fn($s) => $s['summary']['alpha'], $attendanceData['students'])) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Professional Header -->
        <div class="mb-6 fade-in">
            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200">
                <div class="text-center space-y-2">
                    <h2 class="text-2xl font-bold text-gray-800">DAFTAR HADIR PESERTA DIDIK</h2>
                    <h3 class="text-xl font-semibold text-blue-600">SDN GROGOL UTARA 09</h3>
                    <h4 class="text-lg font-medium text-gray-700">KELAS <?= strtoupper($attendanceData['kelas'] ?? '') ?></h4>
                    <p class="text-sm text-gray-500">TAHUN PELAJARAN <?= $attendanceData['year'] ?>/<?= ($attendanceData['year'] + 1) ?></p>
                    <div class="inline-block mt-4 px-6 py-2 bg-gradient-to-r from-blue-100 to-purple-100 rounded-full border border-blue-200">
                        <span class="text-lg font-bold text-blue-700">
                            BULAN <?= strtoupper(date('F Y', mktime(0, 0, 0, (int)$attendanceData['month'], 1, (int)$attendanceData['year']))) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Table -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-gray-200 fade-in">
            
            <!-- Table Info Header -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-table mr-2 text-blue-500"></i>
                        Data Kehadiran
                    </h3>
                    
                    <!-- Legend -->
                    <div class="flex flex-wrap items-center gap-3 text-sm">
                        <div class="flex items-center gap-1">
                            <div class="w-3 h-3 bg-green-200 rounded-full"></div>
                            <span class="text-gray-600">Hadir (✓)</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <div class="w-3 h-3 bg-yellow-200 rounded-full"></div>
                            <span class="text-gray-600">Sakit (S)</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <div class="w-3 h-3 bg-blue-200 rounded-full"></div>
                            <span class="text-gray-600">Izin (I)</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <div class="w-3 h-3 bg-red-200 rounded-full"></div>
                            <span class="text-gray-600">Alpha (A)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile scroll notice -->
            <div class="px-6 py-3 bg-blue-50 border-b border-blue-200 lg:hidden no-print">
                <p class="text-sm text-blue-700 flex items-center">
                    <i class="fas fa-arrows-alt-h mr-2"></i>
                    Geser tabel ke kanan untuk melihat semua tanggal
                </p>
            </div>

            <!-- Main Table -->
            <div class="overflow-x-auto" id="tableContainer">
                <table class="w-full text-sm" id="attendanceTable">
                    <thead>
                        <tr class="gradient-header text-white">
                            <!-- Sticky Left Columns -->
                            <th class="table-sticky-left bg-gradient-to-r from-blue-600 to-purple-600 px-3 py-4 text-center font-bold border-r-2 border-white/30 min-w-[60px]">
                                <i class="fas fa-hashtag text-xs"></i><br>No
                            </th>
                            <th class="table-sticky-second bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-4 text-left font-bold border-r-2 border-white/30 min-w-[200px]">
                                <i class="fas fa-user text-xs"></i><br>Nama Siswa
                            </th>

                            <!-- Date Columns -->
                            <?php foreach ($attendanceData['days'] as $day): ?>
                                <?php 
                                $dayStr = str_pad($day, 2, '0', STR_PAD_LEFT);
                                $currentDate = $attendanceData['year'] . '-' . str_pad($attendanceData['month'], 2, '0', STR_PAD_LEFT) . '-' . $dayStr;
                                $dayOfWeek = date('w', strtotime($currentDate));
                                $isWeekend = ($dayOfWeek == 0 || $dayOfWeek == 6);
                                $isHoliday = isset($attendanceData['holidays']) && in_array($currentDate, $attendanceData['holidays']);
                                
                                $headerColor = getDateHeaderColor($isWeekend, $isHoliday);
                                ?>
                                <th class="px-2 py-4 text-center font-bold border-r border-white/20 attendance-cell <?= $headerColor ?>" title="<?= date('l, d F Y', strtotime($currentDate)) ?>">
                                    <div class="text-xs opacity-75"><?= date('D', strtotime($currentDate)) ?></div>
                                    <div class="text-base font-bold"><?= $day ?></div>
                                </th>
                            <?php endforeach; ?>

                            <!-- Summary Columns -->
                            <th class="px-3 py-4 text-center font-bold bg-yellow-500 border-r border-white/20 min-w-[50px]">
                                <i class="fas fa-thermometer-half text-xs"></i><br>S
                            </th>
                            <th class="px-3 py-4 text-center font-bold bg-blue-500 border-r border-white/20 min-w-[50px]">
                                <i class="fas fa-info-circle text-xs"></i><br>I
                            </th>
                            <th class="px-3 py-4 text-center font-bold bg-red-500 border-r border-white/20 min-w-[50px]">
                                <i class="fas fa-times text-xs"></i><br>A
                            </th>
                            <th class="px-3 py-4 text-center font-bold bg-green-500 border-r border-white/20 min-w-[60px]">
                                <i class="fas fa-check text-xs"></i><br>Total
                            </th>
                            <th class="px-3 py-4 text-center font-bold bg-purple-500 min-w-[60px]">
                                <i class="fas fa-percentage text-xs"></i><br>%
                            </th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php $no = 1; ?>
                        <?php foreach ($attendanceData['students'] as $index => $student): ?>
                            <tr class="<?= $index % 2 === 0 ? 'bg-gray-50' : 'bg-white' ?> hover:bg-blue-50 transition-all duration-200 border-b border-gray-100 student-row">
                                
                                <!-- Sticky Left Columns -->
                                <td class="table-sticky-left <?= $index % 2 === 0 ? 'bg-gray-50' : 'bg-white' ?> hover:bg-blue-50 px-3 py-3 text-center font-semibold border-r border-gray-200 text-gray-700">
                                    <?= $no++ ?>
                                </td>
                                <td class="table-sticky-second <?= $index % 2 === 0 ? 'bg-gray-50' : 'bg-white' ?> hover:bg-blue-50 px-4 py-3 font-medium text-gray-800 border-r border-gray-200" title="<?= $student['nama'] ?>">
                                    <div class="truncate max-w-[180px] font-semibold">
                                        <?= $student['nama'] ?>
                                    </div>
                                </td>

                                <!-- Daily Attendance -->
                                <?php foreach ($attendanceData['days'] as $day): ?>
                                    <?php 
                                    $dayStr = str_pad($day, 2, '0', STR_PAD_LEFT);
                                    $currentDate = $attendanceData['year'] . '-' . str_pad($attendanceData['month'], 2, '0', STR_PAD_LEFT) . '-' . $dayStr;
                                    $dayOfWeek = date('w', strtotime($currentDate));
                                    $isWeekend = ($dayOfWeek == 0 || $dayOfWeek == 6);
                                    $isHoliday = isset($attendanceData['holidays']) && in_array($currentDate, $attendanceData['holidays']);
                                    
                                    $status = $student['daily'][$dayStr] ?? '';
                                    $symbol = getStatusSymbol($status, $isWeekend, $isHoliday);
                                    $colorClass = getStatusColor($status, $isWeekend, $isHoliday);
                                    ?>
                                    <td class="px-2 py-3 text-center font-bold border-r border-gray-100 attendance-cell <?= $colorClass ?> transition-all duration-200" title="<?= date('l, d F Y', strtotime($currentDate)) . ' - ' . ucfirst($status) ?>">
                                        <?= $symbol ?>
                                    </td>
                                <?php endforeach; ?>

                                <!-- Summary Columns -->
                                <td class="px-3 py-3 text-center font-bold bg-yellow-50 text-yellow-700 border-r border-gray-100">
                                    <?= $student['summary']['sakit'] ?>
                                </td>
                                <td class="px-3 py-3 text-center font-bold bg-blue-50 text-blue-700 border-r border-gray-100">
                                    <?= $student['summary']['izin'] ?>
                                </td>
                                <td class="px-3 py-3 text-center font-bold bg-red-50 text-red-700 border-r border-gray-100">
                                    <?= $student['summary']['alpha'] ?>
                                </td>
                                <td class="px-3 py-3 text-center font-bold bg-green-50 text-green-700 border-r border-gray-100">
                                    <?= $student['summary']['hadir'] ?>
                                </td>
                                <td class="px-3 py-3 text-center font-bold bg-purple-50 text-purple-700">
                                    <div class="flex items-center justify-center">
                                        <span class="text-lg"><?= number_format($student['percentage'], 1) ?>%</span>
                                        <div class="ml-2 w-2 h-2 rounded-full <?= $student['percentage'] >= 75 ? 'bg-green-500' : 'bg-red-500' ?>"></div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <!-- Summary Rows -->
                        <?php
                        $totalSakit = array_sum(array_map(fn($s) => $s['summary']['sakit'], $attendanceData['students']));
                        $totalIzin = array_sum(array_map(fn($s) => $s['summary']['izin'], $attendanceData['students']));
                        $totalAlpha = array_sum(array_map(fn($s) => $s['summary']['alpha'], $attendanceData['students']));
                        $totalHadir = array_sum(array_map(fn($s) => $s['summary']['hadir'], $attendanceData['students']));
                        
                        $totalStudents = count($attendanceData['students']);
                        $totalDays = count($attendanceData['days']);
                        $totalPossibleAttendance = $totalStudents * $totalDays;
                        
                        $percentSakit = $totalPossibleAttendance > 0 ? ($totalSakit / $totalPossibleAttendance) * 100 : 0;
                        $percentIzin = $totalPossibleAttendance > 0 ? ($totalIzin / $totalPossibleAttendance) * 100 : 0;
                        $percentAlpha = $totalPossibleAttendance > 0 ? ($totalAlpha / $totalPossibleAttendance) * 100 : 0;
                        $percentHadir = $totalPossibleAttendance > 0 ? ($totalHadir / $totalPossibleAttendance) * 100 : 0;
                        ?>
                        
                        <!-- Total Row -->
                        <tr class="bg-gradient-to-r from-gray-100 to-gray-200 border-t-2 border-gray-400 font-bold text-gray-800">
                            <td colspan="<?= (2 + count($attendanceData['days'])) ?>" class="table-sticky-left bg-gradient-to-r from-gray-100 to-gray-200 px-4 py-4 text-center font-bold border-r border-gray-400">
                                <i class="fas fa-calculator mr-2"></i>TOTAL
                            </td>
                            <td class="px-3 py-4 text-center bg-yellow-100 text-yellow-800 border-r border-gray-300 font-bold text-lg">
                                <?= $totalSakit ?>
                            </td>
                            <td class="px-3 py-4 text-center bg-blue-100 text-blue-800 border-r border-gray-300 font-bold text-lg">
                                <?= $totalIzin ?>
                            </td>
                            <td class="px-3 py-4 text-center bg-red-100 text-red-800 border-r border-gray-300 font-bold text-lg">
                                <?= $totalAlpha ?>
                            </td>
                            <td class="px-3 py-4 text-center bg-green-100 text-green-800 border-r border-gray-300 font-bold text-lg">
                                <?= $totalHadir ?>
                            </td>
                            <td class="px-3 py-4 text-center bg-purple-100 text-purple-800 font-bold text-lg">
                                <?= number_format($percentHadir, 1) ?>%
                            </td>
                        </tr>

                        <!-- Percentage Row -->
                        <tr class="bg-gradient-to-r from-blue-50 to-purple-50 border-b-2 border-blue-300 font-bold text-gray-700">
                            <td colspan="<?= (2 + count($attendanceData['days'])) ?>" class="table-sticky-left bg-gradient-to-r from-blue-50 to-purple-50 px-4 py-4 text-center font-bold border-r border-blue-300">
                                <i class="fas fa-chart-pie mr-2"></i>PERSENTASE
                            </td>
                            <td class="px-3 py-4 text-center bg-yellow-100 text-yellow-800 border-r border-gray-300 font-bold">
                                <?= number_format($percentSakit, 1) ?>%
                            </td>
                            <td class="px-3 py-4 text-center bg-blue-100 text-blue-800 border-r border-gray-300 font-bold">
                                <?= number_format($percentIzin, 1) ?>%
                            </td>
                            <td class="px-3 py-4 text-center bg-red-100 text-red-800 border-r border-gray-300 font-bold">
                                <?= number_format($percentAlpha, 1) ?>%
                            </td>
                            <td class="px-3 py-4 text-center bg-green-100 text-green-800 border-r border-gray-300 font-bold">
                                <?= number_format($percentHadir, 1) ?>%
                            </td>
                            <td class="px-3 py-4 text-center bg-purple-100 text-purple-800 font-bold">
                                100%
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    <?php else: ?>
        <!-- Enhanced No Data Message -->
        <div class="bg-white rounded-xl shadow-lg p-8 text-center fade-in">
            <div class="max-w-md mx-auto">
                <div class="mb-6">
                    <i class="fas fa-clipboard-list text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Data Absensi</h3>
                    <p class="text-gray-500 leading-relaxed">
                        <?php if (!$filterKelas): ?>
                            Silakan pilih kelas dan periode untuk menampilkan data absensi siswa.
                        <?php else: ?>
                            Tidak ada data absensi untuk kelas <strong><?= $filterKelas ?></strong> 
                            pada bulan <strong><?= isset($filterBulan) ? date('F Y', strtotime($filterBulan . '-01')) : date('F Y') ?></strong>.
                        <?php endif; ?>
                    </p>
                </div>
                
                <div class="space-y-3">
                    <button 
                        onclick="document.getElementById('kelas')?.focus() || document.getElementById('bulan')?.focus()" 
                        class="w-full px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-medium rounded-xl transition-all duration-200 transform hover:-translate-y-0.5 shadow-lg hover:shadow-xl"
                    >
                        <i class="fas fa-search mr-2"></i>Pilih Data untuk Ditampilkan
                    </button>
                    
                    <button 
                        onclick="window.location.href='<?= base_url('admin/absensi/input') ?>'"
                        class="w-full px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-medium rounded-xl transition-all duration-200 transform hover:-translate-y-0.5 shadow-lg hover:shadow-xl"
                    >
                        <i class="fas fa-plus mr-2"></i>Input Data Absensi
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Enhanced JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced form functionality
    const filterForm = document.getElementById('filterForm');
    const kelasSelect = document.getElementById('kelas');
    const bulanInput = document.getElementById('bulan');
    const submitBtn = filterForm?.querySelector('button[type="submit"]');
    const downloadBtn = document.getElementById('downloadExcel');
    
    // Enhanced loading state
    function showLoading(button, text, icon = 'fa-spinner fa-spin') {
        if (!button) return;
        
        const originalContent = button.innerHTML;
        button.disabled = true;
        button.innerHTML = `<i class="fas ${icon} mr-2"></i>${text}`;
        button.classList.add('opacity-75', 'cursor-not-allowed');
        
        // Store original content for reset
        button.setAttribute('data-original', originalContent);
        
        return originalContent;
    }
    
    function hideLoading(button) {
        if (!button) return;
        
        const originalContent = button.getAttribute('data-original');
        if (originalContent) {
            button.disabled = false;
            button.innerHTML = originalContent;
            button.classList.remove('opacity-75', 'cursor-not-allowed');
            button.removeAttribute('data-original');
        }
    }
    
    // Smart auto-submit
    function autoSubmit() {
        const hasKelas = kelasSelect?.value || '<?= $userKelas ?? '' ?>';
        const hasBulan = bulanInput?.value;
        
        if (hasKelas && hasBulan) {
            showLoading(submitBtn, 'Memuat Data...', 'fa-sync fa-spin');
            
            // Add smooth transition
            const currentTable = document.getElementById('attendanceTable');
            if (currentTable) {
                currentTable.style.transition = 'all 0.3s ease';
                currentTable.style.opacity = '0.5';
                currentTable.style.transform = 'translateY(10px)';
            }
            
            setTimeout(() => filterForm?.submit(), 500);
        }
    }
    
    // Enhanced event listeners
    kelasSelect?.addEventListener('change', autoSubmit);
    bulanInput?.addEventListener('change', autoSubmit);
    
    // Form submission handler
    filterForm?.addEventListener('submit', function(e) {
        showLoading(submitBtn, 'Memuat Data...', 'fa-sync fa-spin');
    });
    
    // Excel download with progress
    downloadBtn?.addEventListener('click', function() {
        if (this.disabled) return;
        
        showLoading(this, 'Mengunduh...', 'fa-download fa-bounce');
        
        const exportUrl = '<?= base_url('admin/absensi/export') ?>?' + 
            new URLSearchParams({
                kelas: '<?= $filterKelas ?? '' ?>',
                start_date: '<?= $filterBulan ?? '' ?>-01',
                end_date: '<?= isset($filterBulan) ? date('Y-m-t', strtotime($filterBulan . '-01')) : '' ?>'
            });
        
        // Create hidden iframe for download
        const iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = exportUrl;
        document.body.appendChild(iframe);
        
        // Clean up and reset button
        setTimeout(() => {
            document.body.removeChild(iframe);
            hideLoading(this);
        }, 3000);
    });
    
    // Enhanced table animations
    const table = document.getElementById('attendanceTable');
    if (table) {
        // Initial fade-in
        table.style.opacity = '0';
        table.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            table.style.transition = 'all 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
            table.style.opacity = '1';
            table.style.transform = 'translateY(0)';
        }, 200);
        
        // Staggered row animation
        const rows = table.querySelectorAll('.student-row');
        rows.forEach((row, index) => {
            row.style.opacity = '0';
            row.style.transform = 'translateX(-20px)';
            
            setTimeout(() => {
                row.style.transition = 'all 0.6s ease';
                row.style.opacity = '1';
                row.style.transform = 'translateX(0)';
            }, 300 + (index * 50));
        });
    }
    
    // Enhanced scroll effects
    const tableContainer = document.getElementById('tableContainer');
    if (tableContainer) {
        let scrollTimeout;
        
        tableContainer.addEventListener('scroll', function() {
            const scrollLeft = this.scrollLeft;
            const scrollWidth = this.scrollWidth;
            const clientWidth = this.clientWidth;
            
            // Dynamic shadow effects
            this.classList.toggle('scroll-shadow-left', scrollLeft > 10);
            this.classList.toggle('scroll-shadow-right', scrollLeft < scrollWidth - clientWidth - 10);
            
            // Smooth scroll indicator
            clearTimeout(scrollTimeout);
            this.classList.add('scrolling');
            
            scrollTimeout = setTimeout(() => {
                this.classList.remove('scrolling');
            }, 150);
        });
    }
    
    // Enhanced hover effects for cells
    document.querySelectorAll('.attendance-cell').forEach(cell => {
        cell.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.15)';
            this.style.zIndex = '30';
            this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.3)';
            this.style.borderRadius = '8px';
        });
        
        cell.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.zIndex = 'auto';
            this.style.boxShadow = 'none';
            this.style.borderRadius = '0';
        });
    });
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + E for Excel export
        if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
            e.preventDefault();
            downloadBtn?.click();
        }
        
        // Ctrl/Cmd + R for refresh/submit
        if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
            e.preventDefault();
            submitBtn?.click();
        }
    });
    
    // Mobile optimization
    function optimizeForMobile() {
        const isMobile = window.innerWidth < 768;
        
        if (isMobile) {
            // Reduce animations on mobile
            document.querySelectorAll('.attendance-cell').forEach(cell => {
                cell.style.transition = 'all 0.2s ease';
            });
            
            // Add touch-friendly hover effects
            document.querySelectorAll('.attendance-cell').forEach(cell => {
                cell.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(1.1)';
                    this.style.zIndex = '30';
                });
                
                cell.addEventListener('touchend', function() {
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                        this.style.zIndex = 'auto';
                    }, 100);
                });
            });
        }
    }
    
    optimizeForMobile();
    window.addEventListener('resize', optimizeForMobile);
    
    // Performance optimization: Lazy loading for large tables
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    }, { threshold: 0.1 });
    
    document.querySelectorAll('.student-row').forEach(row => {
        observer.observe(row);
    });
    
    console.log('Enhanced Rekap Absensi loaded successfully! 🎉');
});

// Add some CSS for the scrolling effect
const style = document.createElement('style');
style.textContent = `
    .scrolling {
        background: linear-gradient(90deg, transparent 0%, rgba(59, 130, 246, 0.1) 50%, transparent 100%);
    }
    
    .fade-in {
        animation: fadeInUp 0.6s ease forwards;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(style);
</script>

<?= $this->endSection() ?>

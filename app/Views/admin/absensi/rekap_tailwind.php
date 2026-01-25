<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<!-- Tailwind CSS CDN (jika belum include di layout) -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.0/dist/tailwind.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
    /* Custom styles untuk enhance Tailwind */
    .table-sticky-left {
        position: sticky;
        left: 0;
        z-index: 20;
        box-shadow: 2px 0 8px rgba(0, 0, 0, 0.15);
    }

    .table-sticky-second {
        position: sticky;
        left: 60px;
        z-index: 15;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    }

    .attendance-cell {
        min-width: 45px;
        width: 45px;
        max-width: 45px;
        transition: all 0.3s ease;
    }

    .attendance-cell:hover {
        transform: scale(1.15);
        z-index: 25;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .gradient-header {
        background: linear-gradient(135deg, #3b82f6, #8b5cf6, #06b6d4);
        background-size: 200% 200%;
        animation: gradientShift 3s ease infinite;
    }

    @keyframes gradientShift {
        0% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }

        100% {
            background-position: 0% 50%;
        }
    }

    .scroll-indicator {
        background: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.1) 50%, transparent 100%);
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

    /* Print styles */
    @media print {
        .no-print {
            display: none !important;
        }

        .table-sticky-left,
        .table-sticky-second {
            position: static;
            box-shadow: none;
        }
    }
</style>

<div class="min-h-screen bg-gray-50 p-4 lg:p-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="gradient-header rounded-2xl p-6 text-white shadow-2xl">
            <div class="text-center">
                <h1 class="text-3xl lg:text-4xl font-bold mb-2 drop-shadow-lg">
                    📊 REKAP ABSENSI BULANAN
                </h1>
                <p class="text-lg lg:text-xl opacity-90 font-medium">
                    SDN GROGOL UTARA 09
                </p>
                <div class="mt-3 text-sm lg:text-base opacity-80">
                    KELAS: <?= strtoupper($filterKelas ?? '5A') ?> |
                    BULAN: <?= strtoupper($bulan_nama ?? 'JULI') ?> <?= $tahun ?? 2025 ?> |
                    TAHUN PELAJARAN: <?= ($tahun ?? 2025) ?>/<?= ($tahun ?? 2025) + 1 ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-cyan-500 rounded-xl p-6 shadow-xl">
            <form id="filterForm" method="GET" class="space-y-4 lg:space-y-0">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
                    <!-- Kelas Filter -->
                    <div class="space-y-2">
                        <label for="kelas" class="block text-white font-semibold text-sm">
                            <i class="fas fa-school mr-2"></i>Kelas
                        </label>
                        <?php if ($userRole === 'admin'): ?>
                            <select
                                id="kelas"
                                name="kelas"
                                class="w-full px-4 py-3 rounded-lg border-0 bg-white/95 backdrop-blur-sm shadow-lg focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all duration-300 font-medium"
                                required>
                                <option value="">Pilih Kelas</option>
                                <?php foreach ($allKelas as $kelasItem): ?>
                                    <?php
                                    // Check if kelas already contains "Kelas" to avoid duplication
                                    $displayText = (strpos($kelasItem['kelas'], 'Kelas') === 0)
                                        ? $kelasItem['kelas']
                                        : 'Kelas ' . $kelasItem['kelas'];
                                    ?>
                                    <option value="<?= $kelasItem['kelas'] ?>"
                                        <?= $filterKelas === $kelasItem['kelas'] ? 'selected' : '' ?>>
                                        <?= $displayText ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <?php
                            // Check if userKelas already contains "Kelas" to avoid duplication
                            $displayUserKelas = (strpos($userKelas, 'Kelas') === 0)
                                ? $userKelas
                                : 'Kelas ' . $userKelas;
                            ?>
                            <input
                                type="text"
                                class="w-full px-4 py-3 rounded-lg border-0 bg-white/95 backdrop-blur-sm shadow-lg font-medium"
                                value="<?= $displayUserKelas ?>"
                                readonly>
                            <input type="hidden" name="kelas" value="<?= $userKelas ?>">
                        <?php endif; ?>
                    </div>

                    <!-- Bulan Filter -->
                    <div class="space-y-2">
                        <label for="bulan" class="block text-white font-semibold text-sm">
                            <i class="fas fa-calendar-alt mr-2"></i>Bulan & Tahun
                        </label>
                        <input
                            type="month"
                            id="bulan"
                            name="bulan"
                            class="w-full px-4 py-3 rounded-lg border-0 bg-white/95 backdrop-blur-sm shadow-lg focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all duration-300 font-medium"
                            value="<?= $filterBulan ?>"
                            required>
                    </div>

                    <!-- Tampilkan Button -->
                    <div class="space-y-2">
                        <label class="block text-transparent text-sm">Action</label>
                        <button
                            type="submit"
                            class="w-full px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-bold rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 uppercase tracking-wide">
                            <i class="fas fa-search mr-2"></i>Tampilkan
                        </button>
                    </div>

                    <!-- Excel Button -->
                    <div class="space-y-2">
                        <label class="block text-transparent text-sm">Export</label>
                        <button
                            type="button"
                            id="downloadExcel"
                            class="w-full px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white font-bold rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 uppercase tracking-wide disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                            <?= empty($attendanceData) || empty($attendanceData['students']) ? 'disabled' : '' ?>>
                            <i class="fas fa-download mr-2"></i>Excel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Professional Header Section -->
    <?php if (!empty($attendanceData) && !empty($attendanceData['students'])): ?>
        <div class="mb-6">
            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200">
                <div class="text-center space-y-2">
                    <h2 class="text-2xl font-bold text-gray-800 tracking-wide">DAFTAR HADIR PESERTA DIDIK</h2>
                    <h3 class="text-xl font-semibold text-blue-600">SDN GROGOL UTARA 09</h3>
                    <h4 class="text-lg font-medium text-gray-700">KELAS <?= strtoupper($attendanceData['kelas'] ?? 'KELAS 2 A') ?></h4>
                    <p class="text-sm text-gray-500">TAHUN PELAJARAN <?= date('Y') ?>/<?= (date('Y') + 1) ?></p>
                    <div class="inline-block mt-4 px-6 py-2 bg-gradient-to-r from-blue-100 to-cyan-100 rounded-full border border-blue-200">
                        <span class="text-lg font-bold text-blue-700">
                            BULAN <?= strtoupper(date('F Y', mktime(0, 0, 0, (int)$attendanceData['month'], 1, (int)$attendanceData['year']))) ?>
                        </span>
                    </div>

                    <!-- HBE Display Box -->
                    <div class="mt-4 inline-block px-4 py-2 bg-red-100 border-2 border-red-300 rounded-lg">
                        <span class="text-sm font-bold text-red-700">
                            HBE (Hari Efektif): <?= isset($attendanceData['effective_days']) ? $attendanceData['effective_days'] : count($attendanceData['days'] ?? []) ?> hari
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Table -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-gray-200">
            <!-- Table Header Info -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-table mr-2 text-blue-500"></i>
                        Data Absensi
                    </h3>
                    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600">
                        <?php
                        $legend = [
                            ['color' => 'bg-green-200', 'label' => 'Hadir (✓)', 'icon' => 'fa-check'],
                            ['color' => 'bg-yellow-200', 'label' => 'Sakit (S)', 'icon' => 'fa-thermometer-half'],
                            ['color' => 'bg-blue-200', 'label' => 'Izin (I)', 'icon' => 'fa-info-circle'],
                            ['color' => 'bg-red-200', 'label' => 'Alpha (A)', 'icon' => 'fa-times'],
                            ['color' => 'bg-purple-200', 'label' => 'Libur Nasional (🏛️)', 'icon' => 'fa-flag'],
                            ['color' => 'bg-indigo-200', 'label' => 'Libur Sekolah (🏫)', 'icon' => 'fa-school'],
                            ['color' => 'bg-orange-200', 'label' => 'Weekend (🏠)', 'icon' => 'fa-home'],
                            ['color' => 'bg-gray-200', 'label' => 'Off/Libur (📴)', 'icon' => 'fa-power-off'],
                        ];
                        ?>
                        <?php foreach ($legend as $item): ?>
                            <div class="flex items-center gap-2 px-2 py-1 bg-white rounded-lg shadow-sm border">
                                <div class="w-3 h-3 <?= $item['color'] ?> rounded-full"></div>
                                <i class="fas <?= $item['icon'] ?> text-xs text-gray-600"></i>
                                <span class="text-gray-700 font-medium text-xs"><?= $item['label'] ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Horizontal Scroll Notice -->
            <div class="px-6 py-3 bg-blue-50 border-b border-blue-200 lg:hidden">
                <p class="text-sm text-blue-700 flex items-center">
                    <i class="fas fa-arrows-alt-h mr-2"></i>
                    Geser ke kanan untuk melihat semua tanggal
                </p>
            </div>

            <!-- Table Container -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="gradient-header text-white">
                            <!-- Sticky Left Columns -->
                            <th class="table-sticky-left bg-gradient-to-r from-blue-600 to-purple-600 px-3 py-4 text-center font-bold border-r-2 border-white/20">
                                No
                            </th>
                            <th class="table-sticky-second bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-4 text-left font-bold border-r-2 border-white/20 min-w-[200px]">
                                Nama Siswa
                            </th>

                            <!-- Daily Columns -->
                            <?php foreach ($attendanceData['days'] as $day): ?>
                                <?php
                                $dayStr = str_pad($day, 2, '0', STR_PAD_LEFT);
                                $currentDate = $attendanceData['year'] . '-' . str_pad($attendanceData['month'], 2, '0', STR_PAD_LEFT) . '-' . $dayStr;
                                $isWeekend = date('w', strtotime($currentDate)) == 0 || date('w', strtotime($currentDate)) == 6;
                                $isHoliday = in_array($currentDate, $attendanceData['holidays'] ?? []);

                                // Get holiday details
                                $holidayInfo = null;
                                if (isset($attendanceData['holidayDetails'][$currentDate])) {
                                    $holidayInfo = $attendanceData['holidayDetails'][$currentDate];
                                }

                                // Determine header color based on type
                                $headerClass = 'px-2 py-4 text-center font-bold border-r border-white/10 attendance-cell';
                                $icon = '';
                                $tooltip = date('D, d M Y', strtotime($currentDate));

                                if ($holidayInfo) {
                                    switch ($holidayInfo['status']) {
                                        case 'libur_nasional':
                                            $headerClass .= ' bg-purple-500';
                                            $icon = '🏛️';
                                            $tooltip .= ' - ' . $holidayInfo['keterangan'];
                                            break;
                                        case 'libur_sekolah':
                                            $headerClass .= ' bg-indigo-500';
                                            $icon = '🏫';
                                            $tooltip .= ' - ' . $holidayInfo['keterangan'];
                                            break;
                                        case 'off':
                                            $headerClass .= ' bg-gray-500';
                                            $icon = '📴';
                                            $tooltip .= ' - ' . $holidayInfo['keterangan'];
                                            break;
                                    }
                                } else if ($isWeekend) {
                                    $headerClass .= ' bg-orange-500';
                                    $icon = '🏠';
                                    $tooltip .= ' - Weekend';
                                } else {
                                    $headerClass .= ' bg-blue-500';
                                }
                                ?>
                                <th class="<?= $headerClass ?>" title="<?= $tooltip ?>">
                                    <div class="text-base font-bold"><?= $day ?></div>
                                    <?php if ($icon): ?>
                                        <div class="text-xs opacity-75 mt-1"><?= $icon ?></div>
                                    <?php endif; ?>
                                </th>
                            <?php endforeach; ?>

                            <!-- Summary Columns -->
                            <th class="px-3 py-4 text-center font-bold bg-yellow-500 border-r border-white/10 min-w-[50px]">S</th>
                            <th class="px-3 py-4 text-center font-bold bg-blue-500 border-r border-white/10 min-w-[50px]">I</th>
                            <th class="px-3 py-4 text-center font-bold bg-red-500 border-r border-white/10 min-w-[50px]">A</th>
                            <th class="px-3 py-4 text-center font-bold bg-green-500 min-w-[60px]">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($attendanceData['students'] as $index => $student): ?>
                            <tr class="<?= $index % 2 === 0 ? 'bg-gray-50' : 'bg-white' ?> hover:bg-blue-50 transition-colors duration-200 border-b border-gray-100">
                                <!-- Sticky Left Columns -->
                                <td class="table-sticky-left <?= $index % 2 === 0 ? 'bg-gray-50' : 'bg-white' ?> hover:bg-blue-50 px-3 py-3 text-center font-medium border-r border-gray-200">
                                    <?= $no++ ?>
                                </td>
                                <td class="table-sticky-left <?= $index % 2 === 0 ? 'bg-gray-50' : 'bg-white' ?> hover:bg-blue-50 px-4 py-3 font-medium text-gray-800 border-r border-gray-200" title="<?= esc($student['nama']) ?>" style="left: 60px;">
                                    <div class="truncate max-w-[180px]">
                                        <?= esc($student['nama']) ?>
                                    </div>
                                </td>

                                <!-- Daily Attendance -->
                                <?php foreach ($attendanceData['days'] as $day): ?>
                                    <?php
                                    $dayStr = str_pad($day, 2, '0', STR_PAD_LEFT);
                                    $currentDate = $attendanceData['year'] . '-' . str_pad($attendanceData['month'], 2, '0', STR_PAD_LEFT) . '-' . $dayStr;

                                    $status = $student['daily'][$dayStr] ?? '';

                                    // Get holiday details if available
                                    $holidayInfo = '';
                                    if (isset($attendanceData['holidayDetails'][$currentDate])) {
                                        $holidayInfo = ' - ' . $attendanceData['holidayDetails'][$currentDate]['keterangan'];
                                    }

                                    // Use AttendanceHelper for consistent styling
                                    $mark = match ($status) {
                                        'hadir' => '✓',
                                        'izin' => 'I',
                                        'sakit' => 'S',
                                        'alpha' => 'A',
                                        'libur_nasional' => '🏛️',
                                        'libur_sekolah' => '🏫',
                                        'off' => '📴',
                                        'weekend' => '🏠',
                                        default => strtotime($currentDate) > time() ? '-' : 'A'
                                    };

                                    $cellClass = 'px-2 py-3 text-center font-bold text-sm border-r border-gray-100 attendance-cell transition-colors duration-200';
                                    $cellClass .= match ($status) {
                                        'hadir' => ' bg-green-100 text-green-700 hover:bg-green-200',
                                        'izin' => ' bg-blue-100 text-blue-700 hover:bg-blue-200',
                                        'sakit' => ' bg-yellow-100 text-yellow-700 hover:bg-yellow-200',
                                        'alpha' => ' bg-red-100 text-red-700 hover:bg-red-200',
                                        'libur_nasional' => ' bg-purple-100 text-purple-800',
                                        'libur_sekolah' => ' bg-indigo-100 text-indigo-800',
                                        'off' => ' bg-gray-100 text-gray-600',
                                        'weekend' => ' bg-orange-100 text-orange-800',
                                        default => strtotime($currentDate) > time() ? ' bg-gray-50 text-gray-400' : ' bg-red-100 text-red-700 hover:bg-red-200'
                                    };

                                    $statusDesc = match ($status) {
                                        'hadir' => 'Hadir',
                                        'izin' => 'Izin',
                                        'sakit' => 'Sakit',
                                        'alpha' => 'Alpha/Tidak Hadir',
                                        'libur_nasional' => 'Libur Nasional',
                                        'libur_sekolah' => 'Libur Sekolah',
                                        'off' => 'Libur/Off',
                                        'weekend' => 'Weekend',
                                        default => strtotime($currentDate) > time() ? 'Belum ada data' : 'Alpha'
                                    };
                                    ?>
                                    <td class="<?= $cellClass ?>"
                                        title="<?= date('D, d M Y', strtotime($currentDate)) . ' - ' . $statusDesc . $holidayInfo ?>">
                                        <?= $mark ?>
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
                                <td class="px-3 py-3 text-center font-bold bg-green-50 text-green-700">
                                    <?= $student['summary']['hadir'] ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php
                        // Calculate totals with correct formula
                        $finalTotalSakit = array_sum(array_map(fn($s) => $s['summary']['sakit'], $attendanceData['students']));
                        $finalTotalIzin = array_sum(array_map(fn($s) => $s['summary']['izin'], $attendanceData['students']));
                        $finalTotalAlpha = array_sum(array_map(fn($s) => $s['summary']['alpha'], $attendanceData['students']));
                        $finalTotalHadir = array_sum(array_map(fn($s) => $s['summary']['hadir'], $attendanceData['students']));

                        $totalStudents = count($attendanceData['students']);
                        $effectiveDays = isset($attendanceData['effective_days']) ? $attendanceData['effective_days'] : count($attendanceData['days']);
                        $totalAttendanceDays = $totalStudents * $effectiveDays;

                        // Correct percentage formula: Total / (effective_days * total_students) * 100%
                        $percentageSakit = $totalAttendanceDays > 0 ? ($finalTotalSakit / $totalAttendanceDays) * 100 : 0;
                        $percentageIzin = $totalAttendanceDays > 0 ? ($finalTotalIzin / $totalAttendanceDays) * 100 : 0;
                        $percentageAlpha = $totalAttendanceDays > 0 ? ($finalTotalAlpha / $totalAttendanceDays) * 100 : 0;
                        ?>

                        <!-- Total Row -->
                        <tr class="bg-gray-100 border-t-2 border-gray-300 font-bold">
                            <td class="table-sticky-left bg-gray-100 px-3 py-4 text-center font-bold text-gray-800 border-r border-gray-300">
                                -
                            </td>
                            <td class="table-sticky-second bg-gray-100 px-4 py-4 text-center font-bold text-gray-800 border-r border-gray-300">
                                TOTAL
                            </td>
                            <?php for ($i = 0; $i < count($attendanceData['days']); $i++): ?>
                                <td class="px-2 py-4 text-center bg-gray-100 border-r border-gray-200">-</td>
                            <?php endfor; ?>
                            <td class="px-3 py-4 text-center bg-yellow-100 text-yellow-800 border-r border-gray-200">
                                <?= $finalTotalSakit ?>
                            </td>
                            <td class="px-3 py-4 text-center bg-blue-100 text-blue-800 border-r border-gray-200">
                                <?= $finalTotalIzin ?>
                            </td>
                            <td class="px-3 py-4 text-center bg-red-100 text-red-800 border-r border-gray-200">
                                <?= $finalTotalAlpha ?>
                            </td>
                            <td class="px-3 py-4 text-center bg-green-100 text-green-800">
                                <?= $finalTotalHadir ?>
                            </td>
                        </tr>

                        <!-- Percentage Row -->
                        <tr class="bg-blue-50 border-b-2 border-blue-200 font-bold">
                            <td class="table-sticky-left bg-blue-50 px-3 py-4 text-center font-bold text-blue-800 border-r border-blue-200">
                                -
                            </td>
                            <td class="table-sticky-second bg-blue-50 px-4 py-4 text-center font-bold text-blue-800 border-r border-blue-200">
                                PERSENTASE
                            </td>
                            <?php for ($i = 0; $i < count($attendanceData['days']); $i++): ?>
                                <td class="px-2 py-4 text-center bg-blue-50 border-r border-gray-200">-</td>
                            <?php endfor; ?>
                            <td class="px-3 py-4 text-center bg-yellow-100 text-yellow-800 border-r border-gray-200">
                                <?= number_format($percentageSakit, 1) ?>%
                            </td>
                            <td class="px-3 py-4 text-center bg-blue-100 text-blue-800 border-r border-gray-200">
                                <?= number_format($percentageIzin, 1) ?>%
                            </td>
                            <td class="px-3 py-4 text-center bg-red-100 text-red-800 border-r border-gray-200">
                                <?= number_format($percentageAlpha, 1) ?>%
                            </td>
                            <td class="px-3 py-4 text-center bg-blue-50 text-blue-600">
                                Formula: Total / (<?= $effectiveDays ?> × <?= $totalStudents ?>) × 100%
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    <?php else: ?>
        <!-- No Data Message -->
        <div class="bg-white rounded-xl shadow-lg p-8 text-center">
            <div class="max-w-md mx-auto">
                <i class="fas fa-info-circle text-6xl text-gray-400 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak Ada Data</h3>
                <p class="text-gray-500">
                    <?php if (!$filterKelas): ?>
                        Silakan pilih kelas terlebih dahulu untuk menampilkan data absensi.
                    <?php else: ?>
                        Tidak ada data absensi untuk kelas <?= $filterKelas ?> pada bulan
                        <?= isset($filterBulan) ? date('F Y', strtotime($filterBulan . '-01')) : date('F Y') ?>.
                    <?php endif; ?>
                </p>
                <button
                    onclick="document.getElementById('kelas').focus()"
                    class="mt-4 px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-search mr-2"></i>Pilih Data
                </button>
            </div>
        </div>
    <?php endif; ?>

</div>

<!-- JavaScript for Enhanced UX -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form submission with loading states
        const filterForm = document.getElementById('filterForm');
        const kelasSelect = document.getElementById('kelas');
        const bulanInput = document.getElementById('bulan');
        const submitBtn = filterForm.querySelector('button[type="submit"]');
        const downloadBtn = document.getElementById('downloadExcel');

        // Auto submit functionality
        function autoSubmit() {
            const hasKelas = kelasSelect?.value || '<?= $userKelas ?? '' ?>';
            const hasBulan = bulanInput?.value;

            if (hasKelas && hasBulan) {
                showLoading(submitBtn, 'Memuat Data...');
                setTimeout(() => filterForm.submit(), 300);
            }
        }

        // Loading state helper
        function showLoading(button, text) {
            const originalContent = button.innerHTML;
            button.disabled = true;
            button.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i>${text}`;
            button.classList.add('opacity-75');
        }

        // Event listeners
        if (kelasSelect) {
            kelasSelect.addEventListener('change', autoSubmit);
        }

        if (bulanInput) {
            bulanInput.addEventListener('change', autoSubmit);
        }

        // Manual form submission
        filterForm.addEventListener('submit', function(e) {
            showLoading(submitBtn, 'Memuat Data...');
        });

        // Excel download functionality with XLSX format
        if (downloadBtn) {
            downloadBtn.addEventListener('click', function() {
                if (!this.disabled) {
                    showLoading(this, 'Download...');

                    const exportUrl = '<?= base_url('admin/absensi-enhanced/export-excel') ?>?' +
                        new URLSearchParams({
                            kelas: '<?= $filterKelas ?? '' ?>',
                            bulan: '<?= isset($filterBulan) ? date('n', strtotime($filterBulan . '-01')) : date('n') ?>',
                            tahun: '<?= isset($filterBulan) ? date('Y', strtotime($filterBulan . '-01')) : date('Y') ?>'
                        });

                    window.location.href = exportUrl;

                    // Reset button after delay
                    setTimeout(() => {
                        this.disabled = false;
                        this.innerHTML = '<i class="fas fa-download mr-2"></i>Excel';
                        this.classList.remove('opacity-75');
                    }, 2000);
                }
            });
        }

        // Smooth table animations
        const table = document.querySelector('table');
        if (table) {
            table.style.opacity = '0';
            table.style.transform = 'translateY(20px)';

            setTimeout(() => {
                table.style.transition = 'all 0.6s ease';
                table.style.opacity = '1';
                table.style.transform = 'translateY(0)';
            }, 100);
        }

        // Enhanced hover effects for attendance cells
        document.querySelectorAll('.attendance-cell').forEach(cell => {
            cell.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.1)';
                this.style.zIndex = '20';
            });

            cell.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
                this.style.zIndex = 'auto';
            });
        });

        // Responsive table scroll indicator
        const tableContainer = document.querySelector('.overflow-x-auto');
        if (tableContainer) {
            tableContainer.addEventListener('scroll', function() {
                const scrollLeft = this.scrollLeft;
                const scrollWidth = this.scrollWidth;
                const clientWidth = this.clientWidth;

                // Add scroll shadow effects
                if (scrollLeft > 0) {
                    this.classList.add('scroll-shadow-left');
                } else {
                    this.classList.remove('scroll-shadow-left');
                }

                if (scrollLeft < scrollWidth - clientWidth) {
                    this.classList.add('scroll-shadow-right');
                } else {
                    this.classList.remove('scroll-shadow-right');
                }
            });
        }
    });
</script>

<?= $this->endSection() ?>
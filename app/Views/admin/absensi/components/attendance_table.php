<?php
// Load helper
use App\Helpers\AttendanceHelper;
?>

<!-- Attendance Table Component -->
<div class="bg-white rounded-xl shadow-xl overflow-hidden border border-gray-200 fade-in-up" style="animation-delay: 1s;">
    
    <!-- Table Header Info -->
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-table mr-2 text-blue-500"></i>
                Data Kehadiran
            </h3>
            
            <!-- Enhanced Legend -->
            <div class="flex flex-wrap items-center gap-3 text-sm">
                <?php foreach ($legend as $item): ?>
                    <div class="flex items-center gap-2 px-3 py-1 bg-white rounded-lg shadow-sm border">
                        <div class="w-3 h-3 <?= $item['color'] ?> rounded-full"></div>
                        <i class="fas <?= $item['icon'] ?> text-xs text-gray-600"></i>
                        <span class="text-gray-700 font-medium"><?= $item['label'] ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Mobile Scroll Notice -->
    <div class="px-6 py-3 bg-blue-50 border-b border-blue-200 lg:hidden no-print">
        <div class="flex items-center justify-between">
            <p class="text-sm text-blue-700 flex items-center">
                <i class="fas fa-arrows-alt-h mr-2"></i>
                Geser tabel ke kanan untuk melihat semua tanggal
            </p>
            <button 
                onclick="document.getElementById('tableContainer').scrollLeft = 0"
                class="text-xs bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded transition-colors"
            >
                Kembali ke Awal
            </button>
        </div>
    </div>

    <!-- Table Container with Custom Scrollbar -->
    <div class="overflow-x-auto custom-scrollbar" id="tableContainer">
        <table class="w-full text-sm relative" id="attendanceTable">
            <thead>
                <tr class="gradient-header text-white">
                    <!-- Sticky Left Columns -->
                    <th class="table-sticky-left bg-gradient-to-r from-blue-600 to-purple-600 px-3 py-4 text-center font-bold border-r-2 border-white/30 min-w-[60px]">
                        <i class="fas fa-hashtag text-xs block mb-1"></i>
                        <span class="text-sm">No</span>
                    </th>
                    <th class="table-sticky-second bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-4 text-left font-bold border-r-2 border-white/30 min-w-[200px]">
                        <i class="fas fa-user text-xs block mb-1"></i>
                        <span class="text-sm">Nama Siswa</span>
                    </th>

                    <!-- Date Columns -->
                    <?php foreach ($attendanceData['days'] as $day): ?>
                        <?php 
                        $dayStr = str_pad($day, 2, '0', STR_PAD_LEFT);
                        $currentDate = $attendanceData['year'] . '-' . str_pad($attendanceData['month'], 2, '0', STR_PAD_LEFT) . '-' . $dayStr;
                        $isWeekend = AttendanceHelper::isWeekend($currentDate);
                        $isHoliday = in_array($currentDate, $attendanceData['holidays'] ?? []);
                        
                        // Get holiday details
                        $holidayInfo = null;
                        if (isset($attendanceData['holidayDetails'][$currentDate])) {
                            $holidayInfo = $attendanceData['holidayDetails'][$currentDate];
                        }
                        
                        // Determine header color based on type
                        $headerColor = 'bg-blue-500';
                        $icon = '';
                        $tooltip = AttendanceHelper::formatDate($currentDate);
                        
                        if ($holidayInfo) {
                            switch ($holidayInfo['status']) {
                                case 'libur_nasional':
                                    $headerColor = 'bg-purple-500';
                                    $icon = '🏛️';
                                    $tooltip .= ' - ' . $holidayInfo['keterangan'];
                                    break;
                                case 'libur_sekolah':
                                    $headerColor = 'bg-indigo-500';
                                    $icon = '🏫';
                                    $tooltip .= ' - ' . $holidayInfo['keterangan'];
                                    break;
                                case 'off':
                                    $headerColor = 'bg-gray-500';
                                    $icon = '📴';
                                    $tooltip .= ' - ' . $holidayInfo['keterangan'];
                                    break;
                            }
                        } else if ($isWeekend) {
                            $headerColor = 'bg-orange-500';
                            $icon = '🏠';
                            $tooltip .= ' - Weekend';
                        }
                        ?>
                        <th class="px-2 py-4 text-center font-bold border-r border-white/20 attendance-cell <?= $headerColor ?>" 
                            title="<?= $tooltip ?>">
                            <div class="text-xs opacity-75 mb-1">
                                <?= date('D', strtotime($currentDate)) ?>
                            </div>
                            <div class="text-base font-bold"><?= $day ?></div>
                            <?php if ($icon): ?>
                                <div class="text-xs opacity-75 mt-1">
                                    <?= $icon ?>
                                </div>
                            <?php endif; ?>
                        </th>
                    <?php endforeach; ?>

                    <!-- Summary Columns -->
                    <th class="px-3 py-4 text-center font-bold bg-yellow-500 border-r border-white/20 min-w-[50px]">
                        <i class="fas fa-thermometer-half text-xs block mb-1"></i>
                        <span class="text-sm">S</span>
                    </th>
                    <th class="px-3 py-4 text-center font-bold bg-blue-500 border-r border-white/20 min-w-[50px]">
                        <i class="fas fa-info-circle text-xs block mb-1"></i>
                        <span class="text-sm">I</span>
                    </th>
                    <th class="px-3 py-4 text-center font-bold bg-red-500 border-r border-white/20 min-w-[50px]">
                        <i class="fas fa-times text-xs block mb-1"></i>
                        <span class="text-sm">A</span>
                    </th>
                    <th class="px-3 py-4 text-center font-bold bg-green-500 border-r border-white/20 min-w-[60px]">
                        <i class="fas fa-check text-xs block mb-1"></i>
                        <span class="text-sm">Total</span>
                    </th>
                    <th class="px-3 py-4 text-center font-bold bg-purple-500 min-w-[60px]">
                        <i class="fas fa-percentage text-xs block mb-1"></i>
                        <span class="text-sm">%</span>
                    </th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <?php $no = 1; ?>
                <?php foreach ($attendanceData['students'] as $index => $student): ?>
                    <tr class="<?= $index % 2 === 0 ? 'bg-gray-50' : 'bg-white' ?> hover:bg-blue-50 transition-all duration-200 border-b border-gray-100 student-row">
                        
                        <!-- Sticky Left Columns -->
                        <td class="table-sticky-left <?= $index % 2 === 0 ? 'bg-gray-50' : 'bg-white' ?> hover:bg-blue-50 px-3 py-3 text-center font-semibold border-r border-gray-200 text-gray-700">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mx-auto text-blue-700 font-bold text-sm">
                                <?= $no++ ?>
                            </div>
                        </td>
                        <td class="table-sticky-second <?= $index % 2 === 0 ? 'bg-gray-50' : 'bg-white' ?> hover:bg-blue-50 px-4 py-3 font-medium text-gray-800 border-r border-gray-200" 
                            title="<?= esc($student['nama']) ?>">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center mr-3 text-white font-bold text-sm">
                                    <?= strtoupper(substr($student['nama'], 0, 1)) ?>
                                </div>
                                <div class="truncate max-w-[160px] font-semibold">
                                    <?= esc($student['nama']) ?>
                                </div>
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
                            
                            $symbol = AttendanceHelper::getStatusSymbol($status);
                            $colorClass = AttendanceHelper::getStatusColor($status);
                            $statusDescription = AttendanceHelper::getStatusDescription($status) . $holidayInfo;
                            ?>
                            <td class="px-2 py-3 text-center font-bold border-r border-gray-100 attendance-cell <?= $colorClass ?> transition-all duration-200" 
                                title="<?= AttendanceHelper::formatDate($currentDate) . ' - ' . $statusDescription ?>"
                                data-date="<?= $currentDate ?>"
                                data-status="<?= $status ?>">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center mx-auto text-sm font-bold">
                                    <?= $symbol ?>
                                </div>
                            </td>
                        <?php endforeach; ?>

                        <!-- Summary Columns -->
                        <td class="px-3 py-3 text-center font-bold bg-yellow-50 text-yellow-700 border-r border-gray-100">
                            <div class="w-8 h-8 bg-yellow-200 rounded-full flex items-center justify-center mx-auto font-bold">
                                <?= $student['summary']['sakit'] ?>
                            </div>
                        </td>
                        <td class="px-3 py-3 text-center font-bold bg-blue-50 text-blue-700 border-r border-gray-100">
                            <div class="w-8 h-8 bg-blue-200 rounded-full flex items-center justify-center mx-auto font-bold">
                                <?= $student['summary']['izin'] ?>
                            </div>
                        </td>
                        <td class="px-3 py-3 text-center font-bold bg-red-50 text-red-700 border-r border-gray-100">
                            <div class="w-8 h-8 bg-red-200 rounded-full flex items-center justify-center mx-auto font-bold">
                                <?= $student['summary']['alpha'] ?>
                            </div>
                        </td>
                        <td class="px-3 py-3 text-center font-bold bg-green-50 text-green-700 border-r border-gray-100">
                            <div class="w-8 h-8 bg-green-200 rounded-full flex items-center justify-center mx-auto font-bold">
                                <?= $student['summary']['hadir'] ?>
                            </div>
                        </td>
                        <td class="px-3 py-3 text-center font-bold bg-purple-50 text-purple-700">
                            <div class="flex items-center justify-center">
                                <span class="text-lg font-bold"><?= AttendanceHelper::formatNumber($student['percentage']) ?>%</span>
                                <div class="ml-2 w-3 h-3 rounded-full <?= AttendanceHelper::getPercentageBadgeColor($student['percentage']) ?>"></div>
                            </div>
                            <?php if (AttendanceHelper::isLowAttendance($student['percentage'])): ?>
                                <div class="text-xs text-red-600 mt-1">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <!-- Summary Rows -->
                <tr class="bg-gradient-to-r from-gray-100 to-gray-200 border-t-2 border-gray-400 font-bold text-gray-800">
                    <td colspan="<?= (2 + count($attendanceData['days'])) ?>" class="table-sticky-left bg-gradient-to-r from-gray-100 to-gray-200 px-4 py-4 text-center font-bold border-r border-gray-400">
                        <i class="fas fa-calculator mr-2"></i>TOTAL
                    </td>
                    <td class="px-3 py-4 text-center bg-yellow-100 text-yellow-800 border-r border-gray-300 font-bold text-lg">
                        <?= $stats['total_sakit'] ?>
                    </td>
                    <td class="px-3 py-4 text-center bg-blue-100 text-blue-800 border-r border-gray-300 font-bold text-lg">
                        <?= $stats['total_izin'] ?>
                    </td>
                    <td class="px-3 py-4 text-center bg-red-100 text-red-800 border-r border-gray-300 font-bold text-lg">
                        <?= $stats['total_alpha'] ?>
                    </td>
                    <td class="px-3 py-4 text-center bg-green-100 text-green-800 border-r border-gray-300 font-bold text-lg">
                        <?= $stats['total_hadir'] ?>
                    </td>
                    <td class="px-3 py-4 text-center bg-purple-100 text-purple-800 font-bold text-lg">
                        <?= AttendanceHelper::formatNumber($stats['percent_hadir']) ?>%
                    </td>
                </tr>

                <!-- Percentage Row -->
                <tr class="bg-gradient-to-r from-blue-50 to-purple-50 border-b-2 border-blue-300 font-bold text-gray-700">
                    <td colspan="<?= (2 + count($attendanceData['days'])) ?>" class="table-sticky-left bg-gradient-to-r from-blue-50 to-purple-50 px-4 py-4 text-center font-bold border-r border-blue-300">
                        <i class="fas fa-chart-pie mr-2"></i>PERSENTASE
                    </td>
                    <td class="px-3 py-4 text-center bg-yellow-100 text-yellow-800 border-r border-gray-300 font-bold">
                        <?= AttendanceHelper::formatNumber($stats['percent_sakit']) ?>%
                    </td>
                    <td class="px-3 py-4 text-center bg-blue-100 text-blue-800 border-r border-gray-300 font-bold">
                        <?= AttendanceHelper::formatNumber($stats['percent_izin']) ?>%
                    </td>
                    <td class="px-3 py-4 text-center bg-red-100 text-red-800 border-r border-gray-300 font-bold">
                        <?= AttendanceHelper::formatNumber($stats['percent_alpha']) ?>%
                    </td>
                    <td class="px-3 py-4 text-center bg-green-100 text-green-800 border-r border-gray-300 font-bold">
                        <?= AttendanceHelper::formatNumber($stats['percent_hadir']) ?>%
                    </td>
                    <td class="px-3 py-4 text-center bg-purple-100 text-purple-800 font-bold">
                        100%
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Table Footer with Actions -->
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 no-print">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <p class="text-sm text-gray-600">
                <i class="fas fa-info-circle mr-2"></i>
                Menampilkan data <?= count($attendanceData['students']) ?> siswa untuk <?= count($attendanceData['days']) ?> hari efektif
            </p>
            
            <div class="flex gap-2">
                <button 
                    onclick="window.print()" 
                    class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors duration-200 text-sm"
                >
                    <i class="fas fa-print mr-2"></i>Print
                </button>
                <button 
                    id="downloadExcel" 
                    class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors duration-200 text-sm"
                >
                    <i class="fas fa-file-excel mr-2"></i>Download Excel
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<!-- Header removed per simplification (title, greeting, refresh, last updated) -->
<?php if (isset($dbError) && $dbError): ?>
<div class="mb-6 mx-4 sm:mx-6 lg:mx-8 bg-yellow-50 border-l-4 border-yellow-400 rounded-r-xl p-4 shadow-sm">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="ml-3">
            <h3 class="text-base font-semibold text-yellow-800">Peringatan Database</h3>
            <div class="mt-2 text-sm text-yellow-700">
                <p><?= esc($errorMessage) ?></p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Statistics Cards - Mobile Optimized -->
<div class="px-4 sm:px-6 lg:px-8 mb-6 sm:mb-8 lg:mb-10">
    <?php if (isset($isWalikelas) && $isWalikelas): ?>
    <!-- Walikelas Dashboard Cards -->
    <?php 
        $dp = $dailyProgress ?? [
            'attendance_present'=>0,'attendance_marked'=>0,'habit_logged'=>0,'total_students'=>$totalSiswa ?? 0,
            'attendance_percentage'=>0,'habit_percentage'=>0
        ];
    ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Total Siswa -->
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl shadow-md p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-white mb-1"><?= $totalSiswa ?? 0 ?></h3>
            <p class="text-white/80 text-sm">Total Siswa</p>
        </div>
        
        <!-- Laki-laki -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-md p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-white mb-1"><?= $siswaLaki ?? 0 ?></h3>
            <p class="text-white/80 text-sm">Siswa Laki-laki</p>
        </div>
        
        <!-- Perempuan -->
        <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl shadow-md p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422A12.083 12.083 0 0112 21.5a12.083 12.083 0 01-6.16-10.922L12 14z"/></svg>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-white mb-1"><?= $siswaPerempuan ?? 0 ?></h3>
            <p class="text-white/80 text-sm">Siswa Perempuan</p>
        </div>
        
        <!-- Progress Kehadiran -->
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-md p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <h4 class="text-sm font-semibold text-white">Kehadiran Hari Ini</h4>
            </div>
            <div class="mt-2">
                <div class="flex items-baseline gap-1 mb-2">
                    <span class="text-3xl font-bold text-white"><?= $dp['attendance_present'] ?></span>
                    <span class="text-base text-white/70">/ <?= $dp['total_students'] ?></span>
                </div>
                <?php $attendWidth = min(100, (int)$dp['attendance_percentage']); ?>
                <div class="w-full bg-white/30 rounded-full h-2 mb-2">
                    <div class="bg-white h-2 rounded-full transition-all duration-300" style="width: <?= $attendWidth ?>%"></div>
                </div>
                <p class="text-xs text-white/80"><?= $dp['attendance_percentage'] ?>% hadir</p>
            </div>
        </div>
        
        <!-- Progress 7 Kebiasaan -->
        <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl shadow-md p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <h4 class="text-sm font-semibold text-white">7 Kebiasaan Hari Ini</h4>
            </div>
            <div class="mt-2">
                <div class="flex items-baseline gap-1 mb-2">
                    <span class="text-3xl font-bold text-white"><?= $dp['habit_logged'] ?></span>
                    <span class="text-base text-white/70">/ <?= $dp['total_students'] ?></span>
                </div>
                <?php $habitWidth = min(100, (int)$dp['habit_percentage']); ?>
                <div class="w-full bg-white/30 rounded-full h-2 mb-2">
                    <div class="bg-white h-2 rounded-full transition-all duration-300" style="width: <?= $habitWidth ?>%"></div>
                </div>
                <p class="text-xs text-white/80"><?= $dp['habit_percentage'] ?>% sudah input</p>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Charts Section - Enhanced Mobile -->
<div class="px-4 sm:px-6 lg:px-8 mb-8 sm:mb-10 lg:mb-12">
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 sm:gap-8">
        <!-- Attendance Chart -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 sm:mb-8 gap-4">
                <div>
                    <h3 class="text-2xl sm:text-3xl font-bold text-gray-900">Kehadiran Mingguan</h3>
                    <p class="text-gray-500 text-base sm:text-lg mt-2">
                    <?php if (isset($attendanceData['period']) && isset($attendanceData['period']['week_start'])): ?>
                        <?= date('d M Y', strtotime($attendanceData['period']['week_start'])) ?> - <?= date('d M Y', strtotime($attendanceData['period']['week_end'])) ?>
                    <?php elseif (isset($attendanceData['period']) && isset($attendanceData['period']['start_date'])): ?>
                        <?= date('d M Y', strtotime($attendanceData['period']['start_date'])) ?> - <?= date('d M Y', strtotime($attendanceData['period']['end_date'])) ?>
                    <?php else: ?>
                        Minggu ini
                    <?php endif; ?>
                </p>
            </div>
                <div class="flex space-x-4 sm:space-x-6">
                    <button class="text-blue-600 text-base sm:text-lg font-semibold bg-blue-50 px-4 py-2 rounded-lg">Minggu</button>
                    <button class="text-gray-500 text-base sm:text-lg font-medium hover:text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50">Bulan</button>
                </div>
            </div>
            
            <!-- Real Chart with Data -->
            <div class="h-64 sm:h-80 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-2xl flex items-end justify-center space-x-3 sm:space-x-4 p-6 sm:p-8" id="weeklyChart">
            <?php if (isset($attendanceData['weekly']) && !empty($attendanceData['weekly'])): ?>
                <?php foreach ($attendanceData['weekly'] as $day): ?>
                    <div class="flex flex-col items-center space-y-2">
                        <div class="bg-blue-500 rounded-t-md transition-all duration-300 hover:bg-blue-600" 
                             style="width: 32px; height: <?= max(8, ($day['percentage'] * 2)) ?>px;"
                             title="<?= $day['day'] ?>: <?= $day['hadir'] ?>/<?= $day['total'] ?> (<?= $day['percentage'] ?>%)">
                        </div>
                        <span class="text-xs text-gray-600 font-medium"><?= $day['day'] ?></span>
                        <span class="text-xs text-gray-500"><?= $day['percentage'] ?>%</span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback chart -->
                <div class="bg-blue-500 w-8 h-32 rounded-t-md"></div>
                <div class="bg-blue-400 w-8 h-24 rounded-t-md"></div>
                <div class="bg-blue-500 w-8 h-40 rounded-t-md"></div>
                <div class="bg-blue-300 w-8 h-20 rounded-t-md"></div>
                <div class="bg-blue-500 w-8 h-36 rounded-t-md"></div>
                <div class="bg-blue-400 w-8 h-28 rounded-t-md"></div>
                <div class="bg-blue-500 w-8 h-44 rounded-t-md"></div>
            <?php endif; ?>
        </div>
        
        <!-- Chart Legend -->
        <div class="mt-4 flex items-center justify-center space-x-6">
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                <span class="text-sm text-gray-600">Kehadiran</span>
            </div>
            <?php if (isset($attendanceData['monthly'])): ?>
                <div class="text-sm text-gray-500">
                    Total bulan ini: <?= $attendanceData['monthly']['total_hadir'] ?>/<?= $attendanceData['monthly']['total_records'] ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Weekly Performance -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 md:p-6">
        <div class="flex items-center justify-between mb-4 md:mb-6">
            <div>
                <h3 class="text-base md:text-lg font-semibold text-gray-900">Detail Kehadiran Minggu Ini</h3>
                <p class="text-gray-500 text-xs md:text-sm">Persentase per hari</p>
            </div>
        </div>
        
        <div class="space-y-4" id="weeklyDetails">
            <?php if (isset($attendanceData['weekly']) && !empty($attendanceData['weekly'])): ?>
                <?php foreach ($attendanceData['weekly'] as $day): ?>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 w-8"><?= $day['day'] ?></span>
                        <div class="flex-1 mx-4">
                            <div class="bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                                     style="width: <?= $day['percentage'] ?>%"></div>
                            </div>
                        </div>
                        <span class="text-sm font-medium text-gray-900 w-12 text-right"><?= $day['hadir'] ?>/<?= $day['total'] ?></span>
                        <span class="text-sm text-gray-500 w-12 text-right"><?= $day['percentage'] ?>%</span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback data -->
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 w-8">M</span>
                    <div class="flex-1 mx-4">
                        <div class="bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 85%"></div>
                        </div>
                    </div>
                    <span class="text-sm font-medium text-gray-900 w-8 text-right">85</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 w-8">T</span>
                    <div class="flex-1 mx-4">
                        <div class="bg-gray-200 rounded-full h-2">
                            <div class="bg-cyan-500 h-2 rounded-full" style="width: 70%"></div>
                        </div>
                    </div>
                    <span class="text-sm font-medium text-gray-900 w-8 text-right">70</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 w-8">W</span>
                    <div class="flex-1 mx-4">
                        <div class="bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 90%"></div>
                        </div>
                    </div>
                    <span class="text-sm font-medium text-gray-900 w-8 text-right">90</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 w-8">T</span>
                    <div class="flex-1 mx-4">
                        <div class="bg-gray-200 rounded-full h-2">
                            <div class="bg-cyan-500 h-2 rounded-full" style="width: 65%"></div>
                        </div>
                    </div>
                    <span class="text-sm font-medium text-gray-900 w-8 text-right">65</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 w-8">F</span>
                    <div class="flex-1 mx-4">
                        <div class="bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 80%"></div>
                        </div>
                    </div>
                    <span class="text-sm font-medium text-gray-900 w-8 text-right">80</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 w-8">S</span>
                    <div class="flex-1 mx-4">
                        <div class="bg-gray-200 rounded-full h-2">
                            <div class="bg-cyan-500 h-2 rounded-full" style="width: 95%"></div>
                        </div>
                    </div>
                    <span class="text-sm font-medium text-gray-900 w-8 text-right">95</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 w-8">S</span>
                    <div class="flex-1 mx-4">
                        <div class="bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 88%"></div>
                        </div>
                    </div>
                    <span class="text-sm font-medium text-gray-900 w-8 text-right">88</span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Summary -->
        <?php if (isset($attendanceData['monthly'])): ?>
        <div class="mt-4 md:mt-6 p-3 md:p-4 bg-gray-50 rounded-lg">
            <h4 class="text-xs md:text-sm font-medium text-gray-900 mb-3">Ringkasan Bulan Ini</h4>
            <div class="grid grid-cols-2 gap-3 md:gap-4">
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    <span class="text-sm text-gray-600">Hadir: <?= $attendanceData['monthly']['total_hadir'] ?></span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                    <span class="text-sm text-gray-600">Sakit: <?= $attendanceData['monthly']['total_sakit'] ?></span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                    <span class="text-sm text-gray-600">Izin: <?= $attendanceData['monthly']['total_izin'] ?></span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                    <span class="text-sm text-gray-600">Alpha: <?= $attendanceData['monthly']['total_alpha'] ?></span>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Weekly Absence Breakdown (Mon-Fri) -->
    <?php if (isset($isWalikelas) && $isWalikelas && isset($attendanceData['weekly_absence'])): ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 md:p-6 mt-6">
        <div class="mb-4 md:mb-6">
            <h3 class="text-base md:text-lg font-semibold text-gray-900">Rekap Ketidakhadiran (Senin - Jum'at)</h3>
            <p class="text-gray-500 text-xs md:text-sm">Menampilkan daftar siswa yang tidak hadir minggu berjalan</p>
        </div>
        <div class="space-y-3">
            <?php if (!empty($attendanceData['weekly_absence'])): ?>
                <?php foreach ($attendanceData['weekly_absence'] as $idx => $wd): ?>
                    <?php 
                        $totalAbsent = count($wd['alpa']) + count($wd['sakit']) + count($wd['izin']);
                        $hasData = $totalAbsent > 0;
                    ?>
                    <div class="border rounded-lg overflow-hidden <?= $hasData ? 'border-gray-300' : 'border-gray-200' ?>">
                        <!-- Header - Clickable -->
                        <button 
                            type="button"
                            onclick="toggleDay(<?= $idx ?>)"
                            class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 transition-colors text-left"
                        >
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-white border border-gray-200">
                                    <span class="text-sm font-bold text-gray-700"><?= date('d', strtotime($wd['date'])) ?></span>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 text-sm md:text-base">
                                        <?= esc($wd['day']) ?>
                                    </h4>
                                    <p class="text-xs text-gray-500"><?= date('d M Y', strtotime($wd['date'])) ?></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <?php if ($hasData): ?>
                                    <div class="flex items-center gap-2 text-xs">
                                        <?php if (count($wd['alpa']) > 0): ?>
                                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full font-medium">
                                                Alpa: <?= count($wd['alpa']) ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (count($wd['sakit']) > 0): ?>
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full font-medium">
                                                Sakit: <?= count($wd['sakit']) ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (count($wd['izin']) > 0): ?>
                                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full font-medium">
                                                Izin: <?= count($wd['izin']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-xs text-green-600 font-medium">✓ Semua hadir</span>
                                <?php endif; ?>
                                <svg 
                                    id="icon-<?= $idx ?>" 
                                    class="w-5 h-5 text-gray-400 transition-transform duration-200" 
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </button>
                        
                        <!-- Content - Expandable -->
                        <div id="day-<?= $idx ?>" class="hidden border-t border-gray-200">
                            <?php if ($hasData): ?>
                                <div class="p-4 bg-white">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <!-- Alpa -->
                                        <div>
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                                <span class="text-sm font-semibold text-red-700">Alpa</span>
                                                <span class="text-xs text-gray-500">(<?= count($wd['alpa']) ?>)</span>
                                            </div>
                                            <?php if (!empty($wd['alpa'])): ?>
                                                <ul class="space-y-1">
                                                    <?php foreach ($wd['alpa'] as $n): ?>
                                                        <li class="text-sm text-gray-700 pl-4 relative before:content-['•'] before:absolute before:left-0 before:text-red-500">
                                                            <?= esc($n) ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else: ?>
                                                <p class="text-sm text-gray-400 italic pl-4">Tidak ada</p>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Sakit -->
                                        <div>
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                                                <span class="text-sm font-semibold text-yellow-700">Sakit</span>
                                                <span class="text-xs text-gray-500">(<?= count($wd['sakit']) ?>)</span>
                                            </div>
                                            <?php if (!empty($wd['sakit'])): ?>
                                                <ul class="space-y-1">
                                                    <?php foreach ($wd['sakit'] as $n): ?>
                                                        <li class="text-sm text-gray-700 pl-4 relative before:content-['•'] before:absolute before:left-0 before:text-yellow-500">
                                                            <?= esc($n) ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else: ?>
                                                <p class="text-sm text-gray-400 italic pl-4">Tidak ada</p>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Izin -->
                                        <div>
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                                <span class="text-sm font-semibold text-blue-700">Izin</span>
                                                <span class="text-xs text-gray-500">(<?= count($wd['izin']) ?>)</span>
                                            </div>
                                            <?php if (!empty($wd['izin'])): ?>
                                                <ul class="space-y-1">
                                                    <?php foreach ($wd['izin'] as $n): ?>
                                                        <li class="text-sm text-gray-700 pl-4 relative before:content-['•'] before:absolute before:left-0 before:text-blue-500">
                                                            <?= esc($n) ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else: ?>
                                                <p class="text-sm text-gray-400 italic pl-4">Tidak ada</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="p-4 bg-green-50">
                                    <p class="text-sm text-green-700 text-center">
                                        <span class="font-medium">✓</span> Semua siswa hadir pada hari ini
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-8 text-gray-500 text-sm">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p>Belum ada data ketidakhadiran minggu ini.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Recent activity sections removed -->

<!-- JavaScript for Dashboard Functionality -->
<script>
// Toggle day details
function toggleDay(index) {
    const content = document.getElementById('day-' + index);
    const icon = document.getElementById('icon-' + index);
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        content.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}
</script>
<?= $this->endSection() ?>

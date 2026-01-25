<!-- Statistics Component -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6 fade-in-up" style="animation-delay: 0.4s;">
    
    <!-- Total Students -->
    <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="flex items-center">
            <div class="p-3 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full shadow-lg">
                <i class="fas fa-users text-white text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600 font-medium">Total Siswa</p>
                <p class="text-3xl font-bold text-gray-800 leading-none"><?= $stats['total_students'] ?></p>
                <p class="text-xs text-blue-600 mt-1">
                    <i class="fas fa-graduation-cap mr-1"></i>Peserta Didik
                </p>
            </div>
        </div>
    </div>
    
    <!-- Active Days -->
    <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="flex items-center">
            <div class="p-3 bg-gradient-to-br from-green-400 to-green-600 rounded-full shadow-lg">
                <i class="fas fa-calendar-check text-white text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600 font-medium">Hari Aktif</p>
                <p class="text-3xl font-bold text-gray-800 leading-none"><?= $stats['total_days'] ?></p>
                <p class="text-xs text-green-600 mt-1">
                    <i class="fas fa-clock mr-1"></i>Hari Efektif
                </p>
            </div>
        </div>
    </div>
    
    <!-- Average Attendance -->
    <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="flex items-center">
            <div class="p-3 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full shadow-lg">
                <i class="fas fa-percentage text-white text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600 font-medium">Rata-rata Kehadiran</p>
                <p class="text-3xl font-bold text-gray-800 leading-none">
                    <?= number_format($stats['average_percentage'], 1) ?>%
                </p>
                <div class="flex items-center mt-1">
                    <?php 
                    $avgPercentage = $stats['average_percentage'];
                    $statusColor = $avgPercentage >= 90 ? 'text-green-600' : ($avgPercentage >= 75 ? 'text-yellow-600' : 'text-red-600');
                    $statusIcon = $avgPercentage >= 90 ? 'fa-thumbs-up' : ($avgPercentage >= 75 ? 'fa-exclamation-triangle' : 'fa-times-circle');
                    ?>
                    <i class="fas <?= $statusIcon ?> <?= $statusColor ?> mr-1 text-xs"></i>
                    <p class="text-xs <?= $statusColor ?>">
                        <?= $avgPercentage >= 90 ? 'Sangat Baik' : ($avgPercentage >= 75 ? 'Perlu Perhatian' : 'Butuh Tindakan') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Total Alpha -->
    <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="flex items-center">
            <div class="p-3 bg-gradient-to-br from-red-400 to-red-600 rounded-full shadow-lg">
                <i class="fas fa-exclamation-triangle text-white text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600 font-medium">Total Alpha</p>
                <p class="text-3xl font-bold text-gray-800 leading-none"><?= $stats['total_alpha'] ?></p>
                <div class="flex items-center mt-1">
                    <?php 
                    $alphaRate = $stats['total_alpha'] > 0 ? $stats['percent_alpha'] : 0;
                    $alertClass = $alphaRate > 10 ? 'text-red-600' : ($alphaRate > 5 ? 'text-yellow-600' : 'text-green-600');
                    ?>
                    <i class="fas fa-chart-line <?= $alertClass ?> mr-1 text-xs"></i>
                    <p class="text-xs <?= $alertClass ?>">
                        <?= number_format($alphaRate, 1) ?>% dari total
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Summary Bar -->
<div class="mb-6 fade-in-up" style="animation-delay: 0.6s;">
    <div class="bg-white rounded-xl p-4 shadow-lg border border-gray-200">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-chart-bar mr-2 text-blue-500"></i>
                Ringkasan Kehadiran
            </h3>
            
            <!-- Progress Bars -->
            <div class="flex-1 max-w-2xl">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                    <!-- Hadir Progress -->
                    <div class="text-center">
                        <p class="text-green-600 font-medium mb-1">Hadir</p>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full transition-all duration-1000" 
                                 style="width: <?= $stats['percent_hadir'] ?>%"></div>
                        </div>
                        <p class="text-xs text-gray-600 mt-1"><?= number_format($stats['percent_hadir'], 1) ?>%</p>
                    </div>
                    
                    <!-- Sakit Progress -->
                    <div class="text-center">
                        <p class="text-yellow-600 font-medium mb-1">Sakit</p>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-500 h-2 rounded-full transition-all duration-1000" 
                                 style="width: <?= $stats['percent_sakit'] ?>%"></div>
                        </div>
                        <p class="text-xs text-gray-600 mt-1"><?= number_format($stats['percent_sakit'], 1) ?>%</p>
                    </div>
                    
                    <!-- Izin Progress -->
                    <div class="text-center">
                        <p class="text-blue-600 font-medium mb-1">Izin</p>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full transition-all duration-1000" 
                                 style="width: <?= $stats['percent_izin'] ?>%"></div>
                        </div>
                        <p class="text-xs text-gray-600 mt-1"><?= number_format($stats['percent_izin'], 1) ?>%</p>
                    </div>
                    
                    <!-- Alpha Progress -->
                    <div class="text-center">
                        <p class="text-red-600 font-medium mb-1">Alpha</p>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-red-500 h-2 rounded-full transition-all duration-1000" 
                                 style="width: <?= $stats['percent_alpha'] ?>%"></div>
                        </div>
                        <p class="text-xs text-gray-600 mt-1"><?= number_format($stats['percent_alpha'], 1) ?>%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

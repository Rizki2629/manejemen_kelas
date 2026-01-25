<!-- Professional Document Header Component -->
<div class="mb-6 fade-in-up" style="animation-delay: 0.8s;">
    <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200">
        <div class="text-center space-y-3">
            <!-- Main Title -->
            <h2 class="text-2xl lg:text-3xl font-bold text-gray-800 tracking-wide">
                DAFTAR HADIR PESERTA DIDIK
            </h2>
            
            <!-- School Name -->
            <h3 class="text-xl lg:text-2xl font-semibold text-blue-600">
                SDN GROGOL UTARA 09
            </h3>
            
            <!-- Class Information -->
            <h4 class="text-lg lg:text-xl font-medium text-gray-700">
                KELAS <?= strtoupper($attendanceData['kelas'] ?? '') ?>
            </h4>
            
            <!-- Academic Year -->
            <p class="text-sm text-gray-500">
                TAHUN PELAJARAN <?= $attendanceData['year'] ?>/<?= ($attendanceData['year'] + 1) ?>
            </p>
            
            <!-- Month Badge -->
            <div class="inline-block mt-4 px-6 py-3 bg-gradient-to-r from-blue-100 to-purple-100 rounded-full border border-blue-200 shadow-sm">
                <span class="text-lg font-bold text-blue-700 flex items-center">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    BULAN <?= strtoupper(date('F Y', mktime(0, 0, 0, (int)$attendanceData['month'], 1, (int)$attendanceData['year']))) ?>
                </span>
            </div>
            
            <!-- Additional Info -->
            <div class="flex flex-wrap justify-center gap-4 mt-4 text-sm text-gray-600">
                <div class="flex items-center">
                    <i class="fas fa-users mr-1 text-blue-500"></i>
                    <span><?= count($attendanceData['students']) ?> Siswa</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-calendar-day mr-1 text-green-500"></i>
                    <span><?= count($attendanceData['days']) ?> Hari Efektif</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-clock mr-1 text-orange-500"></i>
                    <span>Diperbarui: <?= date('d/m/Y H:i') ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

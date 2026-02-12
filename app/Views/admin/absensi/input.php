<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<?php
$isOffDay = $isOffDay ?? false;
$offDayInfo = $offDayInfo ?? null;
$offDayStatus = is_array($offDayInfo) ? ($offDayInfo['status'] ?? null) : null;
$offDayKet = is_array($offDayInfo) ? ($offDayInfo['keterangan'] ?? '') : '';
?>

<script>
    window.ABSENSI_IS_OFF_DAY = <?= $isOffDay ? 'true' : 'false' ?>;
    window.ABSENSI_OFF_DAY_INFO = <?= json_encode($offDayInfo, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
</script>

<!-- Simple and Clean Attendance Input -->
<div class="container-fluid py-4" style="padding-top: 1rem;">
    <div class="row">
        <div class="col-12">
            <!-- Header Card -->
            <div class="bg-white rounded-xl shadow-lg mb-6 border-0 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-4 md:p-6">
                    <!-- Mobile Layout -->
                    <div class="block md:hidden">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center flex-1 min-w-0">
                                <i class="fas fa-clipboard-list mr-2 text-lg flex-shrink-0"></i>
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-lg font-bold truncate">Input Absensi</h4>
                                </div>
                            </div>
                            <button type="button" class="px-3 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-lg shadow-md transition-all duration-200 ml-2 flex-shrink-0 <?= $isOffDay ? 'opacity-60 cursor-not-allowed' : '' ?>" id="markAllPresentMobile" <?= $isOffDay ? 'disabled' : '' ?>>
                                <i class="fas fa-check-circle mr-2"></i>
                                <span>Hadir Semua</span>
                            </button>
                        </div>
                        <div class="flex items-center justify-between">
                            <small class="text-white/90 flex items-center flex-1 min-w-0">
                                <i class="fas fa-calendar-alt mr-2 flex-shrink-0"></i>
                                <span class="truncate">
                                    <?php
                                    $dayNames = [
                                        'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
                                        'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
                                    ];
                                    $englishDay = date('l', strtotime($selectedDate ?? date('Y-m-d')));
                                    $indonesianDay = $dayNames[$englishDay] ?? $englishDay;
                                    $formattedDate = date('d M Y', strtotime($selectedDate ?? date('Y-m-d')));
                                    echo $indonesianDay . ', ' . $formattedDate;
                                    ?>
                                </span>
                            </small>
                            <?php if ($selectedKelas): ?>
                                <span class="ml-2 px-2 py-1 bg-white/20 text-white text-xs font-medium rounded-full flex-shrink-0">
                                    <?= $selectedKelas ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Desktop Layout -->
                    <div class="hidden md:flex justify-between items-center">
                        <div>
                            <h4 class="text-xl font-bold mb-1 flex items-center">
                                <i class="fas fa-clipboard-list mr-3"></i>
                                Input Absensi
                                <?php if ($selectedKelas): ?>
                                    <span class="ml-3 px-3 py-1 bg-white/20 text-white text-sm font-medium rounded-full"><?= $selectedKelas ?></span>
                                <?php endif; ?>
                            </h4>
                            <small class="text-white/90 flex items-center mt-2">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                <?php
                                $dayNames = [
                                    'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
                                    'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
                                ];
                                $englishDay = date('l', strtotime($selectedDate ?? date('Y-m-d')));
                                $indonesianDay = $dayNames[$englishDay] ?? $englishDay;
                                $formattedDate = date('d M Y', strtotime($selectedDate ?? date('Y-m-d')));
                                echo $indonesianDay . ', ' . $formattedDate;
                                ?>
                            </small>
                        </div>
                        <div class="flex space-x-3">
                            <button type="button" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-lg shadow-md transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5 <?= $isOffDay ? 'opacity-60 cursor-not-allowed' : '' ?>" id="markAllPresent" <?= $isOffDay ? 'disabled' : '' ?>>
                                <i class="fas fa-check-circle mr-2"></i>
                                <span>Hadir Semua</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($isOffDay): ?>
            <div class="mb-6">
                <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-xl p-4 shadow-sm">
                    <div class="flex items-start">
                        <i class="fas fa-ban mt-0.5 mr-3"></i>
                        <div class="flex-1">
                            <div class="font-bold">Input absensi dinonaktifkan</div>
                            <div class="text-sm">Tanggal ini termasuk hari libur/off<?= $offDayKet ? ': ' . esc($offDayKet) : '' ?>.</div>
                        </div>
                        <?php if ($offDayStatus): ?>
                            <span class="ml-3 px-3 py-1 rounded-full text-xs font-semibold bg-amber-200 text-amber-900"><?= esc($offDayStatus) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Filter Card -->
            <div class="bg-white rounded-xl shadow-lg mb-6 border-0 overflow-hidden">
                <div class="p-6 pb-2">
                    <div class="flex justify-between items-center mb-4">
                        <h5 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-filter text-blue-600 mr-3"></i>Filter & Pencarian
                        </h5>
                        <button type="button" class="px-3 py-1 text-sm border border-gray-300 hover:border-gray-400 text-gray-600 hover:text-gray-700 rounded-lg transition-colors duration-200" onclick="clearFilters()">
                            <i class="fas fa-times mr-1"></i> Clear
                        </button>
                    </div>
                    
                    <form id="filterForm" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Date Selection -->
                        <div>
                            <label for="tanggal" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-calendar text-blue-600 mr-2"></i>Tanggal
                            </label>
                            <div class="flex rounded-lg shadow-sm border border-blue-300 overflow-hidden">
                                <button type="button" class="px-3 py-2 bg-blue-50 hover:bg-blue-100 border-r border-blue-300 text-blue-600 transition-colors duration-200" id="prevDay" title="Hari Sebelumnya">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <input type="date" class="flex-1 px-3 py-2 border-0 focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="tanggal" name="tanggal" 
                                       value="<?= $selectedDate ?>" required>
                                <button type="button" class="px-3 py-2 bg-blue-50 hover:bg-blue-100 border-l border-blue-300 text-blue-600 transition-colors duration-200" id="nextDay" title="Hari Berikutnya">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Class Selection -->
                        <?php if ($userRole === 'admin'): ?>
                        <div>
                            <label for="kelas" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-school text-blue-600 mr-2"></i>Kelas
                            </label>
                            <select class="w-full px-3 py-2 border border-blue-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="kelas" name="kelas" required>
                                <option value="">Pilih Kelas</option>
                                <?php foreach ($allKelas as $kelas): ?>
                                <option value="<?= $kelas['kelas'] ?>" <?= $selectedKelas === $kelas['kelas'] ? 'selected' : '' ?>>
                                    <?= $kelas['kelas'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php else: ?>
                        <input type="hidden" name="kelas" value="<?= $userKelas ?>">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-school text-blue-600 mr-2"></i>Kelas
                            </label>
                            <div class="px-3 py-2 bg-blue-50 border border-blue-300 rounded-lg font-bold text-blue-700"><?= $userKelas ?></div>
                        </div>
                        <?php endif; ?>

                        <!-- Student Search -->
                        <div>
                            <label for="studentSearch" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-search text-blue-600 mr-2"></i>Cari Siswa
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-blue-400"></i>
                                </div>
                                <input type="text" class="w-full pl-10 pr-3 py-2 border border-blue-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="studentSearch" 
                                       placeholder="Nama atau NISN...">
                            </div>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label for="filterStatus" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-funnel-dollar text-blue-600 mr-2"></i>Filter Status
                            </label>
                            <select class="w-full px-3 py-2 border border-blue-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="filterStatus">
                                <option value="all">🔍 Semua Status</option>
                                <option value="hadir">✅ Hadir</option>
                                <option value="sakit">🤒 Sakit</option>
                                <option value="izin">✋ Izin</option>
                                <option value="alpha">❌ Alpha</option>
                                <option value="belum">⏳ Belum Diisi</option>
                            </select>
                        </div>
                    </form>
                    
                    <!-- Search Results Info -->
                    <div id="searchResults" class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg hidden">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        <span id="searchResultsText"></span>
                    </div>
                </div>
            </div>

            <!-- Students Grid -->
            <?php if ($selectedKelas && !empty($students)): ?>
            <div class="bg-white rounded-xl shadow-lg border-0 overflow-hidden absensi-page-last">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h5 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-users text-blue-600 mr-3"></i>
                            Daftar Siswa 
                            <span class="ml-3 px-3 py-1 bg-blue-600 text-white text-sm font-medium rounded-full"><?= count($students) ?> siswa</span>
                        </h5>
                        <div class="flex items-center">
                            <span class="text-gray-500 text-sm flex items-center">
                                <i class="fas fa-info-circle mr-2"></i>
                                Klik status untuk mengisi absensi
                            </span>
                        </div>
                    </div>
                </div>
                <div class="p-6 pb-0">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="studentsGrid">
                        <?php foreach ($students as $student): ?>
                        <div class="student-card transition-all duration-300 hover:transform hover:-translate-y-1" 
                             data-siswa-id="<?= $student['siswa_id'] ?>"
                             data-student-name="<?= strtolower($student['nama']) ?>"
                             data-student-nisn="<?= strtolower($student['nisn']) ?>">
                            <div class="bg-white border border-gray-200 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 student-item h-full">
                                <div class="p-4">
                                    <!-- Student Info -->
                                    <div class="flex items-center mb-4 md:flex-row flex-col md:text-left text-center">
                                        <div class="bg-gradient-to-br from-blue-500 to-purple-600 text-white rounded-full flex items-center justify-center font-black text-base shadow-md avatar-circle md:mr-3 mb-2 md:mb-0">
                                            <?= strtoupper(substr($student['nama'], 0, 2)) ?>
                                        </div>
                                        <div class="flex-1 min-w-0 md:pr-2" style="display: block !important;">
                                            <h6 class="font-bold text-gray-800 student-name text-sm leading-tight" style="display: block !important; visibility: visible !important; color: #1f2937 !important; font-size: 14px !important; font-weight: 700 !important; line-height: 1.2 !important;"><?= $student['nama'] ?></h6>
                                            <small class="text-gray-500 flex items-center text-xs md:justify-start justify-center" style="display: flex !important; visibility: visible !important; color: #6b7280 !important; font-size: 12px !important;">
                                                <i class="fas fa-id-card mr-1"></i>
                                                <?= $student['nisn'] ?>
                                            </small>
                                        </div>
                                        <div class="attendance-status-indicator md:ml-2 md:block hidden">
                                            <i class="fas fa-circle text-gray-400 text-xs" title="Belum diisi"></i>
                                        </div>
                                    </div>

                                    <!-- Attendance Buttons -->
                                    <div class="grid grid-cols-4 gap-1 mb-4 attendance-buttons" role="group">
                                        <input type="radio" class="sr-only" name="status_<?= $student['siswa_id'] ?>" 
                                               id="hadir_<?= $student['siswa_id'] ?>" value="hadir"
                                             <?= $isOffDay ? 'disabled' : '' ?>
                                               <?= isset($student['status']) && $student['status'] === 'hadir' ? 'checked' : '' ?>>
                                         <label class="flex flex-col items-center py-2 px-1 border-2 border-green-300 text-green-600 hover:bg-green-50 rounded-lg cursor-pointer transition-all duration-200 hover:border-green-400 text-xs font-semibold attendance-btn<?= $isOffDay ? ' opacity-50 cursor-not-allowed pointer-events-none' : '' ?>" for="hadir_<?= $student['siswa_id'] ?>" 
                                               title="Tandai Hadir">
                                            <i class="fas fa-check mb-1"></i> 
                                            <span class="hidden sm:block">Hadir</span>
                                        </label>

                                        <input type="radio" class="sr-only" name="status_<?= $student['siswa_id'] ?>" 
                                               id="sakit_<?= $student['siswa_id'] ?>" value="sakit"
                                                 <?= $isOffDay ? 'disabled' : '' ?>
                                               <?= isset($student['status']) && $student['status'] === 'sakit' ? 'checked' : '' ?>>
                                             <label class="flex flex-col items-center py-2 px-1 border-2 border-yellow-300 text-yellow-600 hover:bg-yellow-50 rounded-lg cursor-pointer transition-all duration-200 hover:border-yellow-400 text-xs font-semibold attendance-btn<?= $isOffDay ? ' opacity-50 cursor-not-allowed pointer-events-none' : '' ?>" for="sakit_<?= $student['siswa_id'] ?>"
                                               title="Tandai Sakit">
                                            <i class="fas fa-thermometer-half mb-1"></i> 
                                            <span class="hidden sm:block">Sakit</span>
                                        </label>

                                        <input type="radio" class="sr-only" name="status_<?= $student['siswa_id'] ?>" 
                                               id="izin_<?= $student['siswa_id'] ?>" value="izin"
                                                 <?= $isOffDay ? 'disabled' : '' ?>
                                               <?= isset($student['status']) && $student['status'] === 'izin' ? 'checked' : '' ?>>
                                             <label class="flex flex-col items-center py-2 px-1 border-2 border-blue-300 text-blue-600 hover:bg-blue-50 rounded-lg cursor-pointer transition-all duration-200 hover:border-blue-400 text-xs font-semibold attendance-btn<?= $isOffDay ? ' opacity-50 cursor-not-allowed pointer-events-none' : '' ?>" for="izin_<?= $student['siswa_id'] ?>"
                                               title="Tandai Izin">
                                            <i class="fas fa-hand-paper mb-1"></i> 
                                            <span class="hidden sm:block">Izin</span>
                                        </label>

                                        <input type="radio" class="sr-only" name="status_<?= $student['siswa_id'] ?>" 
                                               id="alpha_<?= $student['siswa_id'] ?>" value="alpha"
                                                 <?= $isOffDay ? 'disabled' : '' ?>
                                               <?= isset($student['status']) && $student['status'] === 'alpha' ? 'checked' : '' ?>>
                                             <label class="flex flex-col items-center py-2 px-1 border-2 border-red-300 text-red-600 hover:bg-red-50 rounded-lg cursor-pointer transition-all duration-200 hover:border-red-400 text-xs font-semibold attendance-btn<?= $isOffDay ? ' opacity-50 cursor-not-allowed pointer-events-none' : '' ?>" for="alpha_<?= $student['siswa_id'] ?>"
                                               title="Tandai Alpha">
                                            <i class="fas fa-times mb-1"></i> 
                                            <span class="hidden sm:block">Alpha</span>
                                        </label>
                                    </div>

                                    <!-- Notes -->
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                                            <i class="fas fa-sticky-note mr-1"></i>Keterangan:
                                        </label>
                                        <textarea class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 student-keterangan" 
                                                  rows="2" placeholder="Keterangan opsional..." 
                                                  <?= $isOffDay ? 'disabled' : '' ?>
                                                  data-siswa-id="<?= $student['siswa_id'] ?>"><?= $student['keterangan'] ?? '' ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- Pagination removed: showing all students -->
                </div>
            </div>

            <?php elseif ($selectedKelas): ?>
            <!-- No Students Message -->
            <div class="bg-white rounded-xl shadow-lg">
                <div class="text-center py-12">
                    <i class="fas fa-user-slash text-6xl text-gray-400 mb-4"></i>
                    <h5 class="text-xl font-bold text-gray-700 mb-2">Tidak Ada Siswa</h5>
                    <p class="text-gray-500"><?= $selectedKelas ?> belum memiliki siswa yang terdaftar.</p>
                </div>
            </div>

            <?php else: ?>
            <!-- Initial State -->
            <div class="bg-white rounded-xl shadow-lg">
                <div class="text-center py-12">
                    <i class="fas fa-clipboard-list text-6xl text-gray-400 mb-4"></i>
                    <h5 class="text-xl font-bold text-gray-700 mb-2">Pilih Kelas dan Tanggal</h5>
                    <p class="text-gray-500">Silakan pilih kelas dan tanggal untuk memulai input absensi.</p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Floating Save Button - SINGLE INSTANCE ONLY -->
            <?php if ($selectedKelas && !empty($students)): ?>
            <button type="button" 
                    class="floating-save-btn <?= $isOffDay ? 'opacity-60 cursor-not-allowed' : '' ?>" 
                    id="saveAll" <?= $isOffDay ? 'disabled' : '' ?>>
                <!-- Icon -->
                <i class="fas fa-paper-plane"></i>
                
                <!-- Unsaved Count Badge -->
                <span class="unsaved-count" id="unsavedCount">0</span>
                
                <!-- Hover Text -->
                <div class="hover-tooltip">
                    <span>Kirim Daftar Hadir</span>
                </div>
            </button>
            <?php else: ?>
            <!-- Debug: Show conditions -->
            <div class="fixed bottom-20 right-6 bg-red-500 text-white p-2 rounded text-xs" style="z-index: 99999;">
                Debug: selectedKelas = "<?= $selectedKelas ?? 'NULL' ?>", 
                students = <?= isset($students) ? count($students) : 0 ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Loading Modal -->
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden" id="loadingModal">
        <div class="bg-white rounded-xl shadow-2xl p-8 max-w-sm mx-4">
            <div class="text-center">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-600 border-t-transparent mb-4"></div>
                <h5 class="text-lg font-bold text-gray-800 mb-2">Memproses Data...</h5>
                <p class="text-gray-500">Mohon tunggu sebentar</p>
            </div>
        </div>
    </div>
</div>

<style>
/* ================================
   FLOATING SAVE BUTTON - CLEAN VERSION
   ================================ */

/* Ensure only ONE floating button exists */
#saveAll:not(:first-of-type) {
    display: none !important;
}

.floating-save-btn {
    position: fixed !important;
    bottom: 24px !important;
    right: 24px !important;
    z-index: 999999 !important;
    
    /* Size and Shape */
    width: 64px !important;
    height: 64px !important;
    border-radius: 50% !important;
    
    /* Styling */
    background: linear-gradient(135deg, #3b82f6 0%, #9333ea 100%) !important;
    color: white !important;
    border: 2px solid rgba(255, 255, 255, 0.2) !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3) !important;
    backdrop-filter: blur(10px);
    
    /* Layout */
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    
    /* Interaction */
    cursor: pointer !important;
    pointer-events: auto !important;
    
    /* Animation */
    transition: all 0.3s ease !important;
    animation: float 3s ease-in-out infinite;
}

.floating-save-btn:disabled {
    opacity: 0.55 !important;
    cursor: not-allowed !important;
    pointer-events: none !important;
}

.floating-save-btn:hover {
    transform: scale(1.1) !important;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4) !important;
}

.floating-save-btn:active {
    transform: scale(0.95) !important;
}

/* Icon styling */
.floating-save-btn i {
    font-size: 1.25rem !important;
    color: white !important;
    pointer-events: none !important;
    transition: transform 0.3s ease;
}

.floating-save-btn:hover i {
    transform: rotate(12deg);
}

/* Badge styling */
.floating-save-btn .unsaved-count {
    position: absolute;
    top: -6px;
    right: -6px;
    background: #ef4444;
    color: white;
    font-size: 11px;
    font-weight: bold;
    border-radius: 50%;
    min-width: 18px;
    height: 18px;
    display: none;
    align-items: center;
    justify-content: center;
    animation: pulse 2s infinite;
    border: 2px solid white;
    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
    z-index: 1000000;
    /* Prevent badge from being affected by button transforms */
    transform: none !important;
    transition: none !important;
}

.floating-save-btn .unsaved-count:not(.hidden) {
    display: flex !important;
}

/* Tooltip styling */
.floating-save-btn .hover-tooltip {
    position: absolute;
    right: 100%;
    top: 50%;
    transform: translateY(-50%) translateX(20px);
    margin-right: 16px;
    opacity: 0;
    pointer-events: none;
    transition: all 0.3s ease;
    background: #1f2937;
    color: white;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    white-space: nowrap;
}

.floating-save-btn:hover .hover-tooltip {
    opacity: 1;
    transform: translateY(-50%) translateX(0);
}

/* Float animation */
@keyframes float {
    0%, 100% { 
        transform: translateY(0px); 
    }
    50% { 
        transform: translateY(-8px); 
    }
}

/* Mobile responsive - SINGLE BUTTON ONLY */
@media (max-width: 768px) {
    /* Hide any duplicate buttons */
    .floating-save-btn:not(#saveAll) {
        display: none !important;
    }
    
    .floating-save-btn {
        bottom: 16px !important;
        right: 16px !important;
        width: 56px !important;
        height: 56px !important;
        left: auto !important;
        transform: none !important;
        margin: 0 !important;
        position: fixed !important;
        z-index: 999999 !important;
    }
    
    .floating-save-btn i {
        font-size: 1.1rem !important;
    }
    
    .floating-save-btn .hover-tooltip {
        display: none !important; /* Hide tooltip on mobile */
    }
    
    /* Mobile badge positioning - IMPORTANT FIX */
    .floating-save-btn .unsaved-count {
        top: -4px !important;
        right: -4px !important;
        min-width: 16px !important;
        height: 16px !important;
        font-size: 10px !important;
        font-weight: 800 !important;
        border: 1.5px solid white !important;
        box-shadow: 0 2px 6px rgba(239, 68, 68, 0.5) !important;
        z-index: 1000001 !important;
        line-height: 1 !important;
        padding: 0 !important;
    }
}

/* CRITICAL: Global text visibility overrides */
h6.student-name, .student-name {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    color: #1f2937 !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    line-height: 1.2 !important;
    overflow: visible !important;
    white-space: normal !important;
    text-overflow: unset !important;
}

small.text-gray-500, .text-gray-500 {
    display: flex !important;
    visibility: visible !important;
    opacity: 1 !important;
    color: #6b7280 !important;
    font-size: 12px !important;
}
.student-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.student-card.filtered-out {
    display: none !important;
    opacity: 0 !important;
    transform: scale(0.95) !important;
    pointer-events: none !important;
}

.student-item {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.student-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Ensure student name is always visible */
.student-name {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    color: #1f2937 !important;
    font-size: 0.875rem !important;
    font-weight: 700 !important;
    line-height: 1.25 !important;
    overflow: visible !important;
    white-space: normal !important;
    text-overflow: unset !important;
}

.avatar-circle {
    width: 3rem !important; /* lebar tetap desktop */
    height: 3rem !important; /* tinggi tetap desktop */
    min-width: 3rem !important; /* minimum width untuk mencegah shrinking */
    max-width: 3rem !important; /* maximum width untuk mencegah expanding */
    border-radius: 9999px !important; /* lingkaran penuh */
    aspect-ratio: 1 / 1 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 0.75rem !important;
    font-weight: 700 !important;
    line-height: 1 !important;
    padding: 0 !important;
    overflow: hidden;
    box-sizing: border-box !important;
    text-align: center;
    transition: all 0.3s ease;
    flex-shrink: 0; /* mencegah flex item mengecil */
    flex-grow: 0; /* mencegah flex item membesar */
    font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
    letter-spacing: -0.02em;
    margin-right: 0.75rem !important; /* margin kanan untuk desktop */
    margin-bottom: 0; /* default tidak ada margin bawah */
}

.avatar-circle:hover {
    transform: scale(1.1);
}

/* Radio button states for attendance */
input[type="radio"]:checked + .attendance-btn {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Specific colors for checked states */
input[value="hadir"]:checked + .attendance-btn {
    background: linear-gradient(135deg, #10b981, #059669);
    border-color: transparent;
    color: white;
}

input[value="sakit"]:checked + .attendance-btn {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    border-color: transparent;
    color: white;
}

input[value="izin"]:checked + .attendance-btn {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    border-color: transparent;
    color: white;
}

input[value="alpha"]:checked + .attendance-btn {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    border-color: transparent;
    color: white;
}

/* Attendance status indicator states */
.student-item.has-attendance .attendance-status-indicator .fas.fa-circle {
    color: #10b981 !important;
    transform: scale(1.2);
}

/* Animation for attendance updates */
.student-card.attendance-updated {
    animation: attendanceUpdate 0.6s ease;
}

@keyframes attendanceUpdate {
    0% { transform: scale(1); }
    50% { 
        transform: scale(1.03); 
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
    }
    100% { transform: scale(1); }
}

/* Enhanced animations for mark all present process */
.animate-pulse {
    animation: pulse 1s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: .7;
    }
}

/* Processing animation for cards */
.bg-green-50 {
    background-color: rgb(240 253 244);
    transition: all 0.3s ease;
}

.border-green-200 {
    border-color: rgb(187 247 208);
    transition: all 0.3s ease;
}

.bg-green-100 {
    background-color: rgb(220 252 231);
    transition: all 0.3s ease;
}

.border-green-300 {
    border-color: rgb(134 239 172);
    transition: all 0.3s ease;
}

/* Button loading states */
.opacity-80 {
    opacity: 0.8;
    transition: opacity 0.2s ease;
}

.cursor-not-allowed {
    cursor: not-allowed !important;
}

/* Success animation for button */
.bg-green-600 {
    background-color: rgb(22 163 74) !important;
    transition: background-color 0.3s ease;
}

/* Unsaved count pulse animation */
#unsavedCount.pulse {
    animation: pulse 1s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.05); opacity: 0.8; }
}

/* Loading modal animation */
.loading-modal-enter {
    animation: fadeIn 0.3s ease;
}

.loading-modal-exit {
    animation: fadeOut 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}

@keyframes fadeOut {
    from { opacity: 1; transform: scale(1); }
    to { opacity: 0; transform: scale(0.9); }
}

/* Search results animation */
.search-results-enter {
    animation: slideInDown 0.3s ease;
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Toast notification animations */
.toast-enter {
    animation: slideInRight 0.3s ease;
}

.toast-exit {
    animation: slideOutRight 0.3s ease;
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(100%);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideOutRight {
    from {
        opacity: 1;
        transform: translateX(0);
    }
    to {
        opacity: 0;
        transform: translateX(100%);
    }
}

/* Filter clear animation */
.fade-in-up {
    animation: fadeInUp 0.3s ease forwards;
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

/* Button hover effects */
.btn-hover-lift {
    transition: all 0.2s ease;
}

.btn-hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(0,0,0,0.2);
}

/* Focus states for accessibility */
.focus-ring:focus {
    outline: 2px solid transparent;
    outline-offset: 2px;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);
}

/* Prevent text wrapping in student count and headers */
.flex.justify-between.items-center {
    flex-wrap: nowrap !important;
    align-items: center !important;
}

.flex.justify-between.items-center h5,
.flex.justify-between.items-center h4 {
    flex-wrap: nowrap !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    display: flex !important;
    align-items: center !important;
}

.flex.justify-between.items-center h5 span,
.flex.justify-between.items-center h4 span {
    flex-shrink: 0 !important;
    white-space: nowrap !important;
}

/* Mobile responsive adjustments */
@media (max-width: 768px) {
    /* Container padding adjustment for mobile navbar */
    .container-fluid.py-4 {
        padding-top: 8rem !important;
        padding-bottom: 2rem !important;
    }
    
    /* Hide any duplicate buttons */
    .floating-save-btn:not(#saveAll) {
        display: none !important;
    }
    
    .floating-save-btn {
        bottom: 16px !important;
        right: 16px !important;
        width: 56px !important;
        height: 56px !important;
        left: auto !important;
        transform: none !important;
        margin: 0 !important;
        position: fixed !important;
        z-index: 999999 !important;
    }
    
    .floating-save-btn i {
        font-size: 1.1rem !important;
    }
    
    .floating-save-btn .hover-tooltip {
        display: none !important; /* Hide tooltip on mobile */
    }
    
    /* Mobile badge positioning - CONSISTENT */
    .floating-save-btn .unsaved-count {
        top: -4px !important;
        right: -4px !important;
        min-width: 16px !important;
        height: 16px !important;
        font-size: 10px !important;
        font-weight: 800 !important;
        border: 1.5px solid white !important;
        box-shadow: 0 2px 6px rgba(239, 68, 68, 0.5) !important;
        z-index: 1000001 !important;
        line-height: 1 !important;
        padding: 0 !important;
    }
    
    /* Header button adjustments for mobile */
    #markAllPresent {
        padding: 10px 16px !important;
        font-size: 14px !important;
        white-space: nowrap !important;
        min-width: auto !important;
        width: auto !important;
        flex-shrink: 0 !important;
    }
    
    #markAllPresent span {
        display: inline !important; /* Show full text on mobile */
        margin-left: 6px !important;
    }
    
    #markAllPresent i {
        margin-right: 6px !important;
        font-size: 14px !important;
    }
    
    /* Mobile header button styling */
    #markAllPresentMobile {
        padding: 8px 12px !important;
        font-size: 12px !important;
        white-space: nowrap !important;
        min-width: auto !important;
        width: auto !important;
        flex-shrink: 0 !important;
    }
    
    #markAllPresentMobile span {
        display: inline !important; /* Show full text on mobile */
        margin-left: 4px !important;
        font-size: 12px !important;
    }
    
    #markAllPresentMobile i {
        margin-right: 4px !important;
        font-size: 12px !important;
    }
    
    /* Mobile Header Cleanup - Prevent Overlapping */
    .bg-gradient-to-r {
        padding: 16px !important;
    }
    
    .bg-gradient-to-r .block.md\:hidden .flex.items-center.justify-between.mb-3 {
        margin-bottom: 12px !important;
        align-items: flex-start !important;
        gap: 8px !important;
        flex-wrap: nowrap !important;
    }
    
    .bg-gradient-to-r .flex.items-center.flex-1.min-w-0 {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 4px !important;
        min-width: 0 !important;
        flex-shrink: 1 !important;
    }
    
    .bg-gradient-to-r h4.text-lg {
        font-size: 16px !important;
        line-height: 20px !important;
        margin: 0 !important;
        font-weight: 700 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        max-width: 100% !important;
    }
    
    .bg-gradient-to-r small.text-white\/90 {
        font-size: 12px !important;
        line-height: 16px !important;
        margin: 0 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        max-width: 100% !important;
    }
    
    .bg-gradient-to-r .flex.items-center.justify-between {
        margin-bottom: 0 !important;
        align-items: center !important;
        gap: 8px !important;
    }
    
    .bg-gradient-to-r span.rounded-full {
        font-size: 10px !important;
        padding: 4px 8px !important;
        white-space: nowrap !important;
        flex-shrink: 0 !important;
    }
    
    /* Text wrapping prevention */
    .truncate {
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }
    
    /* Mobile layout adjustments */
    .student-card {
        margin-bottom: 1rem;
    }
    
    /* Ensure student info is visible */
    .student-name {
        font-size: 0.875rem !important;
        line-height: 1.2 !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    .flex-1 {
        flex: 1 1 0% !important;
        min-width: 0 !important;
        display: block !important;
    }
    
    /* Mobile avatar styling - centered, smaller */
    .avatar-circle {
        width: 40px !important; /* diperkecil dari 50px ke 40px */
        height: 40px !important;
        min-width: 40px !important; /* minimum width untuk mencegah shrinking */
        max-width: 40px !important; /* maximum width untuk mencegah expanding */
        font-size: 0.8rem !important; /* font disesuaikan dengan ukuran lebih kecil */
        border-radius: 9999px !important;
        aspect-ratio: 1 / 1 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-weight: 700 !important;
        line-height: 1 !important;
        padding: 0 !important;
        overflow: hidden;
        box-sizing: border-box !important;
        text-align: center;
        flex-shrink: 0; /* mencegah flex item mengecil */
        flex-grow: 0; /* mencegah flex item membesar */
        margin-right: 0 !important; /* hilangkan margin kanan di mobile */
        margin-bottom: 0.5rem !important; /* tambah margin bawah */
        margin-left: auto !important; /* center horizontal */
        margin-top: 0 !important;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.12) !important; /* shadow disesuaikan */
    }
    
    /* Improve attendance button size for mobile */
    .attendance-btn {
        padding: 0.75rem 0.5rem !important;
        font-size: 0.875rem !important;
        min-height: 60px !important;
        gap: 4px !important;
    }
    
    .attendance-btn i {
        font-size: 1rem !important;
        margin-bottom: 4px !important;
    }
    
    .attendance-btn span {
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        display: block !important; /* Show text on mobile */
    }
    
    /* Make the grid gap larger for easier touch */
    .grid-cols-4.gap-1 {
        gap: 6px !important;
    }
    
    /* Filter form responsive */
    .grid.grid-cols-1.md\:grid-cols-2.lg\:grid-cols-4 {
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
    }
    
    /* Student count mobile styling - PREVENT WRAPPING */
    .flex.justify-between.items-center h5 {
        font-size: 1rem !important;
        line-height: 1.2 !important;
        flex-wrap: nowrap !important;
        white-space: nowrap !important;
    }
    
    .flex.justify-between.items-center h5 span {
        font-size: 0.75rem !important;
        padding: 0.25rem 0.5rem !important;
        margin-left: 0.5rem !important;
        white-space: nowrap !important;
        flex-shrink: 0 !important;
    }
    
    /* Header title mobile adjustments */
    .bg-gradient-to-r.from-blue-600.to-purple-600 h4 {
        font-size: 1.25rem !important;
        line-height: 1.3 !important;
    }
    
    .bg-gradient-to-r.from-blue-600.to-purple-600 h4 span {
        font-size: 0.875rem !important;
        padding: 0.25rem 0.75rem !important;
        margin-left: 0.5rem !important;
    }
}

/* Specific for Samsung Galaxy A51 and similar dimensions (around 412px) */
@media (max-width: 414px) {
    /* Consistent container padding */
    .container-fluid.py-4 {
        padding-top: 7rem !important;
        padding-bottom: 1.5rem !important;
    }
    
    .avatar-circle {
        width: 40px !important; /* konsisten dengan breakpoint 768px */
        height: 40px !important;
        min-width: 40px !important; /* minimum width untuk mencegah shrinking */
        max-width: 40px !important; /* maximum width untuk mencegah expanding */
        font-size: 0.75rem !important; /* sedikit lebih kecil untuk layar kecil */
        border-radius: 9999px !important;
        aspect-ratio: 1 / 1 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-weight: 700 !important;
        line-height: 1 !important;
        padding: 0 !important;
        overflow: hidden;
        box-sizing: border-box !important;
        text-align: center;
        flex-shrink: 0; /* mencegah flex item mengecil */
        flex-grow: 0; /* mencegah flex item membesar */
        margin-right: 0 !important;
        margin-bottom: 0.5rem !important;
        margin-left: auto !important;
        margin-top: 0 !important;
        border: 2px solid rgba(255, 255, 255, 0.9) !important;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15) !important;
    }
    
    /* More compact text for smaller screens */
    .flex.justify-between.items-center h5 {
        font-size: 0.9rem !important;
    }
    
    .flex.justify-between.items-center h5 span {
        font-size: 0.7rem !important;
        padding: 0.2rem 0.4rem !important;
    }
}

@media (max-width: 399px) {
    /* Container padding for very small screens */
    .container-fluid.py-4 {
        padding-top: 7rem !important;
        padding-bottom: 1rem !important;
    }
    
    /* Even larger buttons for very small phones */
    .attendance-btn {
        padding: 1rem 0.5rem;
        font-size: 1rem;
        min-height: 70px;
        gap: 6px;
    }
    
    .attendance-btn i {
        font-size: 1.125rem;
        margin-bottom: 6px;
    }
    
    .attendance-btn span {
        font-size: 0.875rem;
        font-weight: 700;
    }
    
    /* Larger grid gap for very small screens */
    .grid-cols-4.gap-1 {
        gap: 8px;
    }
    
    /* Add more padding to student cards */
    .student-card .p-4 {
        padding: 1.25rem;
    }
    
    /* Avatar untuk layar sangat kecil - lebih kecil lagi */
    .avatar-circle {
        width: 35px !important; /* lebih kecil untuk layar sangat kecil */
        height: 35px !important;
        min-width: 35px !important; /* minimum width untuk mencegah shrinking */
        max-width: 35px !important; /* maximum width untuk mencegah expanding */
        font-size: 0.7rem !important; /* font disesuaikan */
        font-weight: 700 !important;
        border-radius: 9999px !important;
        aspect-ratio: 1 / 1 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        line-height: 1 !important;
        padding: 0 !important;
        overflow: hidden;
        box-sizing: border-box !important;
        text-align: center;
        flex-shrink: 0; /* mencegah flex item mengecil */
        flex-grow: 0; /* mencegah flex item membesar */
        margin-right: 0 !important;
        margin-bottom: 0.5rem !important;
        margin-left: auto !important;
        margin-top: 0 !important;
        border: 1.5px solid rgba(255, 255, 255, 0.9) !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12) !important;
    }
    
    /* Very compact text for very small screens */
    .flex.justify-between.items-center h5 {
        font-size: 0.85rem !important;
        line-height: 1.1 !important;
    }
    
    .flex.justify-between.items-center h5 span {
        font-size: 0.65rem !important;
        padding: 0.15rem 0.3rem !important;
        margin-left: 0.25rem !important;
    }
    
    /* Header adjustments for very small screens */
    .bg-gradient-to-r.from-blue-600.to-purple-600 h4 {
        font-size: 1.1rem !important;
        line-height: 1.2 !important;
    }
    
    .bg-gradient-to-r.from-blue-600.to-purple-600 h4 span {
        font-size: 0.8rem !important;
        padding: 0.2rem 0.6rem !important;
        margin-left: 0.4rem !important;
    }
}

/* Ultra small screens (320px and below) - iPhone SE and similar */
@media (max-width: 320px) {
    /* Container ultra compact */
    .container-fluid.py-4 {
        padding-top: 8rem !important;
        padding-left: 8px !important;
        padding-right: 8px !important;
        padding-bottom: 1rem !important;
    }
    
    /* Header ultra compact */
    .bg-gradient-to-r {
        padding: 10px !important;
    }
    
    .bg-gradient-to-r .block.md\:hidden .flex.items-center.justify-between.mb-3 {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 8px !important;
        margin-bottom: 10px !important;
    }
    
    .bg-gradient-to-r h4.text-lg {
        font-size: 14px !important;
        line-height: 18px !important;
    }
    
    .bg-gradient-to-r small {
        font-size: 10px !important;
        line-height: 14px !important;
    }
    
    .bg-gradient-to-r span.rounded-full {
        font-size: 8px !important;
        padding: 2px 6px !important;
    }
    
    #markAllPresent {
        padding: 6px 8px !important;
        font-size: 12px !important;
        min-width: auto !important;
        width: auto !important;
        height: 32px !important;
        white-space: nowrap !important;
        flex-shrink: 0 !important;
    }
    
    #markAllPresent span {
        display: inline !important; /* Show full text */
        margin-left: 4px !important;
        font-size: 10px !important;
    }
    
    #markAllPresent i {
        margin-right: 4px !important;
        font-size: 10px !important;
    }
    
    #markAllPresentMobile {
        padding: 4px 6px !important;
        font-size: 10px !important;
        min-width: auto !important;
        width: auto !important;
        height: 28px !important;
        white-space: nowrap !important;
        flex-shrink: 0 !important;
    }
    
    #markAllPresentMobile span {
        display: inline !important; /* Show full text */
        margin-left: 2px !important;
        font-size: 9px !important;
    }
    
    #markAllPresentMobile i {
        margin-right: 2px !important;
        font-size: 9px !important;
    }
    
    /* Filter form ultra compact */
    .grid.grid-cols-1 {
        gap: 8px !important;
    }
    
    /* Student cards ultra compact */
    .student-card {
        padding: 10px !important;
        margin-bottom: 6px !important;
    }
    
    .student-name {
        font-size: 12px !important;
        line-height: 16px !important;
    }
    
    /* Avatar ultra small */
    .avatar-circle {
        width: 30px !important;
        height: 30px !important;
        min-width: 30px !important;
        max-width: 30px !important;
        font-size: 0.6rem !important;
    }
    
    /* Attendance buttons ultra compact */
    .attendance-btn {
        padding: 0.5rem 0.3rem !important;
        font-size: 0.75rem !important;
        min-height: 50px !important;
        gap: 2px !important;
    }
    
    .attendance-btn i {
        font-size: 0.875rem !important;
        margin-bottom: 2px !important;
    }
    
    /* Floating button ultra small */
    .floating-save-btn {
        width: 44px !important;
        height: 44px !important;
        bottom: 12px !important;
        right: 12px !important;
    }
    
    .floating-save-btn i {
        font-size: 14px !important;
    }
    
    .floating-save-btn .unsaved-count {
        min-width: 12px !important;
        height: 12px !important;
        font-size: 8px !important;
        top: -2px !important;
        right: -2px !important;
    }
}

/* Custom scrollbar */
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f5f9;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* ==============================
   ULTIMATE FLOATING BUTTON FIX - FORCE RIGHT POSITION
   ============================== */
/* Page-specific spacing tweaks */
#studentsGrid { margin-bottom: 0; padding-bottom: 0; }
.absensi-page-last { margin-bottom: 0 !important; padding-bottom: 0 !important; }
/* Reduce bottom gap by letting this page's content shrink to content height */
/* Clean, solid-white background for this page only (no gradients)
   IMPORTANT: keep content scrollable inside the admin flex layout */
.content-area { 
    min-height: 0 !important;
    max-height: calc(100vh - 60px) !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
    padding-bottom: 0 !important; 
    flex: 1 1 auto !important;
    background: #ffffff !important; 
    background-image: none !important;
}
/* Also ensure the document background is plain white while this page is active */
body { background: #ffffff !important; background-image: none !important; }
.content-area > *:last-child { margin-bottom: 0 !important; }
.content-area .container-fluid.py-4 { padding-bottom: 0 !important; }
/* Tighten main content area for this page only */
/* Avoid altering global layout sizing here; rely on layout.css */
/* Override ANY external CSS that might interfere - AGGRESSIVE VERSION */
@media (max-width: 768px) {
    /* CRITICAL: Override aggressive mobile CSS resets */
    .floating-save-btn,
    #saveAll,
    button[id="saveAll"] {
        position: fixed !important;
        z-index: 999999 !important;
        bottom: 16px !important;
        right: 16px !important;
        left: auto !important;
        top: auto !important;
        width: 56px !important;
        height: 56px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 50% !important;
        background: linear-gradient(135deg, #3b82f6 0%, #9333ea 100%) !important;
        color: white !important;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4) !important;
        cursor: pointer !important;
        pointer-events: auto !important;
        visibility: visible !important;
        opacity: 1 !important;
        transform: none !important;
        margin: 0 !important;
    }
    
    /* CRITICAL: Badge positioning for mobile */
    .floating-save-btn .unsaved-count,
    #saveAll .unsaved-count {
        position: absolute !important;
        top: -4px !important;
        right: -4px !important;
        min-width: 16px !important;
        height: 16px !important;
        font-size: 10px !important;
        font-weight: 800 !important;
        border: 1.5px solid white !important;
        box-shadow: 0 2px 6px rgba(239, 68, 68, 0.5) !important;
        z-index: 1000001 !important;
        line-height: 1 !important;
        padding: 0 !important;
        background: #ef4444 !important;
        color: white !important;
        border-radius: 50% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    
    /* CRITICAL: Ensure proper top spacing for mobile */
    body .container-fluid.py-4,
    .container-fluid[style*="padding-top"] {
        padding-top: 8rem !important;
        margin-top: 0 !important;
    }
}
</style>

<script>
// Global variables
let attendanceData = {};
let loadingModal;

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 === ATTENDANCE SYSTEM INITIALIZATION ===');
    console.log('DOM Content Loaded - Starting initialization...');
    
    // Make sure markAllPresent is available globally
    window.markAllPresent = markAllPresent;
    console.log('📍 markAllPresent function attached to window');
    
    // Initialize floating button FIRST
    initializeFloatingButton();
    
    // Initialize other components
    initializeAttendanceSystem();
    initializeSearch();
    initializeFilters();
    initializeModal();
    initializeAllEventListeners();
    
    // Final setup
    updateUnsavedCount();
    updateAttendanceStats();
    // Apply initial filters without pagination
    applyFilters();
    
    console.log('✅ === ATTENDANCE SYSTEM READY ===');
    console.log('All systems initialized successfully!');
});

// Simple floating button initialization
function initializeFloatingButton() {
    // Remove any duplicate buttons first
    const allSaveButtons = document.querySelectorAll('#saveAll, .floating-save-btn');
    if (allSaveButtons.length > 1) {
        console.log('Found multiple save buttons, removing duplicates...');
        for (let i = 1; i < allSaveButtons.length; i++) {
            allSaveButtons[i].remove();
        }
    }
    
    const floatingBtn = document.getElementById('saveAll');
    if (!floatingBtn) {
        console.log('Floating button not found');
        return;
    }
    
    console.log('Floating button initialized');
    
    // Just ensure it's clickable - let CSS handle positioning
    floatingBtn.addEventListener('click', function() {
        saveAllAttendance();
    });
}

// Pagination removed

function getCurrentlyMatchingCards() {
    const searchTerm = document.getElementById('studentSearch')?.value.toLowerCase() || '';
    const statusFilter = document.getElementById('filterStatus')?.value || 'all';
    const cards = Array.from(document.querySelectorAll('.student-card'));
    const result = [];
    cards.forEach(card => {
        const studentName = card.dataset.studentName || '';
        const studentNisn = card.dataset.studentNisn || '';
        const currentStatus = getStudentCurrentStatus(card);
        let match = true;
        if (searchTerm) {
            const nameMatch = studentName.includes(searchTerm);
            const nisnMatch = studentNisn.includes(searchTerm);
            if (!nameMatch && !nisnMatch) match = false;
        }
        if (statusFilter !== 'all' && currentStatus !== statusFilter) match = false;
        if (match) result.push(card);
    });
    return result;
}

function showAllMatchingCards() {
    const matching = getCurrentlyMatchingCards();
    // Show all matching, hide non-matching
    const allCards = Array.from(document.querySelectorAll('.student-card'));
    allCards.forEach(card => {
        if (matching.includes(card)) {
            card.style.display = 'block';
            card.classList.remove('filtered-out');
        } else {
            card.style.display = 'none';
            card.classList.add('filtered-out');
        }
    });
    // Hide any pagination info UI
    const top = document.getElementById('paginationTop');
    const bottom = document.getElementById('paginationBottom');
    if (top) top.innerHTML = '';
    if (bottom) bottom.innerHTML = '';
}

// Remove range details; show a simpler info if filters active
function updateSearchResultsForPagination(totalMatching) {
    const searchResults = document.getElementById('searchResults');
    const searchResultsText = document.getElementById('searchResultsText');
    if (!searchResults || !searchResultsText) return;
    const searchTerm = document.getElementById('studentSearch')?.value.toLowerCase() || '';
    const statusFilter = document.getElementById('filterStatus')?.value || 'all';
    if (searchTerm || statusFilter !== 'all') {
        let message = `Menampilkan ${totalMatching} siswa`;
        if (searchTerm) message += ` untuk pencarian "${searchTerm}"`;
        if (statusFilter !== 'all') {
            const statusNames = { hadir: 'Hadir', sakit: 'Sakit', izin: 'Izin', alpha: 'Alpha', belum: 'Belum Diisi' };
            message += ` dengan status ${statusNames[statusFilter]}`;
        }
        searchResultsText.textContent = message;
        searchResults.classList.remove('hidden');
        searchResults.classList.add('search-results-enter');
    } else {
        searchResults.classList.add('hidden');
    }
}

// Removed pagination controls and navigation

// Initialize attendance system
function initializeAttendanceSystem() {
    // Add event listeners for attendance radios
    document.querySelectorAll('input[type="radio"][name^="status_"]').forEach(radio => {
        radio.addEventListener('change', function() {
            updateAttendanceData(this);
        });
    });
    
    // Add event listeners for keterangan textareas
    document.querySelectorAll('.student-keterangan').forEach(textarea => {
        textarea.addEventListener('input', function() {
            updateKeteranganData(this);
        });
    });
}

// Initialize search functionality
function initializeSearch() {
    const studentSearch = document.getElementById('studentSearch');
    if (studentSearch) {
        studentSearch.addEventListener('input', function() {
            console.log('Search input changed:', this.value);
            applyFilters();
        });
        console.log('Search functionality initialized');
    } else {
        console.warn('Student search element not found');
    }
}

// Initialize filters
function initializeFilters() {
    const filterStatus = document.getElementById('filterStatus');
    if (filterStatus) {
        filterStatus.addEventListener('change', function() {
            console.log('Filter status changed:', this.value);
            applyFilters();
        });
        console.log('Filter functionality initialized');
    } else {
        console.warn('Filter status element not found');
    }
    
    // Mark all present button - Desktop version
    const markAllBtn = document.getElementById('markAllPresent');
    if (markAllBtn) {
        // Clear any existing listeners by cloning the element
        const newMarkAllBtn = markAllBtn.cloneNode(true);
        markAllBtn.parentNode.replaceChild(newMarkAllBtn, markAllBtn);
        
        // Add fresh event listener
        newMarkAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('🎯 Mark All Present button clicked (Desktop)');
            
            // Directly execute without confirmation
            markAllPresent();
        });
        console.log('✅ Mark All Present button (Desktop) initialized');
    } else {
        console.warn('Mark All Present button not found');
    }
    
    // Mark all present button - Mobile version
    const markAllBtnMobile = document.getElementById('markAllPresentMobile');
    if (markAllBtnMobile) {
        // Clear any existing listeners by cloning the element
        const newMarkAllBtnMobile = markAllBtnMobile.cloneNode(true);
        markAllBtnMobile.parentNode.replaceChild(newMarkAllBtnMobile, markAllBtnMobile);
        
        // Add fresh event listener
        newMarkAllBtnMobile.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('🎯 Mark All Present button clicked (Mobile)');
            
            // Directly execute without confirmation
            markAllPresent();
        });
        console.log('✅ Mark All Present Mobile button initialized');
    }
}

// Initialize modal functionality
function initializeModal() {
    const loadingModal = document.getElementById('loadingModal');
    if (loadingModal) {
        loadingModal.show = function() {
            this.classList.remove('hidden');
            this.classList.add('loading-modal-enter');
        };
        
        loadingModal.hide = function() {
            this.classList.add('loading-modal-exit');
            setTimeout(() => {
                this.classList.add('hidden');
                this.classList.remove('loading-modal-enter', 'loading-modal-exit');
            }, 300);
        };
    }
}

// Update attendance data
function updateAttendanceData(radio) {
    const siswaId = radio.name.replace('status_', '');
    const status = radio.value;
    const studentCard = radio.closest('.student-card');
    
    if (!attendanceData[siswaId]) {
        attendanceData[siswaId] = {};
    }
    
    // CRITICAL FIX: Include siswa_id in the data structure
    attendanceData[siswaId].siswa_id = siswaId;
    attendanceData[siswaId].status = status;
    attendanceData[siswaId].keterangan = attendanceData[siswaId].keterangan || '';
    
    // Update visual feedback
    if (studentCard) {
        studentCard.classList.add('attendance-updated');
        setTimeout(() => {
            studentCard.classList.remove('attendance-updated');
        }, 600);
    }
    
    updateUnsavedCount();
    console.log('Attendance updated for student:', siswaId, 'Status:', status, 'Data:', attendanceData[siswaId]);
}

// Update keterangan data
function updateKeteranganData(textarea) {
    const siswaId = textarea.dataset.siswaId || textarea.getAttribute('data-siswa-id');
    const keterangan = textarea.value;
    
    if (!siswaId) {
        console.warn('No siswa ID found for textarea:', textarea);
        return;
    }
    
    if (!attendanceData[siswaId]) {
        attendanceData[siswaId] = {};
    }
    
    // CRITICAL FIX: Include siswa_id in the data structure
    attendanceData[siswaId].siswa_id = siswaId;
    attendanceData[siswaId].keterangan = keterangan;
    // Preserve existing status if any
    if (!attendanceData[siswaId].status) {
        attendanceData[siswaId].status = '';
    }
    
    console.log('Keterangan updated for student:', siswaId, 'Keterangan:', keterangan, 'Data:', attendanceData[siswaId]);
}

// Apply filters
function applyFilters() {
    console.log('Applying filters...');
    const matching = getCurrentlyMatchingCards();
    console.log(`Matching cards: ${matching.length}`);
    showAllMatchingCards();
    updateSearchResultsForPagination(matching.length);
}

// Get current status of student
function getStudentCurrentStatus(card) {
    const checkedRadio = card.querySelector('input[type="radio"]:checked');
    if (checkedRadio) {
        return checkedRadio.value;
    }
    return 'belum';
}

// Update search results display
function updateSearchResults(searchTerm, statusFilter, visibleCount, totalCount) {
    const searchResults = document.getElementById('searchResults');
    const searchResultsText = document.getElementById('searchResultsText');
    
    if (!searchResults || !searchResultsText) return;
    
    if (searchTerm || statusFilter !== 'all') {
        let message = `Menampilkan ${visibleCount} dari ${totalCount} siswa`;
        
        if (searchTerm) {
            message += ` untuk pencarian "${searchTerm}"`;
        }
        
        if (statusFilter !== 'all') {
            const statusNames = {
                'hadir': 'Hadir',
                'sakit': 'Sakit', 
                'izin': 'Izin',
                'alpha': 'Alpha',
                'belum': 'Belum Diisi'
            };
            message += ` dengan status ${statusNames[statusFilter]}`;
        }
        
    searchResultsText.textContent = message;
    searchResults.classList.remove('hidden');
    searchResults.classList.add('search-results-enter');
    } else {
        searchResults.classList.add('hidden');
    }
}

// Mark all students as present
function markAllPresent() {
    console.log('🚀 === MARK ALL PRESENT FUNCTION CALLED ===');
    console.log('Function starting execution...');

    if (window.ABSENSI_IS_OFF_DAY) {
        const ket = window.ABSENSI_OFF_DAY_INFO && window.ABSENSI_OFF_DAY_INFO.keterangan ? window.ABSENSI_OFF_DAY_INFO.keterangan : 'Hari libur/off';
        showNotification('Tidak bisa input absensi pada hari libur/off: ' + ket, 'warning');
        return;
    }
    
    // Prevent multiple executions
    if (window.markAllPresentRunning) {
        console.log('⚠️ Function already running, preventing duplicate execution');
        return;
    }
    
    console.log('✅ Setting running flag to true');
    window.markAllPresentRunning = true;
    
    // Get all visible student cards
    const allCards = document.querySelectorAll('.student-card');
    const visibleCards = Array.from(allCards).filter(card => {
        return card.style.display !== 'none' && !card.classList.contains('filtered-out');
    });
    
    console.log(`Total cards: ${allCards.length}, Visible cards: ${visibleCards.length}`);
    
    if (visibleCards.length === 0) {
        showNotification('Tidak ada siswa yang terlihat untuk ditandai hadir!', 'warning');
        window.markAllPresentRunning = false;
        return;
    }
    
    let successCount = 0;
    
    // Get the active button (desktop or mobile)
    const btnDesktop = document.getElementById('markAllPresent');
    const btnMobile = document.getElementById('markAllPresentMobile');
    const isDesktopView = window.innerWidth >= 768;
    
    let activeButton = isDesktopView ? btnDesktop : btnMobile;
    if (!activeButton) activeButton = btnDesktop || btnMobile; // fallback
    
    if (!activeButton) {
        console.error('No button found');
        window.markAllPresentRunning = false;
        return;
    }
    
    // Store original HTML and set enhanced loading state
    const originalHTML = activeButton.innerHTML;
    const originalDisabled = activeButton.disabled;
    const originalClasses = activeButton.className;
    
    // Enhanced loading state with better animation
    activeButton.innerHTML = `
        <div class="flex items-center justify-center">
            <i class="fas fa-spinner fa-spin mr-2"></i>
            <span>Memproses...</span>
        </div>
    `;
    activeButton.disabled = true;
    activeButton.classList.add('opacity-80', 'cursor-not-allowed');
    
    console.log('Button set to loading:', activeButton.id, 'Window width:', window.innerWidth);
    
    // Show initial loading notification
    showNotification('Memproses data siswa...', 'info');
    
    try {
        let processDelay = 0;
        
        // Process students with visual feedback and smooth animation
        visibleCards.forEach((card, index) => {
            setTimeout(() => {
                const siswaId = card.getAttribute('data-siswa-id');
                
                console.log(`Processing card ${index + 1}/${visibleCards.length}, siswaId: ${siswaId}`);
                
                if (!siswaId) {
                    console.error(`No siswa ID found for card ${index + 1}`);
                    return;
                }
                
                const hadirRadio = card.querySelector(`input[name="status_${siswaId}"][value="hadir"]`);
                
                if (hadirRadio) {
                    // Add processing animation to card
                    card.classList.add('animate-pulse', 'bg-green-50', 'border-green-200');
                    
                    // Select the radio and update data
                    hadirRadio.checked = true;
                    updateAttendanceData(hadirRadio);
                    successCount++;
                    
                    console.log(`✅ Successfully processed student ${index + 1}/${visibleCards.length}: ${siswaId}`);
                    
                    // Remove processing animation and add success highlight
                    setTimeout(() => {
                        card.classList.remove('animate-pulse', 'bg-green-50', 'border-green-200');
                        card.classList.add('bg-green-100', 'border-green-300', 'attendance-updated');
                        
                        // Remove success highlight after delay
                        setTimeout(() => {
                            card.classList.remove('bg-green-100', 'border-green-300', 'attendance-updated');
                        }, 1000);
                    }, 200);
                    
                } else {
                    console.error(`No hadir radio found for student: ${siswaId}`);
                }
                
                // If this is the last card, finish the process
                if (index === visibleCards.length - 1) {
                    setTimeout(() => {
                        finishMarkAllProcess(activeButton, originalHTML, originalDisabled, originalClasses, successCount);
                    }, 300);
                }
            }, processDelay);
            
            processDelay += 30; // 30ms delay between each card for smooth animation
        });
        
    } catch (error) {
        console.error('Error during processing:', error);
        finishMarkAllProcess(activeButton, originalHTML, originalDisabled, originalClasses, successCount);
    }
}

// Finish mark all present process with enhanced animation
function finishMarkAllProcess(activeButton, originalHTML, originalDisabled, originalClasses, successCount) {
    console.log('Finishing mark all present process...');
    
    // Show success state animation on button
    activeButton.innerHTML = `
        <div class="flex items-center justify-center">
            <i class="fas fa-check mr-2 text-green-200"></i>
            <span>Selesai!</span>
        </div>
    `;
    activeButton.classList.remove('opacity-80', 'cursor-not-allowed');
    activeButton.classList.add('bg-green-600', 'animate-pulse');
    
    // After brief success display, restore to original state
    setTimeout(() => {
        activeButton.innerHTML = originalHTML;
        activeButton.disabled = originalDisabled;
        activeButton.className = originalClasses;
        
        // Show final success notification
        showNotification(`🎉 ${successCount} siswa berhasil ditandai hadir!`, 'success');
        updateUnsavedCount();
        
        // Reset running flag
        window.markAllPresentRunning = false;
        
        console.log('✅ Mark all present process completed successfully!');
    }, 1200);
}

// Update unsaved count
function updateUnsavedCount() {
    const count = Object.keys(attendanceData).length;
    const badge = document.getElementById('unsavedCount');
    
    if (badge) {
        badge.textContent = count;
        if (count > 0) {
            badge.classList.remove('hidden');
            badge.classList.add('pulse');
        } else {
            badge.classList.add('hidden');
            badge.classList.remove('pulse');
        }
    }
}

// Update attendance statistics
function updateAttendanceStats() {
    // This function can be expanded to show statistics
    console.log('Attendance stats updated');
}

// Save all attendance data
function saveAllAttendance() {
    console.log('saveAllAttendance() called');
    console.log('Current attendanceData object:', attendanceData);
    console.log('Number of records in attendanceData:', Object.keys(attendanceData).length);

    if (window.ABSENSI_IS_OFF_DAY) {
        const ket = window.ABSENSI_OFF_DAY_INFO && window.ABSENSI_OFF_DAY_INFO.keterangan ? window.ABSENSI_OFF_DAY_INFO.keterangan : 'Hari libur/off';
        showNotification('Tidak bisa input absensi pada hari libur/off: ' + ket, 'warning');
        return;
    }
    
    if (Object.keys(attendanceData).length === 0) {
        showNotification('Belum ada data absensi yang diisi!', 'warning');
        return;
    }
    
    // Enhanced loading state for floating button
    const saveBtn = document.getElementById('saveAll');
    if (!saveBtn) return;
    
    const iconElement = saveBtn.querySelector('.fas');
    const originalIcon = iconElement.className;
    
    // Change to loading state
    iconElement.className = 'fas fa-spinner fa-spin';
    saveBtn.style.pointerEvents = 'none';
    
    // Show loading modal
    const loadingModal = document.getElementById('loadingModal');
    if (loadingModal && loadingModal.show) {
        loadingModal.show();
    }
    
    // Prepare data for submission
    // Convert attendanceData object to array format expected by server
    const attendanceArray = Object.values(attendanceData).filter(data => 
        data.siswa_id && data.status
    );
    
    console.log('Filtered attendance array:', attendanceArray);
    console.log('Array length after filter:', attendanceArray.length);
    
    if (attendanceArray.length === 0) {
        console.warn('No valid attendance data after filtering!');
        showNotification('Data absensi tidak valid - siswa_id atau status tidak lengkap!', 'error');
        
        // Restore button state
        iconElement.className = originalIcon;
        saveBtn.style.pointerEvents = 'auto';
        if (loadingModal && loadingModal.hide) {
            loadingModal.hide();
        }
        return;
    }
    
    const formData = new FormData();
    const tanggal = document.getElementById('tanggal')?.value || '';
    const kelas = document.getElementById('kelas')?.value || document.querySelector('input[name="kelas"]')?.value || '';
    
    formData.append('tanggal', tanggal);
    formData.append('kelas', kelas);
    formData.append('attendance_data', JSON.stringify(attendanceArray));
    
    console.log('Form data being sent:');
    console.log('- tanggal:', tanggal);
    console.log('- kelas:', kelas);
    console.log('- attendance_data:', JSON.stringify(attendanceArray));
    
    // Get CSRF token from cookie
    function getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for(let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
    
    const csrfToken = getCookie('csrf_cookie_name');
    if (csrfToken) {
        formData.append('csrf_test_name', csrfToken);
    }
    
    // Submit data to save_all endpoint
    fetch('/admin/absensi/save_all', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken || ''
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Data absensi berhasil disimpan! 🎉', 'success');
            attendanceData = {}; // Clear data
            updateUnsavedCount();
        } else {
            let msg = 'Gagal menyimpan data: ' + (data.message || 'Unknown error');
            if (Array.isArray(data.details) && data.details.length > 0) {
                const topDetails = data.details.slice(0, 3);
                console.error('Save_all details:', data.details);
                msg += '<br><small>' + topDetails.join('<br>') + '</small>';
                if (data.details.length > 3) {
                    msg += '<br><small>...dan ' + (data.details.length - 3) + ' error lainnya</small>';
                }
            }
            showNotification(msg, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan saat menyimpan data', 'error');
    })
    .finally(() => {
        // Restore button state
        iconElement.className = originalIcon;
        saveBtn.style.pointerEvents = 'auto';
        
        // Hide loading modal
        if (loadingModal && loadingModal.hide) {
            loadingModal.hide();
        }
    });
}

// Show notification
function showNotification(message, type = 'info') {
    console.log(`=== SHOWING NOTIFICATION ===`);
    console.log('Message:', message);
    console.log('Type:', type);
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm toast-enter ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        type === 'warning' ? 'bg-yellow-500 text-black' :
        'bg-blue-500 text-white'
    }`;
    
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${
                type === 'success' ? 'fa-check-circle' :
                type === 'error' ? 'fa-times-circle' :
                type === 'warning' ? 'fa-exclamation-triangle' :
                'fa-info-circle'
            } mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    console.log('Notification element created:', notification);
    console.log('Appending to document.body...');
    document.body.appendChild(notification);
    console.log('Notification appended successfully!');
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        console.log('Removing notification after 3 seconds...');
        notification.classList.add('toast-exit');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
                console.log('Notification removed from DOM');
            }
        }, 300);
    }, 3000);
}

// Clear filters function
function clearFilters() {
    const studentSearch = document.getElementById('studentSearch');
    const filterStatus = document.getElementById('filterStatus');
    
    if (studentSearch) studentSearch.value = '';
    if (filterStatus) filterStatus.value = 'all';
    
    applyFilters();
    showNotification('Filter berhasil dikosongkan', 'success');
}

// Navigate date function
function navigateDate(days) {
    const dateInput = document.getElementById('tanggal');
    if (!dateInput) return;
    
    const currentDate = new Date(dateInput.value);
    currentDate.setDate(currentDate.getDate() + days);
    
    const newDate = currentDate.toISOString().split('T')[0];
    dateInput.value = newDate;
    
    reloadPage();
}

// Reload page function
function reloadPage() {
    const form = document.getElementById('filterForm');
    if (form) {
        form.submit();
    }
}

// Initialize modal functionality - ENHANCED VERSION
function initializeModal() {
    const loadingModal = document.getElementById('loadingModal');
    if (loadingModal) {
        loadingModal.show = function() {
            this.classList.remove('hidden');
            this.classList.add('loading-modal-enter');
        };
        
        loadingModal.hide = function() {
            this.classList.add('loading-modal-exit');
            setTimeout(() => {
                this.classList.add('hidden');
                this.classList.remove('loading-modal-enter', 'loading-modal-exit');
            }, 300);
        };
    }
}

// Initialize all event listeners - ENHANCED VERSION
function initializeAllEventListeners() {
    console.log('Initializing all event listeners...');
    
    // Save button - ONLY handle save functionality here
    const saveBtn = document.getElementById('saveAll');
    if (saveBtn) {
        saveBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Save button clicked!');
            saveAllAttendance();
        });
        console.log('Save button event listener attached');
    }
    
    // NOTE: Mark All Present buttons are handled in initializeFilters()
    // Removed duplicate event listeners to prevent conflicts
    
    // Date navigation
    const prevDayBtn = document.getElementById('prevDay');
    if (prevDayBtn) {
        prevDayBtn.addEventListener('click', function() {
            navigateDate(-1);
        });
    }
    
    const nextDayBtn = document.getElementById('nextDay');
    if (nextDayBtn) {
        nextDayBtn.addEventListener('click', function() {
            navigateDate(1);
        });
    }
    
    // Form change listeners
    const dateInput = document.getElementById('tanggal');
    if (dateInput) {
        dateInput.addEventListener('change', function() {
            reloadPage();
        });
    }
    
    const kelasSelect = document.getElementById('kelas');
    if (kelasSelect) {
        kelasSelect.addEventListener('change', function() {
            reloadPage();
        });
    }
    
    // NOTE: Search and filter listeners are handled in their respective init functions
    // Removed duplicate event listeners to prevent conflicts
    
    // Attendance radio button listeners
    document.querySelectorAll('input[type="radio"][name^="status_"]').forEach(radio => {
        radio.addEventListener('change', function() {
            updateAttendanceData(this);
        });
    });
    
    // Keterangan textarea listeners  
    document.querySelectorAll('.student-keterangan').forEach(textarea => {
        textarea.addEventListener('input', function() {
            updateKeteranganData(this);
        });
    });
    
    console.log('All event listeners initialized successfully');
}
</script>

<?= $this->endSection() ?>

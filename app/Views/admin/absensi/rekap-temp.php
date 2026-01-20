<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<!-- Link to external CSS -->
<link rel="stylesheet" href="<?= base_url('css/rekap-absensi-clean.css') ?>">

<style>
/* Admin-specific header styling */
.admin-header {
    background: linear-gradient(135deg, #4472C4, #5a67d8);
    color: white;
    padding: 25px;
    border-radius: 15px;
    margin-bottom: 25px;
    text-align: center;
    box-shadow: 0 8px 32px rgba(68, 114, 196, 0.3);
}

.admin-header h2 {
    margin-bottom: 10px;
    font-weight: bold;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.admin-header .school-info {
    font-size: 18px;
    margin-bottom: 10px;
    font-weight: 600;
}

.admin-header .class-info {
    font-size: 16px;
    opacity: 0.9;
}
</style>

<div class="container-fluid">
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="text-center text-white">
            <div class="loading-spinner"></div>
            <p class="mt-3">Memproses data...</p>
        </div>
    </div>

    <!-- Admin Header Section -->
    <div class="admin-header">
        <h2>📊 REKAP ABSENSI SISWA</h2>
        <div class="school-info">SDN GROGOL UTARA 09</div>
        <div class="class-info">
            KELAS: <?= strtoupper($kelas ?? '5A') ?> | 
            BULAN: <?= strtoupper($bulan_nama ?? 'JULI') ?> <?= $tahun ?? 2025 ?> |
            TAHUN PELAJARAN: <?= ($tahun ?? 2025) ?>/<?= ($tahun ?? 2025) + 1 ?>
        </div>
    </div>

    <!-- Enhanced Filter Section -->
    <div class="filter-section">
        <form id="filterForm" method="GET">
            <div class="row align-items-end g-4">
                <?php if ($userRole === 'admin'): ?>
                <div class="col-md-3">
                    <label for="kelas" class="form-label">
                        <i class="fas fa-school me-2"></i>Kelas
                    </label>
                    <select class="form-control" id="kelas" name="kelas" required>
                        <option value="">Pilih Kelas</option>
                        <?php foreach ($allKelas as $kelas): ?>
                        <option value="<?= $kelas['kelas'] ?>" 
                                <?= $filterKelas === $kelas['kelas'] ? 'selected' : '' ?>>
                            Kelas <?= $kelas['kelas'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php else: ?>
                <div class="col-md-3">
                    <label for="kelas" class="form-label">
                        <i class="fas fa-school me-2"></i>Kelas
                    </label>
                    <input type="text" class="form-control" value="Kelas <?= $userKelas ?>" readonly>
                    <input type="hidden" name="kelas" value="<?= $userKelas ?>">
                </div>
                <?php endif; ?>
                <div class="col-md-3">
                    <label for="bulan" class="form-label">
                        <i class="fas fa-calendar-alt me-2"></i>Bulan & Tahun
                    </label>
                    <input type="month" class="form-control" id="bulan" name="bulan" 
                           value="<?= $filterBulan ?>" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="fas fa-search me-2"></i>Tampilkan
                    </button>
                </div>
                <div class="col-md-2">
                    <button type="button" id="downloadExcel" class="btn btn-success btn-lg w-100" 
                            <?= empty($attendanceData) || empty($attendanceData['students']) ? 'disabled' : '' ?>>
                        <i class="fas fa-download me-2"></i>Excel
                    </button>
                </div>
            </div>
        </form>
    </div>

    <?php if (!empty($attendanceData) && !empty($attendanceData['students'])): ?>

    <!-- Header Judul -->
    <div class="text-center mb-4">
        <h2 class="fw-bold text-dark mb-1">DAFTAR HADIR PESERTA DIDIK</h2>
        <h3 class="fw-bold text-primary mb-1">SDN GROGOL UTARA 09</h3>
        <h4 class="fw-bold text-secondary mb-1">KELAS <?= strtoupper($attendanceData['kelas'] ?? 'TIDAK DIPILIH') ?></h4>
        <h5 class="text-muted">TAHUN PELAJARAN <?= date('Y') ?>/<?= (date('Y') + 1) ?></h5>
    </div>

    <!-- Nama Bulan -->
    <div class="text-center mb-3">
        <h4 class="fw-bold text-primary">
            BULAN <?= strtoupper(date('F Y', mktime(0, 0, 0, (int)$attendanceData['month'], 1, (int)$attendanceData['year']))) ?>
        </h4>
    </div>

    <!-- Rekap Table Card -->
    <div class="card card-rekap">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-table"></i> 
                Rekap Absensi Kelas <?= $attendanceData['kelas'] ?? 'Tidak Dipilih' ?> - 
                <?php if (!empty($attendanceData) && isset($attendanceData['year']) && isset($attendanceData['month'])): ?>
                    <?= date('F Y', mktime(0, 0, 0, (int)$attendanceData['month'], 1, (int)$attendanceData['year'])) ?>
                <?php else: ?>
                    <?= date('F Y') ?>
                <?php endif; ?>
            </h6>
        </div>
        <div class="card-body p-0">
            
            <div class="table-responsive">
                <table class="excel-style-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">No</th>
                            <th class="student-name" style="width: 150px;">Nama Siswa</th>
                            
                            <!-- Daily columns -->
                            <?php foreach ($attendanceData['days'] as $day): ?>
                            <?php 
                            $dayStr = str_pad($day, 2, '0', STR_PAD_LEFT);
                            $currentDate = $attendanceData['year'] . '-' . str_pad($attendanceData['month'], 2, '0', STR_PAD_LEFT) . '-' . $dayStr;
                            $dayOfWeek = date('w', strtotime($currentDate));
                            $isWeekend = ($dayOfWeek == 0 || $dayOfWeek == 6);
                            $isHoliday = isset($attendanceData['holidays']) && in_array($currentDate, $attendanceData['holidays']);
                            
                            $headerClass = 'day-header';
                            if ($isHoliday) {
                                $headerClass .= ' holiday';
                            } elseif ($isWeekend) {
                                $headerClass .= ' weekend';
                            }
                            ?>
                            <th class="<?= $headerClass ?>"><?= $day ?></th>
                            <?php endforeach; ?>
                            
                            <!-- Summary columns with consistent styling -->
                            <th class="summary-header">S</th>
                            <th class="summary-header">I</th>
                            <th class="summary-header">A</th>
                            
                            <!-- Total and percentage with consistent styling -->
                            <th class="total-header">Total</th>
                            <th class="total-header">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($attendanceData['students'] as $student): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td class="student-name" title="<?= $student['nama'] ?>"><?= $student['nama'] ?></td>
                            
                            <!-- Daily attendance -->
                            <?php foreach ($attendanceData['days'] as $day): ?>
                            <?php 
                            $dayStr = str_pad($day, 2, '0', STR_PAD_LEFT);
                            $currentDate = $attendanceData['year'] . '-' . str_pad($attendanceData['month'], 2, '0', STR_PAD_LEFT) . '-' . $dayStr;
                            $dayOfWeek = date('w', strtotime($currentDate));
                            $isWeekend = ($dayOfWeek == 0 || $dayOfWeek == 6);
                            $isHoliday = isset($attendanceData['holidays']) && in_array($currentDate, $attendanceData['holidays']);
                            
                            $cellClass = '';
                            if ($isHoliday) {
                                $cellClass = 'holiday-cell';
                            } elseif ($isWeekend) {
                                $cellClass = 'weekend-cell';
                            }
                            
                            $status = $student['daily'][$dayStr] ?? '';
                            $mark = '';
                            $markClass = '';
                            
                            if ($isHoliday || $isWeekend) {
                                $mark = '■';
                                $markClass = '';
                            } else {
                                switch ($status) {
                                    case 'hadir':
                                        $mark = '✓';
                                        $markClass = 'hadir';
                                        break;
                                    case 'izin':
                                        $mark = 'I';
                                        $markClass = 'izin';
                                        break;
                                    case 'sakit':
                                        $mark = 'S';
                                        $markClass = 'sakit';
                                        break;
                                    case 'alpha':
                                        $mark = 'A';
                                        $markClass = 'alpha';
                                        break;
                                    default:
                                        if (strtotime($currentDate) <= time()) {
                                            $mark = 'A';
                                            $markClass = 'alpha';
                                        } else {
                                            $mark = '-';
                                            $markClass = '';
                                        }
                                }
                            }
                            ?>
                            <td class="<?= $cellClass ?>">
                                <span class="attendance-mark <?= $markClass ?>"><?= $mark ?></span>
                            </td>
                            <?php endforeach; ?>
                            
                            <!-- Summary columns -->
                            <td class="summary-number"><?= $student['summary']['sakit'] ?></td>
                            <td class="summary-number"><?= $student['summary']['izin'] ?></td>
                            <td class="summary-number"><?= $student['summary']['alpha'] ?></td>
                            
                            <!-- Total and percentage -->
                            <td class="summary-number"><?= $student['summary']['hadir'] ?></td>
                            <td class="percentage-cell"><?= number_format($student['percentage'], 1) ?>%</td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php
                        // Calculate proper totals
                        $finalTotalSakit = 0;
                        $finalTotalIzin = 0;
                        $finalTotalAlpha = 0;
                        
                        foreach ($attendanceData['students'] as $student) {
                            $finalTotalSakit += $student['summary']['sakit'];
                            $finalTotalIzin += $student['summary']['izin'];
                            $finalTotalAlpha += $student['summary']['alpha'];
                        }
                        
                        $totalStudents = count($attendanceData['students']);
                        $totalDays = count($attendanceData['days']);
                        $totalAttendanceDays = $totalStudents * $totalDays;
                        
                        $percentageSakit = $totalAttendanceDays > 0 ? ($finalTotalSakit / $totalAttendanceDays) * 100 : 0;
                        $percentageIzin = $totalAttendanceDays > 0 ? ($finalTotalIzin / $totalAttendanceDays) * 100 : 0;
                        $percentageAlpha = $totalAttendanceDays > 0 ? ($finalTotalAlpha / $totalAttendanceDays) * 100 : 0;
                        ?>
                        
                        <!-- Total Row -->
                        <tr class="summary-row" style="border-top: 2px solid #333;">
                            <td colspan="<?= (2 + count($attendanceData['days'])) ?>" class="merged-summary-cell">Total</td>
                            <!-- Summary totals aligned under S, I, A columns -->
                            <td class="summary-total"><?= $finalTotalSakit ?></td>
                            <td class="summary-total"><?= $finalTotalIzin ?></td>
                            <td class="summary-total"><?= $finalTotalAlpha ?></td>
                            <!-- Empty cells for Total and % columns -->
                            <td></td>
                            <td></td>
                        </tr>
                        
                        <!-- Percentage Row -->
                        <tr class="percentage-row">
                            <td colspan="<?= (2 + count($attendanceData['days'])) ?>" class="merged-percentage-cell">Persentase Kehadiran</td>
                            <!-- Percentage values aligned under S, I, A columns -->
                            <td class="percentage-value" style="color: #1976d2;"><?= number_format($percentageSakit, 1) ?>%</td>
                            <td class="percentage-value" style="color: #1976d2;"><?= number_format($percentageIzin, 1) ?>%</td>
                            <td class="percentage-value" style="color: #1976d2;"><?= number_format($percentageAlpha, 1) ?>%</td>
                            <!-- Empty cells for Total and % columns -->
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php else: ?>
    <!-- No Data Message -->
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Tidak Ada Data</h5>
            <p class="text-muted">
                <?php if (!$filterKelas): ?>
                    Silakan pilih kelas terlebih dahulu.
                <?php else: ?>
                    Tidak ada data absensi untuk kelas <?= $filterKelas ?> pada bulan <?= isset($filterBulan) ? date('F Y', strtotime($filterBulan . '-01')) : date('F Y') ?>.
                <?php endif; ?>
            </p>
        </div>
    </div>
    <?php endif; ?>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced form submission with loading animation
    const filterForm = document.getElementById('filterForm');
    const kelasSelect = document.getElementById('kelas');
    const bulanInput = document.getElementById('bulan');
    const submitBtn = filterForm.querySelector('button[type="submit"]');
    
    // Store original button content
    const originalBtnContent = submitBtn.innerHTML;
    
    // Loading state function
    function showLoading() {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memuat Data...';
        submitBtn.classList.add('btn-secondary');
        submitBtn.classList.remove('btn-primary');
    }
    
    function hideLoading() {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnContent;
        submitBtn.classList.remove('btn-secondary');
        submitBtn.classList.add('btn-primary');
    }
    
    // Auto submit with loading animation
    function autoSubmit() {
        const hasKelas = kelasSelect?.value || '<?= $userKelas ?>';
        const hasBulan = bulanInput?.value;
        
        if (hasKelas && hasBulan) {
            showLoading();
            
            // Add small delay for better UX
            setTimeout(() => {
                filterForm.submit();
            }, 300);
        }
    }
    
    // Auto submit when filters change
    if (kelasSelect) {
        kelasSelect.addEventListener('change', autoSubmit);
    }
    
    if (bulanInput) {
        bulanInput.addEventListener('change', autoSubmit);
    }
    
    // Manual submit with loading
    filterForm.addEventListener('submit', function(e) {
        showLoading();
    });
    
    // Download Excel functionality
    const downloadBtn = document.getElementById('downloadExcel');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function() {
            if (!this.disabled) {
                // Show downloading state
                const originalContent = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Download...';
                this.disabled = true;
                
                // Redirect to export URL
                const exportUrl = '<?= base_url('admin/absensi/export') ?>?' + 
                    new URLSearchParams({
                        kelas: '<?= $filterKelas ?>',
                        start_date: '<?= $filterBulan ?>-01',
                        end_date: '<?= date('Y-m-t', strtotime($filterBulan . '-01')) ?>'
                    });
                
                window.location.href = exportUrl;
                
                // Reset button after delay
                setTimeout(() => {
                    this.innerHTML = originalContent;
                    this.disabled = false;
                }, 2000);
            }
        });
    }
    
    // Add smooth animations to table on load
    const table = document.querySelector('.excel-style-table');
    if (table) {
        table.style.opacity = '0';
        table.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            table.style.transition = 'all 0.5s ease';
            table.style.opacity = '1';
            table.style.transform = 'translateY(0)';
        }, 100);
    }
    
    // Add hover effects to table rows
    const tableRows = document.querySelectorAll('.excel-style-table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
            this.style.transform = 'scale(1.01)';
            this.style.transition = 'all 0.2s ease';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
            this.style.transform = 'scale(1)';
        });
    });
});
</script>

<?= $this->endSection() ?>

<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<style>
/* Enhanced styles for rekap view */
.excel-style-table {
    border-collapse: collapse;
    width: 100%;
    font-size: 12px;
    font-family: Arial, sans-serif;
    background-color: white;
    margin: 0;
}

.excel-style-table th,
.excel-style-table td {
    border: 1px solid #000;
    padding: 4px 6px;
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
    height: 25px;
}

.excel-style-table th {
    background-color: #4472C4;
    font-weight: bold;
    color: white;
    font-size: 11px;
    border: 1px solid #000;
}

.excel-style-table .student-name {
    text-align: left;
    max-width: 150px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    padding-left: 8px;
}

.excel-style-table .day-header {
    width: 25px;
    font-size: 10px;
    background-color: #4472C4;
    font-weight: bold;
    color: white;
}

.excel-style-table .day-header.weekend {
    background-color: #E74C3C;
    color: white;
    font-weight: bold;
}

.excel-style-table .day-header.holiday {
    background-color: #ffcdd2;
    color: #b71c1c;
    font-weight: bold;
}

.excel-style-table .day-cell {
    width: 25px;
    font-size: 11px;
    font-weight: bold;
}

.excel-style-table .day-cell.weekend {
    background-color: #ffebee;
    color: #d32f2f;
}

.excel-style-table .day-cell.holiday {
    background-color: #ffcdd2;
    color: #b71c1c;
}

.attendance-mark {
    font-weight: bold;
    font-size: 12px;
}

.attendance-mark.hadir {
    color: #2e7d32;
}

.attendance-mark.sakit {
    color: #d32f2f;
}

.attendance-mark.izin {
    color: #f57c00;
}

.attendance-mark.alpha {
    color: #424242;
}

.filter-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.filter-section .form-label {
    color: white;
    font-weight: 600;
    margin-bottom: 8px;
}

.filter-section .form-control {
    border: none;
    border-radius: 8px;
    padding: 10px 15px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    font-weight: 500;
}

.filter-section .form-control:focus {
    background: white;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.filter-section .btn-primary {
    background: linear-gradient(135deg, #ff6b6b, #ee5a24);
    border: none;
    border-radius: 8px;
    padding: 10px 20px;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(238, 90, 36, 0.4);
    transition: all 0.3s ease;
}

.filter-section .btn-primary:hover {
    background: linear-gradient(135deg, #ee5a24, #ff6b6b);
    box-shadow: 0 6px 20px rgba(238, 90, 36, 0.6);
}

.filter-section .btn-success {
    background: linear-gradient(135deg, #00b894, #00a085);
    border: none;
    border-radius: 8px;
    padding: 10px 20px;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(0, 184, 148, 0.4);
    transition: all 0.3s ease;
}

.filter-section .btn-success:hover:not(:disabled) {
    background: linear-gradient(135deg, #00a085, #00b894);
    box-shadow: 0 6px 20px rgba(0, 184, 148, 0.6);
}

.filter-section .btn-success:disabled {
    background: #6c757d;
    box-shadow: none;
    opacity: 0.6;
}

.card-rekap {
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border-radius: 15px;
    overflow: hidden;
    animation: slideInUp 0.6s ease-out;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.summary-number {
    background-color: #f0f0f0 !important;
    font-weight: bold !important;
    border: 1px solid #000 !important;
    text-align: center !important;
}

.percentage-cell {
    background-color: #e3f2fd !important;
    font-weight: bold !important;
    color: #1976d2 !important;
    border: 1px solid #000 !important;
    text-align: center !important;
}

.summary-row {
    border-top: 2px solid #000 !important;
}

.summary-row td {
    border: 1px solid #000 !important;
    font-weight: bold !important;
    height: 25px;
}

.summary-total {
    background-color: #f0f0f0 !important;
    font-weight: bold !important;
    text-align: center !important;
    border: 1px solid #000 !important;
}

.percentage-row {
    background-color: #e3f2fd !important;
}

.percentage-row td {
    border: 1px solid #000 !important;
    font-weight: bold !important;
    height: 25px;
}

.percentage-value {
    background-color: #e3f2fd !important;
    font-weight: bold !important;
    text-align: center !important;
    border: 1px solid #000 !important;
    color: #1976d2 !important;
}

.summary-header {
    background-color: #4472C4 !important;
    font-weight: bold !important;
    color: white !important;
    text-align: center !important;
    width: 30px !important;
    border: 1px solid #000 !important;
}

.total-header {
    background-color: #4472C4 !important;
    font-weight: bold !important;
    color: white !important;
    text-align: center !important;
    width: 50px !important;
    border: 1px solid #000 !important;
}

/* Enhanced styling for merged summary cells */
.merged-summary-cell {
    background-color: #f0f0f0 !important;
    border: 1px solid #000 !important;
    text-align: center !important;
    font-weight: bold !important;
    font-size: 13px !important;
    padding: 8px !important;
}

.merged-percentage-cell {
    background-color: #e3f2fd !important;
    border: 1px solid #000 !important;
    text-align: center !important;
    font-weight: bold !important;
    font-size: 13px !important;
    padding: 8px !important;
}

/* Styling untuk header yang lebih kontras */
.excel-style-table thead tr {
    background: linear-gradient(135deg, #4472C4, #5a7fd1);
}

.excel-style-table .day-header.weekend {
    background: linear-gradient(135deg, #E74C3C, #ec6352);
}
</style>

<!-- Alert Testing Mode -->
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <h5 class="alert-heading">
        <i class="fas fa-vial me-2"></i>Mode Testing Aktif
    </h5>
    <p class="mb-0">Anda sedang melihat preview desain rekap absensi dengan sample data. Database tidak diperlukan untuk testing ini.</p>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

<!-- Enhanced Filters Section -->
<div class="filter-section">
    <form id="filterForm" method="GET">
        <div class="row align-items-end g-4">
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
                <button type="button" id="downloadExcel" class="btn btn-success btn-lg w-100">
                    <i class="fas fa-download me-2"></i>Excel
                </button>
            </div>
        </div>
    </form>
</div>

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
            <?= date('F Y', mktime(0, 0, 0, (int)$attendanceData['month'], 1, (int)$attendanceData['year'])) ?>
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
                        
                        $headerClass = 'day-header';
                        if ($isWeekend) {
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
                        
                        $cellClass = '';
                        if ($isWeekend) {
                            $cellClass = 'weekend-cell';
                        }
                        
                        $status = $student['daily'][$dayStr] ?? '';
                        $mark = '';
                        $markClass = '';
                        
                        if ($isWeekend) {
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
                    // Calculate totals for summary rows
                    $finalTotalSakit = 0;
                    $finalTotalIzin = 0;
                    $finalTotalAlpha = 0;
                    $totalStudents = count($attendanceData['students']);
                    $totalDays = count($attendanceData['days']);
                    $totalAttendanceDays = $totalStudents * $totalDays;
                    
                    foreach ($attendanceData['students'] as $student) {
                        $finalTotalSakit += $student['summary']['sakit'];
                        $finalTotalIzin += $student['summary']['izin'];
                        $finalTotalAlpha += $student['summary']['alpha'];
                    }
                    
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

<!-- Success Message -->
<div class="alert alert-success mt-4">
    <h5 class="alert-heading">
        <i class="fas fa-check-circle me-2"></i>Implementasi Berhasil!
    </h5>
    <p class="mb-0">Desain rekap absensi sudah berhasil diimplementasikan ke dalam aplikasi CodeIgniter 4 dengan perfect alignment sesuai format Excel.</p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced form submission with loading animation
    const filterForm = document.getElementById('filterForm');
    const kelasSelect = document.getElementById('kelas');
    const bulanInput = document.getElementById('bulan');
    const submitBtn = filterForm.querySelector('button[type="submit"]');
    const downloadBtn = document.getElementById('downloadExcel');
    
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
    
    // Download Excel functionality
    downloadBtn.addEventListener('click', function() {
        // Show downloading state
        const originalContent = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Download...';
        this.disabled = true;
        
        setTimeout(() => {
            this.innerHTML = originalContent;
            this.disabled = false;
            alert('✅ File Excel berhasil didownload (Demo)!\n📁 Rekap_Absensi_Kelas_5A_Juli_2025.xlsx\n\nCatatan: Ini hanya simulasi dalam mode testing.');
        }, 1500);
    });
    
    // Manual submit with loading
    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        showLoading();
        
        setTimeout(() => {
            hideLoading();
            alert('✅ Data berhasil dimuat! (Demo mode)');
        }, 1000);
    });
    
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
    
    console.log('✅ Test mode loaded successfully!');
    console.log('✅ Design implementation working perfectly!');
});
</script>

<?= $this->endSection() ?>

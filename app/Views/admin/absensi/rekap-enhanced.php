<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<style>
/* Enhanced styles for rekap view with better performance and mobile optimization */
.excel-style-table {
    border-collapse: collapse;
    width: 100%;
    font-size: 12px;
    font-family: Arial, sans-serif;
    background-color: white;
    margin: 0;
    overflow-x: auto;
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
    position: sticky;
    top: 0;
    z-index: 10;
}

.excel-style-table .student-name {
    text-align: left;
    max-width: 150px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    padding-left: 8px;
    position: sticky;
    left: 30px;
    background-color: white;
    z-index: 5;
}

.excel-style-table .no-col {
    position: sticky;
    left: 0;
    background-color: white;
    z-index: 5;
    width: 30px;
    text-align: center;
}

.excel-style-table .day-header {
    width: 25px;
    font-size: 10px;
    background-color: #4472C4;
    font-weight: bold;
    color: white;
}

.excel-style-table .day-header.weekend {
    background: linear-gradient(135deg, #E74C3C, #ec6352);
    color: white;
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

.excel-style-table .summary-cell {
    background-color: #f8f9fa;
    font-weight: bold;
    border-left: 2px solid #4472C4;
}

.excel-style-table .summary-row {
    background-color: #e9ecef;
    font-weight: bold;
    font-size: 13px;
}

.excel-style-table .summary-row td {
    background-color: #e9ecef;
    border-top: 2px solid #000;
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
    color: #c62828;
    background-color: #ffebee;
}

/* Enhanced Filter Section */
.filter-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px;
    border-radius: 15px;
    margin-bottom: 25px;
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.18);
}

.filter-section h4 {
    color: white;
    margin-bottom: 20px;
    font-weight: 600;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.filter-section .form-control,
.filter-section .form-select {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    padding: 10px 15px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.filter-section .form-control:focus,
.filter-section .form-select:focus {
    background: white;
    border-color: #4472C4;
    box-shadow: 0 0 0 0.2rem rgba(68, 114, 196, 0.25);
    transform: translateY(-1px);
}

/* Enhanced Button Styles */
.btn-excel {
    background: linear-gradient(45deg, #1e7e34, #28a745);
    border: none;
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.btn-excel:hover:not(:disabled) {
    background: linear-gradient(45deg, #155724, #1e7e34);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
}

.btn-excel:disabled {
    background: #6c757d;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.btn-excel i {
    margin-right: 8px;
}

/* Header Styles */
.header-section {
    background: linear-gradient(135deg, #4472C4, #5a67d8);
    color: white;
    padding: 25px;
    border-radius: 15px;
    margin-bottom: 25px;
    text-align: center;
    box-shadow: 0 8px 32px rgba(68, 114, 196, 0.3);
}

.header-section h2 {
    margin-bottom: 10px;
    font-weight: bold;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.header-section .school-info {
    font-size: 18px;
    margin-bottom: 10px;
    font-weight: 600;
}

.header-section .class-info {
    font-size: 16px;
    opacity: 0.9;
}

/* Loading and Status */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.loading-spinner {
    width: 50px;
    height: 50px;
    border: 5px solid #f3f3f3;
    border-top: 5px solid #4472C4;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.status-indicator {
    padding: 10px 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 600;
}

.status-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.status-info {
    background-color: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}

/* Mobile Optimization */
@media (max-width: 768px) {
    .excel-style-table {
        font-size: 10px;
    }
    
    .excel-style-table .day-header,
    .excel-style-table .day-cell {
        width: 20px;
        padding: 2px;
        font-size: 9px;
    }
    
    .excel-style-table .student-name {
        max-width: 100px;
        font-size: 10px;
    }
    
    .filter-section {
        padding: 15px;
    }
    
    .btn-excel {
        padding: 10px 20px;
        font-size: 12px;
    }
    
    .header-section {
        padding: 20px 15px;
    }
    
    .header-section h2 {
        font-size: 1.5rem;
    }
}

/* Print Styles */
@media print {
    .filter-section,
    .btn-excel,
    .loading-overlay {
        display: none !important;
    }
    
    .excel-style-table {
        font-size: 10px;
    }
    
    .header-section {
        background: white !important;
        color: black !important;
        box-shadow: none !important;
        border: 1px solid #000;
    }
    
    body {
        margin: 0;
    }
    
    .table-container {
        overflow: visible !important;
    }
}

/* Accessibility */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

/* Enhanced hover effects */
.excel-style-table tbody tr:hover {
    background-color: #f8f9fa;
}

.excel-style-table tbody tr:hover .student-name {
    background-color: #f8f9fa;
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

    <!-- Status Indicator -->
    <div class="status-indicator status-success" id="statusIndicator">
        <i class="fas fa-check-circle"></i>
        Sistem siap digunakan dalam mode demo/test
    </div>

    <!-- Header Section -->
    <div class="header-section">
        <h2>📊 REKAP ABSENSI SISWA</h2>
        <div class="school-info">SDN GROGOL UTARA 09</div>
        <div class="class-info">
            KELAS: <?= strtoupper($kelas) ?> | 
            BULAN: <?= strtoupper($bulan_nama) ?> <?= $tahun ?> |
            TAHUN PELAJARAN: <?= $tahun ?>/<?= $tahun + 1 ?>
        </div>
    </div>

    <!-- Enhanced Filter Section -->
    <div class="filter-section">
        <h4><i class="fas fa-filter"></i> Filter Data Absensi</h4>
        <form method="GET" id="filterForm" class="row align-items-end">
            <div class="col-md-3 mb-3">
                <label class="form-label text-white">Kelas:</label>
                <select name="kelas" class="form-select" id="filterKelas" onchange="autoSubmitFilter()">
                    <?php foreach ($kelasOptions as $k): ?>
                        <option value="<?= $k ?>" <?= $k === $kelas ? 'selected' : '' ?>><?= $k ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label text-white">Bulan:</label>
                <select name="bulan" class="form-select" id="filterBulan" onchange="autoSubmitFilter()">
                    <?php foreach ($months as $num => $name): ?>
                        <option value="<?= $num ?>" <?= $num == $bulan ? 'selected' : '' ?>><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 mb-3">
                <label class="form-label text-white">Tahun:</label>
                <select name="tahun" class="form-select" id="filterTahun" onchange="autoSubmitFilter()">
                    <?php for ($y = 2020; $y <= 2030; $y++): ?>
                        <option value="<?= $y ?>" <?= $y == $tahun ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2 mb-3">
                <button type="submit" class="btn btn-light w-100">
                    <i class="fas fa-search"></i> Filter
                </button>
            </div>
            <div class="col-md-2 mb-3">
                <button type="button" class="btn btn-excel w-100" id="btnDownloadExcel" onclick="downloadExcel()">
                    <i class="fas fa-file-excel"></i> Excel
                </button>
            </div>
        </form>
    </div>

    <!-- Table Container with horizontal scroll -->
    <div class="table-container" style="overflow-x: auto; background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <table class="excel-style-table" role="table" aria-label="Rekap Absensi Siswa">
            <caption class="sr-only">
                Tabel rekap absensi untuk kelas <?= $kelas ?> bulan <?= $bulan_nama ?> tahun <?= $tahun ?>
            </caption>
            
            <thead>
                <tr role="row">
                    <th rowspan="1" class="no-col" scope="col" aria-label="Nomor">NO</th>
                    <th rowspan="1" class="student-name" scope="col" aria-label="Nama Siswa">NAMA SISWA</th>
                    
                    <?php 
                    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
                    for ($day = 1; $day <= $daysInMonth; $day++): 
                        $dayOfWeek = date('w', mktime(0, 0, 0, $bulan, $day, $tahun));
                        $isWeekend = ($dayOfWeek == 0 || $dayOfWeek == 6);
                    ?>
                        <th class="day-header <?= $isWeekend ? 'weekend' : '' ?>" scope="col" aria-label="Tanggal <?= $day ?>">
                            <?= $day ?>
                        </th>
                    <?php endfor; ?>
                    
                    <th class="summary-cell" scope="col" aria-label="Sakit">S</th>
                    <th class="summary-cell" scope="col" aria-label="Izin">I</th>
                    <th class="summary-cell" scope="col" aria-label="Alpha">A</th>
                </tr>
            </thead>
            
            <tbody>
                <?php $no = 1; ?>
                <?php foreach ($attendanceData['students'] as $student): ?>
                    <tr>
                        <td class="no-col"><?= $no ?></td>
                        <td class="student-name"><?= esc($student['nama']) ?></td>
                        
                        <?php for ($day = 1; $day <= $daysInMonth; $day++): 
                            $dayStr = str_pad($day, 2, '0', STR_PAD_LEFT);
                            $attendance = $student['daily'][$dayStr] ?? '';
                            $dayOfWeek = date('w', mktime(0, 0, 0, $bulan, $day, $tahun));
                            $isWeekend = ($dayOfWeek == 0 || $dayOfWeek == 6);
                            
                            $mark = '';
                            $class = '';
                            if ($attendance === 'hadir') {
                                $mark = '✓';
                                $class = 'attendance-mark hadir';
                            } elseif ($attendance === 'sakit') {
                                $mark = 'S';
                                $class = 'attendance-mark sakit';
                            } elseif ($attendance === 'izin') {
                                $mark = 'I';
                                $class = 'attendance-mark izin';
                            } elseif ($attendance === 'alpha') {
                                $mark = 'A';
                                $class = 'attendance-mark alpha';
                            }
                        ?>
                            <td class="day-cell <?= $isWeekend ? 'weekend' : '' ?>">
                                <span class="<?= $class ?>"><?= $mark ?></span>
                            </td>
                        <?php endfor; ?>
                        
                        <td class="summary-cell"><?= $student['summary']['sakit'] ?></td>
                        <td class="summary-cell"><?= $student['summary']['izin'] ?></td>
                        <td class="summary-cell"><?= $student['summary']['alpha'] ?></td>
                    </tr>
                    <?php $no++; ?>
                <?php endforeach; ?>
                
                <!-- Summary Row: Total -->
                <tr class="summary-row">
                    <td colspan="2" style="text-align: center; font-weight: bold;">TOTAL</td>
                    <?php for ($day = 1; $day <= $daysInMonth; $day++): ?>
                        <td></td>
                    <?php endfor; ?>
                    <td style="font-weight: bold;">
                        <?= array_sum(array_map(function($s) { return $s['summary']['sakit']; }, $attendanceData['students'])) ?>
                    </td>
                    <td style="font-weight: bold;">
                        <?= array_sum(array_map(function($s) { return $s['summary']['izin']; }, $attendanceData['students'])) ?>
                    </td>
                    <td style="font-weight: bold;">
                        <?= array_sum(array_map(function($s) { return $s['summary']['alpha']; }, $attendanceData['students'])) ?>
                    </td>
                </tr>
                
                <!-- Summary Row: Percentage -->
                <tr class="summary-row">
                    <td colspan="2" style="text-align: center; font-weight: bold;">PERSENTASE KEHADIRAN</td>
                    <?php for ($day = 1; $day <= $daysInMonth; $day++): ?>
                        <td></td>
                    <?php endfor; ?>
                    <td colspan="3" style="font-weight: bold; text-align: center;">
                        <?= number_format(array_sum(array_map(function($s) { return $s['percentage']; }, $attendanceData['students'])) / count($attendanceData['students']), 1) ?>%
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
let isLoading = false;

function showLoading() {
    isLoading = true;
    document.getElementById('loadingOverlay').style.display = 'flex';
    const btn = document.getElementById('btnDownloadExcel');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
}

function hideLoading() {
    isLoading = false;
    document.getElementById('loadingOverlay').style.display = 'none';
    const btn = document.getElementById('btnDownloadExcel');
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-file-excel"></i> Excel';
}

function autoSubmitFilter() {
    if (!isLoading) {
        showLoading();
        setTimeout(() => {
            document.getElementById('filterForm').submit();
        }, 500);
    }
}

function downloadExcel() {
    if (isLoading) return;
    
    showLoading();
    
    // Real Excel export
    const kelas = document.getElementById('filterKelas').value;
    const bulan = document.getElementById('filterBulan').value;
    const tahun = document.getElementById('filterTahun').value;
    
    const downloadUrl = `<?= base_url('admin/absensi-enhanced/export-excel') ?>?kelas=${kelas}&bulan=${bulan}&tahun=${tahun}`;
    
    // Create invisible link and trigger download
    const link = document.createElement('a');
    link.href = downloadUrl;
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Show success message
    setTimeout(() => {
        hideLoading();
        showStatusMessage('File Excel berhasil didownload!', 'success');
    }, 2000);
}

function showStatusMessage(message, type = 'info') {
    const indicator = document.getElementById('statusIndicator');
    indicator.className = `status-indicator status-${type}`;
    indicator.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i> ${message}`;
    
    setTimeout(() => {
        indicator.className = 'status-indicator status-info';
        indicator.innerHTML = '<i class="fas fa-info-circle"></i> Sistem siap digunakan dalam mode demo/test';
    }, 3000);
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    hideLoading();
    
    // Add keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            downloadExcel();
        }
    });
    
    console.log('✅ Rekap Absensi Enhanced loaded successfully');
    console.log('📊 Data siswa:', <?= count($attendanceData['students']) ?>);
    console.log('🎯 Real Excel export ready!');
});
</script>

<?= $this->endSection() ?>

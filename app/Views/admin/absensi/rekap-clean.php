<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<!-- Link to external CSS -->
<link rel="stylesheet" href="<?= base_url('css/rekap-absensi-clean.css') ?>">

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
        Sistem siap digunakan dalam mode demo/test - Enhanced Version
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

    <!-- Table Container -->
    <div class="table-container">
        <table class="excel-style-table" role="table" aria-label="Rekap Absensi Siswa">
            <caption class="sr-only">
                Tabel rekap absensi untuk kelas <?= $kelas ?> bulan <?= $bulan_nama ?> tahun <?= $tahun ?>
            </caption>
            
            <thead>
                <tr role="row">
                    <th class="no-col" scope="col" aria-label="Nomor">NO</th>
                    <th class="student-name" scope="col" aria-label="Nama Siswa">NAMA SISWA</th>
                    
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
        indicator.innerHTML = '<i class="fas fa-info-circle"></i> Sistem siap digunakan dalam mode demo/test - Enhanced Version';
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

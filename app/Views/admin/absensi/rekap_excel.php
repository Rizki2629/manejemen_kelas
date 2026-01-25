<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<style>
.excel-style-table {
    border-collapse: collapse;
    width: 100%;
    font-size: 12px;
    font-family: Arial, sans-serif;
    background-color: white;
}

.excel-style-table th,
.excel-style-table td {
    border: 1px solid #ccc;
    padding: 4px 6px;
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
}

.excel-style-table th {
    background-color: #f8f9fa;
    font-weight: bold;
    color: #333;
}

.excel-style-table .student-name {
    text-align: left;
    max-width: 150px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.excel-style-table .day-header {
    writing-mode: vertical-rl;
    text-orientation: mixed;
    width: 25px;
    font-size: 10px;
    background-color: #e9ecef;
}

.excel-style-table .summary-header {
    background-color: #d4edda;
    font-weight: bold;
}

.excel-style-table .total-header {
    background-color: #fff3cd;
    font-weight: bold;
}

.attendance-mark {
    font-weight: bold;
    font-size: 11px;
}

.attendance-mark.hadir {
    color: #28a745;
}

.attendance-mark.izin {
    color: #ffc107;
}

.attendance-mark.sakit {
    color: #dc3545;
}

.attendance-mark.alpha {
    color: #6c757d;
}

.summary-number {
    font-weight: bold;
    background-color: #f8f9fa;
}

.percentage-cell {
    font-weight: bold;
    color: #007bff;
}

.card-rekap {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    border: none;
    border-radius: 0.35rem;
}

.table-responsive {
    max-height: 70vh;
    overflow-x: auto;
    overflow-y: auto;
}

.filter-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
}
</style>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-chart-bar"></i> <?= $title ?>
    </h1>
    <div>
        <?php if (!empty($attendanceData) && !empty($attendanceData['students'])): ?>
        <a href="<?= base_url('admin/absensi/export') ?>?<?= http_build_query(['kelas' => $filterKelas, 'start_date' => $filterBulan . '-01', 'end_date' => date('Y-m-t', strtotime($filterBulan . '-01'))]) ?>" 
           class="btn btn-success btn-sm">
            <i class="fas fa-download"></i> Export CSV
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- Filters Section -->
<div class="filter-section">
    <form id="filterForm" method="GET">
        <div class="row align-items-end">
            <?php if ($userRole === 'admin'): ?>
            <div class="col-md-4">
                <label for="kelas" class="form-label fw-bold">Kelas:</label>
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
            <div class="col-md-4">
                <label for="kelas" class="form-label fw-bold">Kelas:</label>
                <input type="text" class="form-control" value="Kelas <?= $userKelas ?>" readonly>
                <input type="hidden" name="kelas" value="<?= $userKelas ?>">
            </div>
            <?php endif; ?>
            <div class="col-md-4">
                <label for="bulan" class="form-label fw-bold">Bulan:</label>
                <input type="month" class="form-control" id="bulan" name="bulan" 
                       value="<?= $filterBulan ?>" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Tampilkan
                </button>
            </div>
        </div>
    </form>
</div>

<?php if (!empty($attendanceData) && !empty($attendanceData['students'])): ?>
<!-- Rekap Table Card -->
<div class="card card-rekap">
    <div class="card-header py-3 bg-primary text-white">
        <h6 class="m-0 font-weight-bold">
            <i class="fas fa-table"></i> 
            Rekap Absensi Kelas <?= $attendanceData['kelas'] ?> - 
            <?= date('F Y', mktime(0, 0, 0, $attendanceData['month'], 1, $attendanceData['year'])) ?>
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="excel-style-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 40px;">No</th>
                        <th rowspan="2" class="student-name">Nama Siswa</th>
                        
                        <!-- Daily columns -->
                        <?php foreach ($attendanceData['days'] as $day): ?>
                        <th class="day-header"><?= $day ?></th>
                        <?php endforeach; ?>
                        
                        <!-- Summary columns -->
                        <th class="summary-header">S</th>
                        <th class="summary-header">I</th>
                        <th class="summary-header">A</th>
                        
                        <!-- Total and percentage -->
                        <th class="total-header">Total</th>
                        <th class="total-header">%</th>
                    </tr>
                    <tr>
                        <!-- Day numbers -->
                        <?php foreach ($attendanceData['days'] as $day): ?>
                        <th class="day-header" style="font-size: 9px;"><?= $day ?></th>
                        <?php endforeach; ?>
                        
                        <!-- Summary headers -->
                        <th class="summary-header" style="font-size: 10px;">Sakit</th>
                        <th class="summary-header" style="font-size: 10px;">Izin</th>
                        <th class="summary-header" style="font-size: 10px;">Alpha</th>
                        
                        <!-- Total headers -->
                        <th class="total-header" style="font-size: 10px;">Hadir</th>
                        <th class="total-header" style="font-size: 10px;">Kehadiran</th>
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
                        <td>
                            <?php 
                            $dayStr = str_pad($day, 2, '0', STR_PAD_LEFT);
                            $status = $student['daily'][$dayStr] ?? '';
                            $mark = '';
                            $class = '';
                            
                            switch ($status) {
                                case 'hadir':
                                    $mark = '✓';
                                    $class = 'hadir';
                                    break;
                                case 'izin':
                                    $mark = 'I';
                                    $class = 'izin';
                                    break;
                                case 'sakit':
                                    $mark = 'S';
                                    $class = 'sakit';
                                    break;
                                case 'alpha':
                                    $mark = 'A';
                                    $class = 'alpha';
                                    break;
                                default:
                                    $mark = '-';
                                    $class = '';
                            }
                            ?>
                            <span class="attendance-mark <?= $class ?>"><?= $mark ?></span>
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
                Tidak ada data absensi untuk kelas <?= $filterKelas ?> pada bulan <?= date('F Y', strtotime($filterBulan . '-01')) ?>.
            <?php endif; ?>
        </p>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto submit form when filters change
    const filterForm = document.getElementById('filterForm');
    const kelasSelect = document.getElementById('kelas');
    const bulanInput = document.getElementById('bulan');
    
    if (kelasSelect) {
        kelasSelect.addEventListener('change', function() {
            if (this.value && bulanInput.value) {
                filterForm.submit();
            }
        });
    }
    
    if (bulanInput) {
        bulanInput.addEventListener('change', function() {
            if (this.value && (kelasSelect?.value || '<?= $userKelas ?>')) {
                filterForm.submit();
            }
        });
    }
});
</script>

<?= $this->endSection() ?>

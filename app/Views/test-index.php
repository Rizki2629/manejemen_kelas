<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Rekap Absensi - Demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <div class="text-center mb-4">
            <h1 class="display-4 text-primary mb-3">
                <i class="fas fa-chart-bar me-3"></i>Test Rekap Absensi
            </h1>
            <p class="lead text-muted">Demo implementasi desain yang sudah disempurnakan</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h4 class="mb-0">
                            <i class="fas fa-database me-2"></i>Status Database & Testing
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-warning mb-4">
                            <h5 class="alert-heading">
                                <i class="fas fa-exclamation-triangle me-2"></i>Database Issue Detected
                            </h5>
                            <p class="mb-2">MySQL service tidak berjalan atau belum dikonfigurasi dengan benar.</p>
                            <p class="mb-0">Silakan gunakan link testing di bawah untuk melihat preview desain.</p>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card border-success h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-vial fa-3x text-success mb-3"></i>
                                        <h5 class="card-title">Testing Mode</h5>
                                        <p class="card-text">Preview desain dengan sample data</p>
                                        <a href="<?= base_url('admin/absensi/rekap-test') ?>" 
                                           class="btn btn-success btn-lg">
                                            <i class="fas fa-play me-2"></i>Test Preview
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-info h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-file-code fa-3x text-info mb-3"></i>
                                        <h5 class="card-title">Static HTML</h5>
                                        <p class="card-text">View file HTML yang sudah dibuat</p>
                                        <a href="<?= base_url('test_rekap_enhanced.html') ?>" 
                                           class="btn btn-info btn-lg">
                                            <i class="fas fa-eye me-2"></i>View HTML
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="fas fa-lightbulb me-2"></i>Solusi Database Issue:
                            </h6>
                            <ol class="mb-0">
                                <li>Start XAMPP Control Panel</li>
                                <li>Start Apache dan MySQL services</li>
                                <li>Atau gunakan link "Test Preview" untuk melihat hasil implementasi</li>
                            </ol>
                        </div>

                        <div class="text-center mt-4">
                            <h6 class="text-muted">Implementasi Status</h6>
                            <div class="row g-2 mt-2">
                                <div class="col">
                                    <span class="badge bg-success fs-6 py-2 px-3">
                                        <i class="fas fa-check me-1"></i>UI/UX ✓
                                    </span>
                                </div>
                                <div class="col">
                                    <span class="badge bg-success fs-6 py-2 px-3">
                                        <i class="fas fa-check me-1"></i>Excel Style ✓
                                    </span>
                                </div>
                                <div class="col">
                                    <span class="badge bg-success fs-6 py-2 px-3">
                                        <i class="fas fa-check me-1"></i>Responsive ✓
                                    </span>
                                </div>
                                <div class="col">
                                    <span class="badge bg-success fs-6 py-2 px-3">
                                        <i class="fas fa-check me-1"></i>Integration ✓
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

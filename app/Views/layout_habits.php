<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>7 Kebiasaan Anak Indonesia Hebat</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <style>
    body { background-color: #f8fafc; }
    .sidebar { min-height: 100vh; border-right: 1px solid #e2e8f0; background: #ffffff; }
    .sidebar .nav-link { border-radius: .5rem; }
    .sidebar .nav-link:hover { background: #f1f5f9; }
    .sidebar .nav-link.active { background: #ede9fe; color: #6d28d9; }
    .card-habit svg { width: 28px; height: 28px; }
  .card-habit .card-body { display: flex; flex-direction: column; }
    .card-habit { min-height: 200px; }
    @media (min-width: 992px) { /* lg and up */
      .card-habit { min-height: 220px; }
    }
  </style>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <nav class="col-12 col-md-3 col-lg-2 sidebar p-3">
        <h5 class="fw-semibold mb-3">Menu</h5>
        <ul class="nav flex-column gap-1">
          <li class="nav-item"><a class="nav-link active" href="/siswa">Siswa - Input Harian</a></li>
          <li class="nav-item"><a class="nav-link" href="/guru/dashboard">Guru - Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="/guru/logs">Guru - Data</a></li>
        </ul>
      </nav>
      <main class="col-12 col-md-9 col-lg-10 p-3 p-md-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h1 class="h5 h-md4 fw-semibold m-0">7 Kebiasaan Anak Indonesia Hebat</h1>
        </div>
        <?= $this->renderSection('content') ?>
      </main>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>

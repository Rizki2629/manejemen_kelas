<?= $this->extend('admin/layout'); ?>

<?= $this->section('content'); ?>
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800"><?= $title ?></h1>
                <p class="text-gray-600 mt-1">Detail nilai siswa mata pelajaran <?= $mataPelajaranList[$selectedMapel] ?></p>
            </div>
            <a href="<?= base_url('/admin/nilai') ?>?kelas=<?= $student['kelas'] ?>&mapel=<?= $selectedMapel ?>" 
               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Student Info -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <h3 class="text-lg font-medium text-gray-800 mb-2">Informasi Siswa</h3>
                <div class="space-y-2">
                    <p><span class="font-medium">Nama:</span> <?= $student['nama'] ?></p>
                    <p><span class="font-medium">NIPD:</span> <?= $student['nipd'] ?></p>
                    <p><span class="font-medium">Kelas:</span> <?= $student['kelas'] ?></p>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-medium text-gray-800 mb-2">Mata Pelajaran</h3>
                <div class="space-y-2">
                    <p><span class="font-medium">Mapel:</span> <?= $mataPelajaranList[$selectedMapel] ?></p>
                    <p><span class="font-medium">Kode:</span> <?= $selectedMapel ?></p>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-medium text-gray-800 mb-2">Ringkasan Nilai</h3>
                <div class="space-y-2">
                    <p><span class="font-medium">Harian:</span> 
                        <?= isset($nilaiDetail['rekap']['nilai_harian']) ? number_format($nilaiDetail['rekap']['nilai_harian'], 1) : '-' ?>
                    </p>
                    <p><span class="font-medium">PTS:</span> 
                        <?= isset($nilaiDetail['rekap']['nilai_pts']) ? number_format($nilaiDetail['rekap']['nilai_pts'], 1) : '-' ?>
                    </p>
                    <p><span class="font-medium">PAS:</span> 
                        <?= isset($nilaiDetail['rekap']['nilai_pas']) ? number_format($nilaiDetail['rekap']['nilai_pas'], 1) : '-' ?>
                    </p>
                    <p><span class="font-medium text-blue-600">Nilai Akhir:</span> 
                        <span class="font-bold text-blue-600">
                            <?= isset($nilaiDetail['rekap']['nilai_akhir']) ? number_format($nilaiDetail['rekap']['nilai_akhir'], 1) : '-' ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Nilai Harian -->
    <?php if (!empty($nilaiDetail['harian'])): ?>
    <div class="bg-white rounded-lg shadow-sm mb-6">
        <div class="px-6 py-4 bg-green-50 border-b">
            <h2 class="text-lg font-medium text-gray-800 flex items-center">
                <i class="fas fa-calendar-day mr-2 text-green-600"></i>
                Nilai Harian (<?= count($nilaiDetail['harian']) ?> nilai)
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TP/Materi</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $no = 1; ?>
                    <?php foreach ($nilaiDetail['harian'] as $nilai): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $no++ ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?= date('d/m/Y', strtotime($nilai['tanggal'])) ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <?= $nilai['tp_materi'] ?: '-' ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900">
                            <?= number_format($nilai['nilai'], 1) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center space-x-2">
                                <a href="<?= base_url('/admin/nilai/edit/' . $nilai['id']) ?>" 
                                   class="text-blue-600 hover:text-blue-900" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="<?= base_url('/admin/nilai/delete/' . $nilai['id']) ?>" 
                                      class="inline" onsubmit="return confirm('Yakin ingin menghapus nilai ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Nilai PTS -->
    <?php if (!empty($nilaiDetail['pts'])): ?>
    <div class="bg-white rounded-lg shadow-sm mb-6">
        <div class="px-6 py-4 bg-blue-50 border-b">
            <h2 class="text-lg font-medium text-gray-800 flex items-center">
                <i class="fas fa-file-alt mr-2 text-blue-600"></i>
                Nilai PTS (<?= count($nilaiDetail['pts']) ?> nilai)
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TP/Materi</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $no = 1; ?>
                    <?php foreach ($nilaiDetail['pts'] as $nilai): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $no++ ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?= date('d/m/Y', strtotime($nilai['tanggal'])) ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <?= $nilai['tp_materi'] ?: '-' ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900">
                            <?= number_format($nilai['nilai'], 1) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center space-x-2">
                                <a href="<?= base_url('/admin/nilai/edit/' . $nilai['id']) ?>" 
                                   class="text-blue-600 hover:text-blue-900" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="<?= base_url('/admin/nilai/delete/' . $nilai['id']) ?>" 
                                      class="inline" onsubmit="return confirm('Yakin ingin menghapus nilai ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Nilai PAS -->
    <?php if (!empty($nilaiDetail['pas'])): ?>
    <div class="bg-white rounded-lg shadow-sm mb-6">
        <div class="px-6 py-4 bg-purple-50 border-b">
            <h2 class="text-lg font-medium text-gray-800 flex items-center">
                <i class="fas fa-graduation-cap mr-2 text-purple-600"></i>
                Nilai PAS (<?= count($nilaiDetail['pas']) ?> nilai)
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TP/Materi</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $no = 1; ?>
                    <?php foreach ($nilaiDetail['pas'] as $nilai): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $no++ ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?= date('d/m/Y', strtotime($nilai['tanggal'])) ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <?= $nilai['tp_materi'] ?: '-' ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900">
                            <?= number_format($nilai['nilai'], 1) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center space-x-2">
                                <a href="<?= base_url('/admin/nilai/edit/' . $nilai['id']) ?>" 
                                   class="text-blue-600 hover:text-blue-900" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="<?= base_url('/admin/nilai/delete/' . $nilai['id']) ?>" 
                                      class="inline" onsubmit="return confirm('Yakin ingin menghapus nilai ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- No Data Message -->
    <?php if (empty($nilaiDetail['harian']) && empty($nilaiDetail['pts']) && empty($nilaiDetail['pas'])): ?>
    <div class="bg-white rounded-lg shadow-sm p-8 text-center">
        <i class="fas fa-chart-line text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-medium text-gray-800 mb-2">Belum Ada Nilai</h3>
        <p class="text-gray-600 mb-4">Siswa ini belum memiliki nilai untuk mata pelajaran <?= $mataPelajaranList[$selectedMapel] ?></p>
        <a href="<?= base_url('/admin/nilai/create') ?>?kelas=<?= $student['kelas'] ?>&mapel=<?= $selectedMapel ?>&siswa_id=<?= $student['id'] ?>" 
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Tambah Nilai
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('success')): ?>
<div class="fixed bottom-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-md z-50" id="success-message">
    <div class="flex items-center">
        <i class="fas fa-check-circle mr-2"></i>
        <span><?= session()->getFlashdata('success') ?></span>
        <button onclick="document.getElementById('success-message').style.display='none'" class="ml-2 text-green-500 hover:text-green-700">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
<div class="fixed bottom-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-md z-50" id="error-message">
    <div class="flex items-center">
        <i class="fas fa-exclamation-circle mr-2"></i>
        <span><?= session()->getFlashdata('error') ?></span>
        <button onclick="document.getElementById('error-message').style.display='none'" class="ml-2 text-red-500 hover:text-red-700">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
<?php endif; ?>

<script>
// Auto hide flash messages after 5 seconds
setTimeout(() => {
    const successMsg = document.getElementById('success-message');
    const errorMsg = document.getElementById('error-message');
    if (successMsg) successMsg.style.display = 'none';
    if (errorMsg) errorMsg.style.display = 'none';
}, 5000);
</script>
<?= $this->endSection(); ?>

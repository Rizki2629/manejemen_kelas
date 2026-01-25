    <?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>
Buku Kasus Siswa
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="w-full px-6 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-book text-white text-xl"></i>
                    </div>
                    Buku Kasus Siswa
                </h1>
                <p class="mt-2 text-sm text-gray-600">Kelola dan pantau catatan kasus siswa secara profesional</p>
            </div>
            <a href="<?= base_url('buku-kasus/tambah') ?>" 
               class="inline-flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                <i class="fas fa-plus"></i>
                <span>Tambah Kasus</span>
            </a>
        </div>
        <nav class="mt-4 flex items-center space-x-2 text-sm">
            <a href="<?= base_url('admin') ?>" class="text-indigo-600 hover:text-indigo-800 font-medium">Dashboard</a>
            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
            <span class="text-gray-700 font-medium">Buku Kasus</span>
        </nav>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="mb-6 rounded-xl bg-gradient-to-r from-emerald-50 to-teal-50 border-l-4 border-emerald-500 p-4 shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-emerald-800"><?= session()->getFlashdata('success') ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="mb-6 rounded-xl bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 p-4 shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800"><?= session()->getFlashdata('error') ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Siswa</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Deskripsi Kasus</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Pelapor</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <?php if (!empty($kasusList)): ?>
                        <?php $no = 1; ?>
                        <?php foreach ($kasusList as $kasus): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center justify-center w-8 h-8 bg-gray-100 rounded-lg text-sm font-semibold text-gray-700">
                                        <?= $no++ ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-calendar text-gray-400 text-xs"></i>
                                        <span class="text-sm text-gray-900 font-medium"><?= date('d M Y', strtotime($kasus['tanggal_kejadian'])) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900"><?= esc($kasus['nama_siswa']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                        <i class="fas fa-graduation-cap"></i>
                                        <?= esc($kasus['kelas']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-md">
                                        <?= esc(strlen($kasus['deskripsi_kasus']) > 100 ? substr($kasus['deskripsi_kasus'], 0, 100) . '...' : $kasus['deskripsi_kasus']) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-user text-gray-400 text-xs"></i>
                                        <span class="text-sm text-gray-700"><?= esc($kasus['nama_guru']) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <a href="<?= base_url('buku-kasus/detail/' . $kasus['id']) ?>" 
                                           class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow-sm hover:shadow-md transition-all transform hover:scale-105" 
                                           title="Detail">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                        <a href="<?= base_url('buku-kasus/edit/' . $kasus['id']) ?>" 
                                           class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-amber-500 text-white hover:bg-amber-600 shadow-sm hover:shadow-md transition-all transform hover:scale-105" 
                                           title="Edit">
                                            <i class="fas fa-edit text-sm"></i>
                                        </a>
                                        <a href="<?= base_url('buku-kasus/cetak/' . $kasus['id']) ?>" 
                                           class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm hover:shadow-md transition-all transform hover:scale-105" 
                                           title="Cetak PDF" target="_blank">
                                            <i class="fas fa-print text-sm"></i>
                                        </a>
                                        <button type="button" 
                                                class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-rose-600 text-white hover:bg-rose-700 shadow-sm hover:shadow-md transition-all transform hover:scale-105"
                                                onclick="confirmDelete(<?= $kasus['id'] ?>)" 
                                                title="Hapus">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-inbox text-gray-400 text-3xl"></i>
                                    </div>
                                    <p class="text-gray-500 text-base font-medium">Tidak ada data kasus</p>
                                    <p class="text-gray-400 text-sm mt-1">Mulai tambahkan kasus baru untuk memulai</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus kasus ini? Tindakan ini tidak dapat dibatalkan.')) {
        window.location.href = '<?= base_url('buku-kasus/hapus/') ?>' + id;
    }
}
</script>
<?= $this->endSection() ?>

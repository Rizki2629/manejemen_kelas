<?= $this->extend('admin/layout'); ?>
<?= $this->section('content'); ?>

<div class="px-4 py-6">
    <div class="max-w-7xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-red-500 to-pink-600 text-white px-6 py-4 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold flex items-center gap-2">
                        <i class="fas fa-search"></i> Cek Duplikasi Data Guru
                    </h2>
                    <p class="text-sm opacity-75">Temukan dan bersihkan data guru yang duplikat</p>
                </div>
                <div class="flex gap-2">
                    <a href="<?= base_url('admin/guru'); ?>" class="bg-white text-red-600 hover:bg-red-100 font-semibold px-4 py-2 rounded shadow transition-colors duration-200">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                    <?php if (!empty($duplicatesByName) || !empty($duplicatesByNip) || !empty($duplicatesByNuptk)): ?>
                    <form method="post" action="<?= base_url('admin/guru/clean-duplicates'); ?>" class="inline">
                        <?= csrf_field(); ?>
                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin membersihkan semua data duplikat? Data yang lebih baru akan dihapus dan data yang lebih lama akan dipertahankan.')" 
                                class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded shadow font-semibold transition-colors duration-200">
                            <i class="fas fa-broom me-1"></i>Bersihkan Duplikasi
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>

            <div class="px-6 py-4">
                <?php if (session()->getFlashdata('pesan')): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
                        <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('pesan'); ?>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
                        <i class="fas fa-exclamation-circle me-2"></i> <?= session()->getFlashdata('error'); ?>
                    </div>
                <?php endif; ?>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-gradient-to-br from-red-50 to-red-100 border border-red-200 rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-user text-white text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-red-800">Duplikasi Nama</h3>
                                <p class="text-2xl font-bold text-red-600"><?= count($duplicatesByName) ?></p>
                                <p class="text-sm text-red-600">nama yang duplikat</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-orange-50 to-orange-100 border border-orange-200 rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-id-card text-white text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-orange-800">Duplikasi NIP</h3>
                                <p class="text-2xl font-bold text-orange-600"><?= count($duplicatesByNip) ?></p>
                                <p class="text-sm text-orange-600">NIP yang duplikat</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 border border-yellow-200 rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-certificate text-white text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-yellow-800">Duplikasi NUPTK</h3>
                                <p class="text-2xl font-bold text-yellow-600"><?= count($duplicatesByNuptk) ?></p>
                                <p class="text-sm text-yellow-600">NUPTK yang duplikat</p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (empty($duplicatesByName) && empty($duplicatesByNip) && empty($duplicatesByNuptk)): ?>
                <!-- No Duplicates Found -->
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check-circle text-4xl text-green-500"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak Ada Duplikasi Ditemukan</h3>
                    <p class="text-gray-600 mb-6">Data guru Anda sudah bersih dari duplikasi berdasarkan nama, NIP, dan NUPTK.</p>
                    <a href="<?= base_url('admin/guru'); ?>" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Data Guru
                    </a>
                </div>
                <?php else: ?>

                <!-- Duplicates by Name -->
                <?php if (!empty($duplicatesByName)): ?>
                <div class="mb-8">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <h3 class="text-lg font-semibold text-red-800 mb-2">
                            <i class="fas fa-user mr-2"></i>
                            Duplikasi Berdasarkan Nama (<?= count($duplicatesByName) ?> nama)
                        </h3>
                        <p class="text-sm text-red-600">Ditemukan nama guru yang sama dalam database</p>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                            <thead class="bg-red-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-red-800">Nama</th>
                                    <th class="px-4 py-3 text-center text-sm font-semibold text-red-800">Jumlah Duplikat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($duplicatesByName as $duplicate): ?>
                                <tr class="border-t border-gray-200">
                                    <td class="px-4 py-3 text-sm text-gray-900"><?= esc($duplicate['nama']) ?></td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <?= $duplicate['count'] ?> data
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Duplicates by NIP -->
                <?php if (!empty($duplicatesByNip)): ?>
                <div class="mb-8">
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
                        <h3 class="text-lg font-semibold text-orange-800 mb-2">
                            <i class="fas fa-id-card mr-2"></i>
                            Duplikasi Berdasarkan NIP (<?= count($duplicatesByNip) ?> NIP)
                        </h3>
                        <p class="text-sm text-orange-600">Ditemukan NIP yang sama dalam database</p>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                            <thead class="bg-orange-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-orange-800">NIP</th>
                                    <th class="px-4 py-3 text-center text-sm font-semibold text-orange-800">Jumlah Duplikat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($duplicatesByNip as $duplicate): ?>
                                <tr class="border-t border-gray-200">
                                    <td class="px-4 py-3 text-sm text-gray-900 font-mono"><?= esc($duplicate['nip']) ?></td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            <?= $duplicate['count'] ?> data
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Duplicates by NUPTK -->
                <?php if (!empty($duplicatesByNuptk)): ?>
                <div class="mb-8">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <h3 class="text-lg font-semibold text-yellow-800 mb-2">
                            <i class="fas fa-certificate mr-2"></i>
                            Duplikasi Berdasarkan NUPTK (<?= count($duplicatesByNuptk) ?> NUPTK)
                        </h3>
                        <p class="text-sm text-yellow-600">Ditemukan NUPTK yang sama dalam database</p>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                            <thead class="bg-yellow-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-yellow-800">NUPTK</th>
                                    <th class="px-4 py-3 text-center text-sm font-semibold text-yellow-800">Jumlah Duplikat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($duplicatesByNuptk as $duplicate): ?>
                                <tr class="border-t border-gray-200">
                                    <td class="px-4 py-3 text-sm text-gray-900 font-mono"><?= esc($duplicate['nuptk']) ?></td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <?= $duplicate['count'] ?> data
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Action Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Informasi Pembersihan</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Sistem akan mempertahankan data dengan ID terkecil (data yang lebih lama)</li>
                                    <li>Data duplikat dengan ID lebih besar akan dihapus</li>
                                    <li>Proses ini tidak dapat dibatalkan, pastikan Anda sudah backup data</li>
                                    <li>Pembersihan hanya berdasarkan nama yang sama persis</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

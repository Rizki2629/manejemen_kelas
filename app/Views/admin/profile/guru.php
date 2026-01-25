<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-4 rounded-2xl">
                        <i class="fas fa-user-tie text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900"><?= esc($title) ?></h1>
                        <p class="text-gray-600 mt-1">Kelola informasi profil guru Anda</p>
                    </div>
                </div>
                <a href="<?= ($role === 'walikelas') ? '/profile/edit' : '/admin/profile/edit' ?>" class="bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <i class="fas fa-edit mr-2"></i>Edit Profil
                </a>
            </div>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-xl mb-6 shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?= session()->getFlashdata('success') ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-xl mb-6 shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?= session()->getFlashdata('error') ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Profile Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 text-center">
                    <div class="relative inline-block mb-6">
                        <div class="w-32 h-32 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-4xl text-white font-bold shadow-lg">
                            <?= strtoupper(substr($guru['nama'], 0, 2)) ?>
                        </div>
                        <div class="absolute -bottom-2 -right-2 bg-green-500 w-8 h-8 rounded-full border-4 border-white flex items-center justify-center">
                            <i class="fas fa-check text-white text-xs"></i>
                        </div>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2"><?= esc($guru['nama']) ?></h2>
                    <p class="text-purple-600 font-semibold mb-2">NIP: <?= esc($guru['nip'] ?? 'Belum diisi') ?></p>
                    <p class="text-gray-600 mb-4"><?= esc($guru['email'] ?? 'Belum diisi') ?></p>
                    
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-2xl p-4 mb-6">
                        <p class="text-sm text-gray-600 mb-1">Status</p>
                        <p class="text-lg font-semibold text-blue-700">Guru Aktif</p>
                    </div>

                    <div class="space-y-3">
                        <?php if (!empty($guru['hp'])): ?>
                        <div class="flex items-center justify-center space-x-2 text-gray-600">
                            <i class="fas fa-phone w-4"></i>
                            <span><?= esc($guru['hp']) ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($guru['tempat_lahir']) && !empty($guru['tanggal_lahir'])): ?>
                        <div class="flex items-center justify-center space-x-2 text-gray-600">
                            <i class="fas fa-map-marker-alt w-4"></i>
                            <span><?= esc($guru['tempat_lahir']) ?>, <?= date('d/m/Y', strtotime($guru['tanggal_lahir'])) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Profile Details -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-3"></i>
                        Informasi Detail
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama Lengkap -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Nama Lengkap</label>
                            <div class="bg-gray-50 rounded-xl p-4 border-l-4 border-blue-500">
                                <p class="text-gray-900 font-medium"><?= esc($guru['nama']) ?></p>
                            </div>
                        </div>

                        <!-- NIP -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">NIP</label>
                            <div class="bg-gray-50 rounded-xl p-4 border-l-4 border-purple-500">
                                <p class="text-gray-900 font-medium"><?= esc($guru['nip']) ?></p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Email</label>
                            <div class="bg-gray-50 rounded-xl p-4 border-l-4 border-green-500">
                                <p class="text-gray-900 font-medium"><?= esc($guru['email']) ?></p>
                            </div>
                        </div>

                        <!-- No HP -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">No. HP</label>
                            <div class="bg-gray-50 rounded-xl p-4 border-l-4 border-yellow-500">
                                <p class="text-gray-900 font-medium"><?= !empty($guru['hp']) ? esc($guru['hp']) : 'Belum diisi' ?></p>
                            </div>
                        </div>

                        <!-- Jenis Kelamin -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Jenis Kelamin</label>
                            <div class="bg-gray-50 rounded-xl p-4 border-l-4 border-pink-500">
                                <p class="text-gray-900 font-medium">
                                    <?php if (!empty($guru['jk'])): ?>
                                        <?= $guru['jk'] === 'L' ? 'Laki-laki' : 'Perempuan' ?>
                                    <?php else: ?>
                                        Belum diisi
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>

                        <!-- Tempat, Tanggal Lahir -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Tempat, Tanggal Lahir</label>
                            <div class="bg-gray-50 rounded-xl p-4 border-l-4 border-indigo-500">
                                <p class="text-gray-900 font-medium">
                                    <?php if (!empty($guru['tempat_lahir']) && !empty($guru['tanggal_lahir'])): ?>
                                        <?= esc($guru['tempat_lahir']) ?>, <?= date('d F Y', strtotime($guru['tanggal_lahir'])) ?>
                                    <?php else: ?>
                                        Belum diisi
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>

                        <!-- Alamat -->
                        <div class="md:col-span-2 space-y-2">
                            <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Alamat</label>
                            <div class="bg-gray-50 rounded-xl p-4 border-l-4 border-red-500">
                                <p class="text-gray-900 font-medium"><?= !empty($guru['alamat_jalan']) ? esc($guru['alamat_jalan']) : 'Belum diisi' ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="<?= ($role === 'walikelas') ? '/profile/edit' : '/admin/profile/edit' ?>" class="flex-1 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl text-center">
                                <i class="fas fa-edit mr-2"></i>Edit Profil
                            </a>
                            <a href="<?= ($role === 'walikelas') ? '/dashboard' : '/admin/dashboard' ?>" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold transition-all duration-300 text-center">
                                <i class="fas fa-arrow-left mr-2"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

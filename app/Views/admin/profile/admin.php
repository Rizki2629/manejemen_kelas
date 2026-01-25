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
                        <i class="fas fa-user-shield text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900"><?= esc($title) ?></h1>
                        <p class="text-gray-600 mt-1">Kelola informasi profil admin Anda</p>
                    </div>
                </div>
                <a href="/admin/profile/edit" class="bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <i class="fas fa-edit mr-2"></i>Edit Profile
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
                            <?= strtoupper(substr($admin['nama'], 0, 2)) ?>
                        </div>
                        <div class="absolute -bottom-2 -right-2 bg-green-500 w-8 h-8 rounded-full border-4 border-white flex items-center justify-center">
                            <i class="fas fa-shield-alt text-white text-xs"></i>
                        </div>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2"><?= esc($admin['nama']) ?></h2>
                    <p class="text-purple-600 font-semibold mb-2">@<?= esc($admin['username']) ?></p>
                    <p class="text-gray-600 mb-4"><?= esc($admin['email']) ?></p>
                    
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-2xl p-4 mb-6">
                        <p class="text-sm text-gray-600 mb-1">Role</p>
                        <p class="text-lg font-semibold text-blue-700">Administrator</p>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center justify-center space-x-2 text-gray-600">
                            <i class="fas fa-calendar w-4"></i>
                            <span>Bergabung <?= date('F Y', strtotime($admin['created_at'])) ?></span>
                        </div>
                        
                        <div class="flex items-center justify-center space-x-2 text-gray-600">
                            <i class="fas fa-clock w-4"></i>
                            <span>Update terakhir <?= date('d/m/Y', strtotime($admin['updated_at'])) ?></span>
                        </div>
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
                        <!-- Nama -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Nama Lengkap</label>
                            <div class="bg-gray-50 rounded-xl p-4 border-l-4 border-blue-500">
                                <p class="text-gray-900 font-medium"><?= esc($admin['nama']) ?></p>
                            </div>
                        </div>

                        <!-- Username -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Username</label>
                            <div class="bg-gray-50 rounded-xl p-4 border-l-4 border-purple-500">
                                <p class="text-gray-900 font-medium"><?= esc($admin['username']) ?></p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Email</label>
                            <div class="bg-gray-50 rounded-xl p-4 border-l-4 border-green-500">
                                <p class="text-gray-900 font-medium"><?= esc($admin['email']) ?></p>
                            </div>
                        </div>

                        <!-- Role -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Role</label>
                            <div class="bg-gray-50 rounded-xl p-4 border-l-4 border-yellow-500">
                                <p class="text-gray-900 font-medium">Administrator</p>
                            </div>
                        </div>

                        <!-- Created At -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Tanggal Bergabung</label>
                            <div class="bg-gray-50 rounded-xl p-4 border-l-4 border-indigo-500">
                                <p class="text-gray-900 font-medium"><?= date('d F Y, H:i', strtotime($admin['created_at'])) ?> WIB</p>
                            </div>
                        </div>

                        <!-- Updated At -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Terakhir Diupdate</label>
                            <div class="bg-gray-50 rounded-xl p-4 border-l-4 border-pink-500">
                                <p class="text-gray-900 font-medium"><?= date('d F Y, H:i', strtotime($admin['updated_at'])) ?> WIB</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="/admin/profile/edit" class="flex-1 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl text-center">
                                <i class="fas fa-edit mr-2"></i>Edit Profile
                            </a>
                            <a href="/admin/dashboard" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold transition-all duration-300 text-center">
                                <i class="fas fa-arrow-left mr-2"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Security Card -->
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 mt-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-shield-alt text-green-500 mr-3"></i>
                        Keamanan Akun
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-200">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-semibold text-green-800">Password</h4>
                                <i class="fas fa-lock text-green-600"></i>
                            </div>
                            <p class="text-green-700 text-sm mb-4">Password terakhir diubah pada pembuatan akun</p>
                            <button onclick="showChangePasswordModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                                Ubah Password
                            </button>
                        </div>

                        <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl p-6 border border-blue-200">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-semibold text-blue-800">Login Terakhir</h4>
                                <i class="fas fa-clock text-blue-600"></i>
                            </div>
                            <p class="text-blue-700 text-sm mb-4">Sesi login saat ini</p>
                            <p class="text-blue-600 text-xs">IP: <?= $_SERVER['REMOTE_ADDR'] ?? 'Unknown' ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div id="changePasswordModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900">Ubah Password</h3>
            <button onclick="hideChangePasswordModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form action="/admin/profile/change-password" method="POST">
            <?= csrf_field() ?>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password Saat Ini</label>
                    <input type="password" name="current_password" required 
                           class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                    <input type="password" name="password" required minlength="6"
                           class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
                    <input type="password" name="confirm_password" required 
                           class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200">
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" class="flex-1 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white py-3 rounded-xl font-semibold transition-all duration-300">
                    Ubah Password
                </button>
                <button type="button" onclick="hideChangePasswordModal()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-xl font-semibold transition-all duration-300">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showChangePasswordModal() {
    document.getElementById('changePasswordModal').classList.remove('hidden');
}

function hideChangePasswordModal() {
    document.getElementById('changePasswordModal').classList.add('hidden');
}
</script>
<?= $this->endSection() ?>

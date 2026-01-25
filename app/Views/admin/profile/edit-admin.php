<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 mb-8">
            <div class="flex items-center space-x-4">
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-4 rounded-2xl">
                    <i class="fas fa-user-edit text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900"><?= esc($title) ?></h1>
                    <p class="text-gray-600 mt-1">Perbarui informasi profile admin Anda</p>
                </div>
            </div>
        </div>

        <?php if (session()->get('errors')): ?>
            <div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                    <h3 class="text-lg font-semibold text-red-800">Terdapat kesalahan:</h3>
                </div>
                <ul class="list-disc list-inside space-y-1 text-red-700">
                    <?php foreach (session()->get('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Form Section -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
            <form action="/admin/profile/update" method="POST" class="space-y-8">
                <?= csrf_field() ?>

                <!-- Data Admin -->
                <div>
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-user-shield text-blue-500 mr-3"></i>
                        Data Admin
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama -->
                        <div class="space-y-2">
                            <label for="nama" class="block text-sm font-semibold text-gray-700">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="nama" name="nama" 
                                   value="<?= old('nama', $admin['nama']) ?>"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200"
                                   placeholder="Masukkan nama lengkap" required>
                        </div>

                        <!-- Username -->
                        <div class="space-y-2">
                            <label for="username" class="block text-sm font-semibold text-gray-700">
                                Username <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="username" name="username" 
                                   value="<?= old('username', $admin['username']) ?>"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200"
                                   placeholder="Masukkan username" required>
                        </div>

                        <!-- Email -->
                        <div class="md:col-span-2 space-y-2">
                            <label for="email" class="block text-sm font-semibold text-gray-700">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" name="email" 
                                   value="<?= old('email', $admin['email']) ?>"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200"
                                   placeholder="Masukkan email" required>
                        </div>
                    </div>
                </div>

                <!-- Ubah Password (Optional) -->
                <div>
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-lock text-green-500 mr-3"></i>
                        Ubah Password (Opsional)
                    </h3>
                    
                    <div class="bg-gray-50 rounded-xl p-6 mb-6">
                        <p class="text-sm text-gray-600 mb-4">
                            <i class="fas fa-info-circle mr-2"></i>
                            Kosongkan field password jika tidak ingin mengubah password
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Password Baru -->
                        <div class="space-y-2">
                            <label for="password" class="block text-sm font-semibold text-gray-700">
                                Password Baru
                            </label>
                            <input type="password" id="password" name="password" 
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200"
                                   placeholder="Masukkan password baru" minlength="6">
                        </div>

                        <!-- Konfirmasi Password -->
                        <div class="space-y-2">
                            <label for="confirm_password" class="block text-sm font-semibold text-gray-700">
                                Konfirmasi Password
                            </label>
                            <input type="password" id="confirm_password" name="confirm_password" 
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200"
                                   placeholder="Konfirmasi password baru">
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                    <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <i class="fas fa-save mr-2"></i>Simpan Perubahan
                    </button>
                    <a href="/admin/profile" 
                       class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold transition-all duration-300 text-center">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const nama = document.getElementById('nama').value.trim();
    const username = document.getElementById('username').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (!nama || !username || !email) {
        e.preventDefault();
        alert('Harap lengkapi semua field yang wajib diisi (*)');
        return false;
    }
    
    if (nama.length < 3) {
        e.preventDefault();
        alert('Nama minimal 3 karakter');
        return false;
    }
    
    if (username.length < 3) {
        e.preventDefault();
        alert('Username minimal 3 karakter');
        return false;
    }
    
    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        e.preventDefault();
        alert('Format email tidak valid');
        return false;
    }
    
    // Password validation (only if provided)
    if (password) {
        if (password.length < 6) {
            e.preventDefault();
            alert('Password minimal 6 karakter');
            return false;
        }
        
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Konfirmasi password tidak cocok');
            return false;
        }
    }
});

// Password confirmation real-time validation
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (password && confirmPassword) {
        if (password === confirmPassword) {
            this.style.borderColor = '#10B981';
            this.style.backgroundColor = '#F0FDF4';
        } else {
            this.style.borderColor = '#EF4444';
            this.style.backgroundColor = '#FEF2F2';
        }
    } else {
        this.style.borderColor = '#D1D5DB';
        this.style.backgroundColor = '#FFFFFF';
    }
});
</script>
<?= $this->endSection() ?>

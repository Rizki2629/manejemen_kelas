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
                    <p class="text-gray-600 mt-1">Perbarui informasi profil guru Anda</p>
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

                <!-- Data Pribadi -->
                <div>
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-user text-blue-500 mr-3"></i>
                        Data Pribadi
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama Guru -->
                        <div class="space-y-2">
                            <label for="nama_guru" class="block text-sm font-semibold text-gray-700">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="nama_guru" name="nama_guru" 
                                   value="<?= old('nama_guru', $guru['nama_guru']) ?>"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200"
                                   placeholder="Masukkan nama lengkap" required>
                        </div>

                        <!-- NIP -->
                        <div class="space-y-2">
                            <label for="nip" class="block text-sm font-semibold text-gray-700">
                                NIP <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="nip" name="nip" 
                                   value="<?= old('nip', $guru['nip']) ?>"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200"
                                   placeholder="Masukkan NIP" required>
                        </div>

                        <!-- Email -->
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-semibold text-gray-700">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" name="email" 
                                   value="<?= old('email', $guru['email']) ?>"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200"
                                   placeholder="Masukkan email" required>
                        </div>

                        <!-- No HP -->
                        <div class="space-y-2">
                            <label for="no_hp" class="block text-sm font-semibold text-gray-700">
                                No. HP
                            </label>
                            <input type="text" id="no_hp" name="no_hp" 
                                   value="<?= old('no_hp', $guru['no_hp']) ?>"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200"
                                   placeholder="Masukkan nomor HP">
                        </div>

                        <!-- Jenis Kelamin -->
                        <div class="space-y-2">
                            <label for="jenis_kelamin" class="block text-sm font-semibold text-gray-700">
                                Jenis Kelamin
                            </label>
                            <select id="jenis_kelamin" name="jenis_kelamin" 
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200">
                                <option value="">Pilih jenis kelamin</option>
                                <option value="L" <?= old('jenis_kelamin', $guru['jenis_kelamin']) === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="P" <?= old('jenis_kelamin', $guru['jenis_kelamin']) === 'P' ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                        </div>

                        <!-- Tempat Lahir -->
                        <div class="space-y-2">
                            <label for="tempat_lahir" class="block text-sm font-semibold text-gray-700">
                                Tempat Lahir
                            </label>
                            <input type="text" id="tempat_lahir" name="tempat_lahir" 
                                   value="<?= old('tempat_lahir', $guru['tempat_lahir']) ?>"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200"
                                   placeholder="Masukkan tempat lahir">
                        </div>

                        <!-- Tanggal Lahir -->
                        <div class="space-y-2">
                            <label for="tanggal_lahir" class="block text-sm font-semibold text-gray-700">
                                Tanggal Lahir
                            </label>
                            <input type="date" id="tanggal_lahir" name="tanggal_lahir" 
                                   value="<?= old('tanggal_lahir', $guru['tanggal_lahir']) ?>"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200">
                        </div>

                        <!-- Alamat -->
                        <div class="md:col-span-2 space-y-2">
                            <label for="alamat" class="block text-sm font-semibold text-gray-700">
                                Alamat
                            </label>
                            <textarea id="alamat" name="alamat" rows="4"
                                      class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 resize-none"
                                      placeholder="Masukkan alamat lengkap"><?= old('alamat', $guru['alamat']) ?></textarea>
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
    const namaGuru = document.getElementById('nama_guru').value.trim();
    const nip = document.getElementById('nip').value.trim();
    const email = document.getElementById('email').value.trim();
    
    if (!namaGuru || !nip || !email) {
        e.preventDefault();
        alert('Harap lengkapi semua field yang wajib diisi (*)');
        return false;
    }
    
    if (namaGuru.length < 3) {
        e.preventDefault();
        alert('Nama guru minimal 3 karakter');
        return false;
    }
    
    if (nip.length < 8) {
        e.preventDefault();
        alert('NIP minimal 8 karakter');
        return false;
    }
    
    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        e.preventDefault();
        alert('Format email tidak valid');
        return false;
    }
});
</script>
<?= $this->endSection() ?>

<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<!-- Breadcrumb -->
<nav class="flex mb-6 pt-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3 pl-2">
        <li class="inline-flex items-center">
            <a href="/admin/dashboard" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                </svg>
                Dashboard
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <a href="/admin/data-siswa" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Data Siswa</a>
            </div>
        </li>
        <li aria-current="page">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Edit Siswa</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-3 pl-2">Edit Data Siswa</h1>
            <p class="text-gray-600 pl-2">
                Ubah data siswa: 
                <span class="font-medium text-blue-600"><?= isset($siswa) ? esc($siswa['nama']) : 'Data tidak ditemukan' ?></span>
            </p>
        </div>
        <div class="flex space-x-3">
            <a href="/admin/data-siswa" class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 flex items-center space-x-2 shadow-lg hover:shadow-xl hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali</span>
            </a>
        </div>
    </div>
</div>

<?php if (isset($siswa)): ?>
<!-- Form -->
<div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 overflow-hidden pl-2">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Form Edit Siswa</h3>
        <p class="text-sm text-gray-500 mt-1">Lengkapi semua field yang wajib diisi (*)</p>
    </div>

    <form action="/admin/data-siswa/update/<?= $siswa['id'] ?>" method="POST" class="p-6">
        <?= csrf_field() ?>
        
        <!-- Basic Information -->
        <div class="mb-8">
            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Data Pribadi
            </h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama -->
                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" id="nama" name="nama" value="<?= old('nama', $siswa['nama']) ?>" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/80 backdrop-blur-sm transition-all duration-300"
                           placeholder="Masukkan nama lengkap siswa">
                    <?php if (isset($validation) && $validation->hasError('nama')): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $validation->getError('nama') ?></p>
                    <?php endif; ?>
                </div>

                <!-- NISN -->
                <div>
                    <label for="nisn" class="block text-sm font-medium text-gray-700 mb-2">NISN <span class="text-red-500">*</span></label>
                    <input type="text" id="nisn" name="nisn" value="<?= old('nisn', $siswa['nisn']) ?>" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/80 backdrop-blur-sm transition-all duration-300"
                           placeholder="Nomor Induk Siswa Nasional">
                    <?php if (isset($validation) && $validation->hasError('nisn')): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $validation->getError('nisn') ?></p>
                    <?php endif; ?>
                </div>

                <!-- NIPD -->
                <div>
                    <label for="nipd" class="block text-sm font-medium text-gray-700 mb-2">NIPD</label>
                    <input type="text" id="nipd" name="nipd" value="<?= old('nipd', $siswa['nipd']) ?>"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/80 backdrop-blur-sm transition-all duration-300"
                           placeholder="Nomor Induk Peserta Didik">
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label for="jk" class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
                    <select id="jk" name="jk" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/80 backdrop-blur-sm transition-all duration-300">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="L" <?= old('jk', $siswa['jk']) === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= old('jk', $siswa['jk']) === 'P' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                    <?php if (isset($validation) && $validation->hasError('jk')): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $validation->getError('jk') ?></p>
                    <?php endif; ?>
                </div>

                <!-- Kelas -->
                <div>
                    <label for="kelas" class="block text-sm font-medium text-gray-700 mb-2">Kelas <span class="text-red-500">*</span></label>
                    <input type="text" id="kelas" name="kelas" value="<?= old('kelas', $siswa['kelas']) ?>" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/80 backdrop-blur-sm transition-all duration-300"
                           placeholder="Contoh: X IPA 1, XI IPS 2">
                    <?php if (isset($validation) && $validation->hasError('kelas')): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $validation->getError('kelas') ?></p>
                    <?php endif; ?>
                </div>

                <!-- Agama -->
                <div>
                    <label for="agama" class="block text-sm font-medium text-gray-700 mb-2">Agama</label>
                    <select id="agama" name="agama"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/80 backdrop-blur-sm transition-all duration-300">
                        <option value="">Pilih Agama</option>
                        <option value="Islam" <?= old('agama', $siswa['agama']) === 'Islam' ? 'selected' : '' ?>>Islam</option>
                        <option value="Kristen" <?= old('agama', $siswa['agama']) === 'Kristen' ? 'selected' : '' ?>>Kristen</option>
                        <option value="Katolik" <?= old('agama', $siswa['agama']) === 'Katolik' ? 'selected' : '' ?>>Katolik</option>
                        <option value="Hindu" <?= old('agama', $siswa['agama']) === 'Hindu' ? 'selected' : '' ?>>Hindu</option>
                        <option value="Buddha" <?= old('agama', $siswa['agama']) === 'Buddha' ? 'selected' : '' ?>>Buddha</option>
                        <option value="Konghucu" <?= old('agama', $siswa['agama']) === 'Konghucu' ? 'selected' : '' ?>>Konghucu</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Birth Information -->
        <div class="mb-8">
            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4v6m4-6v6m-4-4h4" />
                </svg>
                Data Kelahiran
            </h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tempat Lahir -->
                <div>
                    <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir</label>
                    <input type="text" id="tempat_lahir" name="tempat_lahir" value="<?= old('tempat_lahir', $siswa['tempat_lahir']) ?>"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/80 backdrop-blur-sm transition-all duration-300"
                           placeholder="Kota/Kabupaten tempat lahir">
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="<?= old('tanggal_lahir', $siswa['tanggal_lahir']) ?>"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/80 backdrop-blur-sm transition-all duration-300">
                    <?php if (isset($validation) && $validation->hasError('tanggal_lahir')): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $validation->getError('tanggal_lahir') ?></p>
                    <?php endif; ?>
                </div>

                <!-- NIK -->
                <div>
                    <label for="nik" class="block text-sm font-medium text-gray-700 mb-2">NIK</label>
                    <input type="text" id="nik" name="nik" value="<?= old('nik', $siswa['nik']) ?>"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/80 backdrop-blur-sm transition-all duration-300"
                           placeholder="Nomor Induk Kependudukan">
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="mb-8">
            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Kontak & Alamat
            </h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="email" name="email" value="<?= old('email', $siswa['email']) ?>"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/80 backdrop-blur-sm transition-all duration-300"
                           placeholder="contoh@email.com">
                    <?php if (isset($validation) && $validation->hasError('email')): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $validation->getError('email') ?></p>
                    <?php endif; ?>
                </div>

                <!-- Telepon -->
                <div>
                    <label for="hp" class="block text-sm font-medium text-gray-700 mb-2">No. HP/Telepon <span class="text-red-500">*</span></label>
                    <input type="text" id="hp" name="hp" value="<?= old('hp', $siswa['hp']) ?>" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/80 backdrop-blur-sm transition-all duration-300"
                           placeholder="08xxxxxxxxxx">
                    <?php if (isset($validation) && $validation->hasError('hp')): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $validation->getError('hp') ?></p>
                    <?php endif; ?>
                </div>

                <!-- Alamat -->
                <div class="md:col-span-2">
                    <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap</label>
                    <textarea id="alamat" name="alamat" rows="3"
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/80 backdrop-blur-sm transition-all duration-300"
                              placeholder="Alamat lengkap siswa"><?= old('alamat', $siswa['alamat']) ?></textarea>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
            <a href="/admin/data-siswa" 
               class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-sm hover:shadow-md">
                Batal
            </a>
            <button type="submit" 
                    class="bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white px-8 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105">
                Update Data Siswa
            </button>
        </div>
    </form>
</div>

<?php else: ?>
<!-- Error Message -->
<div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-8 text-center">
    <div class="flex flex-col items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-red-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
        </svg>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Data Siswa Tidak Ditemukan</h3>
        <p class="text-gray-600 mb-6">Data siswa yang ingin Anda edit tidak dapat ditemukan atau telah dihapus.</p>
        <a href="/admin/data-siswa" 
           class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105">
            Kembali ke Daftar Siswa
        </a>
    </div>
</div>
<?php endif; ?>

<script>
// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Check required fields
            const requiredFields = ['nama', 'nisn', 'jk', 'kelas', 'hp'];
            
            requiredFields.forEach(fieldName => {
                const field = document.querySelector(`[name="${fieldName}"]`);
                if (field && !field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');
                    
                    // Remove error class after user starts typing
                    field.addEventListener('input', function() {
                        this.classList.remove('border-red-500');
                    });
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang wajib diisi (*)');
            }
        });
        
        // Auto-format phone number
        const phoneField = document.getElementById('hp');
        if (phoneField) {
            phoneField.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                
                // Limit to 15 digits
                if (value.length > 15) {
                    value = value.substring(0, 15);
                }
                
                e.target.value = value;
            });
        }
        
        // Auto-format NIK
        const nikField = document.getElementById('nik');
        if (nikField) {
            nikField.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                
                // Limit to 16 digits for NIK
                if (value.length > 16) {
                    value = value.substring(0, 16);
                }
                
                e.target.value = value;
            });
        }
    }
});
</script>

<?= $this->endSection() ?>

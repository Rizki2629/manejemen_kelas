<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<!-- Tailwind CSS CDN -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.0/dist/tailwind.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<!-- Page Header -->
<div class="mb-8">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">
                <i class="fas fa-user-edit text-blue-600 mr-3"></i>Edit User
            </h1>
            <p class="text-gray-600">Edit data pengguna: <strong><?= esc($user['nama']) ?></strong></p>
        </div>
        <a href="<?= base_url('admin/users') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>
</div>

<!-- Success/Error Messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded-r-lg" id="successAlert">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-green-700"><?= session()->getFlashdata('success') ?></p>
            </div>
            <div class="ml-auto pl-3">
                <button type="button" onclick="closeAlert('successAlert')" class="text-green-400 hover:text-green-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-r-lg" id="errorAlert">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-red-700"><?= session()->getFlashdata('error') ?></p>
            </div>
            <div class="ml-auto pl-3">
                <button type="button" onclick="closeAlert('errorAlert')" class="text-red-400 hover:text-red-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-r-lg" id="errorsAlert">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-red-700 font-medium">Terjadi kesalahan:</p>
                <ul class="mt-2 text-sm text-red-600">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li class="flex items-center"><i class="fas fa-circle text-xs mr-2"></i><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="ml-auto pl-3">
                <button type="button" onclick="closeAlert('errorsAlert')" class="text-red-400 hover:text-red-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Main Form Card -->
<div class="w-full bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
    <!-- Card Header -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
        <h2 class="text-lg font-semibold text-white flex items-center">
            <i class="fas fa-user-edit mr-3"></i>
            Form Edit User
        </h2>
        <p class="text-blue-100 text-sm mt-1">Perbarui informasi pengguna di bawah ini</p>
    </div>

    <!-- Card Body -->
    <div class="p-8">
        <form id="editUserForm" method="POST" action="<?= base_url('admin/users/update/' . $user['id']) ?>" novalidate>
            <?= csrf_field() ?>
            
            <!-- Form Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user text-blue-500 mr-2"></i>
                        Username <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        value="<?= old('username', $user['username']) ?>"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 text-gray-900"
                        placeholder="Masukkan username"
                        required>
                    <p class="text-xs text-gray-500 mt-1">Min. 3 karakter, harus unik</p>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock text-blue-500 mr-2"></i>
                        Password Baru
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 text-gray-900"
                        placeholder="Kosongkan jika tidak ingin mengubah">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password</p>
                </div>

                <!-- Nama Lengkap -->
                <div class="lg:col-span-2">
                    <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-id-card text-blue-500 mr-2"></i>
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="nama" 
                        name="nama" 
                        value="<?= old('nama', $user['nama']) ?>"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 text-gray-900"
                        placeholder="Masukkan nama lengkap"
                        required>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope text-blue-500 mr-2"></i>
                        Email
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="<?= old('email', $user['email']) ?>"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 text-gray-900"
                        placeholder="Masukkan email">
                    <p class="text-xs text-gray-500 mt-1">Opsional, harus unik jika diisi</p>
                </div>

                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user-tag text-blue-500 mr-2"></i>
                        Role <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="role" 
                        name="role" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 text-gray-900"
                        required 
                        onchange="toggleWalikelasField()">
                        <option value="">Pilih Role</option>
                        <option value="admin" <?= old('role', $user['role']) === 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="walikelas" <?= old('role', $user['role']) === 'walikelas' ? 'selected' : '' ?>>Wali Kelas</option>
                        <option value="wali_kelas" <?= old('role', $user['role']) === 'wali_kelas' ? 'selected' : '' ?>>Wali Kelas (Legacy)</option>
                    </select>
                </div>

                <!-- NIP Field (for walikelas only) -->
                <div id="nipField" style="display: none;">
                    <label for="nip" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-id-badge text-blue-500 mr-2"></i>
                        NIP
                    </label>
                    <input 
                        type="text" 
                        id="nip" 
                        name="nip" 
                        value="<?= old('nip', isset($user['walikelas_nip']) ? $user['walikelas_nip'] : '') ?>"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 text-gray-900"
                        placeholder="Masukkan NIP (angka saja)"
                        pattern="[0-9]+"
                        title="Hanya boleh angka">
                    <p class="text-xs text-gray-500 mt-1">Nomor Induk Pegawai (opsional, hanya angka). Kosongkan jika belum ada.</p>
                </div>
            </div>

            <!-- Walikelas specific fields -->
            <div class="grid grid-cols-1 gap-6" id="walikelasSection" style="display: none;">
                <!-- Kelas Assignment -->
                <div>
                    <label for="walikelas_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-chalkboard text-blue-500 mr-2"></i>
                        Kelas yang Dipegang
                    </label>
                    <select 
                        id="walikelas_id" 
                        name="walikelas_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 text-gray-900">
                        <option value="">Pilih Kelas</option>
                        
                        <!-- Existing walikelas assignments -->
                        <?php if (isset($walikelas) && !empty($walikelas)): ?>
                            <optgroup label="Kelas yang Sudah Ada Wali Kelas">
                                <?php foreach ($walikelas as $kelas): ?>
                                    <option value="<?= esc($kelas['id']) ?>" <?= old('walikelas_id', $user['walikelas_id']) == $kelas['id'] ? 'selected' : '' ?>>
                                        <?= esc($kelas['kelas']) ?> (Sudah Ada: <?= esc($kelas['nama']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endif; ?>
                        
                        <!-- All available classes from tb_siswa -->
                        <?php if (isset($availableKelas) && !empty($availableKelas)): ?>
                            <optgroup label="Semua Kelas yang Tersedia">
                                <?php 
                                // Get existing assigned classes
                                $assignedKelas = [];
                                if (isset($walikelas)) {
                                    $assignedKelas = array_column($walikelas, 'kelas');
                                }
                                ?>
                                <?php foreach ($availableKelas as $kelas): ?>
                                    <?php 
                                    // Create a fake ID for new assignments
                                    $isAssigned = in_array($kelas['kelas'], $assignedKelas);
                                    $optionValue = $isAssigned ? '' : 'new_' . urlencode($kelas['kelas']);
                                    $optionText = $kelas['kelas'];
                                    if ($isAssigned) {
                                        $optionText .= ' (Sudah Ada Wali Kelas)';
                                    }
                                    ?>
                                    <option value="<?= esc($optionValue) ?>" 
                                        <?= old('walikelas_id', $user['walikelas_id']) == $optionValue ? 'selected' : '' ?>
                                        <?= $isAssigned ? 'disabled' : '' ?>>
                                        <?= esc($optionText) ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endif; ?>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Pilih kelas untuk wali kelas. Kelas yang sudah memiliki wali kelas akan ditandai.</p>
                </div>
            </div>

            <!-- Status Field (always visible) -->
            <div class="grid grid-cols-1 gap-6">
                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-toggle-on text-blue-500 mr-2"></i>
                        Status User
                    </label>
                    <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                        <input 
                            type="checkbox" 
                            id="is_active" 
                            name="is_active" 
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                            <?= old('is_active', $user['is_active']) ? 'checked' : '' ?>
                            <?= $user['id'] == session('user_id') ? 'disabled' : '' ?>>
                        <label for="is_active" class="ml-2 text-sm font-medium text-gray-900">
                            User Aktif
                        </label>
                    </div>
                    <?php if ($user['id'] == session('user_id')): ?>
                        <p class="text-xs text-gray-500 mt-1">Tidak dapat mengubah status user yang sedang login</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                <a href="<?= base_url('admin/users') ?>" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>
                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-save mr-2"></i>Update User
                </button>
            </div>
        </form>
    </div>
</div>

<!-- User Information Card -->
<div class="mt-8 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
    <div class="bg-gray-50 px-6 py-4">
        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
            <i class="fas fa-info-circle text-blue-500 mr-3"></i>
            Informasi User
        </h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">User ID</label>
                <p class="text-sm font-medium text-gray-900 mt-1"><?= esc($user['id']) ?></p>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Bergabung</label>
                <p class="text-sm font-medium text-gray-900 mt-1"><?= date('d M Y', strtotime($user['created_at'])) ?></p>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Terakhir Diubah</label>
                <p class="text-sm font-medium text-gray-900 mt-1"><?= $user['updated_at'] ? date('d M Y H:i', strtotime($user['updated_at'])) : '-' ?></p>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Login Terakhir</label>
                <p class="text-sm font-medium text-gray-900 mt-1"><?= $user['last_login'] ? date('d M Y H:i', strtotime($user['last_login'])) : 'Belum pernah login' ?></p>
            </div>
        </div>
    </div>
</div>

<script>
// Close alert function
function closeAlert(alertId) {
    document.getElementById(alertId).style.display = 'none';
}

// Toggle walikelas field based on role selection
function toggleWalikelasField() {
    const roleSelect = document.getElementById('role');
    const nipField = document.getElementById('nipField');
    const walikelasSection = document.getElementById('walikelasSection');
    
    if (roleSelect.value === 'walikelas' || roleSelect.value === 'wali_kelas') {
        nipField.style.display = 'block';
        walikelasSection.style.display = 'block';
    } else {
        nipField.style.display = 'none';
        walikelasSection.style.display = 'none';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleWalikelasField();
});

// Auto-hide success/error alerts after 5 seconds
setTimeout(function() {
    const successAlert = document.getElementById('successAlert');
    const errorAlert = document.getElementById('errorAlert');
    const errorsAlert = document.getElementById('errorsAlert');
    
    if (successAlert) successAlert.style.display = 'none';
    if (errorAlert) errorAlert.style.display = 'none';
    if (errorsAlert) errorsAlert.style.display = 'none';
}}, 5000);
</script>

<?= $this->endsection() ?>

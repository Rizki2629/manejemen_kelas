<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="max-w-4xl mx-auto p-6 bg-white rounded-xl shadow-lg">
    <div class="flex items-center space-x-4 mb-6">
        <div class="p-3 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </div>
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Tambah User</h2>
            <p class="text-gray-600">Tambah pengguna baru ke sistem</p>
        </div>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <ul class="list-disc list-inside">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= base_url('admin/users/store') ?>" class="space-y-6">
        <?= csrf_field() ?>

        <div>
            <label for="username" class="block text-gray-700 font-medium mb-1">Username <span class="text-red-500">*</span></label>
            <input type="text" id="username" name="username" value="<?= old('username') ?>" required
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <p class="text-sm text-gray-500 mt-1">Min. 3 karakter, harus unik</p>
        </div>

        <div>
            <label for="password" class="block text-gray-700 font-medium mb-1">Password <span class="text-red-500">*</span></label>
            <input type="password" id="password" name="password" required
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <p class="text-sm text-gray-500 mt-1">Min. 6 karakter</p>
        </div>

        <div>
            <label for="nama" class="block text-gray-700 font-medium mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
            <input type="text" id="nama" name="nama" value="<?= old('nama') ?>" required
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <div>
            <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
            <input type="email" id="email" name="email" value="<?= old('email') ?>"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <p class="text-sm text-gray-500 mt-1">Opsional, harus unik jika diisi</p>
        </div>

        <div>
            <label for="role" class="block text-gray-700 font-medium mb-1">Role <span class="text-red-500">*</span></label>
            <select id="role" name="role" required onchange="toggleWalikelasField()"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Pilih Role</option>
                <option value="admin" <?= old('role') === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="walikelas" <?= old('role') === 'walikelas' ? 'selected' : '' ?>>Wali Kelas</option>
                <option value="wali_kelas" <?= old('role') === 'wali_kelas' ? 'selected' : '' ?>>Wali Kelas (Legacy)</option>
            </select>
        </div>

        <div id="nipField" class="hidden">
            <label for="nip" class="block text-gray-700 font-medium mb-1">NIP</label>
            <input type="text" id="nip" name="nip" value="<?= old('nip') ?>"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                placeholder="Masukkan NIP (angka saja)"
                pattern="[0-9]+"
                title="Hanya boleh angka" />
            <p class="text-sm text-gray-500 mt-1">Nomor Induk Pegawai (opsional, hanya angka). Kosongkan jika belum ada.</p>
        </div>

        <div id="walikelasField" class="hidden">
            <label for="walikelas_id" class="block text-gray-700 font-medium mb-1">Kelas yang Dipegang</label>
            <select id="walikelas_id" name="walikelas_id"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Pilih Kelas</option>
                
                <!-- Existing walikelas assignments (for editing existing assignments) -->
                <?php if (isset($walikelas) && !empty($walikelas)): ?>
                    <optgroup label="Kelas yang Sudah Ada Wali Kelas">
                        <?php foreach ($walikelas as $kelas): ?>
                            <option value="<?= esc($kelas['id']) ?>" <?= old('walikelas_id') == $kelas['id'] ? 'selected' : '' ?>>
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
                            // Create a fake ID for new assignments (negative values)
                            $isAssigned = in_array($kelas['kelas'], $assignedKelas);
                            $optionValue = $isAssigned ? '' : 'new_' . urlencode($kelas['kelas']);
                            $optionText = $kelas['kelas'];
                            if ($isAssigned) {
                                $optionText .= ' (Sudah Ada Wali Kelas)';
                            }
                            ?>
                            <option value="<?= esc($optionValue) ?>" 
                                <?= old('walikelas_id') == $optionValue ? 'selected' : '' ?>
                                <?= $isAssigned ? 'disabled' : '' ?>>
                                <?= esc($optionText) ?>
                            </option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endif; ?>
            </select>
            <p class="text-sm text-gray-500 mt-1">Pilih kelas untuk wali kelas. Kelas yang sudah memiliki wali kelas akan ditandai.</p>
        </div>

        <div class="flex justify-end space-x-4 mt-6">
            <a href="<?= base_url('admin/users') ?>"
                class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 transition">Batal</a>
            <button type="submit"
                class="px-6 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-md hover:from-blue-600 hover:to-indigo-700 transition">Simpan User</button>
        </div>
    </form>
</div>

<script>
function toggleWalikelasField() {
    const role = document.getElementById('role').value;
    const walikelasField = document.getElementById('walikelasField');
    const walikelasSelect = document.getElementById('walikelas_id');
    const nipField = document.getElementById('nipField');
    const nipInput = document.getElementById('nip');

    if (role === 'walikelas' || role === 'wali_kelas') {
        walikelasField.classList.remove('hidden');
        nipField.classList.remove('hidden');
        walikelasSelect.required = true;
        // NIP is optional now
    } else {
        walikelasField.classList.add('hidden');
        nipField.classList.add('hidden');
        walikelasSelect.required = false;
        walikelasSelect.value = '';
        nipInput.value = '';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    toggleWalikelasField();
});
</script>

<?= $this->endSection() ?>

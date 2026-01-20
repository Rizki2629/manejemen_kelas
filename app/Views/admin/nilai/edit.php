<?= $this->extend('admin/layout'); ?>

<?= $this->section('content'); ?>
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800"><?= $title ?></h1>
                <p class="text-gray-600 mt-1">Edit nilai mata pelajaran <?= $mataPelajaranList[$nilai['mata_pelajaran']] ?></p>
            </div>
            <div class="flex space-x-3">
                <a href="<?= base_url('/admin/nilai/detail/' . $nilai['siswa_id']) ?>?mapel=<?= $nilai['mata_pelajaran'] ?>" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="<?= base_url('/admin/nilai/update/' . $nilai['id']) ?>" method="POST" class="space-y-6">
            <?= csrf_field() ?>
            
            <!-- Student Info (Read Only) -->
            <div class="p-4 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Informasi Siswa</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Siswa</label>
                        <input type="text" value="<?= $student['nama'] ?>" 
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm bg-gray-100" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">NIPD</label>
                        <input type="text" value="<?= $student['nipd'] ?>" 
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm bg-gray-100" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kelas</label>
                        <input type="text" value="Kelas <?= $student['kelas'] ?>" 
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm bg-gray-100" readonly>
                    </div>
                </div>
            </div>

            <!-- Form Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Mata Pelajaran -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mata Pelajaran</label>
                    <select name="mata_pelajaran" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <?php foreach ($mataPelajaranList as $code => $name): ?>
                            <option value="<?= $code ?>" <?= $nilai['mata_pelajaran'] === $code ? 'selected' : '' ?>>
                                <?= $name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Jenis Nilai -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Nilai</label>
                    <select name="jenis_nilai" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <?php foreach ($jenisNilaiList as $code => $name): ?>
                            <option value="<?= $code ?>" <?= $nilai['jenis_nilai'] === $code ? 'selected' : '' ?>>
                                <?= $name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Nilai -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nilai</label>
                    <input type="number" name="nilai" min="0" max="100" step="0.1" 
                           value="<?= $nilai['nilai'] ?>"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                           placeholder="Masukkan nilai (0-100)" required>
                </div>

                <!-- Tanggal -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                    <input type="date" name="tanggal" value="<?= $nilai['tanggal'] ?>" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                </div>

                <!-- TP/Materi -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">TP/Materi</label>
                    <textarea name="tp_materi" rows="3"
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                              placeholder="Contoh: TP 1.1 - Ciri-ciri Makhluk Hidup dan Benda Tak Hidup"><?= $nilai['tp_materi'] ?></textarea>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3">
                <a href="<?= base_url('/admin/nilai/detail/' . $nilai['siswa_id']) ?>?mapel=<?= $nilai['mata_pelajaran'] ?>" 
                   class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                    Batal
                </a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Update Nilai
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Validation Error Messages -->
<?php if (session()->getFlashdata('validation')): ?>
<div class="fixed bottom-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-md z-50" id="validation-message">
    <div class="flex items-center">
        <i class="fas fa-exclamation-circle mr-2"></i>
        <div>
            <strong>Validation Errors:</strong>
            <ul class="list-disc list-inside mt-1">
                <?php foreach (session()->getFlashdata('validation') as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <button onclick="document.getElementById('validation-message').style.display='none'" class="ml-2 text-red-500 hover:text-red-700">
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
    const validationMsg = document.getElementById('validation-message');
    const errorMsg = document.getElementById('error-message');
    if (validationMsg) validationMsg.style.display = 'none';
    if (errorMsg) errorMsg.style.display = 'none';
}, 10000);
</script>
<?= $this->endSection(); ?>

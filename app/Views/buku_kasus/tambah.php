<!-- app/Views/buku_kasus/tambah.php -->
<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>
<div class="px-4 py-5 sm:px-6">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold leading-6 text-gray-900 flex items-center">
                <i class="fas fa-plus-circle text-indigo-600 mr-2"></i>
                Tambah Kasus Baru
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Input informasi kasus atau masalah siswa
            </p>
        </div>
        <a href="<?= base_url('buku-kasus'); ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali
        </a>
    </div>
</div>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('error')) : ?>
    <div class="mx-4 mt-4 px-4 py-2 bg-red-100 border-l-4 border-red-500 text-red-700 rounded shadow">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <p><?= session()->getFlashdata('error'); ?></p>
        </div>
    </div>
<?php endif; ?>

<!-- Validation Errors -->
<?php if (isset($validation)) : ?>
    <div class="mx-4 mt-4 px-4 py-2 bg-red-100 border-l-4 border-red-500 text-red-700 rounded shadow">
        <div class="flex items-start">
            <i class="fas fa-exclamation-circle mr-2 mt-0.5"></i>
            <div>
                <?= $validation->listErrors() ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="mt-5 px-4">
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Informasi Kasus</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Silahkan isi informasi lengkap tentang kasus yang terjadi.
                </p>
                <div class="mt-6 border-t border-gray-200 pt-4">
                    <div class="rounded-md bg-blue-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Tips pengisian:</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>Pilih nama siswa yang bersangkutan</li>
                                        <li>Jelaskan masalah dengan detail</li>
                                        <li>Berikan penyelesaian yang konstruktif</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-5 md:mt-0 md:col-span-2">
            <form action="<?= base_url('buku-kasus/simpan'); ?>" method="POST">
                <?= csrf_field() ?>
                <div class="shadow overflow-hidden sm:rounded-md">
                    <div class="px-4 py-5 bg-white sm:p-6">
                        <div class="grid grid-cols-6 gap-6">
                            <!-- Admin Only: Class Selection -->
                            <?php if (session()->get('role') === 'admin') : ?>
                                <div class="col-span-6 sm:col-span-3">
                                    <label for="kelas_id" class="block text-sm font-medium text-gray-700">Kelas</label>
                                    <select id="kelas_id" name="kelas_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">Pilih Kelas</option>
                                        <?php foreach ($kelasList as $kelas) : ?>
                                            <option value="<?= $kelas['id']; ?>"><?= $kelas['nama']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php elseif (isset($kelas)) : ?>
                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Kelas</label>
                                    <div class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm sm:text-sm">
                                        <?= $kelas['nama']; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Student Selection -->
                            <div class="col-span-6 sm:col-span-3">
                                <label for="siswa_id" class="block text-sm font-medium text-gray-700">Nama Siswa</label>
                                <select id="siswa_id" name="siswa_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                    <option value="">Pilih Siswa</option>
                                    <?php foreach ($siswaList as $siswa) : ?>
                                        <option value="<?= $siswa['id']; ?>"><?= $siswa['nama']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div id="loadingSpinner" class="hidden mt-2 text-sm text-gray-500 flex items-center">
                                    <i class="fas fa-spinner fa-spin mr-2"></i> Memuat data siswa...
                                </div>
                            </div>

                            <!-- Problem Type -->
                            <div class="col-span-6 sm:col-span-3">
                                <label for="jenis_masalah" class="block text-sm font-medium text-gray-700">Jenis Masalah</label>
                                <select id="jenis_masalah" name="jenis_masalah" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                    <option value="">Pilih Jenis Masalah</option>
                                    <option value="tidak mengerjakan pr">Tidak Mengerjakan PR</option>
                                    <option value="mengerjakan PR di sekolah">Mengerjakan PR di sekolah</option>
                                    <option value="berkelahi di kelas">Berkelahi di kelas</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>

                            <!-- Other Problem Type (shown conditionally) -->
                            <div id="jenisMasalahLainnyaContainer" class="col-span-6 sm:col-span-3 hidden">
                                <label for="jenis_masalah_lainnya" class="block text-sm font-medium text-gray-700">Jenis Masalah Lainnya</label>
                                <input type="text" name="jenis_masalah_lainnya" id="jenis_masalah_lainnya" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Jelaskan jenis masalah lainnya">
                            </div>

                            <!-- Problem Notes -->
                            <div class="col-span-6">
                                <label for="catatan_masalah" class="block text-sm font-medium text-gray-700">Catatan Masalah</label>
                                <textarea id="catatan_masalah" name="catatan_masalah" rows="4" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Jelaskan detail masalah yang terjadi" required></textarea>
                                <p class="mt-2 text-sm text-gray-500">
                                    Jelaskan kronologi kejadian dengan detail dan jelas.
                                </p>
                            </div>

                            <!-- Resolution -->
                            <div class="col-span-6">
                                <label for="penyelesaian" class="block text-sm font-medium text-gray-700">Penyelesaian</label>
                                <textarea id="penyelesaian" name="penyelesaian" rows="4" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Jelaskan bagaimana masalah ini diselesaikan" required></textarea>
                                <p class="mt-2 text-sm text-gray-500">
                                    Jelaskan langkah-langkah penyelesaian dan tindak lanjut yang dilakukan.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-save mr-2"></i> Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jenisMasalahSelect = document.getElementById('jenis_masalah');
        const jenisMasalahLainnyaContainer = document.getElementById('jenisMasalahLainnyaContainer');
        const jenisMasalahLainnyaInput = document.getElementById('jenis_masalah_lainnya');
        
        // Handle jenis masalah change
        jenisMasalahSelect.addEventListener('change', function() {
            if (this.value === 'lainnya') {
                jenisMasalahLainnyaContainer.classList.remove('hidden');
                jenisMasalahLainnyaInput.setAttribute('required', 'required');
            } else {
                jenisMasalahLainnyaContainer.classList.add('hidden');
                jenisMasalahLainnyaInput.removeAttribute('required');
            }
        });
        
        <?php if (session()->get('role') === 'admin') : ?>
        const kelasSelect = document.getElementById('kelas_id');
        const siswaSelect = document.getElementById('siswa_id');
        const loadingSpinner = document.getElementById('loadingSpinner');
        
        // Handle kelas change for admin
        kelasSelect.addEventListener('change', function() {
            const kelasId = this.value;
            
            if (kelasId) {
                // Clear the student dropdown
                siswaSelect.innerHTML = '<option value="">Pilih Siswa</option>';
                
                // Show loading spinner
                loadingSpinner.classList.remove('hidden');
                
                // Fetch students for the selected class
                fetch('<?= base_url('buku-kasus/get-siswa-by-kelas'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: `kelas_id=${kelasId}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
                })
                .then(response => response.json())
                .then(data => {
                    // Hide loading spinner
                    loadingSpinner.classList.add('hidden');
                    
                    // Populate the student dropdown
                    data.forEach(siswa => {
                        const option = document.createElement('option');
                        option.value = siswa.id;
                        option.textContent = siswa.nama;
                        siswaSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching students:', error);
                    loadingSpinner.classList.add('hidden');
                });
            }
        });
        <?php endif; ?>
    });
</script>
<?= $this->endSection(); ?>

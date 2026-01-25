<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>
Tambah Kasus Baru
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="w-full px-6 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-plus text-white text-xl"></i>
                    </div>
                    Tambah Kasus Baru
                </h1>
                <p class="mt-2 text-sm text-gray-600">Catat dan dokumentasikan kasus siswa dengan lengkap</p>
            </div>
            <a href="<?= base_url('buku-kasus') ?>" 
               class="inline-flex items-center gap-2 px-5 py-3 bg-white text-gray-700 font-semibold rounded-xl border-2 border-gray-300 hover:border-gray-400 hover:bg-gray-50 shadow-sm hover:shadow-md transform hover:-translate-y-0.5 transition-all duration-200">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>
        <nav class="mt-4 flex items-center space-x-2 text-sm">
            <a href="<?= base_url('admin') ?>" class="text-indigo-600 hover:text-indigo-800 font-medium">Dashboard</a>
            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
            <a href="<?= base_url('buku-kasus') ?>" class="text-indigo-600 hover:text-indigo-800 font-medium">Buku Kasus</a>
            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
            <span class="text-gray-700 font-medium">Tambah</span>
        </nav>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="mb-6 rounded-xl bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 p-4 shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800"><?= session()->getFlashdata('error') ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Validation Errors -->
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="mb-6 rounded-xl bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 p-4 shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-semibold text-red-800 mb-2">Terdapat kesalahan:</p>
                    <ul class="list-disc pl-5 space-y-1 text-sm text-red-700">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-md">
                    <i class="fas fa-file-alt text-white"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Form Tambah Kasus</h2>
                    <p class="text-xs text-gray-600">Lengkapi informasi di bawah ini dengan teliti</p>
                </div>
            </div>
        </div>

        <div class="p-6 md:p-8">
            <form action="<?= base_url('buku-kasus/simpan') ?>" method="POST" id="formTambahKasus" class="space-y-8">
                <?= csrf_field() ?>

                <!-- Quick guide -->
                <div class="rounded-xl bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-indigo-500 p-4 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-lightbulb text-white text-sm"></i>
                            </div>
                        </div>
                        <div>
                            <p class="font-semibold text-indigo-900 mb-2">Panduan singkat</p>
                            <ul class="space-y-1 text-sm text-indigo-800">
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-check-circle text-indigo-600 mt-0.5"></i>
                                    <span>Pilih Kelas untuk menampilkan daftar Nama Siswa.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-check-circle text-indigo-600 mt-0.5"></i>
                                    <span>Kolom bertanda <span class="text-rose-600 font-bold">*</span> wajib diisi.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Identitas Siswa -->
                <div class="rounded-xl bg-gradient-to-br from-gray-50 to-gray-100 border border-gray-200 p-6 shadow-sm">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-md">
                            <i class="fas fa-user-graduate text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Identitas Siswa</h3>
                            <p class="text-xs text-gray-600">Pilih kelas dan nama siswa terkait</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="kelas_nama" class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-school text-indigo-600 mr-1"></i>
                                Kelas <span class="text-rose-600">*</span>
                            </label>
                            <select id="kelas_nama" name="kelas_nama" required
                                    class="block w-full rounded-xl border-2 border-gray-300 bg-white px-4 py-3 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all font-medium text-gray-700">
                                <option value="">Pilih Kelas</option>
                                <?php foreach ($kelasList as $kelas): ?>
                                    <option value="<?= $kelas['nama'] ?>" <?= old('kelas_nama') == $kelas['nama'] ? 'selected' : '' ?>>
                                        <?= $kelas['nama'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                                <i class="fas fa-info-circle text-gray-400"></i>
                                Pilih kelas terlebih dahulu
                            </p>
                        </div>
                        <div>
                            <label for="siswa_id" class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-user text-indigo-600 mr-1"></i>
                                Nama Siswa <span class="text-rose-600">*</span>
                            </label>
                            <select id="siswa_id" name="siswa_id" required disabled
                                    class="block w-full rounded-xl border-2 border-gray-300 bg-white px-4 py-3 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all font-medium text-gray-700 disabled:bg-gray-100 disabled:cursor-not-allowed">
                                <option value="">Pilih kelas terlebih dahulu</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                                <i class="fas fa-info-circle text-gray-400"></i>
                                Daftar siswa otomatis muncul
                            </p>
                        </div>
                        <div>
                            <label for="tanggal_kejadian" class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-calendar text-indigo-600 mr-1"></i>
                                Tanggal Kejadian <span class="text-rose-600">*</span>
                            </label>
                            <input type="date" id="tanggal_kejadian" name="tanggal_kejadian" value="<?= old('tanggal_kejadian', date('Y-m-d')) ?>" required
                                   class="block w-full rounded-xl border-2 border-gray-300 bg-white px-4 py-3 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all font-medium text-gray-700" />
                            <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                                <i class="fas fa-info-circle text-gray-400"></i>
                                Tanggal terjadinya kasus
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Rincian Kasus -->
                <div class="rounded-xl bg-gradient-to-br from-gray-50 to-gray-100 border border-gray-200 p-6 shadow-sm">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center shadow-md">
                            <i class="fas fa-file-lines text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Deskripsi Kasus</h3>
                            <p class="text-xs text-gray-600">Jelaskan kronologi dan detail kejadian</p>
                        </div>
                    </div>
                    <div>
                        <label for="deskripsi_kasus" class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-align-left text-indigo-600 mr-1"></i>
                            Deskripsi Kasus <span class="text-rose-600">*</span>
                        </label>
                        <textarea id="deskripsi_kasus" name="deskripsi_kasus" rows="5" required
                                  placeholder="Jelaskan secara detail kejadian yang terjadi, termasuk kronologi, pihak yang terlibat, dan dampak yang ditimbulkan..."
                                  class="block w-full rounded-xl border-2 border-gray-300 bg-white px-4 py-3 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all font-medium text-gray-700 resize-none"><?= old('deskripsi_kasus') ?></textarea>
                        <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                            <i class="fas fa-info-circle text-gray-400"></i>
                            Sertakan kronologi singkat dan pihak yang terlibat (jika ada)
                        </p>
                    </div>
                </div>

                <!-- Tindakan -->
                <div class="rounded-xl bg-gradient-to-br from-gray-50 to-gray-100 border border-gray-200 p-6 shadow-sm">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-md">
                            <i class="fas fa-clipboard-check text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Tindakan yang Diambil</h3>
                            <p class="text-xs text-gray-600">Dokumentasi penanganan kasus</p>
                        </div>
                    </div>
                    <div>
                        <label for="tindakan_yang_diambil" class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-tasks text-indigo-600 mr-1"></i>
                            Tindakan yang Diambil
                        </label>
                        <textarea id="tindakan_yang_diambil" name="tindakan_yang_diambil" rows="4"
                                  placeholder="Uraikan tindakan yang telah diambil untuk menangani kasus ini (pemanggilan orang tua, konseling, teguran, dll)..."
                                  class="block w-full rounded-xl border-2 border-gray-300 bg-white px-4 py-3 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all font-medium text-gray-700 resize-none"><?= old('tindakan_yang_diambil') ?></textarea>
                        <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                            <i class="fas fa-info-circle text-gray-400"></i>
                            Misal: Pemanggilan orang tua, konseling, teguran lisan
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <a href="<?= base_url('buku-kasus') ?>" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-white text-gray-700 font-semibold rounded-xl border-2 border-gray-300 hover:border-gray-400 hover:bg-gray-50 shadow-sm hover:shadow-md transition-all">
                        <i class="fas fa-arrow-left"></i>
                        <span>Kembali</span>
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                        <i class="fas fa-save"></i>
                        <span>Simpan Kasus</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const kelasSelect = document.getElementById('kelas_nama');
    const siswaSelect = document.getElementById('siswa_id');

    kelasSelect.addEventListener('change', function() {
        const kelasNama = this.value;
        
        // Reset siswa dropdown
        siswaSelect.innerHTML = '<option value="">Loading...</option>';
        siswaSelect.disabled = true;

        if (kelasNama) {
            // Fetch siswa data using AJAX
            fetch('<?= base_url('buku-kasus/get-siswa-by-kelas') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'kelas_nama=' + encodeURIComponent(kelasNama) + '&<?= csrf_token() ?>=' + encodeURIComponent('<?= csrf_hash() ?>')
            })
            .then(response => response.json())
            .then(data => {
                siswaSelect.innerHTML = '<option value="">Pilih Siswa</option>';
                
                if (data && data.length > 0) {
                    data.forEach(siswa => {
                        const option = document.createElement('option');
                        option.value = siswa.id;
                        option.textContent = siswa.nama;
                        siswaSelect.appendChild(option);
                    });
                    siswaSelect.disabled = false;
                } else {
                    siswaSelect.innerHTML = '<option value="">Tidak ada siswa di kelas ini</option>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                siswaSelect.innerHTML = '<option value="">Error memuat data siswa</option>';
            });
        } else {
            siswaSelect.innerHTML = '<option value="">Pilih kelas terlebih dahulu</option>';
            siswaSelect.disabled = true;
        }
    });

    // Simple client validation hint styles using Tailwind
    document.getElementById('formTambahKasus').addEventListener('submit', function(e) {
        const requiredFields = ['tanggal_kejadian', 'kelas_nama', 'siswa_id', 'deskripsi_kasus'];
        let isValid = true;

        requiredFields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (!field.value.trim()) {
                field.classList.add('ring-1','ring-rose-500');
                isValid = false;
            } else {
                field.classList.remove('ring-1','ring-rose-500');
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Mohon lengkapi semua field yang wajib diisi (bertanda *)');
        }
    });
});
</script>
<?= $this->endSection() ?>

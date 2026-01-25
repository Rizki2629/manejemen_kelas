<?= $this->extend('admin/layout'); ?>

<?= $this->section('content'); ?>
<div class="min-h-screen bg-gray-50">
    <!-- Page Header with Breadcrumb -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-4">
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <a href="<?= base_url('/admin') ?>" class="text-gray-500 hover:text-gray-700 transition-colors">
                                    <i class="fas fa-home w-4 h-4"></i>
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <i class="fas fa-chevron-right text-gray-400 w-4 h-4"></i>
                                    <a href="<?= base_url('/admin/nilai') ?>" class="ml-1 text-sm font-medium text-gray-500 hover:text-gray-700">
                                        Nilai Siswa
                                    </a>
                                </div>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <i class="fas fa-chevron-right text-gray-400 w-4 h-4"></i>
                                    <span class="ml-1 text-sm font-medium text-gray-700">Tambah Nilai</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">
                        <i class="fas fa-calendar-alt mr-1"></i>
                        <?= date('d F Y') ?>
                    </span>
                    <span class="text-sm text-gray-400">•</span>
                    <span class="text-sm text-gray-500">
                        <i class="fas fa-clock mr-1"></i>
                        <?= date('H:i') ?> WIB
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Page Title Section -->
        <div class="mb-8">
            <div class="md:flex md:items-center md:justify-between">
                <div class="min-w-0 flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 sm:text-4xl">
                        <i class="fas fa-plus-circle text-green-600 mr-3"></i>
                        Tambah Nilai Siswa
                    </h1>
                    <p class="mt-2 text-lg text-gray-600">
                        Input nilai untuk mata pelajaran 
                        <span class="font-semibold text-blue-600">
                            <?= $mataPelajaranList[$selectedMapel] ?? $selectedMapel ?>
                        </span>
                    </p>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4">
                    <a href="<?= base_url('/admin/nilai') ?>?kelas=<?= $selectedKelas ?>&mapel=<?= $selectedMapel ?>" 
                       class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Form Container -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-edit text-blue-600 mr-2"></i>
                    Form Input Nilai
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Isi form di bawah ini untuk menambahkan nilai siswa
                </p>
            </div>

            <form action="<?= base_url('/admin/nilai/store') ?>" method="POST" class="p-6 space-y-8" id="gradeForm">
                <?= csrf_field() ?>
                
                <!-- Date Section - Moved to Top -->
                <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-lg p-6 border border-blue-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>
                        Tanggal Penilaian
                    </h3>
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">
                            Pilih tanggal pelaksanaan penilaian
                        </label>
                        <input type="date" 
                               name="tanggal" 
                               value="<?= date('Y-m-d') ?>" 
                               class="w-full px-4 py-3 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent rounded-lg shadow-sm transition-all duration-200 bg-white" 
                               required>
                    </div>
                </div>

                <!-- Configuration Section -->
                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-cog text-gray-600 mr-2"></i>
                        Konfigurasi Nilai
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Mata Pelajaran - Non-editable -->
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">
                                <i class="fas fa-book text-blue-600 mr-1"></i>
                                Mata Pelajaran
                            </label>
                            <div class="relative">
                                <input type="hidden" name="mata_pelajaran" value="<?= $selectedMapel ?>">
                                <input type="text" 
                                       value="<?= $mataPelajaranList[$selectedMapel] ?? $selectedMapel ?>" 
                                       class="w-full pl-4 pr-10 py-3 text-base border-gray-300 rounded-lg shadow-sm bg-gray-100 text-gray-700" 
                                       readonly>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Kelas - General display -->
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">
                                <i class="fas fa-users text-green-600 mr-1"></i>
                                Kelas
                            </label>
                            <?php if ($userRole === 'admin'): ?>
                            <div class="relative">
                                <select name="kelas" class="w-full pl-4 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent rounded-lg shadow-sm transition-all duration-200 bg-white hover:border-gray-400" required>
                                    <option value="">Pilih Kelas</option>
                                    <?php foreach ($allKelas as $kelas): ?>
                                        <option value="<?= $kelas ?>" <?= $selectedKelas === $kelas ? 'selected' : '' ?>>
                                            <?= $kelas ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                            <?php else: ?>
                            <input type="hidden" name="kelas" value="<?= $selectedKelas ?>">
                            <div class="relative">
                                <input type="text" value="<?= $selectedKelas ?>" class="w-full pl-4 pr-10 py-3 text-base border-gray-300 rounded-lg shadow-sm bg-gray-100 text-gray-700" readonly>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Jenis Nilai -->
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">
                                <i class="fas fa-tags text-purple-600 mr-1"></i>
                                Jenis Nilai
                            </label>
                            <div class="relative">
                                <select name="jenis_nilai" class="w-full pl-4 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent rounded-lg shadow-sm transition-all duration-200 bg-white hover:border-gray-400" required>
                                    <option value="">Pilih Jenis Nilai</option>
                                    <?php foreach ($jenisNilaiList as $code => $name): ?>
                                        <option value="<?= $code ?>"><?= $name ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TP/Materi Section with Dropdown -->
                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-lg p-6 border border-yellow-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-lightbulb text-yellow-600 mr-2"></i>
                        Pilih Materi Pembelajaran
                    </h3>
                    
                    <div class="space-y-6">
                        <!-- TP Dropdown with Add Button -->
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">
                                <i class="fas fa-list text-blue-600 mr-1"></i>
                                Pilih Materi Pembelajaran
                            </label>
                            <div class="flex space-x-3">
                                <div class="flex-1 relative">
                                    <select name="tp_id" id="tpSelect" class="w-full pl-4 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent rounded-lg shadow-sm transition-all duration-200 bg-white hover:border-gray-400" required>
                                        <option value="">Pilih TP...</option>
                                        <!-- This will be populated from database -->
                                        <!-- Example options: -->
                                        <option value="1">TP 1.1 - Ciri-ciri Makhluk Hidup</option>
                                        <option value="2">TP 1.2 - Klasifikasi Makhluk Hidup</option>
                                        <option value="3">TP 2.1 - Sistem Pernafasan</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                                <button type="button" 
                                        id="tambahTpBtn"
                                        onclick="openTpModal()" 
                                        class="px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 flex items-center">
                                    <i class="fas fa-plus mr-2"></i>
                                    Tambah TP
                                </button>
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i>
                                <span>Pilih TP yang sudah ada atau klik "Tambah TP" untuk membuat yang baru</span>
                            </div>
                        </div>

                        <!-- Materi Pembelajaran Display -->
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">
                                <i class="fas fa-book-open text-green-600 mr-1"></i>
                                Materi Pembelajaran
                            </label>
                            <div id="materiPembelajaran" class="w-full px-4 py-3 text-base border-gray-300 rounded-lg shadow-sm bg-gray-50 text-gray-700 min-h-[100px]">
                                <div class="text-gray-500 italic">Pilih TP untuk melihat materi pembelajaran...</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Student Grades Table -->
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <i class="fas fa-list-alt text-blue-600 mr-2"></i>
                                Input Nilai Siswa
                            </h3>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-users mr-1"></i>
                                    <?= count($students) ?> Siswa
                                </span>
                                <button type="button" onclick="fillAllGrades()" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 hover:bg-green-200 transition-colors">
                                    <i class="fas fa-magic mr-1"></i>
                                    Isi Semua
                                </button>
                                <button type="button" onclick="clearAllGrades()" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 hover:bg-red-200 transition-colors">
                                    <i class="fas fa-eraser mr-1"></i>
                                    Hapus Semua
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        <div class="flex items-center space-x-1">
                                            <i class="fas fa-hashtag text-blue-600"></i>
                                            <span>No</span>
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        <div class="flex items-center space-x-1">
                                            <i class="fas fa-user text-green-600"></i>
                                            <span>Nama Siswa</span>
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        <div class="flex items-center space-x-1">
                                            <i class="fas fa-id-card text-purple-600"></i>
                                            <span>NIPD</span>
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        <div class="flex items-center justify-center space-x-1">
                                            <i class="fas fa-star text-yellow-600"></i>
                                            <span>Nilai (0-100)</span>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php $no = 1; foreach ($students as $student): ?>
                                <tr class="hover:bg-blue-50 transition-colors duration-200 group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-r from-blue-100 to-blue-200 rounded-full group-hover:from-blue-200 group-hover:to-blue-300 transition-all duration-200">
                                            <span class="text-sm font-bold text-blue-800"><?= $no++ ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-shrink-0">
                                                <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full flex items-center justify-center shadow-lg">
                                                    <span class="text-white font-bold text-lg">
                                                        <?= substr($student['nama'], 0, 1) ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-semibold text-gray-900 truncate">
                                                    <?= $student['nama'] ?>
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    ID: <?= $student['id'] ?>
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i class="fas fa-id-card text-gray-400 mr-2"></i>
                                            <span class="text-sm text-gray-900 font-medium"><?= $student['nipd'] ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <input type="number" 
                                                   name="nilai[<?= $student['id'] ?>]" 
                                                   min="0" 
                                                   max="100" 
                                                   step="0.1"
                                                   class="w-24 px-3 py-2 text-center text-lg font-semibold border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 hover:border-blue-400" 
                                                   placeholder="0-100"
                                                   oninput="validateGrade(this)">
                                            <input type="hidden" name="siswa_id[]" value="<?= $student['id'] ?>">
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Panduan Penilaian Section -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-6 border border-green-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-green-600 mr-2"></i>
                        Panduan Penilaian
                    </h3>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span>Nilai rentang 0-100</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span>Dapat menggunakan desimal (contoh: 85.5)</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span>Kosongkan jika siswa tidak mengikuti</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span>Gunakan tombol "Isi Semua" untuk nilai sama</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="flex items-center space-x-4">
                        <button type="button" 
                                onclick="window.history.back()" 
                                class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali
                        </button>
                        <button type="reset" 
                                onclick="clearAllGrades()" 
                                class="inline-flex items-center px-6 py-3 border border-red-300 shadow-sm text-base font-medium rounded-lg text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                            <i class="fas fa-undo mr-2"></i>
                            Reset
                        </button>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button type="submit" 
                                class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Nilai
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-sm w-full mx-4 shadow-xl">
        <div class="flex items-center justify-center mb-4">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        </div>
        <p class="text-center text-gray-600 font-medium">Menyimpan data nilai...</p>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed top-4 right-4 z-50 hidden">
    <div class="bg-white border-l-4 border-green-500 rounded-lg shadow-lg p-4 max-w-md">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i id="toastIcon" class="fas fa-check-circle text-green-500"></i>
            </div>
            <div class="ml-3">
                <p id="toastMessage" class="text-sm font-medium text-gray-900"></p>
            </div>
            <div class="ml-auto pl-3">
                <button onclick="hideToast()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Grade validation function
function validateGrade(input) {
    const value = parseFloat(input.value);
    const parentRow = input.closest('tr');
    
    if (isNaN(value) || value < 0 || value > 100) {
        input.classList.add('border-red-500', 'bg-red-50');
        input.classList.remove('border-gray-300', 'border-green-500', 'bg-green-50');
        parentRow.classList.add('bg-red-50');
        parentRow.classList.remove('bg-green-50');
    } else if (value >= 0 && value <= 100) {
        input.classList.add('border-green-500', 'bg-green-50');
        input.classList.remove('border-red-500', 'bg-red-50', 'border-gray-300');
        parentRow.classList.add('bg-green-50');
        parentRow.classList.remove('bg-red-50');
    } else {
        input.classList.remove('border-red-500', 'bg-red-50', 'border-green-500', 'bg-green-50');
        input.classList.add('border-gray-300');
        parentRow.classList.remove('bg-red-50', 'bg-green-50');
    }
}

// Fill all grades with same value
function fillAllGrades() {
    const value = prompt('Masukkan nilai untuk semua siswa (0-100):', '85');
    if (value !== null && value !== '') {
        const numValue = parseFloat(value);
        if (!isNaN(numValue) && numValue >= 0 && numValue <= 100) {
            const inputs = document.querySelectorAll('input[name^="nilai["]');
            inputs.forEach(input => {
                input.value = numValue;
                validateGrade(input);
            });
            showToast('Nilai berhasil diisi untuk semua siswa', 'success');
        } else {
            showToast('Nilai harus antara 0-100', 'error');
        }
    }
}

// Clear all grades
function clearAllGrades() {
    if (confirm('Apakah Anda yakin ingin menghapus semua nilai?')) {
        const inputs = document.querySelectorAll('input[name^="nilai["]');
        inputs.forEach(input => {
            input.value = '';
            input.classList.remove('border-red-500', 'bg-red-50', 'border-green-500', 'bg-green-50');
            input.classList.add('border-gray-300');
            const parentRow = input.closest('tr');
            parentRow.classList.remove('bg-red-50', 'bg-green-50');
        });
        showToast('Semua nilai berhasil dihapus', 'success');
    }
}

// Show toast notification
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    const toastIcon = document.getElementById('toastIcon');
    
    toastMessage.textContent = message;
    
    if (type === 'success') {
        toastIcon.className = 'fas fa-check-circle text-green-500';
        toast.querySelector('div').className = 'bg-white border-l-4 border-green-500 rounded-lg shadow-lg p-4 max-w-md';
    } else {
        toastIcon.className = 'fas fa-exclamation-circle text-red-500';
        toast.querySelector('div').className = 'bg-white border-l-4 border-red-500 rounded-lg shadow-lg p-4 max-w-md';
    }
    
    toast.classList.remove('hidden');
    setTimeout(() => {
        hideToast();
    }, 3000);
}

// Hide toast notification
function hideToast() {
    document.getElementById('toast').classList.add('hidden');
}

// Show loading overlay
function showLoading() {
    document.getElementById('loadingOverlay').classList.remove('hidden');
    document.getElementById('loadingOverlay').classList.add('flex');
}

// Hide loading overlay
function hideLoading() {
    document.getElementById('loadingOverlay').classList.add('hidden');
    document.getElementById('loadingOverlay').classList.remove('flex');
}

// Form submission handling
document.querySelector('form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validate all inputs
    const inputs = document.querySelectorAll('input[name^="nilai["]');
    let hasError = false;
    let filledCount = 0;
    
    inputs.forEach(input => {
        if (input.value.trim() !== '') {
            const value = parseFloat(input.value);
            if (isNaN(value) || value < 0 || value > 100) {
                hasError = true;
            } else {
                filledCount++;
            }
        }
    });
    
    if (hasError) {
        showToast('Terdapat nilai yang tidak valid. Pastikan semua nilai antara 0-100.', 'error');
        return;
    }
    
    if (filledCount === 0) {
        showToast('Minimal satu nilai harus diisi.', 'error');
        return;
    }
    
    // Show loading and submit
    showLoading();
    setTimeout(() => {
        this.submit();
    }, 1000);
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl + S to save
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        document.querySelector('form').dispatchEvent(new Event('submit'));
    }
    
    // Ctrl + R to reset
    if (e.ctrlKey && e.key === 'r') {
        e.preventDefault();
        clearAllGrades();
    }
    
    // Escape to go back
    if (e.key === 'Escape') {
        window.history.back();
    }
});

// Auto-focus first input
document.addEventListener('DOMContentLoaded', function() {
    const firstInput = document.querySelector('input[name^="nilai["]');
    if (firstInput) {
        firstInput.focus();
    }
});
</script>

<style>
/* Custom styles for enhanced UI */
.table-container {
    max-height: 600px;
    overflow-y: auto;
}

.table-container::-webkit-scrollbar {
    width: 8px;
}

.table-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.table-container::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 4px;
}

.table-container::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}

/* Animation for grade input */
input[name^="nilai["] {
    transition: all 0.3s ease;
}

input[name^="nilai["]:focus {
    transform: scale(1.02);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Hover effects */
.group:hover .group-hover\:from-blue-200 {
    --tw-gradient-from: #dbeafe;
}

.group:hover .group-hover\:to-blue-300 {
    --tw-gradient-to: #93c5fd;
}

/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }
    
    .print-only {
        display: block !important;
    }
    
    body {
        -webkit-print-color-adjust: exact;
    }
}
</style>

<!-- Modal for Adding New TP -->
<div id="tpModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[9999]">
    <div class="bg-white rounded-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-yellow-50 to-orange-50">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-plus-circle text-yellow-600 mr-2"></i>
                Tambah Tujuan Pembelajaran Baru
            </h3>
        </div>
        
        <form id="tpForm" class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kelas -->
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700">
                        <i class="fas fa-users text-blue-600 mr-1"></i>
                        Kelas
                    </label>
                    <input type="text" 
                           id="modal_kelas" 
                           class="w-full px-4 py-3 text-base border-gray-300 rounded-lg shadow-sm bg-gray-100 text-gray-700" 
                           readonly>
                </div>

                <!-- Mata Pelajaran -->
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700">
                        <i class="fas fa-book text-green-600 mr-1"></i>
                        Mata Pelajaran
                    </label>
                    <input type="text" 
                           id="modal_mapel" 
                           class="w-full px-4 py-3 text-base border-gray-300 rounded-lg shadow-sm bg-gray-100 text-gray-700" 
                           readonly>
                </div>
            </div>

            <!-- Kode TP -->
            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700">
                    <i class="fas fa-code text-purple-600 mr-1"></i>
                    Kode TP
                </label>
                <input type="text" 
                       id="modal_kode_tp" 
                       class="w-full px-4 py-3 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent rounded-lg shadow-sm transition-all duration-200 bg-white" 
                       placeholder="Contoh: TP 1.1"
                       required>
            </div>

            <!-- Materi Pembelajaran -->
            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700">
                    <i class="fas fa-book-open text-orange-600 mr-1"></i>
                    Materi Pembelajaran
                </label>
                <textarea id="modal_materi_pembelajaran" 
                          rows="4"
                          class="w-full px-4 py-3 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent rounded-lg shadow-sm transition-all duration-200 bg-white resize-none" 
                          placeholder="Contoh: Ciri-ciri makhluk hidup, klasifikasi berdasarkan habitat, cara berkembang biak, dan adaptasi lingkungan."
                          required></textarea>
            </div>

            <!-- Deskripsi Singkat -->
            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700">
                    <i class="fas fa-file-text text-indigo-600 mr-1"></i>
                    Deskripsi Singkat
                </label>
                <textarea id="modal_deskripsi_singkat" 
                          rows="2"
                          class="w-full px-4 py-3 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent rounded-lg shadow-sm transition-all duration-200 bg-white resize-none" 
                          placeholder="Contoh: Ciri-ciri makhluk hidup dan klasifikasinya"
                          required></textarea>
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-info-circle mr-1"></i>
                    Ringkasan singkat untuk ditampilkan dalam dropdown
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <button type="button" 
                        onclick="closeTpModal()" 
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </button>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-yellow-600 to-orange-600 text-white rounded-lg hover:from-yellow-700 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-all duration-200">
                    <i class="fas fa-save mr-2"></i>
                    Simpan TP
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Enhanced JavaScript for TP Management
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Initializing TP Management');
    
    // Initialize existing functionality
    initializeTpDropdown();
    initializeModalFunctions();
    
    // Add additional event listener for the Tambah TP button
    const tambahTpBtn = document.getElementById('tambahTpBtn');
    if (tambahTpBtn) {
        tambahTpBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Tambah TP button clicked');
            openTpModal();
        });
        console.log('Tambah TP button event listener added');
    } else {
        console.error('Tambah TP button not found');
    }
    
    // Test modal existence
    const modal = document.getElementById('tpModal');
    if (modal) {
        console.log('TP Modal found');
    } else {
        console.error('TP Modal not found');
    }
});

// TP Dropdown Management
function initializeTpDropdown() {
    console.log('Initializing TP Dropdown');
    
    const tpSelect = document.getElementById('tpSelect');
    const materiPembelajaran = document.getElementById('materiPembelajaran');
    
    if (!tpSelect) {
        console.error('tpSelect element not found');
        return;
    }
    
    if (!materiPembelajaran) {
        console.error('materiPembelajaran element not found');
        return;
    }
    
    console.log('TP elements found successfully');
    
    // Sample TP data - This should come from database
    const tpData = {
        '1': {
            kode: 'TP 1.1',
            materi: 'Ciri-ciri makhluk hidup, klasifikasi berdasarkan habitat, cara berkembang biak, dan adaptasi lingkungan. Mengamati perbedaan antara makhluk hidup dan benda tak hidup melalui pengamatan langsung.'
        },
        '2': {
            kode: 'TP 1.2',
            materi: 'Klasifikasi makhluk hidup berdasarkan ciri-ciri yang diamati seperti cara bergerak, cara makan, tempat hidup, dan cara berkembang biak. Mengenal berbagai jenis hewan dan tumbuhan di sekitar.'
        },
        '3': {
            kode: 'TP 2.1',
            materi: 'Sistem pernafasan pada manusia, hewan, dan tumbuhan. Organ-organ pernafasan dan fungsinya. Cara menjaga kesehatan organ pernafasan dan dampak polusi udara.'
        }
    };
    
    tpSelect.addEventListener('change', function() {
        const selectedId = this.value;
        console.log('TP Selection changed to:', selectedId);
        
        if (selectedId && tpData[selectedId]) {
            const tp = tpData[selectedId];
            console.log('Found TP data:', tp);
            
            materiPembelajaran.innerHTML = `
                <div class="space-y-2">
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            ${tp.kode}
                        </span>
                    </div>
                    <p class="text-gray-700">${tp.materi}</p>
                </div>
            `;
            console.log('Materi Pembelajaran updated successfully');
        } else {
            console.log('No TP data found for ID:', selectedId);
            materiPembelajaran.innerHTML = '<div class="text-gray-500 italic">Pilih TP untuk melihat materi pembelajaran...</div>';
        }
    });
    
    console.log('TP Dropdown event listener added successfully');
}

// Modal Management
function initializeModalFunctions() {
    const modal = document.getElementById('tpModal');
    const form = document.getElementById('tpForm');
    
    if (!modal || !form) {
        console.error('Modal or form not found in initializeModalFunctions');
        return;
    }
    
    console.log('Initializing modal functions');
    
    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Form submitted');
        saveTp();
    });
    
    // Close modal on backdrop click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            console.log('Backdrop clicked');
            closeTpModal();
        }
    });
    
    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            console.log('Escape key pressed');
            closeTpModal();
        }
    });
    
    console.log('Modal functions initialized');
}

function openTpModal() {
    console.log('Opening TP Modal'); // Debug log
    
    const modal = document.getElementById('tpModal');
    const modalKelas = document.getElementById('modal_kelas');
    const modalMapel = document.getElementById('modal_mapel');
    
    if (!modal) {
        console.error('Modal not found');
        return;
    }
    
    // Get current values safely
    let selectedKelas = '';
    let selectedMapel = '';
    
    // Try to get kelas value
    const kelasSelect = document.querySelector('select[name="kelas"]');
    if (kelasSelect) {
        const selectedOption = kelasSelect.options[kelasSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            // Extract grade level from class name (e.g., "5A" -> "5")
            const gradeLevel = selectedOption.value.match(/\d+/);
            selectedKelas = gradeLevel ? 'Tingkat ' + gradeLevel[0] : '';
        }
    } else {
        // For readonly input (non-admin)
        const kelasInput = document.querySelector('input[type="hidden"][name="kelas"]');
        if (kelasInput) {
            // Extract grade level from class value (e.g., "5A" -> "5")
            const gradeLevel = kelasInput.value.match(/\d+/);
            selectedKelas = gradeLevel ? 'Tingkat ' + gradeLevel[0] : '';
        }
    }
    
    // Try to get mata pelajaran value
    const mapelInput = document.querySelector('input[type="hidden"][name="mata_pelajaran"]');
    if (mapelInput) {
        const mapelCode = mapelInput.value;
        // Get the display text from the readonly input
        const mapelDisplayInput = mapelInput.parentElement.querySelector('input[readonly]');
        selectedMapel = mapelDisplayInput ? mapelDisplayInput.value : mapelCode;
    }
    
    console.log('Selected Kelas:', selectedKelas);
    console.log('Selected Mapel:', selectedMapel);
    
    // Set modal values
    if (modalKelas) modalKelas.value = selectedKelas;
    if (modalMapel) modalMapel.value = selectedMapel;
    
    // Show modal with improved visibility
    modal.style.display = 'flex';
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Add body class to prevent scrolling
    document.body.style.overflow = 'hidden';
    
    // Focus on first input after a small delay
    setTimeout(() => {
        const firstInput = document.getElementById('modal_kode_tp');
        if (firstInput) {
            firstInput.focus();
        }
    }, 100);
}

function closeTpModal() {
    console.log('Closing TP Modal'); // Debug log
    
    const modal = document.getElementById('tpModal');
    if (!modal) return;
    
    // Hide modal
    modal.style.display = 'none';
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    
    // Restore body scrolling
    document.body.style.overflow = '';
    
    // Reset form
    const form = document.getElementById('tpForm');
    if (form) {
        form.reset();
    }
}

function saveTp() {
    const kodeTP = document.getElementById('modal_kode_tp').value;
    const materiPembelajaran = document.getElementById('modal_materi_pembelajaran').value;
    const deskripsiSingkat = document.getElementById('modal_deskripsi_singkat').value;
    
    if (!kodeTP || !materiPembelajaran || !deskripsiSingkat) {
        showToast('Semua field harus diisi!', 'error');
        return;
    }
    
    // Here you would typically send AJAX request to save TP to database
    // For now, we'll simulate success and add to dropdown
    
    // Simulate API call
    setTimeout(() => {
        // Add new option to dropdown
        const tpSelect = document.getElementById('tpSelect');
        const newOption = document.createElement('option');
        const newId = Date.now().toString(); // Simple ID generation
        newOption.value = newId;
        newOption.textContent = `${kodeTP} - ${deskripsiSingkat}`;
        tpSelect.appendChild(newOption);
        
        // Select the new option
        tpSelect.value = newId;
        
        // Update materi pembelajaran display
        const materiDisplay = document.getElementById('materiPembelajaran');
        materiDisplay.innerHTML = `
            <div class="space-y-2">
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        ${kodeTP}
                    </span>
                </div>
                <p class="text-gray-700">${materiPembelajaran}</p>
            </div>
        `;
        
        // Close modal
        closeTpModal();
        
        // Show success message
        showToast('TP berhasil ditambahkan!', 'success');
    }, 1000);
}

// Add to existing JavaScript functions
// Make sure these functions are available from the existing code
</script>

<?= $this->endSection() ?>

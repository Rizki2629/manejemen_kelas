<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<!-- Page Header - Mobile Optimized -->
<div class="mb-6 sm:mb-8 lg:mb-10 px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-4 sm:gap-6">
        <div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-3">
                <i class="fas fa-list-alt text-blue-600 mr-3 sm:mr-4 text-2xl sm:text-3xl lg:text-4xl"></i>Data Tujuan Pembelajaran
            </h1>
            <p class="text-base sm:text-lg text-gray-600">Kelola data Tujuan Pembelajaran (TP) untuk setiap mata pelajaran</p>
        </div>
        
        <!-- Action Buttons - Mobile Optimized -->
        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
            <button 
                onclick="openAddTPModal()"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2.5 sm:py-3 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center space-x-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 text-sm sm:text-base touch-manipulation">
                <i class="fas fa-plus"></i>
                <span>Tambah TP Baru</span>
            </button>
            
            <button 
                onclick="refreshData()"
                class="bg-green-600 hover:bg-green-700 text-white px-4 sm:px-6 py-2.5 sm:py-3 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center space-x-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 text-sm sm:text-base touch-manipulation">
                <i class="fas fa-sync-alt"></i>
                <span>Refresh</span>
            </button>
        </div>
    </div>
</div>

<!-- Filter Section - Mobile Optimized -->
<div class="px-4 sm:px-6 lg:px-8 mb-6 sm:mb-8">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Kelas Filter -->
            <div>
                <label for="kelas" class="block text-sm font-semibold text-gray-700 mb-2">Kelas</label>
                <select name="kelas" id="kelas" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base">
                    <option value="">Semua Kelas</option>
                    <option value="1">Kelas 1</option>
                    <option value="2">Kelas 2</option>
                    <option value="3">Kelas 3</option>
                    <option value="4">Kelas 4</option>
                    <option value="5">Kelas 5</option>
                    <option value="6">Kelas 6</option>
                </select>
            </div>

            <!-- Mata Pelajaran Filter -->
            <div>
                <label for="mapel" class="block text-sm font-semibold text-gray-700 mb-2">Mata Pelajaran</label>
                <select name="mapel" id="mapel" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base">
                    <option value="">Semua Mapel</option>
                    <option value="Pendidikan Agama">Pendidikan Agama</option>
                    <option value="Pendidikan Pancasila">Pendidikan Pancasila</option>
                    <option value="Bahasa Indonesia">Bahasa Indonesia</option>
                    <option value="Matematika">Matematika</option>
                    <option value="Ilmu Pengetahuan Alam dan Sosial">Ilmu Pengetahuan Alam dan Sosial</option>
                    <option value="Seni Rupa">Seni Rupa</option>
                    <option value="Pendidikan Jasmani Olahraga dan Kesenian">Pendidikan Jasmani Olahraga dan Kesenian</option>
                    <option value="Pendidikan Lingkungan dan Budaya Jakarta">Pendidikan Lingkungan dan Budaya Jakarta</option>
                    <option value="Bahasa Inggris">Bahasa Inggris</option>
                    <option value="Coding">Coding</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="sm:col-span-2 lg:col-span-2 flex flex-col sm:flex-row gap-2 sm:gap-3 sm:items-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center space-x-2 shadow-lg hover:shadow-xl text-sm sm:text-base touch-manipulation">
                    <i class="fas fa-search"></i>
                    <span>Filter</span>
                </button>
                
                <a href="/admin/nilai/data-tp" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2.5 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center space-x-2 shadow-lg hover:shadow-xl text-sm sm:text-base touch-manipulation">
                    <i class="fas fa-times"></i>
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Data Table - Mobile Optimized -->
<div class="px-4 sm:px-6 lg:px-8 mb-8">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <!-- Table Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-4 sm:px-6 py-4">
            <h3 class="text-lg sm:text-xl font-bold text-white">Data Tujuan Pembelajaran</h3>
            <p class="text-blue-100 text-sm mt-1">Kelola TP untuk setiap mata pelajaran dan kelas</p>
        </div>
        
        <!-- Table Content -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kelas</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Mata Pelajaran</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kode TP</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Deskripsi TP</th>
                        <th class="px-4 sm:px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Sample Data -->
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900">1</td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Kelas 1
                            </span>
                        </td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">Matematika</td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-600">TP.1.MTK.001</td>
                        <td class="px-4 sm:px-6 py-4 text-sm text-gray-900">Siswa dapat mengenal bilangan 1-10</td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <button onclick="editTP(1)" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200 flex items-center space-x-1 touch-manipulation">
                                    <i class="fas fa-edit text-xs"></i>
                                    <span>Edit</span>
                                </button>
                                <button onclick="deleteTP(1)" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200 flex items-center space-x-1 touch-manipulation">
                                    <i class="fas fa-trash text-xs"></i>
                                    <span>Hapus</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- More sample data -->
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900">2</td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Kelas 2
                            </span>
                        </td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">Bahasa Indonesia</td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-600">TP.2.BI.001</td>
                        <td class="px-4 sm:px-6 py-4 text-sm text-gray-900">Siswa dapat membaca kata sederhana</td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <button onclick="editTP(2)" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200 flex items-center space-x-1 touch-manipulation">
                                    <i class="fas fa-edit text-xs"></i>
                                    <span>Edit</span>
                                </button>
                                <button onclick="deleteTP(2)" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200 flex items-center space-x-1 touch-manipulation">
                                    <i class="fas fa-trash text-xs"></i>
                                    <span>Hapus</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Empty State -->
        <div id="emptyState" class="hidden text-center py-12">
            <div class="max-w-md mx-auto">
                <i class="fas fa-list-alt text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Data TP</h3>
                <p class="text-gray-600 mb-6">Tambahkan Tujuan Pembelajaran pertama untuk mata pelajaran Anda</p>
                <button onclick="openAddTPModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200">
                    Tambah TP Pertama
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit TP Modal -->
<div id="tpModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-screen overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 id="modalTitle" class="text-xl font-bold text-gray-900">Tambah Tujuan Pembelajaran</h3>
                <button onclick="closeTPModal()" class="text-gray-400 hover:text-gray-600 p-2 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>
        
        <form id="tpForm" class="p-6 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Kelas -->
                <div>
                    <label for="modalKelas" class="block text-sm font-semibold text-gray-700 mb-2">Kelas <span class="text-red-500">*</span></label>
                    <select id="modalKelas" name="kelas" required class="w-full px-3 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Pilih Kelas</option>
                        <option value="1">Kelas 1</option>
                        <option value="2">Kelas 2</option>
                        <option value="3">Kelas 3</option>
                        <option value="4">Kelas 4</option>
                        <option value="5">Kelas 5</option>
                        <option value="6">Kelas 6</option>
                    </select>
                </div>

                <!-- Mata Pelajaran -->
                <div>
                    <label for="modalMapel" class="block text-sm font-semibold text-gray-700 mb-2">Mata Pelajaran <span class="text-red-500">*</span></label>
                    <select id="modalMapel" name="mata_pelajaran" required class="w-full px-3 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Pilih Mata Pelajaran</option>
                        <option value="Pendidikan Agama">Pendidikan Agama</option>
                        <option value="Pendidikan Pancasila">Pendidikan Pancasila</option>
                        <option value="Bahasa Indonesia">Bahasa Indonesia</option>
                        <option value="Matematika">Matematika</option>
                        <option value="Ilmu Pengetahuan Alam dan Sosial">Ilmu Pengetahuan Alam dan Sosial</option>
                        <option value="Seni Rupa">Seni Rupa</option>
                        <option value="Pendidikan Jasmani Olahraga dan Kesenian">Pendidikan Jasmani Olahraga dan Kesenian</option>
                        <option value="Pendidikan Lingkungan dan Budaya Jakarta">Pendidikan Lingkungan dan Budaya Jakarta</option>
                        <option value="Bahasa Inggris">Bahasa Inggris</option>
                        <option value="Coding">Coding</option>
                    </select>
                </div>
            </div>

            <!-- Kode TP -->
            <div>
                <label for="modalKodeTP" class="block text-sm font-semibold text-gray-700 mb-2">Kode TP <span class="text-red-500">*</span></label>
                <input type="text" id="modalKodeTP" name="kode_tp" required 
                       placeholder="Contoh: TP.1.MTK.001"
                       class="w-full px-3 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">Format: TP.[Kelas].[Singkatan Mapel].[Nomor urut]</p>
            </div>

            <!-- Deskripsi TP -->
            <div>
                <label for="modalDeskripsi" class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Tujuan Pembelajaran <span class="text-red-500">*</span></label>
                <textarea id="modalDeskripsi" name="deskripsi" required rows="4"
                          placeholder="Jelaskan tujuan pembelajaran yang ingin dicapai..."
                          class="w-full px-3 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 pt-4 border-t border-gray-200">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center space-x-2 flex-1">
                    <i class="fas fa-save"></i>
                    <span>Simpan</span>
                </button>
                <button type="button" onclick="closeTPModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center space-x-2 flex-1">
                    <i class="fas fa-times"></i>
                    <span>Batal</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript -->
<script>
function openAddTPModal() {
    document.getElementById('modalTitle').textContent = 'Tambah Tujuan Pembelajaran';
    document.getElementById('tpForm').reset();
    document.getElementById('tpModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function editTP(id) {
    document.getElementById('modalTitle').textContent = 'Edit Tujuan Pembelajaran';
    // Here you would load the TP data for editing
    document.getElementById('tpModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeTPModal() {
    document.getElementById('tpModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function deleteTP(id) {
    if (confirm('Apakah Anda yakin ingin menghapus Tujuan Pembelajaran ini?')) {
        // Here you would send delete request
        alert('TP berhasil dihapus');
    }
}

function refreshData() {
    window.location.reload();
}

// Close modal when clicking outside
document.getElementById('tpModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeTPModal();
    }
});

// Auto-generate kode TP
document.getElementById('modalKelas').addEventListener('change', generateKodeTP);
document.getElementById('modalMapel').addEventListener('change', generateKodeTP);

function generateKodeTP() {
    const kelas = document.getElementById('modalKelas').value;
    const mapel = document.getElementById('modalMapel').value;
    
    if (kelas && mapel) {
        const mapelCode = {
            'Pendidikan Agama': 'AGM',
            'Pendidikan Pancasila': 'PPKN',
            'Bahasa Indonesia': 'BI',
            'Matematika': 'MTK',
            'Ilmu Pengetahuan Alam dan Sosial': 'IPAS',
            'Seni Rupa': 'SRP',
            'Pendidikan Jasmani Olahraga dan Kesenian': 'PJOK',
            'Pendidikan Lingkungan dan Budaya Jakarta': 'PLBJ',
            'Bahasa Inggris': 'ENG',
            'Coding': 'CDG',
            // Legacy aliases support
            'Agama': 'AGM',
            'PKn': 'PPKN',
            'PPKN': 'PPKN',
            'IPA': 'IPAS',
            'IPS': 'IPAS',
            'IPAS': 'IPAS',
            'Seni Budaya': 'SRP',
            'Olahraga': 'PJOK'
        };
        
        const kodeTP = `TP.${kelas}.${mapelCode[mapel] || 'XXX'}.001`;
        document.getElementById('modalKodeTP').value = kodeTP;
    }
}
</script>

<?= $this->endSection() ?>

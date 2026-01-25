<!-- app/Views/buku_kasus/index.php -->
<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>
<div class="px-4 py-5 sm:px-6">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold leading-6 text-gray-900 flex items-center">
                <i class="fas fa-book text-indigo-600 mr-2"></i>
                Buku Kasus Siswa
                <span class="ml-2 px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                    <?= count($kasus); ?> Kasus
                </span>
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Catatan pelanggaran dan masalah siswa
            </p>
        </div>
        <a href="<?= base_url('buku-kasus/tambah'); ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
            <i class="fas fa-plus mr-2"></i>
            Tambah Kasus Baru
        </a>
    </div>
</div>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('success')) : ?>
    <div class="mx-4 mt-4 px-4 py-2 bg-green-100 border-l-4 border-green-500 text-green-700 rounded shadow">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <p><?= session()->getFlashdata('success'); ?></p>
        </div>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="mx-4 mt-4 px-4 py-2 bg-red-100 border-l-4 border-red-500 text-red-700 rounded shadow">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <p><?= session()->getFlashdata('error'); ?></p>
        </div>
    </div>
<?php endif; ?>

<!-- Search and Filter -->
<div class="p-4">
    <div class="flex flex-col md:flex-row gap-4 mt-2 mb-4">
        <div class="w-full md:w-1/2 lg:w-1/3">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input id="searchInput" type="text" class="pl-10 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Cari nama siswa...">
            </div>
        </div>
        <div class="w-full md:w-1/2 lg:w-1/3">
            <select id="filterJenisMasalah" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="">Semua Jenis Masalah</option>
                <option value="tidak mengerjakan pr">Tidak Mengerjakan PR</option>
                <option value="mengerjakan PR di sekolah">Mengerjakan PR di sekolah</option>
                <option value="berkelahi di kelas">Berkelahi di kelas</option>
                <option value="lainnya">Lainnya</option>
            </select>
        </div>
    </div>
</div>

<!-- Table -->
<div class="flex flex-col px-4">
    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Masalah</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guru Pelapor</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="kasusTableBody" class="bg-white divide-y divide-gray-200">
                        <?php if (empty($kasus)) : ?>
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                    <div class="flex flex-col items-center justify-center py-4">
                                        <i class="fas fa-folder-open text-4xl text-gray-300 mb-3"></i>
                                        <p>Belum ada data kasus</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php $no = 1; ?>
                            <?php foreach ($kasus as $k) : ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $no++; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= date('d/m/Y', strtotime($k['tanggal'])); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $k['nama_siswa']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $k['nama_kelas']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php if ($k['jenis_masalah'] === 'lainnya') : ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <?= $k['jenis_masalah_lainnya']; ?>
                                            </span>
                                        <?php else : ?>
                                            <?php
                                            $badgeClass = 'bg-blue-100 text-blue-800';
                                            if ($k['jenis_masalah'] === 'tidak mengerjakan pr') {
                                                $badgeClass = 'bg-yellow-100 text-yellow-800';
                                            } else if ($k['jenis_masalah'] === 'mengerjakan PR di sekolah') {
                                                $badgeClass = 'bg-orange-100 text-orange-800';
                                            } else if ($k['jenis_masalah'] === 'berkelahi di kelas') {
                                                $badgeClass = 'bg-red-100 text-red-800';
                                            }
                                            ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $badgeClass; ?>">
                                                <?= ucfirst($k['jenis_masalah']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $k['nama_guru']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="<?= base_url('buku-kasus/detail/' . $k['id']); ?>" class="text-indigo-600 hover:text-indigo-900" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('buku-kasus/edit/' . $k['id']); ?>" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= base_url('buku-kasus/cetak/' . $k['id']); ?>" class="text-green-600 hover:text-green-900" title="Cetak PDF" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            <?php if (session()->get('role') === 'admin') : ?>
                                                <a href="#" class="text-red-600 hover:text-red-900 delete-btn" data-id="<?= $k['id']; ?>" data-name="<?= $k['nama_siswa']; ?>" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Hapus Kasus</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Apakah Anda yakin ingin menghapus kasus untuk siswa <span id="deleteStudentName" class="font-semibold"></span>? Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <a id="confirmDelete" href="#" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Hapus
                </a>
                <button type="button" id="cancelDelete" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Search and filter functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const filterJenisMasalah = document.getElementById('filterJenisMasalah');
        const kasusTableBody = document.getElementById('kasusTableBody');
        const originalRows = [...kasusTableBody.querySelectorAll('tr')];
        
        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const jenisMasalahFilter = filterJenisMasalah.value.toLowerCase();
            
            let filteredRows = originalRows;
            
            if (searchTerm) {
                filteredRows = filteredRows.filter(row => {
                    const namaSiswa = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                    return namaSiswa.includes(searchTerm);
                });
            }
            
            if (jenisMasalahFilter) {
                filteredRows = filteredRows.filter(row => {
                    const jenisMasalah = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
                    return jenisMasalah.includes(jenisMasalahFilter);
                });
            }
            
            // Clear table
            kasusTableBody.innerHTML = '';
            
            if (filteredRows.length === 0) {
                const emptyRow = document.createElement('tr');
                emptyRow.innerHTML = `
                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                        <div class="flex flex-col items-center justify-center py-4">
                            <i class="fas fa-search text-4xl text-gray-300 mb-3"></i>
                            <p>Tidak ada data yang sesuai dengan pencarian</p>
                        </div>
                    </td>
                `;
                kasusTableBody.appendChild(emptyRow);
            } else {
                // Re-add filtered rows
                filteredRows.forEach(row => {
                    kasusTableBody.appendChild(row);
                });
            }
        }
        
        searchInput.addEventListener('input', filterTable);
        filterJenisMasalah.addEventListener('change', filterTable);

        // Delete modal functionality
        const deleteModal = document.getElementById('deleteModal');
        const deleteButtons = document.querySelectorAll('.delete-btn');
        const confirmDeleteBtn = document.getElementById('confirmDelete');
        const cancelDeleteBtn = document.getElementById('cancelDelete');
        const deleteStudentName = document.getElementById('deleteStudentName');
        
        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.dataset.id;
                const name = this.dataset.name;
                
                deleteStudentName.textContent = name;
                confirmDeleteBtn.href = `<?= base_url('buku-kasus/hapus/'); ?>/${id}`;
                deleteModal.classList.remove('hidden');
            });
        });
        
        cancelDeleteBtn.addEventListener('click', function() {
            deleteModal.classList.add('hidden');
        });
    });
</script>
<?= $this->endSection(); ?>

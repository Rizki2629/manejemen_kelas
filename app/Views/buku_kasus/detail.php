<!-- app/Views/buku_kasus/detail.php -->
<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>
<div class="px-4 py-5 sm:px-6">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold leading-6 text-gray-900 flex items-center">
                <i class="fas fa-info-circle text-indigo-600 mr-2"></i>
                Detail Kasus
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Informasi lengkap kasus siswa
            </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="<?= base_url('buku-kasus/cetak/' . $kasus['id']); ?>" target="_blank" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                <i class="fas fa-print mr-2"></i>
                Cetak PDF
            </a>
            <a href="<?= base_url('buku-kasus'); ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>
</div>

<div class="bg-white shadow overflow-hidden sm:rounded-lg mx-4 mb-6">
    <div class="px-4 py-5 sm:px-6 bg-gray-50">
        <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
            <i class="fas fa-user-graduate text-indigo-500 mr-2"></i>
            <?= $kasus['nama_siswa']; ?>
            <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                <?= $kasus['nama_kelas']; ?>
            </span>
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Tanggal Kejadian: <?= date('d F Y', strtotime($kasus['tanggal'])); ?>
        </p>
    </div>
    <div class="border-t border-gray-200">
        <dl>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Jenis Masalah</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    <?php if ($kasus['jenis_masalah'] === 'lainnya') : ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            <?= $kasus['jenis_masalah_lainnya']; ?>
                        </span>
                    <?php else : ?>
                        <?php
                        $badgeClass = 'bg-blue-100 text-blue-800';
                        if ($kasus['jenis_masalah'] === 'tidak mengerjakan pr') {
                            $badgeClass = 'bg-yellow-100 text-yellow-800';
                        } else if ($kasus['jenis_masalah'] === 'mengerjakan PR di sekolah') {
                            $badgeClass = 'bg-orange-100 text-orange-800';
                        } else if ($kasus['jenis_masalah'] === 'berkelahi di kelas') {
                            $badgeClass = 'bg-red-100 text-red-800';
                        }
                        ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $badgeClass; ?>">
                            <?= ucfirst($kasus['jenis_masalah']); ?>
                        </span>
                    <?php endif; ?>
                </dd>
            </div>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Catatan Masalah</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    <div class="prose max-w-none">
                        <?= nl2br(esc($kasus['catatan_masalah'])); ?>
                    </div>
                </dd>
            </div>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Penyelesaian</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    <div class="prose max-w-none">
                        <?= nl2br(esc($kasus['penyelesaian'])); ?>
                    </div>
                </dd>
            </div>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Dilaporkan Oleh</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    <?= $kasus['nama_guru']; ?>
                    <?php if (!empty($kasus['nip_guru'])) : ?>
                        <span class="text-gray-500 ml-1">(NIP. <?= $kasus['nip_guru']; ?>)</span>
                    <?php else : ?>
                        <span class="text-gray-500 ml-1">(NIP. -)</span>
                    <?php endif; ?>
                </dd>
            </div>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Waktu Dibuat</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    <?= date('d F Y, H:i', strtotime($kasus['created_at'])); ?> WIB
                </dd>
            </div>
            <?php if ($kasus['updated_at'] != $kasus['created_at']) : ?>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Terakhir Diperbarui</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <?= date('d F Y, H:i', strtotime($kasus['updated_at'])); ?> WIB
                    </dd>
                </div>
            <?php endif; ?>
        </dl>
    </div>
</div>

<div class="flex justify-end space-x-4 mx-4 mb-8">
    <a href="<?= base_url('buku-kasus/edit/' . $kasus['id']); ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors duration-200">
        <i class="fas fa-edit mr-2"></i>
        Edit Kasus
    </a>
    <?php if (session()->get('role') === 'admin') : ?>
        <button id="deleteBtn" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
            <i class="fas fa-trash mr-2"></i>
            Hapus Kasus
        </button>
    <?php endif; ?>
</div>

<!-- Delete Confirmation Modal -->
<?php if (session()->get('role') === 'admin') : ?>
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
                                Apakah Anda yakin ingin menghapus kasus untuk siswa <span class="font-semibold"><?= $kasus['nama_siswa']; ?></span>? Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <a href="<?= base_url('buku-kasus/hapus/' . $kasus['id']); ?>" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
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
    document.addEventListener('DOMContentLoaded', function() {
        const deleteBtn = document.getElementById('deleteBtn');
        const deleteModal = document.getElementById('deleteModal');
        const cancelDelete = document.getElementById('cancelDelete');
        
        deleteBtn.addEventListener('click', function() {
            deleteModal.classList.remove('hidden');
        });
        
        cancelDelete.addEventListener('click', function() {
            deleteModal.classList.add('hidden');
        });
    });
</script>
<?php endif; ?>
<?= $this->endSection(); ?>

<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>
Detail Kasus - <?= esc($kasus['nama_siswa']) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Detail Kasus Siswa</h1>
        <nav class="mt-2 text-sm text-gray-500">
            <ol class="flex items-center space-x-2">
                <li><a href="<?= base_url('admin') ?>" class="text-indigo-600 hover:underline">Dashboard</a></li>
                <li>/</li>
                <li><a href="<?= base_url('buku-kasus') ?>" class="text-indigo-600 hover:underline">Buku Kasus</a></li>
                <li>/</li>
                <li class="text-gray-700">Detail</li>
            </ol>
        </nav>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="mb-4 rounded-md bg-green-50 p-4 border border-green-200 text-green-800">
            <div class="flex"><i class="fa-solid fa-circle-check mr-2"></i><?= session()->getFlashdata('success') ?></div>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 flex items-center justify-between border-b border-gray-100">
                    <h2 class="text-gray-800 font-semibold flex items-center"><i class="fa-solid fa-file-lines text-indigo-600 mr-2"></i>Informasi Kasus</h2>
                    <div>
                        <?php
                        $statusClass = match($kasus['status']) {
                            'belum_ditangani' => 'bg-red-100 text-red-800',
                            'dalam_proses' => 'bg-amber-100 text-amber-800',
                            'selesai' => 'bg-emerald-100 text-emerald-800',
                            default => 'bg-gray-100 text-gray-800'
                        };
                        $statusText = match($kasus['status']) {
                            'belum_ditangani' => 'Belum Ditangani',
                            'dalam_proses' => 'Dalam Proses',
                            'selesai' => 'Selesai',
                            default => 'Unknown'
                        };
                        ?>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium <?= $statusClass ?>"><?= $statusText ?></span>
                    </div>
                </div>
                <div class="px-6 py-5 space-y-3">
                    <div class="grid grid-cols-12 gap-2">
                        <div class="col-span-5 md:col-span-4 text-sm text-gray-500">Tanggal Kejadian</div>
                        <div class="col-span-7 md:col-span-8 text-sm text-gray-800"><?= date('d F Y', strtotime($kasus['tanggal_kejadian'])) ?></div>
                    </div>
                    <div class="grid grid-cols-12 gap-2">
                        <div class="col-span-5 md:col-span-4 text-sm text-gray-500">Jenis Kasus</div>
                        <div class="col-span-7 md:col-span-8 text-sm text-gray-800">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-sky-100 text-sky-800"><?= esc($kasus['jenis_kasus']) ?></span>
                        </div>
                    </div>
                    <div class="grid grid-cols-12 gap-2">
                        <div class="col-span-5 md:col-span-4 text-sm text-gray-500">Tingkat Keparahan</div>
                        <div class="col-span-7 md:col-span-8 text-sm text-gray-800">
                            <?php
                            $severityClass = match($kasus['tingkat_keparahan']) {
                                'Ringan' => 'bg-emerald-100 text-emerald-800',
                                'Sedang' => 'bg-amber-100 text-amber-800',
                                'Berat' => 'bg-rose-100 text-rose-800',
                                default => 'bg-gray-100 text-gray-800'
                            };
                            ?>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= $severityClass ?>"><?= esc($kasus['tingkat_keparahan']) ?></span>
                        </div>
                    </div>
                    <div class="grid grid-cols-12 gap-2">
                        <div class="col-span-5 md:col-span-4 text-sm text-gray-500">Deskripsi Kasus</div>
                        <div class="col-span-7 md:col-span-8 text-sm text-gray-800">
                            <div class="border border-gray-200 rounded-md p-3 bg-gray-50">
                                <?= nl2br(esc($kasus['deskripsi_kasus'])) ?>
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($kasus['tindakan_yang_diambil'])): ?>
                    <div class="grid grid-cols-12 gap-2">
                        <div class="col-span-5 md:col-span-4 text-sm text-gray-500">Tindakan yang Diambil</div>
                        <div class="col-span-7 md:col-span-8 text-sm text-gray-800">
                            <div class="border border-gray-200 rounded-md p-3 bg-gray-50">
                                <?= nl2br(esc($kasus['tindakan_yang_diambil'])) ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($kasus['catatan_guru'])): ?>
                    <div class="grid grid-cols-12 gap-2">
                        <div class="col-span-5 md:col-span-4 text-sm text-gray-500">Catatan Guru</div>
                        <div class="col-span-7 md:col-span-8 text-sm text-gray-800">
                            <div class="border border-gray-200 rounded-md p-3 bg-gray-50">
                                <?= nl2br(esc($kasus['catatan_guru'])) ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="grid grid-cols-12 gap-2">
                        <div class="col-span-5 md:col-span-4 text-sm text-gray-500">Dibuat</div>
                        <div class="col-span-7 md:col-span-8 text-sm text-gray-800"><?= date('d F Y H:i', strtotime($kasus['created_at'])) ?></div>
                    </div>
                    <?php if ($kasus['updated_at'] !== $kasus['created_at']): ?>
                    <div class="grid grid-cols-12 gap-2">
                        <div class="col-span-5 md:col-span-4 text-sm text-gray-500">Terakhir Diperbarui</div>
                        <div class="col-span-7 md:col-span-8 text-sm text-gray-800"><?= date('d F Y H:i', strtotime($kasus['updated_at'])) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <a href="<?= base_url('buku-kasus') ?>" class="a11y-focus inline-flex items-center px-4 py-2 rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fa-solid fa-arrow-left mr-2"></i> Kembali ke Daftar
                        </a>
                        <div class="flex items-center gap-2">
                            <a href="<?= base_url('buku-kasus/cetak/' . $kasus['id']) ?>" 
                               class="a11y-focus inline-flex items-center px-3 py-2 rounded-md text-white bg-emerald-600 hover:bg-emerald-700" target="_blank">
                                <i class="fa-solid fa-print mr-2"></i> Cetak PDF
                            </a>
                            <?php if (session()->get('role') == 'admin' || $kasus['guru_id'] == session()->get('user_id')): ?>
                            <a href="<?= base_url('buku-kasus/edit/' . $kasus['id']) ?>" 
                               class="a11y-focus inline-flex items-center px-3 py-2 rounded-md text-white bg-amber-500 hover:bg-amber-600">
                                <i class="fa-solid fa-pen-to-square mr-2"></i> Edit Kasus
                            </a>
                            <button type="button" class="a11y-focus inline-flex items-center px-3 py-2 rounded-md text-white bg-rose-600 hover:bg-rose-700" onclick="confirmDelete(<?= $kasus['id'] ?>)">
                                <i class="fa-solid fa-trash mr-2"></i> Hapus
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-gray-800 font-semibold"><i class="fa-solid fa-user text-indigo-600 mr-2"></i>Informasi Siswa</h3>
                </div>
                <div class="px-6 py-5">
                    <div class="text-center mb-3">
                        <div class="w-20 h-20 mx-auto mb-2 flex items-center justify-center text-gray-400">
                            <i class="fa-solid fa-circle-user text-6xl"></i>
                        </div>
                        <h4 class="text-gray-800 font-semibold"><?= esc($kasus['nama_siswa']) ?></h4>
                        <p class="text-gray-500 text-sm"><i class="fa-solid fa-id-badge mr-1"></i><?= esc($kasus['nis']) ?></p>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Kelas</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"><?= esc($kasus['kelas']) ?></span>
                        </div>
                        <?php if (!empty($kasus['jenis_kelamin'])): ?>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Jenis Kelamin</span>
                            <span class="text-gray-800"><?= $kasus['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($kasus['tempat_lahir'])): ?>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Tempat Lahir</span>
                            <span class="text-gray-800"><?= esc($kasus['tempat_lahir']) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($kasus['tanggal_lahir'])): ?>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Tanggal Lahir</span>
                            <span class="text-gray-800"><?= date('d F Y', strtotime($kasus['tanggal_lahir'])) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-gray-800 font-semibold"><i class="fa-solid fa-chalkboard-user text-indigo-600 mr-2"></i>Pelapor</h3>
                </div>
                <div class="px-6 py-5 text-center">
                    <div class="w-16 h-16 mx-auto mb-2 flex items-center justify-center text-indigo-600">
                        <i class="fa-solid fa-user-tie text-5xl"></i>
                    </div>
                    <h4 class="text-gray-800 font-semibold"><?= esc($kasus['nama_guru']) ?></h4>
                    <p class="text-gray-500 text-sm"><i class="fa-solid fa-envelope mr-1"></i><?= esc($kasus['email_guru']) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus kasus ini? Tindakan ini tidak dapat dibatalkan.')) {
        window.location.href = '<?= base_url('buku-kasus/hapus/') ?>' + id;
    }
}
</script>

<?= $this->endSection() ?>

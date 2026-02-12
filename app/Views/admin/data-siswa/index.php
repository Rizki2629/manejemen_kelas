<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="mb-8 pt-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-3 pl-2">
                Data Siswa
            </h1>
        </div>
        <div class="flex space-x-3">
            <!-- Tambah Data Siswa Button - Only for Admin -->
            <?php if (!isset($userRole) || $userRole !== 'walikelas'): ?>
                <a href="/admin/data-siswa/create" class="bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 flex items-center space-x-2 shadow-lg hover:shadow-xl hover:scale-105">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Siswa</span>
                </a>
            <?php endif; ?>

            <a href="/admin/data-siswa/export" class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 flex items-center space-x-2 shadow-lg hover:shadow-xl hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>
                    Export CSV
                    <?php if (isset($userRole) && $userRole === 'walikelas' && isset($userWalikelasData) && !empty($userWalikelasData['kelas'])): ?>
                        (<?= esc($userWalikelasData['kelas']) ?>)
                    <?php endif; ?>
                </span>
            </a>
            <button id="exportExcel" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 flex items-center space-x-2 shadow-lg hover:shadow-xl hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>
                    Export Excel
                    <?php if (isset($userRole) && $userRole === 'walikelas' && isset($userWalikelasData) && !empty($userWalikelasData['kelas'])): ?>
                        (<?= esc($userWalikelasData['kelas']) ?>)
                    <?php endif; ?>
                </span>
            </button>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 pl-2">
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6 hover:shadow-xl transition-all duration-300 hover:scale-105">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-gradient-to-r from-blue-400 to-blue-600 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-900"><?= number_format($totalSiswa) ?></h3>
                <p class="text-gray-500 text-sm">Total Siswa</p>
            </div>
        </div>
    </div>

    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6 hover:shadow-xl transition-all duration-300 hover:scale-105">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-gradient-to-r from-blue-400 to-blue-600 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                <span class="text-white font-bold text-lg">♂</span>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-900"><?= number_format($siswaLaki) ?></h3>
                <p class="text-gray-500 text-sm">Laki-laki</p>
            </div>
        </div>
    </div>

    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6 hover:shadow-xl transition-all duration-300 hover:scale-105">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-gradient-to-r from-pink-400 to-pink-600 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                <span class="text-white font-bold text-lg">♀</span>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-900"><?= number_format($siswaPerempuan) ?></h3>
                <p class="text-gray-500 text-sm">Perempuan</p>
            </div>
        </div>
    </div>
</div>

<!-- Filter and Search -->
<div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6 mb-8 pl-2">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-<?= (isset($userRole) && $userRole === 'walikelas') ? '4' : '5' ?> gap-4 items-end">
        <!-- Search -->
        <div class="md:col-span-1">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Cari Siswa</label>
            <div class="relative">
                <input type="text" id="search" name="search" value="<?= esc($search) ?>"
                    placeholder="Nama, NISN, atau NIPD..."
                    class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/80 backdrop-blur-sm transition-all duration-300">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Class Filter - Only show for admin -->
        <?php if (!isset($userRole) || $userRole !== 'walikelas'): ?>
            <div>
                <label for="kelas" class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                <select id="kelas" name="kelas" class="w-full px-3 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/80 backdrop-blur-sm transition-all duration-300">
                    <option value="">Semua Kelas</option>
                    <?php foreach ($kelasOptions as $kelasOption): ?>
                        <option value="<?= esc($kelasOption) ?>" <?= $selectedKelas == $kelasOption ? 'selected' : '' ?>>
                            <?= esc($kelasOption) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php else: ?>
            <!-- Hidden input for walikelas to maintain their class filter -->
            <input type="hidden" name="kelas" value="<?= esc($selectedKelas) ?>">
        <?php endif; ?>

        <!-- Gender Filter -->
        <div>
            <label for="jk" class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
            <select id="jk" name="jk" class="w-full px-3 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/80 backdrop-blur-sm transition-all duration-300">
                <option value="">Semua</option>
                <option value="L" <?= $selectedJk == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                <option value="P" <?= $selectedJk == 'P' ? 'selected' : '' ?>>Perempuan</option>
            </select>
        </div>

        <!-- Filter & Reset Buttons -->
        <div class="flex space-x-3">
            <button type="submit" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105">
                Filter
            </button>
            <a href="/admin/data-siswa" class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 flex items-center">
                Reset
            </a>
        </div>

    </form>
</div>

<!-- Table -->
<div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 overflow-hidden pl-2">


    <?php // Top pagination for easier access 
    ?>
    <?= $this->include('admin/data-siswa/_pagination') ?>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NISN</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">JK</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tempat, Tanggal Lahir</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($siswa)): ?>
                    <?php
                    $no = ($currentPage - 1) * $perPage + 1;
                    foreach ($siswa as $row):
                    ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $no++ ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                        <span class="text-blue-600 font-semibold text-sm">
                                            <?= strtoupper(substr($row['nama'], 0, 2)) ?>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?= esc($row['nama']) ?></div>
                                        <div class="text-sm text-gray-500">NIPD: <?= esc($row['nipd']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= esc($row['nisn']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <?= esc($row['kelas']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($row['jk'] == 'L'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        ♂ Laki-laki
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                        ♀ Perempuan
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div><?= esc($row['tempat_lahir']) ?></div>
                                <div class="text-gray-500"><?= !empty($row['tanggal_lahir']) ? date('d/m/Y', strtotime($row['tanggal_lahir'])) : '-' ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="/admin/data-siswa/detail/<?= $row['id'] ?>"
                                        class="text-blue-600 hover:text-blue-900 bg-gradient-to-r from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 px-3 py-2 rounded-lg transition-all duration-300 shadow-sm hover:shadow-md flex items-center space-x-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>Detail</span>
                                    </a>

                                    <?php if (!isset($userRole) || $userRole !== 'walikelas'): ?>
                                        <a href="/admin/data-siswa/edit/<?= $row['id'] ?>"
                                            class="text-yellow-600 hover:text-yellow-900 bg-gradient-to-r from-yellow-50 to-yellow-100 hover:from-yellow-100 hover:to-yellow-200 px-3 py-2 rounded-lg transition-all duration-300 shadow-sm hover:shadow-md flex items-center space-x-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            <span>Edit</span>
                                        </a>

                                        <button onclick="confirmDelete(<?= $row['id'] ?>, '<?= esc($row['nama']) ?>')"
                                            class="text-red-600 hover:text-red-900 bg-gradient-to-r from-red-50 to-red-100 hover:from-red-100 hover:to-red-200 px-3 py-2 rounded-lg transition-all duration-300 shadow-sm hover:shadow-md flex items-center space-x-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            <span>Hapus</span>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <p class="text-gray-500">Tidak ada data siswa</p>
                                <?php if (!empty($search) || !empty($selectedKelas) || !empty($selectedJk)): ?>
                                    <p class="text-gray-400 text-sm mt-1">Coba ubah filter pencarian</p>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($pager && $pager->getPageCount() > 1): ?>
        <div class="px-6 py-6 border-t border-gray-200/60 bg-gradient-to-r from-gray-50/50 to-white/50">
            <div class="flex items-center justify-between">
                <!-- Mobile Pagination -->
                <div class="flex-1 flex justify-between sm:hidden">
                    <?php if ($currentPage > 1): ?>
                        <a href="<?= url_to('admin/data-siswa') . '?' . http_build_query(array_merge($_GET, ['page' => $currentPage - 1])) ?>"
                            class="relative inline-flex items-center px-5 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-sm font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Sebelumnya
                        </a>
                    <?php endif; ?>
                    <?php if ($currentPage < $pager->getPageCount()): ?>
                        <a href="<?= url_to('admin/data-siswa') . '?' . http_build_query(array_merge($_GET, ['page' => $currentPage + 1])) ?>"
                            class="relative inline-flex items-center px-5 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-sm font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                            Selanjutnya
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Desktop Pagination -->
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div class="bg-white/80 backdrop-blur-sm rounded-xl px-4 py-2 shadow-sm border border-gray-200/50">
                        <p class="text-sm text-gray-600">
                            Menampilkan
                            <span class="font-semibold text-blue-600"><?= number_format(($currentPage - 1) * $perPage + 1) ?></span>
                            sampai
                            <span class="font-semibold text-blue-600"><?= number_format(min($currentPage * $perPage, $totalSiswa)) ?></span>
                            dari
                            <span class="font-bold text-gray-900"><?= number_format($totalSiswa) ?></span>
                            hasil
                        </p>
                    </div>

                    <div class="flex items-center space-x-2">
                        <!-- Previous Button -->
                        <?php if ($currentPage > 1): ?>
                            <a href="<?= url_to('admin/data-siswa') . '?' . http_build_query(array_merge($_GET, ['page' => $currentPage - 1])) ?>"
                                class="inline-flex items-center px-4 py-2 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 transition-all duration-300 shadow-sm hover:shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Sebelumnya
                            </a>
                        <?php endif; ?>

                        <!-- Page Numbers -->
                        <nav class="relative z-0 inline-flex items-center space-x-1" aria-label="Pagination">
                            <?php
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($pager->getPageCount(), $currentPage + 2);

                            // Show first page if not in range
                            if ($startPage > 1): ?>
                                <a href="<?= url_to('admin/data-siswa') . '?' . http_build_query(array_merge($_GET, ['page' => 1])) ?>"
                                    class="inline-flex items-center px-3 py-2 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 transition-all duration-300 shadow-sm hover:shadow-md">
                                    1
                                </a>
                                <?php if ($startPage > 2): ?>
                                    <span class="inline-flex items-center px-2 py-2 text-gray-400">...</span>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <?php if ($i == $currentPage): ?>
                                    <span class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl text-sm font-semibold shadow-lg">
                                        <?= $i ?>
                                    </span>
                                <?php else: ?>
                                    <a href="<?= url_to('admin/data-siswa') . '?' . http_build_query(array_merge($_GET, ['page' => $i])) ?>"
                                        class="inline-flex items-center px-3 py-2 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 transition-all duration-300 shadow-sm hover:shadow-md">
                                        <?= $i ?>
                                    </a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <!-- Show last page if not in range -->
                            <?php if ($endPage < $pager->getPageCount()): ?>
                                <?php if ($endPage < $pager->getPageCount() - 1): ?>
                                    <span class="inline-flex items-center px-2 py-2 text-gray-400">...</span>
                                <?php endif; ?>
                                <a href="<?= url_to('admin/data-siswa') . '?' . http_build_query(array_merge($_GET, ['page' => $pager->getPageCount()])) ?>"
                                    class="inline-flex items-center px-3 py-2 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 transition-all duration-300 shadow-sm hover:shadow-md">
                                    <?= $pager->getPageCount() ?>
                                </a>
                            <?php endif; ?>
                        </nav>

                        <!-- Next Button -->
                        <?php if ($currentPage < $pager->getPageCount()): ?>
                            <a href="<?= url_to('admin/data-siswa') . '?' . http_build_query(array_merge($_GET, ['page' => $currentPage + 1])) ?>"
                                class="inline-flex items-center px-4 py-2 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 transition-all duration-300 shadow-sm hover:shadow-md">
                                Selanjutnya
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform scale-95 transition-all duration-300" id="modalContent">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Konfirmasi Hapus</h3>
            <p class="text-gray-600 text-center mb-6" id="deleteMessage">
                Apakah Anda yakin ingin menghapus data siswa ini?
            </p>
            <div class="flex space-x-3">
                <button type="button" onclick="closeDeleteModal()"
                    class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-xl font-medium transition-all duration-300">
                    Batal
                </button>
                <button type="button" onclick="confirmDeleteAction()"
                    class="flex-1 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-4 py-2 rounded-xl font-medium transition-all duration-300">
                    Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    (() => {
        let deleteStudentId = null;

        window.confirmDelete = function(id, nama) {
            deleteStudentId = id;

            const modal = document.getElementById('deleteModal');
            const message = document.getElementById('deleteMessage');

            if (message) {
                const safeName = (nama ?? '').toString();
                message.textContent = safeName ?
                    `Apakah Anda yakin ingin menghapus siswa "${safeName}"?` :
                    'Apakah Anda yakin ingin menghapus data siswa ini?';
            }

            if (modal) modal.classList.remove('hidden');
        };

        window.closeDeleteModal = function() {
            const modal = document.getElementById('deleteModal');
            if (modal) modal.classList.add('hidden');
            deleteStudentId = null;
        };

        window.confirmDeleteAction = function() {
            if (!deleteStudentId) return;
            window.location.href = `/admin/data-siswa/delete/${deleteStudentId}`;
        };

        document.addEventListener('DOMContentLoaded', () => {
            const exportExcelBtn = document.getElementById('exportExcel');
            if (exportExcelBtn) {
                exportExcelBtn.addEventListener('click', function(e) {
                    e.preventDefault();

                    const originalContent = this.innerHTML;
                    this.disabled = true;
                    this.innerHTML = `
                    <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Mengunduh...</span>
                `;

                    const searchValue = document.getElementById('search')?.value || '';
                    const kelasValue = document.getElementById('kelas')?.value || '';
                    const jkValue = document.getElementById('jk')?.value || '';

                    const params = new URLSearchParams();
                    params.set('format', 'excel');
                    if (searchValue) params.set('search', searchValue);
                    if (kelasValue) params.set('kelas', kelasValue);
                    if (jkValue) params.set('jk', jkValue);

                    window.location.href = `/admin/data-siswa/export?${params.toString()}`;

                    setTimeout(() => {
                        this.disabled = false;
                        this.innerHTML = originalContent;
                    }, 1500);
                });
            }

            const modal = document.getElementById('deleteModal');
            if (modal) {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) window.closeDeleteModal();
                });
            }
        });
    })();
</script>

<?= $this->endSection() ?>
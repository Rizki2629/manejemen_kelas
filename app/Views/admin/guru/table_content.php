<!-- Mobile Card Layout (hidden on desktop) -->
<div class="block md:hidden">
    <?php if (empty($guru)): ?>
        <div class="text-center py-8 text-gray-500 bg-white rounded-lg border">
            <i class="fas fa-users fa-2x mb-2"></i><br>
            <?= isset($keyword) && $keyword ? 'Data guru tidak ditemukan untuk pencarian "' . esc($keyword) . '".' : 'Data guru tidak ditemukan.' ?>
        </div>
    <?php else: ?>
        <?php
        $currentPageNum = $pager ? $pager->getCurrentPage() : 1;
        $nomor = 1 + ($perPage * ($currentPageNum - 1));
        foreach ($guru as $g): ?>
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-3 shadow-sm">
                <!-- Header with avatar and name -->
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="w-10 h-10 flex items-center justify-center rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 text-white flex-shrink-0">
                            <i class="fas fa-user text-sm"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="font-semibold text-gray-900 truncate"><?= esc($g['nama']); ?></div>
                            <div class="text-xs text-gray-500"><?= esc($g['jenis_ptk'] ?? '-'); ?></div>
                        </div>
                    </div>
                    <div class="text-xs text-gray-400 font-medium">#<?= $nomor++; ?></div>
                </div>
                
                <!-- Info Grid -->
                <div class="grid grid-cols-2 gap-3 mb-3 text-sm">
                    <div>
                        <div class="text-xs text-gray-500 uppercase tracking-wide">NIP</div>
                        <div class="font-medium text-gray-900"><?= esc($g['nip']) ?: '-'; ?></div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 uppercase tracking-wide">NUPTK</div>
                        <div class="font-medium text-gray-900"><?= esc($g['nuptk']) ?: '-'; ?></div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 uppercase tracking-wide">Jenis Kelamin</div>
                        <div>
                            <?php if ($g['jk'] == 'L'): ?>
                                <span class="inline-block bg-blue-600 text-white rounded px-2 py-1 text-xs">Laki-laki</span>
                            <?php elseif ($g['jk'] == 'P'): ?>
                                <span class="inline-block bg-pink-500 text-white rounded px-2 py-1 text-xs">Perempuan</span>
                            <?php else: ?>
                                <span class="text-gray-400">-</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 uppercase tracking-wide">Status</div>
                        <div>
                            <?= $g['status_kepegawaian'] ? '<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">'.$g['status_kepegawaian'].'</span>' : '-' ?>
                        </div>
                    </div>
                </div>
                
                <!-- Tugas Mengajar -->
                <div class="mb-3">
                    <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Tugas Mengajar</div>
                    <div class="text-sm text-gray-900"><?= esc($g['tugas_mengajar']) ?: '-'; ?></div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                    <a href="<?= base_url('admin/guru/detail/' . $g['id']); ?>"
                       class="text-blue-600 hover:text-blue-800 p-2 rounded hover:bg-blue-50 transition-colors duration-200" title="Detail">
                        <i class="fas fa-eye text-sm"></i>
                    </a>
                    <a href="<?= base_url('admin/guru/edit/' . $g['id']); ?>"
                       class="text-yellow-500 hover:text-yellow-600 p-2 rounded hover:bg-yellow-50 transition-colors duration-200" title="Edit">
                        <i class="fas fa-edit text-sm"></i>
                    </a>
                    <a href="<?= base_url('admin/guru/delete/' . $g['id']); ?>"
                       onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"
                       class="text-red-600 hover:text-red-700 p-2 rounded hover:bg-red-50 transition-colors duration-200" title="Hapus">
                        <i class="fas fa-trash text-sm"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Desktop Table Layout (hidden on mobile) -->
<div class="hidden md:block overflow-x-auto">
    <table class="min-w-full bg-white border border-gray-200 rounded text-sm text-gray-700">
        <thead class="bg-gray-100 text-gray-800 uppercase text-xs font-semibold">
            <tr>
                <th class="px-4 py-3 text-center w-12">No</th>
                <th class="px-4 py-3">Nama</th>
                <th class="px-4 py-3">NIP</th>
                <th class="px-4 py-3">NUPTK</th>
                <th class="px-4 py-3 text-center">JK</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Tugas Mengajar</th>
                <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($guru)): ?>
                <tr>
                    <td colspan="8" class="text-center py-8 text-gray-500">
                        <i class="fas fa-users fa-2x mb-2"></i><br>
                        <?= isset($keyword) && $keyword ? 'Data guru tidak ditemukan untuk pencarian "' . esc($keyword) . '".' : 'Data guru tidak ditemukan.' ?>
                    </td>
                </tr>
            <?php else: ?>
                <?php
                $currentPageNum = $pager ? $pager->getCurrentPage() : 1;
                $nomor = 1 + ($perPage * ($currentPageNum - 1));
                foreach ($guru as $g): ?>
                    <tr class="border-t hover:bg-gray-50 transition-colors duration-200">
                        <td class="text-center px-4 py-2 font-semibold"><?= $nomor++; ?></td>
                        <td class="px-4 py-2">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 flex items-center justify-center rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 text-white">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <div class="font-semibold"><?= esc($g['nama']); ?></div>
                                    <div class="text-xs text-gray-500"><?= esc($g['jenis_ptk'] ?? '-'); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-2"><?= esc($g['nip']) ?: '-'; ?></td>
                        <td class="px-4 py-2"><?= esc($g['nuptk']) ?: '-'; ?></td>
                        <td class="px-4 py-2 text-center">
                            <?php if ($g['jk'] == 'L'): ?>
                                <span class="inline-block bg-blue-600 text-white rounded px-2 py-1 text-xs">L</span>
                            <?php elseif ($g['jk'] == 'P'): ?>
                                <span class="inline-block bg-pink-500 text-white rounded px-2 py-1 text-xs">P</span>
                            <?php else: ?>
                                <span class="text-gray-400">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-2">
                            <?= $g['status_kepegawaian'] ? '<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">'.$g['status_kepegawaian'].'</span>' : '-' ?>
                        </td>
                        <td class="px-4 py-2 truncate max-w-xs" title="<?= esc($g['tugas_mengajar']); ?>">
                            <?= esc($g['tugas_mengajar']) ?: '-'; ?>
                        </td>
                        <td class="px-4 py-2 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="<?= base_url('admin/guru/detail/' . $g['id']); ?>"
                                   class="text-blue-600 hover:text-blue-800 p-1 rounded hover:bg-blue-50 transition-colors duration-200" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= base_url('admin/guru/edit/' . $g['id']); ?>"
                                   class="text-yellow-500 hover:text-yellow-600 p-1 rounded hover:bg-yellow-50 transition-colors duration-200" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= base_url('admin/guru/delete/' . $g['id']); ?>"
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"
                                   class="text-red-600 hover:text-red-700 p-1 rounded hover:bg-red-50 transition-colors duration-200" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Enhanced Pagination -->
<div class="mt-6">
    <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
        <div class="px-6 py-6 border-t border-gray-200/60 bg-gradient-to-r from-gray-50/50 to-white/50 rounded-b-xl">
            <div class="flex items-center justify-between">
                <!-- Mobile Pagination -->
                <div class="flex-1 flex justify-between sm:hidden">
                    <?php if ($pager->getCurrentPage() > 1): ?>
                        <a href="?page=<?= $pager->getCurrentPage() - 1 ?><?= isset($keyword) && $keyword ? '&keyword=' . urlencode($keyword) : '' ?>" 
                           class="relative inline-flex items-center px-5 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white text-sm font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Sebelumnya
                        </a>
                    <?php endif; ?>
                    <?php if ($pager->getCurrentPage() < $pager->getPageCount()): ?>
                        <a href="?page=<?= $pager->getCurrentPage() + 1 ?><?= isset($keyword) && $keyword ? '&keyword=' . urlencode($keyword) : '' ?>" 
                           class="relative inline-flex items-center px-5 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white text-sm font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
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
                            <span class="font-semibold text-indigo-600"><?= number_format((($pager->getCurrentPage() - 1) * $perPage) + 1) ?></span>
                            sampai 
                            <span class="font-semibold text-indigo-600"><?= number_format(min($pager->getCurrentPage() * $perPage, $totalRecords)) ?></span>
                            dari 
                            <span class="font-bold text-gray-900"><?= number_format($totalRecords) ?></span>
                            hasil
                        </p>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <!-- Previous Button -->
                        <?php if ($pager->getCurrentPage() > 1): ?>
                            <a href="?page=<?= $pager->getCurrentPage() - 1 ?><?= isset($keyword) && $keyword ? '&keyword=' . urlencode($keyword) : '' ?>" 
                               class="inline-flex items-center px-4 py-2 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:border-indigo-300 hover:text-indigo-600 transition-all duration-300 shadow-sm hover:shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Sebelumnya
                            </a>
                        <?php endif; ?>

                        <!-- Page Numbers -->
                        <nav class="relative z-0 inline-flex items-center space-x-1" aria-label="Pagination">
                            <?php
                            $currentPage = $pager->getCurrentPage();
                            $totalPages = $pager->getPageCount();
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($totalPages, $currentPage + 2);
                            
                            // Show first page if not in range
                            if ($startPage > 1): ?>
                                <a href="?page=1<?= isset($keyword) && $keyword ? '&keyword=' . urlencode($keyword) : '' ?>" 
                                   class="inline-flex items-center px-3 py-2 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:border-indigo-300 hover:text-indigo-600 transition-all duration-300 shadow-sm hover:shadow-md">
                                    1
                                </a>
                                <?php if ($startPage > 2): ?>
                                    <span class="inline-flex items-center px-2 py-2 text-gray-400">...</span>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <?php if ($i == $currentPage): ?>
                                    <span class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl text-sm font-semibold shadow-lg">
                                        <?= $i ?>
                                    </span>
                                <?php else: ?>
                                    <a href="?page=<?= $i ?><?= isset($keyword) && $keyword ? '&keyword=' . urlencode($keyword) : '' ?>" 
                                       class="inline-flex items-center px-3 py-2 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:border-indigo-300 hover:text-indigo-600 transition-all duration-300 shadow-sm hover:shadow-md">
                                        <?= $i ?>
                                    </a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <!-- Show last page if not in range -->
                            <?php if ($endPage < $totalPages): ?>
                                <?php if ($endPage < $totalPages - 1): ?>
                                    <span class="inline-flex items-center px-2 py-2 text-gray-400">...</span>
                                <?php endif; ?>
                                <a href="?page=<?= $totalPages ?><?= isset($keyword) && $keyword ? '&keyword=' . urlencode($keyword) : '' ?>" 
                                   class="inline-flex items-center px-3 py-2 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:border-indigo-300 hover:text-indigo-600 transition-all duration-300 shadow-sm hover:shadow-md">
                                    <?= $totalPages ?>
                                </a>
                            <?php endif; ?>
                        </nav>

                        <!-- Next Button -->
                        <?php if ($pager->getCurrentPage() < $pager->getPageCount()): ?>
                            <a href="?page=<?= $pager->getCurrentPage() + 1 ?><?= isset($keyword) && $keyword ? '&keyword=' . urlencode($keyword) : '' ?>" 
                               class="inline-flex items-center px-4 py-2 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:border-indigo-300 hover:text-indigo-600 transition-all duration-300 shadow-sm hover:shadow-md">
                                Selanjutnya
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Items per page selector -->
            <div class="mt-4 flex items-center justify-center sm:justify-end">
                <div class="flex items-center space-x-2 bg-white/80 backdrop-blur-sm rounded-xl px-4 py-2 shadow-sm border border-gray-200/50">
                    <span class="text-sm text-gray-600">Per halaman:</span>
                    <select id="perPageSelect" class="text-sm border border-gray-300 rounded-lg px-3 py-1 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white transition-all duration-300">
                        <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10</option>
                        <option value="15" <?= $perPage == 15 ? 'selected' : '' ?>>15</option>
                        <option value="25" <?= $perPage == 25 ? 'selected' : '' ?>>25</option>
                        <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50</option>
                    </select>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center text-sm text-gray-600 py-4">
            Total: <?= $totalRecords ?> data
            <?php if ($totalRecords > $perPage): ?>
                <div class="mt-2">
                    <span class="text-xs text-gray-500">Pagination akan muncul jika data lebih dari <?= $perPage ?> per halaman</span>
                </div>
            <?php endif; ?>
        </div>
    <?php endif ?>
</div>

<?php if (isset($pager) && $pager && $pager->getPageCount() > 1): ?>
<div class="px-6 py-6 border-t border-gray-200/60 bg-gradient-to-r from-gray-50/50 to-white/50 rounded-b-2xl">
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
                <?php if ($currentPage > 1): ?>
                    <a href="<?= url_to('admin/data-siswa') . '?' . http_build_query(array_merge($_GET, ['page' => $currentPage - 1])) ?>" 
                       class="inline-flex items-center px-4 py-2 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 transition-all duration-300 shadow-sm hover:shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Sebelumnya
                    </a>
                <?php endif; ?>

                <nav class="relative z-0 inline-flex items-center space-x-1" aria-label="Pagination">
                    <?php
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($pager->getPageCount(), $currentPage + 2);
                    if ($startPage > 1): ?>
                        <a href="<?= url_to('admin/data-siswa') . '?' . http_build_query(array_merge($_GET, ['page' => 1])) ?>" 
                           class="inline-flex items-center px-3 py-2 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 transition-all duration-300 shadow-sm hover:shadow-md">1</a>
                        <?php if ($startPage > 2): ?>
                            <span class="inline-flex items-center px-2 py-2 text-gray-400">...</span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <?php if ($i == $currentPage): ?>
                            <span class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl text-sm font-semibold shadow-lg"><?= $i ?></span>
                        <?php else: ?>
                            <a href="<?= url_to('admin/data-siswa') . '?' . http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
                               class="inline-flex items-center px-3 py-2 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 transition-all duration-300 shadow-sm hover:shadow-md"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($endPage < $pager->getPageCount()): ?>
                        <?php if ($endPage < $pager->getPageCount() - 1): ?>
                            <span class="inline-flex items-center px-2 py-2 text-gray-400">...</span>
                        <?php endif; ?>
                        <a href="<?= url_to('admin/data-siswa') . '?' . http_build_query(array_merge($_GET, ['page' => $pager->getPageCount()])) ?>" 
                           class="inline-flex items-center px-3 py-2 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 transition-all duration-300 shadow-sm hover:shadow-md"><?= $pager->getPageCount() ?></a>
                    <?php endif; ?>
                </nav>

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

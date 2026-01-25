<?= $this->extend('admin/layout'); ?>
<?= $this->section('content'); ?>

<div class="p-2 sm:p-4 lg:p-6">
    <div class="max-w-7xl mx-auto">
        <div class="bg-white rounded-lg sm:rounded-xl shadow-lg overflow-hidden">
            <!-- Mobile-optimized header -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white p-3 sm:p-4 lg:px-6 lg:py-4">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 sm:gap-4">
                    <div class="flex-1 min-w-0">
                        <h2 class="text-lg sm:text-xl font-bold flex items-center gap-2">
                            <i class="fas fa-users text-sm sm:text-base"></i> 
                            <span class="truncate">Data Guru</span>
                        </h2>
                        <p class="text-xs sm:text-sm opacity-75 mt-1">Kelola data guru sekolah</p>
                    </div>
                    
                    <!-- Mobile action buttons -->
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-2">
                        <a href="<?= base_url('admin/guru/create'); ?>" 
                           class="bg-white text-indigo-600 hover:bg-indigo-100 font-semibold px-3 sm:px-4 py-2 rounded shadow transition-colors duration-200 text-center text-sm sm:text-base">
                            <i class="fas fa-plus text-xs sm:text-sm me-1"></i>
                            <span class="hidden xs:inline">Tambah Guru</span>
                            <span class="xs:hidden">Tambah</span>
                        </a>
                        
                        <!-- Mobile dropdown for secondary actions -->
                        <div class="relative sm:contents">
                            <button id="mobileActionsToggle" 
                                    class="sm:hidden bg-white/20 hover:bg-white/30 text-white px-3 py-2 rounded shadow transition-colors duration-200 text-sm flex items-center justify-center gap-2">
                                <i class="fas fa-ellipsis-v"></i>
                                <span>Lainnya</span>
                            </button>
                            
                            <div id="mobileActionsMenu" 
                                 class="hidden sm:flex absolute top-full right-0 mt-1 bg-white rounded-lg shadow-lg border z-10 min-w-48 sm:static sm:bg-transparent sm:shadow-none sm:border-none sm:gap-2">
                                <a href="<?= base_url('admin/guru/import'); ?>" 
                                   onclick="return confirm('Apakah Anda yakin ingin mengimpor data dari file JSON?')"
                                   class="block sm:inline-block bg-yellow-500 hover:bg-yellow-600 text-white px-3 sm:px-4 py-2 sm:rounded shadow font-semibold transition-colors duration-200 text-sm">
                                    <i class="fas fa-download text-xs sm:text-sm me-1"></i>
                                    <span class="sm:hidden">Import Data</span>
                                    <span class="hidden sm:inline">Import</span>
                                </a>
                                <a href="<?= base_url('admin/guru/check-duplicates'); ?>" 
                                   class="block sm:inline-block bg-red-500 hover:bg-red-600 text-white px-3 sm:px-4 py-2 sm:rounded shadow font-semibold transition-colors duration-200 text-sm">
                                    <i class="fas fa-search text-xs sm:text-sm me-1"></i>
                                    <span class="sm:hidden">Cek Duplikasi</span>
                                    <span class="hidden sm:inline">Duplikasi</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-3 sm:p-4 lg:px-6 lg:py-4">
                <?php if (session()->getFlashdata('pesan')): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded animate-fade-in">
                        <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('pesan'); ?>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded animate-fade-in">
                        <i class="fas fa-exclamation-circle me-2"></i> <?= session()->getFlashdata('error'); ?>
                    </div>
                <?php endif; ?>

                <!-- Enhanced Search Form -->
                <div class="mb-6 bg-gray-50 p-4 rounded-lg border">
                    <div class="flex flex-col md:flex-row gap-4 items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-search text-gray-400 mr-1"></i>
                                Pencarian Otomatis
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" 
                                       id="searchInput" 
                                       placeholder="Ketik nama, NIP, NUPTK, atau tugas mengajar..."
                                       value="<?= esc($keyword ?? '') ?>"
                                       class="pl-10 pr-10 py-3 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <div id="searchSpinner" class="hidden">
                                        <i class="fas fa-spinner fa-spin text-gray-400"></i>
                                    </div>
                                    <button type="button" id="clearSearch" class="text-gray-400 hover:text-gray-600 <?= empty($keyword) ? 'hidden' : '' ?>">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mt-1 text-xs text-gray-500">
                                Hasil pencarian akan muncul otomatis saat Anda mengetik
                            </div>
                        </div>
                        
                        <!-- Quick Stats -->
                        <div class="flex items-center space-x-4 text-sm">
                            <div class="bg-white px-3 py-2 rounded border">
                                <span class="text-gray-600">Total:</span>
                                <span class="font-semibold text-indigo-600" id="totalCount"><?= $totalRecords ?? 0 ?></span>
                            </div>
                            <?php if (!empty($keyword)): ?>
                            <div class="bg-blue-50 px-3 py-2 rounded border border-blue-200">
                                <span class="text-blue-600">Pencarian:</span>
                                <span class="font-semibold text-blue-800">"<?= esc($keyword) ?>"</span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Loading Overlay -->
                <div id="loadingOverlay" class="hidden absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin text-3xl text-indigo-500 mb-2"></i>
                        <p class="text-gray-600">Memuat data...</p>
                    </div>
                </div>

                <!-- Table Container -->
                <div id="tableContainer" class="relative">
                    <?= view('admin/guru/table_content', [
                        'guru' => $guru,
                        'pager' => $pager,
                        'keyword' => $keyword,
                        'totalRecords' => $totalRecords,
                        'perPage' => $perPage
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}

.table-row-enter {
    animation: slideInUp 0.3s ease-out;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Loading state */
.loading-state {
    opacity: 0.6;
    pointer-events: none;
}

/* Smooth transitions */
.transition-all {
    transition: all 0.2s ease-in-out;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const clearSearch = document.getElementById('clearSearch');
    const searchSpinner = document.getElementById('searchSpinner');
    const tableContainer = document.getElementById('tableContainer');
    const totalCount = document.getElementById('totalCount');
    let searchTimeout;

    // Auto search functionality
    searchInput.addEventListener('input', function() {
        const keyword = this.value.trim();
        
        // Show/hide clear button
        if (keyword) {
            clearSearch.classList.remove('hidden');
        } else {
            clearSearch.classList.add('hidden');
        }

        // Clear previous timeout
        clearTimeout(searchTimeout);
        
        // Set new timeout for search
        searchTimeout = setTimeout(() => {
            performSearch(keyword);
        }, 500); // Wait 500ms after user stops typing
    });

    // Clear search functionality
    clearSearch.addEventListener('click', function() {
        searchInput.value = '';
        clearSearch.classList.add('hidden');
        performSearch('');
    });

    // Perform AJAX search
    function performSearch(keyword) {
        // Show loading spinner
        searchSpinner.classList.remove('hidden');
        tableContainer.classList.add('loading-state');

        // Prepare URL
        const url = new URL(window.location.href);
        if (keyword) {
            url.searchParams.set('keyword', keyword);
        } else {
            url.searchParams.delete('keyword');
        }
        url.searchParams.delete('page'); // Reset to first page

        // Make AJAX request
        fetch(url.toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update table content
                tableContainer.innerHTML = data.html;
                
                // Update total count
                totalCount.textContent = data.data.totalRecords;
                
                // Update URL without page reload
                window.history.replaceState({}, '', url.toString());
                
                // Add animation to new rows
                const rows = tableContainer.querySelectorAll('tbody tr');
                rows.forEach((row, index) => {
                    row.style.opacity = '0';
                    row.style.transform = 'translateY(10px)';
                    setTimeout(() => {
                        row.style.transition = 'all 0.3s ease-out';
                        row.style.opacity = '1';
                        row.style.transform = 'translateY(0)';
                    }, index * 50);
                });
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            showNotification('Terjadi kesalahan saat mencari data', 'error');
        })
        .finally(() => {
            // Hide loading spinner
            searchSpinner.classList.add('hidden');
            tableContainer.classList.remove('loading-state');
        });
    }

    // Handle pagination clicks
    document.addEventListener('click', function(e) {
        if (e.target.closest('a[href*="page="]')) {
            e.preventDefault();
            const link = e.target.closest('a');
            const url = new URL(link.href);
            
            // Show loading
            tableContainer.classList.add('loading-state');
            
            // Make AJAX request for pagination
            fetch(url.toString(), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    tableContainer.innerHTML = data.html;
                    window.history.replaceState({}, '', url.toString());
                    
                    // Re-bind perPage event listener
                    bindPerPageSelector();
                    
                    // Scroll to top of table
                    tableContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            })
            .catch(error => {
                console.error('Pagination error:', error);
                // Fallback to normal navigation
                window.location.href = link.href;
            })
            .finally(() => {
                tableContainer.classList.remove('loading-state');
            });
        }
    });

    // Handle perPage selector change
    function bindPerPageSelector() {
        const perPageSelect = document.getElementById('perPageSelect');
        if (perPageSelect) {
            // Remove existing event listeners
            perPageSelect.replaceWith(perPageSelect.cloneNode(true));
            const newPerPageSelect = document.getElementById('perPageSelect');
            
            newPerPageSelect.addEventListener('change', function() {
                const perPage = this.value;
                const keyword = searchInput.value.trim();
                
                // Show loading
                tableContainer.classList.add('loading-state');
                
                // Prepare URL
                const url = new URL(window.location.href);
                url.searchParams.set('perPage', perPage);
                url.searchParams.set('page', '1'); // Reset to first page
                if (keyword) {
                    url.searchParams.set('keyword', keyword);
                } else {
                    url.searchParams.delete('keyword');
                }
                
                // Make AJAX request
                fetch(url.toString(), {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        tableContainer.innerHTML = data.html;
                        totalCount.textContent = data.data.totalRecords;
                        window.history.replaceState({}, '', url.toString());
                        
                        // Re-bind perPage event listener
                        bindPerPageSelector();
                    }
                })
                .catch(error => {
                    console.error('PerPage error:', error);
                    showNotification('Terjadi kesalahan saat mengubah jumlah data per halaman', 'error');
                })
                .finally(() => {
                    tableContainer.classList.remove('loading-state');
                });
            });
        }
    }

    // Initial binding
    bindPerPageSelector();

    // Notification function
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full max-w-sm`;
        
        const colors = {
            success: 'bg-green-500 text-white',
            error: 'bg-red-500 text-white',
            info: 'bg-blue-500 text-white',
            warning: 'bg-yellow-500 text-white'
        };
        
        notification.className += ` ${colors[type]}`;
        notification.innerHTML = `
            <div class="flex items-center">
                <span class="text-sm font-medium">${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => notification.classList.remove('translate-x-full'), 100);
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }

    // Auto-hide flash messages
    setTimeout(() => {
        const alerts = document.querySelectorAll('.animate-fade-in');
        alerts.forEach(alert => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 300);
        });
    }, 5000);

    // Mobile actions dropdown
    const mobileActionsToggle = document.getElementById('mobileActionsToggle');
    const mobileActionsMenu = document.getElementById('mobileActionsMenu');
    
    if (mobileActionsToggle && mobileActionsMenu) {
        mobileActionsToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            mobileActionsMenu.classList.toggle('hidden');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!mobileActionsToggle.contains(e.target) && !mobileActionsMenu.contains(e.target)) {
                mobileActionsMenu.classList.add('hidden');
            }
        });
    }
});
</script>

<?= $this->endSection(); ?>

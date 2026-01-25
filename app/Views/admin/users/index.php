<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<!-- Page Header - Mobile Optimized -->
<div class="mb-6 sm:mb-8 lg:mb-10 px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-4 sm:gap-6">
        <div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-3">
                <i class="fas fa-users text-purple-600 mr-3 sm:mr-4 text-2xl sm:text-3xl lg:text-4xl"></i>Kelola User
            </h1>
            <p class="text-base sm:text-lg text-gray-600">Kelola pengguna sistem dengan mudah dan terorganisir</p>
        </div>
        
        <!-- Action Buttons - Mobile Optimized -->
        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
            <button 
                onclick="openAddUserModal()"
                class="group inline-flex items-center justify-center px-6 sm:px-8 py-3.5 sm:py-4 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl hover:from-purple-700 hover:to-indigo-700 focus:ring-4 focus:ring-purple-200 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 text-base sm:text-lg touch-manipulation">
                <i class="fas fa-plus mr-3 group-hover:scale-110 transition-transform duration-300 text-lg sm:text-xl"></i>
                <span>Tambah User Baru</span>
            </button>
            
                        <div class="grid grid-cols-1 sm:flex sm:flex-row gap-3 sm:gap-4">
                            <button 
                                    onclick="generateWalikelas()"
                                    class="group inline-flex items-center justify-center px-6 sm:px-8 py-3.5 sm:py-4 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 focus:ring-4 focus:ring-green-200 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 text-base sm:text-lg touch-manipulation">
                                    <i class="fas fa-user-tie mr-3 group-hover:scale-110 transition-transform duration-300 text-lg sm:text-xl"></i>
                                    <span>Generate Walikelas</span>
                            </button>
                            <button 
                                    onclick="generateSiswa()"
                                    class="group inline-flex items-center justify-center px-6 sm:px-8 py-3.5 sm:py-4 bg-gradient-to-r from-sky-600 to-blue-600 text-white rounded-xl hover:from-sky-700 hover:to-blue-700 focus:ring-4 focus:ring-sky-200 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 text-base sm:text-lg touch-manipulation">
                                    <i class="fas fa-user-graduate mr-3 group-hover:scale-110 transition-transform duration-300 text-lg sm:text-xl"></i>
                                    <span>Generate Siswa</span>
                            </button>
                        </div>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-400 p-4 mb-6 rounded-r-xl shadow-sm animate-slideInDown" id="successAlert">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-500 text-lg"></i>
            </div>
            <div class="ml-3">
                <p class="text-green-800 font-medium"><?= session()->getFlashdata('success') ?></p>
            </div>
            <div class="ml-auto pl-3">
                <button type="button" onclick="closeAlert('successAlert')" class="text-green-500 hover:text-green-700 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-400 p-4 mb-6 rounded-r-xl shadow-sm animate-slideInDown" id="errorAlert">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-500 text-lg"></i>
            </div>
            <div class="ml-3">
                <p class="text-red-800 font-medium"><?= session()->getFlashdata('error') ?></p>
            </div>
            <div class="ml-auto pl-3">
                <button type="button" onclick="closeAlert('errorAlert')" class="text-red-500 hover:text-red-700 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Filter and Search Section -->
<div class="bg-white rounded-lg sm:rounded-xl shadow-lg border border-gray-100 p-3 sm:p-4 lg:p-6 mb-4 sm:mb-6 lg:mb-8">
    <div class="flex flex-col gap-3 sm:gap-4 lg:gap-6">
        <!-- Mobile Filter Toggle -->
        <div class="flex items-center justify-between sm:hidden">
            <h3 class="text-base font-semibold text-gray-800">Filter & Pencarian</h3>
            <button 
                id="mobileFilterToggle"
                class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                <i class="fas fa-filter text-sm"></i>
            </button>
        </div>
        
        <!-- Filter Content -->
        <div id="filterContent" class="hidden sm:block">
            <div class="flex flex-col sm:flex-row lg:flex-row gap-3 sm:gap-4 lg:gap-6">
                <!-- Search Bar -->
                <div class="flex-1">
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                        <i class="fas fa-search text-gray-400 mr-1 sm:mr-2 text-xs sm:text-sm"></i>
                        <span class="hidden sm:inline">Pencarian</span>
                        <span class="sm:hidden">Cari</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="searchInput"
                            placeholder="Cari nama atau email..."
                            class="w-full pl-8 sm:pl-11 pr-8 sm:pr-4 py-2 sm:py-3 border-2 border-gray-200 rounded-lg sm:rounded-xl focus:ring-2 sm:focus:ring-3 focus:ring-purple-100 focus:border-purple-500 transition-all duration-300 text-gray-900 placeholder-gray-400 text-sm sm:text-base">
                        <div class="absolute left-2 sm:left-3 top-2 sm:top-3.5 text-gray-400">
                            <i class="fas fa-search text-xs sm:text-sm"></i>
                        </div>
                        <button 
                            onclick="clearSearch()"
                            class="absolute right-2 sm:right-3 top-2 sm:top-3 p-1 text-gray-400 hover:text-gray-600 transition-colors duration-200 hidden"
                            id="clearSearchBtn">
                            <i class="fas fa-times text-xs sm:text-sm"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Role Filter -->
                <div class="sm:w-40 lg:w-48">
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                        <i class="fas fa-user-tag text-gray-400 mr-1 sm:mr-2 text-xs sm:text-sm"></i>
                        <span class="hidden sm:inline">Filter Role</span>
                        <span class="sm:hidden">Role</span>
                    </label>
                    <div class="relative">
                        <select 
                            id="roleFilter"
                            class="w-full pl-8 sm:pl-11 pr-8 sm:pr-10 py-2 sm:py-3 border-2 border-gray-200 rounded-lg sm:rounded-xl focus:ring-2 sm:focus:ring-3 focus:ring-purple-100 focus:border-purple-500 transition-all duration-300 text-gray-900 bg-white appearance-none text-sm sm:text-base">
                            <option value="">Semua</option>
                            <option value="admin">Admin</option>
                            <option value="guru">Guru</option>
                            <option value="walikelas">Wali Kelas</option>
                            <option value="siswa">Siswa</option>
                        </select>
                        <div class="absolute left-2 sm:left-3 top-2 sm:top-3.5 text-gray-400">
                            <i class="fas fa-user-cog text-xs sm:text-sm"></i>
                        </div>
                        <div class="absolute right-2 sm:right-3 top-2 sm:top-3.5 text-gray-400 pointer-events-none">
                            <i class="fas fa-chevron-down text-xs sm:text-sm"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Status Filter -->
                <div class="sm:w-40 lg:w-48">
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                        <i class="fas fa-toggle-on text-gray-400 mr-1 sm:mr-2 text-xs sm:text-sm"></i>
                        <span class="hidden sm:inline">Filter Status</span>
                        <span class="sm:hidden">Status</span>
                    </label>
                    <div class="relative">
                        <select 
                            id="statusFilter"
                            class="w-full pl-8 sm:pl-11 pr-8 sm:pr-10 py-2 sm:py-3 border-2 border-gray-200 rounded-lg sm:rounded-xl focus:ring-2 sm:focus:ring-3 focus:ring-purple-100 focus:border-purple-500 transition-all duration-300 text-gray-900 bg-white appearance-none text-sm sm:text-base">
                            <option value="">Semua</option>
                            <option value="active">Aktif</option>
                            <option value="inactive">Tidak Aktif</option>
                        </select>
                        <div class="absolute left-2 sm:left-3 top-2 sm:top-3.5 text-gray-400">
                            <i class="fas fa-circle text-xs sm:text-sm"></i>
                        </div>
                        <div class="absolute right-2 sm:right-3 top-2 sm:top-3.5 text-gray-400 pointer-events-none">
                            <i class="fas fa-chevron-down text-xs sm:text-sm"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="mt-3 sm:mt-4 lg:mt-6 pt-3 sm:pt-4 lg:pt-6 border-t border-gray-200">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3 lg:gap-4">
                <div class="text-center p-2 sm:p-3 lg:p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg">
                    <div class="text-lg sm:text-xl lg:text-2xl font-bold text-blue-600" id="totalUsers"><?= isset($users) ? count($users) : 0 ?></div>
                    <div class="text-xs sm:text-sm text-gray-600">Total User</div>
                </div>
                <div class="text-center p-2 sm:p-3 lg:p-4 bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg">
                    <div class="text-lg sm:text-xl lg:text-2xl font-bold text-green-600" id="activeUsers"><?= isset($users) ? count(array_filter($users, fn($u) => $u['is_active'] ?? false)) : 0 ?></div>
                    <div class="text-xs sm:text-sm text-gray-600">User Aktif</div>
                </div>
                <div class="text-center p-2 sm:p-3 lg:p-4 bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg">
                    <div class="text-lg sm:text-xl lg:text-2xl font-bold text-purple-600" id="adminUsers"><?= isset($users) ? count(array_filter($users, fn($u) => $u['role'] === 'admin')) : 0 ?></div>
                    <div class="text-xs sm:text-sm text-gray-600">Admin</div>
                </div>
                <div class="text-center p-2 sm:p-3 lg:p-4 bg-gradient-to-br from-orange-50 to-red-50 rounded-lg">
                    <div class="text-lg sm:text-xl lg:text-2xl font-bold text-orange-600" id="teacherUsers"><?= isset($users) ? count(array_filter($users, fn($u) => in_array($u['role'], ['guru', 'walikelas']))) : 0 ?></div>
                    <div class="text-xs sm:text-sm text-gray-600">Guru</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Users Grid -->
<div id="usersContainer">
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6" id="usersGrid">
        <?php if (isset($users) && !empty($users)): ?>
            <?php foreach ($users as $user): ?>
                <div class="user-card bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1" 
                     data-role="<?= $user['role'] ?>" 
                     data-status="<?= ($user['is_active'] ?? false) ? 'active' : 'inactive' ?>"
                     data-name="<?= strtolower($user['nama'] ?? '') ?>"
                     data-email="<?= strtolower($user['email'] ?? '') ?>">
                    
                    <div class="relative">
                        <!-- Status Indicator -->
                        <div class="absolute top-4 right-4 flex items-center space-x-2">
                            <?php if ($user['is_active'] ?? false): ?>
                                <div class="flex items-center bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">
                                    <div class="w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></div>
                                    Aktif
                                </div>
                            <?php else: ?>
                                <div class="flex items-center bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full">
                                    <div class="w-2 h-2 bg-red-500 rounded-full mr-1"></div>
                                    Tidak Aktif
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Avatar Section -->
                        <div class="p-6 pb-0">
                            <div class="flex justify-center">
                                <div class="relative">
                                    <?php
                                    $avatarUrl = isset($user['avatar']) && !empty($user['avatar']) 
                                        ? base_url('uploads/avatars/' . $user['avatar']) 
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($user['nama'] ?? 'User') . '&background=random&size=100';
                                    ?>
                                    <img 
                                        src="<?= $avatarUrl ?>" 
                                        alt="Avatar"
                                        class="w-20 h-20 rounded-full border-4 border-white shadow-lg object-cover <?= !($user['is_active'] ?? false) ? 'opacity-75' : '' ?>"
                                        onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik01MCA0NkM1OC4yODQzIDQ2IDY1IDM5LjI4NDMgNjUgMzFDNjUgMjIuNzE1NyA1OC4yODQzIDE2IDUwIDE2QzQxLjcxNTcgMTYgMzUgMjIuNzE1NyAzNSAzMUMzNSAzOS4yODQzIDQxLjcxNTcgNDYgNTAgNDZaTTUwIDUyQzM5LjUwNjYgNTIgMzEgNjAuNTA2NiAzMSA3MUM3NSA3MSA2OS40OTM0IDUyIDUwIDUyWiIgZmlsbD0iIzlDQTNBRiIvPgo8L3N2Zz4K'">
                                    
                                    <!-- Role Badge -->
                                    <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2">
                                        <?php
                                        $roleColors = [
                                            'admin' => 'bg-purple-100 text-purple-800',
                                            'guru' => 'bg-blue-100 text-blue-800',
                                            'walikelas' => 'bg-indigo-100 text-indigo-800',
                                            'siswa' => 'bg-green-100 text-green-800'
                                        ];
                                        $roleIcons = [
                                            'admin' => 'fas fa-crown',
                                            'guru' => 'fas fa-chalkboard-teacher',
                                            'walikelas' => 'fas fa-users',
                                            'siswa' => 'fas fa-user-graduate'
                                        ];
                                        $roleColor = $roleColors[$user['role']] ?? 'bg-gray-100 text-gray-800';
                                        $roleIcon = $roleIcons[$user['role']] ?? 'fas fa-user';
                                        ?>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= $roleColor ?> border-2 border-white">
                                            <i class="<?= $roleIcon ?> mr-1"></i>
                                            <?= ucfirst($user['role']) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- User Info -->
                    <div class="p-6 pt-4">
                        <div class="text-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1"><?= esc($user['nama'] ?? 'Unknown User') ?></h3>
                            <p class="text-sm text-gray-500 flex items-center justify-center">
                                <i class="fas fa-envelope mr-1"></i>
                                <?= esc($user['email'] ?? 'No email') ?>
                            </p>
                        </div>
                        
                        <!-- Additional Info -->
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-user w-4 text-gray-400 mr-2"></i>
                                @<?= esc($user['username'] ?? 'no-username') ?>
                            </div>
                            <?php if (isset($user['kelas']) && !empty($user['kelas'])): ?>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-graduation-cap w-4 text-gray-400 mr-2"></i>
                                    Kelas: <?= esc($user['kelas']) ?>
                                </div>
                            <?php endif; ?>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-clock w-4 text-gray-400 mr-2"></i>
                                <?= isset($user['last_login']) ? 'Login: ' . date('d M Y', strtotime($user['last_login'])) : 'Belum pernah login' ?>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex space-x-2">
                            <button 
                                onclick="editUser(<?= $user['id'] ?? 0 ?>)"
                                class="flex-1 flex items-center justify-center px-3 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition-all duration-200 text-sm font-medium">
                                <i class="fas fa-edit mr-1"></i>
                                Edit
                            </button>
                            <button 
                                onclick="resetUserPassword(<?= $user['id'] ?? 0 ?>, '<?= esc($user['nama'] ?? 'Unknown User') ?>')"
                                class="flex items-center justify-center px-3 py-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 focus:ring-2 focus:ring-green-500 focus:ring-offset-1 transition-all duration-200 text-sm"
                                title="Reset Password">
                                <i class="fas fa-key"></i>
                            </button>
                            <button 
                                onclick="toggleUserStatus(<?= $user['id'] ?? 0 ?>, '<?= ($user['is_active'] ?? false) ? 'active' : 'inactive' ?>')"
                                class="flex items-center justify-center px-3 py-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 focus:ring-2 focus:ring-yellow-500 focus:ring-offset-1 transition-all duration-200 text-sm">
                                <i class="fas fa-toggle-<?= ($user['is_active'] ?? false) ? 'on' : 'off' ?>"></i>
                            </button>
                            <?php if (($user['id'] ?? 0) != session('user_id')): ?>
                                <button 
                                    onclick="deleteUser(<?= $user['id'] ?? 0 ?>)"
                                    class="flex items-center justify-center px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-all duration-200 text-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Empty State -->
            <div class="col-span-full text-center py-16">
                <div class="max-w-md mx-auto">
                    <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada user</h3>
                    <p class="text-gray-500 mb-6">Mulai dengan menambahkan user baru ke sistem.</p>
                    <button 
                        onclick="openAddUserModal()"
                        class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah User Pertama
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Empty State for Filtered Results -->
    <div id="emptyState" class="text-center py-16 hidden">
        <div class="max-w-md mx-auto">
            <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                <i class="fas fa-search text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada user ditemukan</h3>
            <p class="text-gray-500 mb-6">Coba ubah filter atau kata kunci pencarian Anda.</p>
            <button 
                onclick="resetFilters()"
                class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                <i class="fas fa-undo mr-2"></i>
                Reset Filter
            </button>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeAddUserModal()"></div>
        
        <!-- Modal panel -->
        <div class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
            <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-user-plus text-purple-600 mr-2"></i>
                    Tambah User Baru
                </h3>
                <button 
                    onclick="closeAddUserModal()"
                    class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="addUserForm" class="mt-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="nama"
                            required
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-3 focus:ring-purple-100 focus:border-purple-500 transition-all duration-300">
                    </div>
                    
                    <!-- Username -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Username <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="username"
                            required
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-3 focus:ring-purple-100 focus:border-purple-500 transition-all duration-300">
                    </div>
                    
                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>
                        <input 
                            type="email" 
                            name="email"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-3 focus:ring-purple-100 focus:border-purple-500 transition-all duration-300">
                    </div>
                    
                    <!-- Role -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="role"
                            required
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-3 focus:ring-purple-100 focus:border-purple-500 transition-all duration-300 bg-white">
                            <option value="">Pilih Role</option>
                            <option value="admin">Admin</option>
                            <option value="guru">Guru</option>
                            <option value="walikelas">Wali Kelas</option>
                            <option value="siswa">Siswa</option>
                        </select>
                    </div>
                    
                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="password" 
                            name="password"
                            required
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-3 focus:ring-purple-100 focus:border-purple-500 transition-all duration-300">
                    </div>
                    
                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Konfirmasi Password <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="password" 
                            name="password_confirm"
                            required
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-3 focus:ring-purple-100 focus:border-purple-500 transition-all duration-300">
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 mt-8">
                    <button 
                        type="button"
                        onclick="closeAddUserModal()"
                        class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-300 font-medium">
                        Batal
                    </button>
                    <button 
                        type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl hover:from-purple-700 hover:to-indigo-700 transition-all duration-300 font-semibold">
                        <i class="fas fa-save mr-2"></i>
                        Simpan User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Animations */
@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-slideInDown {
    animation: slideInDown 0.5s ease-out;
}

.animate-fadeInUp {
    animation: fadeInUp 0.6s ease-out;
}

/* User Cards */
.user-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.user-card:hover {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
}

/* Status indicators */
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Modal animations */
#addUserModal {
    backdrop-filter: blur(4px);
}

/* Focus states */
input:focus, select:focus, textarea:focus {
    outline: none !important;
    box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.1) !important;
}

/* Grid responsiveness */
@media (max-width: 768px) {
    .grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
}

/* Loading state */
.loading {
    opacity: 0.7;
    pointer-events: none;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    border: 2px solid #e5e7eb;
    border-top-color: #8b5cf6;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    transform: translate(-50%, -50%);
}

@keyframes spin {
    to {
        transform: translate(-50%, -50%) rotate(360deg);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeUserManagement();
});

function initializeUserManagement() {
    // Initialize search and filters
    const searchInput = document.getElementById('searchInput');
    const roleFilter = document.getElementById('roleFilter');
    const statusFilter = document.getElementById('statusFilter');
    
    // Add event listeners
    searchInput.addEventListener('input', handleSearch);
    roleFilter.addEventListener('change', handleFilter);
    statusFilter.addEventListener('change', handleFilter);
    
    // Initialize stats
    updateStats();
    
    // Auto-hide alerts
    setTimeout(() => {
        const alerts = document.querySelectorAll('[id$="Alert"]');
        alerts.forEach(alert => {
            if (alert) {
                alert.style.opacity = '0';
                alert.style.transform = 'translateX(-20px)';
                setTimeout(() => alert.remove(), 500);
            }
        });
    }, 5000);
}

function handleSearch() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const clearBtn = document.getElementById('clearSearchBtn');
    
    // Show/hide clear button
    if (searchTerm) {
        clearBtn.classList.remove('hidden');
    } else {
        clearBtn.classList.add('hidden');
    }
    
    filterUsers();
}

function handleFilter() {
    filterUsers();
}

function filterUsers() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const roleFilter = document.getElementById('roleFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    const userCards = document.querySelectorAll('.user-card');
    
    let visibleCount = 0;
    
    userCards.forEach(card => {
        const name = card.dataset.name || '';
        const email = card.dataset.email || '';
        const role = card.dataset.role || '';
        const status = card.dataset.status || '';
        
        const matchesSearch = !searchTerm || name.includes(searchTerm) || email.includes(searchTerm);
        const matchesRole = !roleFilter || role === roleFilter;
        const matchesStatus = !statusFilter || status === statusFilter;
        
        if (matchesSearch && matchesRole && matchesStatus) {
            card.style.display = 'block';
            card.classList.add('animate-fadeInUp');
            visibleCount++;
        } else {
            card.style.display = 'none';
            card.classList.remove('animate-fadeInUp');
        }
    });
    
    // Show/hide empty state
    const emptyState = document.getElementById('emptyState');
    const usersGrid = document.getElementById('usersGrid');
    
    if (visibleCount === 0 && userCards.length > 0) {
        usersGrid.style.display = 'none';
        emptyState.classList.remove('hidden');
    } else {
        usersGrid.style.display = 'grid';
        emptyState.classList.add('hidden');
    }
}

function clearSearch() {
    document.getElementById('searchInput').value = '';
    document.getElementById('clearSearchBtn').classList.add('hidden');
    filterUsers();
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('roleFilter').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('clearSearchBtn').classList.add('hidden');
    filterUsers();
}

function updateStats() {
    const userCards = document.querySelectorAll('.user-card');
    const totalUsers = userCards.length;
    let activeUsers = 0;
    let adminUsers = 0;
    let teacherUsers = 0;
    
    userCards.forEach(card => {
        if (card.dataset.status === 'active') activeUsers++;
        if (card.dataset.role === 'admin') adminUsers++;
        if (card.dataset.role === 'guru' || card.dataset.role === 'walikelas') teacherUsers++;
    });
    
    // Animate counters
    animateCounter('totalUsers', totalUsers);
    animateCounter('activeUsers', activeUsers);
    animateCounter('adminUsers', adminUsers);
    animateCounter('teacherUsers', teacherUsers);
}

function animateCounter(elementId, target) {
    const element = document.getElementById(elementId);
    if (!element) return;
    
    const duration = 1000;
    const start = parseInt(element.textContent) || 0;
    const increment = (target - start) / (duration / 16);
    
    let current = start;
    const timer = setInterval(() => {
        current += increment;
        if ((increment > 0 && current >= target) || (increment < 0 && current <= target)) {
            current = target;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current);
    }, 16);
}

// Modal functions
function openAddUserModal() {
    document.getElementById('addUserModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeAddUserModal() {
    document.getElementById('addUserModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('addUserForm').reset();
}

// User actions
function editUser(userId) {
    showNotification(`Mengedit user dengan ID: ${userId}`, 'info');
    // In real implementation: window.location.href = `/admin/users/edit/${userId}`;
}

function toggleUserStatus(userId, currentStatus) {
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
    const statusText = newStatus === 'active' ? 'mengaktifkan' : 'menonaktifkan';
    
    if (confirm(`Apakah Anda yakin ingin ${statusText} user ini?`)) {
        showNotification(`User berhasil ${newStatus === 'active' ? 'diaktifkan' : 'dinonaktifkan'}`, 'success');
        
        // Update UI
        const userCard = document.querySelector(`[onclick*="toggleUserStatus(${userId}"]`).closest('.user-card');
        const statusIndicator = userCard.querySelector('.absolute.top-4.right-4 div');
        const avatar = userCard.querySelector('img');
        
        if (newStatus === 'active') {
            statusIndicator.className = 'flex items-center bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full';
            statusIndicator.innerHTML = '<div class="w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></div>Aktif';
            avatar.classList.remove('opacity-75');
            userCard.dataset.status = 'active';
        } else {
            statusIndicator.className = 'flex items-center bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full';
            statusIndicator.innerHTML = '<div class="w-2 h-2 bg-red-500 rounded-full mr-1"></div>Tidak Aktif';
            avatar.classList.add('opacity-75');
            userCard.dataset.status = 'inactive';
        }
        
        updateStats();
    }
}

function resetUserPassword(userId, userName) {
    if (confirm(`Apakah Anda yakin ingin mereset password user "${userName}" ke password default (guru123456)?`)) {
        // Show loading notification
        showNotification('Sedang mereset password...', 'info');
        
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `<?= base_url('admin/users/reset-password/') ?>${userId}`;
        
        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '<?= csrf_token() ?>';
        csrfInput.value = '<?= csrf_hash() ?>';
        form.appendChild(csrfInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteUser(userId) {
    if (confirm('Apakah Anda yakin ingin menghapus user ini? Tindakan ini tidak dapat dibatalkan.')) {
        showNotification('User berhasil dihapus', 'success');
        
        // Remove user card with animation
        const userCard = document.querySelector(`[onclick*="deleteUser(${userId})"]`).closest('.user-card');
        userCard.style.opacity = '0';
        userCard.style.transform = 'scale(0.95)';
        setTimeout(() => {
            userCard.remove();
            updateStats();
            filterUsers();
        }, 300);
    }
}

// Form submission
document.getElementById('addUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Add loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
    submitBtn.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        showNotification('User baru berhasil ditambahkan', 'success');
        closeAddUserModal();
        
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        updateStats();
        // In real implementation, you would add the new user card here
    }, 2000);
});

// Utility functions
function closeAlert(alertId) {
    const alert = document.getElementById(alertId);
    if (alert) {
        alert.style.opacity = '0';
        alert.style.transform = 'translateX(-20px)';
        setTimeout(() => alert.remove(), 300);
    }
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full max-w-sm`;
    
    const colors = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        info: 'bg-blue-500 text-white',
        warning: 'bg-yellow-500 text-white'
    };
    
    const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        info: 'fas fa-info-circle',
        warning: 'fas fa-exclamation-triangle'
    };
    
    notification.className += ` ${colors[type]}`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="${icons[type]} mr-2"></i>
            <span class="text-sm font-medium">${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 5000);
}

// Generate Accounts function
function postTo(url){
    // Create and submit a small form with CSRF
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = url;
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '<?= csrf_token() ?>';
    csrfInput.value = '<?= csrf_hash() ?>';
    form.appendChild(csrfInput);
    document.body.appendChild(form);
    form.submit();
}

function generateWalikelas() {
    if (confirm('Generate akun untuk semua wali kelas dari data guru bertugas?')) {
        showNotification('Memproses generate akun walikelas...', 'info');
        postTo('<?= base_url('admin/users/generate-walikelas') ?>');
    }
}

function generateSiswa() {
    if (confirm('Generate akun siswa berdasarkan data NISN di tb_siswa? Username=NISN, password default="siswa123".')) {
        showNotification('Memproses generate akun siswa...', 'info');
        postTo('<?= base_url('admin/users/generate-siswa') ?>');
    }
}

// Mobile filter toggle
const mobileFilterToggle = document.getElementById('mobileFilterToggle');
const filterContent = document.getElementById('filterContent');

if (mobileFilterToggle && filterContent) {
    mobileFilterToggle.addEventListener('click', function() {
        const isHidden = filterContent.classList.contains('hidden');
        
        if (isHidden) {
            filterContent.classList.remove('hidden');
            filterContent.classList.add('block');
            this.querySelector('i').classList.remove('fa-filter');
            this.querySelector('i').classList.add('fa-times');
        } else {
            filterContent.classList.add('hidden');
            filterContent.classList.remove('block');
            this.querySelector('i').classList.remove('fa-times');
            this.querySelector('i').classList.add('fa-filter');
        }
    });
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + K for search focus
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        document.getElementById('searchInput').focus();
    }
    
    // Escape to close modal
    if (e.key === 'Escape') {
        closeAddUserModal();
    }
    
    // Ctrl/Cmd + N for new user
    if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
        e.preventDefault();
        openAddUserModal();
    }
});
</script>

<?= $this->endSection() ?>

<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>Kelola User - SDN Grogol Utara 09<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
/* Glassmorphism Style */
.glassmorphism {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.glassmorphism-light {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Animated Background */
.animated-bg {
    background: linear-gradient(-45deg, #667eea, #764ba2, #f093fb, #f5576c);
    background-size: 400% 400%;
    animation: gradientShift 15s ease infinite;
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Card Animations */
.user-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    transform: translateY(0);
}

.user-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

/* Stat Card Animation */
.stat-card {
    animation: slideInUp 0.6s ease-out forwards;
    opacity: 0;
    transform: translateY(20px);
}

.stat-card:nth-child(1) { animation-delay: 0.1s; }
.stat-card:nth-child(2) { animation-delay: 0.2s; }
.stat-card:nth-child(3) { animation-delay: 0.3s; }

@keyframes slideInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Floating Animation */
.floating {
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

/* Button Hover Effects */
.btn-hover {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.btn-hover::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transition: left 0.5s;
}

.btn-hover:hover::before {
    left: 100%;
}

/* Pulsing Animation */
.pulse-slow {
    animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Search Input Focus */
.search-focus:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);
    transform: scale(1.02);
}
</style>

<div class="min-h-screen animated-bg">
    <div class="p-6 max-w-7xl mx-auto">
        <!-- Header -->
        <div class="glassmorphism rounded-3xl p-8 mb-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 glassmorphism-light rounded-2xl flex items-center justify-center floating">
                        <i class="fas fa-users text-3xl text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">Kelola User</h1>
                        <p class="text-gray-200">Manajemen pengguna sistem sekolah</p>
                    </div>
                </div>
                
                <button onclick="openAddUserModal()" class="btn-hover bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white px-8 py-4 rounded-2xl shadow-lg transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah User
                </button>
            </div>
            
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="stat-card glassmorphism-light rounded-2xl p-6 text-center">
                    <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                    <div class="stat-number text-3xl font-bold text-white mb-1">12</div>
                    <div class="text-gray-300 text-sm">Total User</div>
                </div>
                
                <div class="stat-card glassmorphism-light rounded-2xl p-6 text-center">
                    <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-check-circle text-white text-xl"></i>
                    </div>
                    <div class="stat-number text-3xl font-bold text-white mb-1">10</div>
                    <div class="text-gray-300 text-sm">User Aktif</div>
                </div>
                
                <div class="stat-card glassmorphism-light rounded-2xl p-6 text-center">
                    <div class="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-times-circle text-white text-xl"></i>
                    </div>
                    <div class="stat-number text-3xl font-bold text-white mb-1">2</div>
                    <div class="text-gray-300 text-sm">User Tidak Aktif</div>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" 
                               id="searchInput" 
                               placeholder="Cari user berdasarkan nama atau email..."
                               class="search-focus w-full pl-12 pr-4 py-4 glassmorphism-light rounded-2xl text-white placeholder-gray-300 transition-all duration-300">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-300"></i>
                    </div>
                </div>
                
                <select id="roleFilter" class="px-6 py-4 glassmorphism-light rounded-2xl text-white transition-all duration-300">
                    <option value="all">Semua Role</option>
                    <option value="Admin">Admin</option>
                    <option value="Guru">Guru</option>
                    <option value="Siswa">Siswa</option>
                    <option value="Wali Kelas">Wali Kelas</option>
                </select>
                
                <select id="statusFilter" class="px-6 py-4 glassmorphism-light rounded-2xl text-white transition-all duration-300">
                    <option value="all">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Tidak Aktif</option>
                </select>
            </div>
        </div>
        
        <!-- User Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="userGrid">
            <!-- User Card 1 -->
            <div class="user-card glassmorphism rounded-3xl p-6 relative overflow-hidden" data-status="active">
                <div class="absolute top-4 right-4">
                    <div class="flex items-center bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></div>
                        Aktif
                    </div>
                </div>
                
                <div class="text-center mb-6">
                    <img src="https://ui-avatars.com/api/?name=Ahmad+Rizki&background=667eea&color=fff&size=80&rounded=true" alt="Avatar" class="w-20 h-20 rounded-2xl mx-auto mb-4 shadow-lg">
                    <h3 class="text-xl font-bold text-white mb-1">Ahmad Rizki</h3>
                    <p class="text-gray-400 text-sm mb-2">ahmad.rizki@sdn09.sch.id</p>
                    <span class="text-xs font-medium px-3 py-1 rounded-full bg-purple-100 text-purple-800">Admin</span>
                </div>
                
                <div class="flex gap-2">
                    <button onclick="editUser(1)" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 px-3 rounded-xl text-sm transition-colors duration-200">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </button>
                    <button onclick="toggleUserStatus(1, 'active')" class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-3 rounded-xl text-sm transition-colors duration-200">
                        <i class="fas fa-toggle-on mr-1"></i>Toggle
                    </button>
                    <button onclick="deleteUser(1)" class="bg-red-500 hover:bg-red-600 text-white py-2 px-3 rounded-xl text-sm transition-colors duration-200">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            
            <!-- User Card 2 -->
            <div class="user-card glassmorphism rounded-3xl p-6 relative overflow-hidden" data-status="active">
                <div class="absolute top-4 right-4">
                    <div class="flex items-center bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></div>
                        Aktif
                    </div>
                </div>
                
                <div class="text-center mb-6">
                    <img src="https://ui-avatars.com/api/?name=Siti+Nurhaliza&background=764ba2&color=fff&size=80&rounded=true" alt="Avatar" class="w-20 h-20 rounded-2xl mx-auto mb-4 shadow-lg">
                    <h3 class="text-xl font-bold text-white mb-1">Siti Nurhaliza</h3>
                    <p class="text-gray-400 text-sm mb-2">siti.nur@sdn09.sch.id</p>
                    <span class="text-xs font-medium px-3 py-1 rounded-full bg-blue-100 text-blue-800">Guru</span>
                </div>
                
                <div class="flex gap-2">
                    <button onclick="editUser(2)" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 px-3 rounded-xl text-sm transition-colors duration-200">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </button>
                    <button onclick="toggleUserStatus(2, 'active')" class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-3 rounded-xl text-sm transition-colors duration-200">
                        <i class="fas fa-toggle-on mr-1"></i>Toggle
                    </button>
                    <button onclick="deleteUser(2)" class="bg-red-500 hover:bg-red-600 text-white py-2 px-3 rounded-xl text-sm transition-colors duration-200">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            
            <!-- User Card 3 -->
            <div class="user-card glassmorphism rounded-3xl p-6 relative overflow-hidden" data-status="active">
                <div class="absolute top-4 right-4">
                    <div class="flex items-center bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></div>
                        Aktif
                    </div>
                </div>
                
                <div class="text-center mb-6">
                    <img src="https://ui-avatars.com/api/?name=Budi+Santoso&background=f093fb&color=fff&size=80&rounded=true" alt="Avatar" class="w-20 h-20 rounded-2xl mx-auto mb-4 shadow-lg">
                    <h3 class="text-xl font-bold text-white mb-1">Budi Santoso</h3>
                    <p class="text-gray-400 text-sm mb-2">budi.santoso@sdn09.sch.id</p>
                    <span class="text-xs font-medium px-3 py-1 rounded-full bg-orange-100 text-orange-800">Wali Kelas</span>
                </div>
                
                <div class="flex gap-2">
                    <button onclick="editUser(3)" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 px-3 rounded-xl text-sm transition-colors duration-200">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </button>
                    <button onclick="toggleUserStatus(3, 'active')" class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-3 rounded-xl text-sm transition-colors duration-200">
                        <i class="fas fa-toggle-on mr-1"></i>Toggle
                    </button>
                    <button onclick="deleteUser(3)" class="bg-red-500 hover:bg-red-600 text-white py-2 px-3 rounded-xl text-sm transition-colors duration-200">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            
            <!-- User Card 4 - Inactive -->
            <div class="user-card glassmorphism rounded-3xl p-6 relative overflow-hidden" data-status="inactive">
                <div class="absolute top-4 right-4">
                    <div class="flex items-center bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full">
                        <div class="w-2 h-2 bg-red-500 rounded-full mr-1"></div>
                        Tidak Aktif
                    </div>
                </div>
                
                <div class="text-center mb-6">
                    <img src="https://ui-avatars.com/api/?name=Maya+Sari&background=f5576c&color=fff&size=80&rounded=true" alt="Avatar" class="w-20 h-20 rounded-2xl mx-auto mb-4 shadow-lg opacity-75">
                    <h3 class="text-xl font-bold text-white mb-1">Maya Sari</h3>
                    <p class="text-gray-400 text-sm mb-2">maya.sari@sdn09.sch.id</p>
                    <span class="text-xs font-medium px-3 py-1 rounded-full bg-green-100 text-green-800">Siswa</span>
                </div>
                
                <div class="flex gap-2">
                    <button onclick="editUser(4)" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 px-3 rounded-xl text-sm transition-colors duration-200">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </button>
                    <button onclick="toggleUserStatus(4, 'inactive')" class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-3 rounded-xl text-sm transition-colors duration-200">
                        <i class="fas fa-toggle-off mr-1"></i>Toggle
                    </button>
                    <button onclick="deleteUser(4)" class="bg-red-500 hover:bg-red-600 text-white py-2 px-3 rounded-xl text-sm transition-colors duration-200">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-8">
            <p class="text-sm text-gray-400">Total 4 user</p>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="glassmorphism rounded-3xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0">
        <div class="p-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-white">Tambah User Baru</h2>
                <button onclick="closeAddUserModal()" class="text-gray-400 hover:text-white transition-colors duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="addUserForm" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" required class="w-full px-4 py-3 bg-white bg-opacity-10 border border-white border-opacity-20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-200" placeholder="Masukkan nama lengkap">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                    <input type="email" name="email" required class="w-full px-4 py-3 bg-white bg-opacity-10 border border-white border-opacity-20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-200" placeholder="Masukkan email">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Password</label>
                    <input type="password" name="password" required class="w-full px-4 py-3 bg-white bg-opacity-10 border border-white border-opacity-20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-200" placeholder="Masukkan password">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Role</label>
                    <select name="role" required class="w-full px-4 py-3 bg-white bg-opacity-10 border border-white border-opacity-20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-200">
                        <option value="">Pilih Role</option>
                        <option value="Admin">Admin</option>
                        <option value="Guru">Guru</option>
                        <option value="Siswa">Siswa</option>
                        <option value="Wali Kelas">Wali Kelas</option>
                    </select>
                </div>
                
                <div class="flex gap-4 mt-8">
                    <button type="button" onclick="closeAddUserModal()" class="flex-1 px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-xl transition-colors duration-200">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white rounded-xl transition-all duration-200">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    updateStats();
    
    // Initialize search
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('input', filterUsers);
    
    // Initialize role filter
    const roleFilter = document.getElementById('roleFilter');
    roleFilter.addEventListener('change', filterUsers);
    
    // Initialize status filter
    const statusFilter = document.getElementById('statusFilter');
    statusFilter.addEventListener('change', filterUsers);
    
    // Add loading animation to user cards
    const userCards = document.querySelectorAll('.user-card');
    userCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });
    
    // Initialize form submission
    const addUserForm = document.getElementById('addUserForm');
    if (addUserForm) {
        addUserForm.addEventListener('submit', function(e) {
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
    }
});

// User actions
function editUser(userId) {
    showNotification(`Membuka form edit untuk user ID: ${userId}`, 'info');
    
    // Get user data (in real implementation, fetch from server)
    const userCard = document.querySelector(`button[onclick*="editUser(${userId})"]`).closest('.user-card');
    const userName = userCard.querySelector('h3').textContent.trim();
    const userEmail = userCard.querySelector('.text-gray-400').textContent.trim();
    const userRole = userCard.querySelector('.text-xs.font-medium').textContent.trim();
    
    // Create edit modal
    createEditUserModal(userId, userName, userEmail, userRole);
}

function createEditUserModal(userId, name, email, role) {
    // Remove existing modal if any
    const existingModal = document.getElementById('editUserModal');
    if (existingModal) existingModal.remove();
    
    const modalHTML = `
        <div id="editUserModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="glassmorphism rounded-3xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0">
                <div class="p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-white">Edit User</h2>
                        <button onclick="closeEditUserModal()" class="text-gray-400 hover:text-white transition-colors duration-200">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <form id="editUserForm" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Nama Lengkap</label>
                            <input type="text" id="editUserName" value="${name}" class="w-full px-4 py-3 bg-white bg-opacity-10 border border-white border-opacity-20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-200">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                            <input type="email" id="editUserEmail" value="${email}" class="w-full px-4 py-3 bg-white bg-opacity-10 border border-white border-opacity-20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-200">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Role</label>
                            <select id="editUserRole" class="w-full px-4 py-3 bg-white bg-opacity-10 border border-white border-opacity-20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-200">
                                <option value="Admin" ${role === 'Admin' ? 'selected' : ''}>Admin</option>
                                <option value="Guru" ${role === 'Guru' ? 'selected' : ''}>Guru</option>
                                <option value="Siswa" ${role === 'Siswa' ? 'selected' : ''}>Siswa</option>
                                <option value="Wali Kelas" ${role === 'Wali Kelas' ? 'selected' : ''}>Wali Kelas</option>
                            </select>
                        </div>
                        
                        <div class="flex gap-4 mt-8">
                            <button type="button" onclick="closeEditUserModal()" class="flex-1 px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-xl transition-colors duration-200">
                                Batal
                            </button>
                            <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white rounded-xl transition-all duration-200">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Animate in
    setTimeout(() => {
        const modal = document.getElementById('editUserModal');
        modal.querySelector('.glassmorphism').classList.remove('scale-95', 'opacity-0');
        modal.querySelector('.glassmorphism').classList.add('scale-100', 'opacity-100');
    }, 50);
    
    // Form submission
    document.getElementById('editUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        updateUser(userId);
    });
}

function closeEditUserModal() {
    const modal = document.getElementById('editUserModal');
    if (modal) {
        modal.querySelector('.glassmorphism').classList.add('scale-95', 'opacity-0');
        setTimeout(() => modal.remove(), 300);
    }
}

function updateUser(userId) {
    const name = document.getElementById('editUserName').value;
    const email = document.getElementById('editUserEmail').value;
    const role = document.getElementById('editUserRole').value;
    
    const submitBtn = document.querySelector('#editUserForm button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
    submitBtn.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        // Update user card
        const userCard = document.querySelector(`button[onclick*="editUser(${userId})"]`).closest('.user-card');
        userCard.querySelector('h3').textContent = name;
        userCard.querySelector('.text-gray-400').textContent = email;
        
        const roleElement = userCard.querySelector('.text-xs.font-medium');
        roleElement.textContent = role;
        
        // Update role color
        roleElement.className = 'text-xs font-medium px-3 py-1 rounded-full';
        const roleColors = {
            'Admin': 'bg-purple-100 text-purple-800',
            'Guru': 'bg-blue-100 text-blue-800',
            'Siswa': 'bg-green-100 text-green-800',
            'Wali Kelas': 'bg-orange-100 text-orange-800'
        };
        roleElement.className += ' ' + (roleColors[role] || 'bg-gray-100 text-gray-800');
        
        showNotification('User berhasil diperbarui', 'success');
        closeEditUserModal();
        
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 1500);
}

function toggleUserStatus(userId, currentStatus) {
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
    const statusText = newStatus === 'active' ? 'mengaktifkan' : 'menonaktifkan';
    
    if (confirm(`Apakah Anda yakin ingin ${statusText} user ini?`)) {
        showNotification(`User berhasil ${newStatus === 'active' ? 'diaktifkan' : 'dinonaktifkan'}`, 'success');
        
        // Update UI
        const userCard = document.querySelector(`button[onclick*="toggleUserStatus(${userId}"]`).closest('.user-card');
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

function deleteUser(userId) {
    if (confirm('Apakah Anda yakin ingin menghapus user ini? Tindakan ini tidak dapat dibatalkan.')) {
        showNotification('User berhasil dihapus', 'success');
        
        // Remove user card with animation
        const userCard = document.querySelector(`button[onclick*="deleteUser(${userId})"]`).closest('.user-card');
        userCard.style.opacity = '0';
        userCard.style.transform = 'scale(0.95)';
        setTimeout(() => {
            userCard.remove();
            updateStats();
            filterUsers();
        }, 300);
    }
}

// Modal functions
function openAddUserModal() {
    const modal = document.getElementById('addUserModal');
    if (modal) {
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.querySelector('.glassmorphism').classList.remove('scale-95', 'opacity-0');
            modal.querySelector('.glassmorphism').classList.add('scale-100', 'opacity-100');
        }, 50);
    }
}

function closeAddUserModal() {
    const modal = document.getElementById('addUserModal');
    if (modal) {
        modal.querySelector('.glassmorphism').classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            document.getElementById('addUserForm').reset();
        }, 300);
    }
}

// Filter and search functions
function filterUsers() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const roleFilter = document.getElementById('roleFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    
    const userCards = document.querySelectorAll('.user-card');
    let visibleCount = 0;
    
    userCards.forEach(card => {
        const name = card.querySelector('h3').textContent.toLowerCase();
        const email = card.querySelector('.text-gray-400').textContent.toLowerCase();
        const role = card.querySelector('.text-xs.font-medium').textContent;
        const status = card.dataset.status || 'active';
        
        const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
        const matchesRole = roleFilter === 'all' || role === roleFilter;
        const matchesStatus = statusFilter === 'all' || status === statusFilter;
        
        if (matchesSearch && matchesRole && matchesStatus) {
            card.classList.remove('hidden');
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.classList.add('hidden');
            card.style.display = 'none';
        }
    });
    
    // Update results counter
    const resultsText = document.querySelector('.text-center.mt-8 p');
    if (resultsText && (searchTerm || roleFilter !== 'all' || statusFilter !== 'all')) {
        resultsText.textContent = `Menampilkan ${visibleCount} dari ${userCards.length} user`;
    } else if (resultsText) {
        resultsText.textContent = `Total ${userCards.length} user`;
    }
}

function updateStats() {
    const userCards = document.querySelectorAll('.user-card');
    const totalUsers = userCards.length;
    const activeUsers = document.querySelectorAll('.user-card[data-status="active"]').length || 
                       document.querySelectorAll('.user-card').length; // Default to all if no status set
    const inactiveUsers = totalUsers - activeUsers;
    
    // Update stats cards
    const statsCards = document.querySelectorAll('.stat-card');
    if (statsCards.length >= 3) {
        statsCards[0].querySelector('.stat-number').textContent = totalUsers;
        statsCards[1].querySelector('.stat-number').textContent = activeUsers;
        statsCards[2].querySelector('.stat-number').textContent = inactiveUsers;
    }
}

// Utility functions
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-2xl shadow-lg z-50 transform transition-all duration-300 translate-x-full max-w-sm`;
    
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
            <i class="${icons[type]} mr-3"></i>
            <span class="text-sm font-medium">${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200 transition-colors duration-200">
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
        closeEditUserModal();
    }
    
    // Ctrl/Cmd + N for new user
    if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
        e.preventDefault();
        openAddUserModal();
    }
});
</script>

<?= $this->endSection() ?>

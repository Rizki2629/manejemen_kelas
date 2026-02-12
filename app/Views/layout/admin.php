<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes, maximum-scale=5.0">
    <title><?= $this->renderSection('title') ?></title>
    
    <!-- Preload critical CSS -->
    <link rel="preload" href="<?= base_url('css/tailwind.min.css') ?>" as="style">
    
    <!-- DNS Prefetch for external resources -->
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    
    <!-- Tailwind CSS - Compiled Production Build -->
    <link rel="stylesheet" href="<?= base_url('css/tailwind.min.css') ?>">
    
    <!-- Font Awesome - with display swap for better performance -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>
    
    <!-- Custom CSS untuk Mobile-First Design -->
    <style>
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Mobile-First Smooth Transitions */
        * {
            transition: all 0.2s ease;
        }
        
        /* Touch-friendly hover states */
        @media (hover: hover) {
            .hover-effect:hover {
                transform: translateY(-1px);
            }
        }
        
        /* Mobile tap highlight */
        * {
            -webkit-tap-highlight-color: rgba(59, 130, 246, 0.1);
        }
        
        /* Better mobile text rendering */
        body {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        /* Mobile Menu Overlay Styles */
        .mobile-menu-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .mobile-menu-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        /* Mobile Navigation Overlay Behavior */
        #mobileMenu {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 280px !important;
            height: 100vh !important;
            z-index: 1000 !important;
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            backdrop-filter: blur(10px);
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.15);
            overflow-y: auto;
        }

        #mobileMenu.show {
            transform: translateX(0);
        }

        /* Global mobile content area adjustments */
        @media (max-width: 768px) {
            main {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-sizing: border-box;
            }
            
            main > * {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                box-sizing: border-box;
            }
            
            /* Override any container constraints in main content */
            main .container,
            main .max-w-7xl,
            main .mx-auto {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0.5rem !important;
                box-sizing: border-box;
            }
            
            /* Ensure content-area takes full width */
            .content-area {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0.5rem !important;
                box-sizing: border-box;
                overflow-x: hidden;
            }
            
            .content-wrapper {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-sizing: border-box;
            }
            
            /* Ensure all content inside content-area takes full width */
            .content-area > * {
                width: 100% !important;
                max-width: 100% !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                box-sizing: border-box;
            }
            
                         /* Override any container constraints */
             .content-area .container,
             .content-area .max-w-7xl,
             .content-area .mx-auto {
                 width: 100% !important;
                 max-width: 100% !important;
                 margin: 0 !important;
                 padding: 0.5rem !important;
                 box-sizing: border-box;
             }
             
             /* Force all content elements to take full width */
             .content-area *,
             main *,
             .container *,
             .max-w-7xl *,
             .mx-auto * {
                 width: 100% !important;
                 max-width: 100% !important;
                 margin-left: 0 !important;
                 margin-right: 0 !important;
                 box-sizing: border-box;
             }
             
             /* Specific overrides for common layout elements */
             .content-area .px-4,
             .content-area .px-6,
             .content-area .px-8,
             main .px-4,
             main .px-6,
             main .px-8 {
                 padding-left: 0.5rem !important;
                 padding-right: 0.5rem !important;
             }
             
             /* Ensure no horizontal overflow */
             .content-area,
             main,
             body {
                 overflow-x: hidden !important;
             }
             
             /* Additional overrides for any remaining width constraints */
             .content-area .w-auto,
             .content-area .w-fit,
             .content-area .w-max,
             main .w-auto,
             main .w-fit,
             main .w-max {
                 width: 100% !important;
             }
             
             /* Override any flex constraints */
             .content-area .flex,
             .content-area .inline-flex,
             main .flex,
             main .inline-flex {
                 width: 100% !important;
                 max-width: 100% !important;
             }
             
             /* Ensure grid layouts take full width */
             .content-area .grid,
             main .grid {
                 width: 100% !important;
                 max-width: 100% !important;
             }
             
             /* Override any remaining margin constraints */
             .content-area .ml-auto,
             .content-area .mr-auto,
             .content-area .mx-auto,
             main .ml-auto,
             main .mr-auto,
             main .mx-auto {
                 margin-left: 0 !important;
                 margin-right: 0 !important;
             }
         }
    </style>
    
    <?= $this->renderSection('styles') ?>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation Bar - Mobile Optimized -->
    <nav class="bg-white shadow-lg border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
            <div class="flex justify-between items-center h-14 sm:h-16">
                <!-- Logo Section - Mobile Optimized -->
                <div class="flex items-center min-w-0 flex-1">
                    <div class="flex-shrink-0 flex items-center">
                        <i class="fas fa-school text-xl sm:text-2xl text-blue-600 mr-2 sm:mr-3"></i>
                        <div class="min-w-0">
                            <span class="text-base sm:text-xl font-bold text-gray-800 truncate">SDN Grogol Utara 09</span>
                            <span class="hidden sm:inline text-xs sm:text-sm text-gray-500 ml-2">Aplikasi Manajemen kelas</span>
                        </div>
                    </div>
                    
                    <!-- Desktop Navigation Links -->
                    <div class="hidden lg:ml-8 lg:flex lg:space-x-6">
                        <a href="<?= base_url('admin/dashboard') ?>" 
                           class="<?= (current_url() == base_url('admin/dashboard') || current_url() == base_url('admin')) ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-2 pt-1 border-b-2 text-sm font-medium hover-effect">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            Dashboard
                        </a>
                        
                        <a href="<?= base_url('admin/data-siswa') ?>" 
                           class="<?= strpos(current_url(), 'data-siswa') !== false ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-2 pt-1 border-b-2 text-sm font-medium hover-effect">
                            <i class="fas fa-user-graduate mr-2"></i>
                            Data Siswa
                        </a>
                        
                        <a href="<?= base_url('admin/users') ?>" 
                           class="<?= strpos(current_url(), 'users') !== false ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-2 pt-1 border-b-2 text-sm font-medium hover-effect">
                            <i class="fas fa-users mr-2"></i>
                            Kelola User
                        </a>
                        
                        <a href="<?= base_url('admin/profil-sekolah') ?>" 
                           class="<?= strpos(current_url(), 'profil-sekolah') !== false ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-2 pt-1 border-b-2 text-sm font-medium hover-effect">
                            <i class="fas fa-school mr-2"></i>
                            Profil Sekolah
                        </a>
                    </div>
                </div>
                
                <!-- Right Side Menu -->
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <!-- Notifications - Mobile Optimized -->
                    <button class="p-2 sm:p-2.5 rounded-full text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 touch-manipulation">
                        <i class="fas fa-bell text-base sm:text-lg"></i>
                    </button>
                    
                    <!-- User Dropdown - Mobile Optimized -->
                    <div class="relative">
                        <button class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 p-1 hover:bg-gray-50 touch-manipulation" onclick="toggleUserMenu()">
                            <img class="h-8 w-8 sm:h-9 sm:w-9 rounded-full" src="https://ui-avatars.com/api/?name=<?= session('nama') ?? 'Admin' ?>&background=3b82f6&color=fff" alt="Avatar">
                            <span class="hidden sm:block ml-2 text-gray-700 font-medium max-w-24 truncate"><?= session('nama') ?? 'Admin' ?></span>
                            <i class="hidden sm:block fas fa-chevron-down ml-1 text-gray-400 text-xs"></i>
                        </button>
                        
                        <!-- Dropdown Menu - Mobile Optimized -->
                        <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 sm:w-52 bg-white rounded-lg shadow-xl ring-1 ring-black ring-opacity-5 z-50">
                            <div class="py-2">
                                <a href="<?= base_url('admin/profile') ?>" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 touch-manipulation">
                                    <i class="fas fa-user mr-3 text-gray-400"></i>
                                    Profile
                                </a>
                                <a href="<?= base_url('admin/settings') ?>" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 touch-manipulation">
                                    <i class="fas fa-cog mr-3 text-gray-400"></i>
                                    Settings
                                </a>
                                <hr class="my-1">
                                <a href="<?= base_url('logout') ?>" class="flex items-center px-4 py-3 text-sm text-red-700 hover:bg-red-50 touch-manipulation">
                                    <i class="fas fa-sign-out-alt mr-3 text-red-500"></i>
                                    Logout
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mobile menu button - Enhanced -->
                    <div class="lg:hidden flex items-center">
                        <button onclick="toggleMobileMenu()" class="p-2 sm:p-2.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:text-gray-700 rounded-lg touch-manipulation">
                            <i id="mobileMenuIcon" class="fas fa-bars text-lg sm:text-xl"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mobile Menu Overlay -->
        <div id="mobileMenuOverlay" class="mobile-menu-overlay lg:hidden"></div>
        
        <!-- Enhanced Mobile Navigation - Overlay Style -->
        <div id="mobileMenu" class="lg:hidden">
            <div class="px-6 py-6 space-y-2">
                <!-- Logo Section in Mobile Menu -->
                <div class="flex items-center mb-6">
                    <i class="fas fa-school text-2xl text-white mr-3"></i>
                    <div>
                        <div class="text-white font-bold text-lg">SDN Grogol Utara 09</div>
                        <div class="text-white text-sm opacity-90">Aplikasi Manajemen kelas</div>
                    </div>
                </div>
                
                <!-- Navigation Links -->
                <a href="<?= base_url('admin/dashboard') ?>" class="flex items-center px-4 py-4 text-base font-medium text-white hover:bg-white hover:bg-opacity-10 rounded-lg touch-manipulation transition-all duration-200">
                    <i class="fas fa-tachometer-alt mr-4 text-white opacity-90 w-5"></i>
                    Dashboard
                </a>
                <a href="<?= base_url('admin/data-siswa') ?>" class="flex items-center px-4 py-4 text-base font-medium text-white hover:bg-white hover:bg-opacity-10 rounded-lg touch-manipulation transition-all duration-200">
                    <i class="fas fa-user-graduate mr-4 text-white opacity-90 w-5"></i>
                    Data Siswa
                </a>
                <a href="<?= base_url('admin/users') ?>" class="flex items-center px-4 py-4 text-base font-medium text-white hover:bg-white hover:bg-opacity-10 rounded-lg touch-manipulation transition-all duration-200">
                    <i class="fas fa-users mr-4 text-white opacity-90 w-5"></i>
                    Kelola User
                </a>
                <a href="<?= base_url('admin/profil-sekolah') ?>" class="flex items-center px-4 py-4 text-base font-medium text-white hover:bg-white hover:bg-opacity-10 rounded-lg touch-manipulation transition-all duration-200">
                    <i class="fas fa-school mr-4 text-white opacity-90 w-5"></i>
                    Profil Sekolah
                </a>
                
                <!-- Mobile User Info -->
                <div class="border-t border-white border-opacity-20 pt-4 mt-6">
                    <div class="flex items-center px-4 py-3">
                        <img class="h-12 w-12 rounded-full border-2 border-white border-opacity-30" src="https://ui-avatars.com/api/?name=<?= session('nama') ?? 'Admin' ?>&background=ffffff&color=667eea" alt="Avatar">
                        <div class="ml-4">
                            <div class="text-white font-medium text-base"><?= session('nama') ?? 'Admin' ?></div>
                            <div class="text-white text-sm opacity-80">Administrator</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mx-4 mt-4" id="successAlert">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span><?= session()->getFlashdata('success') ?></span>
                <button onclick="closeAlert('successAlert')" class="ml-auto">
                    <i class="fas fa-times text-green-700 hover:text-green-900"></i>
                </button>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mx-4 mt-4" id="errorAlert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span><?= session()->getFlashdata('error') ?></span>
                <button onclick="closeAlert('errorAlert')" class="ml-auto">
                    <i class="fas fa-times text-red-700 hover:text-red-900"></i>
                </button>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('info')): ?>
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mx-4 mt-4" id="infoAlert">
            <div class="flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                <span><?= session()->getFlashdata('info') ?></span>
                <button onclick="closeAlert('infoAlert')" class="ml-auto">
                    <i class="fas fa-times text-blue-700 hover:text-blue-900"></i>
                </button>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main class="flex-1">
        <?= $this->renderSection('content') ?>
    </main>
    
    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <p class="text-sm text-gray-500">
                    © <?= date('Y') ?> SDN Grogol Utara 09. Aplikasi Manajemen kelas.
                </p>
                <p class="text-sm text-gray-500">
                    Version 1.0 | Powered by CodeIgniter 4
                </p>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script>
        // Toggle User Menu
        function toggleUserMenu() {
            const menu = document.getElementById('userMenu');
            menu.classList.toggle('hidden');
        }
        
        // Toggle Mobile Menu with Overlay
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            const overlay = document.getElementById('mobileMenuOverlay');
            const icon = document.getElementById('mobileMenuIcon');
            
            if (menu.classList.contains('show')) {
                // Close menu
                menu.classList.remove('show');
                overlay.classList.remove('show');
                icon.className = 'fas fa-bars text-lg sm:text-xl';
                document.body.style.overflow = '';
            } else {
                // Open menu
                menu.classList.add('show');
                overlay.classList.add('show');
                icon.className = 'fas fa-times text-lg sm:text-xl';
                document.body.style.overflow = 'hidden';
            }
        }
        
        // Close mobile menu when clicking overlay
        document.addEventListener('DOMContentLoaded', function() {
            const overlay = document.getElementById('mobileMenuOverlay');
            if (overlay) {
                overlay.addEventListener('click', function() {
                    toggleMobileMenu();
                });
            }
        });
        
        // Close Alert
        function closeAlert(alertId) {
            const alert = document.getElementById(alertId);
            if (alert) {
                alert.style.opacity = '0';
                alert.style.transform = 'translateX(-20px)';
                setTimeout(() => alert.remove(), 300);
            }
        }
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.getElementById('userMenu');
            const userButton = event.target.closest('[onclick="toggleUserMenu()"]');
            
            if (!userButton && !userMenu.contains(event.target)) {
                userMenu.classList.add('hidden');
            }
        });
        
        // Auto close alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('[id$="Alert"]');
            alerts.forEach(alert => {
                if (alert) {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateX(-20px)';
                    setTimeout(() => alert.remove(), 300);
                }
            });
        }, 5000);
        
        // Loading state helper
        function setLoading(button, loading = true) {
            if (loading) {
                button.disabled = true;
                const originalText = button.innerHTML;
                button.dataset.originalText = originalText;
                button.innerHTML = '<span class="loading"></span> Loading...';
            } else {
                button.disabled = false;
                button.innerHTML = button.dataset.originalText || button.innerHTML;
            }
        }
        
        // Show notification
        function showNotification(message, type = 'info', duration = 5000) {
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
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 300);
            }, duration);
        }
    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>

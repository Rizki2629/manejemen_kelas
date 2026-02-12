<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SDN GrogoL Utara 09</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/tailwind.min.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Custom colors to replace tailwind.config */
        .text-primary { color: #0052cc !important; }
        .bg-primary { background-color: #0052cc !important; }
        .text-secondary { color: #00b894 !important; }
        .bg-secondary { background-color: #00b894 !important; }
        .border-primary { border-color: #0052cc !important; }
        .border-secondary { border-color: #00b894 !important; }
        .hover\:bg-primary:hover { background-color: #0052cc !important; }
        .hover\:text-primary:hover { color: #0052cc !important; }
    </style>
    <style>
        .hero-gradient {
            background: linear-gradient(90deg, #0052cc 0%, #00b894 100%);
        }
        .search-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }
        .stat-item {
            text-align: center;
            color: white;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
        }
        .stat-label {
            font-size: 0.9rem;
        }
        .icon-menu {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1rem;
            transition: all 0.3s;
        }
        .icon-menu:hover {
            transform: translateY(-5px);
        }
        .icon-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5rem;
            background-color: #f0f9ff;
        }

        /* Custom Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
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

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-fade-in-down {
            animation: fadeInDown 0.6s ease-out forwards;
        }

        .animate-fade-in-left {
            animation: fadeInLeft 0.6s ease-out forwards;
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .animate-fade-in-right {
            animation: fadeInRight 0.6s ease-out forwards;
        }

        .delay-100 {
            animation-delay: 0.1s;
        }

        .delay-200 {
            animation-delay: 0.2s;
        }

        .delay-300 {
            animation-delay: 0.3s;
        }

        .delay-400 {
            animation-delay: 0.4s;
        }

        .delay-500 {
            animation-delay: 0.5s;
        }

        .delay-600 {
            animation-delay: 0.6s;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo dan Brand -->
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/bb/Tut_Wuri_Handayani_logo.svg/1200px-Tut_Wuri_Handayani_logo.svg.png" alt="Logo" class="h-10 w-10 mr-2">
                        <h1 class="text-lg font-bold text-gray-800">SDN Grogol Utara 09</h1>
                    </div>
                </div>

                <!-- Menu Desktop -->
                <div class="hidden md:flex items-center space-x-1">
                    <a href="#" class="text-gray-700 hover:text-primary px-3 py-2 rounded-md text-sm font-medium transition duration-300">Home</a>
                    <a href="#" class="text-gray-700 hover:text-primary px-3 py-2 rounded-md text-sm font-medium transition duration-300">Profil <i class="fas fa-chevron-down text-xs ml-1"></i></a>
                    <a href="#" class="text-gray-700 hover:text-primary px-3 py-2 rounded-md text-sm font-medium transition duration-300">Direktori <i class="fas fa-chevron-down text-xs ml-1"></i></a>
                    <a href="#" class="text-gray-700 hover:text-primary px-3 py-2 rounded-md text-sm font-medium transition duration-300">Publikasi <i class="fas fa-chevron-down text-xs ml-1"></i></a>
                    <a href="#" class="text-gray-700 hover:text-primary px-3 py-2 rounded-md text-sm font-medium transition duration-300">Ekskul <i class="fas fa-chevron-down text-xs ml-1"></i></a>
                    <a href="#" class="text-gray-700 hover:text-primary px-3 py-2 rounded-md text-sm font-medium transition duration-300">PPDB <i class="fas fa-chevron-down text-xs ml-1"></i></a>
                    <a href="#" class="text-gray-700 hover:text-primary px-3 py-2 rounded-md text-sm font-medium transition duration-300">Unduhan <i class="fas fa-chevron-down text-xs ml-1"></i></a>
                    
                    <a href="#" class="text-primary hover:text-blue-700 px-3 py-2 rounded-md text-sm font-medium transition duration-300 ml-4">
                        <i class="fas fa-user mr-1"></i> Login
                    </a>
                    <a href="#" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-300 flex items-center">
                        Kontak <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-button" class="text-gray-700 hover:text-primary focus:outline-none focus:text-primary">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="md:hidden hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-white border-t">
                <a href="#" class="text-gray-700 hover:text-primary block px-3 py-2 rounded-md text-base font-medium">Home</a>
                <a href="#" class="text-gray-700 hover:text-primary block px-3 py-2 rounded-md text-base font-medium">Profil</a>
                <a href="#" class="text-gray-700 hover:text-primary block px-3 py-2 rounded-md text-base font-medium">Direktori</a>
                <a href="#" class="text-gray-700 hover:text-primary block px-3 py-2 rounded-md text-base font-medium">Publikasi</a>
                <a href="#" class="text-gray-700 hover:text-primary block px-3 py-2 rounded-md text-base font-medium">Ekskul</a>
                <a href="#" class="text-gray-700 hover:text-primary block px-3 py-2 rounded-md text-base font-medium">PPDB</a>
                <a href="#" class="text-gray-700 hover:text-primary block px-3 py-2 rounded-md text-base font-medium">Unduhan</a>
                <a href="#" class="text-primary hover:text-blue-700 block px-3 py-2 rounded-md text-base font-medium">Login</a>
                <a href="#" class="bg-primary text-white block px-3 py-2 rounded-md text-base font-medium mt-2">Kontak</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-gradient text-white relative overflow-hidden">
        <div class="absolute inset-0 z-0 opacity-20" style="background-image: url(\'https://www.transparenttextures.com/patterns/cubes.png\');"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 relative z-10">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="bg-white text-primary inline-block px-5 py-2 rounded-full mb-4 shadow-md animate-fade-in-down">
                        <i class="fas fa-star mr-2"></i> Selamat Datang di Website
                    </div>
                    <h1 class="text-4xl md:text-6xl font-extrabold leading-tight mb-4 animate-fade-in-left">
                        SDN GrogoL Utara 09
                    </h1>
                    <p class="text-lg md:text-xl mb-8 opacity-90 animate-fade-in-left delay-200">
                        Jl. Kemandoran I Rt. 004 Rw.005 Kel. Grogol Utara Kec. Kebayoran Lama Jakarta Selatan
                    </p>
                    
                    <!-- Search Bar -->
                    <div class="mb-10 animate-fade-in-up delay-400">
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center bg-white rounded-lg shadow-xl overflow-hidden">
                            <div class="relative flex-grow">
                                <input type="text" placeholder="Cari berita, informasi siswa, atau lainnya..." class="w-full px-5 py-3 text-gray-800 focus:outline-none focus:ring-2 focus:ring-primary rounded-l-lg sm:rounded-none sm:rounded-l-lg">
                                <div class="absolute right-0 top-0 flex items-center h-full border-l border-gray-200">
                                    <select class="h-full bg-gray-100 text-gray-700 rounded-none px-4 focus:outline-none focus:ring-2 focus:ring-primary">
                                        <option>Berita</option>
                                        <option>Siswa</option>
                                        <option>Guru</option>
                                    </select>
                                </div>
                            </div>
                            <button class="bg-primary text-white px-6 py-3 rounded-b-lg sm:rounded-r-lg sm:rounded-b-none hover:bg-blue-700 transition duration-300 flex items-center justify-center">
                                <i class="fas fa-search mr-2"></i> Pencarian
                            </button>
                        </div>
                    </div>
                    
                    <!-- Statistics -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 animate-fade-in-up delay-600">
                        <div class="stat-item bg-white bg-opacity-20 p-4 rounded-lg shadow-lg backdrop-filter backdrop-blur-sm">
                            <i class="fas fa-users text-3xl mb-2"></i>
                            <div class="stat-number">6</div>
                            <div class="stat-label">Jumlah Guru</div>
                        </div>
                        <div class="stat-item bg-white bg-opacity-20 p-4 rounded-lg shadow-lg backdrop-filter backdrop-blur-sm">
                            <i class="fas fa-user-graduate text-3xl mb-2"></i>
                            <div class="stat-number">6</div>
                            <div class="stat-label">Jumlah Siswa</div>
                        </div>
                        <div class="stat-item bg-white bg-opacity-20 p-4 rounded-lg shadow-lg backdrop-filter backdrop-blur-sm">
                            <i class="fas fa-handshake text-3xl mb-2"></i>
                            <div class="stat-number">6</div>
                            <div class="stat-label">Komite Sekolah</div>
                        </div>
                        <div class="stat-item bg-white bg-opacity-20 p-4 rounded-lg shadow-lg backdrop-filter backdrop-blur-sm">
                            <i class="fas fa-briefcase text-3xl mb-2"></i>
                            <div class="stat-number">4</div>
                            <div class="stat-label">Total Pegawai</div>
                        </div>
                    </div>
                </div>
                
                <!-- Illustration -->
                <div class="relative flex justify-center items-center animate-fade-in-right">
                    <img src="https://img.freepik.com/free-vector/teacher-students-wearing-face-masks_52683-38400.jpg" alt="School Illustration" class="rounded-full bg-white p-3 w-full max-w-md mx-auto shadow-2xl transform hover:scale-105 transition-transform duration-500">
                    
                    <!-- Go Digital Badge -->
                    <div class="absolute bottom-8 right-8 md:bottom-12 md:right-12 bg-white rounded-xl shadow-xl p-4 flex items-center transform hover:scale-105 transition-transform duration-300">
                        <div class="bg-primary rounded-full p-3 mr-3 shadow-md">
                            <i class="fas fa-graduation-cap text-white text-xl"></i>
                        </div>
                        <div>
                            <div class="font-bold text-gray-800 text-lg">Go Digital</div>
                            <div class="text-sm text-gray-600">Sekolahku Cerdas</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Icon Navigation -->
    <section class="py-16 bg-gradient-to-r from-blue-50 to-indigo-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-extrabold text-gray-900 mb-12 text-center animate-fade-in-up">Jelajahi Fitur Kami</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                <a href="#" class="icon-menu bg-white rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-2 transition-all duration-300">
                    <div class="icon-circle bg-blue-100 text-blue-600">
                        <i class="fas fa-building text-3xl"></i>
                    </div>
                    <span class="font-semibold text-gray-800 text-lg mt-2">Profil</span>
                </a>
                <a href="#" class="icon-menu bg-white rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-2 transition-all duration-300">
                    <div class="icon-circle bg-green-100 text-green-600">
                        <i class="fas fa-sitemap text-3xl"></i>
                    </div>
                    <span class="font-semibold text-gray-800 text-lg mt-2">Struktur</span>
                </a>
                <a href="#" class="icon-menu bg-white rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-2 transition-all duration-300">
                    <div class="icon-circle bg-yellow-100 text-yellow-600">
                        <i class="fas fa-users text-3xl"></i>
                    </div>
                    <span class="font-semibold text-gray-800 text-lg mt-2">Komite</span>
                </a>
                <a href="#" class="icon-menu bg-white rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-2 transition-all duration-300">
                    <div class="icon-circle bg-red-100 text-red-600">
                        <i class="fas fa-chalkboard-teacher text-3xl"></i>
                    </div>
                    <span class="font-semibold text-gray-800 text-lg mt-2">Guru</span>
                </a>
                <a href="#" class="icon-menu bg-white rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-2 transition-all duration-300">
                    <div class="icon-circle bg-purple-100 text-purple-600">
                        <i class="fas fa-user-graduate text-3xl"></i>
                    </div>
                    <span class="font-semibold text-gray-800 text-lg mt-2">Siswa</span>
                </a>
                <a href="#" class="icon-menu bg-white rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-2 transition-all duration-300">
                    <div class="icon-circle bg-teal-100 text-teal-600">
                        <i class="fas fa-newspaper text-3xl"></i>
                    </div>
                    <span class="font-semibold text-gray-800 text-lg mt-2">Publikasi</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Berita Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-extrabold text-gray-900 mb-12 text-center animate-fade-in-up">Berita Terbaru & Pengumuman</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php if (!empty($berita) && is_array($berita)): ?>
                    <?php foreach ($berita as $item): ?>
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-all duration-300 animate-fade-in-up">
                            <?php if (!empty($item["gambar"])): ?>
                                <img src="<?= esc($item["gambar"]) ?>" alt="<?= esc($item["judul"]) ?>" class="w-full h-56 object-cover object-center">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/600x400?text=No+Image" alt="No Image Available" class="w-full h-56 object-cover object-center">
                            <?php endif; ?>
                            <div class="p-6">
                                <p class="text-sm text-gray-500 mb-2"><i class="far fa-calendar-alt mr-1"></i> <?= date("d M Y", strtotime($item["tanggal"])) ?></p>
                                <h3 class="text-xl font-semibold text-gray-800 mb-3 leading-tight"><?= esc($item["judul"]) ?></h3>
                                <p class="text-gray-700 text-base mb-4"><?= esc(substr($item["isi"], 0, 120)) ?>...</p>
                                <a href="#" class="text-primary hover:text-blue-700 font-medium flex items-center">Baca Selengkapnya <i class="fas fa-arrow-right ml-2 text-sm"></i></a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-1 md:col-span-2 lg:col-span-3 bg-white rounded-xl shadow-lg p-8 text-center">
                        <p class="text-gray-600 text-lg"><i class="fas fa-info-circle mr-2"></i> Belum ada berita atau pengumuman terbaru tersedia saat ini. Silakan kunjungi kembali nanti!</p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="text-center mt-12">
                <a href="#" class="inline-block bg-primary text-white px-8 py-3 rounded-full font-semibold text-lg shadow-lg hover:bg-blue-700 transition duration-300 transform hover:-translate-y-1">
                    Lihat Semua Berita <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Pimpinan Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-extrabold text-gray-900 mb-12 text-center animate-fade-in-up">Pimpinan Sekolah</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Kepala Sekolah -->
                <div class="text-center bg-gray-50 p-8 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300 animate-fade-in-up">
                    <div class="mx-auto w-36 h-36 rounded-full overflow-hidden bg-blue-100 flex items-center justify-center mb-6 border-4 border-primary shadow-md">
                        <i class="fas fa-user-tie text-blue-600 text-6xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800">Kepala Sekolah</h3>
                    <p class="text-primary mt-2 text-lg">Nama Kepala Sekolah</p>
                    <p class="text-gray-600 text-sm mt-1">Pemimpin Visioner</p>
                </div>
                <!-- Wakil Bidang Kurikulum -->
                <div class="text-center bg-gray-50 p-8 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300 animate-fade-in-up delay-100">
                    <div class="mx-auto w-36 h-36 rounded-full overflow-hidden bg-green-100 flex items-center justify-center mb-6 border-4 border-secondary shadow-md">
                        <i class="fas fa-book-reader text-green-600 text-6xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800">Wakil Bidang Kurikulum</h3>
                    <p class="text-secondary mt-2 text-lg">Nama Wakil Kurikulum</p>
                    <p class="text-gray-600 text-sm mt-1">Pengembang Akademik</p>
                </div>
                <!-- Wakil Bidang Kesiswaan -->
                <div class="text-center bg-gray-50 p-8 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300 animate-fade-in-up delay-200">
                    <div class="mx-auto w-36 h-36 rounded-full overflow-hidden bg-purple-100 flex items-center justify-center mb-6 border-4 border-purple-600 shadow-md">
                        <i class="fas fa-child text-purple-600 text-6xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800">Wakil Bidang Kesiswaan</h3>
                    <p class="text-purple-600 mt-2 text-lg">Nama Wakil Kesiswaan</p>
                    <p class="text-gray-600 text-sm mt-1">Pembina Karakter Siswa</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-100 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center text-gray-600">
                <p>&copy; 2025 SDN GrogoL Utara 09. Semua hak dilindungi.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Close mobile menu when clicking on a link
        document.querySelectorAll('#mobile-menu a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
            });
        });
    </script>
</body>
</html>






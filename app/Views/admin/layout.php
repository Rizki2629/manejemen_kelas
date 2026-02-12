<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <!-- DNS Prefetch for external resources -->
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Preload critical CSS -->
    <link rel="preload" href="/css/tailwind.min.css" as="style">
    
    <!-- Google Fonts with display swap -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    
    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="/css/tailwind.min.css">
    
    <!-- Font Awesome - lazy loaded -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'" />
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" /></noscript>
    
    <!-- SweetAlert2 - defer loading -->
    <script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root {
            --sidebar-width-expanded: 280px;
            --sidebar-width-collapsed: 88px;
            --content-padding-expanded: 0;
            --content-padding-collapsed: 0;
            --scrollbar-width: 0px;
            --sidebar-gap: 0px;
        }

        /* Hilangkan reservasi ruang di sisi kiri yang menimbulkan strip putih */
        html {
            scrollbar-gutter: stable;
        }

        body.modal-open {
            overflow: hidden;
            padding-right: var(--scrollbar-width);
        }

        body {
            font-family: 'Inter', sans-serif;
            margin: 0 !important;
            padding: 0 !important;
            overflow-x: hidden;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        /* Matikan animasi & transisi untuk kurangi lag */
        *,
        *::before,
        *::after {
            transition: none !important;
            animation: none !important;
        }

        .sidebar,
        .sidebar nav a,
        .sidebar nav div,
        .fixed-header,
        .sidebar nav .submenu,
        .sidebar nav .submenu-chevron,
        .sidebar-text,
        .menu-text,
        .menu-label {
            transition: none !important;
        }

        /* Submenu functionality */
        .menu-item-with-submenu .submenu {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
        }

        /* Perbesar max-height agar item terakhir (Cetak) tidak terpotong; tampilkan overflow saat open */
        .menu-item-with-submenu.open .submenu {
            max-height: 600px;
            opacity: 1;
            overflow: visible;
        }

        .menu-item-with-submenu.open .submenu-chevron {
            transform: rotate(180deg);
        }

        /* Sidebar readability: prevent label wrapping (use ellipsis) */
        .sidebar nav .menu-text {
            min-width: 0;
            max-width: 100%;
        }

        .sidebar nav .menu-text span {
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Submenu visual nesting */
        .menu-item-with-submenu .submenu {
            border-left: 1px solid rgba(226, 232, 240, 1);
            margin-left: 0.25rem;
            padding-left: 0.75rem;
        }

        /* Enhanced menu item styles (no horizontal shift on hover) */
        .sidebar nav a:hover,
        .sidebar nav div:hover {
            transform: none !important;
            box-shadow: none !important;
        }

        /* Badge animations */
        .sidebar nav .bg-blue-400\/30,
        .sidebar nav .bg-green-400\/30,
        .sidebar nav .bg-orange-400\/30 {
            transition: all 0.3s ease !important;
        }

        .sidebar nav a:hover .bg-blue-400\/30,
        .sidebar nav a:hover .bg-green-400\/30,
        .sidebar nav a:hover .bg-orange-400\/30 {
            transform: scale(1.1);
            box-shadow: 0 2px 8px rgba(255, 255, 255, 0.2);
        }

        /* Light sidebar theme (match screenshot style) */
        .gradient-bg {
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        }

        /* Fixed Layout Styles */
        .main-container {
            display: flex;
            min-height: 100vh;
            height: 100vh;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            position: relative;
            overflow: hidden;
            margin: 0 !important;
            padding: 0 !important;
            border: 0 !important;
        }

        .sidebar {
            width: var(--sidebar-width-expanded);
            flex-shrink: 0;
            background: #ffffff;
            position: sticky;
            top: 0;
            height: 100vh;
            z-index: 99999999999999;
            /* elevated overlay */
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            border-right: 1px solid rgba(226, 232, 240, 0.95);
            margin-right: 2px;
            transition: width .15s ease !important;
            /* minimal smoothness */
            box-sizing: border-box;
        }

        /* Hover expanded appearance overlays content without pushing */
        .sidebar.expanded {
            width: var(--sidebar-width-expanded);
        }

        .sidebar.collapsed {
            width: var(--sidebar-width-collapsed);
        }

        /* Desktop hover-expand when collapsed (overlay; content stays put) */
        @media (min-width: 1024px) {
            .sidebar.collapsed:hover {
                width: var(--sidebar-width-expanded);
            }
        }

        /* When collapsed remove large shadow to eliminate perceived gap line; add subtle separator */
        .sidebar.collapsed {
            box-shadow: none;
            border-right: none;
        }

        /* Reduce left padding of content when sidebar collapsed to close visual gap */
        body.sidebar-collapsed .content-area {
            padding-left: 0.5rem !important;
        }

        body.sidebar-collapsed .content-area>*:first-child {
            margin-left: 0 !important;
        }

        .sidebar.collapsed .sidebar-text,
        .sidebar.collapsed .menu-text,
        .sidebar.collapsed .menu-label {
            opacity: 0;
            pointer-events: none;
            width: 0;
            overflow: hidden;
            position: absolute;
            visibility: hidden;
        }

        /* Hide chevron + active indicator when collapsed */
        .sidebar.collapsed .submenu-chevron {
            display: none !important;
        }

        .sidebar.collapsed .w-1.bg-white.rounded-full {
            display: none !important;
        }

        .sidebar .p-6 {
            background: #ffffff;
        }

        .sidebar.collapsed nav ul li a {
            justify-content: center;
            padding: 0.6rem;
            min-height: 48px;
            display: flex;
            align-items: center;
            gap: 0 !important;
        }

        .sidebar.collapsed nav ul li a svg {
            margin: 0;
            opacity: 1;
        }

        /* Uniform icon container size expanded & collapsed */
        .sidebar nav a .flex-shrink-0 {
            width: 46px;
            height: 46px;
        }

        /* Sidebar always expanded on desktop; never block content interaction */
        .sidebar.collapsed nav a .flex-shrink-0 {
            width: 46px;
            height: 46px;
        }

        /* Prevent lateral shift when collapsed */
        .sidebar.collapsed nav a:hover,
        .sidebar.collapsed nav div:hover {
            transform: none !important;
        }

        /* Hide text gap cleanly */
        .sidebar.collapsed nav a .menu-text {
            display: none !important;
        }

        /* Hide active vertical bar when collapsed */
        .sidebar.collapsed nav a>.w-1.h-8 {
            display: none !important;
        }

        /* Remove residual spacing from space-x-* utilities when collapsed */
        .sidebar.collapsed nav a.space-x-3> :not(:first-child),
        .sidebar.collapsed nav div.space-x-3> :not(:first-child) {
            margin-left: 0 !important;
        }

        /* Ensure submenu toggle divs also centered */
        .sidebar.collapsed nav .submenu-toggle {
            justify-content: center;
            padding: 0.6rem;
        }

        /* Square logo box */
        .logo-box {
            width: 56px;
            height: 56px;
            border-radius: 14px;
        }

        @media (max-width:1023px) {
            .logo-box {
                width: 54px;
                height: 54px;
            }
        }

        /* Sidebar toggle button removed */

        /* ===== Light theme overrides for existing sidebar markup ===== */
        .sidebar .sidebar-text h1 {
            color: #6d28d9 !important;
        }

        .sidebar .sidebar-text p {
            color: #475569 !important;
        }

        .sidebar .sidebar-text .text-white\/60,
        .sidebar .sidebar-text .text-white\/80 {
            color: #94a3b8 !important;
        }

        /* Logo area removed */

        /* Brand typography (single-line title + badge) */
        .sidebar .sidebar-text h1 {
            font-size: 1rem !important;
            line-height: 1.1 !important;
            letter-spacing: -0.01em !important;
        }

        .sidebar .sidebar-text p {
            margin-top: 0.15rem !important;
            font-size: 0.82rem !important;
            line-height: 1.2 !important;
        }

        .sidebar .school-name {
            display: flex;
            flex-wrap: nowrap;
            gap: 0.35rem;
            align-items: baseline;
            white-space: nowrap;
            min-width: 0;
            overflow: hidden;
        }

        .sidebar .sidebar-text {
            min-width: 0;
        }

        /* Keep the school name on one line; truncate only the name if needed */
        .sidebar .school-name>span:first-child {
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            flex: 1 1 auto;
        }

        .sidebar .school-code {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.08rem 0.45rem;
            border-radius: 9999px;
            background: #ede9fe;
            color: #6d28d9;
            font-weight: 700;
            font-size: 0.72rem;
            border: 1px solid rgba(167, 139, 250, 0.35);
            white-space: nowrap;
            flex: 0 0 auto;
        }

        .sidebar .menu-label {
            color: #94a3b8 !important;
        }

        .sidebar .w-8.h-px.bg-white\/20 {
            background: rgba(148, 163, 184, 0.35) !important;
        }

        /* Base items */
        .sidebar nav a,
        .sidebar nav .submenu-toggle {
            color: #334155 !important;
            border-color: transparent !important;
            box-shadow: none !important;
        }

        .sidebar nav a:hover,
        .sidebar nav .submenu-toggle:hover {
            background: #f1f5f9 !important;
            color: #0f172a !important;
        }

        /* Active states (existing markup uses bg-white/20 and bg-white/15) */
        .sidebar nav a.bg-white\/20,
        .sidebar nav div.bg-white\/20,
        .sidebar nav a.bg-white\/15 {
            background: #ede9fe !important;
            color: #6d28d9 !important;
            border-color: transparent !important;
            box-shadow: none !important;
        }

        /* Icons: remove boxed backgrounds */
        .sidebar nav a .bg-white\/20,
        .sidebar nav div .bg-white\/20,
        .sidebar nav .submenu a .bg-white\/15,
        .sidebar nav .submenu a .w-6.h-6 {
            background: transparent !important;
            border-color: transparent !important;
            box-shadow: none !important;
        }

        /* Submenu items */
        .sidebar nav .submenu a {
            color: #64748b !important;
        }

        .sidebar nav .submenu a:hover {
            background: #f1f5f9 !important;
            color: #0f172a !important;
        }

        .sidebar nav .submenu a.bg-white\/15 {
            background: #ede9fe !important;
            color: #6d28d9 !important;
        }

        .sidebar .submenu-chevron {
            color: #64748b !important;
        }

        /* Replace old white borders used in markup */
        .sidebar .border-white\/30,
        .sidebar .border-white\/20,
        .sidebar .border-white\/10 {
            border-color: rgba(226, 232, 240, 1) !important;
        }

        /* Active indicator (vertical bar) */
        .sidebar .w-1.h-8.bg-white.rounded-full {
            background: #6d28d9 !important;
        }

        /* Logout keeps red semantics */
        .sidebar nav a.text-red-200 {
            color: #dc2626 !important;
        }

        .sidebar nav a.text-red-200:hover {
            background: #fee2e2 !important;
            color: #b91c1c !important;
        }

        .sidebar nav a.text-red-200 .bg-red-500\/20,
        .sidebar nav a.text-red-200 .bg-red-500\/30 {
            background: transparent !important;
        }

        /* content wrapper removed: header + content are direct children */

        .fixed-header {
            position: sticky;
            top: 0;
            z-index: 50;
            width: 100%;
            height: 60px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
        }

        /* .fixed-header.pinned-expanded intentionally removed (no-op) */

        .content-area {
            flex: 1;
            overflow: auto;
            padding: 1.1rem var(--sidebar-gap) 2rem;
            margin-top: 0;
            min-height: 0;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
            margin-left: 0 !important;
            width: 100%;
            box-sizing: border-box;
            position: relative;
            z-index: 1;
            /* bawah sidebar */
        }

        /* Content container: fill width and stay aligned to sidebar */
        .page-shell {
            width: 100%;
            max-width: none;
            margin: 0;
            padding: 0;
        }

        /* Many pages wrap content in top-level `px-*` blocks; remove that gutter so content sits close to sidebar */
        .page-shell>.px-3,
        .page-shell>.px-4,
        .page-shell>.px-6,
        .page-shell>.px-8,
        .page-shell>.px-10 {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .page-shell>.sm\:px-4,
        .page-shell>.sm\:px-6,
        .page-shell>.sm\:px-8,
        .page-shell>.lg\:px-6,
        .page-shell>.lg\:px-8 {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .page-shell>.mx-4,
        .page-shell>.mx-6,
        .page-shell>.mx-8 {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        .page-shell>.sm\:mx-4,
        .page-shell>.sm\:mx-6,
        .page-shell>.sm\:mx-8,
        .page-shell>.lg\:mx-6,
        .page-shell>.lg\:mx-8 {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        @media (min-width: 1024px) {
            .page-shell {
                max-width: none;
                margin: 0;
                padding-right: 0;
            }

            /* Many views use Tailwind `mx-auto` to center; left-align them inside the shell */
            .page-shell .mx-auto {
                margin-left: 0 !important;
                margin-right: auto !important;
            }

            /* Tailwind `.container` centers by default; force left alignment */
            .page-shell .container {
                margin-left: 0 !important;
                margin-right: auto !important;
            }

            /* Remove common Tailwind width clamps so content can fill the available space */
            .page-shell .container,
            .page-shell .max-w-7xl,
            .page-shell .max-w-6xl,
            .page-shell .max-w-5xl,
            .page-shell .max-w-4xl,
            .page-shell .max-w-3xl {
                max-width: none !important;
                width: 100% !important;
            }

            /* If max-width wrappers still carry auto margins, reset them */
            .page-shell .max-w-7xl,
            .page-shell .max-w-6xl,
            .page-shell .max-w-5xl,
            .page-shell .max-w-4xl,
            .page-shell .max-w-3xl {
                margin-left: 0 !important;
                margin-right: auto !important;
            }
        }

        /* Remove unintended extra top margin from first direct child */
        .content-area>*:first-child {
            margin-top: 0 !important;
        }

        .content-area::-webkit-scrollbar {
            width: 6px;
        }

        .content-area::-webkit-scrollbar-track {
            background: transparent;
        }

        .content-area::-webkit-scrollbar-thumb {
            background: linear-gradient(140deg, #4338CA 0%, #6D28D9 60%, #A21CAF 100%);
            border-radius: 3px;
        }

        .content-area::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(140deg, #4f46e5 0%, #7c3aed 55%, #c026d3 100%);
        }

        /* Ensure sidebar content doesn't overflow */
        .sidebar nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 0;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
            box-sizing: border-box;
        }

        .sidebar nav::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 2px;
        }

        .sidebar nav::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        @media (max-width: 1023px) {
            .main-container {
                padding-left: 0 !important;
            }

            .sidebar {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                width: 320px !important;
                height: 100vh !important;
                z-index: 1000 !important;
                transform: translateX(-100%);
                background: #ffffff !important;
                backdrop-filter: none !important;
                box-shadow: 4px 0 20px rgba(15, 23, 42, 0.12);
                overflow-y: auto;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .sidebar .p-4 {
                padding: 1.5rem !important;
            }

            .sidebar nav {
                padding: 1rem !important;
            }

            .sidebar .menu-text {
                font-size: 1.05rem !important;
                font-weight: 500 !important;
            }

            .sidebar .menu-label {
                font-size: 0.85rem !important;
                margin-bottom: 1rem !important;
            }

            .content-area {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 1rem 1rem 1rem !important;
                box-sizing: border-box;
                overflow-x: hidden;
            }

            .fixed-header {
                width: 100% !important;
                height: 60px !important;
                background: rgba(255, 255, 255, 0.95) !important;
                backdrop-filter: blur(15px) !important;
            }

            .content-area .container,
            .content-area .max-w-7xl,
            .content-area .mx-auto {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 1rem !important;
                box-sizing: border-box;
            }
        }

        @media (max-width: 768px) {
            .content-area {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0.75rem 0.75rem 0.75rem !important;
                box-sizing: border-box;
                overflow-x: hidden;
            }

            .sidebar {
                width: 280px !important;
            }

            .fixed-header {
                padding: 0.5rem 1rem !important;
            }

            .fixed-header .text-xs {
                font-size: 1rem !important;
            }

            .fixed-header .text-sm {
                font-size: 1.1rem !important;
            }

            .fixed-header input {
                font-size: 1.1rem !important;
            }

            .fixed-header button {
                font-size: 1.1rem !important;
            }


            .content-area>* {
                width: 100% !important;
                max-width: 100% !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                box-sizing: border-box;
            }

            .content-area .container,
            .content-area .max-w-7xl,
            .content-area .mx-auto {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0.75rem !important;
                box-sizing: border-box;
            }
        }

        @media (min-width: 1024px) {
            .content-area {
                margin-left: 0;
                padding: 0.5rem var(--sidebar-gap) 0.5rem;
                max-width: none;
                overflow: auto !important;
                overflow-x: hidden !important;
            }

            .fixed-header {
                box-sizing: border-box;
            }
        }

        @media (max-width: 1023px) {
            .sidebar {
                transform: translateX(-100%);
                z-index: 50;
                transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94) !important;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .fixed-header {
                width: 100% !important;
                transition: none !important;
            }
        }

        /* Enhanced hover and focus states */
        .sidebar nav a:focus,
        .sidebar nav div:focus {
            outline: none;
            transform: none !important;
            box-shadow: 0 4px 20px rgba(255, 255, 255, 0.15);
        }

        /* Override Tailwind hover:translate-x-1 from markup to keep items stationary */
        .sidebar nav a,
        .sidebar nav div {
            transition: background-color .3s, color .3s, box-shadow .3s;
        }

        .sidebar nav a:hover {
            --tw-translate-x: 0 !important;
        }

        .sidebar nav div:hover {
            --tw-translate-x: 0 !important;
        }

        /* Global accessible focus outline helper */
        .a11y-focus:focus,
        .a11y-focus:focus-visible {
            outline: 3px solid #6366f1 !important;
            /* indigo-500 */
            outline-offset: 2px !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25) !important;
            border-color: #4f46e5 !important;
            /* indigo-600 */
        }

        /* RESET LAYOUT: simplify to stable sidebar + content */
        .main-container {
            display: flex !important;
            align-items: stretch !important;
            padding-left: 0 !important;
            height: 100vh;
        }

        .content-wrapper {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            margin-left: 0 !important;
            height: 100vh;
        }

        body.sidebar-collapsed .content-wrapper {
            margin-left: 0 !important;
        }

        .content-area {
            flex: 1;
            overflow: auto;
            padding: 1.5rem 1.5rem 2rem !important;
        }

        .page-shell {
            max-width: none;
            margin: 0;
            padding: 0 1.5rem 0 0 !important;
        }

        @media (max-width: 1023px) {
            .content-wrapper {
                margin-left: 0 !important;
            }

            .page-shell {
                max-width: 100%;
                padding: 0 1rem !important;
            }
        }
    </style>
</head>

<body class="main-container bg-gray-50">
    <!-- Sidebar -->
    <aside class="sidebar gradient-bg shadow-xl flex flex-col relative" id="mainSidebar">
        <!-- Logo Section -->
        <div class="p-6 border-b border-white/20 relative bg-gradient-to-r from-white/5 to-transparent">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="sidebar-text">
                        <h1 class="text-white text-xl font-bold tracking-tight school-name">
                            <span>SDN Grogol Utara 09</span>
                        </h1>
                        <p class="text-white/80 text-sm font-medium">Aplikasi Manajemen kelas</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Section -->
        <nav class="flex-1 overflow-y-auto px-4 py-4">
            <?php
            $userRole = session()->get('role');
            // Base URL: walikelas and siswa use root routes, admin uses /admin
            $baseUrl = (in_array($userRole, ['walikelas', 'siswa'])) ? '' : '/admin';
            ?>

            <!-- Main Navigation -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4 py-2 px-2">
                    <h3 class="menu-label text-white/70 text-xs font-bold uppercase tracking-wider">Menu Utama</h3>
                    <div class="w-8 h-px bg-white/20"></div>
                </div>

                <ul class="space-y-2">
                    <?php if ($userRole !== 'siswa'): ?>
                        <!-- Dashboard -->
                        <li>
                            <a href="<?= $baseUrl ?>/dashboard" title="Dashboard" class="group flex items-center space-x-3 py-3 px-4 rounded-xl <?= uri_string() === 'admin/dashboard' || uri_string() === 'dashboard' ? 'bg-white/20 text-white shadow-xl border border-white/30' : 'text-white/85 hover:bg-white/15 hover:text-white hover:shadow-lg' ?>">
                                <div class="flex-shrink-0 w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center group-hover:bg-white/30 transition-all duration-300">
                                    <i class="fas fa-chart-pie text-sm"></i>
                                </div>
                                <div class="menu-text flex-1">
                                    <span class="font-semibold text-sm block">Dashboard</span>
                                </div>
                                <?php if (uri_string() === 'admin/dashboard' || uri_string() === 'dashboard'): ?>
                                    <div class="w-1 h-8 bg-white rounded-full"></div>
                                <?php endif; ?>
                            </a>
                        </li>

                        <!-- Data Siswa -->
                        <li>
                            <a href="<?= $baseUrl ?>/data-siswa" title="Data Siswa" class="group flex items-center space-x-3 py-3 px-4 rounded-xl <?= strpos(uri_string(), 'data-siswa') !== false ? 'bg-white/20 text-white shadow-xl border border-white/30' : 'text-white/85 hover:bg-white/15 hover:text-white hover:shadow-lg' ?>">
                                <div class="flex-shrink-0 w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center group-hover:bg-white/30 transition-all duration-300">
                                    <i class="fas fa-graduation-cap text-sm"></i>
                                </div>
                                <div class="menu-text flex-1">
                                    <span class="font-semibold text-sm block">Data Siswa</span>
                                </div>
                            </a>
                        </li>

                        <!-- Absensi & Kehadiran / Daftar Hadir -->
                        <li class="menu-item-with-submenu">
                            <div class="group flex items-center space-x-3 py-3 px-4 rounded-xl <?= strpos(uri_string(), 'absensi') !== false || strpos(uri_string(), 'daftar-hadir') !== false ? 'bg-white/20 text-white shadow-xl border border-white/30' : 'text-white/85 hover:bg-white/15 hover:text-white hover:shadow-lg' ?> cursor-pointer submenu-toggle">
                                <div class="flex-shrink-0 w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center group-hover:bg-white/30 transition-all duration-300">
                                    <i class="fas fa-clipboard-check text-sm"></i>
                                </div>
                                <div class="menu-text flex-1">
                                    <span class="font-semibold text-sm block">
                                        <?= ($userRole === 'walikelas') ? 'Daftar Hadir' : 'Absensi & Kehadiran' ?>
                                    </span>
                                </div>
                                <i class="fas fa-chevron-down text-xs transition-transform duration-300 submenu-chevron"></i>
                            </div>

                            <!-- Submenu -->
                            <div class="submenu pl-4 mt-2 pb-3 space-y-1 overflow-hidden max-h-0 transition-all duration-300">
                                <a href="<?= $baseUrl ?>/absensi/input" title="Input Harian" class="group flex items-center space-x-3 py-2 px-4 rounded-lg <?= strpos(uri_string(), 'absensi/input') !== false ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' ?> transition-all duration-200 transform hover:translate-x-1">
                                    <div class="w-6 h-6 bg-white/15 rounded-md flex items-center justify-center">
                                        <i class="fas fa-plus text-xs"></i>
                                    </div>
                                    <div class="menu-text">
                                        <span class="text-sm font-medium">Input Harian</span>
                                    </div>
                                </a>

                                <a href="/admin/absensi/rekap" title="Rekap Absensi Bulanan" class="group flex items-center space-x-3 py-2 px-4 rounded-lg <?= strpos(uri_string(), 'admin/absensi/rekap') !== false ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' ?> transition-all duration-200 transform hover:translate-x-1">
                                    <div class="w-6 h-6 bg-white/15 rounded-md flex items-center justify-center">
                                        <i class="fas fa-chart-bar text-xs"></i>
                                    </div>
                                    <div class="menu-text">
                                        <span class="text-sm font-medium">Rekap Absensi Bulanan</span>
                                    </div>
                                </a>

                                <a href="/admin/absensi/persentase" title="Persentase Kehadiran" class="group flex items-center space-x-3 py-2 px-4 rounded-lg <?= strpos(uri_string(), 'admin/absensi/persentase') !== false ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' ?> transition-all duration-200 transform hover:translate-x-1">
                                    <div class="w-6 h-6 bg-white/15 rounded-md flex items-center justify-center">
                                        <i class="fas fa-percent text-xs"></i>
                                    </div>
                                    <div class="menu-text">
                                        <span class="text-sm font-medium">Persentase Kehadiran</span>
                                    </div>
                                </a>
                            </div>
                        </li>

                        <!-- Nilai Siswa / Data Nilai Murid -->
                        <?php $userRole = session()->get('role'); ?>
                        <?php if ($userRole !== 'walikelas' && $userRole !== 'siswa'): ?>
                            <li class="menu-item-with-submenu">
                                <div class="group flex items-center space-x-3 py-3 px-4 rounded-xl <?= strpos(uri_string(), 'nilai') !== false ? 'bg-white/20 text-white shadow-xl border border-white/30' : 'text-white/85 hover:bg-white/15 hover:text-white hover:shadow-lg' ?> transition-all duration-300 cursor-pointer submenu-toggle">
                                    <div class="flex-shrink-0 w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center group-hover:bg-white/30 transition-all duration-300">
                                        <i class="fas fa-star text-sm"></i>
                                    </div>
                                    <div class="menu-text flex-1">
                                        <span class="font-semibold text-sm block">Data Nilai Murid</span>
                                    </div>
                                    <i class="fas fa-chevron-down text-xs transition-transform duration-300 submenu-chevron"></i>
                                </div>

                                <!-- Submenu -->
                                <div class="submenu pl-4 mt-2 space-y-1 overflow-hidden max-h-0 transition-all duration-300">
                                    <a href="<?= $baseUrl ?>/mapel" title="Mata Pelajaran" class="group flex items-center space-x-3 py-2 px-4 rounded-lg <?= strpos(uri_string(), 'mapel') !== false ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' ?> transition-all duration-200 transform hover:translate-x-1">
                                        <div class="w-6 h-6 bg-white/15 rounded-md flex items-center justify-center">
                                            <i class="fas fa-book-open text-xs"></i>
                                        </div>
                                        <div class="menu-text">
                                            <span class="text-sm font-medium">Mata Pelajaran</span>
                                        </div>
                                    </a>
                                    <a href="<?= $baseUrl ?>/nilai/input" title="Penilaian Harian" class="group flex items-center space-x-3 py-2 px-4 rounded-lg <?= strpos(uri_string(), 'nilai/input') !== false ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' ?> transition-all duration-200 transform hover:translate-x-1">
                                        <div class="w-6 h-6 bg-white/15 rounded-md flex items-center justify-center">
                                            <i class="fas fa-clipboard-check text-xs"></i>
                                        </div>
                                        <div class="menu-text">
                                            <span class="text-sm font-medium">Penilaian Harian</span>
                                        </div>
                                    </a>
                                    <a href="<?= $baseUrl ?>/nilai/pts" title="PTS" class="group flex items-center space-x-3 py-2 px-4 rounded-lg text-white/70 hover:bg-white/10 hover:text-white transition-all duration-200 transform hover:translate-x-1">
                                        <div class="w-6 h-6 bg-white/15 rounded-md flex items-center justify-center">
                                            <i class="fas fa-file-alt text-xs"></i>
                                        </div>
                                        <div class="menu-text">
                                            <span class="text-sm font-medium">PTS</span>
                                        </div>
                                    </a>
                                    <a href="<?= $baseUrl ?>/nilai/pas" title="PAS" class="group flex items-center space-x-3 py-2 px-4 rounded-lg text-white/70 hover:bg-white/10 hover:text-white transition-all duration-200 transform hover:translate-x-1">
                                        <div class="w-6 h-6 bg-white/15 rounded-md flex items-center justify-center">
                                            <i class="fas fa-file-signature text-xs"></i>
                                        </div>
                                        <div class="menu-text">
                                            <span class="text-sm font-medium">PAS</span>
                                        </div>
                                    </a>
                                    <a href="<?= $baseUrl ?>/nilai/cetak" title="Cetak" class="group flex items-center space-x-3 py-2 px-4 rounded-lg <?= strpos(uri_string(), 'nilai/cetak') !== false ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' ?> transition-all duration-200 transform hover:translate-x-1 mb-1">
                                        <div class="w-6 h-6 bg-white/15 rounded-md flex items-center justify-center">
                                            <i class="fas fa-print text-xs"></i>
                                        </div>
                                        <div class="menu-text">
                                            <span class="text-sm font-medium">Cetak</span>
                                        </div>
                                    </a>
                                </div>
                            </li>
                        <?php endif; ?>

                        <!-- Buku Kasus -->
                        <?php if ($userRole !== 'walikelas' && $userRole !== 'siswa'): ?>
                            <li class="mt-3">
                                <a href="/buku-kasus" title="Buku Kasus" class="group flex items-center space-x-3 py-3 px-4 rounded-xl <?= strpos(uri_string(), 'buku-kasus') !== false ? 'bg-white/20 text-white shadow-xl border border-white/30' : 'text-white/85 hover:bg-white/15 hover:text-white hover:shadow-lg' ?> transition-all duration-300 transform hover:translate-x-1">
                                    <div class="flex-shrink-0 w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center group-hover:bg-white/30 transition-all duration-300">
                                        <i class="fas fa-book text-sm"></i>
                                    </div>
                                    <div class="menu-text flex-1">
                                        <span class="font-semibold text-sm block">Buku Kasus</span>
                                        <span class="text-xs text-white/70">Catatan Masalah Siswa</span>
                                    </div>
                                    <?php if (strpos(uri_string(), 'buku-kasus') !== false): ?>
                                        <div class="w-1 h-8 bg-white rounded-full"></div>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; // end if not siswa 
                    ?>

                    <!-- Classroom (visible untuk semua role termasuk siswa) -->
                    <li class="menu-item-with-submenu mt-3">
                        <div class="group flex items-center space-x-3 py-3 px-4 rounded-xl <?= strpos(uri_string(), 'classroom') !== false ? 'bg-white/20 text-white shadow-xl border border-white/30' : 'text-white/85 hover:bg-white/15 hover:text-white hover:shadow-lg' ?> cursor-pointer submenu-toggle">
                            <div class="flex-shrink-0 w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center group-hover:bg-white/30 transition-all duration-300">
                                <i class="fas fa-chalkboard text-sm"></i>
                            </div>
                            <div class="menu-text flex-1">
                                <span class="font-semibold text-sm block">Classroom</span>
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform duration-300 submenu-chevron"></i>
                        </div>
                        <div class="submenu pl-4 mt-2 space-y-1 overflow-hidden max-h-0 transition-all duration-300">
                            <a href="/classroom/lessons" title="Materi" class="group flex items-center space-x-3 py-2 px-4 rounded-lg <?= strpos(uri_string(), 'classroom/lessons') !== false ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' ?> transition-all duration-200 transform hover:translate-x-1">
                                <div class="w-6 h-6 bg-white/15 rounded-md flex items-center justify-center">
                                    <i class="fas fa-book-open text-xs"></i>
                                </div>
                                <div class="menu-text">
                                    <span class="text-sm font-medium">Materi</span>
                                </div>
                            </a>
                            <a href="/classroom/assignments" title="Tugas" class="group flex items-center space-x-3 py-2 px-4 rounded-lg <?= strpos(uri_string(), 'classroom/assignments') !== false ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' ?> transition-all duration-200 transform hover:translate-x-1 mb-1">
                                <div class="w-6 h-6 bg-white/15 rounded-md flex items-center justify-center">
                                    <i class="fas fa-tasks text-xs"></i>
                                </div>
                                <div class="menu-text">
                                    <span class="text-sm font-medium">Tugas</span>
                                </div>
                            </a>
                        </div>
                    </li>

                    <!-- Menu Kebiasaan dihapus sesuai permintaan user -->
                    <!-- Rekap Bulanan 7 Kebiasaan (Admin & Walikelas) -->
                    <?php if ($userRole !== 'siswa'): ?>
                        <li>
                            <a href="<?= $baseUrl ?>/habits/monthly" title="Rekap 7 Kebiasaan" class="group flex items-center space-x-3 py-3 px-4 rounded-xl <?= (strpos(uri_string(), 'habits/monthly') !== false) ? 'bg-white/20 text-white shadow-xl border border-white/30' : 'text-white/85 hover:bg-white/15 hover:text-white hover:shadow-lg' ?> transition-all duration-300 transform hover:translate-x-1">
                                <div class="flex-shrink-0 w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center group-hover:bg-white/30 transition-all duration-300">
                                    <i class="fas fa-calendar-check text-sm"></i>
                                </div>
                                <div class="menu-text flex-1">
                                    <span class="font-semibold text-sm block">Rekap 7 Kebiasaan</span>
                                    <span class="text-xs text-white/70">Bulanan</span>
                                </div>
                                <?php if (strpos(uri_string(), 'habits/monthly') !== false): ?>
                                    <div class="w-1 h-8 bg-white rounded-full"></div>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Menu khusus Admin -->
            <?php
            $userRole = session()->get('role');
            if (!isset($userRole) || ($userRole !== 'walikelas' && $userRole !== 'siswa')):
            ?>
                <!-- Data Guru -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4 py-2 px-2">
                        <h3 class="menu-label text-white/70 text-xs font-bold uppercase tracking-wider">Data</h3>
                        <div class="w-8 h-px bg-white/20"></div>
                    </div>

                    <ul class="space-y-2">
                        <li>
                            <a href="/admin/guru" title="Data Guru" class="group flex items-center space-x-3 py-3 px-4 rounded-xl <?= uri_string() === 'admin/guru' ? 'bg-white/20 text-white shadow-xl border border-white/30' : 'text-white/85 hover:bg-white/15 hover:text-white hover:shadow-lg' ?> transition-all duration-300 transform hover:translate-x-1">
                                <div class="flex-shrink-0 w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center group-hover:bg-white/30 transition-all duration-300">
                                    <i class="fas fa-chalkboard-teacher text-sm"></i>
                                </div>
                                <div class="menu-text flex-1">
                                    <span class="font-semibold text-sm block">Data Guru</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Academic Section -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4 py-2 px-2">
                        <h3 class="menu-label text-white/70 text-xs font-bold uppercase tracking-wider">Akademik</h3>
                        <div class="w-8 h-px bg-white/20"></div>
                    </div>

                    <ul class="space-y-2">
                        <!-- Kalender Akademik -->
                        <li>
                            <a href="/admin/kalender-akademik" title="Kalender Akademik" class="group flex items-center space-x-3 py-3 px-4 rounded-xl <?= uri_string() === 'admin/kalender-akademik' ? 'bg-white/20 text-white shadow-xl border border-white/30' : 'text-white/85 hover:bg-white/15 hover:text-white hover:shadow-lg' ?> transition-all duration-300 transform hover:translate-x-1">
                                <div class="flex-shrink-0 w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center group-hover:bg-white/30 transition-all duration-300">
                                    <i class="fas fa-calendar-alt text-sm"></i>
                                </div>
                                <div class="menu-text flex-1">
                                    <span class="font-semibold text-sm block">Kalender Akademik</span>
                                </div>
                            </a>
                        </li>

                        <!-- Mata Pelajaran -->
                        <li>
                            <a href="/admin/mapel" title="Mata Pelajaran" class="group flex items-center space-x-3 py-3 px-4 rounded-xl <?= strpos(uri_string(), 'admin/mapel') !== false ? 'bg-white/20 text-white shadow-xl border border-white/30' : 'text-white/85 hover:bg-white/15 hover:text-white hover:shadow-lg' ?> transition-all duration-300 transform hover:translate-x-1">
                                <div class="flex-shrink-0 w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center group-hover:bg-white/30 transition-all duration-300">
                                    <i class="fas fa-book-open text-sm"></i>
                                </div>
                                <div class="menu-text flex-1">
                                    <span class="font-semibold text-sm block">Mata Pelajaran</span>
                                </div>
                            </a>
                        </li>

                        <!-- Naik Kelas -->
                        <li>
                            <a href="/admin/naik-kelas" title="Naik Kelas" class="group flex items-center space-x-3 py-3 px-4 rounded-xl <?= uri_string() === 'admin/naik-kelas' ? 'bg-white/20 text-white shadow-xl border border-white/30' : 'text-white/85 hover:bg-white/15 hover:text-white hover:shadow-lg' ?> transition-all duration-300 transform hover:translate-x-1">
                                <div class="flex-shrink-0 w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center group-hover:bg-white/30 transition-all duration-300">
                                    <i class="fas fa-level-up-alt text-sm"></i>
                                </div>
                                <div class="menu-text flex-1">
                                    <span class="font-semibold text-sm block">Naik Kelas</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- School Management -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4 py-2 px-2">
                        <h3 class="menu-label text-white/70 text-xs font-bold uppercase tracking-wider">Sekolah</h3>
                        <div class="w-8 h-px bg-white/20"></div>
                    </div>

                    <ul class="space-y-2">
                        <!-- Profil Sekolah -->
                        <li>
                            <a href="/admin/profil-sekolah" title="Profil Sekolah" class="group flex items-center space-x-3 py-3 px-4 rounded-xl <?= uri_string() === 'admin/profil-sekolah' ? 'bg-white/20 text-white shadow-xl border border-white/30' : 'text-white/85 hover:bg-white/15 hover:text-white hover:shadow-lg' ?> transition-all duration-300 transform hover:translate-x-1">
                                <div class="flex-shrink-0 w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center group-hover:bg-white/30 transition-all duration-300">
                                    <i class="fas fa-school text-sm"></i>
                                </div>
                                <div class="menu-text flex-1">
                                    <span class="font-semibold text-sm block">Profil Sekolah</span>
                                </div>
                            </a>
                        </li>

                        <!-- Kelola User -->
                        <li>
                            <a href="/admin/users" title="Kelola User" class="group flex items-center space-x-3 py-3 px-4 rounded-xl <?= uri_string() === 'admin/users' ? 'bg-white/20 text-white shadow-xl border border-white/30' : 'text-white/85 hover:bg-white/15 hover:text-white hover:shadow-lg' ?> transition-all duration-300 transform hover:translate-x-1">
                                <div class="flex-shrink-0 w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center group-hover:bg-white/30 transition-all duration-300">
                                    <i class="fas fa-users-cog text-sm"></i>
                                </div>
                                <div class="menu-text flex-1">
                                    <span class="font-semibold text-sm block">Kelola User</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- System Settings -->
            <div class="mt-auto pt-6 border-t border-white/20">
                <div class="flex items-center justify-between mb-4 py-2 px-2">
                    <h3 class="menu-label text-white/70 text-xs font-bold uppercase tracking-wider">Sistem</h3>
                    <div class="w-8 h-px bg-white/20"></div>
                </div>

                <ul class="space-y-2">
                    <!-- Profile -->
                    <li>
                        <?php
                        // Build profile URL per role
                        $profileUrl = '/admin/profile';
                        if ($userRole === 'walikelas') {
                            $profileUrl = '/profile';
                        } elseif ($userRole === 'siswa') {
                            $profileUrl = '/siswa/profile';
                        }
                        ?>
                        <a href="<?= $profileUrl ?>" title="Profile" class="group flex items-center space-x-3 py-3 px-4 rounded-xl <?= strpos(uri_string(), 'profile') !== false ? 'bg-white/20 text-white shadow-xl border border-white/30' : 'text-white/85 hover:bg-white/15 hover:text-white hover:shadow-lg' ?> transition-all duration-300 transform hover:translate-x-1">
                            <div class="flex-shrink-0 w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center group-hover:bg-white/30 transition-all duration-300">
                                <i class="fas fa-user-circle text-sm"></i>
                            </div>
                            <div class="menu-text flex-1">
                                <span class="font-semibold text-sm block">
                                    <?php
                                    $userRole = session()->get('role');
                                    echo ($userRole === 'walikelas') ? 'Profil Guru' : 'Profile';
                                    ?>
                                </span>
                            </div>
                        </a>
                    </li>

                    <!-- Settings - Only for Admin -->
                    <?php
                    $userRole = session()->get('role');
                    if (isset($userRole) && $userRole === 'admin'):
                    ?>
                        <li>
                            <a href="/admin/settings" title="Settings" class="group flex items-center space-x-3 py-3 px-4 rounded-xl <?= uri_string() === 'admin/settings' ? 'bg-white/20 text-white shadow-xl border border-white/30' : 'text-white/85 hover:bg-white/15 hover:text-white hover:shadow-lg' ?> transition-all duration-300 transform hover:translate-x-1">
                                <div class="flex-shrink-0 w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center group-hover:bg-white/30 transition-all duration-300">
                                    <i class="fas fa-cog text-sm"></i>
                                </div>
                                <div class="menu-text flex-1">
                                    <span class="font-semibold text-sm block">Settings</span>
                                </div>
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Logout -->
                    <li class="pt-2 border-t border-white/10">
                        <a href="/logout" title="Logout" class="group flex items-center space-x-3 py-3 px-4 rounded-xl text-red-200 hover:bg-red-500/20 hover:text-red-100 transition-all duration-300 transform hover:translate-x-1">
                            <div class="flex-shrink-0 w-8 h-8 bg-red-500/20 rounded-lg flex items-center justify-center group-hover:bg-red-500/30 transition-all duration-300">
                                <i class="fas fa-sign-out-alt text-sm"></i>
                            </div>
                            <div class="menu-text flex-1">
                                <span class="font-semibold text-sm block">Logout</span>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="content-wrapper" id="contentWrapper">
        <!-- Fixed Header -->
        <header class="fixed-header" id="fixedHeader">
            <div class="flex items-center h-full px-4">
                <!-- Left: mobile toggle -->
                <button id="mobileMenuToggle" class="lg:hidden w-9 h-9 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center text-white shadow-sm hover:shadow-md">
                    <i class="fas fa-bars text-sm"></i>
                </button>
                <!-- Judul navbar dihapus sesuai permintaan -->
                <div class="ml-3"></div>
                <div class="ml-auto flex items-center space-x-3" x-data="{open:false}" @click.away="open=false">
                    <?php
                    $session = session();
                    $displayName = $session->get('nama') ?: 'Pengguna';
                    $roleKey = $session->get('role') ?: '';
                    $roleMap = ['admin' => 'Administrator', 'walikelas' => 'Wali Kelas', 'guru' => 'Guru', 'siswa' => 'Siswa'];
                    $displayRole = $roleMap[$roleKey] ?? 'Pengguna';
                    $initial = strtoupper(mb_substr(trim($displayName), 0, 1, 'UTF-8')) ?: 'U';
                    ?>
                    <button @click="open=!open" class="flex items-center space-x-2 focus:outline-none">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-semibold shadow ring-2 ring-indigo-200">
                            <?= esc($initial) ?>
                        </div>
                        <div class="hidden sm:flex flex-col text-left">
                            <span class="text-sm font-medium text-gray-800 leading-none"><?= esc($displayName) ?></span>
                            <span class="text-xs text-gray-500 leading-none"><?= esc($displayRole) ?></span>
                        </div>
                        <i class="fas fa-chevron-down text-xs text-gray-400 hidden sm:block"></i>
                    </button>
                    <div x-show="open" x-transition class="absolute top-14 right-4 w-56 bg-white rounded-xl shadow-2xl py-2 ring-1 ring-black ring-opacity-5 border border-gray-100 z-50">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-800 mb-0"><?= esc($displayName) ?></p>
                            <p class="text-xs text-gray-500 capitalize"><?= esc($displayRole) ?></p>
                        </div>
                        <a href="/logout" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-sign-out-alt w-5"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <main class="content-area">
            <div class="page-shell">
                <?= $this->renderSection('content') ?>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Hitung lebar scrollbar untuk kompensasi saat modal open agar tidak geser
            const scrollBarWidth = window.innerWidth - document.documentElement.clientWidth;
            document.documentElement.style.setProperty('--scrollbar-width', scrollBarWidth + 'px');
            const sidebar = document.getElementById('mainSidebar');
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');

            // Mobile menu toggle
            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', function() {
                    const willOpen = !sidebar.classList.contains('open');
                    sidebar.classList.toggle('open');
                    if (willOpen && window.innerWidth <= 1023) {
                        // Pastikan teks menu tampil (hapus collapsed & expanded logic for mobile)
                        sidebar.classList.remove('collapsed');
                        sidebar.classList.remove('expanded');
                    }
                });
            }

            // Submenu toggle functionality
            const submenuToggles = document.querySelectorAll('.submenu-toggle');
            submenuToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const menuItem = this.closest('.menu-item-with-submenu');
                    const isOpen = menuItem.classList.contains('open');

                    // Close all other submenus
                    document.querySelectorAll('.menu-item-with-submenu').forEach(item => {
                        item.classList.remove('open');
                    });

                    // Toggle current submenu
                    if (!isOpen) {
                        menuItem.classList.add('open');
                    }
                });
            });

            // Auto-open submenu if current page is a submenu item
            const currentPath = window.location.pathname;
            if (currentPath.includes('/admin/absensi/')) {
                const absensiMenu = document.querySelector('.menu-item-with-submenu');
                if (absensiMenu) {
                    absensiMenu.classList.add('open');
                }
            }

            if (currentPath.includes('/admin/nilai/')) {
                const nilaiMenus = document.querySelectorAll('.menu-item-with-submenu');
                nilaiMenus.forEach(menu => {
                    const nilaiSubmenu = menu.querySelector('a[href*="/admin/nilai/"]');
                    if (nilaiSubmenu) {
                        menu.classList.add('open');
                    }
                });
            }

            // Auto-open Kebiasaan submenu for /guru/* and /siswa pages
            if (currentPath.startsWith('/guru/')) {
                document.querySelectorAll('.menu-item-with-submenu').forEach(menu => {
                    const kebiasaanLink = menu.querySelector('a[href^="/guru/"]');
                    if (kebiasaanLink) {
                        menu.classList.add('open');
                    }
                });
            }
            if (currentPath === '/siswa' || currentPath.startsWith('/siswa/')) {
                document.querySelectorAll('.menu-item-with-submenu').forEach(menu => {
                    const siswaLink = menu.querySelector('a[href^="/siswa"]');
                    if (siswaLink) {
                        menu.classList.add('open');
                    }
                });
            }

            // Close mobile menu when clicking outside
            document.addEventListener('click', function(e) {
                if (window.innerWidth <= 1023) {
                    if (!sidebar.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
                        sidebar.classList.remove('open');
                    }
                }
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 1023) sidebar.classList.remove('open');
            });

            // Add smooth scrolling for sidebar when menu item is clicked
            const menuLinks = document.querySelectorAll('.sidebar nav a');
            menuLinks.forEach(link => {
                link.addEventListener('click', function() {
                    // Add a subtle click animation
                    this.style.transform = 'scale(0.98)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            });
        });
    </script>
</body>

</html>
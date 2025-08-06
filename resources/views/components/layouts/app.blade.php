<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @livewireStyles

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* --- STYLE UNTUK STICKY FOOTER --- */
        html,
        body {
            height: 100%;
        }

        #app {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* --- AKHIR STYLE STICKY FOOTER --- */

        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fa;
        }

        .navbar {
            padding-bottom: 10px;
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
            color: #ffffff !important;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
        }

        .dropdown-menu {
            background-color: rgb(193, 39, 45);
            border-radius: 10px;
        }

        .dropdown-item {
            color: white !important;
        }

        .dropdown-item:hover {
            background-color: rgba(133, 30, 33, 1) !important;
            color: white;
        }
    </style>
</head>

<body>
    <div id="app">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-md navbar-dark shadow-sm" style="background-color: rgb(193, 39, 45); z-index: 1000; position: fixed; width: 100%; top: 0;">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ url('/') }}" style="margin-right: 10px;">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <div class="user-wrapper" style="margin-right: 20px;">
                    @auth
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle text-white user-dropdown-button" href="#" role="button" data-bs-toggle="dropdown">
                                <img src="{{ asset('/build/assets/asqi.png') }}" alt="User Image">
                                <span>{{ Auth::user()->name }}</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end user-dropdown-menu fade-in-down" style="background-color: rgb(193, 39, 45);">
                                <div class="user-dropdown-header">
                                    <img src="{{ asset('/build/assets/asqi.png') }}" class="rounded-circle" alt="User Image">
                                    <p class="user-name">{{ Auth::user()->name }}</p>
                                    <p class="user-role">{{ Str::ucfirst(Auth::user()->peran) }}</p>
                                </div>
                                <hr class="dropdown-divider">
                                <a class="dropdown-item text-white" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                    @endauth
                </div>
            </div>
        </nav>

        <style>
            .user-dropdown-button {
                background-color: rgba(255, 255, 255, 0.2) !important;
                border-radius: 50em;
                padding: 6px 12px !important;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                border: 1px solid transparent;
            }

            .user-dropdown-button:hover,
            .user-dropdown-button:focus {
                background-color: rgba(255, 255, 255, 0.35) !important;
                transform: translateY(-2px);
                box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
            }

            .user-dropdown-button img {
                width: 32px;
                height: 32px;
                border-radius: 50%;
                margin-right: 10px;
                border: 2px solid #fff;
            }

            .user-dropdown-menu.fade-in-down {
                border-radius: 14px !important;
                border: none !important;
                box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
                padding: 0 !important;
                margin-top: 12px !important;
                overflow: hidden;
                opacity: 0;
                transform: translateY(-10px);
                transition: opacity 0.25s ease-out, transform 0.25s ease-out;
                pointer-events: none;
            }

            .user-dropdown-menu.fade-in-down.show {
                opacity: 1;
                transform: translateY(0);
                pointer-events: auto;
            }

            .user-dropdown-header {
                padding: 15px 20px;
                text-align: center;
                background-color: rgba(0, 0, 0, 0.05);
            }

            .user-dropdown-header .user-name {
                font-weight: bold;
                color: #fff;
                margin-top: 5px;
                margin-bottom: 0;
            }

            .user-dropdown-header .user-role {
                font-size: 0.8rem;
                color: rgba(255, 255, 255, 0.8);
            }

            .user-dropdown-header img {
                width: 60px;
                height: 60px;
                border: 3px solid #fff;
            }

            .user-dropdown-menu .dropdown-item {
                padding: 12px 20px;
                font-weight: 500;
                display: flex;
                align-items: center;
                gap: 12px;
                transition: all 0.2s ease-in-out;
            }

            .user-dropdown-menu .dropdown-item:hover {
                background-color: rgba(165, 33, 37, 1) !important;
                color: #fff !important;
                transform: translateX(5px);
            }

            .user-dropdown-menu .dropdown-item i {
                width: 20px;
            }

            .user-dropdown-menu .dropdown-divider {
                margin: 0;
                border-top: 1px solid rgba(255, 255, 255, 0.2);
            }
        </style>

        <!-- === CSS PERBAIKAN LAYOUT & FOOTER === -->
        <style>
            .sidebar {
                width: 240px;
                /* Lebar sidebar dikecilkan */
                height: 100vh;
                position: fixed;
                top: 0;
                left: 0;
                background-color: #ffffff;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
                z-index: 999;
                display: flex;
                flex-direction: column;
                padding-top: 75px;
            }

            .sidebar-header {
                padding: 20px 20px 15px 20px;
                text-align: center;
                border-bottom: 1px solid #e9ecef;
            }

            .sidebar-header img {
                width: 70px;
                height: 70px;
                margin-bottom: 10px;
                transition: all 0.3s ease;
            }

            .sidebar-header .app-name {
                font-size: 1.25rem;
                font-weight: 700;
                color: #343a40;
            }

            .sidebar-nav {
                list-style: none;
                padding: 15px;
                margin: 0;
                flex-grow: 1;
                overflow-y: auto;
            }

            .sidebar-link {
                display: flex;
                align-items: center;
                padding: 12px 16px;
                /* Padding horizontal disesuaikan */
                margin-bottom: 8px;
                border-radius: 10px;
                text-decoration: none !important;
                font-weight: 500;
                color: #495057;
                transition: all 0.25s ease-in-out;
            }

            .sidebar-link i.nav-icon {
                font-size: 1.2rem;
                margin-right: 14px;
                /* Margin ikon disesuaikan */
                width: 20px;
                text-align: center;
                transition: all 0.25s ease-in-out;
            }

            .sidebar-link:not(.active):hover {
                background-color: rgba(193, 39, 45, 0.08);
                color: rgb(193, 39, 45);
                transform: translateX(5px);
            }

            .sidebar-link.active {
                background: rgb(193, 39, 45);
                color: #ffffff !important;
                font-weight: 600;
                box-shadow: 0 5px 15px -5px rgba(193, 39, 45, 0.6);
            }

            .sidebar-link.active i.nav-icon {
                transform: scale(1.1);
            }

            .sidebar-link .bi-chevron-down {
                margin-left: auto;
                transition: transform 0.3s ease;
                font-size: 0.8rem;
            }

            .sidebar-link[aria-expanded="true"] .bi-chevron-down {
                transform: rotate(180deg);
            }

            .sidebar-submenu {
                list-style: none;
                padding: 5px 0 5px 25px;
                /* Indentasi disesuaikan */
                margin: 0;
            }

            .sub-link {
                padding: 10px 15px;
                font-size: 0.9rem;
            }

            .sub-link.active {
                background: transparent !important;
                box-shadow: none;
                color: rgb(193, 39, 45) !important;
                font-weight: 700;
            }

            .page-content-wrapper {
                margin-left: 240px;
                /* Disesuaikan dengan lebar sidebar baru */
                width: calc(100% - 240px);
                /* Disesuaikan dengan lebar sidebar baru */
                padding-top: 75px;
                display: flex;
                flex-direction: column;
                min-height: 100vh;
            }

            .main-content {
                flex: 1;
                padding: 2rem;
                width: 100%;
            }

            footer {
                width: 100%;
                margin-left: 0 !important;
                box-shadow: 0px -4px 20px rgba(0, 0, 0, 0.15);
                background-color: #fff;
            }

            footer p,
            footer div {
                margin-left: 0 !important;
            }
        </style>

        <div class="page-container d-flex">
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="sidebar-header">
                    <img src="{{ asset('/build/assets/asqi.png') }}" alt="Logo ASQI">
                    <div class="app-name">ASQI</div>
                </div>
                <ul class="sidebar-nav">
                    <li>
                        <a href="{{ route('beranda') }}" wire:navigate class="sidebar-link {{ request()->routeIs('beranda') ? 'active' : '' }}">
                            <i class="bi bi-house-door-fill nav-icon"></i>
                            <span>Beranda</span>
                        </a>
                    </li>
                    @if (Auth::user()->peran == 'admin')
                    <li>
                        <a href="{{ route('user') }}" wire:navigate class="sidebar-link {{ request()->routeIs('user') ? 'active' : '' }}">
                            <i class="bi bi-people-fill nav-icon"></i>
                            <span>Role User</span>
                        </a>
                    </li>
                    <li>
                        <a class="sidebar-link {{ request()->routeIs(['semua_karyawan', 'daftar_jabatan', 'semua-siswa']) ? 'active' : '' }}"
                            data-bs-toggle="collapse" href="#dataMasterCollapse" role="button"
                            aria-expanded="{{ request()->routeIs(['semua_karyawan', 'daftar_jabatan', 'semua-siswa']) ? 'true' : 'false' }}"
                            aria-controls="dataMasterCollapse">
                            <i class="bi bi-person-lines-fill nav-icon"></i>
                            <span>Data Master</span>
                            <i class="bi bi-chevron-down"></i>
                        </a>
                        <div class="collapse {{ request()->routeIs(['semua_karyawan', 'daftar_jabatan', 'semua-siswa']) ? 'show' : '' }}" id="dataMasterCollapse">
                            <ul class="sidebar-submenu">
                                <li><a href="{{ route('semua_karyawan') }}" class="sidebar-link sub-link {{ request()->routeIs('semua_karyawan') ? 'active' : '' }}">Daftar Karyawan</a></li>
                                <li><a href="{{ route('daftar_jabatan') }}" class="sidebar-link sub-link {{ request()->routeIs('daftar_jabatan') ? 'active' : '' }}">Daftar Jabatan</a></li>
                                <li><a href="{{ route('semua-siswa') }}" class="sidebar-link sub-link {{ request()->routeIs('semua-siswa') ? 'active' : '' }}">Daftar Magang</a></li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="{{ route('gaji') }}" wire:navigate class="sidebar-link {{ request()->routeIs('gaji') ? 'active' : '' }}">
                            <i class="bi bi-receipt-cutoff nav-icon"></i>
                            <span>Input Gaji</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('kas.manajemen') }}" wire:navigate class="sidebar-link {{ request()->routeIs('kas.manajemen') ? 'active' : '' }}">
                            <i class="bi bi-cash nav-icon"></i>
                            <span>Transaksi</span>
                        </a>
                    </li>
                    @endif
                    <li>
                        <a href="{{ route('laporan') }}" wire:navigate class="sidebar-link {{ request()->routeIs('laporan') ? 'active' : '' }}">
                            <i class="bi bi-file-earmark-text-fill nav-icon"></i>
                            <span>Laporan</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Wrapper konten dan footer -->
            <div class="page-content-wrapper">
                <!-- Konten Utama -->
                <main class="main-content">
                    {{ $slot }}
                </main>

                <!-- Footer -->
                {{-- <footer class="text-center py-5 px-4">
                    <p class="text-dark" style="text-shadow: 0 1px 2px rgba(0,0,0,0.2); font-weight: 500;">
                        © 2025 Turky & Shalwa. All rights reserved.
                    </p>
                    <div class="d-flex justify-content-center">
                        <a href="#" class="text-dark me-3" style="text-shadow: 0 1px 1px rgba(0,0,0,0.2); font-weight: 500;">Privacy Policy</a>
                        <a href="#" class="text-dark me-3" style="text-shadow: 0 1px 1px rgba(0,0,0,0.2); font-weight: 500;">Terms of Service</a>
                        <a href="mailto:turkykw50@gmail.com" class="text-dark" style="text-shadow: 0 1px 1px rgba(0,0,0,0.2); font-weight: 500;">Kontak Kami</a>
                    </div>
                </footer> --}}
            </div>
        </div>
    </div>

    @livewireScripts
</body>

</html>
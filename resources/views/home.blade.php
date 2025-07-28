@extends('layouts.app')

@section('styles')
{{-- Menempatkan style di section terpisah agar lebih rapi --}}
{{-- Impor Google Fonts --}}
<link rel.preconnect href="https://fonts.googleapis.com">
<link rel.preconnect href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Montserrat:wght@700;800&display=swap" rel="stylesheet">   
@section('content')
<!-- KONTEN HTML ANDA (Tidak perlu diubah, hanya beberapa teks) -->

<!-- Masthead-->
<header class="masthead">
    <div class="container">
        <div class="masthead-subheading text-white">Kreativitas Bertemu Teknologi</div>
        <div class="masthead-heading text-uppercase text-white">Desain Impian Anda</div>
        @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class=" btn btn-primary btn-xl text-uppercase text-white" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif    
    </div>
</header>



<style>
    /* ================================================================== */
    /* ==         FONDASI DESAIN: FONT & PALET WARNA BARU              == */
    /* ================================================================== */
    :root {
        --merah-cerah: #E53935;
        /* Merah yang lebih vibrant dan modern */
        --merah-hover: #C62828;
        /* Versi lebih gelap untuk hover */
        --teks-gelap: #34495e;
        /* Abu-abu gelap yang elegan */
        --teks-abu: #95a5a6;
        /* Abu-abu terang untuk sub-teks */
        --latar-putih: #ffffff;
        --latar-abu: #f9fafb;
        /* Abu-abu sangat terang untuk section */
        --border-halus: #ecf0f1;
    }

    body {
        font-family: 'Lato', sans-serif;
        color: var(--teks-gelap);
        background-color: var(--latar-putih);
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6,
    .masthead-heading,
    .portfolio-caption-heading {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
    }

    .page-section {
        padding: 7rem 0;
    }

    .section-heading {
        font-size: 2.8rem;
        font-weight: 800;
        margin-bottom: 1.5rem;
    }

    .section-subheading {
        font-size: 1.2rem;
        font-weight: 400;
        color: var(--teks-abu);
        margin-bottom: 5rem;
    }

    /* ================================================================== */
    /* ==                HEADER DENGAN GAMBAR ONLINE                   == */
    /* ================================================================== */
    header.masthead {
        /* GAMBAR LANGSUNG DARI UNSPLASH - TIDAK PERLU DOWNLOAD */
        background-image:
            linear-gradient(rgba(0, 0, 0, 0.36), rgba(0, 0, 0, 0.4)),
            url("/build/assets/office.jpg");
        background-size: cover;
        background-position: center;
        padding: 14rem 0;
        text-align: center;
    }

    header.masthead .masthead-subheading {
        color: var(--teks-gelap);
        font-size: 1.75rem;
        font-style: italic;
        margin-bottom: 2rem;
    }

    header.masthead .masthead-heading {
        color: var(--teks-gelap);
        font-size: 4.5rem;
        font-weight: 800;
        margin-bottom: 4rem;
        text-shadow: none;
        /* Teks bersih tanpa bayangan karena latar cerah */
    }

    /* ================================================================== */
    /* ==           TOMBOL, NAVIGASI & INTERAKSI YANG HIDUP            == */
    /* ================================================================== */
    .btn-xl {
        padding: 1.25rem 2.5rem;
        font-size: 1.1rem;
    }

    .btn-primary {
        background-color: var(--merah-cerah);
        border-color: var(--merah-cerah);
        color: var(--putih);
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        border-radius: 8px;
        /* Sedikit lebih kotak, lebih modern */
        transition: all 0.3s ease;
    }

    .btn-primary:hover,
    .btn-primary:focus,
    .btn-primary:active {
        background-color: var(--merah-hover) !important;
        border-color: var(--merah-hover) !important;
        transform: translateY(-4px);
        box-shadow: 0 15px 25px rgba(0, 0, 0, 0.1);
    }

    #mainNav {
        padding-top: 1.5rem;
        padding-bottom: 1.5rem;
        transition: all 0.3s ease;
    }

    /* Efek Navbar saat scroll */
    #mainNav.navbar-shrink {
        background-color: var(--putih);
        padding-top: 1rem;
        padding-bottom: 1rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
    }

    #mainNav.navbar-shrink .navbar-brand,
    #mainNav.navbar-shrink .nav-link {
        color: var(--teks-gelap);
    }

    #mainNav.navbar-shrink .nav-link.active,
    #mainNav.navbar-shrink .nav-link:hover {
        color: var(--merah-cerah);
    }

    /* ================================================================== */
    /* ==     KARTU LAYANAN & PORTOFOLIO DENGAN DESAIN MODERN          == */
    /* ================================================================== */
    .bg-light {
        background-color: var(--latar-abu) !important;
    }

    /* Kartu Layanan (Services) */
    #services .col-md-4 {
        padding: 2rem;
    }

    #services .fa-stack {
        transition: transform 0.3s ease;
    }

    #services .col-md-4:hover .fa-stack {
        transform: scale(1.1);
    }

    .fa-stack .text-primary {
        color: var(--merah-cerah) !important;
    }

    /* Kartu Portofolio */
    .portfolio-item {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid var(--border-halus);
        background-color: var(--putih);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
        transition: all 0.3s ease;
    }

    .portfolio-item:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(52, 73, 94, 0.1);
    }

    .portfolio-item .portfolio-hover {
        background: linear-gradient(45deg, var(--merah-cerah), #ff7043);
        /* Gradasi merah-oranye */
        opacity: 0;
        transition: opacity 0.4s ease;
    }

    .portfolio-item:hover .portfolio-hover {
        opacity: 0.95;
    }

    .portfolio-caption {
        padding: 1.75rem;
        text-align: center;
    }
</style>
</body>

</html>



@endsection
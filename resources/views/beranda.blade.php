<x-layouts.app>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-11 col-xl-10">

                <!-- Header Banner Selamat Datang (Versi Baru) -->
                <div class="header-banner card border-0 shadow-sm mb-5">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <!-- Sisi Kiri: Avatar & Teks -->
                            <div class="d-flex align-items-center">
                                <div class="header-avatar bg-primary-subtle text-primary me-3 me-md-4">
                                    <i class="bi bi-person-fill fs-3"></i>
                                    {{-- Alternatif: Gunakan inisial nama --}}
                                    {{-- <span class="fw-bold fs-5">{{ substr($namaPengguna ?? 'P', 0, 1) }}</span> --}}
                                </div>
                                <div>
                                    <h2 class="fw-bolder mb-1 fs-3">Selamat Datang, {{ $namaPengguna ?? 'Pengguna' }}!</h2>
                                    <p class="text-muted mb-0">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                                    </p>
                                </div>
                            </div>
                            <!-- Sisi Kanan: Tombol Aksi -->
                            <div class="text-lg-end mt-2 mt-md-0">
                                <a href="{{ route('kas.manajemen') }}" class="btn btn-primary rounded-pill px-4 py-2">
                                    <i class="bi bi-plus-circle-fill me-1"></i> Tambah Transaksi
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kartu Ringkasan Keuangan -->
                <div class="row g-4 mb-5">
                    <!-- Pemasukan -->
                    <div class="col-lg-4 col-md-6">
                        <div class="card summary-card card-income border-0 h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="card-text-label mb-2">Pemasukan Bulan Ini</p>
                                        <h3 class="card-text-value fw-bold">Rp {{ number_format($totalPemasukanBulanIni ?? 0, 0, ',', '.') }}</h3>
                                    </div>
                                    <div class="icon-wrapper">
                                        <i class="bi bi-arrow-down-right-circle-fill"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pengeluaran -->
                    <div class="col-lg-4 col-md-6">
                        <div class="card summary-card card-expense border-0 h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="card-text-label mb-2">Pengeluaran Bulan Ini</p>
                                        <h3 class="card-text-value fw-bold">Rp {{ number_format($totalPengeluaranBulanIni ?? 0, 0, ',', '.') }}</h3>
                                    </div>
                                    <div class="icon-wrapper">
                                        <i class="bi bi-arrow-up-right-circle-fill"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Saldo Saat Ini -->
                    <div class="col-lg-4 col-md-12">
                        <div class="card summary-card card-balance border-0 h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="card-text-label mb-2">Total Saldo Anda</p>
                                        <h3 class="card-text-value fw-bold">Rp {{ number_format($saldoSaatIni ?? 0, 0, ',', '.') }}</h3>
                                    </div>
                                    <div class="icon-wrapper">
                                        <i class="bi bi-wallet2"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Aktivitas Terbaru -->
                <div class="card card-custom">
                    <div class="card-header card-header-custom">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-receipt-cutoff me-2"></i>Aktivitas Terbaru</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse ($transaksiTerbaru as $transaksi)
                            <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-circle-sm me-3 {{ $transaksi->jenis == 'masuk' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                        @if ($transaksi->jenis == 'masuk')
                                        <i class="bi bi-plus-lg"></i>
                                        @else
                                        <i class="bi bi-dash-lg"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $transaksi->keterangan }}</div>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($transaksi->tanggal)->isoFormat('dddd, D MMMM Y') }}</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="fw-bold fs-5 {{ $transaksi->jenis == 'masuk' ? 'text-success-emphasis' : 'text-danger-emphasis' }}">
                                        {{ $transaksi->jenis == 'masuk' ? '+' : '-' }}Rp{{ number_format($transaksi->jumlah, 0, ',', '.') }}
                                    </span>
                                </div>
                            </li>
                            @empty
                            <li class="list-group-item text-center py-5">
                                <i class="bi bi-safe2 fs-1 text-muted"></i>
                                <h5 class="mt-3 fw-bold">Belum Ada Transaksi</h5>
                                <p class="text-muted mb-0">Mulai catat transaksi pertama Anda untuk melihat riwayatnya di sini.</p>
                            </li>
                            @endforelse
                        </ul>
                    </div>
                    @if ($transaksiTerbaru->isNotEmpty())
                    <div class="card-footer text-center bg-light-subtle border-0">
                        <a href="{{ route('laporan') }}" class="text-decoration-none fw-semibold">
                            Lihat Semua Riwayat <i class="bi bi-arrow-right-short"></i>
                        </a>
                    </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <!-- CSS Kustom -->
    <style>
        body {
            background-color: #f8f9fa;
        }

        /* Gaya Header Banner Baru */
        .header-banner {
            background: #fff;
            border-radius: 1rem;
        }

        .header-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* Gaya Kartu Ringkasan dengan Gradient */
        .summary-card {
            color: #fff;
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.15), 0 12px 15px -8px rgba(0, 0, 0, 0.15);
        }

        .summary-card .card-body {
            padding: 1.75rem;
        }

        .card-text-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .card-text-value {
            font-size: 2rem;
        }

        .icon-wrapper {
            font-size: 2.5rem;
            /* opacity: 0.3; */
        }

        .card-income {
            background: linear-gradient(135deg, #28a745, #218838);
        }

        .card-expense {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }

        .card-balance {
            background: linear-gradient(135deg, #007bff, #0069d9);
        }

        /* Gaya Card Aktivitas */
        .card-custom {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .card-header-custom {
            background-color: #fff;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f0f0f0;
        }

        .icon-circle-sm {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.2rem;
        }

        .list-group-item {
            border-bottom: 1px solid #f0f0f0 !important;
            transition: background-color 0.2s ease-in-out;
        }

        .list-group-item:last-child {
            border-bottom: none !important;
        }

        .list-group-item-action:hover {
            background-color: #f8f9fa;
        }
    </style>

</x-layouts.app>
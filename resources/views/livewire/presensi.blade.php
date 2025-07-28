<div>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-11 col-xl-10">
                
                {{-- Pesan Global --}}
                @if (session()->has('message'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                {{-- Header Utama --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-0">Aktivitas Karyawan</h2>
                        <p class="text-muted mb-0">Lakukan presensi dan ajukan cuti melalui halaman ini.</p>
                    </div>
                </div>

                {{-- Konten Utama dalam Satu Kartu --}}
                <div class="card card-custom">
                    <div class="card-header card-header-custom">
                        {{-- Navigasi Tab --}}
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item">
                                <a class="nav-link {{ $tampilanAktif == 'presensi' ? 'active' : '' }}" href="#" wire:click.prevent="pilihTampilan('presensi')">
                                    <i class="bi bi-person-check-fill me-1"></i> Presensi Hari Ini
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $tampilanAktif == 'cuti' ? 'active' : '' }}" href="#" wire:click.prevent="pilihTampilan('cuti')">
                                    <i class="bi bi-calendar2-plus-fill me-1"></i> Pengajuan Cuti
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-4">

                        @if ($tampilanAktif == 'presensi')
                        {{-- =============================================== --}}
                        {{-- ============ KONTEN TAB PRESENSI ============ --}}
                        {{-- =============================================== --}}
                        <div class="row g-4">
                            <div class="col-lg-5">
                                <h5 class="mb-3">Aksi Presensi</h5>
                                <div class="text-center">
                                    @if ($cutiHariIni)
                                        <div class="alert alert-warning">
                                            <h5 class="alert-heading">🚫 Sedang Cuti</h5>
                                            <p class="mb-0">Anda tidak dapat melakukan presensi.</p>
                                        </div>
                                    @elseif (!$presensiHariIni)
                                        <button wire:click="checkin" class="btn btn-primary btn-lg w-100"><i class="bi bi-box-arrow-in-right me-2"></i> Check-In Sekarang</button>
                                    @elseif (!$presensiHariIni->jam_keluar)
                                        <div class="alert alert-success">
                                            <p class="mb-0">✅ Berhasil Check-In pada pukul:</p>
                                            <h4 class="mb-0 fw-bold">{{ \Carbon\Carbon::parse($presensiHariIni->jam_masuk)->setTimezone('Asia/Jakarta')->format('H:i:s') }}</h4>
                                        </div>
                                        <button wire:click="checkout" class="btn btn-success btn-lg w-100 mt-2"><i class="bi bi-box-arrow-left me-2"></i> Check-Out Sekarang</button>
                                    @else
                                        <div class="alert alert-secondary text-center">
                                            <p class="mb-1">Presensi hari ini sudah selesai.</p>
                                            <div>
                                                <span class="badge bg-success-subtle text-success-emphasis">Masuk: {{ \Carbon\Carbon::parse($presensiHariIni->jam_masuk)->setTimezone('Asia/Jakarta')->format('H:i:s') }}</span>
                                                <span class="badge bg-danger-subtle text-danger-emphasis">Keluar: {{ \Carbon\Carbon::parse($presensiHariIni->jam_keluar)->setTimezone('Asia/Jakarta')->format('H:i:s') }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <h5 class="mb-3">📜 Riwayat Presensi</h5>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead><tr><th>Tanggal</th><th>Masuk</th><th>Keluar</th></tr></thead>
                                        <tbody>
                                            @forelse ($riwayatPresensi as $presensi)
                                            <tr class="align-middle">
                                                <td>{{ \Carbon\Carbon::parse($presensi->tanggal)->setTimezone('Asia/Jakarta')->format('d M Y') }}</td>
                                                <td><span class="badge bg-light text-dark">{{ $presensi->jam_masuk ? \Carbon\Carbon::parse($presensi->jam_masuk)->setTimezone('Asia/Jakarta')->format('H:i:s') : '-' }}</span></td>
                                                <td><span class="badge bg-light text-dark">{{ $presensi->jam_keluar ? \Carbon\Carbon::parse($presensi->jam_keluar)->setTimezone('Asia/Jakarta')->format('H:i:s') : '-' }}</span></td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="3" class="text-center py-4">Belum ada riwayat presensi.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        @elseif ($tampilanAktif == 'cuti')
                        {{-- ============================================ --}}
                        {{-- ============ KONTEN TAB CUTI ============ --}}
                        {{-- ============================================ --}}
                        <div class="row g-4">
                            <div class="col-lg-5">
                                <h5 class="mb-3">Form Pengajuan</h5>
                                @if ($presensiHariIni && !$cutiHariIni)
                                    <div class="alert alert-warning">🚫 Anda sudah melakukan presensi hari ini, tidak dapat mengajukan cuti.</div>
                                @else
                                    <form wire:submit="ajukanCuti">
                                        <div class="row g-2">
                                            <div class="col-md-6 mb-2">
                                                <label for="tanggal_mulai" class="form-label">Tgl. Mulai</label>
                                                <input id="tanggal_mulai" type="date" wire:model="tanggal_mulai" class="form-control">
                                                @error('tanggal_mulai') <small class="text-danger">{{ $message }}</small> @enderror
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <label for="tanggal_selesai" class="form-label">Tgl. Selesai</label>
                                                <input id="tanggal_selesai" type="date" wire:model="tanggal_selesai" class="form-control">
                                                @error('tanggal_selesai') <small class="text-danger">{{ $message }}</small> @enderror
                                            </div>
                                            <div class="col-12">
                                                <label for="alasan" class="form-label">Alasan</label>
                                                <textarea id="alasan" wire:model="alasan" class="form-control" placeholder="Jelaskan alasan cuti Anda..."></textarea>
                                                @error('alasan') <small class="text-danger">{{ $message }}</small> @enderror
                                            </div>
                                        </div>
                                        <div class="d-grid mt-3">
                                            <button type="submit" class="btn btn-warning">Ajukan Cuti</button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                            <div class="col-lg-7">
                                <h5 class="mb-3">🗓️ Riwayat Cuti</h5>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead><tr><th>Mulai</th><th>Selesai</th><th>Alasan</th></tr></thead>
                                        <tbody>
                                            @forelse ($riwayatCuti as $cuti)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->setTimezone('Asia/Jakarta')->format('d M Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->setTimezone('Asia/Jakarta')->format('d M Y') }}</td>
                                                <td>{{ $cuti->alasan ?? '-' }}</td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="3" class="text-center py-4">Belum ada riwayat cuti.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
    
    <style>
        .card-custom { border: 1px solid #dee2e6; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .card-header-custom { background-color: #fff; padding: 0; border-bottom: 1px solid #dee2e6; border-radius: 12px 12px 0 0 !important; }
        .nav-tabs { border-bottom: none; }
        .nav-tabs .nav-link { border: none; color: #6c757d; padding: 1rem 1.5rem; font-weight: 500; }
        .nav-tabs .nav-link.active { color: #0d6efd; background-color: #fff; border-bottom: 3px solid #0d6efd; }
        .form-control:focus, .form-select:focus { border-color: #86b7fe; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25); }
        .table th { font-weight: 600; }
    </style>
</div>
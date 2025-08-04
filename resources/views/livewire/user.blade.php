<div>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-11 col-xl-10">

                {{-- Pesan Sukses / Error --}}
                @if (session()->has('pesan'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('pesan') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif


                {{-- Konten Utama dalam Satu Kartu --}}
                <div class="card card-custom">
                    <div class="card-header card-header-custom">
                        {{-- Navigasi Tab (Daftar Aktif / Sampah) --}}
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item">
                                <a class="nav-link {{ !$tampilkanTrash && $pilihanMenu != 'tambah' && $pilihanMenu != 'edit' ? 'active' : '' }}"
                                    href="#" wire:click.prevent="tampilkanMenuTrash(false)">
                                    <i class="bi bi-people-fill me-1"></i> Daftar Aktif
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $pilihanMenu == 'tambah' ? 'active' : '' }}" href="#"
                                    wire:click.prevent="pilihMenu('tambah')">
                                    <i class="bi bi-plus-circle me-1"></i> Tambah Pengguna
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $tampilkanTrash ? 'active' : '' }}" href="#"
                                    wire:click.prevent="tampilkanMenuTrash(true)">
                                    <i class="bi bi-trash3-fill me-1"></i> Tempat Sampah
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-4">

                        @if ($pilihanMenu == 'tambah' || $pilihanMenu == 'edit')
                        {{-- ================= FORM TAMBAH / EDIT ================= --}}
                        <h4 class="mb-4">
                            {{ $pilihanMenu == 'tambah' ? 'Form Tambah Pengguna Baru' : 'Form Edit Pengguna' }}
                        </h4>
                        <form wire:submit="{{ $pilihanMenu == 'tambah' ? 'simpan' : 'simpanEdit' }}">
                            <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="nama" class="form-label">Nama</label>
                                        <input id="nama" type="text" class="form-control" wire:model="nama" />
                                        @error('nama')
                                        <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="nis" class="form-label">{{ __('NIS (Nomor Induk)') }}</label>
                                        <input id="nis" type="number"
                                            class="form-control @error('nis') is-invalid @enderror" wire:model="nis"
                                            required autocomplete="nis">
                                        @error('nis')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input id="email" type="email" class="form-control" wire:model="email" />
                                        @error('email')
                                        <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="password" class="form-label">Password</label>
                                        <input id="password" type="password" class="form-control" wire:model="password" />
                                        @error('password')
                                        <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                        <input id="password_confirmation" type="password" class="form-control" wire:model="password_confirmation" />
                                    </div>

                                    <div class="col-md-6">
                                        <label for="peran" class="form-label">Pegawai</label>
                                        <select id="peran" class="form-select" wire:model='peran'>
                                            <option value="">Pilih Pegawai</option>
                                            <option value="karyawan">Karyawan</option>
                                            <option value="direktur">Pemilik / Direktur</option>
                                            <option value="non-karyawan">Non-Karyawan</option>
                                        </select>
                                        @error('peran')
                                        <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            {{-- Tombol Simpan dan Batal --}}
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="button" wire:click="batal" class="btn btn-secondary">Batal</button>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-save me-1"></i> Simpan
                                </button>
                            </div>
                        </form>
                        @else

                        {{-- ================= KONFIRMASI & TABEL PENGGUNA ================= --}}

                        {{-- PERUBAHAN DI SINI: Ditambahkan pengecekan apakah koleksi tidak kosong --}}
                        @if ($tampilkanTrash && $semuaPengguna->isNotEmpty())

                        {{-- Alert Konfirmasi Hapus Semua --}}
                        @if($konfirmasiHapusSemua)
                        <div class="alert alert-danger p-3 mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="alert-heading mb-1">Konfirmasi Hapus Semua Data!</h5>
                                    <span>Anda yakin ingin <strong>menghapus permanen</strong> semua data di tempat sampah? Aksi ini tidak dapat dibatalkan.</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-light btn-sm" wire:click='batal'>Batal</button>
                                    <button class="btn btn-danger btn-sm fw-bold" wire:click='hapusSemuaPermanen'>Ya, Hapus Semua</button>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Tombol Hapus Semua --}}
                        <div class="d-flex justify-content-end mb-3">
                            <button wire:click="konfirmasiHapusSemuaPermanen" class="btn btn-danger btn-sm">
                                <i class="bi bi-trash3-fill me-1"></i> Hapus Semua
                            </button>
                        </div>
                        @endif

                        @if ($pilihanMenu == 'hapus' || $pilihanMenu == 'restore' || $pilihanMenu == 'hapus_permanen')
                        {{-- Alert Konfirmasi Aksi Individual--}}
                        @php
                        $isRestore = $pilihanMenu == 'restore';
                        $isPermanentDelete = $pilihanMenu == 'hapus_permanen';
                        $alertClass = $isRestore ? 'alert-success' : 'alert-danger';
                        $actionText = $isRestore ? 'Pulihkan' : 'Hapus';
                        $wireAction = $isRestore
                        ? 'restore'
                        : ($isPermanentDelete
                        ? 'hapusPermanen'
                        : 'hapus');
                        @endphp
                        <div class="alert {{ $alertClass }} p-3 mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="alert-heading mb-1">Konfirmasi Aksi</h5>
                                    <span>Yakin ingin
                                        <strong>{{ $isPermanentDelete ? 'menghapus permanen' : strtolower($actionText) }}</strong>
                                        pengguna
                                        <strong>"{{ $penggunaTerpilih->name ?? '...' }}"</strong>?</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-light btn-sm" wire:click='batal'>Batal</button>
                                    <button class="btn {{ $isRestore ? 'btn-success' : 'btn-danger' }} btn-sm"
                                        wire:click='{{ $wireAction }}'>Ya, {{ $actionText }}</button>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Tabel Pengguna --}}
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Nama Pengguna</th>
                                        <th>Email</th>
                                        <th>Peran</th>
                                        @if ($tampilkanTrash)
                                        <th>Dihapus Pada</th>
                                        @endif
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($semuaPengguna as $pengguna)
                                    <tr>
                                        <td class="ps-4">{{ $pengguna->name }}</td>
                                        <td>{{ $pengguna->email }}</td>
                                        <td><span
                                                class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill">{{ ucfirst($pengguna->peran) }}</span>
                                        </td>
                                        @if ($tampilkanTrash)
                                        <td>{{ $pengguna->deleted_at->format('d M Y, H:i') }}</td>
                                        @endif
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                @if ($pengguna->trashed())
                                                <button wire:click="pilihRestore({{ $pengguna->id }})"
                                                    class="btn btn-sm btn-outline-success"
                                                    data-bs-toggle="tooltip" title="Pulihkan">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                                <button
                                                    wire:click="pilihHapusPermanen({{ $pengguna->id }})"
                                                    class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="tooltip" title="Hapus Permanen">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                                @else
                                                <button wire:click="pilihEdit({{ $pengguna->id }})"
                                                    class="btn btn-sm btn-outline-warning"
                                                    data-bs-toggle="tooltip" title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <button wire:click="pilihHapus({{ $pengguna->id }})"
                                                    class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="tooltip" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="{{ $tampilkanTrash ? '5' : '4' }}"
                                            class="text-center py-5">
                                            <i
                                                class="bi {{ $tampilkanTrash ? 'bi-trash3' : 'bi-people' }} fs-1 text-muted"></i>
                                            <h5 class="mt-3">
                                                {{ $tampilkanTrash ? 'Tempat Sampah Kosong' : 'Tidak Ada Pengguna Aktif' }}
                                            </h5>
                                            <p class="text-muted">Data akan muncul di sini.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @endif

                    </div> {{-- end card-body --}}

                    @if (
                    ($pilihanMenu == 'lihat' ||
                    $pilihanMenu == 'hapus' ||
                    $pilihanMenu == 'restore' ||
                    $pilihanMenu == 'hapus_permanen') &&
                    $semuaPengguna->hasPages())
                    <div class="card-footer bg-light">
                        {{ $semuaPengguna->links() }}
                    </div>
                    @endif
                </div> {{-- end card --}}

            </div>
        </div>
    </div>

    <style>
        /* === BAGIAN 1: PALET WARNA & FONT DASAR === */
        :root {
            --app-bg: #f8f9fa;
            --app-card-bg: #ffffff;
            --app-border-color: #e9ecef;
            --app-text-dark: #212529;
            --app-text-muted: #6c757d;
            --app-primary: #0d6efd;
            --app-warning: #ffc107;
            --app-danger: #dc3545;
        }

        body {
            background-color: var(--app-bg);
        }

        /* === BAGIAN 2: CARD UTAMA (MODIFIKASI KECIL DARI KODE ANDA) === */
        .card-custom {
            border: 1px solid var(--app-border-color);
            border-radius: 12px;
            /* Dibuat lebih besar agar lebih modern */
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.07);
            background-color: var(--app-card-bg);
        }

        /* === BAGIAN 3: CUSTOM TABS (PENGGANTI CARD-HEADER) === */
        .custom-tabs-container {
            padding: 8px;
            background-color: #f7f7f7;
            /* Latar abu-abu untuk container tab */
            border-radius: 10px;
            margin-bottom: 2rem;
            /* Jarak dari tab ke tabel */
        }

        .custom-tabs-container .nav-pills .nav-link {
            color: var(--app-text-muted);
            font-weight: 600;
            border-radius: 8px;
            /* Radius untuk setiap tombol tab */
            padding: 10px 20px;
            transition: all 0.2s ease-in-out;
        }

        .custom-tabs-container .nav-pills .nav-link.active {
            background-color: var(--app-card-bg) !important;
            /* Warna putih untuk tab aktif */
            color: var(--app-text-dark) !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            /* Shadow agar "terangkat" */
        }

        /* === BAGIAN 4: TABEL YANG BERSIH (MODIFIKASI DARI KODE ANDA) === */
        .table-custom {
            border: none;
        }

        .table-custom thead th {
            font-weight: 600;
            color: var(--app-text-dark);
            background-color: transparent;
            border-bottom: 2px solid var(--app-border-color);
            /* Garis bawah header lebih tebal */
            padding: 1rem 1.5rem;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }

        .table-custom tbody tr {
            border-bottom: 1px solid #f2f2f2;
            /* Garis antar baris yang sangat tipis */
        }

        .table-custom tbody tr:last-child {
            border-bottom: none;
            /* Hilangkan garis di baris terakhir */
        }

        .table-custom tbody td {
            padding: 1.25rem 1.5rem;
            vertical-align: middle;
            color: var(--app-text-dark);
        }

        /* === BAGIAN 5: TOMBOL AKSI CUSTOM (BAGIAN PALING PENTING) === */
        .btn-action {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.2s ease;
        }

        .btn-action:hover {
            transform: translateY(-2px);
        }

        /* Warna Biru (Lihat Transaksi) */
        .btn-action.btn-action-primary {
            color: var(--app-primary);
            border-color: var(--app-primary);
            background-color: rgba(13, 110, 253, 0.1);
        }

        .btn-action.btn-action-primary:hover {
            background-color: rgba(13, 110, 253, 0.2);
        }

        /* Warna Kuning (Edit) */
        .btn-action.btn-action-warning {
            color: var(--app-warning);
            border-color: var(--app-warning);
            background-color: rgba(255, 193, 7, 0.1);
        }

        .btn-action.btn-action-warning:hover {
            background-color: rgba(255, 193, 7, 0.2);
        }

        /* Warna Merah (Hapus) */
        .btn-action.btn-action-danger {
            color: var(--app-danger);
            border-color: var(--app-danger);
            background-color: rgba(220, 53, 69, 0.1);
        }

        .btn-action.btn-action-danger:hover {
            background-color: rgba(220, 53, 69, 0.2);
        }
    </style>
</div>
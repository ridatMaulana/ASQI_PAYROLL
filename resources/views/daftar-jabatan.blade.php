<div>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">

                <div class="card card-custom">
                    <div class="card-header card-header-custom">
                        <h4 class="mb-0">Manajemen Jabatan</h4>
                    </div>
                    <div class="card-body p-4">

                        {{-- Pesan Sukses --}}
                        @if (session()->has('pesanJabatan'))
                            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                {{ session('pesanJabatan') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Form Tambah Jabatan Baru --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Tambah Jabatan Baru</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                <input type="text" class="form-control" wire:model="namaJabatan"
                                    placeholder="Cth: Manajer, Staff, Supervisor" wire:keydown.enter="simpanJabatan">
                                <button class="btn btn-primary" wire:click="simpanJabatan">
                                    <i class="bi bi-plus-lg me-1"></i> Tambah
                                </button>
                            </div>
                            @error('namaJabatan')
                                <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Pembatas --}}
                        <hr>

                        {{-- Tabel Daftar Jabatan --}}
                        <h5 class="mt-4 mb-3">Daftar Jabatan Saat Ini</h5>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th width="50">No</th>
                                        <th class="ps-3">Nama Jabatan</th>
                                        <th class="text-center" width="120">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- ======================================================= --}}
                                    {{-- --- INILAH BAGIAN YANG DIBENERIN DENGAN LOGIKA EDIT --- --}}
                                    {{-- ======================================================= --}}
                                    @foreach ($jabatans as $jabatan)
                                        <tr class="align-middle">
                                            <td>{{ $loop->iteration }}</td>

                                            @if ($editingJabatanId === $jabatan->id)
                                                {{-- TAMPILAN SAAT BARIS INI SEDANG DI-EDIT --}}
                                                <td class="ps-3 py-2">
                                                    <input type="text" class="form-control form-control-sm"
                                                        wire:model="editingJabatanNama"
                                                        wire:keydown.enter.prevent="updateJabatan"
                                                        wire:keydown.escape.prevent="batalEditJabatan">
                                                    @error('editingJabatanNama')
                                                        <span class="text-danger small">{{ $message }}</span>
                                                    @enderror
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-2">
                                                        {{-- Tombol untuk SIMPAN perubahan --}}
                                                        <button class="btn btn-sm btn-outline-success"
                                                            wire:click="updateJabatan" data-bs-toggle="tooltip"
                                                            title="Simpan">
                                                            <i class="bi bi-check-lg"></i>
                                                        </button>
                                                        {{-- Tombol untuk BATAL edit --}}
                                                        <button class="btn btn-sm btn-outline-secondary"
                                                            wire:click="batalEditJabatan" data-bs-toggle="tooltip"
                                                            title="Batal">
                                                            <i class="bi bi-x-lg"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            @else
                                                {{-- TAMPILAN NORMAL (TIDAK SEDANG DI-EDIT) --}}
                                                <td class="ps-3">{{ $jabatan->nama }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-2">
                                                        {{-- Tombol untuk MULAI edit --}}
                                                        <button class="btn btn-sm btn-outline-warning"
                                                            wire:click="editJabatan({{ $jabatan->id }})"
                                                            data-bs-toggle="tooltip" title="Edit">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>
                                                        {{-- Tombol untuk HAPUS --}}
                                                        <button class="btn btn-sm btn-outline-danger"
                                                            wire:click="hapusJabatan({{ $jabatan->id }})"
                                                            wire:confirm="Yakin ingin menghapus jabatan ini?"
                                                            data-bs-toggle="tooltip" title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- CSS Kustom --}}
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

    {{-- Script untuk Tooltip Bootstrap --}}
    <script>
        document.addEventListener('livewire:navigated', () => {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                var oldTooltip = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
                if (oldTooltip) {
                    oldTooltip.dispose();
                }
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</div>

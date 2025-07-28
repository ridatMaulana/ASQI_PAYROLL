<div>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                {{-- Pesan Notifikasi --}}
                @if (session()->has('message'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- HEADER HALAMAN --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-0">Manajemen Pajak</h2>
                        <p class="text-muted mb-0">Kelola aturan dan persentase pajak yang berlaku di perusahaan.</p>
                    </div>
                    <div>
                        <a href="{{ route('gaji') }}" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Penggajian
                        </a>
                    </div>
                </div>

                {{-- KONTEN UTAMA DENGAN TABS --}}
                <div class="card card-custom">
                    <div class="card-header card-header-custom">
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item">
                                {{-- Tab ini aktif jika $pilihanMenu adalah 'lihat' --}}
                                <a class="nav-link {{ $pilihanMenu == 'lihat' ? 'active' : '' }}" href="#"
                                    wire:click.prevent="batal">
                                    <i class="bi bi-journals me-1"></i> Daftar Pajak
                                </a>
                            </li>
                            <li class="nav-item">
                                {{-- Tab ini aktif jika $pilihanMenu adalah 'tambah' atau 'edit' --}}
                                <a class="nav-link {{ in_array($pilihanMenu, ['tambah', 'edit']) ? 'active' : '' }}"
                                    href="#" wire:click.prevent="showCreateForm">
                                    <i class="bi bi-journal-plus me-1"></i>
                                    {{ $pajak_id ? 'Edit Aturan' : 'Tambah Aturan' }}
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-4">

                        {{-- Tampilkan form jika $pilihanMenu adalah 'tambah' atau 'edit' --}}
                        @if (in_array($pilihanMenu, ['tambah', 'edit']))
                            {{-- FORM UNTUK CREATE / EDIT --}}
                            <h4 class="mb-4">
                                {{ $pajak_id ? 'Form Edit Aturan Pajak' : 'Form Tambah Aturan Pajak Baru' }}</h4>
                            <form wire:submit.prevent="store">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="nama_pajak" class="form-label fw-bold">Nama Pajak</label>
                                        <input type="text" id="nama_pajak" wire:model="nama_pajak"
                                            class="form-control @error('nama_pajak') is-invalid @enderror"
                                            placeholder="Contoh: PPh 21 Golongan I">
                                        @error('nama_pajak')
                                            <span class="text-danger small mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="persentase" class="form-label fw-bold">Persentase (%)</label>
                                        <input type="number" step="0.01" id="persentase" wire:model="persentase"
                                            class="form-control @error('persentase') is-invalid @enderror"
                                            placeholder="Contoh: 5 atau 15.50">
                                        @error('persentase')
                                            <span class="text-danger small mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="deskripsi" class="form-label fw-bold">Deskripsi (Opsional)</label>
                                        <textarea id="deskripsi" wire:model="deskripsi" class="form-control" rows="3"
                                            placeholder="Jelaskan secara singkat mengenai aturan pajak ini..."></textarea>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <button type="button" wire:click="batal" class="btn btn-secondary">Batal</button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-1"></i>
                                        {{ $pajak_id ? 'Update Data' : 'Simpan Data' }}
                                    </button>
                                </div>
                            </form>
                        @else
                            {{-- TABEL UNTUK MENAMPILKAN DATA PAJAK --}}
                            <div class="mb-3">
                                <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                                    placeholder="Cari nama pajak...">
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Nama Pajak</th>
                                            <th>Persentase</th>
                                            <th>Deskripsi</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($pajaks as $index => $pajak)
                                            <tr wire:key="pajak-{{ $pajak->id }}">
                                                <td>{{ $pajaks->firstItem() + $index }}</td>
                                                <td>{{ $pajak->nama_pajak }}</td>
                                                <td class="fw-bold">{{ rtrim(rtrim($pajak->persentase, '0'), '.') }}%
                                                </td>
                                                <td>{{ $pajak->deskripsi ?? '-' }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex gap-2 justify-content-center">
                                                        <button wire:click="edit({{ $pajak->id }})"
                                                            class="btn btn-sm btn-outline-warning"
                                                            data-bs-toggle="tooltip" title="Edit">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>
                                                        <button wire:click="delete({{ $pajak->id }})"
                                                            wire:confirm="Yakin ingin menghapus aturan pajak ini?"
                                                            class="btn btn-sm btn-outline-danger"
                                                            data-bs-toggle="tooltip" title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5">
                                                    <i class="bi bi-x-circle fs-1 text-muted"></i>
                                                    <h5 class="mt-3">Data Tidak Ditemukan</h5>
                                                    <p class="text-muted">Tidak ada data pajak yang cocok dengan
                                                        pencarian "{{ $search }}".</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                {{ $pajaks->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Style kustom untuk card dan tabs --}}
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

    {{-- Script untuk re-inisialisasi tooltip Bootstrap setelah update Livewire --}}
    <script>
        document.addEventListener('livewire:navigated', () => {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                var oldTooltip = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
                if (oldTooltip) {
                    oldTooltip.dispose();
                }
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</div>

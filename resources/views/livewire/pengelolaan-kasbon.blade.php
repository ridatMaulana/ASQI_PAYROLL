<div>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-11 col-xl-10">

                @if (session()->has('message'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-0">Manajemen Kasbon</h2>
                        <p class="text-muted mb-0">Kelola pengajuan dan pencairan kasbon karyawan.</p>
                    </div>
                    <div>
                        <a href="{{ route('gaji') }}" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Penggajian
                        </a>
                    </div>
                </div>

                <div class="card card-custom">
                    <div class="card-header card-header-custom">
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item">
                                <a class="nav-link {{ $pilihanMenu == 'lihat' ? 'active' : '' }}" href="#"
                                    wire:click.prevent="pilihMenu('lihat')">
                                    <i class="bi bi-journals me-1"></i> Daftar Kasbon
                                </a>
                            </li>
                            @if (auth()->user()->peran === 'admin')
                                <li class="nav-item">
                                    <a class="nav-link {{ $pilihanMenu == 'tambah' || $pilihanMenu == 'edit' ? 'active' : '' }}"
                                        href="#" wire:click.prevent="pilihMenu('tambah')">
                                        <i class="bi bi-journal-plus me-1"></i>
                                        {{ $editingKasbonId ? 'Edit Kasbon' : 'Ajukan Kasbon' }}
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <div class="card-body p-4">

                        @if (($pilihanMenu == 'tambah' || $pilihanMenu == 'edit') && auth()->user()->peran === 'admin')
                            {{-- FORM TAMBAH/EDIT KASBON --}}
                            <h4 class="mb-4">
                                {{ $editingKasbonId ? 'Form Edit Kasbon' : 'Form Pengajuan Kasbon Baru' }}</h4>
                            <form wire:submit="{{ $editingKasbonId ? 'updateKasbon' : 'createKasbon' }}">
                                <div class="row g-3">
                                    <!-- 1. Pilih Karyawan -->
                                    <div class="col-12">
                                        <label for="karyawan_search" class="form-label fw-bold">Pilih Karyawan</label>

                                        @if ($editingKasbonId)
                                            {{-- Saat mode edit, nama ditampilkan dan tidak bisa diubah --}}
                                            <input id="karyawan_search" type="text" class="form-control"
                                                wire:model="karyawanSearch" disabled readonly>
                                        @else
                                            {{-- Saat mode tambah, gunakan input dengan fitur pencarian --}}
                                            <div class="position-relative">
                                                <input id="karyawan_search" type="text" class="form-control"
                                                    placeholder="Ketik untuk mencari nama karyawan..."
                                                    wire:model.live.debounce.300ms="karyawanSearch"
                                                    {{ $user_id ? 'readonly' : '' }} /> {{-- Input menjadi readonly setelah dipilih --}}

                                                {{-- Tampilkan hasil pencarian --}}
                                                @if (!empty($karyawanSearch) && !$user_id && $hasilPencarianKaryawan->isNotEmpty())
                                                    <div class="list-group position-absolute w-100"
                                                        style="z-index: 1000;">
                                                        @foreach ($hasilPencarianKaryawan as $user)
                                                            <a href="#"
                                                                class="list-group-item list-group-item-action"
                                                                wire:click.prevent="pilihKaryawan({{ $user->id }})">
                                                                {{ $user->name }} <!-- Hanya tampilkan nama -->
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                    {{-- Tampilkan pesan jika tidak ada hasil --}}
                                                @elseif(!empty($karyawanSearch) && !$user_id && $hasilPencarianKaryawan->isEmpty())
                                                    <div class="list-group position-absolute w-100"
                                                        style="z-index: 1000;">
                                                        <span class="list-group-item">Karyawan tidak ditemukan atau
                                                            sudah memiliki kasbon aktif.</span>
                                                    </div>
                                                @endif
                                            </div>
                                            @error('user_id')
                                                {{-- Validasi tetap pada ID yang terpilih --}}
                                                <span class="text-danger small mt-1">{{ $message }}</span>
                                            @enderror
                                        @endif
                                    </div>

                                    <!-- 2. Jenis Kasbon -->
                                    <div class="col-md-6">
                                        <label for="jenis_kasbon" class="form-label fw-bold">Jenis Kasbon</label>
                                        <select id="jenis_kasbon" wire:model="jenis_kasbon" class="form-select">
                                            <option value="">-- Pilih Jenis --</option>
                                            <option value="tunai">Tunai</option>
                                            <option value="barang">Barang</option>
                                            <option value="kesehatan">Kesehatan</option>
                                            <option value="lainnya">Lainnya</option>
                                        </select>
                                        @error('jenis_kasbon')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- 3. Status -->
                                    <div class="col-md-6">
                                        <label for="status" class="form-label fw-bold">Status</label>
                                        <select id="status" wire:model="status" class="form-select">
                                            <option value="diajukan">Diajukan</option>
                                            <option value="disetujui">Disetujui</option>
                                            <option value="dicairkan">Dicairkan</option>
                                            <option value="ditolak">Ditolak</option>
                                        </select>
                                        @error('status')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- 4. Total Kasbon -->
                                    <div class="col-md-6">
                                        <label for="total_kasbon" class="form-label fw-bold">Total Kasbon (Rp)</label>
                                        <input id="total_kasbon" type="number" wire:model="total_kasbon"
                                            class="form-control" min="1">
                                        @error('total_kasbon')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Ubah bayar perbulan menjadi readonly -->
                                    <div class="col-md-6">
                                        <label for="bayar_perbulan" class="form-label fw-bold">Bayar Perbulan
                                            (Rp)</label>
                                        <input id="bayar_perbulan" type="text" wire:model="bayar_perbulan"
                                            class="form-control" readonly placeholder="Tidak perlu diisi">
                                        @error('bayar_perbulan')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="periode_mulai" class="form-label fw-bold">Periode Mulai</label>
                                        <input id="periode_mulai" type="date" wire:model.live="periode_mulai"
                                            class="form-control">
                                        @error('periode_mulai')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="periode_selesai" class="form-label fw-bold">Periode
                                            Selesai</label>
                                        <input id="periode_selesai" type="date" wire:model.live="periode_selesai"
                                            class="form-control">
                                        @error('periode_selesai')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>



                                    <!-- 7. Keterangan -->
                                    <div class="col-12">
                                        <label for="keterangan" class="form-label fw-bold">Keterangan</label>
                                        <textarea id="keterangan" wire:model="keterangan" class="form-control" rows="3"></textarea>
                                        @error('keterangan')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <button type="button" wire:click="batal"
                                        class="btn btn-secondary">Batal</button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-save me-1"></i> Simpan
                                    </button>
                                </div>
                            </form>
                        @else
                            {{-- TABEL DAFTAR KASBON --}}
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="ps-3">No</th>
                                            <th>Nama Karyawan</th>
                                            <th>Jenis Kasbon</th>
                                            <th>Total Kasbon</th>
                                            <th>Status</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($kasbons as $index => $kasbon)
                                            <tr class="align-middle" wire:key="kasbon-{{ $kasbon->id }}"
                                                class="align-middle">
                                                <td class="ps-3">{{ $index + 1 }}</td>
                                                <td>{{ $kasbon->user->name }}</td>
                                                <td>{{ ucfirst($kasbon->jenis_kasbon) }}</td>
                                                <td>Rp {{ number_format($kasbon->total_kasbon, 2) }}</td>
                                                <td>
                                                    @php
                                                        $statusClass = [
                                                            'diajukan' => 'bg-warning-subtle text-warning-emphasis',
                                                            'disetujui' => 'bg-primary-subtle text-primary-emphasis',
                                                            'dicairkan' => 'bg-success-subtle text-success-emphasis',
                                                            'ditolak' => 'bg-danger-subtle text-danger-emphasis',
                                                        ][$kasbon->status];
                                                    @endphp
                                                    <span class="badge rounded-pill {{ $statusClass }}">
                                                        {{ ucfirst($kasbon->status) }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex gap-2 justify-content-center">
                                                        <button wire:click="showKasbonDetail({{ $kasbon->id }})"
                                                            class="btn btn-sm btn-outline-secondary"
                                                            data-bs-toggle="tooltip" title="Lihat Detail">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        @if (auth()->user()->peran === 'admin')
                                                            <button wire:click="editKasbon({{ $kasbon->id }})"
                                                                class="btn btn-sm btn-outline-warning"
                                                                data-bs-toggle="tooltip" title="Edit">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </button>
                                                            <button wire:click="deleteKasbon({{ $kasbon->id }})"
                                                                class="btn btn-sm btn-outline-danger"
                                                                data-bs-toggle="tooltip" title="Hapus"
                                                                wire:confirm="Yakin ingin menghapus data kasbon ini?">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-5">
                                                    <i class="bi bi-journal-x fs-1 text-muted"></i>
                                                    <h5 class="mt-3">Belum Ada Data Kasbon</h5>
                                                    <p class="text-muted">Silakan ajukan kasbon baru jika Anda admin.
                                                    </p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL KASBON --}}
    <div wire:ignore.self class="modal fade" id="kasbonDetailModal" tabindex="-1"
        aria-labelledby="kasbonDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="kasbonDetailModalLabel">
                        <i class="bi bi-receipt me-2"></i> Detail Kasbon
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if (!empty($detailForModal))
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Karyawan</label>
                            <p class="form-control-plaintext">{{ $detailForModal['nama_karyawan'] ?? '-' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Jenis Kasbon</label>
                            <p class="form-control-plaintext">{{ $detailForModal['jenis_kasbon'] ?? '-' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <p class="form-control-plaintext">
                                @php
                                    $statusClass = [
                                        'diajukan' => 'bg-warning-subtle text-warning-emphasis',
                                        'disetujui' => 'bg-primary-subtle text-primary-emphasis',
                                        'dicairkan' => 'bg-success-subtle text-success-emphasis',
                                        'ditolak' => 'bg-danger-subtle text-danger-emphasis',
                                    ][strtolower($detailForModal['status'] ?? '')];
                                @endphp
                                <span class="badge rounded-pill {{ $statusClass }}">
                                    {{ ucfirst($detailForModal['status'] ?? '-') }}
                                </span>
                            </p>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Total Kasbon</label>
                            <p class="form-control-plaintext">Rp {{ $detailForModal['total_kasbon'] ?? '0' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Bayar Perbulan</label>
                            <p class="form-control-plaintext">Rp {{ $detailForModal['bayar_perbulan'] ?? '0' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Periode Selesai</label>
                            <p class="form-control-plaintext">{{ $detailForModal['periode_selesai'] ?? '-' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tanggal Pengajuan</label>
                            <p class="form-control-plaintext">{{ $detailForModal['tanggal_pengajuan'] ?? '-' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Keterangan</label>
                            <p class="form-control-plaintext">{{ $detailForModal['keterangan'] ?? '-' }}</p>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Style --}}
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

    {{-- Script untuk mengontrol Modal dan Tooltip --}}
    <script>
        document.addEventListener('livewire:navigated', () => {
            const kasbonModalElement = document.getElementById('kasbonDetailModal');
            if (!kasbonModalElement) return;

            const kasbonModal = new bootstrap.Modal(kasbonModalElement);

            // Inisialisasi Tooltip
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                var oldTooltip = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
                if (oldTooltip) oldTooltip.dispose();
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Listener untuk event dari Livewire untuk membuka modal
            window.addEventListener('open-kasbon-detail-modal', event => {
                kasbonModal.show();
            });
        });
    </script>
</div>

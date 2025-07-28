<div>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-11 col-xl-10">

                {{-- Pesan Global --}}
                @if (session()->has('pesan'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('pesan') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Header Utama --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-0">Pengelolaan BPJS</h2>
                        <p class="text-muted mb-0">Kelola semua data BPJS yang terdaftar untuk karyawan.</p>
                    </div>
                    <div>
                        <a href="{{ route('gaji') }}" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Penggajian
                        </a>
                    </div>
                </div>

                {{-- Konten Utama dalam Satu Kartu --}}
                <div class="card card-custom">
                    <div class="card-header card-header-custom">
                        {{-- Navigasi Tab --}}
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item">
                                <a class="nav-link {{ $pilihanMenu == 'lihat' ? 'active' : '' }}" href="#"
                                    wire:click.prevent="pilihMenu('lihat')">
                                    <i class="bi bi-list-ul me-1"></i> Daftar BPJS
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $pilihanMenu == 'tambah' || $pilihanMenu == 'edit' ? 'active' : '' }}"
                                    href="#" wire:click.prevent="pilihMenu('tambah')">
                                    <i
                                        class="bi {{ $pilihanMenu == 'edit' ? 'bi-pencil-fill' : 'bi-plus-circle-fill' }} me-1"></i>
                                    {{ $pilihanMenu == 'edit' ? 'Edit Data' : 'Tambah Data Baru' }}
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-4">

                        @if ($pilihanMenu == 'tambah' || $pilihanMenu == 'edit')
                            {{-- ================================================= --}}
                            {{-- ============ KONTEN TAB FORMULIR ============ --}}
                            {{-- ================================================= --}}

                            {{-- PERBAIKAN: wire:submit sekarang selalu menunjuk ke "simpan" --}}
                            <form wire:submit="simpan">
                                <h5 class="mb-4">
                                    {{ $pilihanMenu == 'tambah' ? 'Formulir Tambah Data BPJS' : 'Formulir Edit Data BPJS' }}
                                </h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="karyawan_search" class="form-label">Karyawan</label>

                                        @if ($pilihanMenu == 'edit')
                                            {{-- Saat mode edit, nama karyawan ditampilkan dan tidak bisa diubah --}}
                                            <input id="karyawan_search" type="text" class="form-control"
                                                value="{{ $bpjsTerpilih?->user->name ?? '' }}" disabled readonly>
                                        @else
                                            {{-- Saat mode tambah, gunakan input dengan fitur pencarian --}}
                                            <div class="position-relative">
                                                <input id="karyawan_search" type="text" class="form-control"
                                                    placeholder="Ketik untuk mencari nama karyawan..."
                                                    wire:model.live.debounce.300ms="karyawanSearch"
                                                    {{ $karyawanTerpilihId ? 'readonly' : '' }} />
                                                {{-- Input menjadi readonly setelah dipilih --}}

                                                {{-- Tampilkan hasil pencarian jika ada input dan belum ada karyawan yang dipilih --}}
                                                @if (!empty($karyawanSearch) && !$karyawanTerpilihId && $hasilPencarianKaryawan->isNotEmpty())
                                                    <div class="list-group position-absolute w-100"
                                                        style="z-index: 1000;">
                                                        @foreach ($hasilPencarianKaryawan as $user)
                                                            <a href="#"
                                                                class="list-group-item list-group-item-action"
                                                                wire:click.prevent="pilihKaryawan({{ $user->id }})">
                                                                {{ $user->name }} (NIS: {{ $user->nis }})
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                    {{-- Tampilkan pesan jika tidak ada hasil --}}
                                                @elseif(!empty($karyawanSearch) && !$karyawanTerpilihId && $hasilPencarianKaryawan->isEmpty())
                                                    <div class="list-group position-absolute w-100"
                                                        style="z-index: 1000;">
                                                        <span class="list-group-item">Karyawan tidak ditemukan atau
                                                            sudah terdaftar.</span>
                                                    </div>
                                                @endif
                                            </div>
                                            @error('karyawanTerpilihId')
                                                {{-- Validasi tetap pada ID yang terpilih --}}
                                                <span class="text-danger small mt-1">{{ $message }}</span>
                                            @enderror
                                        @endif
                                    </div>

                                    <div class="col-md-6">
                                        <label for="jenis" class="form-label">Jenis BPJS</label>
                                        {{-- [PERBAIKAN 1] Kembalikan id dan wire:model ke "jenis" --}}
                                        {{-- Tambahkan juga wire:loading.attr="disabled" --}}
                                        <select id="jenis" class="form-select" wire:model.live="jenis"
                                            wire:loading.attr="disabled">
                                            <option value="">-- Pilih Jenis --</option>
                                            <option value="Kesehatan">BPJS Kesehatan</option>
                                            <option value="Ketenagakerjaan">BPJS Ketenagakerjaan</option>
                                        </select>
                                        @error('jenis')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="no_bpjs" class="form-label">Nomor BPJS</label>
                                        <input id="no_bpjs" type="number" class="form-control" wire:model="no_bpjs"
                                            placeholder="">
                                        @error('no_bpjs')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="persentase_potongan" class="form-label">Persentase Potongan
                                            (%)</label>
                                        <input id="persentase_potongan" type="number" step="0.01"
                                            class="form-control" wire:model="persentase_potongan"
                                            placeholder="e.g. 1.00" min="0" max="100">
                                        @error('persentase_potongan')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="status" class="form-label">Status Keanggotaan</label>
                                        <select id="status" class="form-select" wire:model="status">
                                            <option value="">-- Pilih Status --</option>
                                            <option value="Aktif">Aktif</option>
                                            <option value="Non-Aktif">Non-Aktif</option>
                                        </select>
                                        @error('status')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Tombol Aksi --}}
                                <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                                    <button type="button" wire:click="batal"
                                        class="btn btn-secondary me-2">Batal</button>
                                    <button type="submit" class="btn btn-primary fw-bold px-4">
                                        {{-- PERBAIKAN: wire:target sekarang selalu menunjuk ke "simpan" --}}
                                        <span wire:loading.remove wire:target="simpan">
                                            <i class="bi bi-save-fill me-1"></i> Simpan Data
                                        </span>
                                        <span wire:loading wire:target="simpan">
                                            <span class="spinner-border spinner-border-sm"></span> Menyimpan...
                                        </span>
                                    </button>
                                </div>
                            </form>
                        @elseif($pilihanMenu == 'lihat')
                            {{-- =============================================== --}}
                            {{-- ============ KONTEN TAB DAFTAR BPJS ============ --}}
                            {{-- =============================================== --}}
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Nama Karyawan</th>
                                            <th>NIS</th>
                                            <th>Jenis</th>
                                            <th>Nomor BPJS</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($dataBPJS as $index => $bpjs)
                                            <tr class="align-middle" wire:key="bpjs-{{ $bpjs->id }}">
                                                <td>{{ $dataBPJS->firstItem() + $index }}</td>
                                                <td>{{ $bpjs->user->name ?? 'N/A' }}</td>
                                                <td>{{ $bpjs->user->nis ?? 'N/A' }}</td>
                                                <td><span
                                                        class="badge bg-info-subtle text-info-emphasis">{{ $bpjs->jenis }}</span>
                                                </td>
                                                <td>{{ $bpjs->no_bpjs }}</td>
                                                <td class="text-center"><span
                                                        class="badge rounded-pill {{ $bpjs->status == 'Aktif' ? 'bg-success' : 'bg-secondary' }}">{{ $bpjs->status }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-outline-warning"
                                                        wire:click="pilihEdit({{ $bpjs->id }})"
                                                        data-bs-toggle="tooltip" title="Edit"><i
                                                            class="bi bi-pencil-square"></i></button>
                                                    <button class="btn btn-sm btn-outline-danger"
                                                        wire:click="pilihHapus({{ $bpjs->id }})"
                                                        data-bs-toggle="tooltip" title="Hapus"><i
                                                            class="bi bi-trash"></i></button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-5">
                                                    <i class="bi bi-shield-shaded fs-1 text-muted"></i>
                                                    <h5 class="mt-3">Data BPJS Masih Kosong</h5>
                                                    <p class="text-muted">Klik tab "Tambah Data Baru" untuk memulai.
                                                    </p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if ($dataBPJS->hasPages())
                                <div class="pt-4">{{ $dataBPJS->links() }}</div>
                            @endif
                        @elseif($pilihanMenu == 'hapus')
                            {{-- ================================================ --}}
                            {{-- ============ KONTEN KONFIRMASI HAPUS ============ --}}
                            {{-- ================================================ --}}
                            <div class="text-center p-4">
                                <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 4rem;"></i>
                                <h4 class="mt-3">Konfirmasi Penghapusan</h4>
                                <p class="text-muted">
                                    Anda yakin ingin menghapus data BPJS untuk karyawan:<br>
                                    {{-- PERBAIKAN: Optional chaining untuk keamanan --}}
                                    <strong
                                        class="fs-5 d-block mt-2">{{ $bpjsTerpilih?->user->name ?? 'Data Tidak Ditemukan' }}</strong>
                                    (Nomor: {{ $bpjsTerpilih?->no_bpjs ?? 'N/A' }})
                                </p>
                                <p class="text-danger mt-3"><small>Tindakan ini tidak dapat dibatalkan.</small></p>
                                <div class="d-flex justify-content-center gap-2 mt-4">
                                    <button class="btn btn-secondary px-4" wire:click='batal'>Batal</button>
                                    <button class="btn btn-danger px-4" wire:click='hapus'>
                                        <span wire:loading.remove wire:target="hapus">Ya, Hapus</span>
                                        <span wire:loading wire:target="hapus">Menghapus...</span>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CSS Kustom dengan gaya yang konsisten --}}
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

    @push('scripts')
        <script>
            // Inisialisasi semua tooltip di halaman
            document.addEventListener('livewire:navigated', () => {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });
        </script>
    @endpush
</div>

<div>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-11 col-xl-10">

                @if (session()->has('pesan'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('pesan') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-0">Data Siswa / Magang</h2>
                        <p class="text-muted mb-0">Kelola semua data siswa atau peserta magang.</p>
                    </div>
                </div>

                <div class="card card-custom">
                    <div class="card-header card-header-custom">
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item">
                                <a class="nav-link {{ $pilihanMenu == 'lihat' ? 'active' : '' }}" href="#"
                                    wire:click.prevent="pilihMenu('lihat')">
                                    <i class="bi bi-person-lines-fill me-1"></i> Semua Siswa
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $pilihanMenu == 'tambah' || $pilihanMenu == 'edit' ? 'active' : '' }}"
                                    href="#" wire:click.prevent="pilihMenu('tambah')">
                                    <i class="bi bi-person-plus-fill me-1"></i>
                                    {{ $pilihanMenu == 'edit' ? 'Edit Data' : 'Tambah Data' }}
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-4">

                        @if ($pilihanMenu == 'tambah' || $pilihanMenu == 'edit')
                            <h4 class="mb-4">
                                {{ $pilihanMenu == 'tambah' ? 'Form Tambah Siswa/Magang Baru' : 'Form Edit Data' }}</h4>
                            <form wire:submit="{{ $pilihanMenu == 'tambah' ? 'simpan' : 'simpanEdit' }}">
                                <div class="row g-3">
                                    <!-- Ganti div lama untuk 'Nama Mentee' dengan yang ini -->
                                    <div class="col-md-6">
                                        <label for="nama_mentee" class="form-label">Nama Mentee</label>

                                        @if ($pilihanMenu == 'edit')
                                            {{-- Saat mode edit, nama hanya ditampilkan dan tidak bisa diubah --}}
                                            <input id="nama_mentee" type="text" class="form-control"
                                                value="{{ $siswaTerpilih->user->name ?? ($siswaTerpilih->nama ?? '') }}"
                                                disabled readonly />
                                        @else
                                            {{-- Saat mode tambah, gunakan input dengan fitur pencarian --}}
                                            <div class="position-relative">
                                                <input id="nama_mentee" type="text" class="form-control"
                                                    placeholder="Ketik untuk mencari nama siswa/magang..."
                                                    wire:model.live.debounce.300ms="namaSearch" {{-- Terhubung ke properti pencarian --}}
                                                    {{ $userTerpilihId ? 'readonly' : '' }} /> {{-- Input menjadi readonly setelah dipilih --}}

                                                {{-- Tampilkan hasil pencarian jika ada input dan belum ada yang dipilih --}}
                                                @if (!empty($namaSearch) && !$userTerpilihId && $usersUntukDipilih->isNotEmpty())
                                                    <div class="list-group position-absolute w-100"
                                                        style="z-index: 1000;">
                                                        @foreach ($usersUntukDipilih as $user)
                                                            <a href="#"
                                                                class="list-group-item list-group-item-action"
                                                                wire:click.prevent="pilihUser({{ $user->id }})">
                                                                {{ $user->name }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                    {{-- Tampilkan pesan jika tidak ada hasil --}}
                                                @elseif(!empty($namaSearch) && !$userTerpilihId && $usersUntukDipilih->isEmpty())
                                                    <div class="list-group position-absolute w-100"
                                                        style="z-index: 1000;">
                                                        <span class="list-group-item">Siswa tidak ditemukan.</span>
                                                    </div>
                                                @endif
                                            </div>
                                            @error('userTerpilihId')
                                                {{-- Validasi tetap pada ID yang terpilih --}}
                                                <span class="text-danger small mt-1">{{ $message }}</span>
                                            @enderror
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <label for="nis" class="form-label">NIS (Nomor Induk)</label>
                                        <input id="nis" type="text" class="form-control" wire:model="nis"
                                            readonly />
                                        @error('nis')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="pendidikan" class="form-label">Asal Jenjang Pendidikan</label>
                                        <input id="pendidikan" type="text" class="form-control"
                                            wire:model="pendidikan"
                                            placeholder="Cth: SMK Telkom, Universitas Gadjah Mada" />
                                        @error('pendidikan')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="alasan" class="form-label">Alasan Kegiatan</label>
                                        <textarea id="alasan" class="form-control" wire:model="alasan" rows="3"
                                            placeholder="Cth: Program Magang, Praktik Kerja Lapangan, Penelitian"></textarea>
                                        @error('alasan')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                        <input id="tanggal_mulai" type="date" class="form-control"
                                            wire:model="tanggal_mulai" />
                                        @error('tanggal_mulai')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                        <input id="tanggal_selesai" type="date" class="form-control"
                                            wire:model="tanggal_selesai" />
                                        @error('tanggal_selesai')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <button type="button" wire:click="batal" class="btn btn-secondary">Batal</button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-save me-1"></i> Simpan
                                    </button>
                                </div>
                            </form>
                        @else
                            @if ($pilihanMenu == 'hapus')
                                <div class="alert alert-danger p-3 mb-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="alert-heading mb-1">Konfirmasi Hapus</h5>
                                            <span>Anda yakin ingin menghapus data
                                                <strong>"{{ $siswaTerpilih->nama ?? '...' }}"</strong>?</span>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-light btn-sm" wire:click='batal'>Batal</button>
                                            <button class="btn btn-danger btn-sm" wire:click='hapus'>Ya,
                                                Hapus</button>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th class="ps-3">Nama</th>
                                            <th>Pendidikan</th>
                                            <th>Periode Selesai</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($semuaSiswa as $index => $siswa)
                                            <tr
                                                class="align-middle {{ !$siswa->is_aktif ? 'table-secondary text-muted' : '' }}">
                                                <td>{{ $index + 1 }}</td>
                                                <td class="ps-3">{{ $siswa->nama }}</td>
                                                <td>{{ $siswa->pendidikan }}</td>
                                                <td>{{ $siswa->tanggal_selesai->format('d M Y') }}</td>
                                                <td class="text-center">
                                                    @if ($siswa->is_aktif)
                                                        <span class="badge bg-success">Aktif</span>
                                                    @else
                                                        <span class="badge bg-danger">Non-Aktif</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-2">
                                                        @if (!$siswa->is_aktif)
                                                            <button class="btn btn-sm btn-outline-success"
                                                                wire:click="bukaModalPerpanjang({{ $siswa->id }})"
                                                                data-bs-toggle="tooltip" title="Perpanjang">
                                                                <i class="bi bi-calendar-plus"></i>
                                                            </button>
                                                        @endif
                                                        <button class="btn btn-sm btn-outline-warning"
                                                            wire:click="pilihEdit({{ $siswa->id }})"
                                                            data-bs-toggle="tooltip" title="Edit">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger"
                                                            wire:click="pilihHapus({{ $siswa->id }})"
                                                            data-bs-toggle="tooltip" title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-5">
                                                    <i class="bi bi-journal-x fs-1 text-muted"></i>
                                                    <h5 class="mt-3">Tidak Ada Data Siswa/Magang</h5>
                                                    <p class="text-muted">Klik tombol "Tambah Data" untuk memulai.</p>
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

    @if ($showPerpanjangModal)
        <div class="modal fade show" tabindex="-1" style="display: block; background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Perpanjang Masa Magang</h5>
                        <button type="button" class="btn-close" wire:click="tutupModalPerpanjang"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Perpanjang masa magang untuk <strong>{{ $siswaTerpilih->nama ?? '' }}</strong>.</p>
                        <div class="mb-3">
                            <label for="tanggal_selesai_baru" class="form-label">Tanggal Selesai Baru</label>
                            <input id="tanggal_selesai_baru" type="date" class="form-control"
                                wire:model="tanggal_selesai_baru">
                            @error('tanggal_selesai_baru')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            wire:click="tutupModalPerpanjang">Batal</button>
                        <button type="button" class="btn btn-primary" wire:click="perpanjangMasaMagang">
                            <span wire:loading.remove wire:target="perpanjangMasaMagang">
                                Simpan Perpanjangan
                            </span>
                            <span wire:loading wire:target="perpanjangMasaMagang">
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

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

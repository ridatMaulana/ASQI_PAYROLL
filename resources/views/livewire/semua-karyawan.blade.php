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

                {{-- Header Utama --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-0">Data Karyawan</h2>
                        <p class="text-muted mb-0">Kelola semua data karyawan, jabatan.</p>
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
                                    <i class="bi bi-people-fill me-1"></i> Semua Karyawan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $pilihanMenu == 'tambah' || $pilihanMenu == 'edit' ? 'active' : '' }}"
                                    href="#" wire:click.prevent="pilihMenu('tambah')">
                                    <i class="bi bi-person-plus-fill me-1"></i>
                                    {{ $pilihanMenu == 'edit' ? 'Edit Karyawan' : 'Tambah Karyawan' }}
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-4">


                        @if ($pilihanMenu == 'tambah' || $pilihanMenu == 'edit')
                            {{-- ================= FORM TAMBAH / EDIT ================= --}}
                            <h4 class="mb-4">
                                {{ $pilihanMenu == 'tambah' ? 'Form Tambah Karyawan Baru' : 'Form Edit Karyawan' }}
                            </h4>
                            <form wire:submit="{{ $pilihanMenu == 'tambah' ? 'simpan' : 'simpanEdit' }}">
                                <div class="row g-3">
                                    {{-- KODE BARU --}}
                                    <div class="col-md-6">
                                        <label for="nama" class="form-label">Nama Lengkap</label>
                                        @if ($pilihanMenu == 'edit')
                                            {{-- Saat mode edit, nama tetap tidak bisa diubah --}}
                                            <input id="nama" type="text" class="form-control"
                                                value="{{ $penggunaTerpilih->nama ?? '' }}" disabled readonly />
                                        @else
                                            {{-- Saat mode tambah, gunakan input dengan fitur pencarian --}}
                                            <div class="position-relative">
                                                <input id="nama" type="text" class="form-control"
                                                    placeholder="Ketik untuk mencari nama karyawan..."
                                                    wire:model.live.debounce.300ms="namaSearch" {{-- Buat readonly setelah karyawan dipilih untuk mencegah perubahan manual --}}
                                                    {{ $userTerpilihId ? 'readonly' : '' }} />

                                                {{-- Tampilkan hasil pencarian jika ada input dan belum ada karyawan yang dipilih --}}
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
                                                @elseif(!empty($namaSearch) && !$userTerpilihId && $usersUntukDipilih->isEmpty())
                                                    <div class="list-group position-absolute w-100"
                                                        style="z-index: 1000;">
                                                        <span class="list-group-item">Karyawan tidak ditemukan.</span>
                                                    </div>
                                                @endif
                                            </div>
                                            @error('userTerpilihId')
                                                <span class="text-danger small mt-1">{{ $message }}</span>
                                            @enderror
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <label for="nis" class="form-label">NIS (Nomor Induk)</label>
                                        {{-- Input NIS selalu readonly, nilainya didapat otomatis --}}
                                        <input id="nis" type="number" class="form-control" wire:model="nis"
                                            readonly />
                                        @error('nis')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="jabatan" class="form-label">Jabatan</label>
                                        <select id="jabatan" class="form-select" wire:model="jabatanTerpilihId">
                                            <option value="">-- Pilih Jabatan --</option>
                                            @foreach ($daftarJabatan as $jabatan)
                                                <option value="{{ $jabatan->id }}">{{ $jabatan->nama }}</option>
                                            @endforeach
                                        </select>
                                        @error('jabatanTerpilihId')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Tambah field WhatsApp -->
                                    <div class="col-md-6">
                                        <label for="whatsapp" class="form-label">Nomor WhatsApp</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-whatsapp"></i></span>
                                            <input id="whatsapp" type="number" class="form-control"
                                                wire:model="whatsapp" placeholder="Contoh: 6281234567890" />
                                        </div>
                                        @error('whatsapp')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Tambah field Email -->
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                            <input id="email" type="email" class="form-control" wire:model="email"
                                                placeholder="Contoh: karyawan@example.com" />
                                        </div>
                                        @error('email')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Tambah field Alamat -->
                                    <div class="col-12">
                                        <label for="alamat" class="form-label">Alamat Lengkap</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                            <textarea id="alamat" class="form-control" wire:model="alamat" rows="2"
                                                placeholder="Masukkan alamat lengkap"></textarea>
                                        </div>
                                        @error('alamat')
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
                            {{-- ================= KONFIRMASI & TABEL KARYAWAN ================= --}}
                            @if ($pilihanMenu == 'hapus')
                                <div class="alert alert-danger p-3 mb-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="alert-heading mb-1">Konfirmasi Hapus</h5>
                                            <span>Anda yakin ingin menghapus karyawan
                                                <strong>"{{ $penggunaTerpilih->nama ?? '...' }}"</strong>?</span>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-light btn-sm" wire:click='batal'>Batal</button>
                                            <button class="btn btn-danger btn-sm" wire:click='hapus'>Ya,
                                                Hapus</button>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Tabel Karyawan --}}
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th class="ps-3">Nama</th>
                                            <th>NIS</th>
                                            <th>Jabatan</th>
                                            <th>WhatsApp</th>
                                            <th>Email</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($penggunas as $index => $pengguna)
                                            <tr class="align-middle">
                                                <td>{{ $index + 1 }}</td>
                                                <td class="ps-3">
                                                    <div class="fw-semibold">{{ $pengguna->nama }}</div>
                                                    <small
                                                        class="text-muted">{{ Str::limit($pengguna->alamat, 30) }}</small>
                                                </td>
                                                <td>{{ $pengguna->nis }}</td>
                                                <td>
                                                    <span class="badge bg-primary-subtle text-primary-emphasis">
                                                        {{ $pengguna->jabatan->nama ?? '-' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($pengguna->whatsapp)
                                                        <a href="https://wa.me/{{ $pengguna->whatsapp }}"
                                                            target="_blank" class="text-decoration-none"
                                                            data-bs-toggle="tooltip" title="Kirim WhatsApp">
                                                            <i class="bi bi-whatsapp text-success me-1"></i>
                                                            {{ $pengguna->whatsapp }}
                                                        </a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($pengguna->email)
                                                        <a href="mailto:{{ $pengguna->email }}"
                                                            class="text-decoration-none">
                                                            <i class="bi bi-envelope text-primary me-1"></i>
                                                            {{ Str::limit($pengguna->email, 15) }}
                                                        </a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <button class="btn btn-sm btn-outline-warning"
                                                            wire:click="pilihEdit({{ $pengguna->id }})"
                                                            data-bs-toggle="tooltip" title="Edit">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger"
                                                            wire:click="pilihHapus({{ $pengguna->id }})"
                                                            data-bs-toggle="tooltip" title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-5">
                                                    <i class="bi bi-person-x-fill fs-1 text-muted"></i>
                                                    <h5 class="mt-3">Tidak Ada Data Karyawan</h5>
                                                    <p class="text-muted">Klik tombol "Tambah Karyawan" untuk memulai.
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

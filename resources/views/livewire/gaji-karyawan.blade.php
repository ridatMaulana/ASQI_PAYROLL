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
                        <h2 class="mb-0">Manajemen Penggajian</h2>
                        <p class="text-muted mb-0">Kelola riwayat dan input data penggajian karyawan.</p>
                    </div>
                </div>

                {{-- Konten Utama dalam Satu Kartu --}}
                <div class="card card-custom">
                    <div class="card-header card-header-custom">
                        {{-- Navigasi Tab --}}
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item">
                                <a class="nav-link {{ $tampilanAktif == 'riwayat' ? 'active' : '' }}" href="#"
                                    wire:click.prevent="pilihanTampilan('riwayat')">
                                    <i class="bi bi-clock-history me-1"></i> Riwayat Gaji
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $tampilanAktif == 'form' ? 'active' : '' }}" href="#"
                                    wire:click.prevent="pilihanTampilan('form')">
                                    <i class="bi {{ $gaji_id ? 'bi-pencil-fill' : 'bi-plus-circle-fill' }} me-1"></i>
                                    {{ $gaji_id ? 'Edit Gaji' : 'Input Gaji Baru' }}
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-4">

                        @if ($tampilanAktif == 'riwayat')
                        {{-- ================================================= --}}
                        {{-- ============ KONTEN TAB RIWAYAT GAJI ============ --}}
                        {{-- ================================================= --}}
                        <div>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Periode</th>
                                            <th>Nama Karyawan</th>
                                            {{-- TAMBAHKAN HEADER KOLOM BARU --}}
                                            <th><strong>Sumber Dana</strong></th>
                                            <th>Gaji Bersih Akhir</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($data_gaji as $gaji)
                                        <tr class="align-middle" wire:key="gaji-{{ $gaji->id }}">
                                            <td>{{ \Carbon\Carbon::parse($gaji->periode)->format('d F Y') }}
                                            </td>
                                            <td>{{ $gaji->user->name ?? '-' }}</td>
                                            <td>
                                                <strong>
                                                    <span class="badge bg-secondary fw-normal">
                                                        <i class="bi bi-bank me-1"></i>
                                                        {{-- Kode ini memanggil relasi 'sumberDana' dari Model --}}
                                                        {{ $gaji->sumberDana->nama_pengguna ?? 'N/A' }}
                                                    </span>
                                                </strong>
                                            </td>
                                            <td class="fw-bold text-success">Rp
                                                {{ number_format($gaji->total_gaji_bersih, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('gaji.print', $gaji->id) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip"
                                                    title="Cetak Slip Gaji">
                                                    <i class="bi bi-printer"></i>
                                                </a>
                                                <button wire:click="kirimSlipGajiViaEmail({{ $gaji->id }})"
                                                    class="btn btn-sm btn-outline-success"
                                                    data-bs-toggle="tooltip" title="Kirim Slip Gaji ke Email"
                                                    onclick="return confirm('Anda yakin ingin mengirim slip gaji ini ke email karyawan?') || event.stopImmediatePropagation()">

                                                    <!-- Ikon normal -->
                                                    <span wire:loading.remove
                                                        wire:target="kirimSlipGajiViaEmail({{ $gaji->id }})">
                                                        <i class="bi bi-envelope-paper"></i>
                                                    </span>

                                                    <!-- Ikon saat loading -->
                                                    <span wire:loading
                                                        wire:target="kirimSlipGajiViaEmail({{ $gaji->id }})">
                                                        <span class="spinner-border spinner-border-sm"
                                                            role="status" aria-hidden="true"></span>
                                                    </span>
                                                </button>
                                                @if (auth()->user()->peran === 'admin')
                                                <button wire:click="edit({{ $gaji->id }})"
                                                    class="btn btn-sm btn-outline-warning"
                                                    data-bs-toggle="tooltip" title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <button wire:click="hapus({{ $gaji->id }})"
                                                    class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="tooltip" title="Hapus"
                                                    onclick="return confirm('Yakin hapus data gaji ini?') || event.stopImmediatePropagation()">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                <i class="bi bi-wallet fs-1 text-muted"></i>
                                                <h5 class="mt-3">Belum Ada Riwayat Gaji</h5>
                                                <p class="text-muted">Data gaji yang sudah dibuat akan muncul di
                                                    sini.</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if ($data_gaji->hasPages())
                            <div class="pt-4">
                                {{ $data_gaji->links() }}
                            </div>
                            @endif
                        </div>
                        @elseif ($tampilanAktif == 'form')
                        {{-- =============================================== --}}
                        {{-- ============ KONTEN TAB FORMULIR ============ --}}
                        {{-- =============================================== --}}
                        {{-- Ganti tag <form> lama Anda dengan ini --}}
                        <form wire:submit="simpan">
                            <h5 class="mb-4">
                                {{ $gaji_id ? 'Formulir Update Data Gaji' : 'Formulir Input Gaji Baru' }}
                            </h5>

                            {{-- Data Utama --}}
                            <div class="row g-3 mb-4">

                                <div class="col-md-6">
                                    <label for="nama_karyawan_search" class="form-label">Nama Karyawan</label>

                                    {{-- Saat mode edit, input tidak bisa diubah --}}
                                    @if ($gaji_id)
                                    <input id="nama_karyawan_search" type="text" class="form-control"
                                        wire:model="namaKaryawanSearch" disabled readonly />
                                    @else
                                    {{-- Saat mode tambah, gunakan input dengan fitur pencarian --}}
                                    <div class="position-relative">
                                        <input id="nama_karyawan_search" type="text" class="form-control"
                                            placeholder="Ketik untuk mencari nama karyawan..."
                                            wire:model.live.debounce.300ms="namaKaryawanSearch"
                                            {{ $selectedUser ? 'readonly' : '' }} /> {{-- Input menjadi readonly setelah dipilih --}}

                                        {{-- Tampilkan hasil pencarian --}}
                                        @if (!empty($namaKaryawanSearch) && !$selectedUser && $hasilPencarianKaryawan->isNotEmpty())
                                        <div class="list-group position-absolute w-100"
                                            style="z-index: 1000;">
                                            @foreach ($hasilPencarianKaryawan as $user)
                                            <a href="#"
                                                class="list-group-item list-group-item-action"
                                                wire:click.prevent="pilihKaryawan({{ $user->id }})">
                                                {{ $user->name }}
                                            </a>
                                            @endforeach
                                        </div>
                                        {{-- Tampilkan pesan jika tidak ada hasil --}}
                                        @elseif(!empty($namaKaryawanSearch) && !$selectedUser && $hasilPencarianKaryawan->isEmpty())
                                        <div class="list-group position-absolute w-100"
                                            style="z-index: 1000;">
                                            <span class="list-group-item">Karyawan tidak ditemukan.</span>
                                        </div>
                                        @endif
                                    </div>
                                    @error('selectedUser')
                                    {{-- Validasi tetap pada ID yang terpilih --}}
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label for="periode" class="form-label">Tanggal Gaji</label>
                                    <input type="date" wire:model.live="periode" class="form-control"
                                        id="periode">
                                    @error('periode')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <fieldset class="mb-4">
                                <legend class="h6">Sumber Pembayaran</legend>
                                <div class="mb-1">
                                    <label for="sumber_dana" class="form-label">Bayar Dari Akun</label>
                                    <select wire:model.live="sumber_dana_id" id="sumber_dana"
                                        class="form-select @error('sumber_dana_id') is-invalid @enderror">
                                        <option value="">-- Pilih Akun Bank/Kas --</option>
                                        {{-- Variabel $semuaKas dikirim dari komponen Livewire --}}
                                        @foreach ($semuaKas as $kas)
                                        {{-- Menampilkan nama bank beserta saldonya agar informatif --}}
                                        <option value="{{ $kas->id }}">
                                            {{ $kas->nama_pengguna }} (Saldo: Rp
                                            {{ number_format($kas->saldo, 0, ',', '.') }})
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('sumber_dana_id')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                    @enderror
                                </div>
                            </fieldset>

                            <div class="row g-4">
                                {{-- Kolom Pendapatan --}}
                                <div class="col-lg-6">
                                    <fieldset>
                                        <legend class="h6">Pendapatan</legend>
                                        {{-- [MODIFIKASI] Blok Gaji Pokok dengan Alpine.js --}}
                                        <div class="mb-3" x-data="numericInput($wire.entangle('gaji_pokok').live)">
                                            <label for="gaji_pokok" class="form-label">Gaji Pokok</label>
                                            <input type="text" id="gaji_pokok" class="form-control"
                                                x-model="displayValue" @input="updateLivewire"
                                                placeholder="e.g. 5.000.000" inputmode="numeric">
                                            @error('gaji_pokok')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        {{-- [MODIFIKASI] Blok Tunjangan dengan Alpine.js --}}
                                        <div class="mb-3" x-data="numericInput($wire.entangle('tunjangan').live)">
                                            <label for="tunjangan" class="form-label">Tunjangan</label>
                                            <input type="text" id="tunjangan" class="form-control"
                                                x-model="displayValue" @input="updateLivewire"
                                                placeholder="e.g. 500.000" inputmode="numeric">
                                            @error('tunjangan')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </fieldset>
                                </div>


                                {{-- Kolom Potongan --}}
                                <div class="col-lg-6">
                                    <fieldset>
                                        <legend class="d-flex justify-content-between align-items-center">
                                            <span class="h6">Potongan</span>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light py-0 px-2" type="button"
                                                    data-bs-toggle="dropdown" title="Kelola Master Potongan">
                                                    <i class="bi bi-gear-fill"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item"
                                                            href="{{ route('pengelolaan-pajak') }}">Pajak</a></li>
                                                    <li><a class="dropdown-item"
                                                            href="{{ route('pengelolaan-bpjs') }}">BPJS</a></li>
                                                    <li><a class="dropdown-item"
                                                            href="{{ route('pengelolaan-kasbon') }}">Kasbon</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </legend>
                                        <div class="row g-3">
                                            {{-- Dropdown Aturan Pajak --}}
                                            <div class="col-sm-6">
                                                <label for="pajak_rule" class="form-label">Aturan Pajak</label>
                                                <select id="pajak_rule" wire:model.live="pajak_id"
                                                    class="form-select">
                                                    <option value="">-- Tidak Kena Pajak --</option>
                                                    @foreach ($aturanPajak as $p)
                                                    <option value="{{ $p->id }}">{{ $p->nama_pajak }}
                                                        ({{ rtrim(rtrim($p->persentase, '0'), '.') }}%)
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- Hasil Perhitungan Pajak --}}
                                            <div class="col-sm-6">
                                                <label for="pajak_hasil" class="form-label">Jumlah Pajak
                                                    (Rp)</label>
                                                <input type="text" id="pajak_hasil" class="form-control"
                                                    value="{{ number_format($pajak) }}" readonly
                                                    placeholder="Otomatis">
                                                @error('pajak')
                                                <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row g-3 mt-1">
                                            {{-- Hasil Perhitungan BPJS --}}
                                            <div class="col-sm-6">
                                                <label for="bpjs" class="form-label">Potongan BPJS
                                                    (Rp)</label>
                                                <input type="text" id="bpjs" class="form-control"
                                                    value="{{ number_format($bpjs) }}" readonly
                                                    placeholder="Otomatis">
                                            </div>

                                            {{-- Input Potongan Lainnya --}}
                                            <div class="col-sm-6">
                                                <label for="kasbon" class="form-label">Kasbon</label>
                                                <input type="number" id="kasbon" class="form-control"
                                                    wire:model="kasbon" readonly>
                                                @error('kasbon')
                                                <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            {{-- [MODIFIKASI] Blok Potongan Lainnya dengan Alpine.js --}}
                                            <div class="col-sm-6" x-data="numericInput($wire.entangle('potongan_lainnya').live)">
                                                <label for="potongan_lainnya" class="form-label">Potongan
                                                    Lainnya</label>
                                                <input type="text" id="potongan_lainnya" class="form-control"
                                                    x-model="displayValue" @input="updateLivewire"
                                                    placeholder="0" inputmode="numeric">
                                                @error('potongan_lainnya')
                                                <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="alert alert-success mt-4 text-end">
                                <h5 class="mb-0">Total Gaji Bersih: <span
                                        class="fw-bold">{{ 'Rp ' . number_format($total_gaji_bersih) }}</span>
                                </h5>
                            </div>

                            {{-- Tombol Aksi --}}
                            <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                                @if ($gaji_id)
                                <button type="button" wire:click="resetForm"
                                    class="btn btn-secondary me-2">Batal Edit</button>
                                @endif
                                <button type="submit" class="btn btn-primary fw-bold px-4">
                                    <span wire:loading.remove wire:target="simpan">
                                        <i class="bi {{ $gaji_id ? 'bi-arrow-repeat' : 'bi-save-fill' }}"></i>
                                        {{ $gaji_id ? 'Update Data' : 'Simpan Data' }}
                                    </span>
                                    <span wire:loading wire:target="simpan">
                                        <span class="spinner-border spinner-border-sm" role="status"
                                            aria-hidden="true"></span>
                                        Menyimpan...
                                    </span>
                                </button>
                            </div>
                        </form>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Style yang sama persis dengan halaman Aktivitas Karyawan --}}
    <style>
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

        .card-custom {
            border: 1px solid var(--app-border-color);
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.07);
            background-color: var(--app-card-bg);
        }

        .custom-tabs-container {
            padding: 8px;
            background-color: #f7f7f7;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .custom-tabs-container .nav-pills .nav-link {
            color: var(--app-text-muted);
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.2s ease-in-out;
        }

        .custom-tabs-container .nav-pills .nav-link.active {
            background-color: var(--app-card-bg) !important;
            color: var(--app-text-dark) !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .table-custom {
            border: none;
        }

        .table-custom thead th {
            font-weight: 600;
            color: var(--app-text-dark);
            background-color: transparent;
            border-bottom: 2px solid var(--app-border-color);
            padding: 1rem 1.5rem;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }

        .table-custom tbody tr {
            border-bottom: 1px solid #f2f2f2;
        }

        .table-custom tbody tr:last-child {
            border-bottom: none;
        }

        .table-custom tbody td {
            padding: 1.25rem 1.5rem;
            vertical-align: middle;
            color: var(--app-text-dark);
        }

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

    {{-- Script untuk re-inisialisasi tooltip setelah update Livewire --}}
    <script>
        document.addEventListener('livewire:navigated', () => {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                // Hapus instance tooltip lama jika ada untuk mencegah duplikasi
                var oldTooltip = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
                if (oldTooltip) {
                    oldTooltip.dispose();
                }
                // Buat instance tooltip baru
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>

    {{-- [TIDAK ADA PERUBAHAN] Skrip ini sudah siap pakai untuk format angka --}}
    <script>
        function numericInput(livewireProperty) {
            return {
                value: livewireProperty,

                displayValue: '',

                init() {
                    this.displayValue = this.format(this.value);

                    this.$watch('value', (newValue) => {
                        let cleanDisplay = (this.displayValue || '').toString().replace(/\D/g, '');
                        if (Number(cleanDisplay) !== Number(newValue)) {
                            this.displayValue = this.format(newValue);
                        }
                    });
                },

                updateLivewire() {
                    let cleanValue = (this.displayValue || '').toString().replace(/\D/g, '');
                    this.value = cleanValue === '' ? 0 : Number(cleanValue);

                    this.$nextTick(() => {
                        this.displayValue = this.format(cleanValue);
                    });
                },

                format(val) {
                    if (val === null || val === '') return '';
                    return Number(val).toLocaleString('id-ID');
                }
            }
        }
    </script>
</div>
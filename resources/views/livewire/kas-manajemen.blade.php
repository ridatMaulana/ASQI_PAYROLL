<div>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-11 col-xl-10">

                {{-- Pesan Sukses / Error --}}
                @if (session()->has('pesan') || session()->has('pesan_transaksi'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('pesan') ?? session('pesan_transaksi') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if ($mode == 'transaksi')
                {{-- TAMPILAN MODE TRANSAKSI (TIDAK ADA PERUBAHAN) --}}
                <div class="card card-custom">
                    <div class="card-header card-header-custom d-flex justify-content-between align-items-center p-3">
                        <h5 class="mb-0">Detail Transaksi untuk: <strong>{{ $selectedKas->nama_pengguna }}</strong></h5>
                        <button wire:click="kembaliKeDaftar" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            {{-- Form Tambah Transaksi --}}
                            <div class="col-lg-4">
                                <h5 class="mb-3">Tambah Transaksi Baru</h5>
                                <form wire:submit.prevent="simpanTransaksi">
                                    <div class="mb-3">
                                        <label for="tanggal" class="form-label">Tanggal</label>
                                        <input type="date" class="form-control @error('tanggal') is-invalid @enderror" wire:model.defer="tanggal">
                                        @error('tanggal') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="keterangan" class="form-label">Keterangan</label>
                                        <input type="text" class="form-control @error('keterangan') is-invalid @enderror" wire:model.defer="keterangan" placeholder="Cth: Perjalanan Dinas">
                                        @error('keterangan') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="jenis" class="form-label">Jenis Transaksi</label>
                                        <select class="form-select @error('jenis') is-invalid @enderror" wire:model.defer="jenis">
                                            <option value="masuk">Kas Masuk</option>
                                            <option value="keluar">Kas Keluar</option>
                                        </select>
                                        @error('jenis') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    {{-- DENGAN KODE BARU INI --}}
                                    <div
                                        class="mb-3"
                                        x-data="{ 
        value: @entangle('jumlah'),
        formatNumber(num) {
            if (!num) return '';
            // Ganti koma dengan titik untuk perhitungan, lalu format
            let number = parseFloat(String(num).replace(/,/g, '.'));
            return new Intl.NumberFormat('id-ID').format(number);
        },
        updateValue(e) {
            // Hapus semua karakter non-digit kecuali koma
            let rawValue = e.target.value.replace(/[^0-9,]/g, '');
            // Ganti koma desimal dengan titik untuk Livewire
            this.value = rawValue.replace(/,/g, '.');
            // Format ulang tampilan
            e.target.value = this.formatNumber(rawValue);
        }
    }"
                                        x-init="
        $watch('value', val => {
            $refs.display.value = formatNumber(val)
        });
        $refs.display.value = formatNumber(value);
    ">
                                        <label for="jumlah_display" class="form-label">Jumlah (Rp)</label>

                                        {{-- 1. Input yang dilihat pengguna (dengan format) --}}
                                        <input
                                            id="jumlah_display"
                                            type="text"
                                            class="form-control @error('jumlah') is-invalid @enderror"
                                            x-ref="display"
                                            @input.debounce.300ms="updateValue($event)"
                                            placeholder="Ketik angka, cth: 50000,50">

                                        {{-- 2. Input yang terhubung ke Livewire (tanpa format, disembunyikan) --}}
                                        {{-- Livewire akan menerima nilai numerik bersih seperti '50000.50' --}}
                                        <input type="hidden" wire:model.defer="jumlah">

                                        @error('jumlah') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <span wire:loading.remove wire:target="simpanTransaksi">Simpan Transaksi</span>
                                        <span wire:loading wire:target="simpanTransaksi">Menyimpan...</span>
                                    </button>
                                </form>
                            </div>
                            {{-- Tabel Riwayat Transaksi --}}
                            <div class="col-lg-8">
                                <h5 class="mb-3">Riwayat Transaksi</h5>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>

                                                <th>Tanggal</th>
                                                <th>Keterangan</th>
                                                <th class="text-end">Masuk</th>
                                                <th class="text-end">Keluar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($transaksis as $transaksi)
                                            <tr>
                                                <td>{{ $transaksi->tanggal->format('d M Y') }}</td>
                                                <td>{{ $transaksi->keterangan }}</td>
                                                <td class="text-end text-success">@if ($transaksi->jenis == 'masuk'){{ number_format($transaksi->jumlah, 0, ',', '.') }}@endif</td>
                                                <td class="text-end text-danger">@if ($transaksi->jenis == 'keluar'){{ number_format($transaksi->jumlah, 0, ',', '.') }}@endif</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-5"><i class="bi bi-journal-text fs-1 text-muted"></i>
                                                    <h5 class="mt-3">Belum Ada Transaksi</h5>
                                                    <p class="text-muted">Setiap kas masuk dan keluar akan tercatat di sini.</p>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-4 pt-3 border-top text-end">
                                    <h6 class="text-muted fw-normal">Saldo Saat Ini:</h6>
                                    <h3 class="fw-bold mb-0">Rp {{ number_format($selectedKas->saldo, 2, ',', '.') }}</h3>
                                </div>
                                @if ($transaksis->hasPages())<div class="mt-4">{{ $transaksis->links() }}</div>@endif
                            </div>
                        </div>
                    </div>
                </div>
                @else
                {{-- TAMPILAN MODE DAFTAR AKUN BANK --}}
                <div class="card card-custom">
                    <div class="card-header card-header-custom">
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item"><a class="nav-link {{ !$tampilkanTrash && $pilihanMenu != 'tambah' && $pilihanMenu != 'edit' ? 'active' : '' }}" href="#" wire:click.prevent="tampilkanMenuTrash(false)"><i class="bi bi-bank2 me-1"></i> Daftar Bank</a></li>
                            <li class="nav-item"><a class="nav-link {{ $pilihanMenu == 'tambah' ? 'active' : '' }}" href="#" wire:click.prevent="pilihMenu('tambah')"><i class="bi bi-plus-circle me-1"></i> Tambah Bank</a></li>
                            <li class="nav-item"><a class="nav-link {{ $tampilkanTrash ? 'active' : '' }}" href="#" wire:click.prevent="tampilkanMenuTrash(true)"><i class="bi bi-trash3-fill me-1"></i> Data Terhapus</a></li>
                        </ul>
                    </div>
                    <div class="card-body p-4">

                        @if ($pilihanMenu == 'tambah' || $pilihanMenu == 'edit')
                        {{-- FORM TAMBAH / EDIT BANK --}}
                        <h4 class="mb-4">{{ $pilihanMenu == 'tambah' ? 'Form Tambah Bank Baru' : 'Form Edit Nama Bank' }}</h4>
                        <form wire:submit.prevent="{{ $pilihanMenu == 'tambah' ? 'simpan' : 'simpanEdit' }}">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nama" class="form-label">Nama Bank</label>
                                    <input id="nama" type="text" class="form-control" wire:model.defer="nama" placeholder="Contoh: BCA, Mandiri, Kas Tunai" />
                                    @error('nama')<span class="text-danger small">{{ $message }}</span>@enderror
                                </div>
                                @if ($pilihanMenu == 'edit')
                                <div class="col-md-6">
                                    <label class="form-label">Saldo</label>
                                    <input type="text" class="form-control" value="Rp {{ number_format($saldo, 2, ',', '.') }}" readonly disabled />
                                    <small class="form-text text-muted">Saldo hanya bisa diubah melalui halaman transaksi.</small>
                                </div>
                                @endif
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="button" wire:click="batal" class="btn btn-secondary">Batal</button>
                                <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i> Simpan</button>
                            </div>
                        </form>
                        @else
                        {{-- TABEL DAFTAR BANK & KONFIRMASI --}}

                        {{-- PENAMBAHAN: ALERT KONFIRMASI HAPUS SEMUA --}}
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

                        @if ($pilihanMenu == 'hapus' || $pilihanMenu == 'restore' || $pilihanMenu == 'hapus_permanen')
                        {{-- Alert Konfirmasi Aksi Individual --}}
                        @php
                        $isRestore = $pilihanMenu == 'restore'; $isPermanentDelete = $pilihanMenu == 'hapus_permanen';
                        $alertClass = $isRestore ? 'alert-info' : 'alert-danger';
                        $actionText = $isRestore ? 'Pulihkan' : 'Hapus';
                        $wireAction = $isRestore ? 'restore' : ($isPermanentDelete ? 'hapusPermanen' : 'hapus');
                        @endphp
                        <div class="alert {{ $alertClass }} p-3 mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="alert-heading mb-1">Konfirmasi Aksi</h5>
                                    <span>Yakin ingin <strong>{{ $isPermanentDelete ? 'menghapus permanen' : strtolower($actionText) }}</strong> akun bank <strong>"{{ $nama ?? '...' }}"</strong>?</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-light btn-sm" wire:click='batal'>Batal</button>
                                    <button class="btn {{ $isRestore ? 'btn-info' : 'btn-danger' }} btn-sm" wire:click='{{ $wireAction }}'>Ya, {{ $actionText }}</button>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- PENAMBAHAN: TOMBOL HAPUS SEMUA --}}
                        @if($tampilkanTrash && $semuaKas->isNotEmpty())
                        <div class="d-flex justify-content-end mb-3">
                            <button wire:click="konfirmasiHapusSemuaPermanen" class="btn btn-danger btn-sm">
                                <i class="bi bi-trash3-fill me-1"></i> Hapus Semua
                            </button>
                        </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Nama Bank</th>
                                        <th class="text-end">Saldo</th>
                                        @if ($tampilkanTrash)<th>Dihapus Pada</th>@endif
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($semuaKas as $kas)
                                    <tr>
                                        <td class="ps-4">{{ $kas->nama_pengguna }}</td>
                                        <td class="text-end">Rp {{ number_format($kas->saldo, 2, ',', '.') }}</td>
                                        @if ($tampilkanTrash)<td>{{ $kas->deleted_at->format('d M Y, H:i') }}</td>@endif
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                @if ($kas->trashed())
                                                <button wire:click="pilihRestore({{ $kas->id }})" class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" title="Pulihkan"><i class="bi bi-arrow-counterclockwise"></i></button>
                                                <button wire:click="pilihHapusPermanen({{ $kas->id }})" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Hapus Permanen"><i class="bi bi-trash-fill"></i></button>
                                                @else
                                                <button wire:click="lihatTransaksi({{ $kas->id }})" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Lihat Transaksi"><i class="bi bi-journal-text"></i></button>
                                                <button wire:click="pilihEdit({{ $kas->id }})" class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                                <button wire:click="pilihHapus({{ $kas->id }})" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Hapus"><i class="bi bi-trash"></i></button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="{{ $tampilkanTrash ? '4' : '3' }}" class="text-center py-5"><i class="bi {{ $tampilkanTrash ? 'bi-trash3' : 'bi-bank2' }} fs-1 text-muted"></i>
                                            <h5 class="mt-3">{{ $tampilkanTrash ? 'Data Terhapus Kosong' : 'Belum Ada Akun Bank' }}</h5>
                                            <p class="text-muted">Klik tombol "Tambah Bank" untuk memulai.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                    @if (isset($semuaKas) && $semuaKas->hasPages())<div class="card-footer bg-light">{{ $semuaKas->links() }}</div>@endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
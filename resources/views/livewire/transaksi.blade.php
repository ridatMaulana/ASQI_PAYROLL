<div>
    <div class="container">
        <!-- Tombol Transaksi Baru / Batalkan Transaksi -->
        <div class="row my-3">
            <div class="col-12 d-flex flex-wrap gap-2 justify-content-center">
                @if (!$transaksiAktif)
                <button wire:click='transaksiBaru'
                    class="col-lg-10 col-md-6 mb-4 btn {{ !$transaksiAktif ? 'btn-warning' : 'btn-outline-warning' }} mx-2 py-2 px-4 rounded-pill shadow-sm btn-custom">
                    <i class="bi bi-cart-plus"></i>
                    <span class="text-custom-orange-baru">Transaksi Baru</span>
                </button>
                @else
                <button wire:click='batalTransaksi'
                    class="col-lg-10 col-md-6 mb-4 btn {{ $transaksiAktif ? 'btn-danger' : 'btn-outline-danger' }} mx-2 py-2 px-4 rounded-pill shadow-sm btn-custom">
                    <i class="bi bi-x-circle"></i> Batalkan Transaksi
                </button>
                @endif
                <button wire:loading
                    class="col-lg-10 col-md-6 mb-4 btn btn-custom-orange mx-2 py-2 px-4 rounded-pill shadow-sm">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span class="text-custom-orange-loading">Loading...</span>
                </button>
            </div>
        </div>

        <!-- CSS Transaksi -->
        <style>
            .text-custom-orange-baru {
                color: rgb(146, 54, 0)
            }
            .text-custom-orange-loading {
                color: rgb(107, 43, 0)
            }

            .btn-custom-orange {
                background-color: rgb(255, 94, 0);
            }
        </style>
        <!-- --- -->
        <!-- CSS untuk card Transaksi -->
        <style>
            .outline-transaksi {
                border: 2px solid rgb(255, 94, 0);
            }

            .bg-semuauser {
                background-color: rgb(255, 94, 0)
            }
        </style>
        <!-- --- -->

        <!-- Menampilkan Transaksi Aktif -->
        @if ($transaksiAktif)
        <div class="row mt-4">
            <!-- Bagian Kiri (Daftar Produk) -->
            <div class="col-12 col-lg-8 mb-4">
                <div class="card outline-transaksi shadow-sm rounded">
                    <div class="card-body">
                        <h4 class="card-title">No Invoice: <strong>{{ $transaksiAktif->kode }}</strong></h4>
                        <input type="text" class="form-control mb-3 border-warning" placeholder="Masukkan kode invoice"
                            wire:model.live='kode'>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-warning text-center">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Barang</th>
                                        <th>Harga</th>
                                        <th>Qty</th>
                                        <th>Total</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($semuaProduk as $produk)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $produk->produk->nama }}</td>
                                        <td class="text-end">Rp. {{ number_format($produk->produk->harga, 2, '.', ',') }}</td>
                                        <td class="text-center">{{ $produk->jumlah }}</td>
                                        <td class="text-end">Rp. {{ number_format($produk->produk->harga * $produk->jumlah, 2, '.', ',') }}</td>
                                        <td class="text-center">
                                            <button wire:click="hapusProduk({{ $produk->id }})" class="btn btn-danger btn-sm">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bagian Kanan (Total, Bayar, Kembalian) -->
            <div class="col-12 col-lg-4">
                <!-- Total Biaya -->
                <div class="card outline-transaksi shadow-sm rounded mb-3">
                    <div class="card-body">
                        <h4 class="card-title">Total Biaya</h4>
                        <div class="d-flex justify-content-between">
                            <span>Rp.</span>
                            <span>{{ number_format($totalSemuaBelanja, 2, '.', ',') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Bayar -->
                <div class="card outline-transaksi shadow-sm rounded mb-3">
                    <div class="card-body">
                        <h4 class="card-title">Bayar</h4>
                        <input type="number" class="form-control border-warning" placeholder="Masukkan jumlah bayar" wire:model.live='bayar'>
                    </div>
                </div>

                <!-- Kembalian -->
                <div class="card outline-transaksi shadow-sm rounded mb-3">
                    <div class="card-body">
                        <h4 class="card-title">Kembalian</h4>
                        <div class="d-flex justify-content-between">
                            <span>Rp.</span>
                            <span>{{ number_format($kembalian, 2, '.', ',') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Tombol Bayar atau Pesan Error -->
                @if ($bayar)
                @if ($kembalian < 0)
                    <div class="alert alert-danger">
                    Jumlah bayar kurang
            </div>
            @elseif ($kembalian >= 0)
            <button class="btn btn-success w-100 mt-2" wire:click='transaksiSelesai'>
                <i class="bi bi-check-circle"></i> Bayar
            </button>
            @endif
            @endif
        </div>
    </div>
    @endif

    <!-- Menampilkan Barcode setelah transaksi selesai -->
    @if ($transaksiSelesai)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card outline-transaksi shadow-sm rounded">
                <div class="card-body text-center">
                    <h4 class="card-title">Barcode Transaksi</h4>
                    <svg id="barcode"></svg>
                    <p><strong>Invoice Kode: </strong>{{ $transaksiAktif->kode }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>

<script>
    // Emit event ketika transaksi selesai
    Livewire.on('transaksiSelesai', (kodeTransaksi) => {
        const barcode = document.getElementById('barcode');
        JsBarcode(barcode, kodeTransaksi, {
            format: "CODE128",
            displayValue: true
        });
    });
</script>
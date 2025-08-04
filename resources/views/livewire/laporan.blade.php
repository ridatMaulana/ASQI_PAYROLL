<div>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-11">

                {{-- HEADER: I've added the Print button here --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-0">
                            <i class="bi bi-book-half me-2"></i>Laporan Buku Besar
                        </h2>
                        <p class="text-muted mb-0">Rekapitulasi total pemasukan dan pengeluaran per akun bank.</p>
                    </div>
                    <div>
                        {{-- THE NEW PRINT BUTTON --}}
                        <button onclick="window.print()" class="btn btn-success">
                            <i class="bi bi-printer-fill me-1"></i> Cetak Laporan
                        </button>
                    </div>
                </div>

                {{-- The main card now has a unique ID for printing --}}
                <div class="card card-custom" id="laporan-cetak">
                    <div class="card-body p-4">

                        {{-- FILTER AREA: We will hide this when printing --}}
                        <div class="row g-3 mb-4 p-3 bg-light border rounded" id="area-filter">
                            <div class="col-md-3">
                                <label for="filterBulan" class="form-label">Bulan</label>
                                <select wire:model.live="filterBulan" id="filterBulan" class="form-select">
                                    <option value="">Semua Bulan</option>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}">{{ \Carbon\Carbon::create(null, $i)->monthName }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filterTahun" class="form-label">Tahun</label>
                                <select wire:model.live="filterTahun" id="filterTahun" class="form-select">
                                    <option value="">Semua Tahun</option>
                                    @foreach($listTahun as $tahun)
                                        <option value="{{ $tahun }}">{{ $tahun }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Bulan & Tahun</th>
                                        <th>Nama Akun Bank</th>
                                        <th class="text-end text-success">Total Pemasukan</th>
                                        <th class="text-end text-danger">Total Pengeluaran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // Month name mapping array
                                        $bulanNama = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                                    @endphp
                                    @forelse ($rekapData as $rekap)
                                    <tr>
                                        <td class="ps-4 fw-bold">{{ $bulanNama[$rekap->bulan] }} {{ $rekap->tahun }}</td>
                                        <td>{{ $rekap->nama_pengguna }}</td>
                                        <td class="text-end text-success">Rp {{ number_format($rekap->total_pemasukan, 0, ',', '.') }}</td>
                                        <td class="text-end text-danger">Rp {{ number_format($rekap->total_pengeluaran, 0, ',', '.') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <i class="bi bi-journal-x fs-1 text-muted"></i>
                                            <h5 class="mt-3">Data Tidak Ditemukan</h5>
                                            <p class="text-muted">Tidak ada data transaksi yang cocok dengan filter yang dipilih.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                @if($rekapData->isNotEmpty())
                                <tfoot class="table-group-divider">
                                    <tr class="fw-bolder fs-5">
                                        <td colspan="2" class="ps-4">GRAND TOTAL</td>
                                        <td class="text-end text-success">Rp {{ number_format($rekapData->sum('total_pemasukan'), 0, ',', '.') }}</td>
                                        <td class="text-end text-danger">Rp {{ number_format($rekapData->sum('total_pengeluaran'), 0, ',', '.') }}</td>
                                    </tr>
                                     @php
                                        $totalPemasukan = $rekapData->sum('total_pemasukan');
                                        $totalPengeluaran = $rekapData->sum('total_pengeluaran');
                                        $labaRugi = $totalPemasukan - $totalPengeluaran;
                                    @endphp
                                    <tr class="fw-bolder fs-5">
                                        <td colspan="3" class="ps-4">
                                            @if ($labaRugi >= 0)
                                                Laba Bersih (Untung)
                                            @else
                                                Rugi Bersih
                                            @endif
                                        </td>
                                        <td class="text-end @if ($labaRugi >= 0) text-success @else text-danger @endif">
                                            Rp {{ number_format($labaRugi, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Custom CSS, including styles for printing --}}
    <style>
        .card-custom {
            border: 1px solid #dee2e6;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .table th {
            font-weight: 600;
            white-space: nowrap;
            color: #495057;
        }

        /* PRINT STYLES: This will only apply when printing */
        @media print {
            /* Hide everything except the report content */
            body * {
                visibility: hidden;
            }
            #laporan-cetak, #laporan-cetak * {
                visibility: visible;
            }
            /* Position the report at the top of the page */
            #laporan-cetak {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            /* Hide unnecessary elements like filters and shadows */
            .card-custom {
                border: none !important;
                box-shadow: none !important;
            }
            #area-filter {
                display: none;
            }
        }
    </style>
</div>
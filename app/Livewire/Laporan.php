<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaksi;
use App\Models\TransaksiKas;
use Illuminate\Support\Facades\DB;

class Laporan extends Component
{
    // Properti untuk filter
    public $filterBulan;
    public $filterTahun;

    // Properti untuk menampung data filter
    public $listTahun = [];

    /**
     * Method yang berjalan saat komponen pertama kali dimuat.
     */
    public function mount()
    {
        // Ambil semua tahun unik dari data transaksi untuk dropdown filter
        $this->listTahun = TransaksiKas::select(DB::raw('EXTRACT(YEAR FROM tanggal) as tahun'))
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        // Set filter default ke tahun dan bulan saat ini
        $this->filterTahun = now()->year;
        $this->filterBulan = now()->month;
    }

    /**
     * Method render untuk menampilkan data ke view.
     */
    public function render()
    {
        // Ini adalah query inti untuk membuat rekapitulasi buku besar
        $rekapData = TransaksiKas::query()
            // Pilih kolom yang dibutuhkan
            ->select(
                'kas.nama_pengguna',
                DB::raw('EXTRACT(YEAR FROM transaksi_kas.tanggal) as tahun'),
                DB::raw('EXTRACT(MONTH FROM transaksi_kas.tanggal) as bulan'),
                // Hitung total pemasukan: jumlahkan 'jumlah' HANYA JIKA jenisnya 'masuk'
                DB::raw("SUM(CASE WHEN transaksi_kas.jenis = 'masuk' THEN transaksi_kas.jumlah ELSE 0 END) as total_pemasukan"),
                // Hitung total pengeluaran: jumlahkan 'jumlah' HANYA JIKA jenisnya 'keluar'
                DB::raw("SUM(CASE WHEN transaksi_kas.jenis = 'keluar' THEN transaksi_kas.jumlah ELSE 0 END) as total_pengeluaran")
            )
            // Gabungkan dengan tabel 'kas' untuk mendapatkan nama bank
            ->join('kas', 'transaksi_kas.kas_id', '=', 'kas.id')
            // Terapkan filter jika ada yang dipilih
            ->when($this->filterTahun, function ($query) {
                $query->whereYear('transaksi_kas.tanggal', $this->filterTahun);
            })
            ->when($this->filterBulan, function ($query) {
                $query->whereMonth('transaksi_kas.tanggal', $this->filterBulan);
            })
            // Kelompokkan hasil berdasarkan ID bank, tahun, dan bulan
            ->groupBy('kas.id', 'kas.nama_pengguna', 'tahun', 'bulan')
            // Urutkan hasilnya agar rapi
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->orderBy('kas.nama_pengguna', 'asc')
            ->get();

        // Kirim data rekapitulasi ke view
        return view('livewire.laporan', [
            'rekapData' => $rekapData
        ]);
    }

}
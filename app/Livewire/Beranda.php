<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Kas;             // Pastikan nama dan path model ini benar
use App\Models\TransaksiKas;     // Pastikan nama dan path model ini benar

class Beranda extends Component
{
    /**
     * Method render akan mengambil semua data dan mengirimkannya ke view.
     */
    public function render()
    {
        // 1. Ambil nama pengguna yang sedang login
        $namaPengguna = Auth::user()->name;

        // 2. Hitung total pemasukan bulan ini
        $totalPemasukanBulanIni = TransaksiKas::where('jenis', 'masuk')
            ->whereYear('tanggal', now()->year)
            ->whereMonth('tanggal', now()->month)
            ->sum('jumlah');

        // 3. Hitung total pengeluaran bulan ini
        $totalPengeluaranBulanIni = TransaksiKas::where('jenis', 'keluar')
            ->whereYear('tanggal', now()->year)
            ->whereMonth('tanggal', now()->month)
            ->sum('jumlah');

        // 4. Hitung total saldo dari semua akun kas/bank
        $saldoSaatIni = Kas::sum('saldo');

        // 5. Ambil 5 transaksi terakhir untuk ditampilkan
        $transaksiTerbaru = TransaksiKas::latest()->take(5)->get();

        // 6. Kirim semua data yang sudah dihitung ke file view 'livewire.beranda'
        return view('livewire.beranda', [
            'namaPengguna' => $namaPengguna,
            'totalPemasukanBulanIni' => $totalPemasukanBulanIni,
            'totalPengeluaranBulanIni' => $totalPengeluaranBulanIni,
            'saldoSaatIni' => $saldoSaatIni,
            'transaksiTerbaru' => $transaksiTerbaru, 
        ]);
    }
}

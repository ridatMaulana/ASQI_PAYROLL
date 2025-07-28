<?php

namespace App\Http\Controllers;

use App\Livewire\GajiKaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kas;
use App\Models\TransaksiKas; // Anda mungkin menggunakan ini, atau MutasiKas
use App\Models\Gaji;         // Pastikan model Gaji sudah ada
use App\Models\GajiKaryawan as ModelsGajiKaryawan;
use Carbon\Carbon;

class BerandaController extends Controller
{
    public function index()
    {
        // -----------------------------------------------------------------
        // SECTION 1: PERSIAPAN & DATA PENGGUNA
        // -----------------------------------------------------------------
        $namaPengguna = Auth::user()->name;
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();


        // -----------------------------------------------------------------
        // SECTION 2: KALKULASI KARTU RINGKASAN (SUMMARY CARDS)
        // -----------------------------------------------------------------

        // Total Pemasukan: Hanya dari Transaksi Kas 'masuk'
        $totalPemasukanBulanIni = TransaksiKas::where('jenis', 'masuk')
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->sum('jumlah');

        // Total Pengeluaran: Gabungan dari Transaksi Kas 'keluar' dan Gaji
        $pengeluaranKasBulanIni = TransaksiKas::where('jenis', 'keluar')
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->sum('jumlah');

        $pengeluaranGajiBulanIni = ModelsGajiKaryawan::whereBetween('periode', [$startOfMonth, $endOfMonth])
            ->sum('total_gaji_bersih');

        $totalPengeluaranBulanIni = $pengeluaranKasBulanIni + $pengeluaranGajiBulanIni;

        // Total Saldo: Diambil dari total saldo semua akun kas. Ini cara yang efisien dan sudah benar.
        $saldoSaatIni = Kas::sum('saldo');


        // -----------------------------------------------------------------
        // SECTION 3: MENGGABUNGKAN AKTIVITAS TERBARU
        // -----------------------------------------------------------------

        // Ambil 5 transaksi kas terakhir
        $aktivitasKas = TransaksiKas::latest('tanggal')->latest('id')->limit(5)->get()->map(function ($item) {
            return (object) [
                'tanggal'    => $item->tanggal,
                'keterangan' => $item->keterangan,
                'jenis'      => $item->jenis,
                'jumlah'     => $item->jumlah,
                'tipe_sumber'=> 'kas' // Penanda sumber data
            ];
        });

        // Ambil 5 transaksi gaji terakhir
        $aktivitasGaji = ModelsGajiKaryawan::with('user')->latest('periode')->latest('id')->limit(5)->get()->map(function ($item) {
            return (object) [
                'tanggal'    => $item->periode,
                'keterangan' => 'Pembayaran Gaji - ' . ($item->user->name ?? 'Karyawan'),
                'jenis'      => 'keluar',
                'jumlah'     => $item->total_gaji_bersih,
                'tipe_sumber'=> 'gaji' // Penanda sumber data
            ];
        });

        // Gabungkan kedua koleksi, urutkan berdasarkan tanggal, dan ambil 5 yang paling baru
        $transaksiTerbaru = $aktivitasKas->concat($aktivitasGaji)
            ->sortByDesc(function ($item) {
                // Mengurutkan berdasarkan objek Carbon untuk akurasi
                return Carbon::parse($item->tanggal);
            })
            ->take(5);


        // -----------------------------------------------------------------
        // SECTION 4: KIRIM DATA KE VIEW
        // -----------------------------------------------------------------
        return view('beranda', [
            'namaPengguna'             => $namaPengguna,
            'totalPemasukanBulanIni'   => $totalPemasukanBulanIni,
            'totalPengeluaranBulanIni' => $totalPengeluaranBulanIni,
            'saldoSaatIni'             => $saldoSaatIni,
            'transaksiTerbaru'         => $transaksiTerbaru,
        ]);
    }
}
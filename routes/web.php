<?php

use App\Http\Controllers\ExportAbsensiController;
use App\Http\Controllers\BerandaController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Beranda;
use App\Livewire\User;
use App\Livewire\Laporan;
use App\Livewire\Produk;
use App\Livewire\Transaksi;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\GajiController;
use App\Http\Livewire\PresensiHariIni;
use App\Livewire\Absen;
use App\Livewire\Asset;
use App\Livewire\Evaluasi;
use App\Livewire\EvaluasiPage;
use App\Livewire\Pengguna;
use App\Livewire\Presensi;
use App\Livewire\TaskPage;
use App\Livewire\SemuaKaryawan;
use App\Livewire\DaftarJabatan;
use App\Http\Controllers\UserController;
// use App\Http\Livewire\KasManajemen as LivewireKasManajemen;
use App\Livewire\GajiKaryawan;
use App\Livewire\PengelolaanBpjs;
use App\Livewire\PengelolaanKasbon;
use App\Livewire\PengelolaanPajak;
use App\Livewire\SemuaSiswa;
use App\Livewire\KasManajemen;
use App\Livewire\LaporanBukuBesar;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['register' => false]);

// HANYA SATU ROUTE UNTUK HALAMAN UTAMA, MENGGUNAKAN CONTROLLER
Route::get('/', [BerandaController::class, 'index'])->middleware(['auth'])->name('beranda');

// UBAH /home AGAR MENGALIHKAN KE ROUTE 'beranda' YANG BENAR
Route::get('/home', function () {
    return redirect()->route('beranda');
})->middleware(['auth'])->name('home');


Route::get('/pengguna', [UserController::class, 'index'])->name('pengguna');


Route::get('/semua-karyawan', SemuaKaryawan::class)->middleware(['auth'])->name('semua_karyawan');
Route::get('/daftar-jabatan', DaftarJabatan::class)->middleware(['auth'])->name('daftar_jabatan');
Route::get('/semua-siswa', SemuaSiswa::class)->middleware(['auth'])->name('semua-siswa');


Route::get('/user', \App\Livewire\User::class)->middleware(['auth'])->name('user');

// Route::get('/absen', Absen::class)->middleware(['auth'])->name('absen');
Route::get('/laporan', Laporan::class)->middleware(['auth'])->name('laporan');
// Route::get('/produk', Produk::class)->middleware(['auth'])->name('produk');
Route::get('/transaksi', Transaksi::class)->middleware(['auth'])->name('transaksi');
Route::get('/asset', Asset::class)->middleware(['auth'])->name('asset');
//evaluasi
Route::get('/evaluasi', EvaluasiPage::class)->middleware(['auth'])->name('evaluasi');
// Menambahkan route untuk ekspor PDF
Route::get('/export-pdf', [ExportController::class, 'exportPdf'])->middleware(['auth'])->name('export-pdf');
Route::get('/absensi/export/pdf', [ExportAbsensiController::class, 'exportPDF'])->name('absensi.export.pdf');

// Route cetak untuk laporan
Route::get('/cetak', [HomeController::class, 'cetak'])->name('cetak');

Route::get('/chart-data', [HomeController::class, 'getChartData']);
// Route::get('/chart-data', [BerandaController::class, 'chartData'])->middleware(['auth']);

//presensi
Route::get('/presensi', Presensi::class)->middleware(['auth'])->name('presensi');

// routes/web.php
// Route::middleware(['auth'])->group(function () {
//     Route::get('/tugas', \App\Livewire\TaskPage::class)->name('tugas.index');
// });

Route::get('/tugas', TaskPage::class)->middleware(['auth'])->name('tugas');
Route::middleware(['auth'])->group(function () {
    Route::get('/tugas', TaskPage::class)->name('tugas');
});

//GAJI
Route::get('/gaji', GajiKaryawan::class)->middleware(['auth'])->name('gaji');
//print gaji
// web.php
Route::get('/gaji/print/{id}', [GajiController::class, 'print'])->name('gaji.print');

Route::get('/pengelolaan-pajak', PengelolaanPajak::class)->middleware(['auth'])->name('pengelolaan-pajak');
// Route::get('/pengelolaan-bpjs', PengelolaanBpjs::class)->middleware(['auth'])->name('pengelolaan-bpjs');
Route::get('/pengelolaan-kasbon', PengelolaanKasbon::class)->middleware(['auth'])->name('pengelolaan-kasbon');

Route::get('/pengelolaan-bpjs', \App\Livewire\PengelolaanBpjs::class)
    ->middleware(['auth'])
    ->name('pengelolaan-bpjs');

Route::get('/pajak', PengelolaanPajak::class)->name('pajak');

// Kas Manajemen
Route::get('/kas', KasManajemen::class)->name('kas.manajemen');

//buku besar


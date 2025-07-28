<?php

namespace App\Http\Controllers;

use App\Models\GajiKaryawan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GajiController extends Controller
{
    /**
     * Menampilkan slip gaji sebagai PDF di browser.
     * Method ini akan dipanggil saat tombol "Cetak" diklik.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function print($id)
    {
        // 1. Ambil data gaji yang spesifik
        $gaji = GajiKaryawan::with('user')->findOrFail($id);

        // 2. Panggil method helper baru untuk menghasilkan data mentah PDF
        $pdfData = $this->generatePdfData($gaji);

        // 3. Buat nama file yang dinamis untuk ditampilkan di browser
        $periode = Carbon::parse($gaji->periode)->format('F-Y');
        $namaFile = 'Slip Gaji - ' . $gaji->user->name . ' - ' . $periode . '.pdf';

        // 4. Kirim data PDF ke browser untuk ditampilkan (inline)
        return response($pdfData, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $namaFile . '"'
        ]);
    }

        /**
     * Method helper PUBLIK untuk menghasilkan konten PDF mentah.
     * Method ini bisa dipanggil dari mana saja, termasuk dari komponen Livewire,
     * untuk mendapatkan data PDF sebagai lampiran email.
     *
     * @param GajiKaryawan $gaji
     * @return string
     */
    public function generatePdfData(GajiKaryawan $gaji)
    {
        // 1. Pastikan nama view Anda sudah benar.
        // Berdasarkan kode Anda, view-nya ada di 'gaji.print'.
        // Jika nama filenya berbeda, silakan sesuaikan di sini.
        $pdf = Pdf::loadView('gaji.print', compact('gaji'));
        
        // 2. ->output() akan mengembalikan konten PDF sebagai string mentah,
        //    siap untuk dilampirkan ke email.
        return $pdf->output();
    }



    // GajiController.php
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'periode' => 'required|date_format:Y-m',
            'gaji_pokok' => 'required|numeric',
            'tunjangan' => 'numeric|min:0',
            'bpjs' => 'numeric|min:0', // Pastikan ini ada
            'pajak' => 'numeric|min:0',
            'kasbon' => 'numeric|min:0',
            'potongan_absen' => 'numeric|min:0',
            'potongan_lainnya' => 'numeric|min:0'
        ]);

        // Konversi nilai ke integer
        $validated['gaji_pokok'] = (int) str_replace('.', '', $validated['gaji_pokok']);
        $validated['bpjs'] = (int) str_replace('.', '', $validated['bpjs']);
        // ...lakukan hal yang sama untuk field lainnya...

        // Hitung total gaji bersih
        $validated['total_gaji_bersih'] = $validated['gaji_pokok']
            + ($validated['tunjangan'] ?? 0)
            - ($validated['pajak'] ?? 0)
            - $validated['bpjs'] // Pastikan BPJS terpotong
            - ($validated['kasbon'] ?? 0)
            - ($validated['potongan_absen'] ?? 0)
            - ($validated['potongan_lainnya'] ?? 0);

        // Simpan ke database
        $gaji = GajiKaryawan::create($validated);

        return redirect()->route('riwayat-gaji')->with('success', 'Gaji berhasil disimpan');
    }
}

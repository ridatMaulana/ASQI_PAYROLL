<?php

namespace App\Http\Controllers;

use App\Models\PengelolaanBpjs;
use App\Models\Pengguna;
use App\Models\Jabatan;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function PengelolaanBpjs()
    {
        // Ambil data semua karyawan
        $penggunas = Pengguna::all();
        return view('semua-karyawan', compact('penggunas'));
    }
    public function semuaKaryawan()
    {
        // Ambil data semua karyawan
        $penggunas = Pengguna::all();
        return view('semua-karyawan', compact('penggunas'));
    }

    public function daftarJabatan()
    {
        // Ambil data jabatan
        $jabatans = Jabatan::all();
        return view('daftar_jabatan', compact('jabatans'));
    }
}


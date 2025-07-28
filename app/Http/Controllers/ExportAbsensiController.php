<?php

namespace App\Http\Controllers;

use App\Models\Absen as ModelAbsen;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportAbsensiController extends Controller
{
    public function exportPDF()
    {
        $absensi = ModelAbsen::all(); 

        $pdf = Pdf::loadView('pdf.absensi', compact('absensi'));
        return $pdf->download('data-absensi.pdf');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Siswa extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'aktif' => 'boolean', // Casting kolom baru
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // =======================================================
    // ---   INI BAGIAN PALING PENTING: ACCESSOR DINAMIS   ---
    // =======================================================
    /**
     * Accessor untuk memeriksa apakah status magang aktif.
     * Status dianggap aktif jika kolom `aktif` adalah true DAN tanggal selesai belum lewat.
     *
     * Cara memanggilnya di kode lain: $siswa->is_aktif
     */
    protected function isAktif(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->aktif && !Carbon::parse($this->tanggal_selesai)->isPast(),
        );
    }
}
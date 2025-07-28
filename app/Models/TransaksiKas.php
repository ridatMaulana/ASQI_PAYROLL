<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiKas extends Model
{
    use HasFactory;

    // Izinkan kolom-kolom ini untuk diisi secara massal
    protected $fillable = [
        'kas_id',
        'tanggal',
        'kode',
        'keterangan',
        'jenis',
        'jumlah',
    ];

    // Tentukan tipe data agar tidak error
    protected $casts = [
        'tanggal' => 'date',
    ];

    public function kas()
    {
        return $this->belongsTo(App\Models\Kas::class);
    }
}
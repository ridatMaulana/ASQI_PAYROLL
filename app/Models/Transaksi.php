<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DetilTransaksi;

class Transaksi extends Model
{
    // Gabungkan semua kolom yang mungkin diisi
    protected $fillable = [
        'kode',
        'total',
        'status', // Kolom untuk penjualan
        'kas_id',
        'tanggal',
        'keterangan',
        'jenis',
        'jumlah' // Kolom untuk buku kas
    ];

    public function detilTransaksi()
    {
        return $this->hasMany(DetilTransaksi::class);
    }

    public function kas()
    {
        return $this->belongsTo(Kas::class);
    }
}

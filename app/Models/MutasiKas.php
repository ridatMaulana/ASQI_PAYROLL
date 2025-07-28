<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MutasiKas extends Model
{
    use HasFactory;
    
    protected $table = 'mutasi_kas';
    
    protected $fillable = [
        'kas_id',
        'kode',
        'tanggal',
        'keterangan',
        'jenis',
        'jumlah',
    ];

    // =============================================================
    // INI BAGIAN YANG TERLEWAT DAN PERLU ANDA TAMBAHKAN
    // Untuk mengatasi error "Call to a member function format() on string"
    // =============================================================
    protected $casts = [
        'tanggal' => 'datetime',
    ];

    /**
     * Relasi ke model Kas.
     */
    public function kas()
    {
        return $this->belongsTo(Kas::class);
    }
}
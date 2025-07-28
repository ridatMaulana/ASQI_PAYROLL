<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- 1. Tambahkan ini
use Illuminate\Support\Facades\App;

class Kas extends Model
{
    use HasFactory, SoftDeletes; // <-- 2. Tambahkan SoftDeletes di sini

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // 3. Tambahkan properti ini untuk mengizinkan Mass Assignment
    protected $fillable = [
        'nama_pengguna',
        'email',
        'peran',
        'saldo',
    ];

    public function transaksi()
    {
        return $this->hasMany(App\Models\TransaksiKas::class);
    }
}

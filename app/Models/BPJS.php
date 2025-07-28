<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BPJS extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // Ubah dari karyawan_id ke user_id
        'no_bpjs',
        'jenis',
        'status',
        'persentase_potongan',
        'iuran_perusahaan',
        'iuran_karyawan'
    ];

    protected $casts = [
        'persentase_potongan' => 'float',
        'iuran_perusahaan' => 'float',
        'iuran_karyawan' => 'float'
    ];

    protected $table = 'bpjs'; // Tambahkan ini

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi langsung ke User/Karyawan
    public function karyawan()
    {
        return $this->belongsTo(User::class, 'user_id'); // Ubah ke user_id
        return $this->belongsTo(User::class, 'karyawan_id');
    }

    // Scope untuk filter mudah
    public function scopeKesehatan($query)
    {
        return $query->where('jenis', 'Kesehatan');
    }

    public function scopeKetenagakerjaan($query)
    {
        return $query->where('jenis', 'Ketenagakerjaan');
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'Aktif');
    }
}
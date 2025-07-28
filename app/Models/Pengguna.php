<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengguna extends Model
{
    use HasFactory;

    protected $table = 'pengguna'; 

    // Pastikan $fillable sesuai dengan kolom-kolom di migration
    protected $fillable = [
        'user_id',
        'nama',
        'nis',
        'jabatan_id',
        'whatsapp', // sudah ditambahkan
        'email',    // sudah ditambahkan
        'alamat'    // sudah ditambahkan
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Jabatan
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    
    public function tasks()
    {
        return $this->hasMany(Task::class, 'user_id', 'user_id'); // Lebih baik relasi via user_id
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kasbon extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'jenis_kasbon',
        'total_kasbon',
        'periode_mulai',
        'periode_selesai',
        'bayar_perbulan',
        'status',
        'keterangan'
    ];

    protected $dates = [
        'periode_mulai',
        'periode_selesai',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'periode_mulai' => 'date',
        'periode_selesai' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
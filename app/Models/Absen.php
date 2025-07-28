<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absen extends Model
{
    protected $table = 'absen';
    
    protected $fillable = [
        'nama',
        'position',
        'kehadiran',
        'tanggal',
        'waktu',
        'keterangan',
    ];

}

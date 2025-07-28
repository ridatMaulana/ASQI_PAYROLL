<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    protected $fillable = [
        'user_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'alasan',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

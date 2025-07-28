<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class subTugas extends Model
{
     use HasFactory;

    protected $guarded = [];

     public function tugas()
    {
        return $this->belongsTo(Task::class);
    }
}
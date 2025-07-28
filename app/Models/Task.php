<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'tasks';  
    protected $fillable = ['title', 'description', 'user_id'];

    // Task.php
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

        public function subTugas()
    {
        return $this->hasMany(SubTugas::class);
    }


}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    public function instruktur()
{
    return $this->belongsTo(Pengguna::class, 'instruktur');
}

}

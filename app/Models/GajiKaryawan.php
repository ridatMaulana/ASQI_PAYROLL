<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GajiKaryawan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'periode',
        'gaji_pokok',       // akan menyimpan nilai integer (5000000)
        'tunjangan',        // akan menyimpan nilai integer (1000000)
        'pajak',
        'bpjs',
        'kasbon',
        'potongan_absen',
        'potongan_lainnya',
        'total_gaji_bersih',
        'kas_id',
    ];

    /**
     * Relasi ke model User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke model Kas untuk mendapatkan data sumber dana.
     */
    public function sumberDana()
    {
        return $this->belongsTo(Kas::class, 'kas_id');
    }

    /**
     * Accessor untuk menghitung gaji bruto secara dinamis.
     */
    protected function gajiBruto(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => ($attributes['gaji_pokok'] ?? 0) + ($attributes['tunjangan'] ?? 0)
        );
    }

    /**
     * Accessor untuk menghitung total potongan secara dinamis.
     */
    protected function totalPotongan(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => ($attributes['pajak'] ?? 0)
                + ($attributes['bpjs'] ?? 0)
                + ($attributes['kasbon'] ?? 0)
                + ($attributes['potongan_absen'] ?? 0)
                + ($attributes['potongan_lainnya'] ?? 0)
        );
    }
}
<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Presensi;
use App\Models\Cuti;
use Illuminate\Database\Eloquent\SoftDeletes;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'users';  // Nama tabel sesuai dengan yang ada di database


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    
    protected $fillable = [
        'name',
        'nis',
        'email',
        'password',
        'peran',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function presensis()
    {
        return $this->hasMany(Presensi::class);
    }

    public function cutis()
    {
        return $this->hasMany(Cuti::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

     public function siswa()
    {
        return $this->hasOne(Siswa::class);
    }

    protected function statusMagang(): Attribute
    {
        return Attribute::make(
            get: function () {
                // Pertama, cek apakah user ini memiliki data siswa.
                // Ini penting untuk user dengan peran 'admin' atau 'karyawan'.
                if (!$this->siswa) {
                    return null; // Atau 'bukan siswa' jika Anda mau
                }

                // Jika punya, panggil accessor 'is_aktif' dari model Siswa
                // yang sudah kita buat sebelumnya.
                return $this->siswa->is_aktif;
            }
        );
    }
    // Tambahkan di bagian relasi lainnya
    public function bpjs()
    {
        return $this->hasMany(BPJS::class, 'user_id');
    }

    public function pajak()
{
    return $this->hasMany(Pajak::class);
}


}
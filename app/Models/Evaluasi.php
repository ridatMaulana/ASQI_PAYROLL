<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nilai_sikap',
        'nilai_kehadiran',
        'judul_skill',
        'nilai_skill',
        'total_rata_rata',
        'tanggal_evaluasi' // ✅ WAJIB ditambahkan
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // HomeController.php atau component Livewire
    public function getChartData()
    {
        // Ambil 5 bulan terakhir
        $lastMonths = now()->subMonths(4)->startOfMonth();

        $data = Evaluasi::where('tanggal_evaluasi', '>=', $lastMonths)
            ->selectRaw('users.name as user, MONTH(tanggal_evaluasi) as month, YEAR(tanggal_evaluasi) as year, ROUND(AVG(total_rata_rata), 2) as avg_score')
            ->join('users', 'users.id', '=', 'evaluasis.user_id')
            ->groupBy('users.name', 'month', 'year')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return response()->json($data);
    }
}

<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Presensi as ModelPresensi;
use App\Models\Cuti;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Presensi extends Component
{
    public $presensiHariIni;
    public $riwayatPresensi = [];
    public $message;
    public $tanggal_mulai, $tanggal_selesai, $alasan;
    public $riwayatCuti = [];
    public $cutiHariIni;
    public $tampilanAktif = 'presensi';

    protected $rules = [
        'tanggal_mulai' => 'required|date|after_or_equal:today',
        'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        'alasan' => 'nullable|string',
    ];
    
    public function pilihTampilan($tampilan)
    {
        $this->tampilanAktif = $tampilan;
    }

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        // Load presensi hari ini dan riwayat presensi
        $this->presensiHariIni = $user->presensis()->whereDate('tanggal', $today)->first();
        $this->riwayatPresensi = $user->presensis()->latest()->take(30)->get();

        // Load cuti hari ini dan riwayat cuti
        $this->cutiHariIni = $user->cutis()
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->first();

        $this->riwayatCuti = $user->cutis()->latest()->take(30)->get();
    }

    public function ajukanCuti()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        // Jika sudah ada presensi hari ini, tidak boleh cuti
        if ($user->presensis()->whereDate('tanggal', $today)->exists()) {
            $this->message = 'Gagal mengajukan cuti karena Anda sudah presensi hari ini.';
            return;
        }

        $this->validate();

        Cuti::create([
            'user_id' => $user->id,
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai,
            'alasan' => $this->alasan,
        ]);

        session()->flash('message', 'Cuti berhasil diajukan.');

        // Reset inputan form
        $this->reset(['tanggal_mulai', 'tanggal_selesai', 'alasan']);

        // Reload data cuti dan presensi
        $this->loadData();
    }

    public function checkin()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        // Jika sedang cuti, tidak boleh presensi
        if ($user->cutis()
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->exists()) {
            $this->message = 'Tidak bisa check-in karena Anda sedang cuti hari ini.';
            return;
        }

        if ($user->presensis()->whereDate('tanggal', $today)->exists()) {
            $this->message = 'Sudah check-in hari ini.';
            return;
        }

        ModelPresensi::create([
            'user_id' => $user->id,
            'tanggal' => $today,
            'jam_masuk' => Carbon::now()->toTimeString(),
        ]);

        $this->message = 'Berhasil check-in.';
        $this->loadData();
    }

    public function checkout()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $presensi = $user->presensis()->whereDate('tanggal', $today)->first();

        if (!$presensi || $presensi->jam_keluar) {
            $this->message = 'Sudah check-out atau belum check-in.';
            return;
        }

        $presensi->update([
            'jam_keluar' => Carbon::now()->toTimeString(),
        ]);

        $this->message = 'Berhasil check-out.';
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.presensi');
    }
}

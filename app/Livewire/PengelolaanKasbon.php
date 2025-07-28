<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Kasbon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Collection;


class PengelolaanKasbon extends Component
{
    // Properti untuk data dan UI
    public $kasbons;
    public $userRole;
    public $editingKasbonId = null;
    public $pilihanMenu = 'lihat';
    public $detailForModal = [];

    // Properti untuk form
    public $user_id;
    public $jenis_kasbon;
    public $total_kasbon;
    public $periode_selesai;
    public $bayar_perbulan;
    public $status = 'diajukan';
    public $keterangan;
    public $periode_mulai;
    protected $listeners = ['statusUpdated' => 'loadKasbons'];

    public string $karyawanSearch = '';
    public Collection $hasilPencarianKaryawan;

    public function updateStatus($kasbonId, $newStatus)
    {
        if (auth()->user()->peran !== 'admin') {
            abort(403, 'Unauthorized');
        }

        Kasbon::find($kasbonId)->update(['status' => $newStatus]);
        $this->dispatch('statusUpdated');
    }

    public function mount()
    {
        $this->userRole = auth()->user()->peran ?? 'karyawan';
        $this->hasilPencarianKaryawan = collect();
        $this->loadKasbons();
    }

    public function loadKasbons()
    {
        if ($this->userRole === 'admin') {
            $this->kasbons = Kasbon::with('user')->latest()->get();
        } else {
            $this->kasbons = Kasbon::with('user')->where('user_id', Auth::id())->latest()->get();
        }
    }

    public function render()
    {
        return view('livewire.pengelolaan-kasbon');
    }

    public function pilihMenu($menu)
    {
        if ($menu === 'tambah' && $this->pilihanMenu !== 'edit') {
            $this->batal();
        }
        $this->pilihanMenu = $menu;
    }

    public function batal()
    {
        $this->resetForm();
        $this->pilihanMenu = 'lihat';
    }

    public function createKasbon()
    {
        $validatedData = $this->validate([
            'user_id' => 'required|exists:users,id',
            'jenis_kasbon' => 'required|string',
            'total_kasbon' => 'required|integer|min:1',
            'periode_mulai' => 'required|date|before_or_equal:periode_selesai',
            'periode_selesai' => 'required|date|after_or_equal:periode_mulai',
            'bayar_perbulan' => 'required|integer|min:1',
            'status' => 'required|in:diajukan,disetujui,dicairkan,ditolak',
            'keterangan' => 'nullable|string',
        ]);

        Kasbon::create($validatedData);

        session()->flash('message', 'Kasbon berhasil diajukan.');
        $this->batal();
        $this->loadKasbons();
    }

    public function editKasbon($kasbonId)
    {
        $kasbon = Kasbon::findOrFail($kasbonId);

        $this->karyawanSearch = $kasbon->user->name;
        $this->editingKasbonId = $kasbon->id;
        $this->user_id = $kasbon->user_id;
        $this->karyawanSearch = $kasbon->user->name;
        $this->jenis_kasbon = $kasbon->jenis_kasbon;
        $this->total_kasbon = $kasbon->total_kasbon;

        // Tambahkan pengecekan null untuk periode_mulai
        $this->periode_mulai = $kasbon->periode_mulai ? $kasbon->periode_mulai->format('Y-m-d') : null;

        // Tambahkan pengecekan null untuk periode_selesai
        $this->periode_selesai = $kasbon->periode_selesai ? $kasbon->periode_selesai->format('Y-m-d') : null;

        $this->bayar_perbulan = $kasbon->bayar_perbulan;
        $this->status = $kasbon->status;
        $this->keterangan = $kasbon->keterangan;

        $this->pilihanMenu = 'edit';
    }

    public function updateKasbon()
    {
        $validatedData = $this->validate([
            'user_id' => 'required|exists:users,id',
            'jenis_kasbon' => 'required|string',
            'total_kasbon' => 'required|numeric|min:1',
            'periode_mulai' => 'required|date|before_or_equal:periode_selesai',
            'periode_selesai' => 'required|date|after_or_equal:periode_mulai',
            'bayar_perbulan' => 'required|numeric|min:1',
            'status' => 'required|in:diajukan,disetujui,dicairkan,ditolak',
            'keterangan' => 'nullable|string',
        ]);

        $kasbon = Kasbon::findOrFail($this->editingKasbonId);
        $kasbon->update($validatedData);

        $this->kasbons = $this->kasbons->map(function ($item) use ($kasbon) {
            return $item->id == $kasbon->id ? $kasbon : $item;
        });

        session()->flash('message', 'Kasbon berhasil diperbarui.');
        $this->batal();
    }

    public function deleteKasbon($kasbonId)
    {
        Kasbon::destroy($kasbonId);
        session()->flash('message', 'Kasbon berhasil dihapus.');
        $this->loadKasbons();
    }

    public function showKasbonDetail($kasbonId)
    {
        $kasbon = Kasbon::with('user')->findOrFail($kasbonId);

        $this->detailForModal = [
            'nama_karyawan' => $kasbon->user->name,
            'jenis_kasbon' => $kasbon->jenis_kasbon,
            'status' => $kasbon->status,
            'total_kasbon' => number_format($kasbon->total_kasbon, 2),
            'periode_mulai' => $kasbon->periode_mulai ? $kasbon->periode_mulai->format('d F Y') : '-',
            'periode_selesai' => $kasbon->periode_selesai->format('d F Y'),
            'bayar_perbulan' => number_format($kasbon->bayar_perbulan, 2),
            'keterangan' => $kasbon->keterangan,
            'tanggal_pengajuan' => $kasbon->created_at->format('d F Y H:i')
        ];

        $this->dispatch('open-kasbon-detail-modal');
    }

    private function resetForm()
    {
        $this->reset([
            'user_id',
            'jenis_kasbon',
            'total_kasbon',
            'periode_mulai', 
            'periode_selesai',
            'bayar_perbulan',
            'status',
            'keterangan',
            'editingKasbonId',
            'karyawanSearch'
        ]);

        $this->hasilPencarianKaryawan = collect(); 

        $this->resetErrorBag();
    }

    public function updatedPeriodeMulai($value)
    {
        $this->hitungBayarPerbulan();
    }

    public function updatedPeriodeSelesai($value)
    {
        $this->hitungBayarPerbulan();
    }

    public function updatedTotalKasbon($value)
    {
        $this->hitungBayarPerbulan();
    }

    private function hitungBayarPerbulan()
    {
        $totalKasbonNumerik = (int) $this->total_kasbon;
        if ($this->periode_mulai && $this->periode_selesai && $totalKasbonNumerik > 0) {
            try {
                $mulai = Carbon::parse($this->periode_mulai);
                $selesai = Carbon::parse($this->periode_selesai);
                if ($mulai->gt($selesai)) {
                    $this->bayar_perbulan = 0;
                    return;
                }
                $selisihBulan = $mulai->diffInMonths($selesai) + 1;
                $selisihBulan = max(1, $selisihBulan);
                $nilaiPerBulan = $totalKasbonNumerik / $selisihBulan;
                $this->bayar_perbulan = ceil($nilaiPerBulan / 1000) * 1000;
            } catch (\Exception $e) {
                $this->bayar_perbulan = 0;
            }
        } else {
            $this->bayar_perbulan = 0;
        }
    }

    public function updatedKaryawanSearch($value)
    {
        // Berjalan setiap kali user mengetik
        if (!empty($value) && !$this->user_id) {
            $idUserDenganKasbonAktif = Kasbon::where('status', 'dicairkan')->pluck('user_id');
            $this->hasilPencarianKaryawan = User::where('peran', 'karyawan')
                ->where('name', 'like', '%' . $value . '%')
                ->whereNotIn('id', $idUserDenganKasbonAktif)
                ->limit(5)
                ->get();
        } else {
            $this->hasilPencarianKaryawan = collect();
        }
    }

    public function pilihKaryawan($userId)
    {
        // Berjalan saat user mengklik hasil pencarian
        $user = User::findOrFail($userId);
        $this->user_id = $user->id;
        $this->karyawanSearch = $user->name;
        $this->hasilPencarianKaryawan = collect();
    }

    public function updated($propertyName)
    {
        $triggerProperties = ['periode_mulai', 'periode_selesai', 'total_kasbon'];
        if (in_array($propertyName, $triggerProperties)) {
            $this->hitungBayarPerbulan();
        }
    }
}

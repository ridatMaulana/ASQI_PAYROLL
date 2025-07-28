<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Collection; 

class SemuaSiswa extends Component
{
    public $pilihanMenu = 'lihat';
    public $siswaTerpilih;

    public $userTerpilihId = null;
    public $nis;
    public $pendidikan;
    public $alasan;
    public $tanggal_mulai;
    public $tanggal_selesai;
    public $showPerpanjangModal = false;
    public $tanggal_selesai_baru;

    public string $namaSearch = '';
    public Collection $usersUntukDipilih;

    public function mount()
    {

        $this->usersUntukDipilih = collect();
    }

    public function render()
    {
        return view('livewire.semua-siswa', [
            'semuaSiswa' => Siswa::with('user')->latest()->get(),
        ]);
    }

    public function updatedNamaSearch($value)
    {
        if (!empty($value)) {
            $idUserSudahAda = Siswa::pluck('user_id');
            $this->usersUntukDipilih = User::where('peran', 'non-karyawan')
                ->where('name', 'like', '%' . $value . '%')
                ->whereNotIn('id', $idUserSudahAda)
                ->orderBy('name')
                ->limit(5) 
                ->get();
        } else {
            $this->usersUntukDipilih = collect();
        }
    }

    public function pilihUser($userId)
    {
        $user = User::findOrFail($userId);
        
        $this->userTerpilihId = $user->id;
        $this->namaSearch = $user->name;
        $this->nis = $user->nis;
        $this->usersUntukDipilih = collect();
    }

    public function pilihMenu($menu)
    {
        if ($menu === 'tambah' && $this->pilihanMenu !== 'edit') {
            $this->batal();
        }
        $this->pilihanMenu = $menu;
    }

    public function simpan()
    {
        $validatedData = $this->validate([
            'userTerpilihId' => 'required|exists:users,id|unique:siswas,user_id',
            'nis' => 'required|unique:siswas,nis',
            'pendidikan' => 'required|string|max:255',
            'alasan' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $user = User::find($validatedData['userTerpilihId']);
        
        Siswa::create([
            'user_id' => $validatedData['userTerpilihId'],
            'nama' => $user->name,
            'nis' => $validatedData['nis'],
            'pendidikan' => $validatedData['pendidikan'],
            'alasan' => $validatedData['alasan'],
            'tanggal_mulai' => $validatedData['tanggal_mulai'],
            'tanggal_selesai' => $validatedData['tanggal_selesai'],
        ]);

        session()->flash('pesan', 'Data siswa baru berhasil disimpan.');
        $this->batal();
    }

    public function pilihEdit($id)
    {
        $this->siswaTerpilih = Siswa::findOrFail($id);
        $this->userTerpilihId = $this->siswaTerpilih->user_id;
        $this->nis = $this->siswaTerpilih->nis;
        $this->pendidikan = $this->siswaTerpilih->pendidikan;
        $this->alasan = $this->siswaTerpilih->alasan;
        $this->tanggal_mulai = optional($this->siswaTerpilih->tanggal_mulai)->format('Y-m-d');
        $this->tanggal_selesai = optional($this->siswaTerpilih->tanggal_selesai)->format('Y-m-d');
        $this->pilihanMenu = 'edit';
    }

    public function simpanEdit()
    {
        $validatedData = $this->validate([
            'nis' => 'required|unique:siswas,nis,' . $this->siswaTerpilih->id,
            'pendidikan' => 'required|string|max:255',
            'alasan' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $this->siswaTerpilih->update($validatedData);
        session()->flash('pesan', 'Data siswa berhasil diperbarui.');
        $this->batal();
    }

    public function pilihHapus($id)
    {
        $this->siswaTerpilih = Siswa::findOrFail($id);
        $this->pilihanMenu = 'hapus';
    }

    public function hapus()
    {
        $this->siswaTerpilih->delete();
        session()->flash('pesan', 'Data siswa berhasil dihapus.');
        $this->batal();
    }

    public function batal()
    {
        $this->reset();
        $this->usersUntukDipilih = collect();
        $this->pilihanMenu = 'lihat';
    }

    public function bukaModalPerpanjang($id)
    {
        $this->siswaTerpilih = Siswa::findOrFail($id);
        $this->tanggal_selesai_baru = optional($this->siswaTerpilih->tanggal_selesai)->format('Y-m-d');
        $this->showPerpanjangModal = true;
    }

    public function perpanjangMasaMagang()
    {
        $this->validate([
            'tanggal_selesai_baru' => 'required|date|after_or_equal:' . $this->siswaTerpilih->tanggal_selesai->format('Y-m-d')
        ]);

        $this->siswaTerpilih->update([
            'tanggal_selesai' => $this->tanggal_selesai_baru,
            'aktif' => true,
        ]);

        session()->flash('pesan', 'Masa magang untuk ' . $this->siswaTerpilih->nama . ' berhasil diperpanjang.');
        $this->tutupModalPerpanjang();
    }

    public function tutupModalPerpanjang()
    {
        $this->showPerpanjangModal = false;
        $this->reset('siswaTerpilih', 'tanggal_selesai_baru');
    }
}
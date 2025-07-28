<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pengguna as ModelPengguna;
use App\Models\Jabatan;
use Livewire\WithPagination;


class Pengguna extends Component
{
    use WithPagination;

    public $pilihanMenu = "lihat";

    // Data pengguna
    public $nama;
    public $nis;
    public $jabatan; // ⚠️ Ini bisa dihapus kalau sudah pakai relasi jabatan_id
    public $penggunaTerpilih;

    public $whatsapp;
    public $email;
    public $alamat;

    // Untuk input dropdown
    public $jabatanTerpilihId;

  
    public $namaJabatan;

    // Untuk select box
    public $daftarJabatan = [];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->daftarJabatan = Jabatan::all();
    }

    public function render()
    {
        return view('pengguna', [
            'penggunas' => ModelPengguna::with([ 'jabatan'])->get(),
            'jabatans' => $this->daftarJabatan,
        ]);
    }

    public function pilihMenu($menu)
    {
        $this->pilihanMenu = $menu;
    }

    public function simpan()
    {
        $this->validate([
            'nama' => 'required',
            'nis' => 'required|unique:pengguna,nis',
            'jabatanTerpilihId' => 'required|exists:jabatans,id',
        ]);

        ModelPengguna::create([
            'nama' => $this->nama,
            'nis' => $this->nis,
            'jabatan_id' => $this->jabatanTerpilihId,
        ]);

        $this->reset(['nama', 'nis', 'jabatanTerpilihId']);
        $this->pilihanMenu = "lihat";
        session()->flash('pesan', 'Data berhasil disimpan');
    }

    public function pilihEdit($id)
    {
        $this->penggunaTerpilih = ModelPengguna::findOrFail($id);
        $this->nama = $this->penggunaTerpilih->nama;
        $this->nis = $this->penggunaTerpilih->nis;
        $this->jabatanTerpilihId = $this->penggunaTerpilih->jabatan_id;
        $this->pilihanMenu = "edit";
    }

    public function simpanEdit()
    {
        $this->validate([
            'nama' => 'required',
            'nis' => 'required|unique:pengguna,nis,' . $this->penggunaTerpilih->id,
            'jabatanTerpilihId' => 'required|exists:jabatans,id',
        ]);

        $this->penggunaTerpilih->update([
            'nama' => $this->nama,
            'nis' => $this->nis,
            'jabatan_id' => $this->jabatanTerpilihId,
        ]);

        $this->reset(['nama', 'nis', 'jabatanTerpilihId']);
        $this->pilihanMenu = "lihat";
        session()->flash('pesan', 'Data berhasil diperbarui');
    }

    public function pilihHapus($id)
    {
        $this->penggunaTerpilih = ModelPengguna::findOrFail($id);
        $this->pilihanMenu = "hapus";
    }

    public function hapus()
    {
        $this->penggunaTerpilih->delete();
        $this->reset();
        $this->pilihanMenu = "lihat";
        session()->flash('pesan', 'Data berhasil dihapus');
    }

    public function batal()
    {
        $this->reset();
        $this->pilihanMenu = "lihat";
    }

    public function simpanJabatan()
    {
        $this->validate([
            'namaJabatan' => 'required|string|max:255',
        ]);

        Jabatan::create([
            'nama' => $this->namaJabatan,
        ]);

        $this->namaJabatan = '';
        $this->loadData();
        session()->flash('pesanJabatan', 'Berhasil menambah jabatan');
    }

    public function hapusJabatan($id)
    {
        Jabatan::findOrFail($id)->delete();
        $this->loadData();
        session()->flash('pesanJabatan', 'Jabatan berhasil dihapus');
    }
}

<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Absen as ModelAbsen;

class Absen extends Component
{
    public $pilihanMenu = "lihat";
    public $nama;
    public $position;
    public $kehadiran;
    public $keterangan;
    public $penggunaTerpilih;

    public function render()
    {
        return view('livewire.absen', [
            'absensi' => ModelAbsen::all(),
        ]);
    }

    public function pilihMenu($menu)
    {
        $this->resetForm(); // Reset semua input setiap pindah menu
        $this->pilihanMenu = $menu;
    }

    public function simpan()
    {
        $this->validate([
            'nama' => 'required',
            'position' => 'required',
            'kehadiran' => 'required',
            'keterangan' => 'nullable|string|max:255'
        ], [
            'nama.required' => 'Nama tidak boleh kosong',
            'position.required' => 'Jabatan tidak boleh kosong',
            'kehadiran.required' => 'Kehadiran tidak boleh kosong',
        ]);

        ModelAbsen::create([
            'nama' => $this->nama,
            'position' => $this->position,
            'kehadiran' => $this->kehadiran,
            'tanggal' => now('Asia/Jakarta')->format('Y-m-d'),
            'waktu' => now('Asia/Jakarta')->format('H:i'),
            'keterangan' => $this->keterangan,
        ]);

        $this->resetForm();
        $this->pilihanMenu = "lihat";
        session()->flash('pesan', 'Data berhasil disimpan');
    }

    public function pilihEdit($id)
    {
        $this->penggunaTerpilih = ModelAbsen::findOrFail($id);
        $this->nama = $this->penggunaTerpilih->nama;
        $this->position = $this->penggunaTerpilih->position;
        $this->kehadiran = $this->penggunaTerpilih->kehadiran;
        $this->keterangan = $this->penggunaTerpilih->keterangan;
        $this->pilihanMenu = "edit";
    }

    public function simpanEdit()
    {
        $this->validate([
            'nama' => 'required',
            'position' => 'required',
            'kehadiran' => 'required',
        ]);

        $edit = $this->penggunaTerpilih;
        $edit->nama = $this->nama;
        $edit->position = $this->position;
        $edit->kehadiran = $this->kehadiran;
        $edit->keterangan = $this->keterangan;
        $edit->save();

        $this->resetForm();
        $this->pilihanMenu = "lihat";
        session()->flash('pesan', 'Data berhasil diperbarui');
    }

    public function pilihHapus($id)
    {
        $this->penggunaTerpilih = ModelAbsen::findOrFail($id);
        $this->pilihanMenu = "hapus";
    }

    public function hapus()
    {
        if ($this->penggunaTerpilih) {
            $this->penggunaTerpilih->delete();
        }

        $this->resetForm();
        $this->pilihanMenu = "lihat";
        session()->flash('pesan', 'Data berhasil dihapus');
    }

    public function batal()
    {
        $this->resetForm();
        $this->pilihanMenu = "lihat";
    }

    private function resetForm()
    {
        $this->reset(['nama', 'position', 'kehadiran', 'keterangan', 'penggunaTerpilih']);
    }

    public function hapusSemua()
    {
        \App\Models\Absen::truncate(); 
        session()->flash('pesan', 'Semua data absensi berhasil dihapus.');
    }

}

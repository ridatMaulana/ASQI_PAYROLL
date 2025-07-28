<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Produk as ModelProduk;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\Produk as imporProduk;

class Produk extends Component
{
    use WithFileUploads;
    public $pilihanMenu = "lihat";
    public $instruktur;
    public $sekolah;
    public $pembimbing;
    public $siswa;
    public $ket;
    public $produkTerpilih;
    public $daftarInstruktur = [];
    public $namaInstruktur;



    public function pilihEdit($id)
    {
        $this->produkTerpilih = ModelProduk::findOrFail($id);
        $this->instruktur = $this->produkTerpilih->instruktur;
        $this->sekolah = $this->produkTerpilih->sekolah;
        $this->pembimbing = $this->produkTerpilih->pembimbing;
        $this->siswa = $this->produkTerpilih->siswa;
        $this->ket = $this->produkTerpilih->ket;
        $this->pilihanMenu = "edit";
    }
    public function simpanEdit()
    {
        $this->validate([
            'instruktur' => ['required', 'unique:produks,instruktur,'.$this->produkTerpilih->id],
            'sekolah' => 'required',
            'pembimbing' => 'required',
            'siswa' => 'required',
            'ket' => 'required'
        ], [
            'instruktur.required' => 'Nama Instruktur tidak boleh kosong',
            'sekolah.required' => 'Nama Sekolah tidak boleh kosong',
            'pembimbing.required' => 'Nama Pembimbing tidak boleh kosong',
            'siswa.required' => 'Jumlah Siswa tidak boleh kosong',
            'ket.required' => 'Keterangan tidak boleh kosong'
        ]);

        $simpan = $this->produkTerpilih;
        $simpan->instruktur = $this->instruktur;
        $simpan->sekolah = $this->sekolah;
        $simpan->pembimbing = $this->pembimbing;
        $simpan->siswa = $this->siswa;
        $simpan->ket = $this->ket;
        $simpan->save();

        $this->reset('instruktur', 'sekolah', 'pembimbing', 'siswa', 'ket');
        $this->pilihanMenu = "lihat";
        session()->flash('pesan', 'Data berhasil disimpan');
    }
    public function pilihHapus($id)
    {
        $this->produkTerpilih = ModelProduk::findOrFail($id);
        $this->pilihanMenu = "hapus";
    }
    public function batal()
    {
        $this->reset();
    }
    public function hapus()
    {
        $this->produkTerpilih->delete();
        $this->pilihanMenu = "lihat";
    }

    public function simpan()
    {
        $this->validate([
            'instruktur' => 'required',
            'sekolah' => 'required',
            'pembimbing' => 'required',
            'siswa' => 'required',
            'ket' => 'required'
        ], [
            'instruktur.required' => 'Nama Instruktur tidak boleh kosong',
            'sekolah.required' => 'Nama Sekolah tidak boleh kosong',
            'pembimbing.required' => 'Nama Pembimbing tidak boleh kosong',
            'siswa.required' => 'Nama Siswa tidak boleh kosong',
            'ket.required' => 'Keterangan tidak boleh kosong'
        ]);

        $simpan = new ModelProduk();
        $simpan->instruktur = $this->instruktur;
        $simpan->sekolah = $this->sekolah;
        $simpan->pembimbing = $this->pembimbing;
        $simpan->siswa = $this->siswa;
        $simpan->ket = $this->ket;
        $simpan->save();

        $this->reset(['instruktur', 'sekolah', 'pembimbing', 'siswa', 'ket']);
        $this->pilihanMenu = "lihat";
        session()->flash('pesan', 'Data berhasil disimpan');
    }
    public function pilihMenu($menu)
    {
        $this->pilihanMenu = $menu;
    }
    public function render()
    {
        return view('livewire.produk')->with([
            'semuaProduk' => ModelProduk::all()
        ]);
    }
    public function mount()
    {
        $this->daftarInstruktur = \App\Models\Pengguna::where('jabatan', 'pegawai')->get();

    }
}

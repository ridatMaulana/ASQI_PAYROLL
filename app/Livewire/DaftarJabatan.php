<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pengguna as ModelPengguna;
use App\Models\Jabatan;
use Livewire\WithPagination;

class DaftarJabatan extends Component
{
    use WithPagination;

    // Properti yang sudah ada
    public $pilihanMenu = "lihat";
    public $nama;
    public $nis;
    public $penggunaTerpilih;
    public $jabatanTerpilihId;
    public $namaJabatan;
    public $daftarJabatan = [];

    // --- PROPERTI BARU DITAMBAHKAN UNTUK EDIT JABATAN ---
    public $editingJabatanId = null;
    public $editingJabatanNama = '';

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        // Data diurutkan agar tampilan lebih rapi
        $this->daftarJabatan = Jabatan::orderBy('nama')->get();
    }

    public function render()
    {
        return view('daftar-jabatan', [
            'jabatans' => $this->daftarJabatan,
        ]);
    }

    // --- Method-method ini terkait dengan manajemen PENGGUNA, bukan jabatan. Anda bisa menghapusnya jika komponen ini hanya untuk jabatan ---
    public function pilihMenu($menu) { $this->pilihanMenu = $menu; }
    public function simpan() { /* ... kode simpan pengguna ... */ }
    public function pilihEdit($id) { /* ... kode pilih edit pengguna ... */ }
    public function simpanEdit() { /* ... kode simpan edit pengguna ... */ }
    public function pilihHapus($id) { /* ... kode pilih hapus pengguna ... */ }
    public function hapus() { /* ... kode hapus pengguna ... */ }
    public function batal() { /* ... kode batal pengguna ... */ }


    // --- Method untuk manajemen JABATAN ---
    public function simpanJabatan()
    {
        // Ditambahkan validasi 'unique' untuk mencegah duplikat
        $this->validate([
            'namaJabatan' => 'required|string|max:255|unique:jabatans,nama',
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

    // --- METHOD BARU DITAMBAHKAN UNTUK EDIT JABATAN ---

    /**
     * Mengaktifkan mode edit untuk sebuah jabatan.
     * Dipanggil saat tombol pensil diklik.
     */
    public function editJabatan($jabatanId)
    {
        $jabatan = Jabatan::findOrFail($jabatanId);
        $this->editingJabatanId = $jabatan->id;
        $this->editingJabatanNama = $jabatan->nama;
    }

    /**
     * Membatalkan mode edit jabatan.
     * Dipanggil saat tombol Batal (X) diklik.
     */
    public function batalEditJabatan()
    {
        $this->reset('editingJabatanId', 'editingJabatanNama');
    }

    /**
     * Menyimpan perubahan nama jabatan ke database.
     * Dipanggil saat tombol Simpan (✓) diklik.
     */
    public function updateJabatan()
    {
        // Validasi 'unique' dengan pengecualian untuk ID yang sedang diedit
        $this->validate([
            'editingJabatanNama' => 'required|min:3|unique:jabatans,nama,' . $this->editingJabatanId
        ]);

        $jabatan = Jabatan::findOrFail($this->editingJabatanId);
        $jabatan->update(['nama' => $this->editingJabatanNama]);

        $this->batalEditJabatan(); // Reset state setelah berhasil
        $this->loadData();
        session()->flash('pesanJabatan', 'Nama jabatan berhasil diperbarui.');
    }
}
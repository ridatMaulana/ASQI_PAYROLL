<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User as ModelUser;
use Livewire\WithPagination; // Tambahkan ini untuk pagination
use Illuminate\Validation\Rule;

class User extends Component
{
    use WithPagination; // Gunakan trait pagination

    public $pilihanMenu = "lihat";
    public $tampilkanTrash = false;
    public $nis;
    public $nama;
    public $email;
    public $password;
    public $peran;
    public $penggunaTerpilih;
    public $password_confirmation;

    public bool $konfirmasiHapusSemua = false;

    // Gunakan protected $listeners untuk event
    protected $listeners = ['hapusPermanen'];

    public function mount()
    {
        if (auth()->user()->peran !== "admin") {
            abort(403);
        }
    }

    // Mengganti halaman/mode tampilan
    public function tampilkanMenuTrash($status)
    {
        $this->tampilkanTrash = $status;
        $this->pilihanMenu = "lihat"; // Selalu reset ke 'lihat' saat ganti menu
        $this->resetPage(); // Reset pagination saat ganti menu
    }

    public function pilihEdit($id)
    {
        // Tetap gunakan withTrashed agar bisa edit data yg di trash sebelum direstore
        $this->penggunaTerpilih = ModelUser::withTrashed()->findOrFail($id);
        $this->nama = $this->penggunaTerpilih->name;
        $this->email = $this->penggunaTerpilih->email;
        $this->peran = $this->penggunaTerpilih->peran;
        $this->nis = $this->penggunaTerpilih->nis;
        $this->pilihanMenu = "edit";
    }

    public function simpanEdit()
    {
        // Validasi tidak perlu diubah, sudah benar
        $this->validate([
            'nama' => 'required',
            'nis' => 'required|string|max:255|unique:users,nis,' . $this->penggunaTerpilih->id,
            'email' => ['required', 'email', 'unique:users,email,' . $this->penggunaTerpilih->id],
            'peran' => 'required',
            // 'password' => 'nullable|min:6'
        ]);

        $simpan = $this->penggunaTerpilih;
        $simpan->name = $this->nama;
        $simpan->nis = $this->nis;
        $simpan->email = $this->email;
        $simpan->peran = $this->peran;

        // if ($this->password) {
        //     $simpan->password = bcrypt($this->password);
        // }

        $simpan->save();
        $this->batal(); // Gunakan method batal
        session()->flash('pesan', 'Data berhasil diperbarui');
    }

    public function pilihHapus($id)
    {
        // Fungsi ini untuk soft delete, jadi cari dari data yang belum dihapus
        $this->penggunaTerpilih = ModelUser::findOrFail($id);
        $this->pilihanMenu = "hapus";
    }

    public function hapus()
    {
        // Ini adalah soft delete
        $this->penggunaTerpilih->delete();
        $this->batal();
        session()->flash('pesan', 'Data berhasil dipindahkan ke tempat sampah.');
    }

    // --- FUNGSI BARU UNTUK TRASH ---

    public function pilihRestore($id)
    {
        $this->penggunaTerpilih = ModelUser::withTrashed()->findOrFail($id);
        $this->pilihanMenu = "restore";
    }

    public function restore()
    {
        $this->penggunaTerpilih->restore();
        $this->batal();
        session()->flash('pesan', 'Data berhasil dipulihkan.');
    }

    public function pilihHapusPermanen($id)
    {
        $this->penggunaTerpilih = ModelUser::withTrashed()->findOrFail($id);
        $this->pilihanMenu = "hapus_permanen";
    }

    public function hapusPermanen()
    {
        $this->penggunaTerpilih->forceDelete();
        $this->batal();
        session()->flash('pesan', 'Data berhasil dihapus secara permanen.');
    }

    // --- FUNGSI SIMPAN & BATAL ---

    public function simpan()
    {
        $this->validate([
            'nama' => 'required',
            'nis' => 'required|string|max:255|unique:users,nis',
            'email' => ['required', 'email', 'unique:users,email'],
            'peran' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $simpan = new ModelUser();
        $simpan->name = $this->nama;
        $simpan->nis = $this->nis;
        $simpan->email = $this->email;
        $simpan->password = bcrypt($this->password);
        $simpan->peran = $this->peran;
        $simpan->save();

        $this->batal();
        session()->flash('pesan', 'Data baru berhasil disimpan.');
    }

    public function batal()
    {
        $this->reset(['nama',  'nis', 'email', 'peran', 'penggunaTerpilih']);
        $this->pilihanMenu = "lihat";
        $this->konfirmasiHapusSemua = false;
    }

    public function pilihMenu($menu)
    {
        $this->pilihanMenu = $menu;
        // Reset field saat pindah ke menu 'tambah'
        if ($menu === 'tambah') {
            $this->reset(['nama', 'nis', 'email', 'peran', 'penggunaTerpilih']);
        }
    }

    public function render()
    {
        $query = ModelUser::query();

        if ($this->tampilkanTrash) {
            // Jika mode trash, tampilkan hanya yang di-soft-delete
            $query->onlyTrashed();
        }

        return view('livewire.user', [
            // Gunakan pagination untuk performa lebih baik
            'semuaPengguna' => $query->latest()->paginate(10)
        ]);
    }

    // --- METHOD UNTUK HAPUS SEMUA ---
    public function konfirmasiHapusSemuaPermanen()
    {
        $this->batal();
        $this->konfirmasiHapusSemua = true;
    }

    public function hapusSemuaPermanen()
    {
        $dataTerhapus = ModelUser::onlyTrashed()->get();
        if ($dataTerhapus->isNotEmpty()) {
            foreach ($dataTerhapus as $item) {
                $item->forceDelete();
            }
            session()->flash('pesan', 'Semua data dari tempat sampah berhasil dihapus permanen.');
        } else {
            session()->flash('pesan', 'Tidak ada data di tempat sampah untuk dihapus.');
        }
        $this->batal();
    }
}

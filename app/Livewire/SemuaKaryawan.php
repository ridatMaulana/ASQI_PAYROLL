<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pengguna as ModelPengguna;
use App\Models\User;
use App\Models\Jabatan;
use Livewire\WithPagination;

class SemuaKaryawan extends Component
{
    use WithPagination;

    // --- Properti State & Tampilan ---
    public $pilihanMenu = "lihat";
    public $penggunaTerpilih;
    public $daftarJabatan = [];

    // --- Properti Form ---
    public $userTerpilihId; // Akan diisi saat user dipilih dari hasil pencarian
    public $nama;           // Diisi saat edit, atau sebagai display saat tambah
    public $nis;
    public $jabatanTerpilihId;
    public $whatsapp = '';
    public $email = '';
    public $alamat = '';

    // =======================================================
    // --- PROPERTI BARU UNTUK FITUR PENCARIAN ---
    // =======================================================
    /** @var string Properti untuk menampung teks dari input pencarian */
    public $namaSearch = '';

    /** @var \Illuminate\Database\Eloquent\Collection Hasil pencarian user */
    public $usersUntukDipilih;
    // =======================================================


    /**
     * Method mount berjalan sekali saat komponen dimuat.
     */
    public function mount()
    {
        $this->loadData();
        // Inisialisasi usersUntukDipilih sebagai koleksi kosong
        $this->usersUntukDipilih = collect();
    }

    public function loadData()
    {
        $this->daftarJabatan = Jabatan::orderBy('nama')->get();
    }

    /**
     * Method render adalah sumber data utama untuk view.
     * Logika pencarian dinamis ditempatkan di sini.
     */
    public function render()
    {
        // --- LOGIKA PENCARIAN DINAMIS ---
        // Hanya jalankan query jika ada teks di input pencarian DAN belum ada user yang dipilih
        if (!empty($this->namaSearch) && !$this->userTerpilihId) {
            $idUserSudahAda = ModelPengguna::pluck('user_id');

            $this->usersUntukDipilih = User::where('peran', 'karyawan')
                ->where('name', 'like', '%' . $this->namaSearch . '%')
                ->whereNotIn('id', $idUserSudahAda)
                ->orderBy('name')
                ->limit(5) // Batasi hasil untuk performa
                ->get();
        } else {
            // Kosongkan hasil jika tidak ada pencarian atau user sudah dipilih
            $this->usersUntukDipilih = collect();
        }

        // Mengambil data untuk tabel utama
        $penggunas = ModelPengguna::with(['jabatan'])->latest()->get();

        return view('livewire.semua-karyawan', [
            'penggunas' => $penggunas,
        ]);
    }

    // =======================================================
    // --- METHOD BARU: Untuk memilih user dari hasil pencarian ---
    // =======================================================
    /**
     * Method ini dipanggil oleh `wire:click` saat user memilih nama dari daftar.
     */
    public function pilihUser($userId)
    {
        $user = User::find($userId);
        if ($user) {
            $this->userTerpilihId = $user->id;
            $this->namaSearch = $user->name; // Isi input dengan nama lengkap yang dipilih
            $this->nis = $user->nis;         // Otomatis isi NIS
            $this->email = $user->email;     // Otomatis isi Email
            $this->usersUntukDipilih = collect(); // Sembunyikan daftar hasil pencarian
        }
    }


    /**
     * Method lama 'updatedUserTerpilihId' dan 'getUsersUntukDipilihProperty' tidak lagi diperlukan
     * dan telah dihapus untuk menghindari kebingungan.
     */
    public function updatedUserTerpilihId($userId)
    {
        if ($userId) {
            // Ambil data lengkap user yang dipilih
            $user = User::find($userId);
            if ($user) {
                // Isi semua properti form dengan data dari user
                $this->nama = $user->name;
                $this->nis = $user->nis;
                $this->email = $user->email;         // <-- INI DIA, EMAIL OTOMATIS TERISI
                // $this->whatsapp = $user->whatsapp;   // <-- BONUS: WHATSAPP JUGA TERISI
                // $this->alamat = $user->alamat;       // <-- BONUS: ALAMAT JUGA TERISI
            }
        } else {
            // Jika pilihan dropdown dikosongkan, reset semua field terkait
            $this->nama = '';
            $this->nis = '';
            $this->email = '';
            // $this->whatsapp = '';
            // $this->alamat = '';
        }
    }

    // --- AKSI & LOGIKA UTAMA ---

    public function pilihMenu($menu)
    {
        if ($menu === 'tambah' && $this->pilihanMenu !== 'edit') {
            $this->batal(); // Reset form jika pindah ke tab 'tambah'
        }
        $this->pilihanMenu = $menu;
    }

    public function simpan()
    {
        // Validasi pastikan userTerpilihId ada (sudah dipilih dari daftar)
        $validatedData = $this->validate([
            'userTerpilihId' => 'required|exists:users,id|unique:pengguna,user_id',
            'nis' => 'required|unique:pengguna,nis',
            'jabatanTerpilihId' => 'required|exists:jabatans,id',
            'whatsapp' => 'nullable|string|max:15',
            'email' => 'nullable|email|unique:pengguna,email',
            'alamat' => 'nullable|string|min:10',
        ]);

        // Ambil nama dari user yang dipilih
        $user = User::find($validatedData['userTerpilihId']);

        ModelPengguna::create([
            'user_id' => $validatedData['userTerpilihId'],
            'nama' => $user->name, // Ambil nama dari relasi User
            'nis' => $validatedData['nis'],
            'jabatan_id' => $validatedData['jabatanTerpilihId'],

            // =======================================================
            // --- TAMBAHKAN 3 BARIS INI ---
            // =======================================================
            'whatsapp' => $this->whatsapp,
            'email' => $this->email,
            'alamat' => $this->alamat,
        ]);

        session()->flash('pesan', 'Data karyawan baru berhasil disimpan.');
        $this->batal();
    }

    public function pilihEdit($id)
    {
        $this->penggunaTerpilih = ModelPengguna::findOrFail($id);
        $this->userTerpilihId = $this->penggunaTerpilih->user_id;
        $this->nama = $this->penggunaTerpilih->nama;
        $this->nis = $this->penggunaTerpilih->nis;
        $this->whatsapp = $this->penggunaTerpilih->whatsapp;
        $this->email = $this->penggunaTerpilih->email;
        $this->alamat = $this->penggunaTerpilih->alamat;
        $this->jabatanTerpilihId = $this->penggunaTerpilih->jabatan_id; // <-- Baris ini penting untuk edit
        $this->pilihanMenu = "edit";
    }

    public function simpanEdit()
    {
        $validatedData = $this->validate([
            'nama' => 'required',
            'nis' => 'required|unique:pengguna,nis,' . $this->penggunaTerpilih->id,
            'jabatanTerpilihId' => 'required|exists:jabatans,id',
            'whatsapp' => 'nullable|string|max:15',
            'email' => 'nullable|email|unique:pengguna,email,' . $this->penggunaTerpilih->id,
            'alamat' => 'nullable|string|min:10',
        ]);

        $this->penggunaTerpilih->update([
            'nama' => $validatedData['nama'],
            'nis' => $validatedData['nis'],
            'jabatan_id' => $validatedData['jabatanTerpilihId'],
            'whatsapp' => $this->whatsapp,
            'email' => $this->email,
            'alamat' => $this->alamat,
        ]);

        session()->flash('pesan', 'Data karyawan berhasil diperbarui.');
        $this->batal();
    }

    public function pilihHapus($id)
    {
        $this->penggunaTerpilih = ModelPengguna::findOrFail($id);
        $this->pilihanMenu = "hapus";
    }

    public function hapus()
    {
        $this->penggunaTerpilih->delete();
        session()->flash('pesan', 'Data karyawan berhasil dihapus.');
        $this->batal();
    }

    /**
     * Method Batal sekarang juga me-reset properti pencarian.
     */
    public function batal()
    {
        // reset() akan mengembalikan semua properti publik ke nilai awal.
        // Ini sudah mencakup $namaSearch, $userTerpilihId, dll.
        $this->reset();
        $this->mount(); // Panggil mount untuk me-reload data awal & set state
        $this->pilihanMenu = "lihat";
        $this->resetValidation();
    }

    // Method simpanJabatan tidak perlu diubah
    public function simpanJabatan()
    {
        $this->validate([
            'namaJabatan' => 'required|string|max:255',
        ]);

        Jabatan::create([
            'nama' => $this->namaJabatan,
        ]);

        $this->daftarJabatan = '';
        $this->loadData();
        session()->flash('pesanJabatan', 'Berhasil menambah jabatan');
    }

    public function getUsersUntukDipilihProperty()
    {
        // 1. Ambil semua ID user yang sudah terdaftar sebagai pengguna
        $idUserSudahAda = ModelPengguna::pluck('user_id');

        // 2. Ambil user yang perannya 'karyawan' DAN ID-nya tidak ada di daftar di atas
        return User::where('peran', 'karyawan')
            ->whereNotIn('id', $idUserSudahAda)
            ->orderBy('name')
            ->get();
    }
}

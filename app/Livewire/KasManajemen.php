<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Kas;
use App\Models\Transaksi;
use App\Models\TransaksiKas;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KasManajemen extends Component
{
    use WithPagination;

    // --- PROPERTI STATE ---
    public $pilihanMenu = 'lihat';
    public $tampilkanTrash = false;
    public $kasId, $nama, $saldo;
    public $search = '';

    public $mode = 'daftar';
    public $selectedKas = null;
    public $tanggal, $keterangan, $jenis = 'masuk', $jumlah;

    public bool $konfirmasiHapusSemua = false;

    // --- ATURAN VALIDASI ---
    protected function rules()
    {
        return [
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique('kas', 'nama_pengguna')->ignore($this->kasId),
            ],
        ];
    }

    protected function transaksiRules()
    {
        return [
            'tanggal' => 'required|date',
            'keterangan' => 'required|string|max:255',
            'jenis' => 'required|in:masuk,keluar',
            'jumlah' => 'required|numeric|min:1',
        ];
    }

    // --- FUNGSI RENDER UTAMA ---
    // app/Livewire/KasManajemen.php

    // app/Livewire/KasManajemen.php

    public function render()
    {
        if ($this->mode == 'transaksi') {
            // =============================================================
            // BAGIAN INI YANG DIPERBAIKI
            // =============================================================
            $transaksis = TransaksiKas::where('kas_id', $this->selectedKas->id)

                // 1. Urutkan berdasarkan tanggal, dari yang paling baru
                // ->orderBy('tanggal', 'desc')
                // 2. Jika tanggal sama, urutkan berdasarkan waktu pembuatan
                ->latest()
                ->orderBy('created_at', 'desc')
                // HAPUS ->orderBy('tanggal', 'desc')
                // SISAKAN HANYA INI UNTUK MENGURUTKAN BERDASARKAN WAKTU SIMPAN
                ->latest()
                // HAPUS ->orderBy('tanggal', 'desc')
                // SISAKAN HANYA INI UNTUK MENGURUTKAN BERDASARKAN WAKTU SIMPAN
                ->latest()
                ->paginate(10, ['*'], 'transaksiPage');

            // PENTING: Jangan kirim $selectedKas lagi karena sudah jadi properti publik
            return view('livewire.kas-manajemen', ['transaksis' => $transaksis]);
        } else {
            // ... sisa kode tidak perlu diubah ...
            $query = Kas::query();

            if ($this->tampilkanTrash) {
                $query->onlyTrashed();
            }
            if ($this->search) {
                $query->where('nama_pengguna', 'like', '%' . $this->search . '%');
            }

            $semuaKas = $query->latest()->paginate(10, ['*'], 'kasPage');

            // PENTING: Jangan kirim $selectedKas lagi karena sudah jadi properti publik
            return view('livewire.kas-manajemen', ['semuaKas' => $semuaKas]);
        }
    }

    // --- MANAJEMEN MODE ---
    public function lihatTransaksi($id)
    {
        $this->selectedKas = Kas::findOrFail($id);
        $this->mode = 'transaksi';
        $this->resetInputTransaksi();
    }

    public function kembaliKeDaftar()
    {
        $this->selectedKas = null;
        $this->mode = 'daftar';
        $this->resetPage('kasPage');
    }

    // --- LOGIKA TRANSAKSI ---
    public function simpanTransaksi()
    {
        // 1. Validasi input dari form
        $validatedData = $this->validate($this->transaksiRules());

        // 2. Gunakan DB Transaction untuk memastikan proses aman
        DB::transaction(function () use ($validatedData) {

            // 3. Simpan data ke tabel 'mutasi_kas'
            TransaksiKas::create([
                'kas_id'     => $this->selectedKas->id,
                'kode'       => 'MANUAL-' . now()->format('Ymd') . '-' . strtoupper(substr(md5(time() . rand()), 0, 5)),
                'tanggal'    => $validatedData['tanggal'],
                'keterangan' => $validatedData['keterangan'],
                'jenis'      => $validatedData['jenis'],
                'jumlah'     => $validatedData['jumlah'],
            ]);

            // 4. Update saldo di akun kas terkait
            if ($validatedData['jenis'] == 'masuk') {
                $this->selectedKas->increment('saldo', $validatedData['jumlah']);
            } else {
                $this->selectedKas->decrement('saldo', $validatedData['jumlah']);
            }
        });

        // 5. Kirim pesan sukses ke pengguna
        session()->flash('pesan_transaksi', 'Transaksi berhasil disimpan!');

        // 6. Reset input form agar bersih kembali
        $this->resetInputTransaksi();

        // 7. [OPSIONAL TAPI DIANJURKAN] Refresh data di tabel riwayat tanpa perlu reload
        // Ini akan langsung menampilkan data yang baru saja diinput
        // app/Livewire/KasManajemen.php -> akhir method simpanTransaksi()
        $this->transaksis = TransaksiKas::where('kas_id', $this->selectedKas->id)->latest()->paginate(10, ['*'], 'transaksiPage');
    }

    public function resetInputTransaksi()
    {
        $this->tanggal = now();
        $this->keterangan = '';
        $this->jenis = 'masuk';
        $this->jumlah = '';
        $this->resetErrorBag();
    }

    // --- CRUD & AKSI UNTUK AKUN KAS (BANK) ---
    public function tampilkanMenuTrash($status)
    {
        $this->tampilkanTrash = $status;
        $this->batal();
    }

    // ================== PERBAIKAN LOGIKA ADA DI SINI ==================
    public function pilihMenu($menu)
    {
        $this->pilihanMenu = $menu;
        // Pastikan kita tidak lagi berada di mode "trash" saat masuk ke mode form.
        $this->tampilkanTrash = false;
        $this->resetInput();
    }

    public function pilihEdit($id)
    {
        $kas = Kas::findOrFail($id);
        $this->kasId = $kas->id;
        $this->nama = $kas->nama_pengguna;
        $this->saldo = $kas->saldo;
        $this->pilihanMenu = 'edit';
        // Pastikan kita tidak lagi berada di mode "trash" saat masuk ke mode form.
        $this->tampilkanTrash = false;
    }
    // ================== AKHIR DARI PERBAIKAN LOGIKA ==================

    public function resetInput()
    {
        $this->kasId = null;
        $this->nama = '';
        $this->saldo = '';
        $this->resetErrorBag();
    }

    public function batal()
    {
        $this->pilihanMenu = 'lihat';
        $this->resetInput();
        $this->konfirmasiHapusSemua = false;
    }

    public function simpan()
    {
        $this->validate();
        Kas::create([
            'nama_pengguna' => $this->nama,
            'email' => 'bank@example.com',
            'peran' => 'bank',
            'saldo' => 0,
        ]);
        session()->flash('pesan', 'Akun bank berhasil dibuat!');
        $this->batal();
    }

    public function simpanEdit()
    {
        $this->validate();
        Kas::find($this->kasId)->update(['nama_pengguna' => $this->nama]);
        session()->flash('pesan', 'Nama bank berhasil diupdate!');
        $this->batal();
    }

    // --- FUNGSI AKSI LAINNYA (Hapus, Restore, dll) ---
    public function pilihHapus($id)
    {
        $kas = Kas::findOrFail($id);
        $this->kasId = $kas->id;
        $this->nama = $kas->nama_pengguna;
        $this->pilihanMenu = 'hapus';
    }

    public function hapus()
    {
        Kas::find($this->kasId)->delete();
        session()->flash('pesan', 'Akun bank berhasil dihapus!');
        $this->batal();
    }

    public function pilihRestore($id)
    {
        $kas = Kas::withTrashed()->findOrFail($id);
        $this->kasId = $kas->id;
        $this->nama = $kas->nama_pengguna;
        $this->pilihanMenu = 'restore';
    }

    public function restore()
    {
        Kas::withTrashed()->find($this->kasId)->restore();
        session()->flash('pesan', 'Akun bank berhasil dipulihkan!');
        $this->batal();
    }

    public function pilihHapusPermanen($id)
    {
        $kas = Kas::withTrashed()->find($id);
        $this->kasId = $kas->id;
        $this->nama = $kas->nama_pengguna;
        $this->pilihanMenu = 'hapus_permanen';
    }

    public function hapusPermanen()
    {
        Kas::withTrashed()->find($this->kasId)->forceDelete();
        session()->flash('pesan', 'Akun bank berhasil dihapus permanen!');
        $this->batal();
    }

    // --- METHOD UNTUK HAPUS SEMUA ---
    public function konfirmasiHapusSemuaPermanen()
    {
        $this->batal();
        $this->konfirmasiHapusSemua = true;
    }

    public function hapusSemuaPermanen()
    {
        $dataTerhapus = Kas::onlyTrashed()->get();
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

<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pajak; // Pastikan model ini sudah ada
use Livewire\WithPagination;

class PengelolaanPajak extends Component
{
    use WithPagination;

    // --- PROPERTI DIKEMBALIKAN SESUAI PERMINTAAN ANDA ---
    public $pajak_id;
    public $nama_pajak;
    public $persentase;
    public $deskripsi;
    public $pilihanMenu = 'lihat'; // Default ke 'lihat', ini adalah kontrol utama UI

    // Properti untuk fungsionalitas tambahan
    public $search = '';

    protected $rules = [
        'nama_pajak' => 'required|string|min:3|max:255',
        'persentase' => 'required|numeric|between:0,100',
        'deskripsi'  => 'nullable|string',
    ];

    /**
     * Merender komponen.
     */
    public function render()
    {
        $pajaks = Pajak::where('nama_pajak', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.pengelolaan-pajak', [
            'pajaks' => $pajaks
        ]);
    }

    /**
     * Membersihkan inputan form.
     */
    private function resetForm()
    {
        $this->reset(['pajak_id', 'nama_pajak', 'persentase', 'deskripsi']);
        $this->resetValidation();
    }

    /**
     * Menyiapkan form untuk membuat data baru.
     * Mengubah state $pilihanMenu menjadi 'tambah'.
     */
    public function showCreateForm()
    {
        $this->resetForm();
        $this->pilihanMenu = 'tambah';
    }

    /**
     * Mengambil data untuk diedit.
     * Mengubah state $pilihanMenu menjadi 'edit'.
     */
    public function edit($id)
    {
        $pajak = Pajak::findOrFail($id);
        $this->pajak_id = $id;
        $this->nama_pajak = $pajak->nama_pajak;
        $this->persentase = $pajak->persentase;
        $this->deskripsi = $pajak->deskripsi;
        
        $this->pilihanMenu = 'edit'; // Pindah ke mode edit
    }

    /**
     * Menyimpan data baru atau update.
     */
    public function store()
    {
        $this->validate();

        Pajak::updateOrCreate(
            ['id' => $this->pajak_id],
            [
                'nama_pajak' => $this->nama_pajak,
                'persentase' => $this->persentase,
                'deskripsi'  => $this->deskripsi,
            ]
        );

        session()->flash(
            'message',
            $this->pajak_id ? 'Aturan Pajak Berhasil Diperbarui.' : 'Aturan Pajak Berhasil Ditambahkan.'
        );

        $this->batal(); // Kembali ke daftar dan reset form
    }

    /**
     * Membatalkan aksi (create/edit) dan kembali ke daftar.
     */
    public function batal()
    {
        $this->resetForm();
        $this->pilihanMenu = 'lihat';
    }
    
    /**
     * Menghapus data pajak.
     */
    public function delete($id)
    {
        Pajak::find($id)->delete();
        session()->flash('message', 'Aturan Pajak Berhasil Dihapus.');
    }

    /**
     * Reset paginasi saat melakukan pencarian.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }
}
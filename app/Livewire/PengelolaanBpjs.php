<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\BPJS;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;

class PengelolaanBPJS extends Component
{
    use WithPagination;

    // State utama halaman
    public string $pilihanMenu = 'lihat';

    // Properti untuk Form
    public ?int $karyawanTerpilihId = null;
    public string $no_bpjs = '';
    public string $jenis = '';
    public string $status = '';
    public float $persentase_potongan = 1.0;

    // Properti untuk fitur pencarian
    public string $karyawanSearch = '';
    public Collection $hasilPencarianKaryawan;

    // Properti untuk menyimpan model yang sedang dioperasikan
    public ?BPJS $bpjsTerpilih = null;

    // ==========================================================
    // PERBAIKAN #1: Mengembalikan method rules() dan messages()
    // ==========================================================
    protected function rules(): array
    {
        $karyawanRules = $this->pilihanMenu === 'tambah'
            ? [
                'required',
                'exists:users,id',
                Rule::unique('bpjs', 'user_id')->where('jenis', $this->jenis)
            ]
            : ['nullable'];

        return [
            'karyawanTerpilihId'    => $karyawanRules,
            'no_bpjs' => [
                'required', 'string', 'min:13', 'max:16',
                Rule::unique('bpjs', 'no_bpjs')->ignore($this->bpjsTerpilih?->id),
            ],
            'jenis'                 => ['required', Rule::in(['Kesehatan', 'Ketenagakerjaan'])],
            'status'                => ['required', Rule::in(['Aktif', 'Non-Aktif'])],
            'persentase_potongan'   => 'required|numeric|between:0,100',
        ];
    }

    protected array $messages = [
        'karyawanTerpilihId.required' => 'Silakan pilih karyawan terlebih dahulu.',
        'karyawanTerpilihId.unique'   => 'Karyawan ini sudah memiliki data BPJS jenis ini.',
        'no_bpjs.required'            => 'Nomor BPJS wajib diisi.',
        'no_bpjs.min'                 => 'Nomor BPJS minimal 13 karakter.',
        'no_bpjs.unique'              => 'Nomor BPJS ini sudah terdaftar.',
        'jenis.required'              => 'Jenis BPJS harus dipilih.',
        'status.required'             => 'Status keanggotaan harus dipilih.',
        'persentase_potongan.required' => 'Persentase potongan wajib diisi.',
    ];
    
    public function mount()
    {
        $this->hasilPencarianKaryawan = collect();
    }

    // =========================================================================
    // PERBAIKAN #2: Memperbaiki logika pencarian agar tidak "not found"
    // =========================================================================
    public function updatedKaryawanSearch($value)
    {
        if (!empty($value) && !$this->karyawanTerpilihId) {
            // Sederhanakan: Cari semua karyawan yang cocok dengan nama.
            // Validasi duplikat akan ditangani oleh rules() saat menyimpan.
            $this->hasilPencarianKaryawan = User::where('peran', 'karyawan')
                ->where('name', 'like', '%' . $value . '%')
                ->limit(5)
                ->get();
        } else {
            $this->hasilPencarianKaryawan = collect();
        }
    }

    public function pilihKaryawan($userId)
    {
        $user = User::findOrFail($userId);
        
        $this->karyawanTerpilihId = $user->id;
        $this->karyawanSearch = $user->name;
        $this->hasilPencarianKaryawan = collect();
    }
    
    public function updatedJenis($value)
    {
        if ($this->pilihanMenu === 'tambah') {
            $this->reset('karyawanTerpilihId', 'karyawanSearch');
            $this->hasilPencarianKaryawan = collect();
        }
    }

    public function pilihMenu(string $menu): void
    {
        $this->pilihanMenu = $menu;
        if ($menu === 'tambah') {
            $this->resetForm();
        }
        $this->resetErrorBag();
        $this->resetPage();
    }

    public function resetForm(): void
    {
        $this->reset(['karyawanTerpilihId', 'no_bpjs', 'jenis', 'status', 'bpjsTerpilih', 'karyawanSearch']);
        $this->persentase_potongan = 1.0;
        $this->hasilPencarianKaryawan = collect();
    }

    public function pilihEdit(BPJS $bpjs): void
    {
        $this->bpjsTerpilih = $bpjs;
        $this->karyawanTerpilihId = $bpjs->user_id;
        $this->karyawanSearch = $bpjs->user->name; // Pastikan ini ada
        $this->no_bpjs = $bpjs->no_bpjs;
        $this->jenis = $bpjs->jenis;
        $this->status = $bpjs->status;
        $this->persentase_potongan = $bpjs->persentase_potongan;
        $this->pilihanMenu = 'edit';
        $this->resetErrorBag();
    }

    public function pilihHapus(BPJS $bpjs): void
    {
        $this->bpjsTerpilih = $bpjs;
        $this->pilihanMenu = 'hapus';
    }

    public function simpan(): void
    {
        $validatedData = $this->validate();

        if ($this->bpjsTerpilih) {
            unset($validatedData['karyawanTerpilihId']);
            $this->bpjsTerpilih->update($validatedData);
            session()->flash('pesan', 'Data BPJS berhasil diperbarui.');
        } else {
            $dataToCreate = [
                'user_id' => $this->karyawanTerpilihId,
                'no_bpjs' => $this->no_bpjs,
                'jenis' => $this->jenis,
                'status' => $this->status,
                'persentase_potongan' => $this->persentase_potongan,
            ];
            BPJS::create($dataToCreate);
            session()->flash('pesan', 'Data BPJS berhasil ditambahkan.');
        }

        $this->pilihanMenu = 'lihat';
        $this->resetForm();
    }

    public function hapus(): void
    {
        if ($this->bpjsTerpilih) {
            $this->bpjsTerpilih->delete();
            session()->flash('pesan', 'Data BPJS berhasil dihapus.');
        }
        $this->pilihanMenu = 'lihat';
        $this->resetForm();
    }

    public function batal(): void
    {
        $this->pilihanMenu = 'lihat';
        $this->resetForm();
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.pengelolaan-bpjs', [
            'dataBPJS' => BPJS::with('user')->latest()->paginate(10),
        ]);
    }
}
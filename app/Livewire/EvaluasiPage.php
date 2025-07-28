<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Evaluasi;
use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class EvaluasiPage extends Component
{
    // Properti untuk mengelola tampilan dan data tabel
    public $evaluasis, $users;
    public $showForm = false;
    public $userRole;

    // Properti untuk form evaluasi
    public $evaluasiId = null;
    public $selectedUser;
    public $nilaiSikap;
    public $nilaiKehadiran;

    // --- STRUKTUR BARU UNTUK PENILAIAN TUGAS ---
    public $tasksForSelectedUser = []; 
    public $penilaianTugas = [
        'paket_tugas_id' => '',
        'sub_tugas' => [],
    ];

    // =======================================================
    // --- PROPERTI BARU DITAMBAHKAN UNTUK MODAL DETAIL ---
    // =======================================================
    public $detailForModal = [];
    
    // Listener untuk event, seperti konfirmasi hapus
    protected $listeners = [
        'refreshEvaluasi' => '$refresh',
        'confirmDelete' => 'hapus'
    ];

    public function mount()
    {
        $this->userRole = auth()->user()->peran ?? 'karyawan';
        $this->loadData();
    }

    public function loadData()
    {
        $this->users = User::where('peran', 'karyawan')->orderBy('name')->get();
        if ($this->userRole === 'admin') {
            $this->evaluasis = Evaluasi::with('user')->latest()->get();
        } else {
            $this->evaluasis = Evaluasi::with('user')->where('user_id', auth()->id())->latest()->get();
        }
    }

    public function render()
    {
        return view('livewire.evaluasi-page', [
            'evaluasis' => $this->evaluasis,
            'users' => $this->users,
        ]);
    }

    // =======================================================
    // --- METHOD BARU DITAMBAHKAN UNTUK MODAL DETAIL ---
    // =======================================================
    public function showDetailModal($evaluasiId)
    {
        $evaluasi = Evaluasi::findOrFail($evaluasiId);
        
        // Decode data JSON dan simpan ke properti agar bisa diakses di modal
        $this->detailForModal = json_decode($evaluasi->judul_skill, true) ?? [];

        // Kirim event ke browser untuk memicu JavaScript membuka modal
        $this->dispatch('open-detail-modal');
    }

    // --- LOGIKA UTAMA UNTUK FORM DINAMIS (Tidak diubah) ---
    public function updatedSelectedUser($userId)
    {
        if (!empty($userId)) {
            $this->tasksForSelectedUser = Task::where('user_id', $userId)->get();
        } else {
            $this->tasksForSelectedUser = [];
        }
        $this->reset('penilaianTugas');
    }

    public function updatedPenilaianTugas($value, $key)
    {
        if ($key === 'paket_tugas_id') {
            if (!empty($value)) {
                $paketTugas = Task::with('subTugas')->find($value);
                $subTugasData = [];
                if ($paketTugas) {
                    foreach ($paketTugas->subTugas as $item) {
                        $subTugasData[] = ['deskripsi' => $item->deskripsi, 'nilai' => ''];
                    }
                }
                $this->penilaianTugas['sub_tugas'] = $subTugasData;
            } else {
                $this->penilaianTugas['sub_tugas'] = [];
            }
        }
    }

    // --- AKSI FORM (Tidak diubah) ---

    public function simpan()
    {
        if ($this->userRole !== 'admin') abort(403);

        $this->validate([
            'selectedUser' => 'required|exists:users,id',
            'nilaiSikap' => 'required|numeric|min:1|max:100',
            'nilaiKehadiran' => 'required|numeric|min:1|max:100',
            'penilaianTugas.paket_tugas_id' => 'required|exists:tasks,id',
            'penilaianTugas.sub_tugas.*.nilai' => 'required|numeric|min:1|max:100',
        ]);

        $selectedPaket = Task::find($this->penilaianTugas['paket_tugas_id']);
        $detailTugasJson = json_encode([
            'paket_tugas_judul' => $selectedPaket->title,
            'sub_tugas' => $this->penilaianTugas['sub_tugas']
        ]);
        
        $avgSkill = collect($this->penilaianTugas['sub_tugas'])->avg('nilai') ?? 0;
        $rataRata = round(($this->nilaiSikap + $this->nilaiKehadiran + $avgSkill) / 3, 2);

        $data = [
            'user_id' => $this->selectedUser,
            'nilai_sikap' => $this->nilaiSikap,
            'nilai_kehadiran' => $this->nilaiKehadiran,
            'judul_skill' => $detailTugasJson,
            'nilai_skill' => $avgSkill,
            'total_rata_rata' => $rataRata,
            'tanggal_evaluasi' => now(),
        ];
        
        Evaluasi::updateOrCreate(['id' => $this->evaluasiId], $data);
        session()->flash('message', $this->evaluasiId ? 'Evaluasi berhasil diperbarui.' : 'Evaluasi berhasil disimpan.');
        $this->resetForm();
        $this->loadData();
        $this->showForm = false;
    }

    public function edit($id)
    {
        if ($this->userRole !== 'admin') abort(403);
        $evaluasi = Evaluasi::findOrFail($id);
        
        $this->evaluasiId = $id;
        $this->selectedUser = $evaluasi->user_id;
        $this->nilaiSikap = $evaluasi->nilai_sikap;
        $this->nilaiKehadiran = $evaluasi->nilai_kehadiran;
        
        $this->updatedSelectedUser($this->selectedUser);

        $detail = json_decode($evaluasi->judul_skill, true);
        if (is_array($detail) && isset($detail['paket_tugas_judul'])) {
            $paketTugasTersimpan = Task::where('title', $detail['paket_tugas_judul'])->first();
            if ($paketTugasTersimpan) {
                $this->penilaianTugas['paket_tugas_id'] = $paketTugasTersimpan->id;
                $this->penilaianTugas['sub_tugas'] = $detail['sub_tugas'];
            }
        }
        
        $this->showForm = true;
    }

    public function hapus($id)
    {
        if ($this->userRole !== 'admin') abort(403);
        Evaluasi::findOrFail($id)->delete();
        session()->flash('message', 'Data evaluasi berhasil dihapus.');
        $this->loadData();
    }

    // --- FUNGSI HELPER (Tidak diubah) ---
    public function toggleView()
    {
        $this->showForm = !$this->showForm;
        if ($this->showForm === false) {
            $this->resetForm();
        }
    }

    public function resetForm()
    {
        $this->reset(['evaluasiId', 'selectedUser', 'nilaiSikap', 'nilaiKehadiran', 'penilaianTugas', 'tasksForSelectedUser', 'detailForModal']);
    }
}
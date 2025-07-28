<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use App\Models\SubTugas;
use App\Models\User;
use App\Models\Siswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskPage extends Component
{
    // Properti untuk data dan UI
    public $tasks;
    public $userRole;
    public $detailTaskId = null;
    public $editingTaskId = null;
    public $pilihanMenu = 'lihat';

    // Properti untuk form
    public $title;
    public $subTugas = ['']; // Selalu diinisialisasi sebagai array
    public $assignedUser;
    public $tipePenerima = '';
    public $penerimaList = []; // Selalu diinisialisasi sebagai array
    public $detailForModal = [];

    public function mount()
    {
        $this->userRole = auth()->user()->peran ?? 'karyawan';
        $this->loadTasks();
    }

    public function loadTasks()
    {
        if ($this->userRole === 'admin') {
            $this->tasks = Task::with('user', 'subTugas')->latest()->get();
        } else {
            $this->tasks = Task::with('user', 'subTugas')->where('user_id', Auth::id())->latest()->get();
        }
    }

    public function render()
    {
        return view('livewire.task-page');
    }

    // --- LOGIKA UTAMA UNTUK FORM DINAMIS ---

    public function updatedTipePenerima($tipe)
    {
        $this->penerimaList = [];
        $this->reset('assignedUser');
        $this->resetErrorBag('assignedUser_warning');

        if ($tipe === 'karyawan') {
            $this->penerimaList = User::where('peran', 'karyawan')->orderBy('name')->get();
        } elseif ($tipe === 'siswa') {
            // Jika Anda menggunakan peran 'mentee', ganti 'siswa' menjadi 'mentee' di sini
            $this->penerimaList = User::where('peran', 'mentee') 
                ->whereHas('siswa', function ($query) {
                    $query->where('aktif', true)->whereDate('tanggal_selesai', '>=', now());
                })
                ->orderBy('name')->get();
        }
    }

    public function updatedAssignedUser($userId)
    {
        $this->resetErrorBag('assignedUser_warning');
        if ($this->tipePenerima === 'siswa' && $userId) {
            $siswa = Siswa::where('user_id', $userId)->first();
            if ($siswa && !$siswa->is_aktif) {
                $this->addError('assignedUser_warning', 'Peringatan: Masa magang mentee ini telah berakhir.');
            }
        }
    }
    
    // --- AKSI & LOGIKA UTAMA ---

    public function pilihMenu($menu)
    {
        if ($menu === 'tambah' && $this->pilihanMenu !== 'edit') {
            $this->batal();
        }
        $this->pilihanMenu = $menu;
    }

    /**
     * Aksi yang dipanggil saat tombol Batal diklik.
     * Ini akan mereset form dan mengembalikan ke tampilan daftar.
     */
    public function batal()
    {
        // Panggil method reset kustom kita
        $this->resetForm(); 
        $this->pilihanMenu = 'lihat';
    }

    public function createTask()
    {
        if ($this->userRole !== 'admin') abort(403, 'Unauthorized');
        $validatedData = $this->validate([
            'title' => 'required|string|min:5|max:255',
            'tipePenerima' => 'required',
            'assignedUser' => 'required|exists:users,id',
            'subTugas' => 'required|array|min:1',
            'subTugas.*' => 'required|string|min:3',
        ]);
        if($this->tipePenerima === 'siswa') {
            $siswa = Siswa::where('user_id', $validatedData['assignedUser'])->first();
            if(!$siswa || !$siswa->is_aktif) {
                $this->addError('assignedUser_warning', 'Gagal! Mentee ini tidak aktif dan tidak bisa diberi tugas.');
                return;
            }
        }
        DB::transaction(function () use ($validatedData) {
            $paketTugas = Task::create(['title' => $validatedData['title'],'user_id' => $validatedData['assignedUser']]);
            foreach ($validatedData['subTugas'] as $deskripsiItem) {
                $paketTugas->subTugas()->create(['deskripsi' => $deskripsiItem]);
            }
        });
        session()->flash('message', 'Paket tugas berhasil dibuat.');
        $this->batal();
        $this->loadTasks();
    }
    
    public function editTask($taskId)
    {
        if ($this->userRole !== 'admin') abort(403, 'Unauthorized');
        $task = Task::with('subTugas', 'user')->findOrFail($taskId);
        
        $this->editingTaskId = $task->id;
        $this->title = $task->title;
        $this->subTugas = $task->subTugas->pluck('deskripsi')->toArray();
        
        // Mengisi ulang form dinamis saat edit
        $this->tipePenerima = $task->user->peran;
        $this->updatedTipePenerima($this->tipePenerima); // Panggil hook untuk mengisi daftar
        $this->assignedUser = $task->user_id;
        
        $this->pilihanMenu = 'edit';
    }

    public function updateTask()
    {
        if ($this->userRole !== 'admin') abort(403, 'Unauthorized');
        $validatedData = $this->validate([
            'title' => 'required|string|min:5|max:255',
            'assignedUser' => 'required|exists:users,id',
            'subTugas' => 'required|array|min:1',
            'subTugas.*' => 'required|string|min:3',
        ]);
        DB::transaction(function () use ($validatedData) {
            $paketTugas = Task::findOrFail($this->editingTaskId);
            $paketTugas->update(['title' => $validatedData['title'], 'user_id' => $validatedData['assignedUser']]);
            $paketTugas->subTugas()->delete();
            foreach ($validatedData['subTugas'] as $deskripsiItem) {
                $paketTugas->subTugas()->create(['deskripsi' => $deskripsiItem]);
            }
        });
        session()->flash('message', 'Paket tugas berhasil diperbarui.');
        $this->batal();
    }

    public function deleteTask($taskId)
    {
        if ($this->userRole !== 'admin') abort(403, 'Unauthorized');
        Task::destroy($taskId);
        session()->flash('message', 'Paket tugas berhasil dihapus.');
        $this->loadTasks();
    }

     public function showTaskDetail($taskId)
    {
        $task = Task::with('user', 'subTugas')->findOrFail($taskId);
        
        // Isi properti $detailForModal, BUKAN $detailTaskId
        $this->detailForModal = [
            'title' => $task->title,
            'user_name' => $task->user->name,
            'user_peran' => $task->user->peran,
            'sub_tugas' => $task->subTugas->pluck('deskripsi')->toArray()
        ];
        
        $this->dispatch('open-task-detail-modal');
    }

    // --- FUNGSI HELPER UNTUK FORM ---

    /**
     * Method untuk mereset semua properti form ke kondisi awal yang aman.
     */
    private function resetForm()
    {
        $this->reset([
            'title', 
            'assignedUser', 
            'editingTaskId', 
            'tipePenerima'
        ]);
        
        // Pastikan properti array diinisialisasi ulang sebagai array kosong, bukan null.
        $this->penerimaList = [];
        $this->subTugas = [''];
        
        $this->resetErrorBag(); // Hapus semua pesan error validasi
    }

    public function tambahSubTugas()
    {
        $this->subTugas[] = '';
    }

    public function hapusSubTugas($index)
    {
        unset($this->subTugas[$index]);
        $this->subTugas = array_values($this->subTugas);
    }
}
<div>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-11 col-xl-10">

                @if (session()->has('message'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-0">Manajemen Paket Tugas</h2>
                        <p class="text-muted mb-0">Kelola semua paket tugas untuk karyawan dan siswa.</p>
                    </div>
                </div>

                <div class="card card-custom">
                    <div class="card-header card-header-custom">
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item">
                                <a class="nav-link {{ $pilihanMenu == 'lihat' ? 'active' : '' }}" href="#" wire:click.prevent="pilihMenu('lihat')">
                                    <i class="bi bi-journals me-1"></i> Daftar Tugas
                                </a>
                            </li>
                            @if(auth()->user()->peran === 'admin')
                            <li class="nav-item">
                                <a class="nav-link {{ $pilihanMenu == 'tambah' || $pilihanMenu == 'edit' ? 'active' : '' }}" href="#" wire:click.prevent="pilihMenu('tambah')">
                                    <i class="bi bi-journal-plus me-1"></i> {{ $editingTaskId ? 'Edit Tugas' : 'Tambah Tugas' }}
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                    <div class="card-body p-4">

                        @if (($pilihanMenu == 'tambah' || $pilihanMenu == 'edit') && auth()->user()->peran === 'admin')
                            {{-- ================= FORM TAMBAH / EDIT TUGAS ================= --}}
                            <h4 class="mb-4">{{ $editingTaskId ? 'Form Edit Paket Tugas' : 'Form Tambah Paket Tugas Baru' }}</h4>
                            <form wire:submit="{{ $editingTaskId ? 'updateTask' : 'createTask' }}">
                                <div class="row g-3">
                                    <!-- 1. Pilih Tipe Penerima -->
                                    <div class="col-12">
                                        <label for="tipePenerima" class="form-label fw-bold">Pilih Tipe Penerima</label>
                                        <select id="tipePenerima" wire:model.live="tipePenerima" class="form-select">
                                            <option value="">-- Pilih Tipe --</option>
                                            <option value="karyawan">Karyawan</option>
                                            <option value="siswa">Siswa / Mentee</option>
                                        </select>
                                        @error('tipePenerima') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <!-- 2. Tugaskan Kepada (Dinamis) -->
                                    @if (!empty($tipePenerima))
                                    <div class="col-12">
                                        <label for="assignedUser" class="form-label fw-bold">Tugaskan Kepada</label>
                                        <div wire:loading wire:target="tipePenerima" class="text-muted small my-2">
                                            <div class="spinner-border spinner-border-sm" role="status"></div> Memuat daftar...
                                        </div>
                                        <div wire:loading.remove wire:target="tipePenerima">
                                            <select id="assignedUser" wire:model.live="assignedUser" class="form-select">
                                                <option value="">-- Pilih Nama --</option>
                                                @forelse ($penerimaList as $penerima)
                                                    <option value="{{ $penerima->id }}">{{ $penerima->name }}</option>
                                                @empty
                                                    <option disabled>Tidak ada {{ $tipePenerima }} aktif yang tersedia</option>
                                                @endforelse
                                            </select>
                                        </div>
                                        @error('assignedUser') <span class="text-danger small">{{ $message }}</span> @enderror
                                        @error('assignedUser_warning') <div class="alert alert-warning small p-2 mt-2">{{ $message }}</div> @enderror
                                    </div>
                                    @endif

                                    <!-- 3. Judul Paket Tugas -->
                                    <div class="col-12">
                                        <label for="title" class="form-label fw-bold">Judul Paket Tugas</label>
                                        <input id="title" type="text" wire:model="title" placeholder="Cth: Membuat Laporan Keuangan" class="form-control">
                                        @error('title') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- 4. List Pekerjaan -->
                                    <div class="col-12">
                                        <label class="form-label fw-bold">List Pekerjaan</label>
                                        @foreach($subTugas as $index => $item)
                                            <div class="input-group mb-2">
                                                <span class="input-group-text">{{ $index + 1 }}.</span>
                                                <input type="text" wire:model="subTugas.{{ $index }}" placeholder="Cth: Rekapitulasi data penjualan" class="form-control">
                                                <button class="btn btn-outline-danger" wire:click.prevent="hapusSubTugas({{ $index }})" title="Hapus item ini"><i class="bi bi-x-lg"></i></button>
                                            </div>
                                            @error('subTugas.'.$index) <span class="text-danger small d-block mb-2">{{ $message }}</span> @enderror
                                        @endforeach
                                        <button class="btn btn-outline-secondary w-100 mt-2" wire:click.prevent="tambahSubTugas"><i class="bi bi-plus"></i> Tambah List Pekerjaan</button>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <button type="button" wire:click="batal" class="btn btn-secondary">Batal</button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-save me-1"></i> Simpan
                                    </button>
                                </div>
                            </form>
                        @else
                            {{-- ================= TABEL DAFTAR TUGAS ================= --}}
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="ps-3">Judul Paket</th>
                                            <th>Ditugaskan ke</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($tasks as $task)
                                            <tr class="align-middle">
                                                <td class="ps-3">{{ $task->title }}</td>
                                                <td>
                                                    <span class="badge rounded-pill {{ $task->user->peran == 'siswa' ? 'bg-info-subtle text-info-emphasis' : 'bg-secondary-subtle text-secondary-emphasis' }}">
                                                        {{ $task->user->name ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex gap-2 justify-content-center">
                                                        {{-- Tombol mata sekarang memanggil method showTaskDetail() --}}
                                                        <button wire:click="showTaskDetail({{ $task->id }})" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Lihat Detail">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        @if (auth()->user()->peran === 'admin')
                                                            <button wire:click="editTask({{ $task->id }})" class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip" title="Edit">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </button>
                                                            <button wire:click="deleteTask({{ $task->id }})" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Hapus" wire:confirm="Yakin ingin menghapus paket tugas ini?">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center py-5">
                                                    <i class="bi bi-journal-x fs-1 text-muted"></i>
                                                    <h5 class="mt-3">Belum Ada Tugas</h5>
                                                    <p class="text-muted">Silakan tambahkan paket tugas baru jika Anda admin.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- ---   MODAL DIALOG BOOTSTRAP UNTUK MENAMPILKAN DETAIL   --- --}}
    {{-- ======================================================= --}}
    <div wire:ignore.self class="modal fade" id="taskDetailModal" tabindex="-1" aria-labelledby="taskDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="taskDetailModalLabel">
                        <i class="bi bi-list-check me-2"></i> Detail Paket Tugas
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if (!empty($detailForModal))
                        <div class="mb-3">
                            <label class="form-label fw-bold">Judul Paket</label>
                            <p class="form-control-plaintext">{{ $detailForModal['title'] ?? '-' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ditugaskan Kepada</label>
                            <p class="form-control-plaintext">
                                <span class="badge rounded-pill {{ ($detailForModal['user_peran'] ?? '') == 'siswa' ? 'bg-info-subtle text-info-emphasis' : 'bg-secondary-subtle text-secondary-emphasis' }}">
                                    {{ $detailForModal['user_name'] ?? '-' }}
                                </span>
                            </p>
                        </div>
                        <hr>
                        <p class="fw-bold mb-2">Daftar Pekerjaan:</p>
                        @if (!empty($detailForModal['sub_tugas']))
                            <ul class="list-group">
                                @foreach ($detailForModal['sub_tugas'] as $item)
                                    <li class="list-group-item">{{ $item }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">Tidak ada detail pekerjaan.</p>
                        @endif
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Style --}}
    <style>
        .card-custom { border: 1px solid #dee2e6; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .card-header-custom { background-color: #fff; padding: 0; border-bottom: 1px solid #dee2e6; border-radius: 12px 12px 0 0 !important; }
        .nav-tabs { border-bottom: none; }
        .nav-tabs .nav-link { border: none; color: #6c757d; padding: 1rem 1.5rem; font-weight: 500; }
        .nav-tabs .nav-link.active { color: #0d6efd; background-color: #fff; border-bottom: 3px solid #0d6efd; }
        .task-detail-row > td { border-top: 2px solid #0d6efd; }
    </style>

    {{-- Script untuk mengontrol Modal dan Tooltip --}}
    <script>
        document.addEventListener('livewire:navigated', () => {
            const taskModalElement = document.getElementById('taskDetailModal');
            if (!taskModalElement) return;

            const taskModal = new bootstrap.Modal(taskModalElement);

            // Inisialisasi Tooltip
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                var oldTooltip = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
                if(oldTooltip) oldTooltip.dispose();
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Listener untuk event dari Livewire untuk membuka modal
            window.addEventListener('open-task-detail-modal', event => {
                taskModal.show();
            });
        });
    </script>
</div>
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
                        <h2 class="mb-0">Form Penilaian Evaluasi</h2>
                        <p class="text-muted mb-0">Kelola penilaian evaluasi untuk karyawan.</p>
                    </div>
                </div>

                <div class="card card-custom">
                    <div class="card-header card-header-custom">
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item">
                                <a class="nav-link {{ !$showForm ? 'active' : '' }}" href="#" wire:click.prevent="toggleView(false)">
                                    <i class="bi bi-list-ul me-1"></i> Daftar Evaluasi
                                </a>
                            </li>
                            @if(auth()->user()->peran === 'admin')
                            <li class="nav-item">
                                <a class="nav-link {{ $showForm ? 'active' : '' }}" href="#" wire:click.prevent="toggleView(true)">
                                    <i class="bi bi-plus-circle me-1"></i> Form Penilaian
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                    <div class="card-body p-4">

                        @if ($showForm && auth()->user()->peran === 'admin')
                            {{-- ================= FORM PENILAIAN ================= --}}
                            <h4 class="mb-4">{{ $evaluasiId ? 'Edit Evaluasi' : 'Tambah Evaluasi Baru' }}</h4>
                            
                            <div class="mb-4">
                                <label for="user" class="form-label fw-bold">1. Pilih Nama Pengguna</label>
                                <select wire:model.live="selectedUser" class="form-select" id="user">
                                    <option value="">-- Pilih Karyawan --</option>
                                    @foreach ($users->where('peran', 'karyawan') as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedUser') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            @if ($selectedUser)
                                <div class="mb-4">
                                    <label class="form-label fw-bold">2. Penilaian Paket Tugas</label>
                                    <div class="p-3 rounded-3 bg-light">
                                        <!-- Pilih Paket Tugas -->
                                        <div class="mb-3">
                                            <label class="form-label">Pilih Paket Tugas</label>
                                            <div wire:loading wire:target="selectedUser" class="text-muted small my-2">
                                                <div class="spinner-border spinner-border-sm" role="status"></div> Memuat daftar tugas...
                                            </div>
                                            <div wire:loading.remove wire:target="selectedUser">
                                                <select wire:model.live="penilaianTugas.paket_tugas_id" class="form-select">
                                                    <option value="">-- Pilih Paket Tugas --</option>
                                                    @forelse ($tasksForSelectedUser as $task)
                                                        <option value="{{ $task->id }}">{{ $task->title }}</option>
                                                    @empty
                                                        <option disabled>Tidak ada paket tugas untuk karyawan ini</option>
                                                    @endforelse
                                                </select>
                                                @error('penilaianTugas.paket_tugas_id') <small class="text-danger">{{ $message }}</small> @enderror
                                            </div>
                                        </div>

                                        <!-- Daftar Sub-tugas & Penilaian -->
                                        @if (!empty($penilaianTugas['sub_tugas']))
                                            <hr>
                                            <p class="fw-bold mb-2">Detail Pekerjaan:</p>
                                            @foreach ($penilaianTugas['sub_tugas'] as $index => $sub)
                                                <div class="input-group mb-2">
                                                    <span class="input-group-text">{{ $index + 1 }}.</span>
                                                    <input type="text" class="form-control" value="{{ $sub['deskripsi'] }}" readonly>
                                                    <input type="number" wire:model="penilaianTugas.sub_tugas.{{ $index }}.nilai" class="form-control" min="1" max="100" placeholder="Nilai (1-100)">
                                                </div>
                                                @error('penilaianTugas.sub_tugas.'.$index.'.nilai') <small class="text-danger d-block mb-2">{{ $message }}</small> @enderror
                                            @endforeach
                                        @endif
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label for="nilaiSikap" class="form-label fw-bold">3. Nilai Sikap (1-100)</label>
                                        <input type="number" wire:model="nilaiSikap" class="form-control" id="nilaiSikap" min="1" max="100">
                                        @error('nilaiSikap') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="nilaiKehadiran" class="form-label fw-bold">4. Nilai Kehadiran (1-100)</label>
                                        <input type="number" wire:model="nilaiKehadiran" class="form-control" id="nilaiKehadiran" min="1" max="100">
                                        @error('nilaiKehadiran') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>

                                <!-- Tombol Aksi -->
                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <button type="button" wire:click="toggleView(false)" class="btn btn-secondary">Batal</button>
                                    <button type="button" wire:click="simpan" class="btn btn-success">
                                        <span wire:loading.remove wire:target="simpan">
                                            <i class="bi bi-save me-1"></i> Simpan
                                        </span>
                                        <span wire:loading wire:target="simpan">
                                            <div class="spinner-border spinner-border-sm" role="status"></div>
                                            Menyimpan...
                                        </span>
                                    </button>
                                </div>
                            @endif

                        @else
                            {{-- ================= TABEL DAFTAR EVALUASI ================= --}}
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="ps-3">Tanggal</th>
                                            <th>Nama</th>
                                            <th class="text-center">Sikap</th>
                                            <th class="text-center">Hadir</th>
                                            <th>Paket Tugas</th>
                                            <th class="text-center">Rata-rata</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($evaluasis as $eva)
                                            @php
                                                $detailTugas = json_decode($eva->judul_skill, true);
                                            @endphp
                                            <tr class="align-middle">
                                                <td class="ps-3">{{ \Carbon\Carbon::parse($eva->tanggal_evaluasi)->format('d M Y') }}</td>
                                                <td>
                                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                                        {{ $eva->user->name ?? '-' }}
                                                    </span>
                                                </td>
                                                <td class="text-center">{{ $eva->nilai_sikap }}</td>
                                                <td class="text-center">{{ $eva->nilai_kehadiran }}</td>
                                                <td>{{ $detailTugas['paket_tugas_judul'] ?? 'Tugas' }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary-subtle text-primary-emphasis">{{ number_format($eva->total_rata_rata, 2) }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex gap-2 justify-content-center">
                                                        <button wire:click="showDetailModal({{ $eva->id }})" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Lihat Detail">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        @if (auth()->user()->peran === 'admin')
                                                            <button wire:click="edit({{ $eva->id }})" class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip" title="Edit">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </button>
                                                            <button wire:click="$dispatch('confirmDelete', {id: {{ $eva->id }}})" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Hapus">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-5">
                                                    <i class="bi bi-journal-x fs-1 text-muted"></i>
                                                    <h5 class="mt-3">Belum Ada Evaluasi</h5>
                                                    <p class="text-muted">Silakan tambahkan evaluasi baru jika Anda admin.</p>
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

    {{-- MODAL DETAIL TUGAS --}}
    <div wire:ignore.self class="modal fade" id="detailTugasModal" tabindex="-1" aria-labelledby="detailTugasModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailTugasModalLabel">
                        <i class="bi bi-list-check me-2"></i> Detail Pekerjaan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if (!empty($detailForModal))
                        <div class="mb-3">
                            <label class="form-label fw-bold">Paket Tugas</label>
                            <p class="form-control-plaintext">{{ $detailForModal['paket_tugas_judul'] ?? '-' }}</p>
                        </div>
                        <hr>
                        <p class="fw-bold mb-2">Daftar Pekerjaan:</p>
                        @if (!empty($detailForModal['sub_tugas']))
                            <ul class="list-group">
                                @foreach ($detailForModal['sub_tugas'] as $item)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>{{ $item['deskripsi'] ?? 'N/A' }}</span>
                                        <span class="badge bg-primary rounded-pill">{{ $item['nilai'] ?? '-' }}</span>
                                    </li>
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
            // Inisialisasi Modal
            const detailModalEl = document.getElementById('detailTugasModal');
            if (detailModalEl) {
                const detailModal = new bootstrap.Modal(detailModalEl);
                
                window.addEventListener('open-detail-modal', () => {
                    detailModal.show();
                });
            }

            // Inisialisasi Tooltip
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                var oldTooltip = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
                if(oldTooltip) oldTooltip.dispose();
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</div>
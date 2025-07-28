<div class="container">
    <div class="row my-4">
        <div class="col-12 text-center">
            <!-- Tombol navigasi -->
            <button wire:click="pilihMenu('lihat')"
                class="col-lg-4 col-md-6 mb-4 btn {{ $pilihanMenu == 'lihat' ? 'btn-warning' : 'btn-outline-warning' }} mx-2 py-2 px-4 rounded-pill shadow-sm btn-custom">
                <i class="fas fa-box-open"></i>
                <span class="text-custom-orange-semualatihan">Lihat Pelatihan</span>
            </button>
            <button wire:click="pilihMenu('tambah')"
                class="col-lg-4 col-md-6 mb-4 btn {{ $pilihanMenu == 'tambah' ? 'btn-warning' : 'btn-outline-warning' }} mx-2 py-2 px-4 rounded-pill shadow-sm btn-custom">
                <i class="fas fa-plus-circle"></i>
                <span class="text-custom-orange-tambahlatihan">Tambah Pelatihan</span>
            </button>
            <button wire:loading
                class="col-lg-4 col-md-6 mb-4 btn btn-custom-orange mx-2 py-2 px-4 rounded-pill shadow-sm">
                <i class="fas fa-spinner fa-spin"></i>
                <span class="text-custom-orange-loading">Loading...</span>
            </button>
        </div>
    </div>

    <!-- CSS -->
    <style>
        .text-custom-orange-tambahlatihan,
        .text-custom-orange-semualatihan {
            color: rgb(146, 54, 0);
        }

        .text-custom-orange-loading {
            color: rgb(107, 43, 0);
        }

        .btn-custom-orange {
            background-color: rgb(255, 94, 0);
        }

        .outline-semuaukaryawan {
            border: 2px solid rgb(255, 94, 0);
        }

        .bg-semuaukaryawan {
            background-color: rgb(255, 94, 0);
        }
    </style>

    <div class="row">
        <div class="col-12">
            @if ($pilihanMenu == 'lihat')
            <div class="card outline-semuaukaryawan shadow-sm mb-4">
                <div class="card-header bg-semuaukaryawan text-white">Semua Pelatihan</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-warning thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Instruktur</th>
                                    <th>Nama Sekolah</th>
                                    <th>Nama Pembimbing</th>
                                    <th>Jumlah Siswa</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($semuaProduk as $produk)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $produk->instruktur }}</td>
                                    <td>{{ $produk->sekolah }}</td>
                                    <td>{{ $produk->pembimbing }}</td>
                                    <td>{{ $produk->siswa }}</td>
                                    <td>{{ $produk->ket }}</td>
                                    <td>
                                        <div class="d-flex gap-3 flex-wrap justify-content-center">
                                            <button wire:click="pilihEdit({{ $produk->id }})"
                                                class="btn col-lg-9 btn-sm btn-outline-warning px-3">
                                                <i class="fas fa-pencil-alt"></i> Edit
                                            </button>
                                            <button wire:click="pilihHapus({{ $produk->id }})"
                                                class="btn col-lg-9 btn-sm btn-outline-danger px-3">
                                                <i class="fas fa-trash-alt"></i> Hapus
                                            </button>
                                            
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                <div class="card outline-semuaukaryawan shadow-sm mb-4">
                    <div class="card-header bg-semuaukaryawan text-white">Semua Pelatihan</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-warning thead-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Instruktur</th>
                                        <th>Nama Sekolah</th>
                                        <th>Nama Pembimbing</th>
                                        <th>Jumlah Siswa</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($semuaProduk as $produk)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $produk->instruktur }}</td>
                                            <td>{{ $produk->sekolah }}</td>
                                            <td>{{ $produk->pembimbing }}</td>
                                            <td>{{ $produk->siswa }}</td>
                                            <td>{{ $produk->ket }}</td>
                                            <td>
                                                <div class="d-flex gap-3 flex-wrap justify-content-center">
                                                    <button wire:click="pilihEdit({{ $produk->id }})"
                                                        class="btn col-lg-5 col-md-6 btn-sm btn-outline-warning mb-2">
                                                        <i class="fas fa-pencil-alt"></i> Edit
                                                    </button>
                                                    <button wire:click="pilihHapus({{ $produk->id }})"
                                                        class="btn col-lg-5 col-md-6 btn-sm btn-outline-danger mb-2">
                                                        <i class="fas fa-trash-alt"></i> Hapus
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            @elseif(in_array($pilihanMenu, ['tambah', 'edit']))
            <p class="lead text-muted mb-1">
                <span style="color: red; font-size: 20px;">*</span>
                <i style="font-size: 15px;">Hanya Jabatan Pegawai Yang Dapat Menjadi Instruktur!!</i>
            </p>

            <div class="card outline-semuaukaryawan shadow-sm mb-4">
                <div class="card-header bg-semuaukaryawan text-white">
                    {{ $pilihanMenu == 'tambah' ? '➕ Tambah Pelatihan' : '✏ Edit Produk' }}
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="{{ $pilihanMenu == 'tambah' ? 'simpan' : 'simpanEdit' }}">
                        <div class="mb-3">
                            <label class="form-label">Instruktur</label>
                            <select class="form-control border-warning" wire:model="instruktur">
                                <option value="">Pilih Pegawai</option>
                                @foreach($daftarInstruktur as $pegawai)
                                <option value="{{ $pegawai->nama }}">{{ $pegawai->nama }}</option>
                                @endforeach
                            </select>
                            @error('instruktur') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Sekolah</label>
                            <input type="text" class="form-control border-warning" wire:model="sekolah" />
                            @error('sekolah') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Pembimbing</label>
                            <input type="text" class="form-control border-warning" wire:model="pembimbing" />
                            @error('pembimbing') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jumlah Siswa</label>
                            <input type="text" class="form-control border-warning" wire:model="siswa" />
                            @error('siswa') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <input type="text" class="form-control border-warning" wire:model="ket" />
                            @error('ket') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="btn btn-success mt-2 w-100">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    </form>
                </div>
            </div>

            @elseif($pilihanMenu == 'hapus')
            <div class="card border-danger shadow-sm mb-4">
                <div class="card-header bg-danger text-white">⚠ Konfirmasi Hapus</div>
                <div class="card-body">
                    <p>Anda yakin ingin menghapus pelatihan ini?</p>
                    <ul>
                        <li><strong>Atas nama Instruktur:</strong> {{ $produkTerpilih->instruktur }}</li>
                    </ul>
                    <div class="d-flex gap-3 justify-content-center">
                        <button class="btn col-lg-3 col-md-4 btn-sm btn-danger" wire:click='hapus'>
                            <i class="fas fa-trash-alt"></i> Hapus
                        </button>
                        <button class="btn col-lg-3 col-md-4 btn-sm btn-secondary" wire:click='batal'>
                            <i class="fas fa-times"></i> Batal
                        </button>
                <p class="lead text-muted mb-1">
                    <span style="color: red; font-size: 20px;">*</span>
                    <i style="font-size: 15px;">Hanya Jabatan Pegawai Yang Dapat Menjadi Instruktur!!</i>
                </p>

                <div class="card outline-semuaukaryawan shadow-sm mb-4">
                    <div class="card-header bg-semuaukaryawan text-white">
                        {{ $pilihanMenu == 'tambah' ? '➕ Tambah Pelatihan' : '✏️ Edit Produk' }}
                    </div>
                    <div class="card-body">
                        <form wire:submit.prevent="{{ $pilihanMenu == 'tambah' ? 'simpan' : 'simpanEdit' }}">
                            <div class="mb-3">
                                <label class="form-label">Instruktur</label>
                                <select class="form-control border-warning" wire:model="instruktur">
                                    <option value="">Pilih Pegawai</option>
                                    @foreach($daftarInstruktur as $pegawai)
                                        <option value="{{ $pegawai->nama }}">{{ $pegawai->nama }}</option>
                                    @endforeach
                                </select>
                                @error('instruktur') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Sekolah</label>
                                <input type="text" class="form-control border-warning" wire:model="sekolah" />
                                @error('sekolah') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Pembimbing</label>
                                <input type="text" class="form-control border-warning" wire:model="pembimbing" />
                                @error('pembimbing') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Jumlah Siswa</label>
                                <input type="text" class="form-control border-warning" wire:model="siswa" />
                                @error('siswa') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Keterangan</label>
                                <input type="text" class="form-control border-warning" wire:model="ket" />
                                @error('ket') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <button type="submit" class="btn btn-success mt-2 w-100">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                        </form>
                    </div>
                </div>

            @elseif($pilihanMenu == 'hapus')
                <div class="card border-danger shadow-sm mb-4">
                    <div class="card-header bg-danger text-white">⚠️ Konfirmasi Hapus</div>
                    <div class="card-body">
                        <p>Anda yakin ingin menghapus pelatihan ini?</p>
                        <ul>
                            <li><strong>Atas nama Instruktur:</strong> {{ $produkTerpilih->instruktur }}</li>
                        </ul>
                        <div class="d-flex gap-3 justify-content-center">
                            <button class="btn col-lg-3 col-md-4 btn-sm btn-danger" wire:click='hapus'>
                                <i class="fas fa-trash-alt"></i> Hapus
                            </button>
                            <button class="btn col-lg-3 col-md-4 btn-sm btn-secondary" wire:click='batal'>
                                <i class="fas fa-times"></i> Batal
                            </button>
                        </div>
                    </div>
                </div>
            @elseif($pilihanMenu == 'excel')
            <div class="card border-success shadow-sm mb-4">
            </div>
                <div class="card border-success shadow-sm mb-4">
                </div>
            @endif
        </div>
    </div>
</div>

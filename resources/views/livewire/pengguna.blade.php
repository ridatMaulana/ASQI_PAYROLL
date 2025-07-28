<div class="container-fluid">
    <div class="row my-4">
        <div class="col-12">
            <!-- Tombol navigasi -->
            <button wire:click="pilihMenu('lihat')"
                class=" btn {{ $pilihanMenu == 'lihat' ? 'btn-warning' : 'btn-outline-warning' }} mx-2 py-2 px-4 rounded-pill shadow-sm btn-custom">
                <i class="fas fa-box-open"></i>
                <span class="text-custom-orange-semuakaryawan">Semua Karyawan</span>
            </button>
            <button wire:click="pilihMenu('tambah')"
                class=" btn {{ $pilihanMenu == 'tambah' ? 'btn-warning' : 'btn-outline-warning' }} mx-2 py-2 px-4 rounded-pill shadow-sm btn-custom">
                <i class="fas fa-plus-circle"></i>
                <span class="text-custom-orange-tambahkaryawan">Tambah Karyawan</span>
            </button>
            <button wire:loading
                class=" btn btn-custom-orange mx-2 py-2 px-4 rounded-pill shadow-sm">
                <i class="fas fa-spinner fa-spin"></i>
                <span class="text-custom-orange-loading">Loading...</span>
            </button>
        </div>
    </div>

    <!-- Kondisi untuk Menampilkan Halaman Tertentu -->
    <div class="row">
        <div class="col-12">
            @if ($pilihanMenu == 'lihat')
            <div class="card outline-semuaukaryawan shadow-sm mb-4">
                <div class="card-header bg-semuakaryawan text-white">
                    Semua Karyawan
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-warning">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Lengkap</th>
                                    <th>NIS</th>
                                    <th>Jabatan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($penggunas as $pengguna)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $pengguna->nama }}</td>
                                    <td>{{ $pengguna->nis }}</td>
                                    <td>{{ $pengguna->jabatan->nama ?? '-' }}</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm bi-pencil-square" wire:click="pilihEdit({{ $pengguna->id }})"></button>
                                        <button class="btn btn-danger btn-sm bi-trash" wire:click="pilihHapus({{ $pengguna->id }})"
                                            onclick="return confirm('Yakin ingin menghapus pengguna ini?')"></button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                <!-- Tampilkan Semua Karyawan -->
                <div class="card outline-semuaukaryawan shadow-sm mb-4">
                    <div class="card-header bg-semuakaryawan text-white">
                        Semua Karyawan
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-warning">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>NIS</th>
                                        <th>Jabatan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($penggunas as $pengguna)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $pengguna->nama }}</td>
                                        <td>{{ $pengguna->nis }}</td>
                                        <td>{{ $pengguna->jabatan->nama ?? '-' }}</td>
                                        <td>
                                            <button class="btn btn-warning btn-sm bi-pencil-square" wire:click="pilihEdit({{ $pengguna->id }})"></button>
                                            <button class="btn btn-danger btn-sm bi-trash" wire:click="pilihHapus({{ $pengguna->id }})"
                                                onclick="return confirm('Yakin ingin menghapus pengguna ini?')"></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            
            @elseif ($pilihanMenu == 'jabatan')
                <!-- Tampilkan Daftar Jabatan -->
                <div class="col-md-6 ps-md-2">
                    <div class="card border-warning h-100">
                        <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Daftar Jabatan</span>
                            <div class="d-flex align-items-center">
                                <input type="text" class="form-control form-control-sm me-2" style="width: 180px;" wire:model="namaJabatan" placeholder="Nama Jabatan">
                                <button class="btn btn-sm btn-light" wire:click="simpanJabatan">
                                    <i class="fas fa-plus-circle me-1"></i> Tambah
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-warning">
                                        <tr>
                                            <th width="50">No</th>
                                            <th>Nama Jabatan</th>
                                            <th width="100">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($jabatans as $jabatan)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $jabatan->nama }}</td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-danger bi-trash" wire:click="hapusJabatan({{ $jabatan->id }})" onclick="return confirm('Yakin ingin hapus jabatan ini?')"></button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @elseif ($pilihanMenu == 'tambah')
            <div class="card outline-semuaukaryawan shadow-sm mb-4">
                <div class="card-header bg-semuakaryawan text-white">
                    Tambah Karyawan
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="simpan">

                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control border-warning" wire:model="nama" />
                            @error('nama') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">NIS</label>
                            <input type="number" class="form-control border-warning" wire:model="nis" />
                            @error('nis') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jabatan</label>
                            <select class="form-control border-warning" wire:model="jabatanTerpilihId">
                                <option value="">-- Pilih Jabatan --</option>
                                @foreach($daftarJabatan as $jabatan)
                                <option value="{{ $jabatan->id }}">{{ $jabatan->nama }}</option>
                                @endforeach
                            </select>
                            @error('jabatanTerpilihId') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="btn btn-success rounded-pill px-4 py-2 mt-3">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    </form>
                </div>
            </div>

            @elseif ($pilihanMenu == 'edit')
            <div class="card outline-semuaukaryawan shadow-sm mb-4">
                <div class="card-header bg-semuakaryawan text-white">
                    Edit Pengguna
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="simpanEdit">

                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" wire:model="nama" />
                            @error('nama') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">NIS</label>
                            <input type="number" class="form-control" wire:model="nis" />
                            @error('nis') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jabatan</label>
                            <select class="form-control" wire:model="jabatanTerpilihId">
                                <option value="">-- Pilih Jabatan --</option>
                                @foreach($daftarJabatan as $jabatan)
                                <option value="{{ $jabatan->id }}">{{ $jabatan->nama }}</option>
                                @endforeach
                            </select>
                            @error('jabatanTerpilihId') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="btn btn-success rounded-pill mt-3 px-4 py-2">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <button type="button" wire:click='batal' class="btn btn-secondary rounded-pill mt-3 px-4 py-2">
                            <i class="fas fa-times"></i> Batal
                        </button>
                    </form>
                </div>
            </div>

            @elseif ($pilihanMenu == 'hapus')
            <div class="card border-danger shadow-sm mb-4">
                <div class="card-header bg-danger text-white">
                    Hapus Pengguna
                </div>
                <div class="card-body">
                    <p>Anda yakin ingin menghapus pengguna ini?</p>
                    <p><strong>Nama:</strong> {{ $penggunaTerpilih->nama }}</p>
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
            @endif
        </div>
    </div>

    
</div>

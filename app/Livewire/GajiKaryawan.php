<?php


namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use App\Mail\SlipGajiMail;
use App\Http\Controllers\GajiController;



// Model yang dibutuhkan

use App\Models\User;
use App\Models\GajiKaryawan as ModelsGajiKaryawan;
use App\Models\Kas;
use App\Models\TransaksiKas;
use App\Models\Pajak;
use App\Models\BPJS;
use App\Models\Kasbon;

class GajiKaryawan extends Component

{

    use WithPagination;

    public ?int $gaji_id = null;
    public string $tampilanAktif = 'riwayat';
    public string $userRole;

    // Properti untuk Form
    public $selectedUser = null;
    public $periode = '';
    public $gaji_pokok = 0;
    public $tunjangan = 0;
    public $pajak_id = null;
    public $pajak = 0;
    public $bpjs = 0;
    public $kasbon = 0;
    public $potongan_absen = 0;
    public $potongan_lainnya = 0;
    public $total_gaji_bersih = 0;
    public $sumber_dana_id = null;

    public string $namaKaryawanSearch = '';
    public Collection $hasilPencarianKaryawan;

    public $aturanPajak = [];
    public $semuaKas = [];

    protected function rules(): array
    {
        return [
            'selectedUser'     => 'required|exists:users,id',
            'periode'          => [
                'required',
                'date_format:Y-m-d',

                function ($attribute, $value, $fail) {
                    $year = Carbon::parse($value)->year;
                    $month = Carbon::parse($value)->month;
                    $query = ModelsGajiKaryawan::where('user_id', $this->selectedUser)
                        ->whereYear('periode', $year)
                        ->whereMonth('periode', $month);
                    if ($this->gaji_id) {
                        $query->where('id', '!=', $this->gaji_id);
                    }
                    if ($query->exists()) {
                        $fail('Gaji untuk karyawan ini pada periode (bulan) tersebut sudah ada.');
                    }
                }
            ],

            'gaji_pokok'       => 'required|integer|min:0',
            'tunjangan'        => 'required|integer|min:0',
            'pajak_id'         => 'nullable|exists:pajaks,id',
            'potongan_absen'   => 'required|integer|min:0',
            'potongan_lainnya' => 'required|integer|min:0',
            'sumber_dana_id'   => ['required', 'exists:kas,id', function ($attribute, $value, $fail) {
                $kas = Kas::find($value);
                if ($kas && $kas->saldo < $this->total_gaji_bersih) {
                    $fail('Saldo pada akun ' . $kas->nama_pengguna . ' tidak mencukupi untuk pembayaran ini.');
                }
            }],
        ];
    }

    protected $messages = [
        'sumber_dana_id.required' => 'Sumber dana pembayaran harus dipilih.',
        'sumber_dana_id.exists'   => 'Sumber dana yang dipilih tidak valid.',
        'periode.unique'          => 'Gaji untuk karyawan ini pada periode tersebut sudah ada.',
    ];





    public function pilihKaryawan($userId)
    {
        $user = User::findOrFail($userId);

        $this->selectedUser = $user->id;
        $this->namaKaryawanSearch = $user->name;
        $this->hasilPencarianKaryawan = collect(); 

        $periodeGaji = Carbon::parse($this->periode); 

        $kasbonAktif = Kasbon::where('user_id', $userId)
            ->where('status', 'dicairkan') 
            ->where('periode_mulai', '<=', $periodeGaji->format('Y-m-d'))
            ->where('periode_selesai', '>=', $periodeGaji->format('Y-m-d'))
            ->first();

        $this->kasbon = $kasbonAktif ? $kasbonAktif->bayar_perbulan : 0;

        $this->calculateAll();
    }



    public function mount()
    {
        $this->userRole = auth()->user()->peran ?? 'user';

        $this->aturanPajak = Pajak::orderBy('nama_pajak')->get();
        $this->semuaKas = Kas::orderBy('nama_pengguna')->get();

        $this->hasilPencarianKaryawan = collect();

        $this->resetForm();
    }



    public function render()
    {
        $query = ModelsGajiKaryawan::with(['user', 'sumberDana'])->orderBy('periode', 'desc');

        if ($this->userRole === 'user') {
            $query->where('user_id', auth()->id());
        }

        return view('livewire.gaji-karyawan', [
            'data_gaji' => $query->paginate(10)
        ]);
    }


    public function updatedNamaKaryawanSearch($value)
    {
        // Hanya cari jika user belum dipilih
        if (!empty($value) && !$this->selectedUser) {
            $this->hasilPencarianKaryawan = User::where('peran', 'karyawan')
                ->where('name', 'like', '%' . $value . '%')
                ->limit(5) 
                ->get();
        } else {
            $this->hasilPencarianKaryawan = collect();
        }
    }

    public function calculateAll()
    {
        $this->gaji_pokok = (int) $this->gaji_pokok ?: 0;
        $this->tunjangan = (int) $this->tunjangan ?: 0;
        $this->potongan_absen = (int) $this->potongan_absen ?: 0;
        $this->potongan_lainnya = (int) $this->potongan_lainnya ?: 0;
        $this->kasbon = (int) $this->kasbon ?: 0;

        $gajiBruto = $this->gaji_pokok + $this->tunjangan;

        $this->bpjs = 0;
        if ($this->selectedUser) {
            $bpjsData = BPJS::where('user_id', $this->selectedUser)->where('status', 'Aktif')->first();
            if ($bpjsData) {
                $this->bpjs = round(($bpjsData->persentase_potongan / 100) * $gajiBruto);
            }
        }


        $this->pajak = 0;

        if ($this->pajak_id) {
            $pajakRule = Pajak::find($this->pajak_id);
            if ($pajakRule) {
                $this->pajak = round(($pajakRule->persentase / 100) * $gajiBruto);
            }
        }

        $totalPotongan = $this->pajak + $this->bpjs + $this->kasbon + $this->potongan_absen + $this->potongan_lainnya;
        $this->total_gaji_bersih = $gajiBruto - $totalPotongan;
    }

    public function simpan(): void
    {
        // Panggil kalkulasi terakhir kali untuk memastikan data final
        $this->calculateAll();
        $this->validate();

        // Gunakan transaction untuk memastikan integritas data
        DB::transaction(function () {
            $karyawan = User::findOrFail($this->selectedUser);
            $kas = Kas::findOrFail($this->sumber_dana_id);

            $gaji = ModelsGajiKaryawan::updateOrCreate(
                ['id' => $this->gaji_id],
                [
                    'user_id'           => $this->selectedUser,
                    'periode'           => \Carbon\Carbon::parse($this->periode)->startOfMonth(),
                    'gaji_pokok'        => $this->gaji_pokok,
                    'tunjangan'         => $this->tunjangan,
                    'pajak'             => $this->pajak,
                    'bpjs'              => $this->bpjs,
                    'kasbon'            => $this->kasbon,
                    'potongan_absen'    => $this->potongan_absen,
                    'potongan_lainnya'  => $this->potongan_lainnya,
                    'total_gaji_bersih' => $this->total_gaji_bersih,
                    'kas_id'            => $this->sumber_dana_id,
                ]
            );

            TransaksiKas::create([
                'kas_id'     => $kas->id,
                'kode'       => 'GAJI-' . $gaji->id, // Kode unik berdasarkan ID Gaji
                'tanggal'    => \Carbon\Carbon::parse($this->periode)->endOfMonth()->setTimeFrom(now()),
                'keterangan' => 'Penggajian ' . $karyawan->name . ' periode ' . \Carbon\Carbon::parse($this->periode)->format('F Y'),
                'jenis'      => 'keluar',
                'jumlah'     => $this->total_gaji_bersih,
            ]);

            $kas->decrement('saldo', $this->total_gaji_bersih);
        });

        session()->flash('message', $this->gaji_id ? 'Data gaji berhasil diperbarui.' : 'Data gaji berhasil disimpan.');
        $this->pilihanTampilan('riwayat');
    }





    //-------------------------------------------------

    //  Event Listeners (updated* hooks)

    //-------------------------------------------------



    public function updated($propertyName)

    {

        // Properti yang jika berubah akan memicu kalkulasi ulang

        $triggerProperties = [

            'gaji_pokok',
            'tunjangan',
            'pajak_id',
            'potongan_absen',
            'potongan_lainnya'

        ];

        if (in_array($propertyName, $triggerProperties)) {

            $this->calculateAll();
        }
    }



    public function updatedSelectedUser($userId)
    {

        if ($userId) {

            $periodeGaji = Carbon::parse($this->periode);
            $kasbonAktif = Kasbon::where('user_id', $userId)

                ->where('status', 'dicairkan')
                ->where('periode_mulai', '<=', $periodeGaji->format('Y-m-d'))
                ->where('periode_selesai', '>=', $periodeGaji->format('Y-m-d'))
                ->first();

            $this->kasbon = $kasbonAktif ? $kasbonAktif->bayar_perbulan : 0;
        } else {
            $this->kasbon = 0;
        }
        $this->calculateAll();
    }



    public function updatedPeriode()
    {
        if ($this->selectedUser) {
            $this->updatedSelectedUser($this->selectedUser);
        }
    }





    //-------------------------------------------------

    //  Method Bantu & Navigasi

    //-------------------------------------------------



    public function edit($id)
    {

        $gaji = ModelsGajiKaryawan::findOrFail($id);

        $this->gaji_id = $gaji->id;
        $this->selectedUser = $gaji->user_id;
        $this->namaKaryawanSearch = $gaji->user->name;
        $this->periode = Carbon::parse($gaji->periode)->format('Y-m-d');
        $this->gaji_pokok = $gaji->gaji_pokok;
        $this->tunjangan = $gaji->tunjangan;
        $this->kasbon = $gaji->kasbon;
        $this->potongan_absen = $gaji->potongan_absen;
        $this->potongan_lainnya = $gaji->potongan_lainnya;
        $this->sumber_dana_id = $gaji->kas_id;

        $this->calculateAll();
        $this->pilihanTampilan('form');
    }



    public function hapus($id)
    {

        if ($this->userRole !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        DB::transaction(function () use ($id) {
            $gaji = ModelsGajiKaryawan::findOrFail($id);
            $kas = Kas::find($gaji->kas_id);

            if ($kas) {
                $kas->increment('saldo', $gaji->total_gaji_bersih);
            }

            $gaji->delete();
        });

        session()->flash('message', 'Data gaji berhasil dihapus dan dana telah dikembalikan.');
    }



    public function resetForm()
    {

        $this->reset([
            'gaji_id',
            'selectedUser',
            'gaji_pokok',
            'tunjangan',
            'pajak_id',
            'pajak',
            'bpjs',
            'kasbon',
            'potongan_absen',
            'potongan_lainnya',
            'total_gaji_bersih',
            'sumber_dana_id',
            'namaKaryawanSearch'
        ]);

        $this->hasilPencarianKaryawan = collect();

        $this->periode = now()->format('Y-m-d');
        if ($this->userRole === 'user') {
            $this->selectedUser = auth()->id();
            $this->namaKaryawanSearch = auth()->user()->name;
        }
        $this->resetErrorBag();
    }


    public function pilihanTampilan(string $tampilan): void
    {
        $this->tampilanAktif = $tampilan;
        if ($tampilan === 'form' && $this->gaji_id === null) {
            $this->resetForm();
        }
        $this->resetPage();
    }

    public function kirimSlipGajiViaEmail($gajiId)
    {
        // Gunakan try-catch untuk menangani potensi error koneksi email
        try {
            // 1. Ambil data gaji beserta relasi user
            $gaji = ModelsGajiKaryawan::with('user')->findOrFail($gajiId);

            // 2. Cek apakah user punya email
            if (!$gaji->user || !$gaji->user->email) {
                session()->flash('message', 'Gagal! Karyawan ini tidak memiliki alamat email.');
                return;
            }

            // 3. Panggil GajiController untuk membuat data PDF mentah
            $gajiController = new GajiController();
            $pdfData = $gajiController->generatePdfData($gaji);

            // 4. Kirim email menggunakan Mailable yang sudah kita buat
            Mail::to($gaji->user->email)->send(new SlipGajiMail($gaji, $pdfData));

            // 5. Kirim pesan sukses
            session()->flash('message', 'Slip gaji berhasil dikirim ke ' . $gaji->user->email);
        } catch (\Exception $e) {
            // Tangani jika ada error (misal: config .env salah)
            session()->flash('message', 'Gagal mengirim email. Error: ' . $e->getMessage());
        }
    }
}

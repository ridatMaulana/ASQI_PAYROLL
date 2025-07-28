<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Slip Gaji {{ $gaji->user->name }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            width: 600px;
            margin: 0 auto;
            padding: 20px;
            font-size: 13px;
            color: #333;
            border: 1px solid #ccc;
        }

        .header {
            display: flex;
            align-items: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header img {
            width: 70px;
            height: auto;
            margin-right: 15px;
        }

        .company-details {
            flex-grow: 1;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
        }

        .company-address {
            font-size: 12px;
            color: #666;
        }

        h2 {
            text-align: center;
            text-transform: uppercase;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .info {
            margin-bottom: 15px;
        }

        .info p {
            margin: 3px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            padding: 8px;
            border: 1px solid #ccc;
        }

        table th {
            background-color: #f0f0f0;
            text-align: left;
        }

        .total {
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .signature {
            margin-top: 40px;
            text-align: right;
        }

        .signature p {
            margin-bottom: 60px;

        }
    </style>
</head>
<body>
    {{-- Header Perusahaan --}}
    <div class="header">
        <div class="company-details">
            <div class="company-name">PT ASQI DIGITAL INOVATION</div>
            <div class="company-address">
                Kp. Sabandar Hilir RT 001 RW 007 Ds. Bandar Kec. Karang Tengah Kab. Cianjur <br>
                Telp: (+62) 877-4141-4976 | Email: contact@asqi.co.id

            </div>
        </div>
    </div>

    <h2>Slip Gaji Karyawan</h2>

    {{-- Info Karyawan --}}
    <div class="info">
        <p><strong>Nama:</strong> {{ $gaji->user->name }}</p>
        <p><strong>Periode:</strong> {{ \Carbon\Carbon::parse($gaji->periode)->format('F Y') }}</p>
    </div>

    {{-- Tabel Gaji --}}
    <table>
        <tr>
            <th>Rincian</th>
            <th>Jumlah</th>
        </tr>
        <tr>
            <td>Gaji Pokok</td>
            <td>Rp {{ number_format($gaji->gaji_pokok, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Tunjangan</td>
            <td>Rp {{ number_format($gaji->tunjangan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Pajak</td>
            <td>Rp {{ number_format($gaji->pajak, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>BPJS</td>
            <td>Rp {{ number_format($gaji->bpjs, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Kasbon</td>
            <td>Rp {{ number_format($gaji->kasbon, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Potongan Lainnya</td>
            <td>Rp {{ number_format($gaji->potongan_lainnya, 0, ',', '.') }}</td>
        </tr>
        <tr class="total">
            <td>Gaji Bersih</td>
            <td>Rp {{ number_format($gaji->total_gaji_bersih, 0, ',', '.') }}</td>
        </tr>
    </table>

    {{-- Tanda Tangan --}}
    <div class="signature">
        <p>Hormat Kami,</p>
        <p>___________________________</p>
        <p><strong>HR Department</strong></p>
    </div>
</body>
</html>
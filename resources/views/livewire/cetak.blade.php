<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            width: 700px; 
            margin: 20px auto;
            padding: 25px;
            font-size: 14px;
            color: #333;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .header {
            display: flex;
            align-items: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .header .company-details {
            flex-grow: 1;
        }

        .company-name {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .company-address {
            font-size: 12px;
            color: #666;
        }

        .report-title {
            text-align: center;
            text-transform: uppercase;
            margin-bottom: 25px;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .report-info {
            margin-bottom: 20px;
        }

        .report-info p {
            margin: 4px 0;
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        table th,
        table td {
            padding: 10px;
            border: 1px solid #ccc;
        }

        table thead th {
            background-color: #f0f0f0;
            text-align: left;
            font-weight: bold;
        }
        
        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-right {
            text-align: right;
        }

        .signature {
            margin-top: 50px;
            text-align: right;
        }

        .signature-space {
            height: 60px;
        }

        .signature-name {
            font-weight: bold;
        }

        /* Tombol Cetak */
        .print-button-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .print-button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
        }

        @media print {
            body {
                border: none;
                box-shadow: none;
                width: 100%;
                margin: 0;
                padding: 0;
            }
            .print-button-container {
                display: none;
            }
            .signature {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>

    {{-- Header Perusahaan (diadaptasi dari referensi) --}}
    <div class="header">
        <div class="company-details">
            <div class="company-name">NAMA PERUSAHAAN ANDA</div>
            <div class="company-address">
                Alamat lengkap perusahaan Anda di sini <br>
                Telp: (021) 123-4567 | Email: info@perusahaan.com
            </div>
        </div>
    </div>

    <h2 class="report-title">Laporan Transaksi</h2>

    {{-- Info Laporan --}}
    <div class="report-info">
        <p><strong>Periode:</strong> {{-- Anda bisa mengisi periode di sini, misal: 01 - 31 Mei 2024 --}}</p>
        <p><strong>Tanggal Cetak:</strong> {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
    </div>

    {{-- Tombol Cetak (ditempatkan di sini agar mudah diakses) --}}
    <div class="print-button-container">
        <button class="print-button" onclick="window.print()">Cetak Laporan</button>
    </div>

    {{-- Tabel Laporan Transaksi --}}
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Tanggal</th>
                <th style="width: 40%;">No Inv.</th>
                <th class="text-right" style="width: 30%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($semuaTransaksi as $transaksi)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $transaksi->created_at->format('d-m-Y') }}</td>
                <td>{{ $transaksi->kode }}</td>
                <td class="text-right">Rp. {{ number_format($transaksi->total, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center;">Tidak ada data transaksi pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Tanda Tangan (diadaptasi dari referensi) --}}
    <div class="signature">
        <p>Hormat Kami,</p>
        <div class="signature-space"></div>
        <p class="signature-name">(_________)</p>
        <p>Bagian Keuangan</p>
    </div>

</body>
</html>
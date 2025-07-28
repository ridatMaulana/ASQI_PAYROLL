<!DOCTYPE html>
<html>
<head>
    <title>Data Absensi</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
        }
        th {
            background-color: #eee;
        }
    </style>
</head>
<body>
    <h3 style="text-align: center;">Data Absensi Karyawan</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Jabatan</th>
                <th>Kehadiran</th>
                <th>Tanggal</th>
                <th>Waktu Kedatangan</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($absensi as $index => $absen)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $absen->nama }}</td>
                <td>{{ $absen->position }}</td>
                <td>{{ $absen->kehadiran }}</td>
                <td>{{ $absen->tanggal }}</td>
                <td>{{ $absen->waktu }}</td>
                <td>{{ $absen->keterangan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

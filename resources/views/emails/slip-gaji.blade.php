<!DOCTYPE html>
<html>

<head>
    <title>Slip Gaji</title>
</head>

<body>
    <p>Halo, {{ $gaji->user->name }},</p>
    <p>Terlampir adalah slip gaji Anda untuk periode {{ \Carbon\Carbon::parse($gaji->periode)->format('F Y') }}.</p>
    <p>Terima kasih atas kerja keras Anda.</p>
    <br>
    <p>Hormat kami,</p>
    <p><strong>PT. Asqi Digital Inovation</strong></p>
</body>

</html>

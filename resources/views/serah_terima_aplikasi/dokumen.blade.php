<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Dokumen Persetujuan Pengembangan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .no-border {
            border: none;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-left {
            text-align: left;
        }
        .bold {
            font-weight: bold;
        }
        .page-break {
            page-break-after: always;
        }
        .side-content-header{
            font-size: 11px;
        }
    </style>
</head>
<body>

@foreach($dataserahterima as $serahterima)
<div class="header">
    <table>
        <tr>
            <td rowspan="4">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('img/logo_ptsi.png'))) }}" alt="Logo" width="100">
            </td>
            <td rowspan="4" class="text-center" style="width:350px;">
                <h3>Berita Acara Serah Terima Pekerjaan Sistem Aplikasi</h3>
            </td>
            <td class="side-content-header">No. Dokumen</td>
            <td class="side-content-header">FP-DTI03-06</td>
        </tr>
        <tr class="side-content-header">
            <td>No. Revisi</td>
            <td>0</td>
        </tr>
        <tr class="side-content-header">
            <td>Tahun Revisi</td>
            <td>2024</td>
        </tr>
        <tr class="side-content-header">
            <td>Halaman</td>
            <td>1</td>
        </tr>
    </table>
</div>

<h3 class="text-center" style="font-size: 11px;">Yang Bertanda Tangan Dibawah Ini, Menyatakan Bahwa Pada</h3>
<table style="font-size: 11px; width: 100%; border-collapse: collapse;">
    <tr>
        <td style="width: 25%;">Hari</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;">{{ $serahterima->hari }}</td>
    </tr>
    <tr>
        <td style="width: 25%;">Tanggal</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;">{{ \Carbon\Carbon::parse($serahterima->tanggal)->format('d M Y') }}</td>
    </tr>
    <tr>
        <td style="width: 25%;">Lokasi</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;">{{ $serahterima->lokasi }}</td>
    </tr>
    <tr>
        <td style="width: 25%;">Nama Aplikasi</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;">{{ $serahterima->nama_aplikasi }}</td>
    </tr>
    <tr>
        <td style="width: 25%;">Nomor Dokumen</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;">{{ $serahterima->nomor_dokumen }}</td>
    </tr>
    <tr>
        <td style="width: 25%;">Keterangan</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;">{{ $serahterima->keterangan }}</td>
    </tr>
</table>

<h3 class="text-center" style="font-size: 11px; margin-top: 20px;">Telah berfungsi dengan baik sesuai dengan kebutuhan dan menerima sistem aplikasi ini untuk dipergunakan sebagaimana mestinya.</h3>

<table class="table" style="font-size: 11px; width: 100%; border-collapse: collapse; table-layout: fixed; page-break-inside: avoid;">
    <thead>
        <tr>
            <th class="text-center" style="width: 50%;">Yang Menyerahkan</th>
            <th class="text-center" style="width: 50%;">Penerima</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="height: 100px; vertical-align: top; text-align: center;" colspan="1"></td>
            <td style="height: 100px; vertical-align: top; text-align: center;" colspan="1"></td>
        </tr>
        <tr>
            <td class="text-center" style="vertical-align: top;">{{ $serahterima->pemberi }}</td>
            <td class="text-center" style="vertical-align: top;">{{ $serahterima->penerima }}</td>
        </tr>
        <tr>
            <td class="text-center" style="vertical-align: top;">{{ $serahterima->nik_pemberi }}</td>
            <td class="text-center" style="vertical-align: top;">{{ $serahterima->nik_penerima }}</td>
        </tr>
    </tbody>
</table>
<h3 class="text-left" style="font-size:11px;font-style:italic;">Lampiran</h3>
<h3 class="text-left" style="font-size:11px;font-style:italic;">1. Fotocopy User Acceptance Test</h3>
@if (!$loop->last)
<div class="page-break"></div>
@endif

@endforeach

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Dokumen QAT</title>
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

@foreach($dataQAT as $qualityassurancetesting)
<div class="header">
    <table>
        <tr>
            <td rowspan="4">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('img/logo_ptsi.png'))) }}" alt="Logo" width="100">
            </td>
            <td rowspan="4" class="text-center" style="width:350px;">
                <h3>Quality Assurance Testing (QAT)</h3>
            </td>
            <td class="side-content-header">No. Dokumen</td>
            <td class="side-content-header">FP-DTI03-G</td>
        </tr>
        <tr class="side-content-header">
            <td>No. Revisi</td>
            <td>0</td>
        </tr>
        <tr class="side-content-header">
            <td>Tanggal Revisi</td>
            <td>2024</td>
        </tr>
        <tr class="side-content-header">
            <td>Halaman</td>
            <td>1</td>
        </tr>
    </table>
</div>

<h4 class="text-right" style="font-size:11px;"><strong>NO: </strong></h4>
<h3 class="text-center bold" style="font-size:11px;">QUALITY ASSURANCE TESTING</h3>
<table style="font-size:11px;">
    <tr>
        <td style="width: 25%;">Nama Aplikasi</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;">{{ $qualityassurancetesting->nama_aplikasi }}</td>
    </tr>
    <tr>
        <td style="width: 25%;">Jenis Aplikasi</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;">{{ $qualityassurancetesting->jenis_aplikasi }}</td>
    </tr>
    <tr>
        <td style="width: 25%;">Kebutuhan Fungsional</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;">{{ $qualityassurancetesting->kebutuhan_nonfungsional }}</td>
    </tr>
    <tr>
        <td style="width: 25%;">Unit Pemilik Proses Bisnis</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;">{{ $qualityassurancetesting->unit_pemilik_proses_bisnis }}</td>
    </tr>
    <tr>
        <td style="width: 25%;">Lokasi Pengujian</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;">{{ $qualityassurancetesting->lokasi_pengujian }}</td>
    </tr>
    <tr>
        <td style="width: 25%;">Tanggal Pengujian</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;">{{ \Carbon\Carbon::parse($qualityassurancetesting->tgl_pengujian)->format('d M Y') }}</td>
    </tr>
    <tr>
        <td style="width: 25%;">Manual Book</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;">{{$qualityassurancetesting->manual_book}}</td>
    </tr>
</table>

<table class="table" style="font-size:11px;">
    <tr>
        <td class="text-center" colspan="2">Tanggal: {{ \Carbon\Carbon::parse($qualityassurancetesting->tgl_disusun)->format('d-m-Y') }}</td>
        <td class="text-center" colspan="2">Tanggal: {{ \Carbon\Carbon::parse($qualityassurancetesting->tanggal_disetujui)->format('d-m-Y') }}</td>
    </tr>
    <tr>
        <th class="text-center" colspan="2">Diketahui oleh</th>
        <th class="text-center" colspan="2">Disetujui oleh</th>
    </tr>
    <tr>
        <td colspan="2" style="height: 100px;"></td>
        <td colspan="2" style="height: 100px;"></td>
    </tr>
    <tr>
        <td class="text-center" colspan="2">{{ $qualityassurancetesting->nama_mengetahui }}<br>{{$qualityassurancetesting->jabatan_mengetahui}}</td>
        <td class="text-center" colspan="2">{{ $qualityassurancetesting->nama_penyetuju }}<br>{{$qualityassurancetesting->jabatan_penyetuju}}</td>
    </tr>
</table>

@if (!$loop->last)
<div class="page-break"></div>
@endif

@endforeach

</body>
</html>

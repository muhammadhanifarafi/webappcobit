<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Dokumen UAT</title>
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

@foreach($dataUserAcceptanceTesting as $useracceptancetesting)
<div class="header">
    <table>
        <tr>
            <td rowspan="4">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('img/logo_ptsi.png'))) }}" alt="Logo" width="100">
            </td>
            <td rowspan="4" class="text-center" style="width:350px;">
                <h3>User Acceptance Testing (UAT)</h3>
            </td>
            <td class="side-content-header">No. Dokumen</td>
            <td class="side-content-header">FP-DTI03-F</td>
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

<h4 class="text-right" style="font-size:11px;"><strong>NO: {{ $useracceptancetesting->nomor_dokumen; }}</strong></h4>
<h3 class="text-center bold" style="font-size:11px;">USER ACCEPTANCE TESTING</h3>
<table style="font-size:11px;">
    <tr>
        <td style="width: 25%;">Nama Aplikasi</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;">{{ $useracceptancetesting->nama_aplikasi }}</td>
    </tr>
    <tr>
        <td style="width: 25%;">Jenis Aplikasi</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;">
            @php
                $jenisAplikasiArray = json_decode($useracceptancetesting->jenis_aplikasi, true);
            @endphp
            {{ is_array($jenisAplikasiArray) ? implode(', ', $jenisAplikasiArray) : $useracceptancetesting->jenis_aplikasi }}
        </td>
    </tr>
    <!-- <tr>
        <td style="width: 25%;">Kebutuhan Fungsional</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;">{!! $useracceptancetesting->kebutuhan_fungsional !!}</td>
    </tr> -->
    <!-- <tr>
        <td style="width: 25%;">Unit Pemilik Proses Bisnis</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;">{{ $useracceptancetesting->unit_pemilik_proses_bisnis }}</td>
    </tr> -->
    <!-- <tr>
        <td style="width: 25%;">Lokasi Pengujian</td>
        <td style="width: 5%;">:</td>
        <td>{{ $useracceptancetesting->lokasi_pengujian }}</td>
    </tr> -->
    <tr>
        <td style="width: 25%;">Nama Penguji</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;">{{ $useracceptancetesting->nama_penguji }}</td>
    </tr>
    <tr>
        <td style="width: 25%;">Tanggal Pengujian</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;">{{ \Carbon\Carbon::parse($useracceptancetesting->tgl_pengujian)->format('d M Y') }}</td>
    </tr>
    <tr>
        <td style="width: 25%;">Tanda Tangan</td>
        <td style="width: 5%;">:</td>
        @if($useracceptancetesting->path_qrcode_penyetuju)
            <td style="width: 70%; height: 100px; text-align: center;">
                <img src="{{ URL($useracceptancetesting->path_qrcode_penyetuju) }}" alt="QR Code Penyetuju" style="max-height: 100px;">
            </td>
        @else
            <td style="width: 70%;"></td>  <!-- Tampilkan kolom kosong jika path_qrcode_penyetuju kosong -->
        @endif
    </tr>
    <!-- <tr>
        <td style="width: 25%;">Manual Book</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;">{{$useracceptancetesting->manual_book}}</td>
    </tr> -->
</table>

<!-- <table class="table" style="font-size:11px;">
    <tr>
        <th class="text-center" colspan="2">Diketahui oleh</th>
        <th class="text-center" colspan="2">Disetujui oleh</th>
    </tr>
    <tr>
        <td colspan="2" style="height: 100px;"></td>
        <td colspan="2" style="height: 100px;"></td>
    </tr>
    <tr>
        <td class="text-center" colspan="2">{{ $useracceptancetesting->nama_penyusun }}<br>{{$useracceptancetesting->jabatan_penyusun}}</td>
        <td class="text-center" colspan="2">{{ $useracceptancetesting->nama_penyetuju }}<br>{{$useracceptancetesting->jabatan_penyetuju}}</td>
    </tr>
    <tr>
        <td class="text-center" colspan="2">Tanggal: {{ \Carbon\Carbon::parse($useracceptancetesting->tgl_disusun)->format('d-m-Y') }}</td>
        <td class="text-center" colspan="2">Tanggal: {{ \Carbon\Carbon::parse($useracceptancetesting->tanggal_disetujui)->format('d-m-Y') }}</td>
    </tr>
</table> -->

<h3 class="text-center bold" style="font-size:11px;">ITEM PENGUJIAN</h3>
<h3 class="text-left bold" style="font-size:11px;">KEAMANAN INFORMASI</h3>
<table style="width: 100%; table-layout: fixed;">
    <?php 
        $filteredDetailUAT = $detailUAT->filter(function($dqa) {
            return $dqa->jenis == 1;
        });
            
        if ($filteredDetailUAT->isNotEmpty()): 
    ?>
    <tr> 
        <td class="side-content-header" style="width: 5%;"><b>No</b></td>
        <td class="side-content-header" style="width: 30%;"><b>Item</b></td>
        <td class="side-content-header" style="width: 15%;"><b>Pass</b></td>
        <td class="side-content-header" style="width: 15%;"><b>Fail</b></td>
        <td class="side-content-header" style="width: 35%;"><b>Notes</b></td>
    </tr>
    <tr> 
        <td colspan="5" style="font-size:13px;"><b>A. MANAJEMEN USER</b></td>
    </tr>
    <?php foreach($detailUAT as $dqa){ 
        $no_a = 1; 
        if($dqa->jenis == 1){  
    ?>
        <tr> 
            <td class="side-content-header" style="font-weight:normal"><?= $no_a; ?></td>
            <td class="side-content-header" style="font-weight:normal"><?= $dqa->item ?></td>
            
            <!-- Cek nilai pass_fail, jika Pass beri warna hitam pada kolom Pass -->
            <td class="side-content-header" style="font-weight:normal; background-color: <?= $dqa->pass_fail == 'Pass' ? 'black' : 'white'; ?>; color: <?= $dqa->pass_fail == 'Pass' ? 'white' : 'black'; ?>;">
                
            </td>
            
            <!-- Cek nilai pass_fail, jika Fail beri warna hitam pada kolom Fail -->
            <td class="side-content-header" style="font-weight:normal; background-color: <?= $dqa->pass_fail == 'Fail' ? 'black' : 'white'; ?>; color: <?= $dqa->pass_fail == 'Fail' ? 'white' : 'black'; ?>;">
                
            </td>
            
            <td class="side-content-header" style="font-weight:normal"><?= $dqa->notes ?></td>
        </tr>
    <?php } ?>
    <?php $no_a++; } ?>
    <?php endif; ?>
    <?php 
        $filteredDetailUAT = $detailUAT->filter(function($dqa) {
            return $dqa->jenis == 2;
        });
            
        if ($filteredDetailUAT->isNotEmpty()): 
    ?>
    <tr> 
        <td colspan="5" style="font-size:13px;"><b>B. KEAMANAN APLIKASI</b></td>
    </tr>
    <?php foreach($detailUAT as $dqa){
        $no_b = 1;  
        if($dqa->jenis == 2){  
    ?>
        <tr> 
            <td class="side-content-header" style="font-weight:normal"><?= $no_b; ?></td>
            <td class="side-content-header" style="font-weight:normal"><?= $dqa->item ?></td>
            
            <!-- Cek nilai pass_fail, jika Pass beri warna hitam pada kolom Pass -->
            <td class="side-content-header" style="font-weight:normal; background-color: <?= $dqa->pass_fail == 'Pass' ? 'black' : 'white'; ?>; color: <?= $dqa->pass_fail == 'Pass' ? 'white' : 'black'; ?>;">
                
            </td>
            
            <!-- Cek nilai pass_fail, jika Fail beri warna hitam pada kolom Fail -->
            <td class="side-content-header" style="font-weight:normal; background-color: <?= $dqa->pass_fail == 'Fail' ? 'black' : 'white'; ?>; color: <?= $dqa->pass_fail == 'Fail' ? 'white' : 'black'; ?>;">
            </td>
            
            <td class="side-content-header" style="font-weight:normal"><?= $dqa->notes ?></td>
        </tr>
    <?php } ?>
    <?php $no_b++; } ?>
    <?php endif; ?>
</table>

<h3 class="text-left bold" style="font-size:11px;">SISTEM INFORMASI</h3>
<table style="width: 100%; table-layout: fixed;">
    <?php 
        $filteredDetailUAT = $detailUAT->filter(function($dqa) {
            return $dqa->jenis == 3;
        });
            
        if ($filteredDetailUAT->isNotEmpty()): 
    ?>
    <tr> 
        <td class="side-content-header" style="width: 5%;"><b>No</b></td>
        <td class="side-content-header" style="width: 30%;"><b>Item</b></td>
        <td class="side-content-header" style="width: 15%;"><b>Pass</b></td>
        <td class="side-content-header" style="width: 15%;"><b>Fail</b></td>
        <td class="side-content-header" style="width: 35%;"><b>Notes</b></td>
    </tr>
    <tr> 
        <td colspan="5" style="font-size:13px;"><b>C. FORM PERUSAHAAN</b></td>
    </tr>
    <?php foreach($detailUAT as $dqa){ 
        $no_c = 1; 
        if($dqa->jenis == 3){  
    ?>
        <tr> 
            <td class="side-content-header" style="font-weight:normal"><?= $no_c; ?></td>
            <td class="side-content-header" style="font-weight:normal"><?= $dqa->item ?></td>
            
            <!-- Cek nilai pass_fail, jika Pass beri warna hitam pada kolom Pass -->
            <td class="side-content-header" style="font-weight:normal; background-color: <?= $dqa->pass_fail == 'Pass' ? 'black' : 'white'; ?>; color: <?= $dqa->pass_fail == 'Pass' ? 'white' : 'black'; ?>;">
            </td>
            
            <!-- Cek nilai pass_fail, jika Fail beri warna hitam pada kolom Fail -->
            <td class="side-content-header" style="font-weight:normal; background-color: <?= $dqa->pass_fail == 'Fail' ? 'black' : 'white'; ?>; color: <?= $dqa->pass_fail == 'Fail' ? 'white' : 'black'; ?>;">
            </td>
            
            <td class="side-content-header" style="font-weight:normal"><?= $dqa->notes ?></td>
        </tr>
    <?php } ?>
    <?php $no_c++; } ?>
    <?php endif; ?>
</table>
@if (!$loop->last)
<div class="page-break"></div>
@endif

@endforeach

</body>
</html>
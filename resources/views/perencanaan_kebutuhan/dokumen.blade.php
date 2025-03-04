<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Dokumen Perencanaan Kebutuhan</title>
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

@foreach($datakebutuhan as $kebutuhan)
<div class="header">
    <table>
        <tr>
            <td rowspan="4">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('img/logo_ptsi.png'))) }}" alt="Logo" width="100">
            </td>
            <td rowspan="4" class="text-center" style="width:350px;">
                <h3>Perencanaan Kebutuhan Pengembangan Sistem Informasi</h3>
            </td>
            <td class="side-content-header">No. Dokumen</td>
            <td class="side-content-header">FP-DTI03-0C</td>
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

<h4 class="text-right" style="font-size:11px;"><strong>NO: {{ $kebutuhan->nomor_dokumen }}</strong></h4>
<h3 class="text-center bold" style="font-size:11px;">PERANCANAAN KEBUTUHAN SISTEM INFORMASI</h3>
<div class="bordered">
    <table class="table-container" style="font-size:11px;">
        <tr>
            <th style="width: 25%;">Nomor Proyek</th>
            <td style="width: 5%;">:</td>
            <td style="width: 70%;">{{ $kebutuhan->nomor_proyek }}</td>
        </tr>
        <tr>
            <th style="width: 25%;">Nama Proyek</th>
            <td style="width: 5%;">:</td>
            <td style="width: 70%;">{{ $kebutuhan->nama_proyek }}</td>
        </tr>
        <!-- <tr>
            <th style="width: 25%;">Deskripsi</th>
            <td style="width: 5%;">:</td>
            <td style="width: 70%;">{!! $kebutuhan->deskripsi !!}</td>
        </tr> -->
        <tr>
            <th style="width: 25%;">Pemilik Proyek</th>
            <td style="width: 5%;">:</td>
            <td style="width: 70%;">{{ $kebutuhan->pemilik_proyek }}</td>
        </tr>
        <tr>
            <th style="width: 25%;">Manajer Proyek</th>
            <td style="width: 5%;">:</td>
            <td style="width: 70%;">{{ $kebutuhan->manajer_proyek }}</td>
        </tr>
        <tr>
            <th style="width: 25%;">Stakeholders</th>
            <td style="width: 5%;">:</td>
            <td style="width: 70%;">{!! $kebutuhan->stakeholders !!}</td>
        </tr>
        <tr>
            <th style="width: 25%;">Kebutuhan Fungsional</th>
            <td style="width: 5%;">:</td>
            <td style="width: 70%;">{!! $kebutuhan->kebutuhan_fungsional !!}</td>
        </tr>
        <tr>
            <th style="width: 25%;">Kebutuhan Non-Fungsional</th>
            <td style="width: 5%;">:</td>
            <td style="width: 70%;">{!! $kebutuhan->kebutuhan_nonfungsional !!}</td>
        </tr>
        <!-- Kriteria Aplikasi -->
        <tr>
            <th style="width: 25%;">Kriteria Aplikasi</th>
            <td style="width: 5%;">:</td>
            <td style="width: 70%;">{{ $kebutuhan->nama_kriteria_aplikasi }}
            <br>
            <div style="font-size: 8px; margin-top: 5px;">
                {{ $kebutuhan->deskripsi_kriteria_aplikasi }}
            </div>
            </td>
        </tr>

        <!-- Klasifikasi Model Backup -->
        <tr>
            <th style="width: 25%;">Klasifikasi Model Backup</th>
            <td style="width: 5%;">:</td>
            <td style="width: 70%;">{{ $kebutuhan->nama_klasifikasi_model_backup }}
            <br>
            <div style="font-size: 8px; margin-top: 5px;">
                {{ $kebutuhan->deskripsi_klasifikasi_model_backup }}
            </div>
            </td>
        </tr>

        <!-- Bahasa Pemrograman -->
        <tr>
            <th style="width: 25%;">Bahasa Pemrograman</th>
            <td style="width: 5%;">:</td>
            <td style="width: 70%;">{{ $kebutuhan->bahasa_pemrograman }}</td>
        </tr>

        <!-- Tipe Resource Server -->
        <tr>
            <th style="width: 25%;">Tipe Resource Server</th>
            <td style="width: 5%;">:</td>
            <td style="width: 70%;">{{ $kebutuhan->nama_tipe_resource_server }}
            <br>
            <div style="font-size: 8px; margin-top: 5px;">
                {{ $kebutuhan->deskripsi_tipe_resource_server }}
            </div>
            </td>
        </tr>

        <!-- Tipe Server -->
        <tr>
            <th style="width: 25%;">Tipe Server</th>
            <td style="width: 5%;">:</td>
            <td style="width: 70%;">{{ $kebutuhan->nama_tipe_server }}</td>
        </tr>

        <!-- Database -->
        <tr>
            <th style="width: 25%;">Database</th>
            <td style="width: 5%;">:</td>
            <td style="width: 70%;">{{ $kebutuhan->nama_database }}</td>
        </tr>

        <!-- Storage -->
        <tr>
            <th style="width: 25%;">Storage</th>
            <td style="width: 5%;">:</td>
            <td style="width: 70%;">{{ $kebutuhan->nama_storage }}
            <br>
            <div style="font-size: 8px; margin-top: 5px;">
                {{ $kebutuhan->deskripsi_storage }}
            </div>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;">Lampiran</th>
            <td style="width: 5%;">:</td>
            <td style="width: 70%;">{{ $kebutuhan->lampiran }}</td>
        </tr>
    </table>

    <table class="table" style="font-size:11px;table-layout: fixed;page-break-inside: avoid;">
        <tr>
            <td class="text-center" colspan="2">Tanggal: {{ \Carbon\Carbon::parse($kebutuhan->tanggal_disiapkan)->format('d M Y') }}</td> 
            <td class="text-center" colspan="2">
                @if($kebutuhan->approve_at_pemverifikasi)
                    Tanggal: {{ \Carbon\Carbon::parse($kebutuhan->approve_at_pemverifikasi)->format('d M Y') }}
                @else
                    Tanggal:
                @endif
            </td>
            
            <td class="text-center" colspan="2">
                @if($kebutuhan->approve_at)
                    Tanggal: {{ \Carbon\Carbon::parse($kebutuhan->approve_at)->format('d M Y') }}
                @else
                    Tanggal:
                @endif
            </td>
        </tr>
        <tr>
            <th class="text-center" colspan="2">Disiapkan oleh</th>
            <th class="text-center" colspan="2">Diverifikasi oleh</th>
            <th class="text-center" colspan="2">Disetujui oleh</th>
        </tr>
        <tr>
            @if($kebutuhan->path_qrcode_pemohon)
                <td colspan="2" style="height: 100px; text-align: center;">
                    <img src="{{ URL($kebutuhan->path_qrcode_pemohon) }}" alt="QR Code Pemohon" style="max-height: 100px;">
                </td>
            @else
                <td colspan="2"></td>  <!-- Kolom kosong jika path_qrcode_pemohon kosong -->
            @endif

            @if($kebutuhan->path_qrcode_pemverifikasi)
                <td colspan="2" style="height: 100px; text-align: center;">
                    <img src="{{ URL($kebutuhan->path_qrcode_pemverifikasi) }}" alt="QR Code Pemverifikasi" style="max-height: 100px;">
                </td>
            @else
                <td colspan="2"></td>  <!-- Kolom kosong jika path_qrcode_pemverifikasi kosong -->
            @endif

            @if($kebutuhan->path_qrcode_penyetuju)
                <td colspan="2" style="height: 100px; text-align: center;">
                    <img src="{{ URL($kebutuhan->path_qrcode_penyetuju) }}" alt="QR Code Penyetuju" style="max-height: 100px;">
                </td>
            @else
                <td colspan="2"></td>  <!-- Kolom kosong jika path_qrcode_penyetuju kosong -->
            @endif
        </tr>
        <tr>
            <td class="text-center" colspan="2">{{ $kebutuhan->nama_pemohon }}<br>{{$kebutuhan->jabatan_pemohon}}</td>
            <td class="text-center" colspan="2">{{ $kebutuhan->nama_pemverifikasi }}<br>{{$kebutuhan->jabatan_pemverifikasi}}</td>
            <td class="text-center" colspan="2">{{ $kebutuhan->nama_penyetuju }}<br>{{$kebutuhan->jabatan_penyetuju}}</td>
        </tr>
    </table>

    @if (!$loop->last)
    <div class="page-break"></div>
    @endif

@endforeach

</body>
</html>

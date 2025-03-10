@php
use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Dokumen Project Summary</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            background-color: #f4f4f9;
            font-size: 12px;
        }
        .header {
            margin-bottom: 20px;
            background-color: #007bff;
            color: white;
            padding: 10px;
            border-radius: 5px;
        }
        .header h2 {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #fff;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        .icon {
            font-size: 20px;
            margin-right: 10px;
            vertical-align: middle;
        }
        .status-approved {
            color: green;
            font-weight: bold;
        }
        .status-pending {
            color: orange;
            font-weight: bold;
        }
        .status-not-created {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="header">
    <div style="display: flex; align-items: center;">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('img/logo-white.png'))) }}" alt="Logo" width="100" style="margin-right: 20px;">
        <h2>COBIT Project Progress Summary</h2>
    </div>
</div>

<table>
    <tr>
        <th>Project Name</th>
        <th>PIC</th>
        <th>Tahap</th>
        <th>Approval Status</th>
        <th>Approval Date</th>
    </tr>

    @foreach ($datapermintaan as $data)
        @php 
            $persetujuan = $trx_persetujuan_pengembangan->where('id_permintaan_pengembangan', $data->id_permintaan_pengembangan)->first();
            $perencanaanProyek = $trx_perencanaan_proyek->where('id_persetujuan_pengembangan', $persetujuan->id_persetujuan_pengembangan ?? 0);
        @endphp
        <tr>
            <td>
                <b style="font-size: 16px;"><b>Proyek</b> : {{ $data->judul ?? 'Judul Belum Tersedia' }}</b>
                <br>
                <br>
                <b>Nomor Permintaan</b> : {{ $data->nomor_dokumen }}
                <br>
                <b>Pemilik Proyek</b> : {{ $perencanaanProyek->first()->pemilik_proyek ?? 'Pemilik Belum Tersedia' }}
                <br>
                <b>Manajer Proyek</b> : {{ $perencanaanProyek->first()->manajer_proyek ?? 'Manajer Belum Tersedia' }}
                <br>
                <b>Ruang Lingkup</b> : {!! $perencanaanProyek->first()->ruang_lingkup ?? 'Ruang Lingkup Belum Tersedia' !!}
                <br>
                <b>Tanggal Mulai</b> : {{ $perencanaanProyek->first()->tanggal_mulai ?? 'Tanggal Mulai Belum Tersedia' }}
                <br>
                <b>Target Selesai</b> : {{ $perencanaanProyek->first()->target_selesai ?? 'Tanggal Selesai Belum Tersedia' }}
                <br>
                <b>Nilai Kontrak</b> : {{ $perencanaanProyek->first()->nilai_kontrak ?? 'Nilai Belum Tersedia' }}
                <br>
            </td>
            <td>{{ $data->nama_pengaju ?? 'Nama Tidak Ada' }}</td>

            {{-- Permintaan Pengembangan --}}
            @php
                $permintaan = $trx_permintaan_pengembangan->where('id_permintaan_pengembangan', $data->id_permintaan_pengembangan); 
            @endphp
            <td>Permintaan Pengembangan</td>
            <td class="{{ $permintaan->isNotEmpty() && optional($permintaan->first())->is_approve ? 'status-approved' : ($permintaan->isNotEmpty() ? 'status-pending' : 'status-not-created') }}">
                {{ $permintaan->isNotEmpty() ? ($permintaan->first()->is_approve ? 'Disetujui Penyetuju' : 'Menunggu Persetujuan ' . (optional($permintaan->first())->approve_by ?? '')) : 'Belum Dibuat' }}
            </td>
            <td>{{ optional($permintaan->first())->approve_at ? Carbon::parse(optional($permintaan->first())->approve_at)->translatedFormat('d F Y') : '' }}</td>
        </tr>

        {{-- Persetujuan Pengembangan --}}
        @php 
            $persetujuan = $trx_persetujuan_pengembangan->where('id_permintaan_pengembangan', $data->id_permintaan_pengembangan); 
        @endphp
        <tr>
            <td></td>
            <td></td>
            <td>Persetujuan Pengembangan</td>
            <td class="{{ $persetujuan->isNotEmpty() && optional($persetujuan->first())->is_approve ? 'status-approved' : ($persetujuan->isNotEmpty() ? 'status-pending' : 'status-not-created') }}">
                {{ $persetujuan->isNotEmpty() ? ($persetujuan->first()->is_approve ? 'Disetujui Penyetuju' : 'Menunggu Persetujuan Penyetuju') : 'Belum Dibuat' }}
            </td>
            <td>{{ optional($persetujuan->first())->approve_at ? Carbon::parse(optional($persetujuan->first())->approve_at)->translatedFormat('d F Y') : '' }}</td>
        </tr>

        {{-- Perencanaan Proyek --}}
        @php 
            $persetujuan = $trx_persetujuan_pengembangan->where('id_permintaan_pengembangan', $data->id_permintaan_pengembangan)->first();
            $perencanaanProyek = $trx_perencanaan_proyek->where('id_persetujuan_pengembangan', $persetujuan->id_persetujuan_pengembangan ?? 0); 
        @endphp
        <tr>
            <td></td>
            <td></td>
            <td>Perencanaan Proyek</td>
            <td class="{{ $perencanaanProyek->isNotEmpty() && optional($perencanaanProyek->first())->is_approve && optional($perencanaanProyek->first())->is_approve_pemverifikasi ? 'status-approved' : ($perencanaanProyek->isNotEmpty() && optional($perencanaanProyek->first())->is_approve ? 'status-approved' : 'status-pending') }}">
                {{ $perencanaanProyek->isNotEmpty() ? (optional($perencanaanProyek->first())->is_approve && optional($perencanaanProyek->first())->is_approve_pemverifikasi ? 'Sudah di Approve Penyetuju' : (optional($perencanaanProyek->first())->is_approve ? 'Sudah di Approve Pemverifikasi' : 'Menunggu Approval Pemverifikasi')) : 'Belum Dibuat' }} 
            </td>
            <td>{{ optional($perencanaanProyek->first())->approve_at ? Carbon::parse(optional($perencanaanProyek->first())->approve_at)->translatedFormat('d F Y') : '' }}</td>
        </tr>

        {{-- Perencanaan Kebutuhan --}}
        @php 
            $persetujuan = $trx_persetujuan_pengembangan->where('id_permintaan_pengembangan', $data->id_permintaan_pengembangan)->first();
            $perencanaanKebutuhan = $trx_perencanaan_kebutuhan->where('id_persetujuan_pengembangan', $persetujuan->id_persetujuan_pengembangan ?? 0);
        @endphp
        <tr>
            <td></td>
            <td></td>
            <td>Perencanaan Kebutuhan</td>
            <td class="{{ $perencanaanKebutuhan->isNotEmpty() && optional($perencanaanKebutuhan->first())->is_approve && optional($perencanaanKebutuhan->first())->is_approve_pemverifikasi ? 'status-approved' : ($perencanaanKebutuhan->isNotEmpty() && optional($perencanaanKebutuhan->first())->is_approve ? 'status-approved' : 'status-pending') }}">
                {{ $perencanaanKebutuhan->isNotEmpty() ? (optional($perencanaanKebutuhan->first())->is_approve && optional($perencanaanKebutuhan->first())->is_approve_pemverifikasi ? 'Sudah di Approve Penyetuju' : (optional($perencanaanKebutuhan->first())->is_approve ? 'Sudah di Approve Pemverifikasi' : 'Menunggu Approval Pemverifikasi')) : 'Belum Dibuat' }} 
            </td>
            <td>{{ optional($perencanaanKebutuhan->first())->approve_at ? Carbon::parse(optional($perencanaanKebutuhan->first())->approve_at)->translatedFormat('d F Y') : '' }}</td>
        </tr>

        {{-- Analisis Desain --}}
        @php 
            $analisisDesain = $trx_analisis_desain->where('id_permintaan_pengembangan', $data->id_permintaan_pengembangan); 
        @endphp
        <tr>
            <td></td>
            <td></td>
            <td>Analisis Desain</td>
            <td class="{{ $analisisDesain->isNotEmpty() && optional($analisisDesain->first())->is_approve ? 'status-approved' : ($analisisDesain->isNotEmpty() ? 'status-pending' : 'status-not-created') }}">
                {{ $analisisDesain->isNotEmpty() ? ($analisisDesain->first()->is_approve ? 'Disetujui Penyetuju' : 'Menunggu Persetujuan Penyetuju') : 'Belum Dibuat' }} 
            </td>
            <td>{{ optional($analisisDesain->first())->approve_at ? Carbon::parse(optional($analisisDesain->first())->approve_at)->translatedFormat('d F Y') : '' }}</td>
        </tr>

        {{-- Quality Assurance Testing --}}
        @php 
            $qualityAssurance = $trx_quality_assurance_testing->where('id_permintaan_pengembangan', $data->id_permintaan_pengembangan); 
        @endphp
        <tr>
            <td></td>
            <td></td>
            <td>Quality Assurance Testing</td>
            <td class="{{ $qualityAssurance->isNotEmpty() && optional($qualityAssurance->first())->is_approve ? 'status-approved' : ($qualityAssurance->isNotEmpty() ? 'status-pending' : 'status-not-created') }}">
                {{ $qualityAssurance->isNotEmpty() ? ($qualityAssurance->first()->is_approve ? 'Disetujui Penyetuju' : 'Menunggu Persetujuan Penyetuju') : 'Belum Dibuat' }} 
            </td>
            <td>{{ optional($qualityAssurance->first())->approve_at ? Carbon::parse(optional($qualityAssurance->first())->approve_at)->translatedFormat('d F Y') : '' }}</td>
        </tr>

        {{-- User Acceptance Testing --}}
        @php 
            $userAcceptanceTesting = $trx_user_acceptance_testing->where('id_permintaan_pengembangan', $data->id_permintaan_pengembangan); 
        @endphp
        <tr>
            <td></td>
            <td></td>
            <td>User Acceptance Testing</td>
            <td class="{{ $userAcceptanceTesting->isNotEmpty() && optional($userAcceptanceTesting->first())->is_approve ? 'status-approved' : ($userAcceptanceTesting->isNotEmpty() ? 'status-pending' : 'status-not-created') }}">
                {{ $userAcceptanceTesting->isNotEmpty() ? ($userAcceptanceTesting->first()->is_approve ? 'Disetujui Penyetuju' : 'Menunggu Persetujuan Penyetuju') : 'Belum Dibuat' }}
            </td>
            <td>{{ optional($userAcceptanceTesting->first())->approve_at ? Carbon::parse(optional($userAcceptanceTesting->first())->approve_at)->translatedFormat('d F Y') : '' }}</td>
        </tr>

        {{-- Serah Terima Aplikasi --}}
        @php 
            $serahTerima = $trx_serah_terima_aplikasi->where('id_permintaan_pengembangan', $data->id_permintaan_pengembangan); 
        @endphp
        <tr>
            <td></td>
            <td></td>
            <td>Serah Terima Aplikasi</td>
            <td class="{{ $serahTerima->isNotEmpty() && optional($serahTerima->first())->is_approve ? 'status-approved' : ($serahTerima->isNotEmpty() ? 'status-pending' : 'status-not-created') }}">
                {{ $serahTerima->isNotEmpty() ? ($serahTerima->first()->is_approve ? 'Disetujui Penyetuju' : 'Menunggu Persetujuan Penyetuju') : 'Belum Dibuat' }}
            </td>
            <td>{{ optional($serahTerima->first())->approve_at ? Carbon::parse(optional($serahTerima->first())->approve_at)->translatedFormat('d F Y') : '' }}</td>
        </tr>
    @endforeach
</table>

<div style="text-align: right; font-style: italic; font-size: 12px; margin-top: 10px;">
    <p>{{ Carbon::now()->format('d-m-Y H:i:s') }}</p>
</div>

</body>
</html>

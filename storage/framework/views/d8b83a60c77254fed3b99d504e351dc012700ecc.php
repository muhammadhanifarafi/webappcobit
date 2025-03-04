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

<?php $__currentLoopData = $dataQAT; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $qualityassurancetesting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="header">
    <table>
        <tr>
            <td rowspan="4">
                <img src="data:image/png;base64,<?php echo e(base64_encode(file_get_contents(public_path('img/logo_ptsi.png')))); ?>" alt="Logo" width="100">
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
            <td>Tahun Revisi</td>
            <td>2024</td>
        </tr>
        <tr class="side-content-header">
            <td>Halaman</td>
            <td>1</td>
        </tr>
    </table>
</div>

<h4 class="text-right" style="font-size:11px;"><strong>NO: <?php echo e($qualityassurancetesting->nomor_dokumen); ?></strong></h4>
<h3 class="text-center bold" style="font-size:11px;">QUALITY ASSURANCE TESTING</h3>
<table style="font-size:11px;">
    <tr>
        <td style="width: 25%;">Nama Aplikasi</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;"><?php echo e($qualityassurancetesting->nama_aplikasi); ?></td>
    </tr>
    <tr>
        <td style="width: 25%;">Jenis Aplikasi</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;">
            <?php
                $jenisAplikasiArray = json_decode($qualityassurancetesting->jenis_aplikasi, true);
            ?>
            <?php echo e(is_array($jenisAplikasiArray) ? implode(', ', $jenisAplikasiArray) : $qualityassurancetesting->jenis_aplikasi); ?>

        </td>
    </tr>
    <tr>
        <td style="width: 25%;">Unit Pemilik Proses Bisnis</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;"><?php echo e($qualityassurancetesting->unit_pemilik); ?></td>
    </tr>
    <tr>
        <td style="width: 25%;">Kebutuhan Non Fungsional</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;"><?php echo $qualityassurancetesting->kebutuhan_nonfungsional; ?></td>
    </tr>
    <tr>
        <td style="width: 25%;">Lokasi Pengujian</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;"><?php echo e($qualityassurancetesting->lokasi_pengujian); ?></td>
    </tr>
    <tr>
        <td style="width: 25%;">Tanggal Pengujian</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;"><?php echo e(\Carbon\Carbon::parse($qualityassurancetesting->tgl_pengujian)->format('d M Y')); ?></td>
    </tr>
    <tr>
        <td style="width: 25%;">Manual Book</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;"><?php echo e($qualityassurancetesting->manual_book); ?></td>
    </tr>
</table>
<h3 class="text-center bold" style="font-size:11px;">ITEM PENGUJIAN</h3>
<table style="width: 100%; table-layout: fixed; border-collapse: collapse;">
    <tr>
        <td colspan="6" style="text-align: left; font-size: 12px;"><b>Hasil Uji QAT</b></td>
    </tr>
    <?php 
        $filteredDetailQAT = $detailQAT->filter(function($dqa) {
            return $dqa->jenis == 1;
        });
        
        if ($filteredDetailQAT->isNotEmpty()): 
    ?>
    <tr>
        <td colspan="6" style="font-size: 12px;"><b>A. Pengujian Kinerja Sistem Informasi</b></td>
    </tr>
    <tr>
        <td class="side-content-header" style="width: 5%;"><b>No</b></td>
        <td class="side-content-header" style="width: 25%;"><b>Fitur/Fungsi *)</b></td>
        <td class="side-content-header" style="width: 15%;"><b>Steps</b></td>
        <td class="side-content-header" style="width: 25%;"><b>Ekspektasi</b></td>
        <td class="side-content-header" style="width: 15%;"><b>Berhasil / Gagal</b></td>
        <td class="side-content-header" style="width: 15%;"><b>Notes</b></td>
    </tr>
    <?php $no_a = 1; foreach($detailQAT as $dqa){ 
        if($dqa->jenis == 1){  
    ?>
    <tr>
        <td class="side-content-header" style="font-weight:normal"><?= $no_a; ?></td>
        <td class="side-content-header" style="font-weight:normal"><?= $dqa->fitur_fungsi ?></td>
        <td class="side-content-header" style="font-weight:normal"><?= $dqa->steps ?></td>
        <td class="side-content-header" style="font-weight:normal"><?= $dqa->ekspetasi ?></td>
        <td class="side-content-header" style="font-weight:normal"><?= $dqa->berhasil_gagal ?></td>
        <td class="side-content-header" style="font-weight:normal"><?= $dqa->notes ?></td>
    </tr>
    <?php } ?>
    <?php $no_a++; } ?>
    <?php endif; ?>
    <?php 
        $filteredDetailQAT = $detailQAT->filter(function($dqa) {
            return $dqa->jenis == 2;
        });

        if ($filteredDetailQAT->isNotEmpty()): 
    ?>
    <tr>
        <td colspan="6" style="font-size: 12px;"><b>B. Pengujian Keamanan Sistem Informasi</b></td>
    </tr>
    <tr>
        <td class="side-content-header" style="width: 5%;"><b>No</b></td>
        <td class="side-content-header" style="width: 25%;"><b>Fitur/Fungsi *)</b></td>
        <td class="side-content-header" style="width: 15%;"><b>Steps</b></td>
        <td class="side-content-header" style="width: 25%;"><b>Ekspektasi</b></td>
        <td class="side-content-header" style="width: 15%;"><b>Berhasil / Gagal</b></td>
        <td class="side-content-header" style="width: 15%;"><b>Notes</b></td>
    </tr>
    <?php $no_b = 1; foreach($detailQAT as $dqa){ 
        if($dqa->jenis == 2){  
    ?>
    <tr>
        <td class="side-content-header" style="font-weight:normal"><?= $no_b; ?></td>
        <td class="side-content-header" style="font-weight:normal"><?= $dqa->fitur_fungsi ?></td>
        <td class="side-content-header" style="font-weight:normal"><?= $dqa->steps ?></td>
        <td class="side-content-header" style="font-weight:normal"><?= $dqa->ekspetasi ?></td>
        <td class="side-content-header" style="font-weight:normal"><?= $dqa->berhasil_gagal ?></td>
        <td class="side-content-header" style="font-weight:normal"><?= $dqa->notes ?></td>
    </tr>
    <?php } ?>
    <?php $no_b++; } ?>
    <?php endif; ?>
    <?php 
        $filteredDetailQAT = $detailQAT->filter(function($dqa) {
            return $dqa->jenis == 3;
        });
        
        if ($filteredDetailQAT->isNotEmpty()): 
    ?>
    <tr>
        <td colspan="6" style="font-size: 12px;"><b>C. Pengujian Keandalan Sistem Informasi</b></td>
    </tr>
    <tr>
        <td class="side-content-header" style="width: 5%;"><b>No</b></td>
        <td class="side-content-header" style="width: 25%;"><b>Fitur/Fungsi *)</b></td>
        <td class="side-content-header" style="width: 15%;"><b>Steps</b></td>
        <td class="side-content-header" style="width: 25%;"><b>Ekspektasi</b></td>
        <td class="side-content-header" style="width: 15%;"><b>Berhasil / Gagal</b></td>
        <td class="side-content-header" style="width: 15%;"><b>Notes</b></td>
    </tr>
    <?php $no_c = 1; foreach($detailQAT as $dqa){ 
        if($dqa->jenis == 3){  
    ?>
    <tr>
        <td class="side-content-header" style="font-weight:normal"><?= $no_c; ?></td>
        <td class="side-content-header" style="font-weight:normal"><?= $dqa->fitur_fungsi ?></td>
        <td class="side-content-header" style="font-weight:normal"><?= $dqa->steps ?></td>
        <td class="side-content-header" style="font-weight:normal"><?= $dqa->ekspetasi ?></td>
        <td class="side-content-header" style="font-weight:normal"><?= $dqa->berhasil_gagal ?></td>
        <td class="side-content-header" style="font-weight:normal"><?= $dqa->notes ?></td>
    </tr>
    <?php } ?>
    <?php $no_c++; } ?>
    <?php endif; ?>
</table>

<h3 class="text-center bold" style="font-size:11px;">DATA PENGUJI</h3>
<table style="width: 100%; table-layout: fixed; border-collapse: collapse;">
    <tr>
        <td class="side-content-header" style="width: 5%;"><b>NO</b></td>
        <td class="side-content-header" style="width: 30%;"><b>NIK Penguji</b></td>
        <td class="side-content-header" style="width: 35%;"><b>Nama Penguji</b></td>
        <td class="side-content-header" style="width: 30%;"><b>Tanda Tangan</b></td>
    </tr>
    <?php if (count($detailPengujiQAT) == 0) { ?>
        <tr>
            <td class="side-content-header" style="font-weight:normal"></td>
            <td class="side-content-header" style="font-weight:normal"></td>
            <td class="side-content-header" style="font-weight:normal"></td>
            <td class="side-content-header" style="font-weight:normal"></td>
        </tr>
    <?php } else { ?>
        <?php $no_d = 1; foreach($detailPengujiQAT as $dpq){ ?>
            <tr>
                <td class="side-content-header" style="font-weight:normal"><?= $no_d; ?></td>
                <td class="side-content-header" style="font-weight:normal"><?= $dpq->nik_penguji ?></td>
                <td class="side-content-header" style="font-weight:normal"><?= $dpq->nama_penguji ?></td>
                <td class="side-content-header" style="font-weight:normal"></td>
            </tr>
            <?php $no_d++; } ?>
    <?php } ?>
</table>

<table class="table" style="font-size:11px; width: 100%; table-layout: fixed; border-collapse: collapse;">
    <tr>
        <td class="text-center" colspan="2">Tanggal: <?php echo e(\Carbon\Carbon::parse($qualityassurancetesting->approve_at_penyetuju)->format('d M Y')); ?></td>
        <td class="text-center" colspan="2">Tanggal: <?php echo e(\Carbon\Carbon::parse($qualityassurancetesting->approve_at_penyetuju)->format('d M Y')); ?></td>
    </tr>
    <tr>
        <th class="text-center" colspan="2">Disiapkan oleh</th>
        <th class="text-center" colspan="2">Disetujui oleh</th>
    </tr>
    <tr>
        <?php if($qualityassurancetesting->path_qrcode_pemohon): ?>
            <td colspan="2" style="height: 100px; text-align: center;">
                <img src="<?php echo e(URL($qualityassurancetesting->path_qrcode_pemohon)); ?>" alt="QR Code Pemohon" style="max-height: 100px;">
            </td>
        <?php else: ?>
            <td colspan="2"></td>  <!-- Tampilkan kolom kosong jika path_qrcode_pemohon kosong -->
        <?php endif; ?>

        <?php if($qualityassurancetesting->path_qrcode_penyetuju): ?>
            <td colspan="2" style="height: 100px; text-align: center;">
                <img src="<?php echo e(URL($qualityassurancetesting->path_qrcode_penyetuju)); ?>" alt="QR Code Penyetuju" style="max-height: 100px;">
            </td>
        <?php else: ?>
            <td colspan="2"></td>  <!-- Tampilkan kolom kosong jika path_qrcode_penyetuju kosong -->
        <?php endif; ?>
    </tr>
    <tr>
        <td class="text-center" colspan="2"><?php echo e($qualityassurancetesting->nama_pemohon); ?><br><?php echo e($qualityassurancetesting->jabatan_pemohon); ?></td>
        <td class="text-center" colspan="2"><?php echo e($qualityassurancetesting->nama_penyetuju); ?><br><?php echo e($qualityassurancetesting->jabatan_penyetuju); ?></td>
    </tr>
</table>

<?php if(!$loop->last): ?>
<div class="page-break"></div>
<?php endif; ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</body>
</html>
<?php /**PATH /home/cobitptsico/public_html/resources/views/quality_assurance_testing/dokumen_rev.blade.php ENDPATH**/ ?>
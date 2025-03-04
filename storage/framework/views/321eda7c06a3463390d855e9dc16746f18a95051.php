<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Dokumen Analisis Desain</title>
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

<?php $__currentLoopData = $dataanalisis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $analisis): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="header">
    <table>
        <tr>
            <td rowspan="4">
                <img src="data:image/png;base64,<?php echo e(base64_encode(file_get_contents(public_path('img/logo_ptsi.png')))); ?>" alt="Logo" width="100">
            </td>
            <td rowspan="4" class="text-center" style="width:350px;">
                <h3>Analisis & Desain Sistem Informasi</h3>
            </td>
            <td class="side-content-header">No. Dokumen</td>
            <td class="side-content-header">FP-DTI03-0D</td>
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

<h4 class="text-right"  style="font-size:11px;"><strong>NO: <?php echo e($analisis->nomor_dokumen); ?></strong></h4>
<h3 class="text-center bold" style="font-size:11px;">ANALISIS & DESAIN SISTEM INFORMASI</h3>
<table style="font-size:11px;">
    <tr>
        <th style="width: 25%;">Nomor Proyek</th>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;"><?php echo e($analisis->nomor_proyek); ?></td>
    </tr>
    <tr>
        <th style="width: 25%;">Nama Proyek</th>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;"><?php echo e($analisis->nama_proyek); ?></td>
    </tr>
    <!-- <tr>
        <th style="width: 25%;">Deskripsi</th>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;"><?php echo $analisis->deskripsi_proyek; ?></td>
    </tr> -->
    <tr>
        <th style="width: 25%;">Manajer Proyek</th>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;"><?php echo e($analisis->manajer_proyek); ?></td>
    </tr>
    <tr>
        <th style="width: 25%;">Kebutuhan Fungsional dan Deskripsi</th>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;"><?php echo $analisis->kebutuhan_fungsional; ?></td>
    </tr>
    <tr>
        <th style="width: 25%;">Arsitektur Sistem Informasi</th>
        <td style="width: 5%;">:</td>
        <td><?php echo $analisis->gambaran_arsitektur; ?></td>
    </tr>
    <tr>
        <th style="width: 25%;">Desain Detil</th>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;"><?php echo $analisis->detil_arsitektur; ?></td>
    </tr>
    <tr>
        <th style="width: 25%;">Lampiran</th>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;"></td>
    </tr>
</table>

<table class="table" style="font-size:11px; width: 100%; table-layout: fixed; border-collapse: collapse;">
    <tr>
        <td class="text-center" colspan="2">Tanggal: <?php echo e(\Carbon\Carbon::parse($analisis->tanggal_disiapkan)->format('d M Y')); ?></td>
        <td class="text-center" colspan="2">Tanggal: <?php echo e(\Carbon\Carbon::parse($analisis->tanggal_disetujui)->format('d M Y')); ?></td>
    </tr>
    <tr>
        <th class="text-center" colspan="2">Disiapkan oleh</th>
        <th class="text-center" colspan="2">Disetujui oleh</th>
        <tr>
            <?php if($analisis->path_qrcode_pemohon): ?>
                <td colspan="2" style="height: 100px; text-align: center;">
                    <img src="<?php echo e(URL($analisis->path_qrcode_pemohon)); ?>" alt="QR Code Pemohon" style="max-height: 100px;">
                </td>
            <?php else: ?>
                <td colspan="2"></td> <!-- Kolom kosong jika path_qrcode_pemohon kosong -->
            <?php endif; ?>

            <?php if($analisis->path_qrcode_penyetuju): ?>
                <td colspan="2" style="height: 100px; text-align: center;">
                    <img src="<?php echo e(URL($analisis->path_qrcode_penyetuju)); ?>" alt="QR Code Penyetuju" style="max-height: 100px;">
                </td>
            <?php else: ?>
                <td colspan="2"></td> <!-- Kolom kosong jika path_qrcode_penyetuju kosong -->
            <?php endif; ?>
        </tr>
    <tr>
        <td class="text-center" colspan="2"><?php echo e($analisis->nama_pemohon); ?><br><?php echo e($analisis->jabatan_pemohon); ?></td>
        <td class="text-center" colspan="2"><?php echo e($analisis->nama_penyetuju); ?><br><?php echo e($analisis->jabatan_penyetuju); ?></td>
    </tr>
</table>

<?php if(!$loop->last): ?>
<div class="page-break"></div>
<?php endif; ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</body>
</html>
<?php /**PATH /home/cobitptsico/public_html/resources/views/analisis_desain/dokumen.blade.php ENDPATH**/ ?>
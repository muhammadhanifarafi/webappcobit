<?php
use Carbon\Carbon;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Dokumen Permintaan Pengembangan Sistem Informasi</title>
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

<?php $__currentLoopData = $datapermintaan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permintaan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="header">
    <table>
        <tr>
            <td rowspan="4">
                <img src="data:image/png;base64,<?php echo e(base64_encode(file_get_contents(public_path('img/logo_ptsi.png')))); ?>" alt="Logo" width="100">
            </td>
            <td rowspan="4" class="text-center" style="width:350px;">
                <h3>Permintaan Pengembangan Sistem Informasi</h3>
            </td>
            <td class="side-content-header">No. Dokumen</td>
            <td class="side-content-header">FP-DTI03-04</td>
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

<h4 class="text-right" style="font-size:11px;"><strong>NO: <?php echo e($datapermintaan->first()->nomor_dokumen); ?></strong></h4>
<h3 class="text-center bold" style="font-size:11px;">INFO KEBUTUHAN SISTEM INFORMASI</h3>
<table style="font-size:11px;">
    <tr>
        <td style="width: 25%;">Judul</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;"><?php echo $permintaan->judul; ?></td>
    </tr>
    <tr>
        <td style="width: 25%;">Latar Belakang</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;"><?php echo $permintaan->latar_belakang; ?></td>
    </tr>
    <tr>
        <td style="width: 25%;">Tujuan</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;"><?php echo $permintaan->tujuan; ?></td>
    </tr>
    <tr>
        <td style="width: 25%;">Target Implementasi Sistem</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;"><?php echo \Carbon\Carbon::parse($permintaan->target_implementasi_sistem)->format('d M Y'); ?></td>
    </tr>
    <tr>
        <td style="width: 25%;">Fungsi-fungsi Sistem Informasi</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;"><?php echo $permintaan->fungsi_sistem_informasi; ?></td>
    </tr>
    <tr>
        <td style="width: 25%;">Jenis Aplikasi</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;">
            <?php
                $jenisAplikasiArray = json_decode($permintaan->jenis_aplikasi, true);
            ?>
            <?php echo e(is_array($jenisAplikasiArray) ? implode(', ', $jenisAplikasiArray) : $permintaan->jenis_aplikasi); ?>

        </td>
    </tr>
    <tr>
        <td style="width: 25%;">Pengguna</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;">        
            <?php
                $penggunaArray = json_decode($permintaan->pengguna, true);
            ?>
            <?php echo e(is_array($penggunaArray) ? implode(', ', $penggunaArray) : $permintaan->pengguna); ?>

        </td>
    </tr>
    <tr>
        <td style="width: 25%;">Uraian Permintaan Tambahan/Khusus</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;"><?php echo $permintaan->uraian_permintaan_tambahan; ?></td>
    </tr>
    <!-- <tr>
        <td style="width: 25%;">Lampiran</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;"><?php echo e($permintaan->lampiran); ?></td>
    </tr> -->
</table>

<table class="table" style="font-size:11px; width: 100%; table-layout: fixed; border-collapse: collapse;">
    <tr>
        <td class="text-center" colspan="2">Tanggal: <?php echo e(\Carbon\Carbon::parse($permintaan->created_at)->format('d M Y')); ?></td>
        <td class="text-center" colspan="2">
            <?php if($permintaan->approve_at): ?>
                Tanggal: <?php echo e(\Carbon\Carbon::parse($permintaan->approve_at)->format('d M Y')); ?>

            <?php else: ?>
                Tanggal:
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <th class="text-center" colspan="2">Disiapkan oleh</th>
        <th class="text-center" colspan="2">Disetujui oleh</th>
    </tr>
    <tr>
        <?php if($permintaan->path_qrcode_pemohon): ?>
            <td colspan="2" style="height: 100px; text-align: center;">
                <img src="<?php echo e(URL($permintaan->path_qrcode_pemohon)); ?>" alt="QR Code Pemohon" style="max-height: 100px;">
            </td>
        <?php else: ?>
            <td colspan="2"></td>  <!-- Tampilkan kolom kosong jika path_qrcode_pemohon kosong -->
        <?php endif; ?>

        <?php if($permintaan->path_qrcode_penyetuju): ?>
            <td colspan="2" style="height: 100px; text-align: center;">
                <img src="<?php echo e(URL($permintaan->path_qrcode_penyetuju)); ?>" alt="QR Code Penyetuju" style="max-height: 100px;">
            </td>
        <?php else: ?>
            <td colspan="2"></td>  <!-- Tampilkan kolom kosong jika path_qrcode_penyetuju kosong -->
        <?php endif; ?>
    </tr>
    <tr>
        <td class="text-center" colspan="2"><?php echo e($permintaan->nama_pemohon); ?><br><?php echo e($permintaan->jabatan_pemohon); ?></td>
        <td class="text-center" colspan="2"><?php echo e($permintaan->nama_penyetuju); ?><br><?php echo e($permintaan->jabatan_penyetuju); ?></td>
    </tr>
</table>

<?php if(!$loop->last): ?>
<div class="page-break"></div>
<?php endif; ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</body>
</html>
<?php /**PATH /home/cobitptsico/public_html/resources/views/permintaan_pengembangan/dokumen.blade.php ENDPATH**/ ?>
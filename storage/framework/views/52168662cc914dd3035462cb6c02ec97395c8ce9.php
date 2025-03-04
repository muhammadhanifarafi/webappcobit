

<?php $__env->startSection('title'); ?>
    Tambah Detail Quality Assurance Testing
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <?php echo \Illuminate\View\Factory::parentPlaceholder('breadcrumb'); ?>
    <li class="active">Daftar QAT</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
  <div class="modal-content" style="width: 50%;">
    <div class="modal-body">

    <form action="<?php echo e(route('detail-ttd-qat.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="id_quality_assurance_testing" value="<?php echo e($qualityAssuranceTesting->id_quality_assurance_testing); ?>">
        <div class="form-group">
            <label for="nama_aplikasi">Nama Aplikasi</label>
            <input type="text" class="form-control" value="<?php echo e($qualityAssuranceTesting->nama_aplikasi); ?>" readonly>
        </div>
        
        <div class="form-group">
            <label for="nik_penguji">NIK Penguji</label>
            <input type="text" class="form-control" id="nik_penguji" name="nik_penguji" required>
        </div>

        <div class="form-group">
            <label for="nama_penguji">Nama Penguji</label>
            <input type="text" class="form-control" id="nama_penguji" name="nama_penguji" required>
        </div>

        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
        <a href="<?php echo e(route('detail-ttd-qat.index', ['id' => $qualityAssuranceTesting->id_quality_assurance_testing])); ?>" class="btn btn-warning btn-sm">Kembali</a>
    </form>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/cobitdemoptsico/public_html/resources/views/detail-ttd-qat/create.blade.php ENDPATH**/ ?>
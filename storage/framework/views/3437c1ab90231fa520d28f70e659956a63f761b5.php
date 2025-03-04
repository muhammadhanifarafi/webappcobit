

<?php $__env->startSection('title'); ?>
    Edit Detail User Acceptance Testing
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <?php echo \Illuminate\View\Factory::parentPlaceholder('breadcrumb'); ?>
    <li class="active">Daftar UAT</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
  <div class="modal-content" style="width: 50%;">

    <div class="modal-body">
    <!-- Form untuk mengedit data -->
    <form action="<?php echo e(route('detail-user-acceptance-testing.update', $detail->id_detail_user_acceptance_testing)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?> <!-- Untuk memberi tahu bahwa ini adalah form untuk update -->

        <input type="hidden" name="id_user_acceptance_testing" value="<?php echo e($detail->id_user_acceptance_testing); ?>">

        <div class="form-group">
            <label for="nama_aplikasi">Nama Aplikasi</label>
            <input type="text" class="form-control" value="<?php echo e($detail->nama_aplikasi); ?>" readonly>
        </div>

        <div class="form-group">
            <label for="jenis">Bagian</label>
            <select name="jenis" class="form-control" required>
                <option value="" disabled selected>Pilih Bagian</option>
                <option value="1" <?php echo e($detail->jenis == 1 ? 'selected' : ''); ?>>A. MANAJEMEN USER</option>
                <option value="2" <?php echo e($detail->jenis == 2 ? 'selected' : ''); ?>>B. KEAMANAN APLIKASI</option>
                <option value="3" <?php echo e($detail->jenis == 3 ? 'selected' : ''); ?>>C. FORM PERUSAHAAN</option>
            </select>
        </div>

        <div class="form-group">
            <label for="item">Item</label>
            <input type="text" name="item" class="form-control" value="<?php echo e($detail->item); ?>" required>
        </div>

        <div class="form-group">
            <label for="pass_fail">Pass / Fail</label>
            <select name="pass_fail" class="form-control" required>
                <option value="" disabled selected>Pilih Status</option>
                <option value="Pass" <?php echo e($detail->pass_fail == 'Pass' ? 'selected' : ''); ?>>Pass</option>
                <option value="Fail" <?php echo e($detail->pass_fail == 'Fail' ? 'selected' : ''); ?>>Fail</option>
            </select>
        </div>

        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea name="notes" class="form-control" required><?php echo e($detail->notes); ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-sm">Update</button>
        <a href="<?php echo e(route('detail-user-acceptance-testing.index', ['id' => $detail->id_user_acceptance_testing])); ?>" class="btn btn-warning btn-sm">Kembali</a>
    </form>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/cobitdemoptsico/public_html/resources/views/detail-user-acceptance-testing/edit.blade.php ENDPATH**/ ?>
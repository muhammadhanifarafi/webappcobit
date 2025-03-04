

<?php $__env->startSection('title'); ?>
    Edit Detail Quality Assurance Testing
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <?php echo \Illuminate\View\Factory::parentPlaceholder('breadcrumb'); ?>
    <li class="active">Edit Detail Quality Assurance Testing</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
  <div class="modal-content" style="width: 50%;">
    <div class="modal-body">
    <form action="<?php echo e(route('detail-quality-assurance-testing.update', $detail->id_detail_quality_assurance_testing)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?> <!-- Ini untuk menandakan bahwa ini adalah request PUT -->

        <div class="form-group">
            <label for="jenis">Jenis</label>
            <select name="jenis" class="form-control" required>
                <option value="1" <?php echo e($detail->jenis == '1' ? 'selected' : ''); ?>>A. Pengujian Kinerja Sistem Informasi</option>
                <option value="2" <?php echo e($detail->jenis == '2' ? 'selected' : ''); ?>>B. Pengujian Keamanan Sistem Informasi</option>
                <option value="3" <?php echo e($detail->jenis == '3' ? 'selected' : ''); ?>>C. Pengujian Keandalan Sistem Informasi</option>
            </select>
        </div>

        <div class="form-group">
            <label for="fitur_fungsi">Fitur Fungsi</label>
            <input type="text" name="fitur_fungsi" class="form-control" value="<?php echo e($detail->fitur_fungsi); ?>" required>
        </div>

        <div class="form-group">
            <label for="steps">Steps</label>
            <textarea name="steps" class="form-control" required><?php echo e($detail->steps); ?></textarea>
        </div>

        <div class="form-group">
            <label for="ekspetasi">Ekspetasi</label>
            <textarea name="ekspetasi" class="form-control" required><?php echo e($detail->ekspetasi); ?></textarea>
        </div>

        <div class="form-group">
            <label for="berhasil_gagal">Berhasil / Gagal</label>
            <select name="berhasil_gagal" class="form-control" required>
                <option value="Berhasil" <?php echo e($detail->berhasil_gagal == 'Berhasil' ? 'selected' : ''); ?>>Berhasil</option>
                <option value="Gagal" <?php echo e($detail->berhasil_gagal == 'Gagal' ? 'selected' : ''); ?>>Gagal</option>
            </select>
        </div>

        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea name="notes" class="form-control"><?php echo e($detail->notes); ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="<?php echo e(route('detail-quality-assurance-testing.index', ['id' => $detail->id_quality_assurance_testing])); ?>" class="btn btn-warning">Kembali</a>
    </form>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/cobitdemoptsico/public_html/resources/views/detail_quality_assurance_testing/edit.blade.php ENDPATH**/ ?>
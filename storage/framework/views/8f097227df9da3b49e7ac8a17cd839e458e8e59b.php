<!-- resources/views/detail_quality_assurance_testing_with_ttd/index.blade.php -->



<?php $__env->startSection('content'); ?>
<div class="container">
    <h2>Daftar Detail Quality Assurance Testing dengan TTD</h2>
    <a href="<?php echo e(route('detail-quality-assurance-testing-with-ttd.create', ['id_quality_assurance_testing' => $id])); ?>" class="btn btn-success">Tambah Data</a>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>ID Penguji QAT</th>
                <th>NIK Penguji</th>
                <th>Nama Penguji</th>
                <th>Nama TTD</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($detail->id_penguji_qat); ?></td>
                <td><?php echo e($detail->nik_penguji); ?></td>
                <td><?php echo e($detail->nama_penguji); ?></td>
                <td><?php echo e($detail->nama_ttd); ?></td>
                <td>
                    <a href="<?php echo e(route('detail-quality-assurance-testing-with-ttd.edit', $detail->id)); ?>" class="btn btn-warning">Edit</a>
                    <form action="<?php echo e(route('detail-quality-assurance-testing-with-ttd.destroy', $detail->id)); ?>" method="POST" style="display:inline;">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/cobitdemoptsico/public_html/resources/views/detail_ttd_quality_assurance_testing/index.blade.php ENDPATH**/ ?>
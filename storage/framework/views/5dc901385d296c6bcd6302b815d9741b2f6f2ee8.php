

<?php $__env->startSection('title'); ?>
    Daftar Item Pengujian User Acceptance Testing
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <?php echo \Illuminate\View\Factory::parentPlaceholder('breadcrumb'); ?>
    <li class="active">Daftar UAT</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <a href="<?php echo e(route('detail-user-acceptance-testing.create')); ?>/<?php echo e($id); ?>" class="btn btn-primary btn-sm">Tambah Data</a>
                <a href="<?php echo e(route('quality_assurance_testing.index')); ?>" class="btn btn-warning btn-sm">Kembali</a>
            </div>
            <div class="box-body table-responsive">
                    <?php echo csrf_field(); ?>
                    <table class="table table-stiped table-bordered" style="font-size: 12px;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Item</th>
                                <th>Pass / Fail</th>
                                <th>Notes</th>
                                <th>Jenis</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($loop->iteration); ?></td>
                                    <td><?php echo e($detail->item); ?></td>
                                    <td><?php echo e($detail->pass_fail); ?></td>
                                    <td><?php echo e($detail->notes); ?></td>
                                    <td>
                                        <?php switch($detail->jenis):
                                            case (1): ?>
                                                Manajemen User
                                                <?php break; ?>
                                            <?php case (2): ?>
                                                Keamanan Aplikasi
                                                <?php break; ?>
                                            <?php case (3): ?>
                                                Form Perusahaan
                                                <?php break; ?>
                                            <?php default: ?>
                                                Unknown
                                        <?php endswitch; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('detail-user-acceptance-testing.edit', $detail->id_detail_user_acceptance_testing)); ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="<?php echo e(route('detail-user-acceptance-testing.destroy', $detail->id_detail_user_acceptance_testing)); ?>" method="POST" style="display:inline;">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/cobitptsico/public_html/resources/views/detail-user-acceptance-testing/index.blade.php ENDPATH**/ ?>
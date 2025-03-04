<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form">
    <div class="modal-dialog modal-lg" role="document">
        <form action="" method="post" class="form-horizontal">
            <?php echo csrf_field(); ?>
            <?php echo method_field('post'); ?>

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="condition-set">
                            <label for="id_permintaan_pengembangan" class="col-lg-2 col-lg-offset-1 control-label">Nomor Dokumen Permintaan</label>
                            <div class="col-lg-6">
                                <select name="id_permintaan_pengembangan" id="id_permintaan_pengembangan" class="form-control" required>
                                    <option value="">Pilih Nomor Dokumen</option>
                                    <?php $__currentLoopData = $trx_permintaan_pengembangan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>" <?php echo e(isset($selected_id) && $selected_id == $key ? 'selected' : ''); ?>>
                                        <?php echo e($item); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <span class="help-block with-errors"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="nama_proyek" class="col-lg-2 col-lg-offset-1 control-label">Nama Proyek</label>
                        <div class="col-lg-6">
                            <input type="text" name="nama_proyek" id="nama_proyek" class="form-control" required autofocus placeholder="Masukkan Nama Proyek">
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                    <label for="deskripsi" class="col-lg-2 col-lg-offset-1 control-label">Deskripsi Proyek</label>
                        <div class="col-lg-6">
                            <textarea type="text" id="editor1" name="deskripsi" id="deskripsi" class="form-control" required autofocus></textarea>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="id_mst_persetujuan" class="col-lg-2 col-lg-offset-1 control-label">Status Persetujuan</label>
                        <div class="col-lg-6">
                            <select name="id_mst_persetujuan" id="id_mst_persetujuan" class="form-control" required>
                                <option value="">Pilih Status Persetujuan</option>
                                <?php $__currentLoopData = $mst_persetujuan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($item); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="id_mst_persetujuanalasan" class="col-lg-2 col-lg-offset-1 control-label">Alasan Persetujuan</label>
                        <div class="col-lg-6">
                            <select name="id_mst_persetujuanalasan" id="id_mst_persetujuanalasan" class="form-control" required>
                                <option value="">Pilih Alasan</option>
                                <?php $__currentLoopData = $mst_persetujuanalasan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($item); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="namapemohon" class="col-lg-2 col-lg-offset-1 control-label">Nama Pemohon</label>
                        <div class="col-lg-6">
                            <input type="text" name="namapemohon" id="namapemohon" class="form-control" required autofocus placeholder="Masukkan Nama Pemohon"></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="namapeninjau" class="col-lg-2 col-lg-offset-1 control-label">Nama Penyetuju</label>
                        <div class="col-lg-6">
                            <input type="text" name="namapeninjau" id="namapeninjau" class="form-control" required autofocus placeholder="Masukkan Nama Penyetuju"></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="jabatanpeninjau" class="col-lg-2 col-lg-offset-1 control-label">Jabatan Pemohon</label>
                        <div class="col-lg-6">
                            <input type="text" name="jabatanpeninjau" id="jabatanpeninjau" class="form-control" required autofocus placeholder="Masukkan Jabatan Pemohon"></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="namapenyetuju" class="col-lg-2 col-lg-offset-1 control-label">Jabatan Penyetuju</label>
                        <div class="col-lg-6">
                            <input type="text" name="namapenyetuju" id="namapenyetuju" class="form-control" required autofocus placeholder="Masukkan Jabatan Penyetuju"></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-flat btn-primary"><i class="fa fa-save"></i> Simpan</button>
                    <button type="button" class="btn btn-sm btn-flat btn-warning" data-dismiss="modal"><i class="fa fa-arrow-circle-left"></i> Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php /**PATH C:\laragon\www\ptsi_\resources\views/persetujuan_pengembangan/form.blade.php ENDPATH**/ ?>
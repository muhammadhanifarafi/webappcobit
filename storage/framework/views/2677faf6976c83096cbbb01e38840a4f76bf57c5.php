<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form">
    <div class="modal-dialog modal-lg" role="document">
        <form action="<?php echo e(route('perencanaan_proyek.store')); ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('post'); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <!-- <div class="form-group row">
                        <label for="nomor_proyek" class="col-lg-2 col-lg-offset-1 control-label">Nomor Proyek</label>
                        <div class="col-lg-6">
                            <input type="text" name="nomor_proyek" id="nomor_proyek" class="form-control" required autofocus placeholder="Input Nama Proyek"></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div> -->
                    <div class="condition-set-1">
                        <div class="form-group row">
                                <label for="id_persetujuan_pengembangan" class="col-lg-2 col-lg-offset-1 control-label">Nomor Permintaan Dokumen</label>
                                <div class="col-lg-6">
                                    <select name="id_persetujuan_pengembangan" id="id_persetujuan_pengembangan" class="form-control" required>
                                        <option value="">Pilih Nomor Dokumen Permintaan</option>
                                        <?php $__currentLoopData = $trx_persetujuan_pengembangan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($key); ?>"><?php echo e($item); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <span class="help-block with-errors"></span>
                                </div>
                        </div>
                    </div>
                    <div class="condition-set-2">
                        <div class="form-group row">
                            <label for="nama_proyek" class="col-lg-2 col-lg-offset-1 control-label">Nama Proyek</label>
                                <div class="col-lg-6">
                                    <input type="text" name="nama_proyek" id="nama_proyek" class="form-control" required placeholder="Input Nama Proyek"></input>
                                    <span class="help-block with-errors"></span>
                                </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="pemilik_proyek" class="col-lg-2 col-lg-offset-1 control-label">Pemilik Proyek</label>
                        <div class="col-lg-6">
                            <input type="text" name="pemilik_proyek" id="pemilik_proyek" class="form-control" required autofocus placeholder="Input Nama Pemilik Proyek"></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="manajer_proyek" class="col-lg-2 col-lg-offset-1 control-label">Manajer Proyek</label>
                        <div class="col-lg-6">
                            <input type="text" name="manajer_proyek" id="manajer_proyek" class="form-control" required autofocus placeholder="Input Nama Manajer Proyek"></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="ruang_lingkup" class="col-lg-2 col-lg-offset-1 control-label">Ruang Lingkup</label>
                        <div class="col-lg-6">
                            <textarea type="text" name="ruang_lingkup" id="editor1" class="form-control" required autofocus placeholder="Input Ruang Lingkup Proyek"></textarea>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="tanggal_mulai" class="col-lg-2 col-lg-offset-1 control-label">Tanggal Mulai</label>
                        <div class="col-lg-6">
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" required autofocus></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="target_selesai" class="col-lg-2 col-lg-offset-1 control-label">Target Selesai</label>
                        <div class="col-lg-6">
                            <input type="date" name="target_selesai" id="target_selesai" class="form-control" required autofocus></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="nilai_kontrak" class="col-lg-2 col-lg-offset-1 control-label">Nilai Proyek</label>
                        <div class="col-lg-6">
                            <input type="number" name="nilai_kontrak" id="nilai_kontrak" class="form-control" required autofocus placeholder="Input Nilai Proyek"></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="lampiran" class="col-lg-2 col-lg-offset-1 control-label">Lampiran</label>
                        <div class="col-lg-6">
                            <input type="file" name="lampiran" id="lampiran" class="form-control">
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="nik_pemohon" class="col-lg-2 col-lg-offset-1 control-label">Nama Pemohon IT</label>
                        <div class="col-lg-6">
                            <select name="nik_pemohon" id="nik_pemohon" class="form-control select2" required>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <input type="hidden" name="jabatan_pemohon" id="jabatan_pemohon" class="form-control"></input>
                    <input type="hidden" name="nama_pemohon" id="nama_pemohon" class="form-control"></input>
                    <div class="form-group row">
                        <label for="tanggal_disiapkan" class="col-lg-2 col-lg-offset-1 control-label">Tanggal TTD Disiapkan</label>
                        <div class="col-lg-6">
                            <input type="date" name="tanggal_disiapkan" id="tanggal_disiapkan" class="form-control" required autofocus></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="nik_pemverifikasi" class="col-lg-2 col-lg-offset-1 control-label">Nama Pemverifikasi IT</label>
                        <div class="col-lg-6">
                            <select name="nik_pemverifikasi" id="nik_pemverifikasi" class="form-control select2" required>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <input type="hidden" name="jabatan_pemverifikasi" id="jabatan_pemverifikasi" class="form-control"></input>
                    <input type="hidden" name="nama_pemverifikasi" id="nama_pemverifikasi" class="form-control"></input>
                    <div class="form-group row">
                        <label for="tanggal_verifikasi" class="col-lg-2 col-lg-offset-1 control-label">Tanggal TTD Diverifikasi IT</label>
                        <div class="col-lg-6">
                            <input type="date" name="tanggal_verifikasi" id="tanggal_verifikasi" class="form-control" required autofocus></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="nik_penyetuju" class="col-lg-2 col-lg-offset-1 control-label">Nama Penyetuju</label>
                        <div class="col-lg-6">
                            <select name="nik_penyetuju" id="nik_penyetuju" class="form-control select2" required>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <input type="hidden" name="jabatan_penyetuju" id="jabatan_penyetuju" class="form-control"></input>
                    <input type="hidden" name="nama_penyetuju" id="nama_penyetuju" class="form-control"></input>
                    <div class="form-group row">
                        <label for="tanggal_disetujui" class="col-lg-2 col-lg-offset-1 control-label">Tanggal TTD Disetujui</label>
                        <div class="col-lg-6">
                            <input type="date" name="tanggal_disetujui" id="tanggal_disetujui" class="form-control" required autofocus></input>
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
</div><?php /**PATH /home/cobitdemoptsico/public_html/resources/views/perencanaan_proyek/form.blade.php ENDPATH**/ ?>
<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form">
    <div class="modal-dialog modal-lg" role="document">
        <form action="" method="post" class="form-horizontal">
            <?php echo csrf_field(); ?>
            <?php echo method_field('post'); ?>

            <div class="modal-content">
                    <div class="modal-header" style="margin-top: 20px;">
                        <h4 class="modal-title">Contoh Pengisian / Referensi Pengisian</h4>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-3" style="margin-left: 15px;">
                            <a href="/refrensi_dokumen/analisis_kebutuhan.pdf" target="_blank" class="btn btn-sm btn-info">
                                <i class="fa fa-file-pdf-o"></i> Lihat Dokumen Acuan
                            </a>
                            <p style="font-style: italic; font-size: 12px;">*Sebagai panduan acuan pengisian</p>
                        </div>
                    </div>
                <div class="modal-body">
                    <div class="condition-set-1">
                        <div class="form-group row">
                            <label for="id_persetujuan_pengembangan" class="col-lg-2 col-lg-offset-1 control-label">Nomor Permintaan Dokumen</label>
                            <div class="col-lg-6">
                                <select name="id_persetujuan_pengembangan" id="id_persetujuan_pengembangan" class="form-control" required>
                                    <option value="">Pilih Nomor Dokumen Permintaan</option>
                                    <?php $__currentLoopData = $trx_persetujuan_pengembangan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($item->id_persetujuan_pengembangan); ?>">
                                            <?php echo e($item->nomor_dokumen); ?> - <?php echo e($item->judul); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <span class="help-block with-errors"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="stakeholders" class="col-lg-2 col-lg-offset-1 control-label">Stakeholders</label>
                        <div class="col-lg-6">
                            <textarea type="text" name="stakeholders" id="editor1" class="form-control" required autofocus placeholder="Input Stakeholders Proyek"></textarea>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="kriteria_aplikasi" class="col-lg-2 col-lg-offset-1 control-label">Kriteria Aplikasi</label>
                        <div class="col-lg-6">
                            <select name="kriteria_aplikasi" id="kriteria_aplikasi" class="form-control" required>
                                <option value="">Pilih Kriteria Aplikasi</option>
                                <?php $__currentLoopData = $kriteria_aplikasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($item->id); ?>"><?php echo e($item->nama); ?> - <?php echo e($item->deskripsi); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="model_backup" class="col-lg-2 col-lg-offset-1 control-label">Model Backup</label>
                        <div class="col-lg-6">
                            <select name="model_backup" id="model_backup" class="form-control" required>
                                <option value="">Pilih Model Backup</option>
                                <?php $__currentLoopData = $model_backup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($item->id); ?>"><?php echo e($item->nama); ?> - <?php echo e($item->deskripsi); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="bahasa_pemrograman" class="col-lg-2 col-lg-offset-1 control-label">Bahasa Pemrograman</label>
                        <div class="col-lg-6">
                            <input type="text" name="bahasa_pemrograman" id="bahasa_pemrograman" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="resource_server" class="col-lg-2 col-lg-offset-1 control-label">Tipe Resource Server</label>
                        <div class="col-lg-6">
                            <select name="resource_server" id="resource_server" class="form-control" required>
                                <option value="">Pilih Tipe Resource Server</option>
                                <?php $__currentLoopData = $resource_server; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($item->id); ?>"><?php echo e($item->nama); ?> - <?php echo e($item->deskripsi); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="tipe_server" class="col-lg-2 col-lg-offset-1 control-label">Tipe Server</label>
                        <div class="col-lg-6">
                            <select name="tipe_server" id="tipe_server" class="form-control" required>
                                <option value="">Pilih Tipe Server</option>
                                <?php $__currentLoopData = $tipe_server; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($item->id); ?>"><?php echo e($item->nama); ?> - <?php echo e($item->deskripsi); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="tipe_database" class="col-lg-2 col-lg-offset-1 control-label">Tipe Database</label>
                        <div class="col-lg-6">
                            <select name="tipe_database" id="tipe_database" class="form-control" required>
                                <option value="">Pilih Tipe Database</option>
                                <?php $__currentLoopData = $tipe_database; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($item->id); ?>"><?php echo e($item->nama); ?> - <?php echo e($item->deskripsi); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="tipe_storage" class="col-lg-2 col-lg-offset-1 control-label">Tipe Storage</label>
                        <div class="col-lg-6">
                            <select name="tipe_storage" id="tipe_storage" class="form-control" required>
                                <option value="">Pilih Tipe Storage</option>
                                <?php $__currentLoopData = $tipe_storage; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($item->id); ?>"><?php echo e($item->nama); ?> - <?php echo e($item->deskripsi); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="kebutuhan_fungsional" class="col-lg-2 col-lg-offset-1 control-label">Kebutuhan Fungsional</label>
                        <div class="col-lg-6">
                            <textarea type="text" name="kebutuhan_fungsional" id="editor2" class="form-control" required autofocus placeholder="Input Kebutuhan Fungsional (Kebutuhan Struktural)"></textarea>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="kebutuhan_nonfungsional" class="col-lg-2 col-lg-offset-1 control-label">Kebutuhan Non-fungsional</label>
                        <div class="col-lg-6">
                            <textarea type="text" name="kebutuhan_nonfungsional" id="editor3" class="form-control" required autofocus placeholder="Input Kebutuhan Non-Fungsional (Kebutuhan Non-Struktural)"></textarea>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="lampiran" class="col-lg-2 col-lg-offset-1 control-label">Lampiran</label>
                        <div class="col-lg-6">
                            <input type="file" name="lampiran" id="lampiran" class="form-control" autofocus></input>
                            <span class="help-block with-errors"></span>
                            <small class="form-text text-muted">Upload Maximal File 4 MB Tipe File PDF *</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="nik_pemohon" class="col-lg-2 col-lg-offset-1 control-label">Nama Pemohon DTI</label>
                        <div class="col-lg-6">
                            <select name="nik_pemohon" id="nik_pemohon" class="form-control select2" required>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <input type="hidden" name="jabatan_pemohon" id="jabatan_pemohon" class="form-control"></input>
                    <input type="hidden" name="nama_pemohon" id="nama_pemohon" class="form-control"></input>
                    <!-- <div class="form-group row">
                        <label for="tanggal_disiapkan" class="col-lg-2 col-lg-offset-1 control-label">Tanggal TTD Disiapkan</label>
                        <div class="col-lg-6">
                            <input type="date" name="tanggal_disiapkan" id="tanggal_disiapkan" class="form-control" required autofocus></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div> -->
                    <div class="form-group row">
                        <label for="nik_pemverifikasi" class="col-lg-2 col-lg-offset-1 control-label">Nama Pemverifikasi DTI</label>
                        <div class="col-lg-6">
                            <select name="nik_pemverifikasi" id="nik_pemverifikasi" class="form-control select2" required>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <input type="hidden" name="jabatan_pemverifikasi" id="jabatan_pemverifikasi" class="form-control"></input>
                    <input type="hidden" name="nama_pemverifikasi" id="nama_pemverifikasi" class="form-control"></input>
                    <!-- <div class="form-group row">
                        <label for="tanggal_verifikasi" class="col-lg-2 col-lg-offset-1 control-label">Tanggal TTD Diverifikasi IT</label>
                        <div class="col-lg-6">
                            <input type="date" name="tanggal_verifikasi" id="tanggal_verifikasi" class="form-control" required autofocus></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div> -->
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
                    <!-- <div class="form-group row">
                        <label for="tanggal_disetujui" class="col-lg-2 col-lg-offset-1 control-label">Tanggal TTD Disetujui</label>
                        <div class="col-lg-6">
                            <input type="date" name="tanggal_disetujui" id="tanggal_disetujui" class="form-control" required autofocus></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div> -->
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-flat btn-primary"><i class="fa fa-save"></i> Simpan</button>
                    <button type="button" class="btn btn-sm btn-flat btn-warning" data-dismiss="modal"><i class="fa fa-arrow-circle-left"></i> Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php /**PATH /home/cobitptsico/public_html/resources/views/perencanaan_kebutuhan/form.blade.php ENDPATH**/ ?>
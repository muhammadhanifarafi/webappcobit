<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form">
    <div class="modal-dialog modal-lg" role="document">
        <!-- Tombol di luar form untuk melihat dokumen acuan/referensi -->
        <form action="<?php echo e(route('perencanaan_proyek.store')); ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('post'); ?>
                <div class="modal-content">
                    <div class="modal-header" style="margin-top: 20px;">
                        <h4 class="modal-title">Contoh Pengisian / Referensi Pengisian</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-3" style="margin-left: 15px;">
                            <a href="/refrensi_dokumen/perencanaan_proyek.pdf" target="_blank" class="btn btn-sm btn-info">
                                <i class="fa fa-file-pdf-o"></i> Lihat Dokumen Acuan
                            </a>
                            <p style="font-style: italic; font-size: 12px;">*Sebagai panduan acuan pengisian</p>
                        </div>
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
                                <label for="id_persetujuan_pengembangan" class="col-lg-2 col-lg-offset-1 control-label">Nomor Permintaan Dokumen <span class="required-field" style="color: red;">*</span></label>
                                <div class="col-lg-6">
                                    <select name="id_persetujuan_pengembangan" id="id_persetujuan_pengembangan" class="form-control" required>
                                        <option value="">Pilih Nomor Dokumen Permintaan</option>
                                        <?php $__currentLoopData = $trx_persetujuan_pengembangan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($item->id_persetujuan_pengembangan); ?>">
                                                <?php echo e($item->nomor_dokumen); ?> - <?php echo e($item->judul); ?> <!-- Menampilkan nomor dokumen dan judul proyek -->
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <span class="help-block with-errors"></span>
                                </div>
                        </div>
                    </div>
                    <div class="condition-set-2">
                        <div class="form-group row">
                            <label for="nama_proyek" class="col-lg-2 col-lg-offset-1 control-label">Nama Proyek<span class="required-field" style="color: red;">*</span></label>
                                <div class="col-lg-6">
                                    <input type="text" name="nama_proyek" id="nama_proyek" class="form-control" required placeholder="Input Nama Proyek"></input>
                                    <span class="help-block with-errors"></span>
                                </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="pemilik_proyek" class="col-lg-2 col-lg-offset-1 control-label">Pemilik Proyek<span class="required-field" style="color: red;">*</span></label>
                        <div class="col-lg-6">
                            <input type="text" name="pemilik_proyek" id="pemilik_proyek" class="form-control" required autofocus placeholder="Input Nama Unit Kerja Pemilik Proyek"></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="manajer_proyek" class="col-lg-2 col-lg-offset-1 control-label">Manajer Proyek<span class="required-field" style="color: red;">*</span></label>
                        <div class="col-lg-6">
                            <input type="text" name="manajer_proyek" id="manajer_proyek" class="form-control" required autofocus placeholder="Input Nama Manajer Proyek"></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="ruang_lingkup" class="col-lg-2 col-lg-offset-1 control-label">Ruang Lingkup<span class="required-field" style="color: red;">*</span></label>
                        <div class="col-lg-6">
                            <textarea type="text" name="ruang_lingkup" id="editor1" class="form-control" required autofocus placeholder="Input Deskripsi Ruang Lingkup Proyek"></textarea>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="tanggal_mulai" class="col-lg-2 col-lg-offset-1 control-label">Tanggal Mulai<span class="required-field" style="color: red;">*</span></label>
                        <div class="col-lg-6">
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" required autofocus></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="target_selesai" class="col-lg-2 col-lg-offset-1 control-label">Target Selesai<span class="required-field" style="color: red;">*</span></label>
                        <div class="col-lg-6">
                            <input type="date" name="target_selesai" id="target_selesai" class="form-control" required autofocus></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="nilai_kontrak" class="col-lg-2 col-lg-offset-1 control-label">Nilai Proyek<span class="required-field" style="color: red;">*</span></label>
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
                        <label for="nik_pemohon" class="col-lg-2 col-lg-offset-1 control-label">Nama Pemohon DTI<span class="required-field" style="color: red;">*</span></label>
                        <div class="col-lg-6">
                            <select name="nik_pemohon" id="nik_pemohon" class="form-control select2" required>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <input type="hidden" name="jabatan_pemohon" id="jabatan_pemohon" class="form-control"></input>
                    <input type="hidden" name="nama_pemohon" id="nama_pemohon" class="form-control"></input>
                    <!-- <div class="form-group row">
                        <label for="tanggal_disiapkan" class="col-lg-2 col-lg-offset-1 control-label">Tanggal TTD Disiapkan<span class="required-field" style="color: red;">*</span></label>
                        <div class="col-lg-6">
                            <input type="date" name="tanggal_disiapkan" id="tanggal_disiapkan" class="form-control" required autofocus></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div> -->
                    <div class="form-group row">
                        <label for="nik_pemverifikasi" class="col-lg-2 col-lg-offset-1 control-label">Nama Pemverifikasi DTI<span class="required-field" style="color: red;">*</span></label>
                        <div class="col-lg-6">
                            <select name="nik_pemverifikasi" id="nik_pemverifikasi" class="form-control select2" required>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <input type="hidden" name="jabatan_pemverifikasi" id="jabatan_pemverifikasi" class="form-control"></input>
                    <input type="hidden" name="nama_pemverifikasi" id="nama_pemverifikasi" class="form-control"></input>
                    <!-- <div class="form-group row">
                        <label for="tanggal_verifikasi" class="col-lg-2 col-lg-offset-1 control-label">Tanggal TTD Diverifikasi IT<span class="required-field" style="color: red;">*</span></label>
                        <div class="col-lg-6">
                            <input type="date" name="tanggal_verifikasi" id="tanggal_verifikasi" class="form-control" required autofocus></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div> -->
                    <div class="form-group row">
                        <label for="nik_penyetuju" class="col-lg-2 col-lg-offset-1 control-label">Nama Penyetuju<span class="required-field" style="color: red;">*</span></label>
                        <div class="col-lg-6">
                            <select name="nik_penyetuju" id="nik_penyetuju" class="form-control select2" required>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <input type="hidden" name="jabatan_penyetuju" id="jabatan_penyetuju" class="form-control"></input>
                    <input type="hidden" name="nama_penyetuju" id="nama_penyetuju" class="form-control"></input>
                    <!-- <div class="form-group row">
                        <label for="tanggal_disetujui" class="col-lg-2 col-lg-offset-1 control-label">Tanggal TTD Disetujui<span class="required-field" style="color: red;">*</span></label>
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
</div><?php /**PATH /home/cobitptsico/public_html/resources/views/perencanaan_proyek/form.blade.php ENDPATH**/ ?>
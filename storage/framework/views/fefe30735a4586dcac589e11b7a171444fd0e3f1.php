<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form">
    <div class="modal-dialog modal-lg" role="document">
        <form action="" method="post" class="form-horizontal" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('post'); ?>

            <div class="modal-content">
            <div class="modal-header" style="margin-top: 20px;">
                        <h4 class="modal-title">Contoh Pengisian / Referensi Pengisian</h4>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-3" style="margin-left: 15px;">
                            <a href="/refrensi_dokumen/analisa_kebutuhan.pdf" target="_blank" class="btn btn-sm btn-info">
                                <i class="fa fa-file-pdf-o"></i> Lihat Dokumen Acuan
                            </a>
                            <p style="font-style: italic; font-size: 12px;">*Sebagai panduan acuan pengisian</p>
                        </div>
                    </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="condition-set">
                            <label for="id_permintaan_pengembangan" class="col-lg-2 col-lg-offset-1 control-label">Nomor Dokumen Permintaan</label>
                            <div class="col-lg-8">
                                <select name="id_permintaan_pengembangan" id="id_permintaan_pengembangan" class="form-control" required>
                                    <option value="">Pilih Nomor Dokumen Permintaan</option>
                                    <?php $__currentLoopData = $trx_permintaan_pengembangan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($item->id_permintaan_pengembangan); ?>">
                                            <?php echo e($item->nomor_dokumen); ?> - <?php echo e($item->judul); ?> <!-- Menampilkan nomor dokumen dan judul -->
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <span class="help-block with-errors"></span>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="form-group row">
                        <label for="deskripsi_proyek" class="col-lg-2 col-lg-offset-1 control-label">Deskripsi Proyek</label>
                        <div class="col-lg-6">
                            <textarea type="text" id="editor1" name="deskripsi_proyek" id="deskripsi_proyek" class="form-control" required autofocus placeholder="Input Deskripsi Proyek"></textarea>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="manajer_proyek" class="col-lg-2 col-lg-offset-1 control-label">Manajer Proyek</label>
                        <div class="col-lg-6">
                            <input type="text" name="manajer_proyek" id="manajer_proyek" class="form-control" required autofocus placeholder="Input Manajer Proyek">
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="kebutuhan_fungsi" class="col-lg-2 col-lg-offset-1 control-label">Kebutuhan Fungsional</label>
                        <div class="col-lg-6">
                            <textarea type="text" id="editor2" name="kebutuhan_fungsi" id="kebutuhan_fungsi" class="form-control" required autofocus placeholder="Input Kebutuhan Fungsional (Struktural)"></textarea>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div> -->
                    <div class="form-group row">
                        <label for="gambaran_arsitektur" class="col-lg-2 col-lg-offset-1 control-label">Gambaran Arsitektur Sistem</label>
                        <div class="col-lg-8">
                            <textarea type="text" id="editor3" name="gambaran_arsitektur" id="gambaran_arsitektur" class="form-control" required autofocus placeholder="Input Gambaran Arsitektur Sistem (Dapat berupa Link)"></textarea>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="detil_arsitektur" class="col-lg-2 col-lg-offset-1 control-label">Detail Arsitektur Sistem</label>
                        <div class="col-lg-8">
                            <textarea type="text" id="editor4" name="detil_arsitektur" id="detil_arsitektur" class="form-control" required autofocus placeholder="Input Detail Arsitektur Sistem (Dapat Berupa Link)"></textarea>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="lampiran_mockup" class="col-lg-2 col-lg-offset-1 control-label">Lampiran Mockup (Link canva, figma, atau lainnya)</label>
                        <div class="col-lg-8">
                            <textarea type="text" id="editor5" name="lampiran_mockup" id="lampiran_mockup" class="form-control" required autofocus placeholder="Input Link Canva, Figma, atau Lainnya untuk Lampiran Mockup"></textarea>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="lampiran_1" class="col-lg-2 col-lg-offset-1 control-label">Lampiran Dokumen 1</label>
                        <div class="col-lg-8">
                            <input type="file" name="lampiran_1" id="lampiran_1" class="form-control" accept=".pdf, .jpg, .jpeg, .png" onchange="validateFile()">
                            <span class="help-block with-errors" id="fileError"></span>
                            <small class="form-text text-muted">Upload Maximal File 2 MB Tipe File PDF/JPG/PNG, File Lampiran Merupakan Korelasi dari Gambaran Arsitektur Sistem, Detil Arsitektur Sistem ataupun dapat berupa dokumen pendukung lainnya *</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="lampiran_2" class="col-lg-2 col-lg-offset-1 control-label">Lampiran Dokumen 2</label>
                        <div class="col-lg-8">
                            <input type="file" name="lampiran_2" id="lampiran_2" class="form-control" accept=".pdf, .jpg, .jpeg, .png" onchange="validateFile()">
                            <span class="help-block with-errors" id="fileError"></span>
                            <small class="form-text text-muted">Upload Maximal File 2 MB Tipe File PDF/JPG/PNG, File Lampiran Merupakan Korelasi dari Gambaran Arsitektur Sistem, Detil Arsitektur Sistem ataupun dapat berupa dokumen pendukung lainnya *</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="lampiran_3" class="col-lg-2 col-lg-offset-1 control-label">Lampiran Dokumen 3</label>
                        <div class="col-lg-8">
                            <input type="file" name="lampiran_3" id="lampiran_3" class="form-control" accept=".pdf, .jpg, .jpeg, .png" onchange="validateFile()">
                            <span class="help-block with-errors" id="fileError"></span>
                            <small class="form-text text-muted">Upload Maximal File 2 MB Tipe File PDF/JPG/PNG, File Lampiran Merupakan Korelasi dari Gambaran Arsitektur Sistem, Detil Arsitektur Sistem ataupun dapat berupa dokumen pendukung lainnya *</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="lampiran_4" class="col-lg-2 col-lg-offset-1 control-label">Lampiran Dokumen 4</label>
                        <div class="col-lg-8">
                            <input type="file" name="lampiran_4" id="lampiran_4" class="form-control" accept=".pdf, .jpg, .jpeg, .png" onchange="validateFile()">
                            <span class="help-block with-errors" id="fileError"></span>
                            <small class="form-text text-muted">Upload Maximal File 2 MB Tipe File PDF/JPG/PNG, File Lampiran Merupakan Korelasi dari Gambaran Arsitektur Sistem, Detil Arsitektur Sistem ataupun dapat berupa dokumen pendukung lainnya *</small>
                        </div>
                    </div>
                    <input type="hidden" name="jabatan_pemohon" id="jabatan_pemohon" class="form-control"></input>
                    <input type="hidden" name="nama_pemohon" id="nama_pemohon" class="form-control"></input>
                    <div class="form-group row">
                        <label for="nik_pemohon" class="col-lg-2 col-lg-offset-1 control-label">Nama Pemohon DTI</label>
                        <div class="col-lg-8">
                            <select name="nik_pemohon" id="nik_pemohon" class="form-control select2" required>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <!-- <div class="form-group row">
                        <label for="jabatan_pemohon" class="col-lg-2 col-lg-offset-1 control-label">Jabatan Pemohon</label>
                        <div class="col-lg-6">
                            <input type="text" name="jabatan_pemohon" id="jabatan_pemohon" class="form-control" required autofocus placeholder="Input Jabatan Pemohon">
                            <span class="help-block with-errors"></span>
                        </div>
                    </div> -->
                    <!-- <div class="form-group row">
                        <label for="tanggal_disiapkan" class="col-lg-2 col-lg-offset-1 control-label">Tanggal TTD Disiapkan</label>
                        <div class="col-lg-6">
                            <input type="date" name="tanggal_disiapkan" id="tanggal_disiapkan" class="form-control" required autofocus>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div> -->
                    <div class="form-group row">
                        <label for="nik_penyetuju" class="col-lg-2 col-lg-offset-1 control-label">Nama Penyetuju AVP DTI</label>
                        <div class="col-lg-8">
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
                            <input type="date" name="tanggal_disetujui" id="tanggal_disetujui" class="form-control" required autofocus>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div> -->
                    <!-- <div class="form-group row">
                        <label for="status" class="col-lg-2 col-lg-offset-1 control-label">Persetujuan</label>
                        <div class="col-lg-6">
                            <select name="status" id="status" class="form-control" required>
                                <option selected>-- Pilih Persetujuan --</option>
                                <option value="disetujui">Disetujui</option>
                                <option value="tidak_disetujui">Tidak Disetujui</option>
                                <option value="ditunda">Ditunda</option>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div> -->
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-flat btn-primary button-save"><i class="fa fa-save"></i> Simpan</button>
                    <button type="button" class="btn btn-sm btn-flat btn-warning" data-dismiss="modal"><i class="fa fa-arrow-circle-left"></i> Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php /**PATH /home/cobitptsico/public_html/resources/views/analisis_desain/form.blade.php ENDPATH**/ ?>
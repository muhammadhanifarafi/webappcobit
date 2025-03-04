<div class="modal fade" id="modal-view" tabindex="-1" role="dialog" aria-labelledby="modalViewLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalViewLabel">Lihat Perancanaan Kebutuhan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="box box-default">
                    <div class="box-body">
                        <div class="row">
                            <!-- Kolom 1 -->
                            <div class="col-md-6">
                                <!-- Lampiran -->
                                <div class="form-group">
                                    <label for="lampiran">Lampiran</label>
                                    <a href="#" id="lampiran-link" target="_blank" class="btn btn-primary">Lihat Lampiran</a>
                                </div>
                                <!-- Nama Pemohon -->
                                <div class="form-group">
                                    <label for="nama_pemohon">Nama Pemohon</label>
                                    <input type="text" name="nama_pemohon" id="nama_pemohon" class="form-control" readonly>
                                </div>
                            </div>

                            <!-- Kolom 2 -->
                            <div class="col-md-6">
                                <!-- Jabatan Pemohon -->
                                <div class="form-group">
                                    <label for="jabatan_pemohon">Jabatan Pemohon</label>
                                    <input type="text" name="jabatan_pemohon" id="jabatan_pemohon" class="form-control" readonly>
                                </div>
                                <!-- Tanggal Disiapkan -->
                                <div class="form-group">
                                    <label for="tanggal_disiapkan">Tanggal Disiapkan</label>
                                    <input type="date" name="tanggal_disiapkan" id="tanggal_disiapkan" class="form-control" readonly>
                                </div>
                            </div>

                            <!-- Kolom 3 -->
                            <div class="col-md-6">
                                <!-- Nama Pemverifikasi -->
                                <div class="form-group">
                                    <label for="nama_pemverifikasi">Nama Pemverifikasi</label>
                                    <input type="text" name="nama_pemverifikasi" id="nama_pemverifikasi" class="form-control" readonly>
                                </div>
                                <!-- Jabatan Pemverifikasi -->
                                <div class="form-group">
                                    <label for="jabatan_pemverifikasi">Jabatan Pemverifikasi</label>
                                    <input type="text" name="jabatan_pemverifikasi" id="jabatan_pemverifikasi" class="form-control" readonly>
                                </div>
                            </div>

                            <!-- Kolom 4 -->
                            <div class="col-md-6">
                                <!-- Tanggal Diverifikasi -->
                                <div class="form-group">
                                    <label for="tanggal_disiapkan">Tanggal Diverifikasi</label>
                                    <input type="date" name="tanggal_disiapkan" id="tanggal_disiapkan" class="form-control" readonly>
                                </div>
                                <!-- Nama Penyetuju -->
                                <div class="form-group">
                                    <label for="nama_penyetuju">Nama Penyetuju</label>
                                    <input type="text" name="nama_penyetuju" id="nama_penyetuju" class="form-control" readonly>
                                </div>
                            </div>

                            <!-- Kolom 5 -->
                            <div class="col-md-6">
                                <!-- Jabatan Penyetuju -->
                                <div class="form-group">
                                    <label for="jabatan_penyetuju">Jabatan Penyetuju</label>
                                    <input type="text" name="jabatan_penyetuju" id="jabatan_penyetuju" class="form-control" readonly>
                                </div>
                                <!-- Tanggal Disetujui -->
                                <div class="form-group">
                                    <label for="tanggal_disetujui">Tanggal Disetujui</label>
                                    <input type="date" name="tanggal_disetujui" id="tanggal_disetujui" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /home/cobitdemoptsico/public_html/resources/views/perencanaan_kebutuhan/viewform.blade.php ENDPATH**/ ?>
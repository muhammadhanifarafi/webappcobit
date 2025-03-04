<div class="modal fade" id="modal-view" tabindex="-1" role="dialog" aria-labelledby="modalViewLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalViewLabel">Lihat Analisis Desain</h4>
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
                                <!-- Nama Pemohon -->
                                <div class="form-group">
                                    <label for="nama_pemohon" class="control-label">Nama Pemohon</label>
                                    <input type="text" name="nama_pemohon" id="nama_pemohon" class="form-control" readonly>
                                </div>
                                <!-- Jabatan Pemohon -->
                                <div class="form-group">
                                    <label for="jabatan_pemohon" class="control-label">Jabatan Pemohon</label>
                                    <input type="text" name="jabatan_pemohon" id="jabatan_pemohon" class="form-control" readonly>
                                </div>
                            </div>

                            <!-- Kolom 2 -->
                            <div class="col-md-6">
                                <!-- Tanggal Disiapkan -->
                                <div class="form-group">
                                    <label for="tanggal_disiapkan" class="control-label">Tanggal Disiapkan</label>
                                    <input type="date" name="tanggal_disiapkan" id="tanggal_disiapkan" class="form-control" readonly>
                                </div>
                                <!-- Nama Penyetuju -->
                                <div class="form-group">
                                    <label for="nama_penyetuju" class="control-label">Nama Penyetuju</label>
                                    <input type="text" name="nama_penyetuju" id="nama_penyetuju" class="form-control" readonly>
                                </div>
                            </div>

                            <!-- Kolom 3 -->
                            <div class="col-md-6">
                                <!-- Jabatan Penyetuju -->
                                <div class="form-group">
                                    <label for="jabatan_penyetuju" class="control-label">Jabatan Penyetuju</label>
                                    <input type="text" name="jabatan_penyetuju" id="jabatan_penyetuju" class="form-control" readonly>
                                </div>
                                <!-- Tanggal Disetujui -->
                                <div class="form-group">
                                    <label for="tanggal_disetujui" class="control-label">Tanggal Disetujui</label>
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

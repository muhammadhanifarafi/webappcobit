<div class="modal fade" id="modal-view" tabindex="-1" role="dialog" aria-labelledby="modalViewLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
                <h4 class="modal-title" id="modalViewLabel">Lihat Berita Acara</h4>
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
                                <!-- Hari -->
                                <div class="form-group">
                                    <label for="hari" class="control-label">Hari</label>
                                    <input type="text" name="hari" id="hari" class="form-control" readonly>
                                </div>
                                <!-- Tanggal -->
                                <div class="form-group">
                                    <label for="tanggal" class="control-label">Tanggal</label>
                                    <input type="date" name="tanggal" id="tanggal" class="form-control" readonly>
                                </div>
                            </div>

                            <!-- Kolom 2 -->
                            <div class="col-md-6">
                                <!-- Deskripsi -->
                                <div class="form-group">
                                    <label for="deskripsi" class="control-label">Deskripsi</label>
                                    <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" readonly></textarea>
                                </div>
                                <!-- Lokasi -->
                                <div class="form-group">
                                    <label for="lokasi" class="control-label">Lokasi</label>
                                    <input type="text" name="lokasi" id="lokasi" class="form-control" readonly>
                                </div>
                            </div>

                            <!-- Kolom 3 -->
                            <div class="col-md-6">
                                <!-- Nama Aplikasi -->
                                <div class="form-group">
                                    <label for="nama_aplikasi" class="control-label">Nama Aplikasi</label>
                                    <input type="text" name="nama_aplikasi" id="nama_aplikasi" class="form-control" readonly>
                                </div>
                                <!-- Nomor Permintaan -->
                                <div class="form-group">
                                    <label for="no_permintaan" class="control-label">Nomor Permintaan</label>
                                    <input type="text" name="no_permintaan" id="no_permintaan" class="form-control" readonly>
                                </div>
                            </div>

                            <!-- Kolom 4 -->
                            <div class="col-md-6">
                                <!-- Keterangan -->
                                <div class="form-group">
                                    <label for="keterangan" class="control-label">Keterangan</label>
                                    <textarea name="keterangan" id="keterangan" class="form-control" rows="3" readonly></textarea>
                                </div>
                                <!-- Nama Pemberi -->
                                <div class="form-group">
                                    <label for="pemberi" class="control-label">Nama Pemberi</label>
                                    <input type="text" name="pemberi" id="pemberi" class="form-control" readonly>
                                </div>
                            </div>

                            <!-- Kolom 5 -->
                            <div class="col-md-6">
                                <!-- Nama Penerima -->
                                <div class="form-group">
                                    <label for="penerima" class="control-label">Nama Penerima</label>
                                    <input type="text" name="penerima" id="penerima" class="form-control" readonly>
                                </div>
                                <!-- NIK Pemberi -->
                                <div class="form-group">
                                    <label for="nik_pemberi" class="control-label">NIK Pemberi</label>
                                    <input type="text" name="nik_pemberi" id="nik_pemberi" class="form-control" readonly>
                                </div>
                            </div>

                            <!-- Kolom 6 -->
                            <div class="col-md-6">
                                <!-- NIK Penerima -->
                                <div class="form-group">
                                    <label for="nik_penerima" class="control-label">NIK Penerima</label>
                                    <input type="text" name="nik_penerima" id="nik_penerima" class="form-control" readonly>
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

<div class="modal fade" id="modal-view" tabindex="-1" role="dialog" aria-labelledby="modalViewLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalViewLabel">Lihat Permintaan Pengembangan</h4>
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
                                <!-- Nomor Dokumen -->
                                <div class="form-group">
                                    <label for="nomor_dokumen">Nomor Dokumen</label>
                                    <input type="text" name="nomor_dokumen" id="nomor_dokumen" class="form-control" readonly>
                                </div>
                                <!-- Target Implementasi Sistem -->
                                <div class="form-group">
                                    <label for="target_implementasi_sistem">Target Implementasi Sistem</label>
                                    <input type="text" name="target_implementasi_sistem" id="target_implementasi_sistem" class="form-control" readonly>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Uraian Latar Belakang -->
                                <div class="form-group">
                                    <label for="latar_belakang">Latar Belakang</label>
                                    <textarea name="latar_belakang" id="latar_belakang" class="form-control" rows="4" readonly></textarea>
                                </div>
                                <!-- Uraian Tujuan -->
                                <div class="form-group">
                                    <label for="tujuan">Tujuan</label>
                                    <textarea name="tujuan" id="tujuan" class="form-control" rows="4" readonly></textarea>
                                </div>
                            </div>

                            <!-- Kolom 2 -->
                            <div class="col-md-6">
                                <!-- Jenis Aplikasi -->
                                <div class="form-group">
                                    <label for="jenis_aplikasi">Jenis Aplikasi</label>
                                    <input type="text" name="jenis_aplikasi" id="jenis_aplikasi" class="form-control" readonly>
                                </div>
                                <!-- Uraian Fungsi - Fungsi Sistem Informasi -->
                                <div class="form-group">
                                    <label for="fungsi_sistem_informasi">Fungsi Sistem Informasi</label>
                                    <textarea name="fungsi_sistem_informasi" id="fungsi_sistem_informasi" class="form-control" rows="4" readonly></textarea>
                                </div>
                            </div>

                            <!-- Kolom 3 -->
                            <div class="col-md-6">
                                <!-- Uraian Permintaan Tambahan -->
                                <div class="form-group">
                                    <label for="uraian_permintaan_tambahan">Uraian Permintaan Tambahan/Khusus</label>
                                    <textarea name="uraian_permintaan_tambahan" id="uraian_permintaan_tambahan" class="form-control" rows="4" readonly></textarea>
                                </div>
                                <!-- Pengguna -->
                                <div class="form-group">
                                    <label for="pengguna">Pengguna</label>
                                    <input type="text" name="pengguna" id="pengguna" class="form-control" readonly>
                                </div>
                            </div>

                            <!-- Kolom 4 -->
                            <div class="col-md-6">
                                <!-- Nama Pemohon -->
                                <div class="form-group">
                                    <label for="nama_pemohon">Nama Pemohon</label>
                                    <input type="text" name="nama_pemohon" id="nama_pemohon" class="form-control" readonly>
                                </div>
                                <!-- Jabatan Pemohon -->
                                <div class="form-group">
                                    <label for="jabatan_pemohon">Jabatan Pemohon</label>
                                    <input type="text" name="jabatan_pemohon" id="jabatan_pemohon" class="form-control" readonly>
                                </div>
                            </div>

                            <!-- Kolom 5 -->
                            <div class="col-md-6">
                                <!-- Tanggal Disiapkan -->
                                <div class="form-group">
                                    <label for="tanggal_disiapkan">Tanggal Disiapkan</label>
                                    <input type="text" name="tanggal_disiapkan" id="tanggal_disiapkan" class="form-control" readonly>
                                </div>
                                <!-- Nama Penyetuju -->
                                <div class="form-group">
                                    <label for="nama_penyetuju">Nama Penyetuju</label>
                                    <input type="text" name="nama_penyetuju" id="nama_penyetuju" class="form-control" readonly>
                                </div>
                                <!-- Lampiran -->
                                <div class="form-group">
                                    <label for="lampiran">Lampiran</label>
                                    <a href="#" id="lampiran-link" target="_blank" class="btn btn-primary">Lihat Lampiran</a>
                                </div>
                            </div>

                            <!-- Kolom 6 -->
                            <div class="col-md-6">
                                <!-- Jabatan Penyetuju -->
                                <div class="form-group">
                                    <label for="jabatan_penyetuju">Jabatan Penyetuju</label>
                                    <input type="text" name="jabatan_penyetuju" id="jabatan_penyetuju" class="form-control" readonly>
                                </div>
                                <!-- Tanggal Disetujui -->
                                <div class="form-group">
                                    <label for="tanggal_disetujui">Tanggal Disetujui</label>
                                    <input type="text" name="tanggal_disetujui" id="tanggal_disetujui" class="form-control" readonly>
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
</div><?php /**PATH /home/cobitptsico/public_html/resources/views/permintaan_pengembangan/viewform.blade.php ENDPATH**/ ?>
<div class="modal fade" id="modal-view" tabindex="-1" role="dialog" aria-labelledby="modalViewLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalViewLabel">Lihat Perencanaan Proyek</h4>
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
                                <!-- Nomor Proyek -->
                                <div class="form-group">
                                    <label for="nomor_proyek">Nomor Proyek</label>
                                    <input type="text" name="nomor_proyek" id="nomor_proyek" class="form-control" readonly>
                                </div>
                                <!-- Nama Proyek -->
                                <div class="form-group">
                                    <label for="nama_proyek">Nama Proyek</label>
                                    <input type="text" name="id_persetujuan_pengembangan" id="nama_proyek" class="form-control" readonly>
                                </div>
                            </div>

                            <!-- Kolom 2 -->
                            <div class="col-md-6">
                                <!-- Pemilik Proyek -->
                                <div class="form-group">
                                    <label for="pemilik_proyek">Pemilik Proyek</label>
                                    <input type="text" name="pemilik_proyek" id="pemilik_proyek" class="form-control" readonly>
                                </div>
                                <!-- Manajer Proyek -->
                                <div class="form-group">
                                    <label for="manajer_proyek">Manajer Proyek</label>
                                    <input type="text" name="manajer_proyek" id="manajer_proyek" class="form-control" readonly>
                                </div>
                            </div>

                            <!-- Kolom 3 -->
                            <div class="col-md-6">
                                <!-- Ruang Lingkup -->
                                <div class="form-group">
                                    <label for="ruang_lingkup">Ruang Lingkup</label>
                                    <textarea name="ruang_lingkup" id="ruang_lingkup" class="form-control" rows="3" readonly></textarea>
                                </div>
                                <!-- Tanggal Mulai -->
                                <div class="form-group">
                                    <label for="tanggal_mulai">Tanggal Mulai</label>
                                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" readonly>
                                </div>
                            </div>

                            <!-- Kolom 4 -->
                            <div class="col-md-6">
                                <!-- Target Selesai -->
                                <div class="form-group">
                                    <label for="target_selesai">Target Selesai</label>
                                    <input type="date" name="target_selesai" id="target_selesai" class="form-control" readonly>
                                </div>
                                <!-- Nilai Kontrak -->
                                <div class="form-group">
                                    <label for="nilai_kontrak">Nilai Kontrak</label>
                                    <input type="text" name="nilai_kontrak" id="nilai_kontrak" class="form-control" readonly>
                                </div>
                            </div>

                            <!-- Kolom 5 -->
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

                            <!-- Kolom 6 -->
                            <div class="col-md-6">
                                <!-- Tanggal Disiapkan -->
                                <div class="form-group">
                                    <label for="tanggal_disiapkan">Tanggal Disiapkan</label>
                                    <input type="date" name="tanggal_disiapkan" id="tanggal_disiapkan" class="form-control" readonly>
                                </div>
                                <!-- Nama Pemverifikasi -->
                                <div class="form-group">
                                    <label for="nama_pemverifikasi">Nama Pemverifikasi</label>
                                    <input type="text" name="nama_pemverifikasi" id="nama_pemverifikasi" class="form-control" readonly>
                                </div>
                            </div>

                            <!-- Kolom 7 -->
                            <div class="col-md-6">
                                <!-- Jabatan Pemverifikasi -->
                                <div class="form-group">
                                    <label for="jabatan_pemverifikasi">Jabatan Pemverifikasi</label>
                                    <input type="text" name="jabatan_pemverifikasi" id="jabatan_pemverifikasi" class="form-control" readonly>
                                </div>
                                <!-- Tanggal Verifikasi -->
                                <div class="form-group">
                                    <label for="tanggal_verifikasi">Tanggal Verifikasi</label>
                                    <input type="date" name="tanggal_verifikasi" id="tanggal_verifikasi" class="form-control" readonly>
                                </div>
                            </div>

                            <!-- Kolom 8 -->
                            <div class="col-md-6">
                                <!-- Nama Penyetuju -->
                                <div class="form-group">
                                    <label for="nama_penyetuju">Nama Penyetuju</label>
                                    <input type="text" name="nama_penyetuju" id="nama_penyetuju" class="form-control" readonly>
                                </div>
                                <!-- Jabatan Penyetuju -->
                                <div class="form-group">
                                    <label for="jabatan_penyetuju">Jabatan Penyetuju</label>
                                    <input type="text" name="jabatan_penyetuju" id="jabatan_penyetuju" class="form-control" readonly>
                                </div>
                            </div>

                            <!-- Kolom 9 -->
                            <div class="col-md-6">
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
<?php /**PATH /home/cobitptsico/public_html/resources/views/perencanaan_proyek/viewform.blade.php ENDPATH**/ ?>
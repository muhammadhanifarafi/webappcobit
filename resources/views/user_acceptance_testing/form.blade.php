<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form">
    <div class="modal-dialog modal-lg" role="document">
        <form action="{{ route('user_acceptance_testing.store') }}" method="post" class="form-horizontal" enctype="multipart/form-data">
            @csrf
            @method('post')
            <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Contoh Pengisian / Referensi Pengisian</h4>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6 col-lg-offset-3 text-center">
                            <a href="/refrensi_dokumen/pengujian_uat.pdf" target="_blank" class="btn btn-sm btn-info">
                                <i class="fa fa-file-pdf-o"></i> Lihat Dokumen Acuan
                            </a>
                        </div>
                    </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="condition-set">
                            <label for="id_permintaan_pengembangan" class="col-lg-2 col-lg-offset-1 control-label">Nomor Dokumen Permintaan</label>
                            <div class="col-lg-6">
                                <select name="id_permintaan_pengembangan" id="id_permintaan_pengembangan" class="form-control" required>
                                    <option value="">Pilih Nomor Dokumen Permintaan</option>
                                    @foreach ($trx_permintaan_pengembangan as $item)
                                        <option value="{{ $item->id_permintaan_pengembangan }}">
                                            {{ $item->nomor_dokumen }} - {{ $item->judul }} <!-- Menampilkan nomor dokumen dan judul -->
                                        </option>
                                    @endforeach
                                </select>
                                <span class="help-block with-errors"></span>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="form-group row">
                        <label for="nomor_proyek" class="col-lg-2 col-lg-offset-1 control-label">Nomor Proyek</label>
                        <div class="col-lg-6">
                            <input type="text" name="nomor_proyek" id="nomor_proyek" class="form-control" required autofocus>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div> -->
                    <!-- <div class="form-group row">
                        <label for="nama_aplikasi" class="col-lg-2 col-lg-offset-1 control-label">Nama Aplikasi</label>
                        <div class="col-lg-6">
                            <input type="text" name="nama_aplikasi" id="nama_aplikasi" class="form-control" required autofocus placeholder="Masukkan Nama Aplikasi">
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="jenis_aplikasi" class="col-lg-2 col-lg-offset-1 control-label">Jenis Aplikasi</label>
                        <div class="col-lg-6">
                            <input type="text" name="jenis_aplikasi" id="jenis_aplikasi" class="form-control" required autofocus placeholder="Masukkan Jenis Aplikasi">
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="kebutuhan_nonfungsional" class="col-lg-2 col-lg-offset-1 control-label">Kebutuhan Non-Fungsional</label>
                        <div class="col-lg-6">
                            <textarea name="kebutuhan_nonfungsional" id="editor2" class="form-control" required autofocus placeholder="Masukkan Kebutuhan Non Fungsional"></textarea>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div> -->
                    <!-- <div class="form-group row">
                        <label for="nama_penguji" class="col-lg-2 col-lg-offset-1 control-label">Nama Penguji</label>
                        <div class="col-lg-6">
                            <input type="text" name="nama_penguji" id="nama_penguji" class="form-control" required autofocus placeholder="Masukkan Nama Penguji"></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div> -->
                    <div class="form-group row">
                        <label for="unit_pemilik_proses_bisnis" class="col-lg-2 col-lg-offset-1 control-label">Unit Pemilik Sistem</label>
                        <div class="col-lg-6">
                            <input type="text" name="unit_pemilik_proses_bisnis" id="unit_pemilik_proses_bisnis" class="form-control" required autofocus placeholder="Masukkan Unit Pemilik Sistem"></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="tanggal_pengujian" class="col-lg-2 col-lg-offset-1 control-label">Tanggal Pengujian</label>
                        <div class="col-lg-6">
                            <input type="date" name="tanggal_pengujian" id="tanggal_pengujian" class="form-control" required autofocus></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="nik_penyetuju" class="col-lg-2 col-lg-offset-1 control-label">Nama Penguji</label>
                        <div class="col-lg-6">
                            <select name="nik_penyetuju" id="nik_penyetuju" class="form-control select2" required>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <input type="hidden" name="jabatan_penyetuju" id="jabatan_penyetuju" class="form-control"></input>
                    <input type="hidden" name="nama_penyetuju" id="nama_penyetuju" class="form-control"></input>
                    <!-- <div class="form-group row">
                        <label for="manual_book" class="col-lg-2 col-lg-offset-1 control-label">Manual Book</label>
                        <div class="col-lg-6">
                            <input type="text" name="manual_book" id="manual_book" class="form-control" required autofocus placeholder="Masukkan Nama Manual Book"></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div> -->
                    <!-- <div class="form-group row">
                        <label for="nik_pemohon" class="col-lg-2 col-lg-offset-1 control-label">Nama Pemohon</label>
                        <div class="col-lg-6">
                            <select name="nik_pemohon" id="nik_pemohon" class="form-control select2" required>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <input type="hidden" name="jabatan_pemohon" id="jabatan_pemohon" class="form-control"></input>
                    <input type="hidden" name="nama_pemohon" id="nama_pemohon" class="form-control"></input> -->
                    <!-- <div class="form-group row">
                        <label for="jabatan_mengetahui" class="col-lg-2 col-lg-offset-1 control-label">Jabatan</label>
                        <div class="col-lg-6">
                            <input type="text" name="jabatan_mengetahui" id="jabatan_mengetahui" class="form-control" required autofocus placeholder="Masukkan Nama Jabatan"></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div> -->
                    <!-- <div class="form-group row">
                        <label for="tanggal_disiapkan" class="col-lg-2 col-lg-offset-1 control-label">Tanggal TTD Disiapkan</label>
                        <div class="col-lg-6">
                            <input type="date" name="tanggal_disiapkan" id="tanggal_disiapkan" class="form-control" required autofocus>
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
                            <input type="date" name="tanggal_disetujui" id="tanggal_disetujui" class="form-control" required autofocus>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div> -->
                    <!-- <div class="form-group row">
                        <label for="jabatan_penyetuju" class="col-lg-2 col-lg-offset-1 control-label">Jabatan Penyetuju</label>
                        <div class="col-lg-6">
                            <input type="text" name="jabatan_penyetuju" id="jabatan_penyetuju" class="form-control" required autofocus placeholder="Masukkan Jabatan Penyetuju"></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div> -->
                    <!-- <div class="form-group row">
                        <label for="tanggal_disetujui" class="col-lg-2 col-lg-offset-1 control-label">Tanggal Disetujui</label>
                        <div class="col-lg-6">
                            <input type="date" name="tanggal_disetujui" id="tanggal_disetujui" class="form-control" required autofocus></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div> -->
                <div class="modal-footer">
                    <button class="btn btn-sm btn-flat btn-primary"><i class="fa fa-save"></i> Simpan</button>
                    <button type="button" class="btn btn-sm btn-flat btn-warning" data-dismiss="modal"><i class="fa fa-arrow-circle-left"></i> Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>

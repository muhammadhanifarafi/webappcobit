<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form">
    <div class="modal-dialog modal-lg" role="document">
        <form action="{{ route('quality_assurance_testing.store') }}" method="post" class="form-horizontal" enctype="multipart/form-data">
            @csrf
            @method('post')

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"></h4>
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
                    <div class="form-group row">
                        <label for="nama_aplikasi" class="col-lg-2 col-lg-offset-1 control-label">Nama Aplikasi</label>
                        <div class="col-lg-6">
                            <input type="text" name="nama_aplikasi" id="nama_aplikasi" class="form-control" required autofocus placeholder="Masukkan Nama Aplikasi">
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="manual_book" class="col-lg-2 col-lg-offset-1 control-label">Manual Book</label>
                        <div class="col-lg-6">
                            <select name="manual_book" id="manual_book" class="form-control" required>
                                <option value="" disabled selected>Pilih Salah Satu</option>
                                <option value="Ada">Ada</option>
                                <option value="Tidak">Tidak</option>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="lampiran" class="col-lg-2 col-lg-offset-1 control-label">Lampiran Manual Book</label>
                        <div class="col-lg-6">
                            <input type="file" name="lampiran" id="lampiran" class="form-control">
                            <span class="help-block with-errors"></span>
                            <small class="form-text text-muted">Upload Maximal File 4 MB Tipe File PDF *</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="lokasi_pengujian" class="col-lg-2 col-lg-offset-1 control-label">Lokasi Pengujian</label>
                        <div class="col-lg-6">
                            <input type="text" name="lokasi_pengujian" id="lokasi_pengujian" class="form-control" required autofocus placeholder="Masukkan Lokasi Pengujian"></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="unit_pemilik" class="col-lg-2 col-lg-offset-1 control-label">Unit Pemilik Sistem</label>
                        <div class="col-lg-6">
                            <input type="text" name="unit_pemilik" id="unit_pemilik" class="form-control" required autofocus placeholder="Masukkan Unit Pemilik Sistem"></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="tgl_pengujian" class="col-lg-2 col-lg-offset-1 control-label">Tanggal Pengujian</label>
                        <div class="col-lg-6">
                            <input type="date" name="tgl_pengujian" id="tgl_pengujian" class="form-control">
                            <span class="help-block with-errors"></span>
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
                    </div> -->
                    <div class="form-group row">
                        <label for="nik_penyetuju" class="col-lg-2 col-lg-offset-1 control-label">Nama Penyetuju AVP DTI</label>
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
                            <input type="date" name="tanggal_disetujui" id="tanggal_disetujui" class="form-control" required autofocus>
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
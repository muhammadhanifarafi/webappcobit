@extends('layouts.master')

@section('title')
    Form Permintaan Pengembangan
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Form Permintaan Pengembangan</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
        <form action="{{ route('permintaan_pengembangan.store') }}" method="post" class="form-horizontal" enctype="multipart/form-data">
            @csrf
            @method('post')
            <div class="modal-content">
                <!-- <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"></h4>
                </div> -->
                <div class="modal-body">
                    <!-- <div class="form-group row">
                        <label for="nomor_dokumen" class="col-lg-2 col-lg-offset-1 control-label">Nomor Dokumen</label>
                        <div class="col-lg-6">
                            <input type="text" name="nomor_dokumen" id="nomor_dokumen" class="form-control" required autofocus>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div> -->
                    <div class="form-group row">
                        <label for="judul" class="col-lg-2 col-lg-offset-1 control-label">Judul<span class="required-field" style="color: red;">*</span></label>
                        <div class="col-lg-6">
                        <input type="text" name="judul" id="judul" class="form-control" required autofocus placeholder="Masukkan Nama Judul"></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="latar_belakang" class="col-lg-2 col-lg-offset-1 control-label">Latar Belakang</label>
                        <div class="col-lg-9">
                            <textarea type="textarea" id="editor1" name="latar_belakang" id="latar_belakang" class="form-control" required autofocus placeholder="Latar Belakang atau Deskripsi Proyek"></textarea>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="tujuan" class="col-lg-2 col-lg-offset-1 control-label">Tujuan</label>
                        <div class="col-lg-9">
                            <textarea type="textarea" id="editor2" name="tujuan" id="tujuan" class="form-control" required autofocus placeholder="Tujuan Proyek"></textarea>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <!-- <div class="form-group row">
                        <label for="target_implementasi_sistem" class="col-lg-2 col-lg-offset-1 control-label">Target Implementasi</label>
                        <div class="col-lg-9">
                            <textarea id="editor3" name="target_implementasi_sistem" id="target_implementasi_sistem" class="form-control" rows="4" cols="50" required></textarea>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div> -->
                    <div class="form-group row">
                        <label for="target_implementasi_sistem" class="col-lg-2 col-lg-offset-1 control-label">Target Implementasi<span class="required-field" style="color: red;">*</span></label>
                        <div class="col-lg-6">
                            <div class="datepicker date input-group">
                                <input type="date" name="target_implementasi_sistem" placeholder="Choose Date" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="fungsi_sistem_informasi" class="col-lg-2 col-lg-offset-1 control-label">Fungsi-fungsi Sistem Informasi</label>
                        <div class="col-lg-9">
                            <textarea id="editor4" type="text" name="fungsi_sistem_informasi" id="fungsi_sistem_informasi" class="form-control" required autofocus placeholder="Fungsi Sistem Informasi"></textarea>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="jenis_aplikasi" class="col-lg-2 col-lg-offset-1 control-label">Jenis Aplikasi<span class="required-field" style="color: red;">*</span></label>
                        <div class="col-lg-6">
                            <select name="jenis_aplikasi[]" id="jenis_aplikasi" style="width: 100%;" class="form-control select2" multiple required>
                                <option value="" disabled>Pilih Jenis Aplikasi</option>
                                <option value="mobile">Mobile</option>
                                <option value="dekstop">Dekstop</option>
                                <option value="web">Web</option>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="pengguna" class="col-lg-2 col-lg-offset-1 control-label">Pengguna<span class="required-field" style="color: red;">*</span></label>
                        <div class="col-lg-6">
                            <select name="pengguna[]" id="pengguna" class="form-control select2" style="width: 100%;" multiple required>
                                <option value="" disabled>Pilih Pengguna</option>
                                <option value="internal">Internal</option>
                                <option value="eksternal">Eksternal</option>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="uraian_permintaan_tambahan" class="col-lg-2 col-lg-offset-1 control-label">Uraian Permintaan Tambahan/Khusus</label>
                        <div class="col-lg-9">
                            <textarea id="editor5" type="textareas" name="uraian_permintaan_tambahan" id="uraian_permintaan_tambahan" class="form-control" required autofocus placeholder="Permintaan Tambahan"></textarea>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="lampiran" class="col-lg-2 col-lg-offset-1 control-label">Lampiran Dokumen MoM</label>
                        <div class="col-lg-6">
                            <input type="file" name="lampiran" id="lampiran" class="form-control" onchange="validateFile()">
                            <span class="help-block with-errors" id="fileError"></span>
                            <small class="form-text text-muted">Upload Maximal File 4 MB Tipe File PDF *</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="lampiran_2" class="col-lg-2 col-lg-offset-1 control-label">Lampiran Dokumen Studi Kelayakan</label>
                        <div class="col-lg-6">
                            <input type="file" name="lampiran_2" id="lampiran_2" class="form-control" onchange="validateFile()">
                            <span class="help-block with-errors" id="fileError"></span>
                            <small class="form-text text-muted">Upload Maximal File 4 MB Tipe File PDF *</small>
                        </div>
                    </div>
                    <!-- <div class="form-group row">
                        <label for="pic" class="col-lg-2 col-lg-offset-1 control-label">Nama PIC<span class="required-field" style="color: red;">*</span></label>
                        <div class="col-lg-6">
                            <input type="text" name="pic" id="pic" class="form-control" required autofocus placeholder="Masukkan Nama PIC"></input>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div> -->
                    <div class="form-group row">
                        <label for="jenis_kepala_bagian" class="col-lg-2 col-lg-offset-1 control-label">Kepala Bagian IT<span class="required-field" style="color: red;">*</span></label>
                        <div class="col-lg-6">
                            <select name="jenis_kepala_bagian" id="jenis_kepala_bagian" class="form-control select2" required>
                                    <option value="IT Bisnis">IT Bisnis</option>
                                    <option value="IT Korporat">IT Korporat</option>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="nik_pemohon" class="col-lg-2 col-lg-offset-1 control-label">Nama Pemohon<span class="required-field" style="color: red;">*</span></label>
                        <div class="col-lg-6">
                            <select name="nik_pemohon" id="nik_pemohon" class="form-control select2" required>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <input type="hidden" name="jabatan_pemohon" id="jabatan_pemohon" class="form-control"></input>
                    <input type="hidden" name="nama_pemohon" id="nama_pemohon" class="form-control"></input>
                    <!-- <div class="form-group row">
                        <label for="tanggal_disiapkan" class="col-lg-2 col-lg-offset-1 control-label">Tanggal Disiapkan</label>
                        <div class="col-lg-6">
                            <div class="datepicker date input-group">
                                <input type="text" name="tanggal_disiapkan" placeholder="Choose Date" class="form-control" id="fecha1">
                            </div>
                        </div>
                    </div> -->
                    <!-- <div class="form-group row">
                        <label for="nik_penyetuju" class="col-lg-2 col-lg-offset-1 control-label">Nama Penyetuju<span class="required-field" style="color: red;">*</span></label>
                        <div class="col-lg-6">
                            <select name="nik_penyetuju" id="nik_penyetuju" class="form-control select2" required>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <input type="hidden" name="jabatan_penyetuju" id="jabatan_penyetuju" class="form-control"></input>
                    <input type="hidden" name="nama_penyetuju" id="nama_penyetuju" class="form-control"></input>
                    <div class="form-group row">
                        <label for="tanggal_disetujui" class="col-lg-2 col-lg-offset-1 control-label">Tanggal Disetujui</label>
                        <div class="col-lg-6">
                            <div class="datepicker date input-group">
                                <input type="text" name="tanggal_disetujui" placeholder="Choose Date" class="form-control" id="fecha2">
                            </div>
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
</div>
@endsection
@push('scripts')
@endpush
@extends('layouts.master')

@section('title')
    Tambah Detail Quality Assurance Testing
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar QAT</li>
@endsection

@section('content')
<div class="container">
  <div class="modal-content" style="width: 50%;">
    <div class="modal-body">

    <form action="{{ route('detail-quality-assurance-testing.store') }}" method="POST">
        @csrf
        <input type="hidden" name="id_quality_assurance_testing" value="{{ $qualityAssuranceTesting->id_quality_assurance_testing }}">
        <div class="form-group">
            <label for="nama_aplikasi">Nama Aplikasi</label>
            <input type="text" class="form-control" value="{{ $qualityAssuranceTesting->nama_aplikasi }}" readonly>
        </div>
        <div class="form-group">
            <label for="jenis">Bagian</label>
            <select name="jenis" class="form-control" required>
                <option value="" disabled selected>Pilih Bagian</option>
                <option value="1">A. Pengujian Kinerja Sistem Informasi</option>
                <option value="2">B. Pengujian Keamanan Sistem Informasi</option>
                <option value="3">C. Pengujian Keandalan Sistem Informasi</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="fitur_fungsi">Fitur Fungsi</label>
            <input type="text" name="fitur_fungsi" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="steps">Steps</label>
            <textarea name="steps" class="form-control" required></textarea>
        </div>

        <div class="form-group">
            <label for="ekspetasi">Ekspetasi</label>
            <textarea name="ekspetasi" class="form-control" required></textarea>
        </div>

        <div class="form-group">
            <label for="berhasil_gagal">Berhasil / Gagal</label>
            <select name="berhasil_gagal" class="form-control" required>
                <option value="" disabled selected>Pilih Status</option>
                <option value="Berhasil">Berhasil</option>
                <option value="Gagal">Gagal</option>
            </select>
        </div>

        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea name="notes" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
        <a href="{{ route('detail-quality-assurance-testing.index', ['id' => $qualityAssuranceTesting->id_quality_assurance_testing]) }}" class="btn btn-warning btn-sm">Kembali</a>
    </form>
    </div>
  </div>
</div>
@endsection
@push('scripts')
@endpush
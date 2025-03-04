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

    <form action="{{ route('detail-ttd-qat.store') }}" method="POST">
        @csrf
        <input type="hidden" name="id_quality_assurance_testing" value="{{ $qualityAssuranceTesting->id_quality_assurance_testing }}">
        <div class="form-group">
            <label for="nama_aplikasi">Nama Aplikasi</label>
            <input type="text" class="form-control" value="{{ $qualityAssuranceTesting->nama_aplikasi }}" readonly>
        </div>
        
        <div class="form-group">
            <label for="nik_penguji">NIK Penguji</label>
            <input type="text" class="form-control" id="nik_penguji" name="nik_penguji" required>
        </div>

        <div class="form-group">
            <label for="nama_penguji">Nama Penguji</label>
            <input type="text" class="form-control" id="nama_penguji" name="nama_penguji" required>
        </div>

        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
        <a href="{{ route('detail-ttd-qat.index', ['id' => $qualityAssuranceTesting->id_quality_assurance_testing]) }}" class="btn btn-warning btn-sm">Kembali</a>
    </form>
    </div>
  </div>
</div>
@endsection
@push('scripts')
@endpush
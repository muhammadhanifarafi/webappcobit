@extends('layouts.master')

@section('title')
    Tambah Detail User Acceptance Testing
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar UAT</li>
@endsection

@section('content')
<div class="container">
  <div class="modal-content" style="width: 50%;">
    <div class="modal-body">

    <form action="{{ route('detail-user-acceptance-testing.store') }}" method="POST">
        @csrf
        <input type="hidden" name="id_user_acceptance_testing" value="{{ $userAcceptanceTesting->id_user_acceptance_testing }}">
        <div class="form-group">
            <label for="nama_aplikasi">Nama Aplikasi</label>
            <input type="text" class="form-control" value="{{ $userAcceptanceTesting->nama_aplikasi }}" readonly>
        </div>
        <div class="form-group">
            <label for="jenis">Bagian</label>
            <select name="jenis" class="form-control" required>
                <option value="" disabled selected>Pilih Bagian</option>
                <option value="1">A. MANAJEMEN USER</option>
                <option value="2">B. KEAMANAN APLIKASI</option>
                <option value="3">C. FORM PERUSAHAAN </option>
            </select>
        </div>
        <div class="form-group">
            <label for="item">Item</label>
            <input type="text" name="item" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="pass_fail">Pass / Fail</label>
            <select name="pass_fail" class="form-control" required>
                <option value="" disabled selected>Pilih Status</option>
                <option value="Pass">Pass</option>
                <option value="Fail">Fail</option>
            </select>
        </div>

        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea name="notes" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
        <a href="{{ route('detail-user-acceptance-testing.index', ['id' => $userAcceptanceTesting->id_user_acceptance_testing]) }}" class="btn btn-warning btn-sm">Kembali</a>
    </form>
    </div>
  </div>
</div>
@endsection
@push('scripts')
@endpush
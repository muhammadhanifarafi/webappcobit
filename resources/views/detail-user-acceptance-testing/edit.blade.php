@extends('layouts.master')

@section('title')
    Edit Detail User Acceptance Testing
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar UAT</li>
@endsection

@section('content')
<div class="container">
  <div class="modal-content" style="width: 50%;">

    <div class="modal-body">
    <!-- Form untuk mengedit data -->
    <form action="{{ route('detail-user-acceptance-testing.update', $detail->id_detail_user_acceptance_testing) }}" method="POST">
        @csrf
        @method('PUT') <!-- Untuk memberi tahu bahwa ini adalah form untuk update -->

        <input type="hidden" name="id_user_acceptance_testing" value="{{ $detail->id_user_acceptance_testing }}">

        <div class="form-group">
            <label for="nama_aplikasi">Nama Aplikasi</label>
            <input type="text" class="form-control" value="{{ $detail->nama_aplikasi }}" readonly>
        </div>

        <div class="form-group">
            <label for="jenis">Bagian</label>
            <select name="jenis" class="form-control" required>
                <option value="" disabled selected>Pilih Bagian</option>
                <option value="1" {{ $detail->jenis == 1 ? 'selected' : '' }}>A. MANAJEMEN USER</option>
                <option value="2" {{ $detail->jenis == 2 ? 'selected' : '' }}>B. KEAMANAN APLIKASI</option>
                <option value="3" {{ $detail->jenis == 3 ? 'selected' : '' }}>C. FORM PERUSAHAAN</option>
            </select>
        </div>

        <div class="form-group">
            <label for="item">Item</label>
            <input type="text" name="item" class="form-control" value="{{ $detail->item }}" required>
        </div>

        <div class="form-group">
            <label for="pass_fail">Pass / Fail</label>
            <select name="pass_fail" class="form-control" required>
                <option value="" disabled selected>Pilih Status</option>
                <option value="Pass" {{ $detail->pass_fail == 'Pass' ? 'selected' : '' }}>Pass</option>
                <option value="Fail" {{ $detail->pass_fail == 'Fail' ? 'selected' : '' }}>Fail</option>
            </select>
        </div>

        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea name="notes" class="form-control" required>{{ $detail->notes }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-sm">Update</button>
        <a href="{{ route('detail-user-acceptance-testing.index', ['id' => $detail->id_user_acceptance_testing]) }}" class="btn btn-warning btn-sm">Kembali</a>
    </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
@endpush
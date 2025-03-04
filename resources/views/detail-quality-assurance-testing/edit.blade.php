@extends('layouts.master')

@section('title')
    Edit Detail Quality Assurance Testing
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Edit Detail Quality Assurance Testing</li>
@endsection

@section('content')
<div class="container">
  <div class="modal-content" style="width: 50%;">
    <div class="modal-body">
    <form action="{{ route('detail-quality-assurance-testing.update', $detail->id_detail_quality_assurance_testing) }}" method="POST">
        @csrf
        @method('PUT') <!-- Ini untuk menandakan bahwa ini adalah request PUT -->

        <div class="form-group">
            <label for="jenis">Jenis</label>
            <select name="jenis" class="form-control" required>
                <option value="1" {{ $detail->jenis == '1' ? 'selected' : '' }}>A. Pengujian Kinerja Sistem Informasi</option>
                <option value="2" {{ $detail->jenis == '2' ? 'selected' : '' }}>B. Pengujian Keamanan Sistem Informasi</option>
                <option value="3" {{ $detail->jenis == '3' ? 'selected' : '' }}>C. Pengujian Keandalan Sistem Informasi</option>
            </select>
        </div>

        <div class="form-group">
            <label for="fitur_fungsi">Fitur Fungsi</label>
            <input type="text" name="fitur_fungsi" class="form-control" value="{{ $detail->fitur_fungsi }}" required>
        </div>

        <div class="form-group">
            <label for="steps">Steps</label>
            <textarea name="steps" class="form-control" required>{{ $detail->steps }}</textarea>
        </div>

        <div class="form-group">
            <label for="ekspetasi">Ekspetasi</label>
            <textarea name="ekspetasi" class="form-control" required>{{ $detail->ekspetasi }}</textarea>
        </div>

        <div class="form-group">
            <label for="berhasil_gagal">Berhasil / Gagal</label>
            <select name="berhasil_gagal" class="form-control" required>
                <option value="Berhasil" {{ $detail->berhasil_gagal == 'Berhasil' ? 'selected' : '' }}>Berhasil</option>
                <option value="Gagal" {{ $detail->berhasil_gagal == 'Gagal' ? 'selected' : '' }}>Gagal</option>
            </select>
        </div>

        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea name="notes" class="form-control">{{ $detail->notes }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('detail-quality-assurance-testing.index', ['id' => $detail->id_quality_assurance_testing]) }}" class="btn btn-warning">Kembali</a>
    </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
@endpush
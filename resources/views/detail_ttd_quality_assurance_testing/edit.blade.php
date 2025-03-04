<!-- resources/views/detail_quality_assurance_testing_with_ttd/edit.blade.php -->

@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Detail Quality Assurance Testing dengan TTD</h2>

    <form action="{{ route('detail-quality-assurance-testing-with-ttd.update', $detail->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="id_penguji_qat">ID Penguji QAT</label>
            <input type="text" class="form-control" id="id_penguji_qat" name="id_penguji_qat" value="{{ $detail->id_penguji_qat }}" required>
        </div>

        <div class="form-group">
            <label for="nik_penguji">NIK Penguji</label>
            <input type="text" class="form-control" id="nik_penguji" name="nik_penguji" value="{{ $detail->nik_penguji }}" required>
        </div>

        <div class="form-group">
            <label for="nama_penguji">Nama Penguji</label>
            <input type="text" class="form-control" id="nama_penguji" name="nama_penguji" value="{{ $detail->nama_penguji }}" required>
        </div>

        <div class="form-group">
            <label for="nama_ttd">Nama TTD</label>
            <input type="text" class="form-control" id="nama_ttd" name="nama_ttd" value="{{ $detail->nama_ttd }}">
        </div>

        <button type="submit" class="btn btn-primary">Perbarui</button>
    </form>
</div>
@endsection

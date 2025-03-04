<!-- resources/views/detail_quality_assurance_testing_with_ttd/index.blade.php -->

@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Daftar Detail Quality Assurance Testing dengan TTD</h2>
    <a href="{{ route('detail-quality-assurance-testing-with-ttd.create', ['id_quality_assurance_testing' => $id]) }}" class="btn btn-success">Tambah Data</a>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>ID Penguji QAT</th>
                <th>NIK Penguji</th>
                <th>Nama Penguji</th>
                <th>Nama TTD</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($details as $detail)
            <tr>
                <td>{{ $detail->id_penguji_qat }}</td>
                <td>{{ $detail->nik_penguji }}</td>
                <td>{{ $detail->nama_penguji }}</td>
                <td>{{ $detail->nama_ttd }}</td>
                <td>
                    <a href="{{ route('detail-quality-assurance-testing-with-ttd.edit', $detail->id) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('detail-quality-assurance-testing-with-ttd.destroy', $detail->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

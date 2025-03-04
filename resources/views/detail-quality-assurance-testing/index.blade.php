@extends('layouts.master')

@section('title')
    Daftar Item Pengujian Quality Assurance Testing
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar QAT</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <a href="{{ route('detail-quality-assurance-testing.create') }}/{{ $id }}" class="btn btn-primary btn-sm">Tambah Data</a>
                <a href="{{ route('quality_assurance_testing.index') }}" class="btn btn-warning btn-sm">Kembali</a>
            </div>
            <div class="box-body table-responsive">
                    @csrf
                    <table class="table table-stiped table-bordered" style="font-size: 12px;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Fitur Fungsi</th>
                                <th>Steps</th>
                                <th>Ekspetasi</th>
                                <th>Berhasil/Gagal</th>
                                <th>Jenis</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($details as $detail)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $detail->fitur_fungsi }}</td>
                                    <td>{{ $detail->steps }}</td>
                                    <td>{{ $detail->ekspetasi }}</td>
                                    <td>{{ $detail->berhasil_gagal }}</td>
                                    <td>{{ $detail->jenis }}</td>
                                    <td>
                                        <a href="{{ route('detail-quality-assurance-testing.edit', $detail->id_detail_quality_assurance_testing) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('detail-quality-assurance-testing.destroy', $detail->id_detail_quality_assurance_testing) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
@endpush

@extends('layouts.master')

@section('title')
    Daftar Penguji Quality Assurance Testing
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
                <a href="{{ route('detail-ttd-qat.create') }}/{{ $id }}" class="btn btn-primary btn-sm">Tambah Data</a>
                <a href="{{ route('quality_assurance_testing.index') }}" class="btn btn-warning btn-sm">Kembali</a>
            </div>
            <div class="box-body table-responsive">
                    @csrf
                    <table class="table table-stiped table-bordered" style="font-size: 12px;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIK Penguji</th>
                                <th>Nama Penguji</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($details as $detail)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $detail->nik_penguji }}</td>
                                    <td>{{ $detail->nama_penguji }}</td>
                                    <td>
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

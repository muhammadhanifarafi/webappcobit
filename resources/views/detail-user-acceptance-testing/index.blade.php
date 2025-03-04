@extends('layouts.master')

@section('title')
    Daftar Item Pengujian User Acceptance Testing
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar UAT</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <a href="{{ route('detail-user-acceptance-testing.create') }}/{{ $id }}" class="btn btn-primary btn-sm">Tambah Data</a>
                <a href="{{ route('quality_assurance_testing.index') }}" class="btn btn-warning btn-sm">Kembali</a>
            </div>
            <div class="box-body table-responsive">
                    @csrf
                    <table class="table table-stiped table-bordered" style="font-size: 12px;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Item</th>
                                <th>Pass / Fail</th>
                                <th>Notes</th>
                                <th>Jenis</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($details as $detail)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $detail->item }}</td>
                                    <td>{{ $detail->pass_fail }}</td>
                                    <td>{{ $detail->notes }}</td>
                                    <td>
                                        @switch($detail->jenis)
                                            @case(1)
                                                Manajemen User
                                                @break
                                            @case(2)
                                                Keamanan Aplikasi
                                                @break
                                            @case(3)
                                                Form Perusahaan
                                                @break
                                            @default
                                                Unknown
                                        @endswitch
                                    </td>
                                    <td>
                                        <a href="{{ route('detail-user-acceptance-testing.edit', $detail->id_detail_user_acceptance_testing) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('detail-user-acceptance-testing.destroy', $detail->id_detail_user_acceptance_testing) }}" method="POST" style="display:inline;">
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

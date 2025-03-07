@extends('layouts.master')

@section('title')
    Daftar Analisis dan Desain
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar Analisis dan Desain</li>
@endsection

@section('content')
<style>
td {
    word-wrap: break-word;
    max-width: 1000px;
}

/* CSS untuk Scroll hanya pada kolom tertentu (gambaran_arsitektur dan detil_arsitektur) */
td.gambaran_arsitektur,
td.detil_arsitektur {
    padding: 0; /* Menghilangkan padding default untuk memberi ruang */
    vertical-align: top; /* Pastikan teks dimulai dari atas */
}

/* Membungkus konten dalam div untuk kontrol scroll */
td.gambaran_arsitektur div,
td.detil_arsitektur div {
    max-height: 200px;  /* Batas tinggi kolom */
    overflow-y: auto;   /* Scroll vertikal jika konten melebihi */
    white-space: normal; /* Biarkan teks membungkus ke baris berikutnya */
    word-wrap: break-word; /* Membiarkan kata-kata panjang membungkus ke baris berikutnya */
    display: block;     /* Pastikan div mengambil seluruh lebar td */
    width: 100%;        /* Agar div menggunakan lebar penuh dalam td */
}

/* Opsional: Mengatur lebar kolom agar tidak terlalu lebar */
table.dataTable td.gambaran_arsitektur,
table.dataTable td.detil_arsitektur {
    width: 500px;  /* Atur lebar kolom sesuai kebutuhan */
}
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
        <div class="box-header with-border">
            <?php if (auth()->user()->level == 1 || auth()->user()->level == 5) { ?>
                <button onclick="addForm('{{ route('analisis_desain.store') }}')" class="btn btn-success btn-xs btn-flat"><i class="fa fa-plus-circle"></i> Tambah</button>
                <!-- <button onclick="deleteSelected('{{ route('analisis_desain.delete_selected') }}')" class="btn btn-danger btn-xs btn-flat"><i class="fa fa-trash"></i> Hapus</button> -->
            <?php } ?>
            <!-- <button onclick="cetakDokumen('{{ route('analisis_desain.cetakDokumen') }}')" class="btn btn-info btn-xs btn-flat"><i class="fa fa-download"></i> Cetak Dokumen</button> -->
        </div>
            <div class="box-body table-responsive">
                    @csrf
                    <table class="table table-stiped table-bordered" style="font-size: 12px;">
                            <thead>
                            <th width="5%">
                                <input type="checkbox" name="select_all" id="select_all">
                            </th>
                            <th width="5%">No</th>
                            <th>Nomor Dokumen</th>
                            <th>Nama Proyek</th>
                            <th>Manajer Proyek</th>
                            <!-- <th>Kebutuhan Fungsional</th> -->
                            <th>Gambaran Arsitektur Sistem</th>
                            <th>Detail Arsitektur Sistem</th>
                            <th>Lampiran Mockup Sistem (Link canva, Figma, atau lainnya)</th>
                            <th style="width: 200px; padding: 4px;">File Upload Dokumen Permintaan TTD Lengkap</th>
                            <th>Status</th>
                            <th>Lampiran</th>
                            <th width="15%"><i class="fa fa-cog"></i></th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('analisis_desain.upload')
@include('analisis_desain.viewform')
@include('analisis_desain.update_progress')
@includeIf('analisis_desain.form')
@endsection

@push('scripts')
<script>
    let table;

    $(function () {
        table = $('.table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('analisis_desain.data') }}',
            },
            columns: [
                {data: 'select_all', searchable: false, sortable: false},
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'nomor_dokumen'},
                {data: 'nama_proyek'},
                {data: 'manajer_proyek'},
                {
                    data: 'gambaran_arsitektur',
                    className: 'gambaran_arsitektur',
                    render: function(data, type, row) {
                        return '<div>' + data + '</div>'; // Bungkus konten dalam div untuk scroll
                    }
                },
                {
                    data: 'detil_arsitektur',
                    className: 'detil_arsitektur',
                    render: function(data, type, row) {
                        return '<div>' + data + '</div>'; // Bungkus konten dalam div untuk scroll
                    }
                },
                {data: 'lampiran_mockup'},
                {data: 'file_pdf'},
                {data: 'approval_status'},
                {
                    data: function(row) {
                        let buttons = '';

                        if (row.lampiran_1) {
                            buttons += `<a href="/storage/assets/lampiran/analisisdesain/${row.lampiran_1}" target="_blank" class="btn btn-info btn-xs btn-flat"><i class="fa fa-download"></i> Cetak Lampiran 1</a>`;
                        }

                        if (row.lampiran_2) {
                            buttons += `<a href="/storage/assets/lampiran/analisisdesain/${row.lampiran_2}" target="_blank" class="btn btn-info btn-xs btn-flat"><i class="fa fa-download"></i> Cetak Lampiran 2</a>`;
                        }

                        if (row.lampiran_3) {
                            buttons += `<a href="/storage/assets/lampiran/analisisdesain/${row.lampiran_3}" target="_blank" class="btn btn-info btn-xs btn-flat"><i class="fa fa-download"></i> Cetak Lampiran 3</a>`;
                        }

                        if (row.lampiran_4) {
                            buttons += `<a href="/storage/assets/lampiran/analisisdesain/${row.lampiran_4}" target="_blank" class="btn btn-info btn-xs btn-flat"><i class="fa fa-download"></i> Cetak Lampiran 4</a>`;
                        }

                        return buttons || 'Tidak Ada Lampiran';
                    }
                },
                {data: 'aksi', searchable: false, sortable: false},
            ],
        });

        $('#modal-form').validator().on('submit', function (e) {
            if (!e.preventDefault()) {
                let formData = new FormData($('#modal-form form')[0]);
                formData.append('lampiran_1', $('input[name="lampiran_1"]')[0].files[0]);
                formData.append('lampiran_2', $('input[name="lampiran_2"]')[0].files[0]);
                formData.append('lampiran_3', $('input[name="lampiran_3"]')[0].files[0]);
                formData.append('lampiran_4', $('input[name="lampiran_4"]')[0].files[0]);

                $.ajax({
                    url: $('#modal-form form').attr('action'),
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#modal-form').modal('hide');
                        // table.ajax.reload();
                        window.location.href = 'http://127.0.0.1:8000/analisis_desain';
                    },
                    error: function(errors) {
                        alert('Tidak dapat menyimpan data');
                        return;
                    }
                });
            }
        });

        $('[name=select_all]').on('click', function () {
            $(':checkbox').prop('checked', this.checked);
        });
    });

    function addForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Tambah Analisis dan Desain');
        $('.condition-set').show();
        $('#modal-form [name=id_permintaan_pengembangan]').prop('disabled', false);

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
        $('#modal-form [name=nama_proyek]').focus();

        var getnamapemohon = 'https://cobit-demo.ptsi.co.id/dashboard/get-nama-pemohon';
        $.get(getnamapemohon)
        .done((response) => {
            let pemohonSelect = $('#modal-form [name=nik_pemohon]');
            pemohonSelect.empty();
            pemohonSelect.append('<option value="" disabled selected>Pilih Nama Pemohon</option>'); 
            if (Array.isArray(response.pemohon)) {
                response.pemohon.forEach(function(item, index) {
                    pemohonSelect.append(`<option value="${item.employee_id}">${item.full_name} - ${item.position_name}</option>`);
                });
            } else {
                pemohonSelect.append('<option value="" disabled>Tidak ada data pemohon</option>');
            }
        })
        .fail((errors) => {
            alert('Tidak dapat mengambil data pemohon');
            return;
        });
        var getnamapenyetuju = 'https://cobit-demo.ptsi.co.id/dashboard/get-nama-penyetuju';
        $.get(getnamapenyetuju)
        .done((response) => {
            let penyetujuSelect = $('#modal-form #nik_penyetuju');
            penyetujuSelect.empty(); 
            penyetujuSelect.append('<option value="" disabled selected>Pilih Nama Penyetuju</option>');
            if (Array.isArray(response.penyetuju)) {
                response.penyetuju.forEach(function(item, index) {
                    penyetujuSelect.append(`<option value="${item.employee_id}">${item.full_name} - ${item.position_name}</option>`);
                });
            } else {
                penyetujuSelect.append('<option value="" disabled>Tidak ada data penyetuju</option>');
            }
        })
        .fail((errors) => {
            alert('Tidak dapat mengambil data penyetuju');
            return;
        });
    }
    // On Change Atribut
    $("#modal-form [name=nik_pemohon]").on('change', function() {
            var nik = $(this).val();
            var url_link_get_identity_by_nik = 'https://cobit-demo.ptsi.co.id/dashboard/get-identity-by-nik/' + nik;
            
            $.get(url_link_get_identity_by_nik)
            .done((response) => {
                $('#modal-form [name=nama_pemohon]').val(response.pemohonpenyetuju.full_name);
                $('#modal-form [name=jabatan_pemohon]').val(response.pemohonpenyetuju.position_name);
            })
            .fail((errors) => {
                alert('Tidak dapat mengambil data pemohon dan penyetuju');
                return;
            });
    });

    $("#modal-form [name=nik_penyetuju]").on('change', function() {
            var nik = $(this).val();
            var url_link_get_identity_by_nik = 'https://cobit-demo.ptsi.co.id/dashboard/get-identity-by-nik/' + nik;

            $.get(url_link_get_identity_by_nik)
            .done((response) => {
                $('#modal-form [name=nama_penyetuju]').val(response.pemohonpenyetuju.full_name);
                $('#modal-form [name=jabatan_penyetuju]').val(response.pemohonpenyetuju.position_name);
            })
            .fail((errors) => {
                alert('Tidak dapat mengambil data pemohon dan penyetuju');
                return;
            });
    });

    function editForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Edit Analisis dan Desain');
        $('.condition-set').hide();

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('put');
        $('#modal-form [name=nama_proyek]').focus();

        $.get(url)
            .done((response) => {

                $('#modal-form [name=id_permintaan_pengembangan]').val(response.id_permintaan_pengembangan).prop('disabled', true);
                $('#modal-form [name=nama_proyek]').val(response.nama_proyek);
                $('#modal-form [id=nama_proyek_id]').val(response.nama_proyek);
                $('#modal-form [name=nama_proyek]').val(response.nama_proyek);
                $('#modal-form [name=deskripsi_proyek]').val(response.deskripsi_proyek);
                // CKEDITOR.instances['editor1'].setData(response.deskripsi_proyek);
                $('#modal-form [name=manajer_proyek]').val(response.manajer_proyek);
                $('#modal-form [name=kebutuhan_fungsi]').val(response.kebutuhan_fungsi);
                // CKEDITOR.instances['editor2'].setData(response.kebutuhan_fungsi);
                $('#modal-form [name=gambaran_arsitektur]').val(response.gambaran_arsitektur);
                CKEDITOR.instances['editor3'].setData(response.gambaran_arsitektur);
                $('#modal-form [name=detil_arsitektur]').val(response.detil_arsitektur);
                CKEDITOR.instances['editor4'].setData(response.detil_arsitektur);
                $('#modal-form [name=lampiran_mockup]').val(response.lampiran_mockup);
                CKEDITOR.instances['editor5'].setData(response.lampiran_mockup);
                $('#modal-form [name=nama_pemohon]').val(response.nama_pemohon);
                $('#modal-form [name=jabatan_pemohon]').val(response.jabatan_pemohon);
                $('#modal-form [name=nik_pemohon]').val(response.nik_pemohon);
                $('#modal-form [name=tanggal_disiapkan]').val(response.tanggal_disiapkan);
                $('#modal-form [name=nama_penyetuju]').val(response.nama_penyetuju);
                $('#modal-form [name=jabatan_penyetuju]').val(response.jabatan_penyetuju);
                $('#modal-form [name=nik_penyetuju]').val(response.nik_penyetuju);
                $('#modal-form [name=tanggal_disetujui]').val(response.tanggal_disetujui);
                $('#modal-form [name=status]').val(response.status);

                
                if (response.is_approve == '1') {
                    $('#modal-form .button-save').prop('disabled', true);
                    $('#modal-form [name=id_permintaan_pengembangan]').prop('disabled', true);
                    $('#modal-form [name=nama_proyek]').prop('disabled', true);
                    $('#modal-form [name=deskripsi_proyek]').prop('disabled', true);
                    $('#modal-form [name=manajer_proyek]').prop('disabled', true);
                    $('#modal-form [name=kebutuhan_fungsi]').prop('disabled', true);
                    $('#modal-form [name=gambaran_arsitektur]').prop('disabled', true);
                    $('#modal-form [name=detil_arsitektur]').prop('disabled', true);
                    $('#modal-form [name=lampiran_mockup]').prop('disabled', true);
                    $('#modal-form [name=nik_pemohon]').prop('disabled', true);
                    $('#modal-form [name=nik_penyetuju]').prop('disabled', true);
                    CKEDITOR.instances['editor3'].setReadOnly(true);
                    CKEDITOR.instances['editor4'].setReadOnly(true);
                    CKEDITOR.instances['editor5'].setReadOnly(true);
                }else{
                    $('#modal-form .button-save').prop('disabled', false);
                }

                var response_nik_pemohon = response.nik_pemohon;
                var response_nik_penyetuju = response.nik_penyetuju;

                var getnamapemohonpenyetuju = 'https://cobit-demo.ptsi.co.id/dashboard/get-nama-pemohon-penyetuju';
                $.get(getnamapemohonpenyetuju)
                .done((response) => {
                    let pemohonSelect = $('#modal-form [name=nik_pemohon]');
                    pemohonSelect.empty();
                    pemohonSelect.append('<option value="" disabled>Pilih Nama Pemohon</option>'); 
                    if (Array.isArray(response.pemohonpenyetuju)) {
                        response.pemohonpenyetuju.forEach(function(item) {
                            pemohonSelect.append(`<option value="${item.employee_id}" ${item.employee_id == response_nik_pemohon ? 'selected' : ''}>${item.full_name} - ${item.position_name}</option>`);
                        });
                    } else {
                        pemohonSelect.append('<option value="" disabled>Tidak ada data pemohon</option>');
                    }

                    let penyetujuSelect = $('#modal-form [name=nik_penyetuju]');
                    penyetujuSelect.empty(); 
                    penyetujuSelect.append('<option value="" disabled>Pilih Nama Penyetuju</option>');
                    if (Array.isArray(response.pemohonpenyetuju)) {
                        response.pemohonpenyetuju.forEach(function(item) {
                            penyetujuSelect.append(`<option value="${item.employee_id}" ${item.employee_id == response_nik_penyetuju ? 'selected' : ''}>${item.full_name} - ${item.position_name}</option>`);
                        });
                    } else {
                        penyetujuSelect.append('<option value="" disabled>Tidak ada data penyetuju</option>');
                    }

                })
                .fail((errors) => {
                    alert('Tidak dapat mengambil data pemohon dan penyetuju');
                });
            })
            .fail((errors) => {
                console.error(errors);
                alert('Tidak dapat menampilkan data');
            });
    }

    function deleteData(url) {
        if (confirm('Yakin ingin menghapus data terpilih?')) {
            $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete'
                })
                .done((response) => {
                    table.ajax.reload();
                })
                .fail((errors) => {
                    console.error(errors);
                    alert('Tidak dapat menghapus data');
                });
        }
    }

    function deleteSelected(url) {
        var ids = [];
        $('[name="id_analisis_desain[]"]:checked').each(function () {
            ids.push($(this).val());
        });

        if (ids.length > 0) {
            if (confirm('Yakin ingin menghapus data terpilih?')) {
                $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete',
                    'id_analisis_desain': ids
                })
                .done((response) => {
                    table.ajax.reload();
                })
                .fail((errors) => {
                    alert('Tidak dapat menghapus data');
                    return;
                });
            }
        } else {
            alert('Pilih data yang akan dihapus');
        }
    }

    function cetakDokumen(url, idAnalisisDesain) {
        // if ($('input:checked').length < 1) {
        //     alert('Pilih data yang akan dicetak');
        //     return;
        // } else {
            var form = $('<form>', {
                'method': 'POST',
                'action': url,
                'target': '_blank'
            });

            form.append($('<input>', {
                'type': 'hidden',
                'name': '_token',
                'value': '{{ csrf_token() }}'
            }));

            // $('input:checked').each(function() {
            //     form.append($('<input>', {
            //         'type': 'hidden',
            //         'name': 'id_analisis_desain[]',
            //         'value': $(this).val()
            //     }));
            // });
            
            // Tambahkan ID permintaan pengembangan ke dalam form
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'id_analisis_desain',
                'value': idAnalisisDesain
            }));

            $('body').append(form);
            form.submit();
        // }
    }

    function viewForm(url) {
        $('#modal-view').modal('show');

        $.get(url)
            .done((response) => {
                $('#modal-view [name=nama_pemohon]').val(response.nama_pemohon);
                $('#modal-view [name=nik_pemohon]').val(response.nik_pemohon);
                $('#modal-view [name=jabatan_pemohon]').val(response.jabatan_pemohon);
                $('#modal-view [name=tanggal_disiapkan]').val(response.tanggal_disiapkan);
                $('#modal-view [name=nama_penyetuju]').val(response.nama_penyetuju);
                $('#modal-view [name=nik_penyetuju]').val(response.nik_penyetuju);
                $('#modal-view [name=jabatan_penyetuju]').val(response.jabatan_penyetuju);
                $('#modal-view [name=tanggal_disetujui]').val(response.tanggal_disetujui);
            })
            .fail((errors) => {
                alert('Tidak dapat menampilkan data');
                return;
            });
    }

    function UploadPDF(url) {
        $('#modal-upload').modal('show');
        $('#modal-upload form').attr('action', url);

        $('#modal-upload form').off('submit').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData($(this)[0]);

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#modal-upload').modal('hide');
                    table.ajax.reload();
                    alert(response);
                },
                error: function(errors) {
                    alert('Tidak dapat mengupload PDF');
                }
            });
        });
    }

    function updateProgressForm(url) {
        // Reset form
        $('#modalUpdateProgress form')[0].reset();
        $('#modalUpdateProgress').modal('show');
        $('#modalUpdateProgress form').attr('action', url);

        $.get(url, function(response) {
            if (response) {
                // Mengisi nilai nomor_dokumen
                $('#modalUpdateProgress [name=nomor_dokumen]').val(response.nomor_dokumen);

                // Mengisi nilai progress dan mengatur selected pada dropdown
                $('#modalUpdateProgress [name=progress]').val(response.progress).change(); // Memicu event change jika diperlukan
            }
        });
    }

    $('#formUpdateProgress form').on('submit', function(e) {
        e.preventDefault();
        var url = $(this).attr('action');
        $.ajax({
            url: url,
            method: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                $('#modalUpdateProgress').modal('hide');
                // alert('Progress berhasil diperbarui!');
                location.reload();
            },
            error: function(xhr) {
                alert('Gagal memperbarui progress! Silakan coba lagi.');
            }
        });
    });

    function approveProyek(url) {
        // SweetAlert konfirmasi sebelum melanjutkan proses
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Proyek ini akan disetujui!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, setujui!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika pengguna mengonfirmasi, lanjutkan dengan request AJAX
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}' // Gunakan CSRF token untuk keamanan request
                    },
                    success: function(response) {
                        // Jika berhasil, ubah tampilan tombol menjadi 'Approved'
                        let button = $(`button[onclick="approveProyek('${url}')"]`);
                        button.removeClass('btn-warning').addClass('btn-success').attr('disabled', true).html('<i class="fa fa-check"></i> Approved');
                        
                        // Menampilkan pesan sukses (opsional)
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Proyek berhasil disetujui.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(function() {
                            location.reload();  // Reload halaman setelah proses selesai
                        });
                    },
                    error: function(xhr) {
                        // Menampilkan pesan error jika terjadi kesalahan
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan, coba lagi nanti.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    }
</script>
@endpush


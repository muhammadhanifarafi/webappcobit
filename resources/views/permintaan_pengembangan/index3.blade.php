@extends('layouts.master')

@section('title')
    Daftar Project
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar Project</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <a href="{{ route('permintaan_pengembangan.cetakAllDokumenSummary') }}" class="btn btn-primary btn-flat" style="background-color: #007bff; border-radius: 10px; padding: 10px 20px; border: none; color: white; font-weight: bold;">
                    <i class="fa fa-download"></i>  Cetak Summary Report Project
                </a>
            </div>
            <div class="box-body table-responsive">
                    @csrf
                    <table class="table table-stiped table-bordered" style="font-size: 12px;">
                        <thead>
                        <tr>
                            <th style="width: 45px; padding: 4px;">
                                <input type="checkbox" name="select_all" id="select_all">
                            </th>
                            <th style="width: 15px; padding: 4px;">No</th>
                            <!-- <th style="width: 80px; padding: 4px;">Nomor Dokumen</th> -->
                            <th style="padding: 4px;">Nama Project</th>
                            <th style="padding: 4px;">Unit Pemohon</th>
                            <th style="width: 100px; padding: 4px;">Status Cobit</th>
                            <!-- <th style="width: 50px; padding: 4px;">User Created</th> -->
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Persetujuan -->
<div class="modal fade" id="approvalModal" tabindex="-1" role="dialog" aria-labelledby="approvalModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="approvalModalLabel">Persetujuan Proyek</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Form untuk status persetujuan dan alasan persetujuan -->
        <form id="approvalForm">
          <div class="form-group row">
            <label for="id_mst_persetujuan" class="col-lg-2 col-lg-offset-1 control-label">Status Persetujuan<span class="required-field" style="color: red;">*</span></label>
            <div class="col-lg-6">
              <select name="id_mst_persetujuan" id="id_mst_persetujuan" class="form-control" required>
                <option value="">Pilih Status Persetujuan</option>
                @foreach ($mst_persetujuan as $key => $item)
                  <option value="{{ $key }}">{{ $item }}</option>
                @endforeach
              </select>
              <span class="help-block with-errors"></span>
            </div>
          </div>
          <div class="form-group row">
            <label for="id_mst_persetujuanalasan" class="col-lg-2 col-lg-offset-1 control-label">Alasan Persetujuan<span class="required-field" style="color: red;">*</span></label>
            <div class="col-lg-6">
              <select name="id_mst_persetujuanalasan" id="id_mst_persetujuanalasan" class="form-control" required>
                <option value="">Pilih Alasan</option>
                @foreach ($mst_persetujuanalasan as $key => $item)
                  <option value="{{ $key }}">{{ $item }}</option>
                @endforeach
              </select>
              <span class="help-block with-errors"></span>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-primary" onclick="submitApprovalForm()">Setujui</button>
      </div>
    </div>
  </div>
</div>

@include('permintaan_pengembangan.upload')
@include('permintaan_pengembangan.viewform')
@include('permintaan_pengembangan.update_progress')
@includeIf('permintaan_pengembangan.form')
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
                url: '{{ route('permintaan_pengembangan.data3') }}',
            },
            columns: [
                {data: 'select_all', searchable: false, sortable: false},
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                // {data: 'nomor_dokumen'},
                {data: 'judul'},
                {data: 'unit_pemohon'},
                {data: 'approval_status'},
            ],
        });

        $('#modal-form').validator().on('submit', function (e) {
            if (!e.preventDefault()) {
                let formData = new FormData($('#modal-form form')[0]);
                formData.append('lampiran', $('input[name="lampiran"]')[0].files[0]);

                $.ajax({
                    url: $('#modal-form form').attr('action'),
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#modal-form').modal('hide');
                        // table.ajax.reload();
                        window.location.href = 'http://127.0.0.1:8000/permintaan_pengembangan/index2';
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
        $('#modal-form .modal-title').text('Tambah Permintaan Pengembangan');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
        $('#modal-form [name=nomor_dokumen]').focus();

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
                alert('Tidak dapat mengambil data pemohon');
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
                alert('Tidak dapat mengambil data penyetuju');
                return;
            });
    });

    function editForm(url) {

        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Edit Permintaan Pengembangan');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('put');
        $('#modal-form [name=nomor_dokumen]').focus();

        $.get(url)
            .done((response) => {
                $('#modal-form [name=nomor_dokumen]').val(response.nomor_dokumen);
                $('#modal-form [name=latar_belakang]').val(response.latar_belakang);
                $('#modal-form [name=judul]').val(response.judul);
                // CKEDITOR.instances['editor1'].setData(response.latar_belakang || '');
                $('#modal-form [name=tujuan]').val(response.tujuan);
                // CKEDITOR.instances['editor2'].setData(response.tujuan);
                $('#modal-form [name=target_implementasi_sistem]').val(response.target_implementasi_sistem);
                // CKEDITOR.instances['editor3'].setData(response.target_implementasi_sistem);
                $('#modal-form [name=fungsi_sistem_informasi]').val(response.fungsi_sistem_informasi);
                // CKEDITOR.instances['editor4'].setData(response.fungsi_sistem_informasi);
                // Pastikan jenis_aplikasi adalah array
                if (typeof response.jenis_aplikasi === 'string') {
                    // Jika jenis_aplikasi berupa string JSON, konversi menggunakan JSON.parse
                    response.jenis_aplikasi = JSON.parse(response.jenis_aplikasi);
                }
                $('#modal-form [name=jenis_aplikasi]').val(response.jenis_aplikasi).trigger('change.select2');
                // $('#modal-form [name="jenis_aplikasi[]"]').trigger('change');
                $('#modal-form [name=pengguna]').val(response.pengguna);
                $('#modal-form [name=uraian_permintaan_tambahan]').val(response.uraian_permintaan_tambahan);
                CKEDITOR.instances['editor5'].setData(response.uraian_permintaan_tambahan);
                $('#modal-form [name=nama_pemohon]').val(response.nama_pemohon);
                $('#modal-form [name=jabatan_pemohon]').val(response.jabatan_pemohon);
                $('#modal-form [name=pic]').val(response.pic);
                $('#modal-form [name=tanggal_disiapkan]').val(response.tanggal_disiapkan);
                $('#modal-form [name=nama_penyetuju]').val(response.nama_penyetuju);
                $('#modal-form [name=jabatan_penyetuju]').val(response.jabatan_penyetuju);
                $('#modal-form [name=nik_penyetuju]').val(response.nik_penyetuju);
                $('#modal-form [name=tanggal_disetujui]').val(response.tanggal_disetujui);
                
                var response_nik_pemohon = response.nik_pemohon;
                var response_nik_penyetuju = response.nik_penyetuju;

                var getnamapemohon = 'https://cobit-demo.ptsi.co.id/dashboard/get-nama-pemohon';
                $.get(getnamapemohon)
                .done((response) => {
                    let pemohonSelect = $('#modal-form [name=nik_pemohon]');
                    pemohonSelect.empty();
                    pemohonSelect.append('<option value="" disabled selected>Pilih Nama Pemohon</option>'); 
                    if (Array.isArray(response.pemohon)) {
                        response.pemohon.forEach(function(item, index) {
                            pemohonSelect.append(`<option value="${item.employee_id}" ${item.employee_id == response_nik_pemohon ? 'selected' : ''}>${item.full_name} - ${item.position_name}</option>`);
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
                            penyetujuSelect.append(`<option value="${item.employee_id}" ${item.employee_id == response_nik_penyetuju ? 'selected' : ''}>${item.full_name} - ${item.position_name}</option>`);
                        });
                    } else {
                        penyetujuSelect.append('<option value="" disabled>Tidak ada data penyetuju</option>');
                    }
                })
                .fail((errors) => {
                    alert('Tidak dapat mengambil data penyetuju');
                    return;
                });
            })
            .fail((errors) => {
                alert('Tidak dapat menampilkan data');
                return;
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
                    alert('Tidak dapat menghapus data');
                    return;
                });
        }
    }

    function deleteSelected(url) {
        var ids = [];
        $('[name="id_permintaan_pengembangan[]"]:checked').each(function () {
            ids.push($(this).val());
        });

        if (ids.length > 0) {
            if (confirm('Yakin ingin menghapus data terpilih?')) {
                $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete',
                    'id_permintaan_pengembangan': ids
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


    function cetakDokumen(url, idPermintaan) {
        var form = $('<form>', {
            'method': 'POST',
            'action': url,
            'target': '_blank'
        });

        // Tambahkan CSRF token untuk keamanan
        form.append($('<input>', {
            'type': 'hidden',
            'name': '_token',
            'value': '{{ csrf_token() }}'
        }));

        // Tambahkan ID permintaan pengembangan ke dalam form
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'id_permintaan_pengembangan',
            'value': idPermintaan
        }));

        $('body').append(form);
        form.submit();
    }
    
    function cetakDokumenPersetujuan(url, idPermintaan) {
        var form = $('<form>', {
            'method': 'POST',
            'action': url,
            'target': '_blank'
        });

        // Tambahkan CSRF token untuk keamanan
        form.append($('<input>', {
            'type': 'hidden',
            'name': '_token',
            'value': '{{ csrf_token() }}'
        }));

        // Tambahkan ID permintaan pengembangan ke dalam form
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'id_permintaan_pengembangan',
            'value': idPermintaan
        }));

        $('body').append(form);
        form.submit();
    }

    function cetakReportSummary(url) {
        window.open(url, '_blank');
    }

    function viewForm(url) {
        $('#modal-view').modal('show');

        $.get(url)
            .done((response) => {

                $('#modal-view [name=nomor_dokumen]').val(response.nomor_dokumen);
                $('#modal-view [name=latar_belakang]').val(
                    $('<div>').html(response.latar_belakang).text()
                );
                $('#modal-view [name=tujuan]').val(
                    $('<div>').html(response.tujuan).text()
                );
                $('#modal-view [name=target_implementasi_sistem]').val(
                    $('<div>').html(response.target_implementasi_sistem).text()
                );
                $('#modal-view [name=fungsi_sistem_informasi]').val(
                    $('<div>').html(response.fungsi_sistem_informasi).text()
                );
                $('#modal-view [name=jenis_aplikasi]').val(
                    typeof response.jenis_aplikasi === 'string' && response.jenis_aplikasi.trim().startsWith('[') && response.jenis_aplikasi.trim().endsWith(']')
                    ? JSON.parse(response.jenis_aplikasi).join(', ') // Parse and join array elements
                    : response.jenis_aplikasi // Otherwise, use the raw data as-is
                );
                $('#modal-view [name=pengguna]').val(
                    typeof response.pengguna === 'string' && response.pengguna.trim().startsWith('[') && response.pengguna.trim().endsWith(']')
                    ? JSON.parse(response.pengguna).join(', ') // Parse and join array elements
                    : response.pengguna // Otherwise, use the raw data as-is
                );
                $('#modal-view [name=pic]').val(response.pic);
                $('#modal-view [name=uraian_permintaan_tambahan]').val(
                    $('<div>').html(response.uraian_permintaan_tambahan).text()
                );
                $('#modal-view [name=nama_pemohon]').val(response.nama_pemohon);
                $('#modal-view [name=jabatan_pemohon]').val(response.jabatan_pemohon);
                $('#modal-view [name=tanggal_disiapkan]').val(formatTanggal(response.tanggal_disiapkan));
                $('#modal-view [name=nama_penyetuju]').val(response.nama_penyetuju);
                $('#modal-view [name=jabatan_penyetuju]').val(response.jabatan_penyetuju);
                $('#modal-view [name=tanggal_disetujui]').val(
                    (response.approve_at && response.approve_at !== '1970-01-01 00:00:00') 
                    ? formatTanggal(response.approve_at) 
                    : '' // Atau bisa diganti dengan tanggal default lainnya
                );

                if (response.lampiran) {
                    $('#modal-view #lampiran-link').attr('href', '/storage/assets/lampiran/' + response.lampiran);
                } else {
                    $('#modal-view #lampiran-link').attr('href', '#').text('Tidak ada lampiran');
                }
            })
            .fail((errors) => {
                alert('Tidak dapat menampilkan data');
                return;
            });
    }

    function formatTanggal(dateString) {
        const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        const date = new Date(dateString);
        const day = date.getDate();
        const month = months[date.getMonth()];
        const year = date.getFullYear();

        return `${day < 10 ? '0' + day : day}-${month}-${year}`;
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
        // Menampilkan modal ketika tombol diklik
        $('#approvalModal').modal('show');
        
        // Menyimpan URL untuk submit form nanti
        $('#approvalForm').data('url', url);
        
        // Menambahkan logika untuk mengubah tampilan tombol
        let button = $(`button[onclick="approveProyek('${url}')"]`);
        button.removeClass('btn-warning').addClass('btn-success').attr('disabled', true).html('<i class="fa fa-check"></i> Approved');
    }

    function submitApprovalForm() {
        // Mengambil data URL yang sudah disimpan pada form
        let url = $('#approvalForm').data('url');
        
        // Mengambil data dari form
        let status = $('#id_mst_persetujuan').val();
        let alasan = $('#id_mst_persetujuanalasan').val();
        
        // Mengecek apakah form sudah lengkap
        if (!status || !alasan) {
            alert("Harap pilih semua opsi.");
            return;
        }
        
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id_mst_persetujuan: status,
                id_mst_persetujuanalasan: alasan
            },
            success: function(response) {
                // Menyembunyikan modal setelah berhasil
                $('#approvalModal').modal('hide');
                
                // Menampilkan pesan sukses (opsional)
                alert('Project berhasil di-approve!');
                
                // Refresh halaman
                // location.reload();
                window.location.href = 'http://127.0.0.1:8000/permintaan_pengembangan/index2';
            },
            error: function(xhr) {
                alert('Terjadi kesalahan, coba lagi nanti.');
            }
        });
    }
</script>
@endpush



<?php $__env->startSection('title'); ?>
    Daftar Persetujuan Pengembangan
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <?php echo \Illuminate\View\Factory::parentPlaceholder('breadcrumb'); ?>
    <li class="active">Daftar Persetujuan Pengembangan</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <?php if (auth()->user()->level == 1 || auth()->user()->level == 5) { ?>
                    <button onclick="addForm('<?php echo e(route('persetujuan_pengembangan.store')); ?>')" class="btn btn-success btn-xs btn-flat"><i class="fa fa-plus-circle"></i> Tambah</button>
                    <button onclick="deleteSelected('<?php echo e(route('persetujuan_pengembangan.delete_selected')); ?>')" class="btn btn-danger btn-xs btn-flat"><i class="fa fa-trash"></i> Hapus</button>
                <?php } ?>
                <!-- <button onclick="cetakDokumen('<?php echo e(route('persetujuan_pengembangan.cetakDokumen')); ?>')" class="btn btn-info btn-xs btn-flat"><i class="fa fa-download"></i> Cetak Dokumen</button> -->
            </div>
            <div class="box-body table-responsive">
                <?php echo csrf_field(); ?>
                <table class="table table-stiped table-bordered" style="font-size: 12px;">
                    <thead>
                    <th style="width: 45px; padding: 4px;">
                        <input type="checkbox" name="select_all" id="select_all">
                    </th>
                    <th style="width: 15px; padding: 4px;">No</th>
                    <th style="width: 80px; padding: 4px;">Nomor Dokumen</th>
                    <th style="padding: 4px;">Alasan Persetujuan</th>
                    <th>Nama Pemohon</th>
                    <th>Jabatan Pemohon</th>
                    <th>Nama Penyetuju</th>
                    <th>Jabatan Penyetuju</th>
                    <th>Status Persetujuan</th>
                    <th style="width: 50px; padding: 4px;">File Upload Dokumen Permintaan TTD Lengkap</th>
                    <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<?php echo $__env->make('persetujuan_pengembangan.upload', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('persetujuan_pengembangan.viewform', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('persetujuan_pengembangan.update_progress', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('persetujuan_pengembangan.approve_dokumen', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php if ($__env->exists('persetujuan_pengembangan.form')) echo $__env->make('persetujuan_pengembangan.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    let table;

    $(function () {
        table = $('.table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '<?php echo e(route('persetujuan_pengembangan.data')); ?>',
            },
            columns: [
                {data: 'select_all', searchable: false, sortable: false},
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'nomor_dokumen'},
                {data: 'alasan_persetujuan'},
                {data: 'nama_pemohon'},
                {data: 'jabatan_pemohon'},
                {data: 'nama_penyetuju'},
                {data: 'jabatan_penyetuju'},
                {data: 'file_pdf'},
                {data: 'approval_status'},
                {data: 'aksi', searchable: false, sortable: false},
            ],
        });

        $('#modal-form').validator().on('submit', function (e) {
            if (!e.preventDefault()) {
                $.post($('#modal-form form').attr('action'), $('#modal-form form').serialize())
                    .done((response) => {
                        $('#modal-form').modal('hide');
                        table.ajax.reload();
                    })
                    .fail((errors) => {
                        alert('Tambah Data Gagal, Karena Anda Melewati Tahapan Sebelumnya atau Progress tahapan sebebelumnya belum 100%');
                        return;
                    });
            }
        });

        $('[name=select_all]').on('click', function () {
            $(':checkbox').prop('checked', this.checked);
        });

        $('#id_mst_persetujuan').change(function () {
            var id = $(this).val();
            $.get('/get-alasan-persetujuan/' + id, function (data) {
                var alasanSelect = $('#id_mst_persetujuanalasan');
                alasanSelect.empty();
                alasanSelect.append('<option value="">Pilih Alasan</option>');
                $.each(data, function (key, value) {
                    alasanSelect.append('<option value="' + key + '">' + value + '</option>');
                });
            });
        });
    });

    function addForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Tambah Persetujuan Pengembangan');
        $('.condition-set').show();

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
        $('#modal-form [name=id_permintaan_pengembangan]').prop('disabled', false);
        $('#modal-form [name=nama_proyek]').focus();

        var getnamapemohon = 'https://cobit-demo.ptsi.co.id/public/dashboard/get-nama-pemohon';
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
        var getnamapenyetuju = 'https://cobit-demo.ptsi.co.id/public/dashboard/get-nama-penyetuju';
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
            var url_link_get_identity_by_nik = 'https://cobit-demo.ptsi.co.id/public/dashboard/get-identity-by-nik/' + nik;
            
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
            var url_link_get_identity_by_nik = 'https://cobit-demo.ptsi.co.id/public/dashboard/get-identity-by-nik/' + nik;

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
    $('#modal-form .modal-title').text('Edit Persetujuan Pengembangan');
    $('.condition-set').hide();

    $('#modal-form form')[0].reset();
    $('#modal-form form').attr('action', url);
    $('#modal-form [name=_method]').val('put');
    $('#modal-form [name=nama_proyek]').focus();

        $.get(url)
            .done((response) => {
                $('#modal-form [name=id_permintaan_pengembangan]').val(response.id_permintaan_pengembangan).prop('disabled', true);
                $('#modal-form [name=nama_proyek]').val(response.nama_proyek);
                $('#modal-form [name=deskripsi]').val(response.deskripsi);
                CKEDITOR.instances['editor1'].setData(response.deskripsi);
                $('#modal-form [name=id_mst_persetujuan]').val(response.id_mst_persetujuan);
                $('#modal-form [name=nama_pemohon]').val(response.nama_pemohon);
                $('#modal-form [name=jabatan_pemohon]').val(response.jabatan_pemohon);
                $('#modal-form [name=nik_pemohon]').val(response.nik_pemohon);
                $('#modal-form [name=tanggal_disiapkan]').val(response.tanggal_disiapkan);
                $('#modal-form [name=nama]').val(response.nama);
                $('#modal-form [name=jabatan]').val(response.jabatan);
                $('#modal-form [name=nik_penyetuju]').val(response.nik_penyetuju);
                $('#modal-form [name=tanggal_disetujui]').val(response.tanggal_disetujui);

                var response_nik_pemohon = response.nik_pemohon;
                var response_nik_penyetuju = response.nik_penyetuju;

                var getnamapemohonpenyetuju = 'https://cobit-demo.ptsi.co.id/public/dashboard/get-nama-pemohon-penyetuju';
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
        $('[name="id_persetujuan_pengembangan[]"]:checked').each(function () {
            ids.push($(this).val());
        });

        if (ids.length > 0) {
            if (confirm('Yakin ingin menghapus data terpilih?')) {
                $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete',
                    'id_persetujuan_pengembangan': ids
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

    // function cetakDokumen(url) {
    //     var ids = [];
    //     $('[name="id_persetujuan_pengembangan[]"]:checked').each(function () {
    //         ids.push($(this).val());
    //     });

    //     if (ids.length < 1) {
    //         alert('Pilih data yang akan dicetak');
    //         return;
    //     } else {
    //         var form = $('<form>', {
    //             'method': 'POST',
    //             'action': url,
    //             'target': '_blank'
    //         });

    //         form.append($('<input>', {
    //             'type': 'hidden',
    //             'name': '_token',
    //             'value': '<?php echo e(csrf_token()); ?>'
    //         }));

    //         $.each(ids, function(index, id) {
    //             form.append($('<input>', {
    //                 'type': 'hidden',
    //                 'name': 'id_persetujuan_pengembangan[]',
    //                 'value': id
    //             }));
    //         });

    //         $('body').append(form);
    //         form.submit();
    //     }
    // }
    function cetakDokumen(url, idPersetujuan) {
        var form = $('<form>', {
            'method': 'POST',
            'action': url,
            'target': '_blank'
        });

        // Tambahkan CSRF token untuk keamanan
        form.append($('<input>', {
            'type': 'hidden',
            'name': '_token',
            'value': '<?php echo e(csrf_token()); ?>'
        }));

        // Tambahkan ID permintaan pengembangan ke dalam form
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'id_persetujuan_pengembangan',
            'value': idPersetujuan
        }));

        $('body').append(form);
        form.submit();
    }
    function viewForm(url) {
        $('#modal-view').modal('show');

        $.get(url)
            .done((response) => {
                $('#modal-view [name=id_permintaan_pengembangan]').val(response.nomor_dokumen)
                $('#modal-view [name=nama_proyek]').val(response.nama_proyek);
                $('#modal-view [name=deskripsi]').val(response.deskripsi);
                $('#modal-view [name=id_mst_persetujuan]').val(response.status_persetujuan);
                $('#modal-view [name=id_mst_persetujuanalasan]').val(response.alasan_persetujuan);
                $('#modal-view [name=namapemohon]').val(response.namapemohon);
                $('#modal-view [name=namapeninjau]').val(response.namapeninjau);
                $('#modal-view [name=jabatanpeninjau]').val(response.jabatanpeninjau);
                $('#modal-view [name=namapenyetuju]').val(response.namapenyetuju);
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
            console.log(url);
            if (response) {
                // Mengisi nilai nomor_dokumen
                $('#modalUpdateProgress [name=nomor_dokumen]').val(response.nomor_dokumen);

                // Mengisi nilai progress dan mengatur selected pada dropdown
                $('#modalUpdateProgress [name=progress]').val(response.progress).change(); // Memicu event change jika diperlukan
            }
        });
    }

    function approveForm(id) {
        // Reset form
        $('#modalApproveDokumen').modal('show');
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
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '<?php echo e(csrf_token()); ?>' // Gunakan CSRF token untuk keamanan request
            },
            success: function(response) {
                // Perbarui tampilan tombol menjadi 'Approved'
                let button = $(`button[onclick="approveProyek('${url}')"]`);
                button.removeClass('btn-warning').addClass('btn-success').attr('disabled', true).html('<i class="fa fa-check"></i> Approved');
                
                // Menampilkan pesan sukses (opsional)
                alert('Project berhasil di-approve!');
                location.reload();
            },
            error: function(xhr) {
                alert('Terjadi kesalahan, coba lagi nanti.');
            }
        });
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/cobitdemoptsico/public_html/resources/views/persetujuan_pengembangan/index.blade.php ENDPATH**/ ?>
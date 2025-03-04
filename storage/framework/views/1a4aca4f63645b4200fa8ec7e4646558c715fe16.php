

<?php $__env->startSection('title'); ?>
 Daftar Berita Acara Serah Terima Pekerjaan Sistem Aplikasi
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <?php echo \Illuminate\View\Factory::parentPlaceholder('breadcrumb'); ?>
    <li class="active">Daftar Berita Acara Serah Terima Aplikasi</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
            <?php if (auth()->user()->level == 1 || auth()->user()->level == 5) { ?>
                <button onclick="addForm('<?php echo e(route('serah_terima_aplikasi.store')); ?>')" class="btn btn-success btn-xs btn-flat"><i class="fa fa-plus-circle"></i> Tambah</button>
                <!-- <button onclick="deleteSelected('<?php echo e(route('serah_terima_aplikasi.delete_selected')); ?>')" class="btn btn-danger btn-xs btn-flat"><i class="fa fa-trash"></i> Hapus</button> -->
                <!-- <button onclick="cetakDokumen('<?php echo e(route('serah_terima_aplikasi.cetakDokumen')); ?>')" class="btn btn-info btn-xs btn-flat"><i class="fa fa-download"></i> Cetak Dokumen</button> -->
            <?php } ?>
            </div>
            <div class="box-body table-responsive">
                    <?php echo csrf_field(); ?>
                    <table class="table table-stiped table-bordered" style="font-size: 12px;">
                        <thead>
                        <th width="5%">
                            <input type="checkbox" name="select_all" id="select_all">
                        </th>
                        <th width="5%">No</th>
                        <th>Hari</th>
                        <th>Tanggal</th>
                        <th>Deskripsi</th>
                        <th>Lokasi</th>
                        <th>Nama Aplikasi</th>
                        <th>Nomor Permintaan</th>
                        <th>Keterangan</th>
                        <th>Pemberi</th>
                        <th>NIK Pemberi</th>
                        <th>Penerima</th>
                        <th>NIK Penerima</th>
                        <th style="width: 50px; padding: 4px;">File Upload Dokumen Permintaan TTD Lengkap</th>
                        <th width="15%"><i class="fa fa-cog"></i>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $__env->make('serah_terima_aplikasi.upload', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('serah_terima_aplikasi.viewform', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('serah_terima_aplikasi.update_progress', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php if ($__env->exists('serah_terima_aplikasi.form')) echo $__env->make('serah_terima_aplikasi.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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
                url: '<?php echo e(route('serah_terima_aplikasi.data')); ?>',
            },
            columns: [
                {data: 'select_all', searchable: false, sortable: false},
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'hari'},
                {
                    data: 'tanggal',
                    render: function(data) {
                        if (data) {
                            return new Date(data).toLocaleDateString('en-GB', { year: 'numeric', month: 'short', day: 'numeric' });
                        }
                        return data;
                    }
                },
                {data: 'deskripsi'},
                {data: 'lokasi'},
                {data: 'nama_aplikasi'},
                {data: 'nomor_dokumen'},
                {data: 'keterangan'},
                {data: 'pemberi'},
                {data: 'nik_pemberi'},
                {data: 'penerima'},
                {data: 'nik_penerima'},
                {data: 'file_pdf'},
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
    });


    function addForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Tambah Berita Acara Serah Terima Aplikasi');

        $('.condition-set').show();

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
        $('#modal-form [name=hari]').focus();
    }

    function editForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Edit Permintaan Pengembangan');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('put');
        $('#modal-form [name=harihari]').focus();

        $('.condition-set').hide();

        $.get(url)
            .done((response) => {
                $('#modal-form [name=id_permintaan_pengembangan]').val(response.no_permintaan).prop('disabled', true);
                $('#modal-form [name=hari]').val(response.hari);
                $('#modal-form [name=tanggal]').val(response.tanggal);
                $('#modal-form [name=deskripsi]').val(response.deskripsi);
                $('#modal-form [name=lokasi]').val(response.lokasi);
                $('#modal-form [name=nama_aplikasi]').val(response.nama_aplikasi);
                $('#modal-form [name=no_permintaan]').val(response.no_permintaan);
                $('#modal-form [name=keterangan]').val(response.keterangan);
                $('#modal-form [name=pemberi]').val(response.pemberi);
                $('#modal-form [name=penerima]').val(response.penerima);
                $('#modal-form [name=nik_pemberi]').val(response.pemberi);
                $('#modal-form [name=nik_penerima]').val(response.penerima);
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
        $('[name="id_serah_terima_aplikasi[]"]:checked').each(function () {
            ids.push($(this).val());
        });

        if (ids.length > 0) {
            if (confirm('Yakin ingin menghapus data terpilih?')) {
                $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete',
                    'id_serah_terima_aplikasi': ids
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


    function cetakDokumen(url, idSerahTerimaAplikasi) { 
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
                'value': '<?php echo e(csrf_token()); ?>'
            }));

            // $('input:checked').each(function() {
            //     form.append($('<input>', {
            //         'type': 'hidden',
            //         'name': 'id_serah_terima_aplikasi[]',
            //         'value': $(this).val()
            //     }));
            // });

            form.append($('<input>', {
                'type': 'hidden',
                'name': 'id_serah_terima_aplikasi',
                'value': idSerahTerimaAplikasi
            }));

            $('body').append(form);
            form.submit();
        // }
    }

    function viewForm(url) {
        $('#modal-view').modal('show');

        $.get(url)
            .done((response) => {
                $('#modal-view [name=hari]').val(response.hari);
                $('#modal-view [name=tanggal]').val(response.tanggal);
                $('#modal-view [name=deskripsi]').val(response.deskripsi);
                $('#modal-view [name=lokasi]').val(response.lokasi);
                $('#modal-view [name=nama_aplikasi]').val(response.nama_aplikasi);
                $('#modal-view [name=no_permintaan]').val(response.nomor_dokumen);
                $('#modal-view [name=keterangan]').val(response.keterangan);
                $('#modal-view [name=pemberi]').val(response.pemberi);
                $('#modal-view [name=penerima]').val(response.penerima);
                $('#modal-view [name=nik_pemberi]').val(response.pemberi);
                $('#modal-view [name=nik_penerima]').val(response.penerima);
            })
            .fail((errors) => {
                alert('Tidak dapat menampilkan data');
                return;
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

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/cobitptsico/public_html/resources/views/serah_terima_aplikasi/index.blade.php ENDPATH**/ ?>
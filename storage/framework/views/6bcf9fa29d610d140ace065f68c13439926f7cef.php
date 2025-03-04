

<?php $__env->startSection('title'); ?>
    Daftar User Acceptance Testing
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <?php echo \Illuminate\View\Factory::parentPlaceholder('breadcrumb'); ?>
    <li class="active">Daftar User Acceptance Testing</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
            <?php if (auth()->user()->level == 1 || auth()->user()->level == 5) { ?>
                <button onclick="addForm('<?php echo e(route('user_acceptance_testing.store')); ?>')" class="btn btn-success btn-xs btn-flat"><i class="fa fa-plus-circle"></i> Tambah</button>
                <button onclick="deleteSelected('<?php echo e(route('user_acceptance_testing.delete_selected')); ?>')" class="btn btn-danger btn-xs btn-flat"><i class="fa fa-trash"></i> Hapus</button>
                <!-- <button onclick="cetakDokumenPerencanaan('<?php echo e(route('user_acceptance_testing.cetakDokumenPerencanaan')); ?>')" class="btn btn-info btn-xs btn-flat"><i class="fa fa-download"></i> Cetak Dok Perencanaan UAT</button> -->
                <!-- <button onclick="cetakDokumen('<?php echo e(route('user_acceptance_testing.cetakDokumen')); ?>')" class="btn btn-info btn-xs btn-flat"><i class="fa fa-download"></i> Cetak Dokumen UAT</button> -->
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
                         <th>Tambah Detail Pengisian</th>
                        <!-- <th>Nomor Proyek</th> -->
                        <th>Nama Aplikasi</th>
                        <th>Jenis Aplikasi</th>
                        <th>Unit Pemilik</th>
                        <th>Tanggal Pengujian</th>
                        <th>Nama Penguji</th>
                        <!-- <th>Tanggal Pengujian</th>
                        <th>Nama Pemohon</th>
                        <th>Jabatan Pemohon</th>
                        <th>Tanggal Disiapkan</th>
                        <th>Nama Penyetuju</th>
                        <th>Jabatan Penyetuju</th>
                        <th>Tanggal Disetujui</th> -->
                        <th>File PDF Dokumen UAT</th>
                        <!-- <th>Status</th> -->
                        <th width="15%"><i class="fa fa-cog"></i>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $__env->make('user_acceptance_testing.upload', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('user_acceptance_testing.update_progress', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php if ($__env->exists('user_acceptance_testing.form')) echo $__env->make('user_acceptance_testing.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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
                url: '<?php echo e(route('user_acceptance_testing.data')); ?>',
            },
            columns: [
                {data: 'select_all', searchable: false, sortable: false},
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'detail_pengisian'},
                // {data: 'nomor_proyek'},
                {data: 'nama_aplikasi'},
                {data: 'jenis_aplikasi'},
                {data: 'unit_pemilik'},
                {
                    data: 'tanggal_pengujian',
                    render: function(data) {
                        if (data) {
                            return new Date(data).toLocaleDateString('en-GB', { year: 'numeric', month: 'short', day: 'numeric' });
                        }
                        return data;
                    }
                },
                {data: 'nama_penguji'},
                // {data: 'nama_pemohon'},
                // {data: 'jabatan_pemohon'},
                // {data: 'tanggal_disiapkan'},
                // {data: 'nama_penyetuju'},
                // {data: 'jabatan_penyetuju'},
                // {data: 'tanggal_disetujui'},
                {data: 'file_pdf'},
                // {data: 'approval_status'},
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
        $('#modal-form .modal-title').text('Tambah User Acceptance Testing');
        $('.condition-set').show();

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
        $('#modal-form [name=nomor_proyek]').focus();

        var getnamapemohonpenyetuju = 'https://cobit.ptsi.co.id/public/dashboard/get-nama-pemohon-penyetuju';
        $.get(getnamapemohonpenyetuju)
        .done((response) => {
            let pemohonSelect = $('#modal-form [name=nik_pemohon]');
            pemohonSelect.empty();
            pemohonSelect.append('<option value="" disabled selected>Pilih Nama Pemohon</option>'); 
            if (Array.isArray(response.pemohonpenyetuju)) {
                response.pemohonpenyetuju.forEach(function(item, index) {
                    pemohonSelect.append(`<option value="${item.employee_id}">${item.full_name} - ${item.position_name}</option>`);
                });
            } else {
                penyetujuSelect.append('<option value="" disabled>Tidak ada data pemohon</option>');
            }

            let penyetujuSelect = $('#modal-form #nik_penyetuju');
            penyetujuSelect.empty(); 
            penyetujuSelect.append('<option value="" disabled selected>Pilih Nama Penyetuju</option>');
            if (Array.isArray(response.pemohonpenyetuju)) {
                response.pemohonpenyetuju.forEach(function(item, index) {
                    penyetujuSelect.append(`<option value="${item.employee_id}">${item.full_name} - ${item.position_name}</option>`);
                });
            } else {
                penyetujuSelect.append('<option value="" disabled>Tidak ada data penyetuju</option>');
            }
        })
        .fail((errors) => {
            alert('Tidak dapat mengambil data pemohon dan penyetuju');
            return;
        });
    }

    // On Change Atribut
    $("#modal-form [name=nik_pemohon]").on('change', function() {
            var nik = $(this).val();
            var url_link_get_identity_by_nik = 'https://cobit.ptsi.co.id/public/dashboard/get-identity-by-nik/' + nik;
            
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
            var url_link_get_identity_by_nik = 'https://cobit.ptsi.co.id/public/dashboard/get-identity-by-nik/' + nik;

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
        $('#modal-form .modal-title').text('Edit User Acceptance Testing');
        $('.condition-set').hide();

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('put');
        $('#modal-form [name=nomor_proyek]').focus();

        $.get(url)
            .done((response) => {
                $('#modal-form [name=id_permintaan_pengembangan]').val(response.id_permintaan_pengembangan).prop('disabled', true);
                $('#modal-form [name=nomor_proyek]').val(response.nomor_proyek);
                $('#modal-form [name=nama_aplikasi]').val(response.nama_aplikasi);
                $('#modal-form [name=unit_pemilik_proses_bisnis]').val(response.unit_pemilik_proses_bisnis);
                $('#modal-form [name=nama_penguji]').val(response.nama_penguji);
                $('#modal-form [name=jenis_aplikasi]').val(response.jenis_aplikasi);
                $('#modal-form [name=kebutuhan_fungsional]').val(response.kebutuhan_fungsional);
                CKEDITOR.instances['editor1'].setData(response.kebutuhan_fungsional);
                $('#modal-form [name=kebutuhan_nonfungsional]').val(response.kebutuhan_nonfungsional);
                CKEDITOR.instances['editor2'].setData(response.kebutuhan_nonfungsional);
                $('#modal-form [name=unit_pemilik_proses_bisnis]').val(response.unit_pemilik_proses_bisnis);
                $('#modal-form [name=lokasi_pengujian]').val(response.lokasi_pengujian);
                var tanggal = response.tanggal_pengujian; // Misalnya "13-11-2024"
                var parts = tanggal.split("-"); // Memisahkan berdasarkan '-'
                var formattedDate = parts[2] + '-' + parts[1] + '-' + parts[0]; // Format menjadi "2024-11-13"

                // Menetapkan nilai ke input
                $('#modal-form [name=tanggal_pengujian]').val(formattedDate);
                $('#modal-form [name=manual_book]').val(response.manual_book);
                $('#modal-form [name=nama_penyusun]').val(response.nama_penyusun);
                $('#modal-form [name=jabatan_penyusun]').val(response.jabatan_penyusun);
                $('#modal-form [name=tgl_disusun]').val(response.tgl_disusun);
                $('#modal-form [name=nama_penyetuju]').val(response.nama_penyetuju);
                $('#modal-form [name=jabatan_penyetuju]').val(response.jabatan_penyetuju);
                $('#modal-form [name=tanggal_disetujui]').val(response.tanggal_disetujui);
                
                var response_nik_pemohon = response.nik_pemohon;
                var response_nik_penyetuju = response.nik_penyetuju;

                var getnamapemohonpenyetuju = 'https://cobit.ptsi.co.id/public/dashboard/get-nama-pemohon-penyetuju';
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
        $('[name="id_user_acceptance_testing[]"]:checked').each(function () {
            ids.push($(this).val());
        });

        if (ids.length > 0) {
            if (confirm('Yakin ingin menghapus data terpilih?')) {
                $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete',
                    'id_user_acceptance_testing': ids
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


    function cetakDokumen(url, iduseracceptancetesting) {
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
            //         'name': 'id_user_acceptance_testing[]',
            //         'value': $(this).val()
            //     }));
            // });
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'id_user_acceptance_testing',
                'value': iduseracceptancetesting
            }));

            $('body').append(form);
            form.submit();
        // }
    }

    function cetakDokumenPerencanaan(url, iduseracceptancetesting) {
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
            //         'name': 'id_user_acceptance_testing[]',
            //         'value': $(this).val()
            //     }));
            // });

            form.append($('<input>', {
                'type': 'hidden',
                'name': 'id_user_acceptance_testing',
                'value': iduseracceptancetesting
            }));

            $('body').append(form);
            form.submit();
        // }
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
                        _token: '<?php echo e(csrf_token()); ?>' // Gunakan CSRF token untuk keamanan request
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/cobitptsico/public_html/resources/views/user_acceptance_testing_dev/index.blade.php ENDPATH**/ ?>
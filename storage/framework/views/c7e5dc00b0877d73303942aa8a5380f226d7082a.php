

<?php $__env->startSection('title'); ?>
    Daftar Perencanaan Kebutuhan
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <?php echo \Illuminate\View\Factory::parentPlaceholder('breadcrumb'); ?>
    <li class="active">Daftar Perencanaan Kebutuhan</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <?php if (auth()->user()->level == 1 || auth()->user()->level == 5) { ?>
                    <button onclick="addForm('<?php echo e(route('perencanaan_kebutuhan.store')); ?>')" class="btn btn-success btn-xs btn-flat"><i class="fa fa-plus-circle"></i> Tambah</button>
                    <button onclick="deleteSelected('<?php echo e(route('perencanaan_kebutuhan.delete_selected')); ?>')" class="btn btn-danger btn-xs btn-flat"><i class="fa fa-trash"></i> Hapus</button>
                    <!-- <button onclick="cetakDokumen('<?php echo e(route('perencanaan_kebutuhan.cetakDokumen')); ?>')" class="btn btn-info btn-xs btn-flat"><i class="fa fa-download"></i> Cetak Dokumen</button> -->
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
                            <th>Nomor Dokumen</th>
                            <!-- <th>Nomor Proyek</th> -->
                            <th>Nama Proyek</th>
                            <th>Pemilik Proyek</th>
                            <th>Manajer Proyek</th>
                            <th>Stakeholders</th>
                            <th>Kebutuhan Fungsional</th>
                            <th>Kebutuhan Non-fungsional</th>
                            <th style="width: 100px; padding: 4px;">File Upload Lampiran</th>
                            <th style="width: 50px; padding: 4px;">File Upload Dokumen Permintaan TTD Lengkap</th>
                            <th>Status</th>
                            <th width="15%"><i class="fa fa-cog"></i>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $__env->make('perencanaan_kebutuhan.upload', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('perencanaan_kebutuhan.viewform', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('perencanaan_kebutuhan.update_progress', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php if ($__env->exists('perencanaan_kebutuhan.form')) echo $__env->make('perencanaan_kebutuhan.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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
                url: '<?php echo e(route('perencanaan_kebutuhan.data')); ?>',
            },
            columns: [
                {data: 'select_all', searchable: false, sortable: false},
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'nomor_dokumen'},
                // {data: 'nomor_proyek'},
                {data: 'nama_proyek'},
                {data: 'pemilik_proyek'},
                {data: 'manajer_proyek'},
                {data: 'stakeholders'},
                {data: 'kebutuhan_fungsional'},
                {data: 'kebutuhan_nonfungsional'},
                {
                    data:function(row){
                        return `
                                <div class="col-lg-6">
                                    <a href="/storage/assets/lampiran/${row.lampiran}" target="_blank" class="btn btn-primary btn-sm">Lihat Lampiran</a>
                                </div>
                                `;
                    }
                },
                {data: 'file_pdf'},
                {data: 'approval_status'},
                {data: 'aksi', searchable: false, sortable: false},
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
                        table.ajax.reload();
                    },
                    error: function(errors) {
                        alert('Tambah Data Gagal, Karena Anda Melewati Tahapan Sebelumnya atau Progress tahapan sebebelumnya belum 100%');
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
        $('#modal-form .modal-title').text('Tambah Perencanaan Kebutuhan');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
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

        var getnamapemverifikasi = 'https://cobit-demo.ptsi.co.id/public/dashboard/get-nama-pemverifikasi';
        $.get(getnamapemverifikasi)
        .done((response) => {
            let pemverifikasiSelect = $('#modal-form #nik_pemverifikasi');
            pemverifikasiSelect.empty(); 
            pemverifikasiSelect.append('<option value="" disabled selected>Pilih Nama Pemverifikasi</option>');
            if (Array.isArray(response.pemverifikasi)) {
                response.pemverifikasi.forEach(function(item, index) {
                pemverifikasiSelect.append(`<option value="${item.employee_id}">${item.full_name} - ${item.position_name}</option>`);
            });
            } else {
                pemverifikasiSelect.append('<option value="" disabled>Tidak ada data pemverifikasi</option>');
            }
        })
        .fail((errors) => {
            alert('Tidak dapat mengambil data pemverifikasi');
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

    $("#modal-form [name=nik_pemverifikasi]").on('change', function() {
            var nik = $(this).val();
            var url_link_get_identity_by_nik = 'https://cobit-demo.ptsi.co.id/public/dashboard/get-identity-by-nik/' + nik;

            $.get(url_link_get_identity_by_nik)
            .done((response) => {
                $('#modal-form [name=nama_pemverifikasi]').val(response.pemohonpenyetuju.full_name);
                $('#modal-form [name=jabatan_pemverifikasi]').val(response.pemohonpenyetuju.position_name);
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
        $('#modal-form .modal-title').text('Edit Perencanaan Kebutuhan');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('put');
        $('#modal-form [name=nama_proyek]').focus();

        $.get(url)
            .done((response) => {
                $('#modal-form [name=id_persetujuan_pengembangan]').val(response.nama_proyek).prop('disabled', true);
                $('#modal-form [name=id_perencanaan_proyek]').val(response.id_perencanaan_proyek).prop('disabled', true);
                $('#modal-form [name=stakeholders]').val(response.stakeholders);
                CKEDITOR.instances['editor1'].setData(response.stakeholders);
                $('#modal-form [name=kebutuhan_fungsional]').val(response.kebutuhan_fungsional);
                CKEDITOR.instances['editor2'].setData(response.kebutuhan_fungsional);
                $('#modal-form [name=kebutuhan_nonfungsional]').val(response.kebutuhan_nonfungsional);
                CKEDITOR.instances['editor3'].setData(response.kebutuhan_nonfungsional);
                $('#modal-form [name=lampiran]').val(response.lampiran);
                $('#modal-form [name=nama_pemohon]').val(response.nama_pemohon);
                $('#modal-form [name=jabatan_pemohon]').val(response.jabatan_pemohon);
                $('#modal-form [name=nik_pemohon]').val(response.nik_pemohon);
                $('#modal-form [name=tanggal_disiapkan]').val(response.tanggal_disiapkan);
                $('#modal-form [name=nama_pemverifikasi]').val(response.nama_pemverifikasi);
                $('#modal-form [name=jabatan_pemverifikasi]').val(response.jabatan_pemverifikasi);
                $('#modal-form [name=nik_pemverifikasi]').val(response.nik_pemverifikasi);
                $('#modal-form [name=tanggal_verifikasi]').val(response.tanggal_verifikasi);
                $('#modal-form [name=nama_penyetuju]').val(response.nama_penyetuju);
                $('#modal-form [name=jabatan_penyetuju]').val(response.jabatan_penyetuju);
                $('#modal-form [name=nik_penyetuju]').val(response.nik_penyetuju);
                $('#modal-form [name=tanggal_disetujui]').val(response.tanggal_disetujui);
                
                var response_nik_pemohon = response.nik_pemohon;
                var response_nik_pemverifikasi = response.nik_pemverifikasi;
                var response_nik_penyetuju = response.nik_penyetuju;

                var getnamapemohon = 'https://cobit-demo.ptsi.co.id/public/dashboard/get-nama-pemohon';
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

                var getnamapemverifikasi = 'https://cobit-demo.ptsi.co.id/public/dashboard/get-nama-pemverifikasi';
                $.get(getnamapemverifikasi)
                .done((response) => {
                    let pemverifikasiSelect = $('#modal-form #nik_pemverifikasi');
                    pemverifikasiSelect.empty(); 
                    pemverifikasiSelect.append('<option value="" disabled selected>Pilih Nama Pemverifikasi</option>');
                    if (Array.isArray(response.pemverifikasi)) {
                        response.pemverifikasi.forEach(function(item, index) {
                            pemverifikasiSelect.append(`<option value="${item.employee_id}" ${item.employee_id == response_nik_pemverifikasi ? 'selected' : ''}>${item.full_name} - ${item.position_name}</option>`);
                        });
                    } else {
                        pemverifikasiSelect.append('<option value="" disabled>Tidak ada data pemverifikasi</option>');
                    }
                })
                .fail((errors) => {
                    alert('Tidak dapat mengambil data pemverifikasi');
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
        $('[name="id_perencanaan_kebutuhan[]"]:checked').each(function () {
            ids.push($(this).val());
        });

        if (ids.length > 0) {
            if (confirm('Yakin ingin menghapus data terpilih?')) {
                $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete',
                    'id_perencanaan_kebutuhan': ids
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
    //     if ($('input:checked').length < 1) {
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

    //         $('input:checked').each(function() {
    //             form.append($('<input>', {
    //                 'type': 'hidden',
    //                 'name': 'id_perencanaan_kebutuhan[]',
    //                 'value': $(this).val()
    //             }));
    //         });

    //         $('body').append(form);
    //         form.submit();
    //     }
    // }

    function cetakDokumen(url, idPerencanaanKebutuhan) {
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
            'name': 'id_perencanaan_kebutuhan',
            'value': idPerencanaanKebutuhan
        }));

        $('body').append(form);
        form.submit();
    }
    
    function viewForm(url) {
        $('#modal-view').modal('show');

        $.get(url)
            .done((response) => {
                $('#modal-view [name=id_persetujuan_pengembangan]').val(response.id_persetujuan_pengembangan).prop('disabled', true);

                $('#modal-view [name=nama_pemohon]').val(response.nama_pemohon);
                $('#modal-view [name=jabatan_pemohon]').val(response.jabatan_pemohon);
                $('#modal-view [name=tanggal_disiapkan]').val(response.tanggal_disiapkan);

                $('#modal-view [name=nama_pemverifikasi]').val(response.nama_pemverifikasi);
                $('#modal-view [name=jabatan_pemverifikasi]').val(response.jabatan_pemverifikasi);
                $('#modal-view [name=tanggaal_pemverifikasi]').val(response.tanggaal_pemverifikasi);

                $('#modal-view [name=nama_penyetuju]').val(response.nama_penyetuju);
                $('#modal-view [name=jabatan_penyetuju]').val(response.jabatan_penyetuju);
                $('#modal-view [name=tanggal_verifikasi]').val(response.tanggal_verifikasi);

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
                // Mengisi nilai nama_proyek
                $('#modalUpdateProgress [name=nama_proyek]').val(response.nama_proyek);

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

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/cobitdemoptsico/public_html/resources/views/perencanaan_kebutuhan/index.blade.php ENDPATH**/ ?>
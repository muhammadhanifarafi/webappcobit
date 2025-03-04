<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Cobit | {{ $setting->nama_perusahaan }} | @yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <link rel="icon" href="{{ url($setting->path_logo) }}" type="image/png">

    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('/AdminLTE-2/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('/AdminLTE-2/bower_components/font-awesome/css/font-awesome.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('/AdminLTE-2/dist/css/AdminLTE.min.css') }}">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ asset('/AdminLTE-2/dist/css/skins/_all-skins.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('/AdminLTE-2/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet" href="../../plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('/AdminLTE-2/bower_components/select2/dist/css/select2.min.css') }}">
    <!-- sweetalert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.min.css">
    <!-- datepicker -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker3.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    <style>
        .select2 {
            width: 100% !important; /* memastikan lebar select2 100% dari container */
        }
    </style>
    @stack('css')
</head>
<body class="hold-transition skin-purple-light sidebar-mini">
    <div class="wrapper">

        @includeIf('layouts.header')

        @includeIf('layouts.sidebar')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    @yield('title')
                </h1>
                <ol class="breadcrumb">
                    @section('breadcrumb')
                        <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                    @show
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                
                @yield('content')

            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        @includeIf('layouts.footer')
    </div>
    <!-- ./wrapper -->

    <!-- jQuery 3 -->
    <script src="{{ asset('AdminLTE-2/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="{{ asset('AdminLTE-2/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- Moment -->
    <script src="{{ asset('AdminLTE-2/bower_components/moment/min/moment.min.js') }}"></script>
    <!-- sweetalert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.min.js"></script>
    <!-- DataTables -->
    <script src="{{ asset('AdminLTE-2/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('AdminLTE-2/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('AdminLTE-2/dist/js/adminlte.min.js') }}"></script>
    <!-- Validator -->
    <script src="{{ asset('js/validator.min.js') }}"></script>
    <!-- CK Editor -->
    <script src="{{ asset('AdminLTE-2/bower_components/ckeditor/ckeditor.js') }}"></script>
    <!-- Bootstrap WYSIHTML5 -->
    <script src="{{ asset('AdminLTE-2/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('AdminLTE-2/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <!-- Datepicker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

    <script>
        $('.select2').select2()

        function preview(selector, temporaryFile, width = 200)  {
            $(selector).empty();
            $(selector).append(`<img src="${window.URL.createObjectURL(temporaryFile)}" width="${width}">`);
        }

        (function () {
            // Replace the <textarea id="editor1"> with a CKEditor instance, using default configuration.
            CKEDITOR.replace('editor1', {
                removePlugins: 'image,link', // Menonaktifkan plugin image, link, dan clipboard (copy/paste)
                allowedContent: true, // Membolehkan semua konten, menghindari pemformatan tertentu
                disallowedContent: 'img, a', // Menonaktifkan elemen gambar dan tautan
                // Menonaktifkan paste dan drag-drop gambar
                on: {
                    paste: function (evt) {
                        // Allow text and clean up unwanted HTML elements
                        const data = evt.data.dataTransfer.getData('text/plain'); // Get plain text from paste
                        if (data) {
                            evt.data.dataValue = data; // Insert the plain text into the editor
                        }
                    }
                }
            });

            CKEDITOR.replace('editor2', {
                removePlugins: 'image,link',
                allowedContent: true,
                disallowedContent: 'img, a',
                paste: function (evt) {
                    // Allow text and clean up unwanted HTML elements
                    const data = evt.data.dataTransfer.getData('text/plain'); // Get plain text from paste
                    if (data) {
                        evt.data.dataValue = data; // Insert the plain text into the editor
                    }
                }
            });

            CKEDITOR.replace('editor3', {
                removePlugins: 'image,link',
                allowedContent: true,
                disallowedContent: 'img, a',
                paste: function (evt) {
                    // Allow text and clean up unwanted HTML elements
                    const data = evt.data.dataTransfer.getData('text/plain'); // Get plain text from paste
                    if (data) {
                        evt.data.dataValue = data; // Insert the plain text into the editor
                    }
                }
            });

            CKEDITOR.replace('editor4', {
                removePlugins: 'image,link',
                allowedContent: true,
                disallowedContent: 'img, a',
                paste: function (evt) {
                    // Allow text and clean up unwanted HTML elements
                    const data = evt.data.dataTransfer.getData('text/plain'); // Get plain text from paste
                    if (data) {
                        evt.data.dataValue = data; // Insert the plain text into the editor
                    }
                }
            });

            CKEDITOR.replace('editor5', {
                removePlugins: 'image,link',
                allowedContent: true,
                disallowedContent: 'img, a',
                paste: function (evt) {
                    // Allow text and clean up unwanted HTML elements
                    const data = evt.data.dataTransfer.getData('text/plain'); // Get plain text from paste
                    if (data) {
                        evt.data.dataValue = data; // Insert the plain text into the editor
                    }
                }
            });

            //bootstrap WYSIHTML5 - text editor
            $('.textarea').wysihtml5();
        })();
        
        function addForm() {
            var addrow = `
                <div class="form-group baru-data">
                    <div class="col-md-3">
                        <input type="text" name="nama_produk[]" placeholder="Nama Produk" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="jumlah_produk[]" placeholder="Jumlah Produk" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <select name="kategori_produk[]" class="form-control">
                            <option value="">- Pilih Kategori -</option>
                            <option value="1">Buku</option>
                            <option value="2">Elektronik</option>
                            <option value="3">Kesehatan</option>
                            <option value="4">Rumah Tangga</option>
                            <option value="5">Mainan Hobi</option>
                            <option value="6">Olahraga</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <textarea name="deskripsi_produk[]" placeholder="Deskripsi Produk" class="form-control" rows="1"></textarea>
                    </div>
                    <div class="button-group">
                        <button type="button" class="btn btn-success btn-tambah"><i class="fa fa-plus"></i></button>
                        <button type="button" class="btn btn-danger btn-hapus"><i class="fa fa-times"></i></button>
                    </div>
                </div>
            `;
            $("#dynamic_form").append(addrow);
        }

        // Add new row when "Tambah" button is clicked
        $("#dynamic_form").on("click", ".btn-tambah", function() {
            addForm(); // Add a new row
            $(this).css("display", "none"); // Hide the "Tambah" button in the current row
            // Show the "Hapus" button
            $(this).closest('.button-group').find('.btn-hapus').css("display", "inline-block");
        });

        // Remove a row when "Hapus" button is clicked
        $("#dynamic_form").on("click", ".btn-hapus", function() {
            $(this).closest('.form-group').remove(); // Remove the current row
            var rows = $(".baru-data").length;
            if (rows === 1) {
                $(".btn-hapus").css("display", "none"); // Hide the "Hapus" button if there's only 1 row left
                $(".btn-tambah").css("display", "inline-block"); // Show the "Tambah" button
            } else {
                $('.baru-data').last().find('.btn-tambah').css("display", "inline-block"); // Ensure "Tambah" button is shown in the last row
            }
        });

        // Validate the form before submission
        $('.btn-simpan').on('click', function(event) {
            $('#dynamic_form').find('input[type="text"], input[type="number"], select, textarea').each(function() {
                if ($(this).val() == "") {
                    event.preventDefault(); // Prevent form submission
                    $(this).css('border-color', 'red'); // Highlight the empty fields
                    $(this).on('focus', function() {
                        $(this).css('border-color', '#ccc'); // Reset border color when focused
                    });
                }
            });
        });

        $('#fecha1').datepicker({
          autoclose: true,
          format: "dd-M-yyyy", // dd-mmm-yyyy format (10-Nov-2024)
          language: "en", // use "en" for English month names (e.g., "Nov")
          todayHighlight: true // Optionally highlight today's date
        });

        $('#fecha2').datepicker({
          autoclose: true,
          format: "dd-M-yyyy", // dd-mmm-yyyy format (10-Nov-2024)
          language: "en", // use "en" for English month names (e.g., "Nov")
          todayHighlight: true // Optionally highlight today's date
        });

        function validateFile() {
            var fileInput = document.getElementById('lampiran');
            var filePath = fileInput.value;
            var fileError = document.getElementById('fileError');
            
            // Clear any previous error messages
            fileError.textContent = "";

            // Check if a file is selected
            if (fileInput.files && fileInput.files[0]) {
                var file = fileInput.files[0];
                var fileSize = file.size / 1024 / 1024; // Convert size to MB
                var fileType = file.type;

                // Check if file size is more than 25 MB
                if (fileSize > 25) {
                    fileError.textContent = "Error: File size exceeds 25 MB!";
                    fileInput.value = ''; // Clear the input if the file is too large
                    return false;
                }

                // Check if the file is a PDF
                if (fileType !== "application/pdf") {
                    fileError.textContent = "Error: Only PDF files are allowed!";
                    fileInput.value = ''; // Clear the input if the file type is not PDF
                    return false;
                }
            }
        }
    </script>
    @stack('scripts')
</body>
</html>

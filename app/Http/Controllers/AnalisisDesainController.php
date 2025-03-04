<?php

namespace App\Http\Controllers;

use App\Models\AnalisisDesain;
use App\Models\PerencanaanProyek;
use App\Models\PermintaanPengembangan;
use App\Models\FlagStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;

class AnalisisDesainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $nama_permintaan_terpakai = AnalisisDesain::pluck('id_permintaan_pengembangan')->toArray();

        // Menggunakan select untuk mengambil kolom nomor dokumen dan judul
        $trx_permintaan_pengembangan = PermintaanPengembangan::leftJoin('trx_persetujuan_pengembangan', 'trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan')
        ->whereNotIn('trx_permintaan_pengembangan.id_permintaan_pengembangan', $nama_permintaan_terpakai)
        ->whereIn('trx_permintaan_pengembangan.id_permintaan_pengembangan', function ($query) {
            $query->select('id_permintaan_pengembangan')
                ->from('trx_persetujuan_pengembangan');
        })
        ->when(auth()->user()->level != 1, function ($query) {
            // Menambahkan filter berdasarkan created_by hanya jika level pengguna = 1 (admin)
            return $query->where('trx_permintaan_pengembangan.created_by', auth()->id());
        })
        ->select('trx_permintaan_pengembangan.id_permintaan_pengembangan', 'trx_permintaan_pengembangan.nomor_dokumen', 'trx_permintaan_pengembangan.judul') // Pilih kolom yang diinginkan
        ->get(); // Ambil data dalam bentuk koleksi           
        
        return view('analisis_desain.index', compact('trx_permintaan_pengembangan'));
    }

    public function data()
    {
        $trx_analisis_desain = AnalisisDesain::leftJoin('trx_permintaan_pengembangan', 'trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_analisis_desain.id_permintaan_pengembangan')
        ->leftJoin('trx_persetujuan_pengembangan', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan', '=', 'trx_permintaan_pengembangan.id_permintaan_pengembangan')
        ->leftJoin('trx_perencanaan_proyek', 'trx_perencanaan_proyek.id_persetujuan_pengembangan', '=', 'trx_persetujuan_pengembangan.id_persetujuan_pengembangan')
        ->leftJoin('trx_perencanaan_kebutuhan', 'trx_perencanaan_kebutuhan.id_persetujuan_pengembangan', '=', 'trx_persetujuan_pengembangan.id_persetujuan_pengembangan')
        ->select(
            'trx_permintaan_pengembangan.nomor_dokumen',
            'trx_permintaan_pengembangan.file_pdf as file_pdf_permintaan',
            'trx_analisis_desain.id_analisis_desain', 
            'trx_perencanaan_proyek.nomor_proyek',
            'trx_perencanaan_proyek.nama_proyek',
            'trx_perencanaan_proyek.manajer_proyek',
            'trx_perencanaan_proyek.file_pdf as file_pdf_proyek',
            'trx_perencanaan_kebutuhan.kebutuhan_fungsional',
            'trx_analisis_desain.gambaran_arsitektur',
            'trx_analisis_desain.detil_arsitektur',
            'trx_analisis_desain.lampiran_mockup',
            'trx_analisis_desain.nama_pemohon',
            'trx_analisis_desain.jabatan_pemohon',
            'trx_analisis_desain.tanggal_disiapkan',
            'trx_analisis_desain.nama_penyetuju',
            'trx_analisis_desain.jabatan_penyetuju',
            'trx_analisis_desain.tanggal_disetujui',
            'trx_analisis_desain.status',
            'trx_analisis_desain.file_pdf',
            'trx_analisis_desain.progress',
            'trx_analisis_desain.is_approve',
            'trx_analisis_desain.approve_by',
            'trx_analisis_desain.is_approve_penyetuju',
            'trx_analisis_desain.approve_by_penyetuju',
            'trx_analisis_desain.nik_pemohon',
            'trx_analisis_desain.nik_penyetuju',
            'trx_analisis_desain.lampiran_1',
            'trx_analisis_desain.lampiran_2',
            'trx_analisis_desain.lampiran_3',
            'trx_analisis_desain.lampiran_4',
        );
        if (auth()->user()->level != 1) {
            $trx_analisis_desain = $trx_analisis_desain
                ->where('trx_analisis_desain.nik_penyetuju', auth()->user()->nik)
                ->orWhere('trx_analisis_desain.created_by', auth()->user()->id);
        } 
        $trx_analisis_desain = $trx_analisis_desain
            ->orderByRaw("trx_analisis_desain.is_approve asc,trx_analisis_desain.approve_at desc, trx_analisis_desain.created_at desc")
            ->get();

        return datatables()
            ->of($trx_analisis_desain)
            ->addIndexColumn()
            ->addColumn('select_all', function ($trx_analisis_desain) {
                return '
                    <input type="checkbox" name="id_analisis_desain[]" value="'. $trx_analisis_desain->id_analisis_desain .'">
                ';
            })
            ->addColumn('aksi', function ($trx_analisis_desain) {
                // Cek apakah progress sudah 100% dan file PDF sudah terisi
                // $isApproved = $trx_analisis_desain->progress == 100 && !empty($trx_analisis_desain->file_pdf);
                // $isApproved = !empty($trx_analisis_desain->file_pdf);
                // $uploadPdf = $trx_analisis_desain->is_approve_penyetuju === 1;
                $isApproved = (int) $trx_analisis_desain->is_approve != 1 && empty($trx_analisis_desain->file_pdf);
                $isApproved2 = (int) $trx_analisis_desain->is_approve == 1 && empty($trx_analisis_desain->file_pdf);

                // Menentukan apakah pengguna telah menyetujui
                if(auth()->user()->level == 1){
                    $alreadyApproved = "";
                }
                
                if (auth()->user()->level == 2 || auth()->user()->level == 3) {
                    if (auth()->user()->nik == $trx_analisis_desain->nik_penyetuju) {
                        $alreadyApproved = $trx_analisis_desain->is_approve == 1;
                        if ($alreadyApproved) {
                            $approveButton = '<button type="button" class="btn btn-xs btn-success btn-flat" disabled><i class="fa fa-check"></i> Approved</button>';
                        } else {
                            $approveButton = '<button type="button" onclick="approveProyek(`'. route('analisis_desain.approveProyek', $trx_analisis_desain->id_analisis_desain) .'`)" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-check"></i> Approve</button>';
                        }
                    } else {
                        $alreadyApproved = false;
                        $approveButton = '';
                    }
                }else{
                    $alreadyApproved = '';
                    $approveButton = '';
                }
                
                // if ((int) $trx_analisis_desain->is_approve != 1) {
                //     $alreadyApproved = "";
                // }
                
                // Menentukan tombol approve
                // $approveButton = $alreadyApproved
                //     ? '<button type="button" class="btn btn-xs btn-success btn-flat" disabled><i class="fa fa-check"></i> Approved</button>'
                //     : ($isApproved 
                //         ? '<button type="button" onclick="approveProyek(`'. route('analisis_desain.approveProyek', $trx_analisis_desain->id_analisis_desain) .'`)" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-check"></i> Approve</button>'
                //         : '');

                // var_dump($trx_analisis_desain->is_approve, $trx_analisis_desain->is_approve_jabatan,auth()->user()->id);
                
                // Kondisi untuk tombol upload PDF
                $uploadButton = ($trx_analisis_desain->is_approve == 1 && auth()->user()->id == 1)
                                ? '<button onclick="UploadPDF(`'. route('analisis_desain.updatePDF', $trx_analisis_desain->id_analisis_desain) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-upload"></i></button>'
                                : '';
                
                // Membuat grup tombol
                return '
                        <div class="btn-group">
                            ' . (auth()->user()->level == 1 || auth()->user()->level == 5? '
                                <button type="button" onclick="deleteData(`' . route('analisis_desain.destroy', $trx_analisis_desain->id_analisis_desain) . '`)" class="btn btn-xs btn-danger btn-flat">
                                    <i class="fa fa-trash"></i>
                                </button>
                                <button type="button" onclick="editForm(`' . route('analisis_desain.update', $trx_analisis_desain->id_analisis_desain) . '`)" class="btn btn-xs btn-info btn-flat">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                    ' . ($isApproved ? '
                                    ': '') . '
                                    ' . ($isApproved2 ? $uploadButton : '') . '
                            ' : '') . '
                            <button onclick="cetakDokumen(`' . route('analisis_desain.cetakDokumen', $trx_analisis_desain->id_analisis_desain) . '`)" class="btn btn-info btn-xs btn-flat">
                                <i class="fa fa-download"></i> Cetak Dokumen
                            </button>
                            <button type="button" onclick="viewForm(`' . route('analisis_desain.view', $trx_analisis_desain->id_analisis_desain) . '`)" class="btn btn-xs btn-primary btn-flat">
                                <i class="fa fa-eye"></i>
                            </button>
                            ' . (auth()->user()->level == 2 || auth()->user()->level == 3 || auth()->user()->level == 4 ? $approveButton : '') . '
                        </div>
                       ';                  

                // return '
                // <div class="btn-group">
                //     <button onclick="deleteData(`'. route('analisis_desain.destroy', $trx_analisis_desain->id_analisis_desain) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                //     ' . (!$isApproved ? '
                //     <button onclick="editForm(`'. route('analisis_desain.update', $trx_analisis_desain->id_analisis_desain) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                //     <button onclick="UploadPDF(`'. route('analisis_desain.updatePDF', $trx_analisis_desain->id_analisis_desain) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-upload"></i></button>
                //     <button onclick="updateProgressForm(`'. route('analisis_desain.editProgress', $trx_analisis_desain->id_analisis_desain) .'`)" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-tasks"></i> Update Progress</button>
                //     ' : '') . '
                //     '. $approveButton .'
                //     <button onclick="cetakDokumen(`'.route('analisis_desain.cetakDokumen', $trx_analisis_desain->id_analisis_desain) .'`)" class="btn btn-info btn-xs btn-flat">
                //         <i class="fa fa-download"></i> Cetak Dokumen
                //     </button>
                //     <button onclick="viewForm(`'. route('analisis_desain.view', $trx_analisis_desain->id_analisis_desain) .'`)" class="btn btn-xs btn-primary btn-flat"><i class="fa fa-eye"></i></button>
                // </div>
                // ';
            })
            ->addColumn('approval_status', function ($trx_analisis_desain) {
                if ($trx_analisis_desain->is_approve === 1 && $trx_analisis_desain->file_pdf != null) {
                    return '<b>File Dokumen Akhir sudah diupload</b>';
                } elseif ($trx_analisis_desain->is_approve === 1) {
                    return '<b>Sudah di Approve</b>';
                } elseif ($trx_analisis_desain->is_approve !== 1) {
                    return '<b>Menunggu Approval Penyetuju</b>';
                } else {
                    return '';
                }
            })
            ->addColumn('file_pdf', function ($trx_analisis_desain) {
                if ($trx_analisis_desain->file_pdf) {
                    return '<a href="/storage/assets/pdf/' . $trx_analisis_desain->file_pdf . '" target="_blank" class="btn btn-warning btn-sm">Lihat PDF</a>';
                }
                return '-';
            })
            ->editColumn('deskripsi_proyek', function($row) {
                return $row->deskripsi_proyek; // Data dengan HTML
            })
            ->editColumn('kebutuhan_fungsional', function($row) {
                return $row->kebutuhan_fungsional; // Data dengan HTML
            })
            ->editColumn('gambaran_arsitektur', function($row) {
                return $row->gambaran_arsitektur; // Data dengan HTML
            })
            ->editColumn('detil_arsitektur', function($row) {
                return $row->detil_arsitektur; // Data dengan HTML
            })
            ->editColumn('lampiran_mockup', function($row) {
                return $row->lampiran_mockup; // Data dengan HTML
            })
            ->addColumn('nomor_dokumen', function ($trx_analisis_desain) {
                if ($trx_analisis_desain->file_pdf_permintaan) {
                    return '<a href="/storage/assets/pdf/' . $trx_analisis_desain->file_pdf_permintaan . '" target="_blank">' . $trx_analisis_desain->nomor_dokumen . '</a>';
                }else{
                    return $trx_analisis_desain->nomor_dokumen;
                }
                return '-';
            })
            ->addColumn('nomor_proyek', function ($trx_analisis_desain) {
                if ($trx_analisis_desain->file_pdf_proyek) {
                    return '<a href="/storage/assets/pdf/' . $trx_analisis_desain->file_pdf_proyek . '" target="_blank">' . $trx_analisis_desain->nomor_proyek . '</a>';
                }
                return '-';
            })
            ->rawColumns(['aksi', 'select_all','file_pdf','approval_status', 'deskripsi_proyek', 'kebutuhan_fungsional', 'gambaran_arsitektur', 'detil_arsitektur', 'lampiran_mockup','nomor_dokumen','nomor_proyek'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $nomor_dokumen = DB::table('trx_permintaan_pengembangan as tp')
        ->leftJoin('trx_persetujuan_pengembangan as ts', 'tp.id_permintaan_pengembangan', '=', 'ts.id_permintaan_pengembangan')
        ->where('tp.id_permintaan_pengembangan', $request->id_permintaan_pengembangan) 
        ->select('tp.nomor_dokumen')
        ->first();
        
        // Cek Validasi Sudah Pengisian Tahap Sebelumnya atau Belum
        $sql_validasi = "SELECT 
                            flag_status.id_permintaan, 
                            tpp.nomor_dokumen, 
                            tpp.latar_belakang, 
                            tpp.tujuan, 
                            MAX(flag_status.flag) AS max_flag
                        FROM 
                            flag_status
                        LEFT JOIN trx_permintaan_pengembangan AS tpp ON tpp.id_permintaan_pengembangan 
                            = flag_status.id_permintaan
                        LEFT JOIN trx_persetujuan_pengembangan AS tpp2 ON tpp2.id_permintaan_pengembangan 
                                = tpp.id_permintaan_pengembangan
                        LEFT JOIN trx_perencanaan_kebutuhan AS tpk ON tpk.id_persetujuan_pengembangan 
                                = tpp2.id_persetujuan_pengembangan
                        -- WHERE id_permintaan = $request->id_permintaan_pengembangan AND tpk.progress = 100 AND tpk.is_approve = 1
                        WHERE id_permintaan = $request->id_permintaan_pengembangan AND tpk.is_approve = 1
                        GROUP BY 
                            flag_status.id_permintaan, 
                            tpp.nomor_dokumen, 
                            tpp.latar_belakang, 
                            tpp.tujuan
                        HAVING 
                            MAX(flag_status.flag) = 4;
                        ";

        if ($request->hasFile('lampiran_1')) {
            $file = $request->file('lampiran_1');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->move(public_path('storage/assets/lampiran/analisisdesain'), $filename);
            $data['lampiran_1'] = $filename;
        } else {
            $data['lampiran_1'] = null;
        }

        if ($request->hasFile('lampiran_2')) {
            $file = $request->file('lampiran_2');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->move(public_path('storage/assets/lampiran/analisisdesain'), $filename);
            $data['lampiran_2'] = $filename;
        } else {
            $data['lampiran_2'] = null;
        }

        if ($request->hasFile('lampiran_3')) {
            $file = $request->file('lampiran_3');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->move(public_path('storage/assets/lampiran/analisisdesain'), $filename);
            $data['lampiran_3'] = $filename;
        } else {
            $data['lampiran_3'] = null;
        }
        if ($request->hasFile('lampiran_4')) {
            $file = $request->file('lampiran_4');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->move(public_path('storage/assets/lampiran/analisisdesain'), $filename);
            $data['lampiran_4'] = $filename;
        } else {
            $data['lampiran_4'] = null;
        }
        
        if ($request->hasFile('file_pdf')) {
            $file = $request->file('file_pdf');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->move(public_path('storage/assets/pdf'), $filename);
            $data['file_pdf'] = $filename;
        }

        $data['tanggal_disiapkan'] = now();
        $data['created_by'] = auth()->user()->id;

        
        // Content for the QR code
        $qrContent ='Analisis Desain, Pemohon :' . $request->nama_pemohon . 
                    ', Nomor Dokumen : ' . $nomor_dokumen->nomor_dokumen .
                    ', NIK Karyawan : ' . $request->nik_pemohon . 
                    ', Tanggal Disetujui : ' . now(); 
    
        // Sanitize nomor_dokumen to remove slashes and create a valid filename
        $safeNomorDokumen = str_replace('/', '', $nomor_dokumen->nomor_dokumen);  // Remove slashes
        $fileName = '' . 'qr_code_analisisdesain_pemohon_'. $safeNomorDokumen . '.png';
        $filePath = 'storage/assets/qrcode/' . $fileName;

        // Create QR code instance with additional settings
        $qrCode = new QrCode(
            data: $qrContent,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,  // Set to 'Medium' error correction
            size: 500,  // Set size to 500px
            margin: 10, // Set margin
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),  // Black foreground color
            backgroundColor: new Color(255, 255, 255) // White background color
        );

        // Write the QR code with label (no logo here)
        $writer = new PngWriter();

        // Generate the image content of the QR code and save to file
        $result = $writer->write($qrCode, null, null);  // No logo, just label

        // Save the QR code image to the specified file path
        $filePathWithPublicPath = public_path($filePath);  // Full file path with public_path
        file_put_contents($filePathWithPublicPath, $result->getString());  // Save the file content

        // Store the path to the database (e.g., store in 'path_qrcode' column)
        $data['path_qrcode_pemohon'] = $filePath;

        // $result = DB::select($sql_validasi);

        // if (count($result) > 0) {
            $trx_analisis_desain = AnalisisDesain::create($data);
            $id_permintaan_pengembangan = $trx_analisis_desain->id_permintaan_pengembangan;
            $lastId = $trx_analisis_desain->id_analisis_desain;

            FlagStatus::create([
                'kat_modul' => 5,
                'id_permintaan' => $id_permintaan_pengembangan,
                'nama_modul' => "Analisis Desain",
                'id_tabel' => $lastId,
                'flag' => 5
            ]);

            return response()->json('Data berhasil disimpan', 200);

        // }else{
        //     echo "Tambah Data Gagal, Karena Anda Melewati Tahapan Sebelumnya";
        //     die;
        // }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id_analisis_desain)
    {
        $trx_analisis_desain = AnalisisDesain::find($id_analisis_desain);

        return response()->json($trx_analisis_desain);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id_analisis_desain)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_analisis_desain)
    {
        // Ambil data yang sudah ada untuk analisis desain yang ingin diupdate
        $trx_analisis_desain = AnalisisDesain::find($id_analisis_desain);

        // Simpan data lainnya yang tidak terkait dengan file
        $data = $request->except(['lampiran_1', 'lampiran_2', 'lampiran_3', 'lampiran_4', 'file_pdf']);  // Semua data kecuali file

        // Proses file lampiran_1 jika ada
        if ($request->hasFile('lampiran_1')) {
            $file = $request->file('lampiran_1');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->move(public_path('storage/assets/lampiran/analisisdesain'), $filename);
            $data['lampiran_1'] = $filename;
        } else {
            // Jika tidak ada file baru, biarkan data yang lama tetap ada
            if (!$request->has('lampiran_1')) {
                $data['lampiran_1'] = $trx_analisis_desain->lampiran_1;
            }
        }

        // Proses file lampiran_2 jika ada
        if ($request->hasFile('lampiran_2')) {
            $file = $request->file('lampiran_2');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->move(public_path('storage/assets/lampiran/analisisdesain'), $filename);
            $data['lampiran_2'] = $filename;
        } else {
            // Jika tidak ada file baru, biarkan data yang lama tetap ada
            if (!$request->has('lampiran_2')) {
                $data['lampiran_2'] = $trx_analisis_desain->lampiran_2;
            }
        }

        // Proses file lampiran_3 jika ada
        if ($request->hasFile('lampiran_3')) {
            $file = $request->file('lampiran_3');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->move(public_path('storage/assets/lampiran/analisisdesain'), $filename);
            $data['lampiran_3'] = $filename;
        } else {
            // Jika tidak ada file baru, biarkan data yang lama tetap ada
            if (!$request->has('lampiran_3')) {
                $data['lampiran_3'] = $trx_analisis_desain->lampiran_3;
            }
        }

        // Proses file lampiran_4 jika ada
        if ($request->hasFile('lampiran_4')) {
            $file = $request->file('lampiran_4');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->move(public_path('storage/assets/lampiran/analisisdesain'), $filename);
            $data['lampiran_4'] = $filename;
        } else {
            // Jika tidak ada file baru, biarkan data yang lama tetap ada
            if (!$request->has('lampiran_4')) {
                $data['lampiran_4'] = $trx_analisis_desain->lampiran_4;
            }
        }

        // Proses file PDF jika ada
        if ($request->hasFile('file_pdf')) {
            $file = $request->file('file_pdf');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->move(public_path('storage/assets/pdf'), $filename);
            $data['file_pdf'] = $filename;
        } else {
            // Jika tidak ada file baru, biarkan data yang lama tetap ada
            if (!$request->has('file_pdf')) {
                $data['file_pdf'] = $trx_analisis_desain->file_pdf;
            }
        }

        // Perbarui data di database
        $trx_analisis_desain->update($data);

        return response()->json('Data berhasil diperbarui', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_analisis_desain)
    {
        $trx_analisis_desain = AnalisisDesain::find($id_analisis_desain);
        $trx_analisis_desain->delete();

        return response(null, 204);
    }

    public function cetakDokumen(Request $request)
    {
        set_time_limit(300);

        // $dataanalisis = AnalisisDesain::whereIn('id_analisis_desain', $request->id_analisis_desain)->get();

        $idPerencanaanKebutuhan = $request->query();
        $id_analisis_desain = key($idPerencanaanKebutuhan); 
        $id_analisis_desain = (int) $id_analisis_desain;

        // $dataanalisis = AnalisisDesain::whereIn('id_analisis_desain', [$id_analisis_desain])->get();

        $dataanalisis = AnalisisDesain::leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'trx_analisis_desain.id_permintaan_pengembangan')
        ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'trx_analisis_desain.id_permintaan_pengembangan')
        ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
        ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
        ->select(
            'tpp.nomor_dokumen',
            'tpp3.nomor_proyek',
            'tpp3.nama_proyek',
            'tpp3.manajer_proyek',
            'tpk.kebutuhan_fungsional',
            'trx_analisis_desain.gambaran_arsitektur',
            'trx_analisis_desain.detil_arsitektur',
            'trx_analisis_desain.lampiran_mockup',            
            'trx_analisis_desain.nama_pemohon',
            'trx_analisis_desain.jabatan_pemohon',
            'trx_analisis_desain.tanggal_disiapkan',
            'trx_analisis_desain.nama_penyetuju',
            'trx_analisis_desain.jabatan_penyetuju',
            'trx_analisis_desain.tanggal_disetujui',
            'trx_analisis_desain.path_qrcode_pemohon',
            'trx_analisis_desain.path_qrcode_penyetuju',
        )
        ->whereIn('trx_analisis_desain.id_analisis_desain', [$id_analisis_desain])
        ->get();

        $no  = 1;
        $pdf = PDF::loadView('analisis_desain.dokumen', compact('dataanalisis', 'no'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('analisis.pdf');
    }

    public function deleteSelected(Request $request)
    {
        $ids = $request->id_analisis_desain;
        AnalisisDesain::whereIn('id_analisis_desain', $ids)->delete();
        return response()->json('Data berhasil dihapus', 200);
    }

    public function view($id)
    {
        $trx_analisis_desain = AnalisisDesain::leftJoin('trx_permintaan_pengembangan', 'trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_analisis_desain.id_permintaan_pengembangan')
        ->leftJoin('trx_persetujuan_pengembangan', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan', '=', 'trx_permintaan_pengembangan.id_permintaan_pengembangan')
        ->select(
            'trx_analisis_desain.nama_pemohon',
            'trx_analisis_desain.jabatan_pemohon',
            'trx_analisis_desain.tanggal_disiapkan',
            'trx_analisis_desain.nama_penyetuju',
            'trx_analisis_desain.jabatan_penyetuju',
            'trx_analisis_desain.tanggal_disetujui',
        )
        ->where('trx_analisis_desain.id_analisis_desain', $id) // Menggunakan kondisi where pada id_perencanaan_proyek
        ->first(); // Mengambil satu hasil pertama dari query

        return response()->json($trx_analisis_desain);
    }

    public function updatePDF(Request $request, $id_analisis_desain)
    {
        // Temukan data berdasarkan ID
        $trx_analisis_desain = AnalisisDesain::findOrFail($id_analisis_desain);

        // Validasi input file
        $request->validate([
            'file_pdf' => 'required|file|mimes:pdf|max:2048', // Aturan validasi untuk file PDF
        ]);

        // Periksa apakah ada file PDF yang diupload
        if ($request->hasFile('file_pdf')) {
            $file = $request->file('file_pdf');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->move(public_path('storage/assets/pdf'), $filename);

            // Simpan nama file baru ke kolom `file_pdf`
            $trx_analisis_desain->file_pdf = $filename;

            // Update data di database
            $trx_analisis_desain->save();

            return response()->json('File PDF berhasil diperbarui', 200);
        }

        return response()->json('Tidak ada file yang diupload', 400);
    }

    // For Update Progress Project
    public function editProgress($id)
    {
        $trx_analisis_desain = AnalisisDesain::leftJoin('trx_permintaan_pengembangan', 'trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_analisis_desain.id_permintaan_pengembangan')
            ->leftJoin('trx_persetujuan_pengembangan', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan', '=', 'trx_permintaan_pengembangan.id_permintaan_pengembangan')
            ->select(
                'trx_analisis_desain.id_analisis_desain', 
                'trx_permintaan_pengembangan.nomor_dokumen', 
                'trx_perencanaan_proyek.nama_proyek', 
 
                'trx_perencanaan_proyek.manajer_proyek',
                'trx_analisis_desain.kebutuhan_fungsi',
                'trx_analisis_desain.gambaran_arsitektur',
                'trx_analisis_desain.detil_arsitektur',
                'trx_analisis_desain.lampiran_mockup',
                'trx_analisis_desain.nama_pemohon',
                'trx_analisis_desain.jabatan_pemohon',
                'trx_analisis_desain.tanggal_disiapkan',
                'trx_analisis_desain.nama_penyetuju',
                'trx_analisis_desain.jabatan_penyetuju',
                'trx_analisis_desain.tanggal_disetujui',
                'trx_analisis_desain.status',
                'trx_analisis_desain.progress',
                'trx_analisis_desain.file_pdf',
            )
            ->where('trx_analisis_desain.id_analisis_desain', $id)
            ->first();

        // Kirim data ke response JSON
        return response()->json($trx_analisis_desain);
    }

    public function updateProgress(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'progress' => 'required|integer|min:0|max:100', // Validasi progress
            'nomor_dokumen' => 'required|string|max:255', // Validasi nomor dokumen
        ]);

        // Cari data permintaan pengembangan berdasarkan ID
        $trx_analisis_desain = AnalisisDesain::findOrFail($id);

        // Update progress
        $trx_analisis_desain->progress = $request->progress; // Pastikan ada kolom 'progress' di tabel
        $trx_analisis_desain->save(); // Simpan perubahan

        // Kembali dengan respon sukses
        return redirect()->route('analisis_desain.index');
    }

    // Method untuk melakukan approve proyek
    public function approveProyek($id)
    {
        // Ambil data proyek berdasarkan id
        $proyek = AnalisisDesain::leftJoin('trx_permintaan_pengembangan', 'trx_analisis_desain.id_permintaan_pengembangan', '=', 'trx_permintaan_pengembangan.id_permintaan_pengembangan')
        ->where('trx_analisis_desain.id_analisis_desain', $id)  // Sesuaikan dengan parameter ID yang Anda inginkan
        ->select('trx_analisis_desain.*', 'trx_permintaan_pengembangan.nomor_dokumen')  // Memilih semua kolom dari kedua tabel
        ->first();  // Ambil data pertama (karena kita menggunakan where ID)

        // Cek apakah progress sudah 100% dan file_pdf sudah terisi
        // if (!empty($proyek->file_pdf)) {
        // if ($proyek->progress == 100 && !empty($proyek->file_pdf)) {
            // Update status proyek menjadi approved (Anda dapat menambah field status_approval di tabel)
            // if(auth()->user()->level == 2){
            //     $proyek->is_approve = 1;
            //     $proyek->approve_at = now(); // Set tanggal persetujuan saat ini
            //     $proyek->approve_by = auth()->user()->id; // Set tanggal persetujuan saat ini
            //     $proyek->save();
    
            //     return response()->json(['success' => 'Proyek berhasil di-approve.']);
            // }else if(auth()->user()->level == 3){
            //     $proyek->is_approve_penyetuju = 1;
            //     $proyek->approve_at_penyetuju = now(); // Set tanggal persetujuan saat ini
            //     $proyek->approve_by_penyetuju = auth()->user()->id; // Set tanggal persetujuan saat ini
            //     $proyek->save();
    
            //     return response()->json(['success' => 'Proyek berhasil di-approve.']);
            // }
        // }
        $proyek->is_approve = 1;
        $proyek->approve_at = now(); // Set tanggal persetujuan saat ini
        $proyek->approve_by = auth()->user()->id; // Set tanggal persetujuan saat ini
        $proyek->tanggal_disetujui = now(); // Set tanggal persetujuan saat ini
                    // Content for the QR code
                    $qrContent ='Analisis Desain, Penyetuju :' . auth()->user()->name . 
                    ', Nomor Dokumen : ' . $proyek->nomor_dokumen .
                    ', NIK Karyawan : ' . $proyek->nik_penyetuju . 
                    ', Tanggal Disetujui : ' . now(); 
        
        // Sanitize nomor_dokumen to remove slashes and create a valid filename
        $safeNomorDokumen = str_replace('/', '', $proyek->nomor_dokumen);  // Remove slashes
        $fileName = '' . 'qr_code_analisisdesain_penyetuju_' . $proyek->id_permintaan_pengembangan . '_' . $safeNomorDokumen . '.png';
        $filePath = 'storage/assets/qrcode/' . $fileName;

        // Create QR code instance with additional settings
        $qrCode = new QrCode(
            data: $qrContent,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,  // Set to 'Medium' error correction
            size: 500,  // Set size to 500px
            margin: 10, // Set margin
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),  // Black foreground color
            backgroundColor: new Color(255, 255, 255) // White background color
        );

        // Write the QR code with label (no logo here)
        $writer = new PngWriter();

        // Generate the image content of the QR code and save to file
        $result = $writer->write($qrCode, null, null);  // No logo, just label

        // Save the QR code image to the specified file path
        $filePathWithPublicPath = public_path($filePath);  // Full file path with public_path
        file_put_contents($filePathWithPublicPath, $result->getString());  // Save the file content

        // Store the path to the database (e.g., store in 'path_qrcode' column)
        $proyek->path_qrcode_penyetuju = $filePath;      
        $proyek->save();

        return response()->json(['success' => 'Proyek berhasil di-approve.']);
        // Jika belum memenuhi syarat approval, kembalikan pesan error
        return response()->json(['error' => 'Proyek belum memenuhi syarat untuk di-approve.'], 400);
    }
}
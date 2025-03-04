<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\PersetujuanPengembangan;
use App\Models\PerencanaanKebutuhan;
use App\Models\FlagStatus;
use App\Models\KriteriaAplikasi;
use App\Models\KlasifikasiModelBackup;
use App\Models\TipeResourceServer;
use App\Models\TipeServer;
use App\Models\TipeDatabase;
use App\Models\TipeStorage;
use Illuminate\Support\Facades\DB;
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

class PerencanaanKebutuhanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $nama_proyek_terpakai         = PerencanaanKebutuhan::pluck('id_persetujuan_pengembangan')->toArray();
        $trx_persetujuan_pengembangan = PersetujuanPengembangan::whereNotIn('trx_persetujuan_pengembangan.id_persetujuan_pengembangan', $nama_proyek_terpakai)
        ->join('trx_permintaan_pengembangan', 'trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan')
        ->whereNotNull('trx_permintaan_pengembangan.nomor_dokumen')  // Memastikan nomor dokumen tidak null
        ->whereIn('trx_permintaan_pengembangan.id_permintaan_pengembangan', function ($query) {
            $query->select('id_permintaan_pengembangan')
                ->from('trx_permintaan_pengembangan')
                ->whereNotNull('nomor_dokumen');  // Pastikan hanya yang memiliki nomor dokumen
        })
        ->when(auth()->user()->level != 1, function ($query) {
            // Jika level user adalah 1, tambahkan filter berdasarkan level
            return $query->where('trx_permintaan_pengembangan.created_by', auth()->id());
        })
        ->select(
            'trx_permintaan_pengembangan.nomor_dokumen', 
            'trx_permintaan_pengembangan.judul', 
            'trx_persetujuan_pengembangan.id_persetujuan_pengembangan'
        ) // Pilih kolom yang diperlukan
        ->get(); // Mengambil data dalam bentuk koleksi

        $kriteria_aplikasi = KriteriaAplikasi::all();
        $model_backup = KlasifikasiModelBackup::all();
        $resource_server = TipeResourceServer::all();
        $tipe_server = TipeServer::all();
        $tipe_database = TipeDatabase::all();
        $tipe_storage = TipeStorage::all();

        return view('perencanaan_kebutuhan.index',compact('trx_persetujuan_pengembangan', 'kriteria_aplikasi', 'model_backup', 'resource_server', 'tipe_server', 'tipe_database', 'tipe_storage'));
    }

    public function data()
    {
        // $trx_perencanaan_kebutuhan = PersetujuanPengembangan::leftJoin('trx_perencanaan_kebutuhan', 'trx_perencanaan_kebutuhan.id_persetujuan_pengembangan', '=', 'trx_persetujuan_pengembangan.id_persetujuan_pengembangan')
        // ->leftJoin('trx_perencanaan_proyek', 'trx_perencanaan_kebutuhan.id_perencanaan_proyek', '=', 'trx_perencanaan_proyek.id_perencanaan_proyek')
        // ->select('persetujuan_pengembangan.*, trx_perencanaan_proyek.*, trx_perencanaan_kebutuhan.*')
        // ->get();
        $trx_perencanaan_kebutuhan = PerencanaanKebutuhan::leftJoin('trx_persetujuan_pengembangan', 'trx_persetujuan_pengembangan.id_persetujuan_pengembangan', '=', 'trx_perencanaan_kebutuhan.id_persetujuan_pengembangan')
        ->join('trx_perencanaan_proyek', 'trx_perencanaan_proyek.id_persetujuan_pengembangan', '=', 'trx_perencanaan_kebutuhan.id_persetujuan_pengembangan')
        ->leftJoin('trx_permintaan_pengembangan', 'trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan')
        // ->join('trx_persetujuan_pengembangan', function ($join) {
        //     $join->on('trx_persetujuan_pengembangan.id_persetujuan_pengembangan', '=', 'trx_perencanaan_kebutuhan.id_persetujuan_pengembangan')
        //     ->orOn('trx_persetujuan_pengembangan.id_perencanaan_proyek', '=', 'trx_perencanaan_kebutuhan.id_perencanaan_proyek');
        // })
        ->select('trx_perencanaan_kebutuhan.progress', 'trx_perencanaan_kebutuhan.id_perencanaan_kebutuhan', 
        'trx_perencanaan_kebutuhan.is_approve', 'trx_perencanaan_kebutuhan.kebutuhan_fungsional', 
        'trx_perencanaan_kebutuhan.kebutuhan_nonfungsional', 'trx_perencanaan_kebutuhan.lampiran', 
        'trx_perencanaan_kebutuhan.nama_pemohon', 'trx_perencanaan_kebutuhan.jabatan_pemohon', 
        'trx_perencanaan_kebutuhan.tanggal_disiapkan', 'trx_perencanaan_kebutuhan.nama_penyetuju', 
        'trx_perencanaan_kebutuhan.nama_pemverifikasi', 'trx_perencanaan_kebutuhan.jabatan_pemverifikasi', 
        'trx_perencanaan_kebutuhan.tanggal_verifikasi',
        'trx_perencanaan_kebutuhan.jabatan_penyetuju', 'trx_perencanaan_kebutuhan.tanggal_disetujui', 
        'trx_perencanaan_kebutuhan.file_pdf', 'trx_perencanaan_kebutuhan.stakeholders', 'trx_perencanaan_proyek.nomor_proyek', 
        'trx_perencanaan_proyek.pemilik_proyek', 'trx_perencanaan_proyek.manajer_proyek', 'trx_perencanaan_proyek.nama_proyek', 
        'trx_perencanaan_kebutuhan.is_approve', 'trx_perencanaan_kebutuhan.is_approve_pemverifikasi', 'trx_perencanaan_kebutuhan.nik_pemverifikasi', 'trx_perencanaan_kebutuhan.nik_penyetuju', 'trx_perencanaan_kebutuhan.nik_pemohon',  'trx_permintaan_pengembangan.nomor_dokumen',
        'trx_permintaan_pengembangan.nomor_dokumen', 'trx_permintaan_pengembangan.file_pdf as file_pdf_permintaan',
        'trx_perencanaan_proyek.nomor_proyek', 'trx_perencanaan_proyek.file_pdf as file_pdf_proyek',
        );
        if (auth()->user()->level != 1) {
            $trx_perencanaan_kebutuhan = $trx_perencanaan_kebutuhan
            ->where('trx_perencanaan_kebutuhan.nik_penyetuju', auth()->user()->nik)
            ->orWhere('trx_perencanaan_kebutuhan.nik_pemverifikasi', auth()->user()->nik)
            ->orWhere('trx_perencanaan_kebutuhan.created_by', auth()->user()->id);
        }
        $trx_perencanaan_kebutuhan = $trx_perencanaan_kebutuhan
        ->orderByRaw("trx_perencanaan_kebutuhan.is_approve asc,trx_perencanaan_kebutuhan.approve_at desc, trx_perencanaan_kebutuhan.created_at desc")
        ->get();

        return datatables()
            ->of($trx_perencanaan_kebutuhan)
            ->addIndexColumn()
            ->addColumn('select_all', function ($trx_perencanaan_kebutuhan) {
                return '
                    <input type="checkbox" name="id_perencanaan_kebutuhan[]" value="'. $trx_perencanaan_kebutuhan->id_perencanaan_kebutuhan .'">
                ';
            })
            ->addColumn('deskripsi', function($trx_perencanaan_kebutuhan){
                return $trx_perencanaan_kebutuhan->deskripsi;
            })
            ->addColumn('pemilik_proyek', function($trx_perencanaan_kebutuhan){
                return $trx_perencanaan_kebutuhan->pemilik_proyek;
            })
            ->addColumn('manajer_proyek', function($trx_perencanaan_kebutuhan){
                return $trx_perencanaan_kebutuhan->manajer_proyek;
            })
            ->addColumn('aksi', function ($trx_perencanaan_kebutuhan) {
                $id_proyek = $trx_perencanaan_kebutuhan->id_perencanaan_kebutuhan;
                $isApproved = (int) $trx_perencanaan_kebutuhan->is_approve_pemverifikasi != 1 && empty($trx_perencanaan_kebutuhan->file_pdf);
                $isApproved2 = (int) $trx_perencanaan_kebutuhan->is_approve_pemverifikasi == 1 && empty($trx_perencanaan_kebutuhan->file_pdf);
                // Cek apakah progress sudah 100% dan file PDF sudah terisi
                // $isApproved = $trx_perencanaan_kebutuhan->progress == 100 && !empty($trx_perencanaan_kebutuhan->file_pdf);
                // $isApproved = !empty($trx_perencanaan_kebutuhan->file_pdf) ;
                // $alreadyApproved = (int) $trx_perencanaan_kebutuhan->is_approve === 1; // Tambahkan kondisi untuk status approval
                
                // Menentukan apakah pengguna telah menyetujui
                if(auth()->user()->level == 1){
                    $alreadyApproved = "";
                }
                
                if (auth()->user()->level != 1) {
                    if (auth()->user()->nik == $trx_perencanaan_kebutuhan->nik_pemverifikasi) {
                        if ($trx_perencanaan_kebutuhan->is_approve_pemverifikasi == 1) {
                            $approveButton = '<button type="button" class="btn btn-xs btn-success btn-flat" disabled><i class="fa fa-check"></i> Approved</button>';
                        }else{
                            $approveButton = '<button type="button" onclick="approveProyek(`'. route('perencanaan_kebutuhan.approveProyek', $id_proyek) .'`)" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-check"></i> Approve</button>';
                        }
                    }
                    elseif (auth()->user()->nik == $trx_perencanaan_kebutuhan->nik_penyetuju) {
                        if ($trx_perencanaan_kebutuhan->is_approve != 1) {
                            $approveButton = '<button type="button" onclick="approveProyek(`'. route('perencanaan_kebutuhan.approveProyek', $id_proyek) .'`)" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-check"></i> Approve</button>';
                        } else {
                            $approveButton = '<button type="button" class="btn btn-xs btn-success btn-flat" disabled><i class="fa fa-check"></i> Approved</button>';
                        }
                    } else {
                        $approveButton = '';
                    }
                } else {
                    $approveButton = '';
                }    
            
                // $approveButton = is_null($trx_perencanaan_kebutuhan->approve_by)
                //                 ? ($isApproved 
                //                     ? '<button type="button" class="btn btn-xs btn-success btn-flat" disabled><i class="fa fa-check"></i> Approved</button>'
                //                     : '<button type="button" onclick="approveProyek(`'. route('perencanaan_kebutuhan.approveProyek', $id_proyek) .'`)" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-check"></i> Approve</button>') // Jika tidak memenuhi syarat, tampilkan Approved
                //                 : '<button type="button" class="btn btn-xs btn-success btn-flat" disabled><i class="fa fa-check"></i> Approved</button>'; // Jika approve_by terisi

                $uploadButton = ($trx_perencanaan_kebutuhan->is_approve == 1 && auth()->user()->id == 1)
                                ? '<button onclick="UploadPDF(`'. route('perencanaan_kebutuhan.updatePDF', $trx_perencanaan_kebutuhan->id_perencanaan_kebutuhan) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-upload"></i></button>'
                                : '';

                return '
                        <div class="btn-group">
                            ' . (auth()->user()->level == 1 || auth()->user()->level == 5? '
                                <button type="button" onclick="deleteData(`' . route('perencanaan_kebutuhan.destroy', $id_proyek) . '`)" class="btn btn-xs btn-danger btn-flat">
                                    <i class="fa fa-trash"></i>
                                </button>
                                    <button type="button" onclick="editForm(`' . route('perencanaan_kebutuhan.update', $id_proyek) . '`)" class="btn btn-xs btn-info btn-flat">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    ' . ($isApproved ? '
                                    ': '') . '
                                    ' . ($isApproved2 ? $uploadButton : '') . '
                            ' : '') . '
                            <button onclick="cetakDokumen(`' . route('perencanaan_kebutuhan.cetakDokumen', $id_proyek) . '`)" class="btn btn-info btn-xs btn-flat">
                                <i class="fa fa-download"></i> Cetak Dokumen
                            </button>
                            <button type="button" onclick="viewForm(`' . route('perencanaan_kebutuhan.view', $id_proyek) . '`)" class="btn btn-xs btn-primary btn-flat">
                                <i class="fa fa-eye"></i>
                            </button>
                            ' . (auth()->user()->level == 2 || auth()->user()->level == 3 || auth()->user()->level == 4 || auth()->user()->level == 5 ? $approveButton : '') . '
                        </div>
                       ';   

                // return '
                // <div class="btn-group">
                //     <button onclick="deleteData(`'. route('perencanaan_kebutuhan.destroy', $trx_perencanaan_kebutuhan->id_perencanaan_kebutuhan) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                //     ' . (!$isApproved ? '
                //     <button onclick="editForm(`'. route('perencanaan_kebutuhan.update', $trx_perencanaan_kebutuhan->id_perencanaan_kebutuhan) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                //     <button onclick="UploadPDF(`'. route('perencanaan_kebutuhan.updatePDF', $trx_perencanaan_kebutuhan->id_perencanaan_kebutuhan) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-upload"></i></button>
                //     <button onclick="updateProgressForm(`'. route('perencanaan_kebutuhan.editProgress', $trx_perencanaan_kebutuhan->id_perencanaan_kebutuhan) .'`)" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-tasks"></i> Update Progress</button>
                //     ' : '') . '
                //     '. $approveButton .'
                //     <button onclick="cetakDokumen(`'.route('perencanaan_kebutuhan.cetakDokumen', $trx_perencanaan_kebutuhan->id_perencanaan_kebutuhan) .'`)" class="btn btn-info btn-xs btn-flat">
                //         <i class="fa fa-download"></i> Cetak Dokumen
                //     </button>
                //     <button onclick="viewForm(`'. route('perencanaan_kebutuhan.view', $trx_perencanaan_kebutuhan->id_perencanaan_kebutuhan) .'`)" class="btn btn-xs btn-primary btn-flat"><i class="fa fa-eye"></i></button>
                // </div>
                // ';
            })
            ->addColumn('file_pdf', function ($trx_perencanaan_kebutuhan) {
                if ($trx_perencanaan_kebutuhan->file_pdf) {
                    return '<a href="/storage/assets/pdf/' . $trx_perencanaan_kebutuhan->file_pdf . '" target="_blank" class="btn btn-warning btn-sm">Lihat Dokumen</a>';
                }
                return '-';
            })
            ->addColumn('approval_status', function ($trx_perencanaan_kebutuhan) {
                if ($trx_perencanaan_kebutuhan->is_approve == 1 && $trx_perencanaan_kebutuhan->is_approve_pemverifikasi == 1 && $trx_perencanaan_kebutuhan->file_pdf != null) {
                    return '<b>File Dokumen Akhir sudah diupload</b>';
                } elseif ($trx_perencanaan_kebutuhan->is_approve == 1) {
                    return '<b>Sudah di Approve Penyetuju</b>';
                } elseif ($trx_perencanaan_kebutuhan->is_approve_pemverifikasi == 1) {
                    return '<b>Sudah di Approve Pemverifikator</b>';
                } elseif ($trx_perencanaan_kebutuhan->is_approve_pemverifikasi != 1) {
                    return '<b>Menunggu Approval Pemverifikator</b>';
                } elseif ($trx_perencanaan_kebutuhan->is_approve_pemverifikasi == 1 && $trx_perencanaan_kebutuhan->is_approve != 1) {
                    return '<b>Menunggu Approval Penyetuju</b>';
                }else {
                    return '';
                }   
            })
            ->addColumn('nomor_dokumen', function ($trx_perencanaan_proyek) {
                if ($trx_perencanaan_proyek->file_pdf_permintaan) {
                    return '<a href="/storage/assets/pdf/' . $trx_perencanaan_proyek->file_pdf_permintaan . '" target="_blank">' . $trx_perencanaan_proyek->nomor_dokumen . '</a>';
                }else{
                    return $trx_perencanaan_proyek->nomor_dokumen;
                }
                return '-';
            })
            ->addColumn('nomor_proyek', function ($trx_perencanaan_proyek) {
                if ($trx_perencanaan_proyek->file_pdf_proyek) {
                    return '<a href="/storage/assets/pdf/' . $trx_perencanaan_proyek->file_pdf_proyek . '" target="_blank">' . $trx_perencanaan_proyek->nomor_proyek . '</a>';
                }
                return '-';
            })
            ->editColumn('deskripsi', function($row) {
                return $row->deskripsi; // Data dengan HTML
            })
            ->editColumn('stakeholders', function($row) {
                return $row->stakeholders; // Data dengan HTML
            })
            ->editColumn('kebutuhan_fungsional', function($row) {
                return $row->kebutuhan_fungsional; // Data dengan HTML
            })
            ->editColumn('kebutuhan_nonfungsional', function($row) {
                return $row->kebutuhan_nonfungsional; // Data dengan HTML
            })
            ->rawColumns(['aksi', 'select_all','file_pdf','deskripsi', 'stakeholders', 'kebutuhan_fungsional', 'kebutuhan_nonfungsional','approval_status', 'nomor_dokumen', 'nomor_proyek'])
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

        $proyek = DB::table('trx_permintaan_pengembangan as tp')
        ->leftJoin('trx_persetujuan_pengembangan as ts', 'tp.id_permintaan_pengembangan', '=', 'ts.id_permintaan_pengembangan')
        ->where('ts.id_persetujuan_pengembangan', $request->id_persetujuan_pengembangan) 
        ->select('tp.nomor_dokumen', 'tp.id_permintaan_pengembangan', 'ts.id_persetujuan_pengembangan')
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
                        LEFT JOIN trx_perencanaan_proyek AS tpp3 ON tpp3.id_persetujuan_pengembangan = tpp2.id_persetujuan_pengembangan 
                        -- WHERE tpp2.id_persetujuan_pengembangan = $request->id_persetujuan_pengembangan AND tpp3.progress = 100 AND tpp3.is_approve = 1
                        WHERE tpp2.id_persetujuan_pengembangan = $request->id_persetujuan_pengembangan AND tpp3.is_approve = 1
                        GROUP BY 
                            flag_status.id_permintaan, 
                            tpp.nomor_dokumen, 
                            tpp.latar_belakang, 
                            tpp.tujuan
                        HAVING 
                            MAX(flag_status.flag) = 3;
                        ";
        
            // Content for the QR code
            $qrContent = 'Perencanaan Kebutuhan, Pemohon :' . $request->nama_pemohon . 
                         ', Nomor Dokumen : ' . $proyek->nomor_dokumen .
                         ', NIK Karyawan : ' . $request->nik_pemohon . 
                         ', Tanggal Disetujui : ' . now(); 

           // Sanitize nomor_dokumen to remove slashes and create a valid filename
           $safeNomorDokumen = str_replace('/', '', $proyek->nomor_dokumen);  // Remove slashes
           $fileName = '' . 'qr_code_perencanaankebutuhan_pemohon_' . $proyek->id_permintaan_pengembangan . '_' . $safeNomorDokumen . '.png';
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

        $result = DB::select($sql_validasi);

        if (count($result) > 0) {
            
            // Cek apakah ada file lampiran yang diupload
            if ($request->hasFile('lampiran')) {
                $file = $request->file('lampiran');
                $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('assets/lampiran', $filename, 'public');
                $data['lampiran'] = $filename;
            } else {
                // Jika tidak ada file yang diupload, set nilai menjadi null atau biarkan kosong
                $data['lampiran'] = null;
            }

            // Cek apakah ada file file_pdf yang diupload
            if ($request->hasFile('file_pdf')) {
                $file = $request->file('file_pdf');
                $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('assets/pdf', $filename, 'public');
                $data['file_pdf'] = $filename;
            } else {
                // Jika tidak ada file yang diupload, set nilai menjadi null atau biarkan kosong
                $data['file_pdf'] = null;
            }

            $data['tanggal_disiapkan'] = now();
            $data['created_by'] = auth()->user()->id;

            $trx_perencanaan_kebutuhan = PerencanaanKebutuhan::create($data);
            $lastId = $trx_perencanaan_kebutuhan->id_perencanaan_kebutuhan;
            $id_persetujuan_pengembangan = $trx_perencanaan_kebutuhan->id_persetujuan_pengembangan;

            $id_permintaan_pengembangan  = DB::table('trx_perencanaan_kebutuhan as tpk')
                                            ->join('trx_persetujuan_pengembangan as b', 'b.id_persetujuan_pengembangan', '=', 'tpk.id_persetujuan_pengembangan')
                                            ->where('tpk.id_persetujuan_pengembangan', $id_persetujuan_pengembangan)
                                            ->select('b.id_permintaan_pengembangan')
                                            ->first(); // Mengambil satu hasil 

            $id_permintaan_pengembangan = $id_permintaan_pengembangan->id_permintaan_pengembangan;

            FlagStatus::create([
                'kat_modul' => 4,
                'id_permintaan' => $id_permintaan_pengembangan,
                'nama_modul' => "Perencanaan Kebutuhan",
                'id_tabel' => $lastId,
                'flag' => 4
            ]);

            return response()->json('Data berhasil disimpan', 200);
        }else{
            echo "Tambah Data Gagal, Karena Anda Melewati Tahapan Sebelumnya atau Progress Belum 100%";
            die;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $trx_perencanaan_kebutuhan = PerencanaanKebutuhan::leftJoin('trx_persetujuan_pengembangan', 'trx_persetujuan_pengembangan.id_persetujuan_pengembangan', '=', 'trx_perencanaan_kebutuhan.id_persetujuan_pengembangan')
            ->join('trx_perencanaan_proyek','trx_perencanaan_proyek.id_persetujuan_pengembangan', '=', 'trx_perencanaan_kebutuhan.id_persetujuan_pengembangan')
            ->select([
                'trx_perencanaan_kebutuhan.id_perencanaan_kebutuhan', 
                'trx_perencanaan_kebutuhan.kebutuhan_fungsional', 
                'trx_perencanaan_kebutuhan.kebutuhan_nonfungsional', 
                'trx_perencanaan_kebutuhan.lampiran', 
                'trx_perencanaan_kebutuhan.nama_pemohon', 
                'trx_perencanaan_kebutuhan.jabatan_pemohon', 
                'trx_perencanaan_kebutuhan.tanggal_disiapkan', 
                'trx_perencanaan_kebutuhan.nama_penyetuju', 
                'trx_perencanaan_kebutuhan.jabatan_penyetuju', 
                'trx_perencanaan_kebutuhan.tanggal_disetujui', 
                'trx_perencanaan_kebutuhan.file_pdf', 
                'trx_perencanaan_kebutuhan.stakeholders', 
                'trx_perencanaan_proyek.nomor_proyek', 
                'trx_perencanaan_proyek.pemilik_proyek', 
                'trx_perencanaan_proyek.manajer_proyek', 
                'trx_perencanaan_proyek.nama_proyek',
            ])
            ->where('trx_perencanaan_kebutuhan.id_perencanaan_kebutuhan', $id) // Menggunakan kondisi where pada id_perencanaan_proyek
            ->first(); // Mengambil satu hasil pertama dari query
        
        return response()->json($trx_perencanaan_kebutuhan);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
    public function update(Request $request, $id_perencanaan_kebutuhan)
    {
        $trx_perencanaan_kebutuhan = PerencanaanKebutuhan::findOrFail($id_perencanaan_kebutuhan);
        $data = $request->all();

        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');

            $path = $file->move(public_path('storage/assets/pdf'), $filename);

            $data['lampiran'] = 'storage/' . $path;
        }

        $trx_perencanaan_kebutuhan->update($data);
        return response()->json('Data berhasil diperbarui', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $trx_perencanaan_kebutuhan = PerencanaanKebutuhan::find($id);
        $trx_perencanaan_kebutuhan->delete();

        return response(null, 204);
    }

    public function deleteSelected(Request $request)
    {
        $ids = $request->id_perencanaan_kebutuhan;
        PerencanaanKebutuhan::whereIn('id_perencanaan_kebutuhan', $ids)->delete();
        return response()->json('Data berhasil dihapus', 200);
    }

    public function cetakDokumen(Request $request)
    {
        set_time_limit(300);

        $idPerencanaanKebutuhan = $request->query();
        $id_perencanaan_kebutuhan = key($idPerencanaanKebutuhan); 
        $id_perencanaan_kebutuhan = (int) $id_perencanaan_kebutuhan;

        $datakebutuhan = PerencanaanKebutuhan::leftJoin('trx_persetujuan_pengembangan', 'trx_persetujuan_pengembangan.id_persetujuan_pengembangan', '=', 'trx_perencanaan_kebutuhan.id_persetujuan_pengembangan')
            ->leftJoin('trx_permintaan_pengembangan', 'trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan')
            ->leftJoin('trx_perencanaan_proyek', 'trx_perencanaan_proyek.id_persetujuan_pengembangan', '=', 'trx_perencanaan_kebutuhan.id_persetujuan_pengembangan')
            // Left Join untuk mengambil informasi tambahan
            ->leftJoin('mst_kriteria_aplikasi', 'mst_kriteria_aplikasi.id', '=', 'trx_perencanaan_kebutuhan.kriteria_aplikasi') // join dengan tabel kriteria_aplikasi
            ->leftJoin('mst_klasifikasi_model_backup', 'mst_klasifikasi_model_backup.id', '=', 'trx_perencanaan_kebutuhan.model_backup') // join dengan tabel model_backup
            ->leftJoin('mst_tipe_resource_server', 'mst_tipe_resource_server.id', '=', 'trx_perencanaan_kebutuhan.resource_server') // join dengan tabel resource_server
            ->leftJoin('mst_tipe_server', 'mst_tipe_server.id', '=', 'trx_perencanaan_kebutuhan.tipe_server') // join dengan tabel tipe_server
            ->leftJoin('mst_tipe_database', 'mst_tipe_database.id', '=', 'trx_perencanaan_kebutuhan.tipe_database') // join dengan tabel database
            ->leftJoin('mst_tipe_storage', 'mst_tipe_storage.id', '=', 'trx_perencanaan_kebutuhan.tipe_storage') // join dengan tabel storage
            ->select(
                'trx_permintaan_pengembangan.nomor_dokumen',
                'trx_perencanaan_proyek.nomor_proyek', 
                'trx_perencanaan_proyek.nama_proyek', 
                'trx_perencanaan_proyek.pemilik_proyek', 
                'trx_perencanaan_proyek.manajer_proyek',
                'trx_perencanaan_kebutuhan.stakeholders', 
                'trx_perencanaan_kebutuhan.bahasa_pemrograman', 
                'trx_perencanaan_kebutuhan.kebutuhan_fungsional', 
                'trx_perencanaan_kebutuhan.kebutuhan_nonfungsional', 
                'trx_perencanaan_kebutuhan.approve_at_pemverifikasi', 
                'trx_perencanaan_kebutuhan.approve_at',
                'trx_perencanaan_kebutuhan.lampiran',
                'trx_perencanaan_kebutuhan.nama_pemohon',
                'trx_perencanaan_kebutuhan.nama_pemverifikasi',
                'trx_perencanaan_kebutuhan.nama_penyetuju',
                'trx_perencanaan_kebutuhan.jabatan_pemohon',
                'trx_perencanaan_kebutuhan.jabatan_pemverifikasi',
                'trx_perencanaan_kebutuhan.jabatan_penyetuju',
                'trx_perencanaan_kebutuhan.path_qrcode_pemohon',
                'trx_perencanaan_kebutuhan.path_qrcode_pemverifikasi',
                'trx_perencanaan_kebutuhan.path_qrcode_penyetuju',
                // Tambahkan kolom-kolom tambahan yang bergabung dengan tabel lainnya
                'mst_kriteria_aplikasi.nama as nama_kriteria_aplikasi', 
                'mst_kriteria_aplikasi.deskripsi as deskripsi_kriteria_aplikasi', 
                'mst_klasifikasi_model_backup.nama as nama_klasifikasi_model_backup', 
                'mst_klasifikasi_model_backup.deskripsi as deskripsi_klasifikasi_model_backup', 
                'mst_tipe_resource_server.nama as nama_tipe_resource_server',
                'mst_tipe_resource_server.deskripsi as deskripsi_tipe_resource_server',
                'mst_tipe_server.nama as nama_tipe_server', 
                'mst_tipe_database.nama as nama_database',
                'mst_tipe_storage.nama as nama_storage',
                'mst_tipe_storage.deskripsi as deskripsi_storage',
            )
            ->whereIn('id_perencanaan_kebutuhan', [$id_perencanaan_kebutuhan])
            ->get();

        $no  = 1;
        $pdf = PDF::loadView('perencanaan_kebutuhan.dokumen', compact('datakebutuhan', 'no'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('PerencanaanKebutuhan.pdf');
    }

    public function view($id)
    {
        $trx_perencanaan_kebutuhan = PerencanaanKebutuhan::findOrFail($id);
        return response()->json($trx_perencanaan_kebutuhan);
    }

    
    public function updatePDF(Request $request, $id_perencanaan_kebutuhan)
    {
        // Temukan data berdasarkan ID
        $trx_perencanaan_kebutuhan = PerencanaanKebutuhan::findOrFail($id_perencanaan_kebutuhan);

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
            $trx_perencanaan_kebutuhan->file_pdf = $filename;

            // Update data di database
            $trx_perencanaan_kebutuhan->save();

            return response()->json('File PDF berhasil diperbarui', 200);
        }

        return response()->json('Tidak ada file yang diupload', 400);
    }

    // For Update Progress Project
    public function editProgress($id)
    {
        // Cari data permintaan pengembangan berdasarkan ID
        $trx_perencanaan_kebutuhan = PerencanaanKebutuhan::leftJoin('trx_persetujuan_pengembangan', 'trx_persetujuan_pengembangan.id_persetujuan_pengembangan', '=', 'trx_perencanaan_kebutuhan.id_persetujuan_pengembangan')
            ->join('trx_perencanaan_proyek', 'trx_perencanaan_proyek.id_persetujuan_pengembangan', '=', 'trx_perencanaan_kebutuhan.id_persetujuan_pengembangan')
            // ->leftJoin('trx_persetujuan_pengembangan', 'trx_persetujuan_pengembangan.id_persetujuan_pengembangan', '=', 'trx_perencanaan_kebutuhan.id_persetujuan_pengembangan')
            // ->join('trx_persetujuan_pengembangan', function ($join) {
            //     $join->on('trx_persetujuan_pengembangan.id_persetujuan_pengembangan', '=', 'trx_perencanaan_kebutuhan.id_persetujuan_pengembangan')
            //     ->orOn('trx_persetujuan_pengembangan.id_perencanaan_proyek', '=', 'trx_perencanaan_kebutuhan.id_perencanaan_proyek');
            // })
            ->select('trx_perencanaan_kebutuhan.id_perencanaan_kebutuhan', 'trx_perencanaan_proyek.nama_proyek', 'trx_perencanaan_kebutuhan.kebutuhan_fungsional', 'trx_perencanaan_kebutuhan.kebutuhan_nonfungsional', 'trx_perencanaan_kebutuhan.lampiran', 'trx_perencanaan_kebutuhan.nama_pemohon', 'trx_perencanaan_kebutuhan.jabatan_pemohon', 'trx_perencanaan_kebutuhan.tanggal_disiapkan', 'trx_perencanaan_kebutuhan.nama', 'trx_perencanaan_kebutuhan.jabatan', 'trx_perencanaan_kebutuhan.tanggal_disetujui', 'trx_perencanaan_kebutuhan.file_pdf', 'trx_perencanaan_kebutuhan.stakeholders', 'trx_perencanaan_proyek.nomor_proyek', 'trx_perencanaan_proyek.pemilik_proyek', 'trx_perencanaan_proyek.manajer_proyek', 'trx_perencanaan_proyek.nama_proyek', 'trx_perencanaan_kebutuhan.progress')
            ->where('trx_perencanaan_kebutuhan.id_perencanaan_kebutuhan', $id)
            ->first();

        // Kirim data ke response JSON
        return response()->json($trx_perencanaan_kebutuhan);
    }

    public function updateProgress(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'progress' => 'required|integer|min:0|max:100', // Validasi progress
            'nama_proyek' => 'required|string|max:255', // Validasi nomor dokumen
        ]);

        // Cari data permintaan pengembangan berdasarkan ID
        $trx_perencanaan_kebutuhan = PerencanaanKebutuhan::findOrFail($id);

        // Update progress
        $trx_perencanaan_kebutuhan->progress = $request->progress; // Pastikan ada kolom 'progress' di tabel
        $trx_perencanaan_kebutuhan->save(); // Simpan perubahan

        // Kembali dengan respon sukses
        return redirect()->route('perencanaan_kebutuhan.index');
    }

    // Method untuk melakukan approve proyek
    public function approveProyek($id)
    {
        // Ambil data proyek berdasarkan id
        $proyek = PerencanaanKebutuhan::leftJoin('trx_persetujuan_pengembangan', 'trx_persetujuan_pengembangan.id_persetujuan_pengembangan', '=', 'trx_perencanaan_kebutuhan.id_persetujuan_pengembangan')
                  ->leftJoin('trx_permintaan_pengembangan', 'trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan')
                  ->where('trx_perencanaan_kebutuhan.id_perencanaan_kebutuhan', $id)  // Sesuaikan dengan parameter ID yang Anda inginkan
                  ->select('trx_perencanaan_kebutuhan.*', 'trx_permintaan_pengembangan.nomor_dokumen', 'trx_permintaan_pengembangan.id_permintaan_pengembangan')  // Memilih semua kolom dari kedua tabel
                  ->first();  // Ambil data pertama (karena kita menggunakan where ID)

        // Cek apakah progress sudah 100% dan file_pdf sudah terisi
        // if ($proyek->progress == 100 && !empty($proyek->file_pdf)) {
        // if (!empty($proyek->file_pdf)) {
            // Update status proyek menjadi approved (Anda dapat menambah field status_approval di tabel)
        if(auth()->user()->nik == $proyek->nik_pemverifikasi){
            $proyek->is_approve_pemverifikasi = 1;
            $proyek->approve_at_pemverifikasi = now(); // Set tanggal persetujuan saat ini
            $proyek->approve_by_pemverifikasi = auth()->user()->id; // Set tanggal persetujuan saat ini
            $proyek->tanggal_verifikasi = now(); // Set tanggal persetujuan saat ini
            // Content for the QR code
            $qrContent ='Perencanaan Kebutuhan, Pemverifikasi :' . auth()->user()->name . 
                        ', Nomor Dokumen : ' . $proyek->nomor_dokumen .
                        ', NIK Karyawan : ' . $proyek->nik_penyetuju . 
                        ', Tanggal Disetujui : ' . now(); 
            
            // Sanitize nomor_dokumen to remove slashes and create a valid filename
            $safeNomorDokumen = str_replace('/', '', $proyek->nomor_dokumen);  // Remove slashes
            $fileName = '' . 'qr_code_perencanaankebutuhan_pemverifikasi_' . $proyek->id_permintaan_pengembangan . '_' . $safeNomorDokumen . '.png';
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
            $proyek->path_qrcode_pemverifikasi = $filePath;
            $proyek->save();

            return response()->json(['success' => 'Proyek berhasil di-approve.']);
        }else if(auth()->user()->nik == $proyek->nik_penyetuju){
            $proyek->is_approve = 1;
            $proyek->approve_at = now(); // Set tanggal persetujuan saat ini
            $proyek->approve_by = auth()->user()->id; // Set tanggal persetujuan saat ini
            $proyek->tanggal_disetujui = now(); // Set tanggal persetujuan saat ini
            // Content for the QR code
            $qrContent ='Perencanaan Kebutuhan, Penyetuju :' . auth()->user()->name . 
                        ', Nomor Dokumen : ' . $proyek->nomor_dokumen .
                        ', NIK Karyawan : ' . $proyek->nik_penyetuju . 
                        ', Tanggal Disetujui : ' . now(); 
            
            // Sanitize nomor_dokumen to remove slashes and create a valid filename
            $safeNomorDokumen = str_replace('/', '', $proyek->nomor_dokumen);  // Remove slashes
            $fileName = '' . 'qr_code_perencanaankebutuhan_penyetuju_' . $proyek->id_permintaan_pengembangan . '_' . $safeNomorDokumen . '.png';
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
        }
        // }

        // Jika belum memenuhi syarat approval, kembalikan pesan error
        return response()->json(['error' => 'Proyek belum memenuhi syarat untuk di-approve.'], 400);
    }
}

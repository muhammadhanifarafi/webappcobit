<?php

namespace App\Http\Controllers;

use App\Models\PersetujuanPengembangan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\PerencanaanProyek;
use App\Models\FlagStatus;
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
use App\Services\WhatsAppService;

class PerencanaanProyekController extends Controller
{
    protected $whatsAppService;

    // Injeksi WhatsAppService melalui konstruktor
    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $nama_proyek_terpakai = PerencanaanProyek::pluck('id_persetujuan_pengembangan')->toArray();

        $trx_persetujuan_pengembangan = PersetujuanPengembangan::whereNotIn('id_persetujuan_pengembangan', $nama_proyek_terpakai)
        ->join('trx_permintaan_pengembangan', 'trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan')
        ->whereNotNull('trx_permintaan_pengembangan.nomor_dokumen')  // Memastikan nomor dokumen tidak null
        ->whereIn('trx_permintaan_pengembangan.id_permintaan_pengembangan', function($query) {
            $query->select('id_permintaan_pengembangan')
                ->from('trx_permintaan_pengembangan')
                ->whereNotNull('nomor_dokumen');  // Pastikan hanya yang memiliki nomor dokumen
        })
        ->when(auth()->user()->level != 1, function ($query) {
            // Jika level user adalah 1, tambahkan filter berdasarkan level
            return $query->where('trx_permintaan_pengembangan.created_by', auth()->id());
        })
        ->select('trx_permintaan_pengembangan.nomor_dokumen', 'trx_permintaan_pengembangan.judul', 'trx_persetujuan_pengembangan.id_persetujuan_pengembangan') // Ambil kolom yang dibutuhkan
        ->get(); // Menggunakan get untuk mengambil semua hasil dalam bentuk koleksi         

        return view('perencanaan_proyek.index',compact('trx_persetujuan_pengembangan'));
    }

    public function data()
    {
        $trx_perencanaan_proyek = PerencanaanProyek::leftJoin('trx_persetujuan_pengembangan', 'trx_persetujuan_pengembangan.id_persetujuan_pengembangan', '=', 'trx_perencanaan_proyek.id_persetujuan_pengembangan')
        ->leftJoin('trx_permintaan_pengembangan', 'trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan')
        ->select([
            'trx_permintaan_pengembangan.nomor_dokumen',
            'trx_permintaan_pengembangan.file_pdf as file_pdf_dokumen',
            'trx_permintaan_pengembangan.nama_pemohon',
            'trx_permintaan_pengembangan.nik_pemohon',
            'trx_permintaan_pengembangan.jabatan_pemohon',

            'trx_perencanaan_proyek.id_perencanaan_proyek',
            'trx_perencanaan_proyek.id_persetujuan_pengembangan',
            'trx_perencanaan_proyek.nomor_proyek',
            'trx_perencanaan_proyek.nama_proyek',
            'trx_perencanaan_proyek.pemilik_proyek',
            'trx_perencanaan_proyek.manajer_proyek',
            'trx_perencanaan_proyek.ruang_lingkup',
            'trx_perencanaan_proyek.tanggal_mulai',
            'trx_perencanaan_proyek.target_selesai',
            'trx_perencanaan_proyek.nilai_kontrak',
            'trx_perencanaan_proyek.nama_pemohon',
            'trx_perencanaan_proyek.jabatan_pemohon',
            'trx_perencanaan_proyek.tanggal_disiapkan',
            'trx_perencanaan_proyek.nama_penyetuju',
            'trx_perencanaan_proyek.is_approve',
            'trx_perencanaan_proyek.is_approve_pemverifikasi',
            'trx_perencanaan_proyek.approve_by',
            'trx_perencanaan_proyek.jabatan_penyetuju',
            'trx_perencanaan_proyek.tanggal_disetujui',
            'trx_perencanaan_proyek.file_pdf',
            'trx_perencanaan_proyek.created_at as proyek_created_at',
            'trx_perencanaan_proyek.updated_at as proyek_updated_at',
            'trx_perencanaan_proyek.progress',
            'trx_perencanaan_proyek.nik_pemohon',
            'trx_perencanaan_proyek.nik_pemverifikasi',
            'trx_perencanaan_proyek.nik_penyetuju',
            'trx_perencanaan_proyek.lampiran',
            
            'trx_persetujuan_pengembangan.id_persetujuan_pengembangan as persetujuan_id',
            'trx_persetujuan_pengembangan.id_permintaan_pengembangan',
            'trx_persetujuan_pengembangan.id_mst_persetujuan',
            'trx_persetujuan_pengembangan.id_mst_persetujuanalasan',
            'trx_persetujuan_pengembangan.nama_pemohon as persetujuan_namapemohon',
            'trx_persetujuan_pengembangan.jabatan_pemohon',
            'trx_persetujuan_pengembangan.nama_penyetuju',
            'trx_persetujuan_pengembangan.file_pdf as persetujuan_file_pdf',
            'trx_persetujuan_pengembangan.created_at as persetujuan_created_at',
            'trx_persetujuan_pengembangan.updated_at as persetujuan_updated_at',
        ])
        ->orderByRaw("trx_perencanaan_proyek.is_approve asc,trx_perencanaan_proyek.approve_at desc, trx_perencanaan_proyek.created_at desc");
        if (auth()->user()->level != 1) {
            $trx_perencanaan_proyek = $trx_perencanaan_proyek
            ->where('trx_perencanaan_proyek.nik_penyetuju', auth()->user()->nik)
            ->orWhere('trx_perencanaan_proyek.nik_pemverifikasi', auth()->user()->nik)
            ->orWhere('trx_perencanaan_proyek.created_by', auth()->user()->id);
        }

        $trx_perencanaan_proyek = $trx_perencanaan_proyek->get();
        

        return datatables()
            ->of($trx_perencanaan_proyek)
            ->addIndexColumn()
            ->addColumn('select_all', function ($trx_perencanaan_proyek) {
                return '
                    <input type="checkbox" name="id_perencanaan_proyek[]" value="'. $trx_perencanaan_proyek->id_perencanaan_proyek .'">
                ';
            })
            ->addColumn('deskripsi', function($trx_persetujuan_pengembangan){
                return $trx_persetujuan_pengembangan->deskripsi;
            })
            ->addColumn('approval_status', function ($trx_perencanaan_proyek) {
                if ($trx_perencanaan_proyek->is_approve == 1 && $trx_perencanaan_proyek->is_approve_pemverifikasi == 1 && $trx_perencanaan_proyek->file_pdf != null) {
                    return '<b>File Dokumen Akhir sudah diupload</b>';
                } elseif ($trx_perencanaan_proyek->is_approve == 1) {
                    return '<b>Sudah di Approve Penyetuju</b>';
                } elseif ($trx_perencanaan_proyek->is_approve_pemverifikasi == 1) {
                    return '<b>Sudah di Approve Pemverifikator</b>';
                } elseif ($trx_perencanaan_proyek->is_approve_pemverifikasi != 1) {
                    return '<b>Menunggu Approval Pemverifikator</b>';
                } elseif ($trx_perencanaan_proyek->is_approve_pemverifikasi == 1 && $trx_perencanaan_proyek->is_approve != 1) {
                    return '<b>Menunggu Approval Penyetuju</b>';
                }else {
                    return '';
                }                
            })
            ->addColumn('aksi', function ($trx_perencanaan_proyek) {
                $id_proyek = $trx_perencanaan_proyek->id_perencanaan_proyek;
                $isApproved = (int) $trx_perencanaan_proyek->is_approve_pemverifikasi != 1 && empty($trx_perencanaan_proyek->file_pdf);
                $isApproved2 = (int) $trx_perencanaan_proyek->is_approve_pemverifikasi == 1 && empty($trx_perencanaan_proyek->file_pdf);
                // Cek apakah progress sudah 100% dan file PDF sudah terisi
                // $isApproved = $trx_perencanaan_proyek->progress == 100 && !empty($trx_perencanaan_proyek->file_pdf);
                // $isApproved = !empty($trx_perencanaan_proyek->file_pdf) ;
                // $alreadyApproved = (int) $trx_perencanaan_proyek->is_approve === 1; // Tambahkan kondisi untuk status approval
                
                // Menentukan apakah pengguna telah menyetujui
                if(auth()->user()->level == 1){
                    $alreadyApproved = "";
                }
                
                if (auth()->user()->level != 1) {
                    if (auth()->user()->nik == $trx_perencanaan_proyek->nik_pemverifikasi) {
                        if ($trx_perencanaan_proyek->is_approve_pemverifikasi == 1) {
                            $approveButton = '<button type="button" class="btn btn-xs btn-success btn-flat" disabled><i class="fa fa-check"></i> Approved</button>';
                        }else{
                            $approveButton = '<button type="button" onclick="approveProyek(`'. route('perencanaan_proyek.approveProyek', $id_proyek) .'`)" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-check"></i> Approve</button>';
                        }
                    }
                    elseif (auth()->user()->nik == $trx_perencanaan_proyek->nik_penyetuju) {
                        if ($trx_perencanaan_proyek->is_approve != 1) {
                            $approveButton = '<button type="button" onclick="approveProyek(`'. route('perencanaan_proyek.approveProyek', $id_proyek) .'`)" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-check"></i> Approve</button>';
                        } else {
                            $approveButton = '<button type="button" class="btn btn-xs btn-success btn-flat" disabled><i class="fa fa-check"></i> Approved</button>';
                        }
                    } else {
                        $approveButton = '';
                    }
                } else {
                    $approveButton = '';
                } 
            
                // $approveButton = is_null($trx_perencanaan_proyek->approve_by)
                //                 ? ($isApproved 
                //                     ? '<button type="button" class="btn btn-xs btn-success btn-flat" disabled><i class="fa fa-check"></i> Approved</button>'
                //                     : '<button type="button" onclick="approveProyek(`'. route('perencanaan_proyek.approveProyek', $id_proyek) .'`)" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-check"></i> Approve</button>') // Jika tidak memenuhi syarat, tampilkan Approved
                //                 : '<button type="button" class="btn btn-xs btn-success btn-flat" disabled><i class="fa fa-check"></i> Approved</button>'; // Jika approve_by terisi

                $uploadButton = ($trx_perencanaan_proyek->is_approve == 1 && auth()->user()->id == 1)
                                ? '<button onclick="UploadPDF(`'. route('perencanaan_proyek.updatePDF', $trx_perencanaan_proyek->id_perencanaan_proyek) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-upload"></i></button>'
                                : '';

                return '
                        <div class="btn-group">
                            ' . (auth()->user()->level == 1 || auth()->user()->level == 5? '
                                <button type="button" onclick="deleteData(`' . route('perencanaan_proyek.destroy', $id_proyek) . '`)" class="btn btn-xs btn-danger btn-flat">
                                    <i class="fa fa-trash"></i>
                                </button>
                                    ' . ($isApproved ? '
                                    <button type="button" onclick="editForm(`' . route('perencanaan_proyek.update', $id_proyek) . '`)" class="btn btn-xs btn-info btn-flat">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    ': '') . '
                                    ' . ($isApproved2 ? $uploadButton : '') . '
                            ' : '') . '
                            <button onclick="cetakDokumen(`' . route('perencanaan_proyek.cetakDokumen', $id_proyek) . '`)" class="btn btn-info btn-xs btn-flat">
                                <i class="fa fa-download"></i> Cetak Dokumen
                            </button>
                            <button type="button" onclick="viewForm(`' . route('perencanaan_proyek.view', $id_proyek) . '`)" class="btn btn-xs btn-primary btn-flat">
                                <i class="fa fa-eye"></i>
                            </button>
                            ' . (auth()->user()->level == 2 || auth()->user()->level == 3 || auth()->user()->level == 4 || auth()->user()->level == 5 ? $approveButton : '') . '
                        </div>
                       ';                    

                // return '
                // <div class="btn-group">
                //     <button type="button" onclick="deleteData(`'. route('perencanaan_proyek.destroy', $id_proyek) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                //     ' . (!$isApproved ? '
                //     <button type="button" onclick="editForm(`'. route('perencanaan_proyek.update', $id_proyek) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                //     <button type="button" onclick="UploadPDF(`'. route('perencanaan_proyek.updatePDF', $id_proyek) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-upload"></i></button>
                //     <button onclick="updateProgressForm(`'. route('perencanaan_proyek.editProgress', $id_proyek) .'`)" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-tasks"></i> Update Progress</button>
                //     ' : '') . '
                //     '. $approveButton .'
                //     <button onclick="cetakDokumen(`'.route('perencanaan_proyek.cetakDokumen', $id_proyek) .'`)" class="btn btn-info btn-xs btn-flat">
                //         <i class="fa fa-download"></i> Cetak Dokumen
                //     </button>
                //     <button type="button" onclick="viewForm(`'. route('perencanaan_proyek.view', $id_proyek) .'`)" class="btn btn btn-xs btn-primary btn-flat"><i class="fa fa-eye"></i></button>
                // </div>
                // ';
            })
            ->addColumn('file_pdf', function ($trx_perencanaan_proyek) {
                if ($trx_perencanaan_proyek->file_pdf) {
                    return '<a href="/storage/assets/pdf/' . $trx_perencanaan_proyek->file_pdf . '" target="_blank">Lihat PDF</a>';
                }
                return '-';
            })
            ->addColumn('nomor_dokumen', function ($trx_perencanaan_proyek) {
                if ($trx_perencanaan_proyek->file_pdf_dokumen) {
                    return '<a href="/storage/assets/pdf/' . $trx_perencanaan_proyek->file_pdf_dokumen . '" target="_blank">' . $trx_perencanaan_proyek->nomor_dokumen . '</a>';
                }else{
                    return $trx_perencanaan_proyek->nomor_dokumen;
                }
                return '-';
            })
            ->editColumn('deskripsi', function($row) {
                return $row->deskripsi; // Data dengan HTML
            })
            ->editColumn('ruang_lingkup', function($row) {
                $ruangLingkup = $row->ruang_lingkup;
                $maxLength = 100; // Batasi panjang karakter
            
                // Cek apakah teks lebih panjang dari batas yang ditentukan
                if (strlen($ruangLingkup) > $maxLength) {
                    // Jika teks lebih panjang, tampilkan sebagian dengan "Read More"
                    return '<span class="short-text">' . substr($ruangLingkup, 0, $maxLength) . '...</span>
                            <a href="javascript:void(0)" class="read-more" data-full-text="' . htmlspecialchars($ruangLingkup) . '">Read More</a>';
                }
            
                // Jika teks tidak lebih panjang, tampilkan teks lengkap
                return $ruangLingkup;
            })
            ->rawColumns(['ruang_lingkup', 'aksi', 'select_all','file_pdf','deskripsi','approval_status','nomor_dokumen'])
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
        // $sql_validasi = "   SELECT
        //                         flag_status.id_permintaan,
        //                         tpp.nomor_dokumen,
        //                         tpp.latar_belakang,
        //                         tpp.tujuan,
        //                         MAX( flag_status.flag ) AS max_flag 
        //                     FROM
        //                         flag_status
        //                         LEFT JOIN trx_permintaan_pengembangan AS tpp ON tpp.id_permintaan_pengembangan = flag_status.id_permintaan
        //                         LEFT JOIN trx_persetujuan_pengembangan AS tpp2 ON tpp2.id_permintaan_pengembangan = tpp.id_permintaan_pengembangan
        //                     WHERE
        //                         -- tpp2.id_persetujuan_pengembangan = $request->id_persetujuan_pengembangan AND tpp2.progress = 100 
        //                         tpp2.id_persetujuan_pengembangan = $request->id_persetujuan_pengembangan AND tpp2.is_approve = 1 
        //                     GROUP BY
        //                         flag_status.id_permintaan,
        //                         tpp.nomor_dokumen,
        //                         tpp.latar_belakang,
        //                         tpp.tujuan 
        //                     HAVING
        //                         MAX( flag_status.flag ) = 1;
        //                 ";
        // Content for the QR code
        $qrContent = 'Perencanaan Proyek, Pemohon :' . $request->nama_pemohon . 
                     ', Nomor Dokumen : ' . $proyek->nomor_dokumen .
                     ', NIK Karyawan : ' . $request->nik_pemohon . 
                     ', Tanggal Disiapkan : ' . now(); 
   
                // Sanitize nomor_dokumen to remove slashes and create a valid filename
                $safeNomorDokumen = str_replace('/', '', $proyek->nomor_dokumen);  // Remove slashes
                $fileName = '' . 'qr_code_perencanaanproyek_pemohon_' . $proyek->id_permintaan_pengembangan . '_' . $safeNomorDokumen . '.png';
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

            $lastNoDocument = PerencanaanProyek::orderBy('nomor_proyek', 'desc')->first();

            if (!$lastNoDocument || date('Y') != date('Y', strtotime($lastNoDocument->created_at))) {
                $number = 1;
            } else {
                $number = intval($lastNoDocument->nomor_proyek) + 1;
            }

            $formattedNumber = str_pad($number, 4, '0', STR_PAD_LEFT);
            
            $months = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
            $currentMonth = $months[date('n') - 1];
            
            $currentYear = date('Y');
            
            $data['nomor_proyek'] = "{$formattedNumber}/{$currentMonth}/PYK/DTI/{$currentYear}";  

            if ($request->hasFile('lampiran')) {
                $file = $request->file('lampiran');
                $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
                $path = $file->move(public_path('storage/assets/lampiran'), $filename);
                $data['lampiran'] = $filename;
            }

            if ($request->hasFile('file_pdf')) {
                $file = $request->file('file_pdf');
                $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
                $path = $file->move(public_path('storage/assets/pdf'), $filename);
                $data['file_pdf'] = $filename;
            }

            $data['tanggal_disiapkan'] = now();
            $data['created_by'] = auth()->user()->id;

            $trx_perencanaan_proyek = PerencanaanProyek::create($data);
            $lastId = $trx_perencanaan_proyek->id_perencanaan_proyek;
            $id_persetujuan_pengembangan = $trx_perencanaan_proyek->id_persetujuan_pengembangan;
            $id_permintaan_pengembangan  = DB::table('trx_perencanaan_proyek as tpp')
                                            ->join('trx_persetujuan_pengembangan as b', 'b.id_persetujuan_pengembangan', '=', 'tpp.id_persetujuan_pengembangan')
                                            ->where('tpp.id_persetujuan_pengembangan', $id_persetujuan_pengembangan)
                                            ->select('b.id_permintaan_pengembangan')
                                            ->first(); // Mengambil satu hasil 
                                            
            $id_permintaan_pengembangan = $id_permintaan_pengembangan->id_permintaan_pengembangan;

            FlagStatus::create([
                'kat_modul' => 3,
                'id_permintaan' => $id_permintaan_pengembangan,
                'nama_modul' => "Perencanaan Proyek",
                'id_tabel' => $lastId,
                'flag' => 3
            ]);

            $user = Users::where('nik', $data['nik_pemverifikasi'])->first();
            if ($user) {
                $data['no_telp'] = $user->no_telp;
            }

            // Kirim pesan WhatsApp
            $message = "Permintaan Pengembangan *{$request->judul}* telah diajukan oleh *{$request->nama_pemohon}* "
                    . "\ndengan nomor dokumen *{$proyek->nomor_dokumen}* pada *" . now()->format('d F Y H:i:s') . "* "
                    . "\ndan sedang menunggu diverifikasi oleh *{$request->nama_pemverifikasi}*.";

            $this->whatsAppService->sendWhatsAppMessage($data['no_telp'], $message);

            return response()->json('Data berhasil disimpan', 200);
        // }else{
        //     echo "Tambah Data Gagal, Karena Anda Melewati Tahapan Sebelumnya atau Progress Belum 100%";
        //     die;
        // }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id_perencanaan_proyek)
    {
        $trx_perencanaan_proyek = PerencanaanProyek::find($id_perencanaan_proyek);

        return response()->json($trx_perencanaan_proyek);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id_perencanaan_proyek)
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
    public function update(Request $request, $id_perencanaan_proyek)
    {
        $trx_perencanaan_proyek = PerencanaanProyek::find($id_perencanaan_proyek);
        $trx_perencanaan_proyek->update($request->all());

        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_perencanaan_proyek)
    {
        $trx_perencanaan_proyek = PerencanaanProyek::find($id_perencanaan_proyek);
        $trx_perencanaan_proyek->delete();

        return response(null, 204);
    }

    public function deleteSelected(Request $request)
    {
        $ids = $request->id_perencanaan_proyek;
        PerencanaanProyek::whereIn('id_perencanaan_proyek', $ids)->delete();
        return response()->json('Data berhasil dihapus', 200);
    }

    public function cetakDokumen(Request $request)
    {
        set_time_limit(300);

        // $ids = $request->id_perencanaan_proyek;
        $idPerencanaanProyek = $request->query();
        $id_perencanaan_proyek = key($idPerencanaanProyek); 
        $ids = (int) $id_perencanaan_proyek;

        $dataperencanaan =  PerencanaanProyek::leftJoin('trx_persetujuan_pengembangan','trx_persetujuan_pengembangan.id_persetujuan_pengembangan', '=', 'trx_perencanaan_proyek.id_persetujuan_pengembangan')
                            ->leftJoin('trx_permintaan_pengembangan','trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan')
                            ->select('trx_permintaan_pengembangan.nomor_dokumen', 'trx_perencanaan_proyek.*')
                            ->whereIn('trx_perencanaan_proyek.id_perencanaan_proyek', [$ids])
                            ->get();

        $pdf = PDF::loadView('perencanaan_proyek.dokumen', compact('dataperencanaan'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('perencanaan.pdf');
    }

    public function view($id)
    {
        $trx_perencanaan_proyek = PerencanaanProyek::leftJoin('trx_persetujuan_pengembangan', 'trx_persetujuan_pengembangan.id_persetujuan_pengembangan', '=', 'trx_perencanaan_proyek.id_persetujuan_pengembangan')
        ->select(
            'trx_perencanaan_proyek.*',
        )
        ->findOrFail($id);

        return response()->json($trx_perencanaan_proyek);
    }
    public function updatePDF(Request $request, $id_perencanaan_proyek)
    {
        // Temukan data berdasarkan ID
        $trx_perencanaan_proyek = PerencanaanProyek::findOrFail($id_perencanaan_proyek);

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
            $trx_perencanaan_proyek->file_pdf = $filename;

            // Update data di database
            $trx_perencanaan_proyek->save();

            return response()->json('File PDF berhasil diperbarui', 200);
        }

        return response()->json('Tidak ada file yang diupload', 400);
    }

    // For Update Progress Project
    public function editProgress($id)
    {
            // Cari data permintaan pengembangan berdasarkan ID
            $trx_perencanaan_proyek = PerencanaanProyek::leftJoin('trx_persetujuan_pengembangan', 'trx_persetujuan_pengembangan.id_persetujuan_pengembangan', '=', 'trx_perencanaan_proyek.id_persetujuan_pengembangan')
            ->select([
                'trx_perencanaan_proyek.id_perencanaan_proyek',
                'trx_perencanaan_proyek.id_persetujuan_pengembangan',
                'trx_perencanaan_proyek.nomor_proyek',
                'trx_perencanaan_proyek.pemilik_proyek',
                'trx_perencanaan_proyek.manajer_proyek',
                'trx_perencanaan_proyek.ruang_lingkup',
                'trx_perencanaan_proyek.tanggal_mulai',
                'trx_perencanaan_proyek.target_selesai',
                'trx_perencanaan_proyek.nilai_kontrak',
                'trx_perencanaan_proyek.nama_pemohon',
                'trx_perencanaan_proyek.jabatan_pemohon',
                'trx_perencanaan_proyek.tanggal_disiapkan',
                'trx_perencanaan_proyek.nama',
                'trx_perencanaan_proyek.jabatan',
                'trx_perencanaan_proyek.tanggal_disetujui',
                'trx_perencanaan_proyek.file_pdf',  // Menggunakan alias untuk menghindari konflik
                'trx_perencanaan_proyek.progress',  // Menggunakan alias untuk menghindari konflik
                'trx_perencanaan_proyek.created_at as proyek_created_at',
                'trx_perencanaan_proyek.updated_at as proyek_updated_at',
                
                // Kolom dari tabel trx_persetujuan_pengembangan
                'trx_persetujuan_pengembangan.id_persetujuan_pengembangan as persetujuan_id',
                'trx_persetujuan_pengembangan.id_permintaan_pengembangan',
                'trx_persetujuan_pengembangan.id_mst_persetujuan',
                'trx_persetujuan_pengembangan.id_mst_persetujuanalasan',
                'trx_persetujuan_pengembangan.nama_proyek',
                'trx_persetujuan_pengembangan.deskripsi',
                'trx_persetujuan_pengembangan.namapemohon as persetujuan_namapemohon',
                'trx_persetujuan_pengembangan.namapeninjau',
                'trx_persetujuan_pengembangan.jabatanpeninjau',
                'trx_persetujuan_pengembangan.namapenyetuju',
                'trx_persetujuan_pengembangan.file_pdf as persetujuan_file_pdf',
                'trx_persetujuan_pengembangan.created_at as persetujuan_created_at',
                'trx_persetujuan_pengembangan.updated_at as persetujuan_updated_at',
            ])
            ->where('trx_perencanaan_proyek.id_perencanaan_proyek', $id)
            ->first();

        // Kirim data ke response JSON
        return response()->json($trx_perencanaan_proyek);
    }

    public function updateProgress(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'progress' => 'required|integer|min:0|max:100', // Validasi progress
            'nomor_proyek' => 'required|string|max:255', // Validasi nomor dokumen
        ]);

        // Cari data permintaan pengembangan berdasarkan ID
        $trx_perencanaan_proyek = PerencanaanProyek::findOrFail($id);

        // Update progress
        $trx_perencanaan_proyek->progress = $request->progress; // Pastikan ada kolom 'progress' di tabel
        $trx_perencanaan_proyek->save(); // Simpan perubahan

        // Kembali dengan respon sukses
        return redirect()->route('perencanaan_proyek.index');
    }

    // Method untuk melakukan approve proyek
    public function approveProyek($id)
    {    
        // Ambil data proyek berdasarkan id
        $proyek = PerencanaanProyek::leftJoin('trx_persetujuan_pengembangan', 'trx_persetujuan_pengembangan.id_persetujuan_pengembangan', '=', 'trx_perencanaan_proyek.id_persetujuan_pengembangan')
                  ->leftJoin('trx_permintaan_pengembangan', 'trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan')
                  ->where('trx_perencanaan_proyek.id_perencanaan_proyek', $id)  // Sesuaikan dengan parameter ID yang Anda inginkan
                  ->select('trx_perencanaan_proyek.*', 'trx_permintaan_pengembangan.nomor_dokumen', 'trx_permintaan_pengembangan.id_permintaan_pengembangan')  // Memilih semua kolom dari kedua tabel
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
            $qrContent ='Perencanaan Proyek, Pemverifikasi :' . auth()->user()->name . 
                        ', Nomor Dokumen : ' . $proyek->nomor_dokumen .
                        ', NIK Karyawan : ' . $proyek->nik_penyetuju . 
                        ', Tanggal Disetujui : ' . now(); 
            
            // Sanitize nomor_dokumen to remove slashes and create a valid filename
            $safeNomorDokumen = str_replace('/', '', $proyek->nomor_dokumen);  // Remove slashes
            $fileName = '' . 'qr_code_perencanaanproyek_pemverifikasi_' . $proyek->id_permintaan_pengembangan . '_' . $safeNomorDokumen . '.png';
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
            $proyek->path_qrcode_pemverifikasi = $filePath;
            $proyek->save();

            return response()->json(['success' => 'Proyek berhasil di-approve.']);
        }else if(auth()->user()->nik == $proyek->nik_penyetuju){
            $proyek->is_approve = 1;
            $proyek->approve_at = now(); // Set tanggal persetujuan saat ini
            $proyek->approve_by = auth()->user()->id; // Set tanggal persetujuan saat ini
            $proyek->tanggal_disetujui = now(); // Set tanggal persetujuan saat ini
            // Content for the QR code
            $qrContent ='Perencanaan Proyek, Penyetuju :' . auth()->user()->name . 
                        ', Nomor Dokumen : ' . $proyek->nomor_dokumen .
                        ', NIK Karyawan : ' . $proyek->nik_penyetuju . 
                        ', Tanggal Disetujui : ' . now(); 
            
            // Sanitize nomor_dokumen to remove slashes and create a valid filename
            $safeNomorDokumen = str_replace('/', '', $proyek->nomor_dokumen);  // Remove slashes
            $fileName = '' . 'qr_code_perencanaanproyek_penyetuju_' . $proyek->id_permintaan_pengembangan . '_' . $safeNomorDokumen . '.png';
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

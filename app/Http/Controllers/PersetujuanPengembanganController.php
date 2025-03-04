<?php

namespace App\Http\Controllers;

use App\Models\PermintaanPengembangan;
use Illuminate\Support\Str;
use App\Models\Persetujuan;
use App\Models\PersetujuanAlasan;
use Illuminate\Http\Request;
use App\Models\PersetujuanPengembangan;
use Illuminate\Support\Facades\DB;
use App\Models\FlagStatus;
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

class PersetujuanPengembanganController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $nomor_dokumen_terpakai = PersetujuanPengembangan::pluck('id_permintaan_pengembangan')->toArray();

        $trx_permintaan_pengembangan = PermintaanPengembangan::whereNotIn('id_permintaan_pengembangan', $nomor_dokumen_terpakai)
                                       ->where('is_approve', 1)
                                       ->pluck('nomor_dokumen', 'id_permintaan_pengembangan');

        $mst_persetujuan = Persetujuan::all()->pluck('nama_persetujuan', 'id_mst_persetujuan');
        $mst_persetujuanalasan = PersetujuanAlasan::all()->pluck('nama_alasan', 'id_mst_persetujuanalasan');

        return view('persetujuan_pengembangan.index', compact('trx_permintaan_pengembangan', 'mst_persetujuan', 'mst_persetujuanalasan'));
    }

    public function data()
    {
        $trx_persetujuan_pengembangan = PersetujuanPengembangan::leftJoin('trx_permintaan_pengembangan', 'trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan')
            ->leftJoin('mst_persetujuan', 'mst_persetujuan.id_mst_persetujuan', '=', 'trx_persetujuan_pengembangan.id_mst_persetujuan')
            ->leftJoin('mst_persetujuanalasan', 'mst_persetujuanalasan.id_mst_persetujuanalasan', '=', 'trx_persetujuan_pengembangan.id_mst_persetujuanalasan')
            ->select(
                'trx_persetujuan_pengembangan.*',
                'trx_permintaan_pengembangan.nomor_dokumen',
                'trx_permintaan_pengembangan.file_pdf as file_pdf_permintaan',
                'mst_persetujuan.nama_persetujuan as status_persetujuan',
                'mst_persetujuanalasan.nama_alasan as alasan_persetujuan'
            );
            // ->orderBy('trx_permintaan_pengembangan.id_permintaan_pengembangan', 'asc')
            if (auth()->user()->level != 1) {
                $trx_persetujuan_pengembangan = $trx_persetujuan_pengembangan
                    ->where('trx_persetujuan_pengembangan.nik_penyetuju', auth()->user()->nik)
                    ->orWhere('trx_persetujuan_pengembangan.created_by', auth()->user()->id);
            }        
            $trx_persetujuan_pengembangan = $trx_persetujuan_pengembangan->get();    

        return datatables()
            ->of($trx_persetujuan_pengembangan)
            ->addIndexColumn()
            ->addColumn('select_all', function ($trx_persetujuan_pengembangan) {
                return '
                    <input type="checkbox" name="id_persetujuan_pengembangan[]" value="'. $trx_persetujuan_pengembangan->id_persetujuan_pengembangan .'">
                ';
            })
            ->addColumn('aksi', function ($trx_persetujuan_pengembangan) {
                $id_persetujuan_pengembangan = $trx_persetujuan_pengembangan->id_persetujuan_pengembangan;
                $isApproved = (int) $trx_persetujuan_pengembangan->is_approve != 1 && empty($trx_persetujuan_pengembangan->file_pdf);
                $isApproved2 = (int) $trx_persetujuan_pengembangan->is_approve == 1 && empty($trx_persetujuan_pengembangan->file_pdf);

                // Menentukan apakah pengguna telah menyetujui
                if(auth()->user()->level == 1){
                    $alreadyApproved = "";
                }
                
                if (auth()->user()->level == 2 || auth()->user()->level == 3) {
                    if (auth()->user()->nik == $trx_persetujuan_pengembangan->nik_penyetuju) {
                        $alreadyApproved = $trx_persetujuan_pengembangan->is_approve == 1;
                        if ($alreadyApproved) {
                            $approveButton = '<button type="button" class="btn btn-xs btn-success btn-flat" disabled><i class="fa fa-check"></i> Approved</button>';
                        } else {
                            $approveButton = '<button type="button" onclick="approveProyek(`'. route('persetujuan_pengembangan.approveProyek', $trx_persetujuan_pengembangan->id_persetujuan_pengembangan) .'`)" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-check"></i> Approve</button>';
                        }
                    } else {
                        $alreadyApproved = false;
                        $approveButton = '';
                    }
                }else{
                    $alreadyApproved = '';
                    $approveButton = '';
                }

                $uploadButton = ($trx_persetujuan_pengembangan->is_approve == 1 && auth()->user()->id == 1)
                        ? '<button onclick="UploadPDF(`'. route('persetujuan_pengembangan.updatePDF', $trx_persetujuan_pengembangan->id_persetujuan_pengembangan) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-upload"></i></button>'
                        : '';

                return '
                        <div class="btn-group">
                            ' . (auth()->user()->level == 1 ? '
                                <button type="button" onclick="deleteData(`' . route('persetujuan_pengembangan.destroy', $trx_persetujuan_pengembangan->id_persetujuan_pengembangan) . '`)" class="btn btn-xs btn-danger btn-flat">
                                    <i class="fa fa-trash"></i>
                                </button>
                                    ' . ($isApproved ? '
                                    <button type="button" onclick="editForm(`' . route('persetujuan_pengembangan.update', $trx_persetujuan_pengembangan->id_persetujuan_pengembangan) . '`)" class="btn btn-xs btn-info btn-flat">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    ': '') . '
                                    ' . ($isApproved2 ? $uploadButton : '') . '
                            ' : '') . '
                            <button onclick="cetakDokumen(`' . route('persetujuan_pengembangan.cetakDokumen', $trx_persetujuan_pengembangan->id_persetujuan_pengembangan) . '`)" class="btn btn-info btn-xs btn-flat">
                                <i class="fa fa-download"></i> Cetak Dokumen
                            </button>
                            ' . (auth()->user()->level == 2 || auth()->user()->level == 3 || auth()->user()->level == 4 ? $approveButton : '') . '
                        </div>
                       ';   

                // Existing 20-10-2024
                // return '
                // <div class="btn-group">
                //     <button type="button" onclick="deleteData(`'. route('persetujuan_pengembangan.destroy', $trx_persetujuan_pengembangan->id_persetujuan_pengembangan) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                //     ' . (!$isApproved ? '
                //     <button type="button" onclick="editForm(`'. route('persetujuan_pengembangan.update', $trx_persetujuan_pengembangan->id_persetujuan_pengembangan) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                //     <button onclick="UploadPDF(`'. route('persetujuan_pengembangan.updatePDF', $trx_persetujuan_pengembangan->id_persetujuan_pengembangan) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-upload"></i></button>
                //     <button onclick="updateProgressForm(`'. route('persetujuan_pengembangan.editProgress', $trx_persetujuan_pengembangan->id_persetujuan_pengembangan) .'`)" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-tasks"></i> Update Progress</button>
                //     ' : '') . '
                //     '. $approveButton .'
                //     <button onclick="cetakDokumen(`'.route('persetujuan_pengembangan.cetakDokumen', $trx_persetujuan_pengembangan->id_persetujuan_pengembangan) .'`)" class="btn btn-info btn-xs btn-flat">
                //         <i class="fa fa-download"></i> Cetak Dokumen
                //     </button>
                //     <button onclick="viewForm(`'. route('persetujuan_pengembangan.view', $trx_persetujuan_pengembangan->id_persetujuan_pengembangan) .'`)" class="btn btn-xs btn-primary btn-flat"><i class="fa fa-eye"></i></button>
                // </div>
                // ';
            })
            ->addColumn('file_pdf', function ($trx_persetujuan_pengembangan) {
                if ($trx_persetujuan_pengembangan->file_pdf) {
                    return '<a href="/storage/assets/pdf/' . $trx_persetujuan_pengembangan->file_pdf . '" target="_blank">Lihat PDF</a>';
                }
                return '-';
            })
            ->addColumn('nomor_dokumen', function ($trx_persetujuan_pengembangan) {
                if ($trx_persetujuan_pengembangan->file_pdf_permintaan) {
                    return '<a href="/storage/assets/pdf/' . $trx_persetujuan_pengembangan->file_pdf_permintaan . '" target="_blank">' . $trx_persetujuan_pengembangan->nomor_dokumen . '</a>';
                }
                return '-';
            })
            ->addColumn('approval_status', function ($trx_permintaan_pengembangan) {
                if ($trx_permintaan_pengembangan->is_approve === 1 && $trx_permintaan_pengembangan->file_pdf != null) {
                    return '<b>File Dokumen Akhir sudah diupload</b>';
                } elseif ($trx_permintaan_pengembangan->is_approve === 1) {
                    return '<b>Sudah di Approve Penyetuju</b>';
                } elseif ($trx_permintaan_pengembangan->is_approve !== 1) {
                    return '<b>Menunggu Approval Penyetuju</b>';
                } else {
                    return '';
                }
            })
            ->editColumn('deskripsi', function($row) {
                return $row->deskripsi; // Data dengan HTML
            })
            ->rawColumns(['aksi', 'select_all','file_pdf', 'deskripsi', 'approval_status', 'nomor_dokumen'])
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
      
        if ($request->hasFile('file_pdf')) {
            $file = $request->file('file_pdf');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('assets/pdf', $filename, 'public');
            $data['file_pdf'] = $filename;
        }

        $data['created_by'] = auth()->user()->id;

        // Content for the QR code
        $qrContent ='Persetujuan Pengembangan, Pemohon :' . $request->nama_pemohon . 
                    ', Nomor Dokumen : ' . $nomor_dokumen->nomor_dokumen .
                    ', NIK Karyawan : ' . $request->nik_pemohon . 
                    ', Tanggal Disetujui : ' . now(); 
    
        // Sanitize nomor_dokumen to remove slashes and create a valid filename
        $safeNomorDokumen = str_replace('/', '', $nomor_dokumen->nomor_dokumen);  // Remove slashes
        $fileName = '' . 'qr_code_persetujuanpengembangan_pemohon_'. $safeNomorDokumen . '.png';
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
                    -- WHERE id_permintaan = $request->id_permintaan_pengembangan AND tpp.progress = 100
                    WHERE id_permintaan = $request->id_permintaan_pengembangan AND tpp.is_approve = 1
                    GROUP BY 
                        flag_status.id_permintaan, 
                        tpp.nomor_dokumen, 
                        tpp.latar_belakang, 
                        tpp.tujuan
                    HAVING 
                        MAX(flag_status.flag) = 1;
        ";

        $result = DB::select($sql_validasi);

        if (count($result) > 0) {
            $trx_persetujuan_pengembangan = PersetujuanPengembangan::create($data);
            $lastId = $trx_persetujuan_pengembangan->id_persetujuan_pengembangan;
            $id_permintaan_pengembangan = $trx_persetujuan_pengembangan->id_permintaan_pengembangan;
    
            FlagStatus::create([
                'kat_modul' => 2,
                'id_permintaan' => $id_permintaan_pengembangan,
                'nama_modul' => "Persetujuan Pengembangan",
                'id_tabel' => $lastId,
                'flag' => 2
            ]);
            
            return response()->json([
                'message' => 'Data berhasil disimpan',
                'lastId' => $lastId
            ], 200);
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
    public function show($id_persetujuan_pengembangan)
    {
        $trx_persetujuan_pengembangan = PersetujuanPengembangan::find($id_persetujuan_pengembangan);

        return response()->json($trx_persetujuan_pengembangan);
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
    public function update(Request $request, $id_persetujuan_pengembangan)
    {
        $trx_persetujuan_pengembangan = PersetujuanPengembangan::find($id_persetujuan_pengembangan);
        $trx_persetujuan_pengembangan->update($request->all());

        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_persetujuan_pengembangan)
    {
        $trx_persetujuan_pengembangan = PersetujuanPengembangan::find($id_persetujuan_pengembangan);
        $trx_persetujuan_pengembangan->delete();

        return response(null, 204);
    }

    public function cetakDokumen(Request $request)
    {
        set_time_limit(300);

        // $ids = $request->id_persetujuan_pengembangan;
        $idPersetujuan = $request->query();
        $id_persetujuan_pengembangan = key($idPersetujuan); 
        $ids = (int) $id_persetujuan_pengembangan;


        $datapersetujuan = PersetujuanPengembangan::leftJoin('trx_permintaan_pengembangan', 'trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan')
            ->leftJoin('mst_persetujuan', 'mst_persetujuan.id_mst_persetujuan', '=', 'trx_persetujuan_pengembangan.id_mst_persetujuan')
            ->leftJoin('mst_persetujuanalasan', 'mst_persetujuanalasan.id_mst_persetujuanalasan', '=', 'trx_persetujuan_pengembangan.id_mst_persetujuanalasan')
            ->select('trx_permintaan_pengembangan.*', 'trx_persetujuan_pengembangan.*', 'mst_persetujuan.nama_persetujuan', 'mst_persetujuanalasan.nama_alasan')
            ->whereIn('trx_persetujuan_pengembangan.id_persetujuan_pengembangan', [$ids])
            ->get();

        $pdf = PDF::loadView('persetujuan_pengembangan.dokumen', compact('datapersetujuan'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('persetujuan.pdf');
    }

    public function getAlasanPersetujuan($id_mst_persetujuan)
    {
        $alasan = PersetujuanAlasan::where('id_mst_persetujuan', $id_mst_persetujuan)->pluck('nama_alasan', 'id_mst_persetujuanalasan');
        return response()->json($alasan);
    }

    public function deleteSelected(Request $request)
    {
        $ids = $request->id_persetujuan_pengembangan;
        PersetujuanPengembangan::whereIn('id_persetujuan_pengembangan', $ids)->delete();
        return response()->json('Data berhasil dihapus', 200);
    }
    public function view($id)
    {
        $trx_persetujuan_pengembangan = PersetujuanPengembangan::leftJoin('trx_permintaan_pengembangan', 'trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan')
        ->leftJoin('mst_persetujuan', 'mst_persetujuan.id_mst_persetujuan', '=', 'trx_persetujuan_pengembangan.id_mst_persetujuan')
        ->leftJoin('mst_persetujuanalasan', 'mst_persetujuanalasan.id_mst_persetujuanalasan', '=', 'trx_persetujuan_pengembangan.id_mst_persetujuanalasan')
        ->select(
            'trx_persetujuan_pengembangan.*',
            'trx_permintaan_pengembangan.nomor_dokumen',
            'mst_persetujuan.nama_persetujuan as status_persetujuan',
            'mst_persetujuanalasan.nama_alasan as alasan_persetujuan'
        )
        ->findOrFail($id);

        return response()->json($trx_persetujuan_pengembangan);
    }
    public function updatePDF(Request $request, $id_persetujuan_pengembangan)
    {
        // Temukan data berdasarkan ID
        $trx_persetujuan_pengembangan = PersetujuanPengembangan::findOrFail($id_persetujuan_pengembangan);

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
            $trx_persetujuan_pengembangan->file_pdf = $filename;

            // Update data di database
            $trx_persetujuan_pengembangan->save();

            return response()->json('File PDF berhasil diperbarui', 200);
        }

        return response()->json('Tidak ada file yang diupload', 400);
    }

    // For Update Progress Project
    public function editProgress($id)
    {

    }

    public function updateProgress(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'progress' => 'required|integer|min:0|max:100', // Validasi progress
            'nomor_dokumen' => 'required|string|max:255', // Validasi nomor dokumen
        ]);

        // Cari data permintaan pengembangan berdasarkan ID
        $trx_persetujuan_pengembangan = PersetujuanPengembangan::findOrFail($id);

        // Update progress
        $trx_persetujuan_pengembangan->progress = $request->progress; // Pastikan ada kolom 'progress' di tabel
        $trx_persetujuan_pengembangan->save(); // Simpan perubahan

        // Kembali dengan respon sukses
        return redirect()->route('persetujuan_pengembangan.index');
    }

    // For Update Progress Project
    public function editApprove($id)
    {
        // Cari data permintaan pengembangan berdasarkan ID
        $trx_persetujuan_pengembangan = PersetujuanPengembangan::leftJoin('trx_permintaan_pengembangan', 'trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan')
            ->leftJoin('mst_persetujuan', 'mst_persetujuan.id_mst_persetujuan', '=', 'trx_persetujuan_pengembangan.id_mst_persetujuan')
            ->leftJoin('mst_persetujuanalasan', 'mst_persetujuanalasan.id_mst_persetujuanalasan', '=', 'trx_persetujuan_pengembangan.id_mst_persetujuanalasan')
            ->select(
                'trx_persetujuan_pengembangan.*',
                'trx_permintaan_pengembangan.nomor_dokumen',
                'mst_persetujuan.nama_persetujuan as status_persetujuan',
                'mst_persetujuanalasan.nama_alasan as alasan_persetujuan'
            )
            ->where('trx_persetujuan_pengembangan.id_persetujuan_pengembangan', $id)
            ->first();

        // Kirim data ke response JSON
        return response()->json($trx_persetujuan_pengembangan);
    }

    // Method untuk melakukan approve proyek
    public function approveProyek($id)
    {
            // Ambil data proyek berdasarkan id
            $proyek = PersetujuanPengembangan::leftJoin('trx_permintaan_pengembangan', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan', '=', 'trx_permintaan_pengembangan.id_permintaan_pengembangan')
                      ->where('trx_persetujuan_pengembangan.id_persetujuan_pengembangan', $id)  // Sesuaikan dengan parameter ID yang Anda inginkan
                      ->select('trx_persetujuan_pengembangan.*', 'trx_permintaan_pengembangan.nomor_dokumen')  // Memilih semua kolom dari kedua tabel
                      ->first();  // Ambil data pertama (karena kita menggunakan where ID)

            // Cek apakah progress sudah 100% dan file_pdf sudah terisi
            // if ($proyek->progress == 100 && !empty($proyek->file_pdf)) {
            // if (!empty($proyek->file_pdf)) {
            // Update status proyek menjadi approved (Anda dapat menambah field status_approval di tabel)
            $proyek->is_approve = 1;
            $proyek->approve_at = now(); // Set tanggal persetujuan saat ini
            $proyek->approve_by = auth()->user()->name; // Set tanggal persetujuan saat ini

            // Content for the QR code
            $qrContent ='Persetujuan Pengembangan, Penyetuju :' . auth()->user()->name . 
                        ', Nomor Dokumen : ' . $proyek->nomor_dokumen .
                        ', NIK Karyawan : ' . $proyek->nik_penyetuju . 
                        ', Tanggal Disetujui : ' . now(); 
            
            // Sanitize nomor_dokumen to remove slashes and create a valid filename
            $safeNomorDokumen = str_replace('/', '', $proyek->nomor_dokumen);  // Remove slashes
            $fileName = '' . 'qr_code_permintaanpengembangan_penyetuju_' . $proyek->id_permintaan_pengembangan . '_' . $safeNomorDokumen . '.png';
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
            // }

        // Jika belum memenuhi syarat approval, kembalikan pesan error
        // return response()->json(['error' => 'Proyek belum memenuhi syarat untuk di-approve.'], 400);
    }

    public function getPersetujuanAlasan($id)
    {
        $alasan = PersetujuanAlasan::where('id_mst_persetujuan', $id)->get(['id', 'nama']);
        return response()->json($alasan);
    }
}


<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\UserAcceptanceTesting;
use App\Models\DetailPengujian;
use App\Models\PermintaanPengembangan;
use App\Models\PerencanaanProyek;
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

class UserAcceptanceTestingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $nama_permintaan_terpakai = UserAcceptanceTesting::pluck('id_permintaan_pengembangan')->toArray();
        // $trx_permintaan_pengembangan = PermintaanPengembangan::whereNotIn('id_permintaan_pengembangan', $nama_permintaan_terpakai)->pluck('nomor_dokumen', 'id_permintaan_pengembangan');
        // Menggunakan select untuk mengambil kolom nomor dokumen dan judul
        $trx_permintaan_pengembangan = PermintaanPengembangan::leftJoin('trx_persetujuan_pengembangan', 'trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan')
            ->whereNotIn('trx_permintaan_pengembangan.id_permintaan_pengembangan', $nama_permintaan_terpakai)
            ->whereIn('trx_permintaan_pengembangan.id_permintaan_pengembangan', function ($query) {
                $query->select('id_permintaan_pengembangan')
                    ->from('trx_persetujuan_pengembangan');
            }) 
            ->select('trx_permintaan_pengembangan.id_permintaan_pengembangan', 'trx_permintaan_pengembangan.nomor_dokumen', 'trx_permintaan_pengembangan.judul'); // Pilih kolom yang diinginkan
        
        // Menambahkan logika if di luar query builder
        if (auth()->user()->level != 1) {
            $trx_permintaan_pengembangan = $trx_permintaan_pengembangan->where('trx_permintaan_pengembangan.created_by', auth()->id());
        }
        
        $trx_permintaan_pengembangan = $trx_permintaan_pengembangan->get(); // Ambil data dalam bentuk koleksi    

        return view('user_acceptance_testing.index', compact('trx_permintaan_pengembangan'));
    }

    public function data()
    {
        $trx_user_acceptance_testing = UserAcceptanceTesting::leftJoin('trx_permintaan_pengembangan', 'trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_user_acceptance_testing.id_permintaan_pengembangan')
        ->leftJoin('trx_persetujuan_pengembangan', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan', '=', 'trx_permintaan_pengembangan.id_permintaan_pengembangan')
        ->leftJoin('trx_perencanaan_proyek', 'trx_perencanaan_proyek.id_persetujuan_pengembangan', '=', 'trx_persetujuan_pengembangan.id_persetujuan_pengembangan')
        ->leftJoin('trx_quality_assurance_testing', 'trx_quality_assurance_testing.id_permintaan_pengembangan', '=', 'trx_permintaan_pengembangan.id_permintaan_pengembangan')
        ->select(
            'trx_user_acceptance_testing.*',
            'trx_quality_assurance_testing.nama_aplikasi',
            'trx_quality_assurance_testing.unit_pemilik',
            'trx_perencanaan_proyek.nomor_proyek',
            'trx_permintaan_pengembangan.id_permintaan_pengembangan',
            'trx_permintaan_pengembangan.jenis_aplikasi'
        );
        if (auth()->user()->level != 1) {
            $trx_user_acceptance_testing = $trx_user_acceptance_testing
                ->where('trx_user_acceptance_testing.nik_penyetuju', auth()->user()->nik)
                ->orWhere('trx_user_acceptance_testing.created_by', auth()->user()->id);
        } 
        $trx_user_acceptance_testing = $trx_user_acceptance_testing->get();

        return datatables()
            ->of($trx_user_acceptance_testing)
            ->addIndexColumn()
            ->addColumn('select_all', function ($trx_user_acceptance_testing) {
                return '
                    <input type="checkbox" name="id_user_acceptance_testing[]" value="'. $trx_user_acceptance_testing->id_user_acceptance_testing .'">
                ';
            })
            ->addColumn('aksi', function ($trx_user_acceptance_testing) {
                // Cek apakah progress sudah 100% dan file PDF sudah terisi
                // $isApproved = $trx_user_acceptance_testing->progress == 100;
                // $alreadyApproved = (int) $trx_user_acceptance_testing->is_approve === 1; // Tambahkan kondisi untuk status approval
                $isApproved = (int) $trx_user_acceptance_testing->is_approve != 1 && empty($trx_user_acceptance_testing->file_pdf);
                $isApproved2 = (int) $trx_user_acceptance_testing->is_approve == 1 && empty($trx_user_acceptance_testing->file_pdf);

                if(auth()->user()->level == 1){
                    $alreadyApproved = "";
                }

                if (auth()->user()->level == 2 || auth()->user()->level == 3 || auth()->user()->level == 5) {
                    if (auth()->user()->nik == $trx_user_acceptance_testing->nik_penyetuju) {
                        $alreadyApproved = $trx_user_acceptance_testing->is_approve == 1;
                        if ($alreadyApproved) {
                            $approveButton = '<button type="button" class="btn btn-xs btn-success btn-flat" disabled><i class="fa fa-check"></i> Approved</button>';
                        } else {
                            $approveButton = '<button type="button" onclick="approveProyek(`'. route('user_acceptance_testing.approveProyek', $trx_user_acceptance_testing->id_user_acceptance_testing) .'`)" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-check"></i> Approve</button>';
                        }
                    } else {
                        $alreadyApproved = false;
                        $approveButton = '';
                    }
                }else{
                    $alreadyApproved = '';
                    $approveButton = '';
                }
                // Ubah teks dan tombol berdasarkan kondisi approval
                // $approveButton = $alreadyApproved
                //     ? '<button type="button" class="btn btn-xs btn-success btn-flat" disabled><i class="fa fa-check"></i> Approved</button>' // Jika sudah di-approve
                //     : '<button type="button" onclick="approveProyek(`'. route('user_acceptance_testing.approveProyek', $trx_user_acceptance_testing->id_user_acceptance_testing) .'`)" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-check"></i> Approve</button>'; // Tampilkan tombol Approve jika belum di-approve
                $uploadButton = ($trx_user_acceptance_testing->is_approve == 1 && auth()->user()->id == 1)
                ? '<button onclick="UploadPDF(`'. route('user_acceptance_testing.updatePDF', $trx_user_acceptance_testing->id_user_acceptance_testing) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-upload"></i></button>'
                : '';
                
                    return '
                    <div class="btn-group">
                        ' . (auth()->user()->level == 1 || auth()->user()->level == 5? '
                            <button type="button" onclick="deleteData(`' . route('user_acceptance_testing.destroy', $trx_user_acceptance_testing->id_user_acceptance_testing) . '`)" class="btn btn-xs btn-danger btn-flat">
                                <i class="fa fa-trash"></i>
                            </button>
                                ' . ($isApproved ? '
                                <button type="button" onclick="editForm(`' . route('user_acceptance_testing.update', $trx_user_acceptance_testing->id_user_acceptance_testing) . '`)" class="btn btn-xs btn-info btn-flat">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <button onclick="UploadPDF(`'. route('user_acceptance_testing.updatePDF', $trx_user_acceptance_testing->id_user_acceptance_testing) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-upload"></i></button>
                                ': '') . '
                                ' . ($isApproved2 ? $uploadButton : '') . '
                        ' : '') . '
                        <button onclick="cetakDokumen(`'.route('user_acceptance_testing.cetakDokumen', $trx_user_acceptance_testing->id_user_acceptance_testing) .'`)" class="btn btn-info btn-xs btn-flat">
                            <i class="fa fa-download"></i> Cetak Dokumen UAT
                        </button>
                        ' . (auth()->user()->level == 2 || auth()->user()->level == 3 || auth()->user()->level == 4 ? $approveButton : '') . '
                    </div>
                   ';     

                // return '
                // <div class="btn-group">
                //     ' . (!$isApproved ? '
                //     <button onclick="editForm(`'. route('user_acceptance_testing.update', $trx_user_acceptance_testing->id_user_acceptance_testing) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                //     <button onclick="UploadPDF(`'. route('user_acceptance_testing.updatePDF', $trx_user_acceptance_testing->id_user_acceptance_testing) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-upload"></i></button>
                //     <button onclick="updateProgressForm(`'. route('user_acceptance_testing.editProgress', $trx_user_acceptance_testing->id_user_acceptance_testing) .'`)" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-tasks"></i> Update Progress</button>
                //     ' : '') . '
                //     '. $approveButton .'
                //     <button onclick="cetakDokumenPerencanaan(`'.route('user_acceptance_testing.cetakDokumenPerencanaan', $trx_user_acceptance_testing->id_user_acceptance_testing) .'`)" class="btn btn-info btn-xs btn-flat">
                //         <i class="fa fa-download"></i>Cetak Dok Perencanaan UAT
                //     </button>
                //     <button onclick="cetakDokumen(`'.route('user_acceptance_testing.cetakDokumen', $trx_user_acceptance_testing->id_user_acceptance_testing) .'`)" class="btn btn-info btn-xs btn-flat">
                //         <i class="fa fa-download"></i> Cetak Dokumen UAT
                //     </button>
                //     <button onclick="deleteData(`'. route('user_acceptance_testing.destroy', $trx_user_acceptance_testing->id_user_acceptance_testing) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                // </div>
                // ';
            })
            ->addColumn('approval_status', function ($trx_user_acceptance_testing) {
                if ($trx_user_acceptance_testing->is_approve === 1 && $trx_user_acceptance_testing->file_pdf != null) {
                    return '<b>File Dokumen Perencanaan UAT sudah diupload</b>';
                }elseif ($trx_user_acceptance_testing->is_approve === 1 && $trx_user_acceptance_testing->file_pdf_2!= null) {
                    return '<b>File Dokumen UAT sudah diupload</b>';
                } elseif ($trx_user_acceptance_testing->is_approve === 1 && $trx_user_acceptance_testing->file_pdf != null && $trx_user_acceptance_testing->file_pdf_2 != null) {
                    return '<b>File Dokumen UAT dan Dokumen Perencanaan UAT sudah diupload</b>';
                } elseif ($trx_user_acceptance_testing->is_approve === 1) {
                    return '<b>Sudah di Approve</b>';
                } elseif ($trx_user_acceptance_testing->is_approve !== 1) {
                    return '<b>Menunggu Approval Penyetuju</b>';
                } else {
                    return '';
                }
            })
            ->editColumn('kebutuhan_fungsional', function($row) {
                return $row->kebutuhan_fungsional; // Data dengan HTML
            })
            ->editColumn('kebutuhan_nonfungsional', function($row) {
                return $row->kebutuhan_nonfungsional; // Data dengan HTML
            })
            ->addColumn('file_pdf', function ($trx_analisis_desain) {
                if ($trx_analisis_desain->file_pdf) {
                    return '<a href="/storage/assets/pdf/' . $trx_analisis_desain->file_pdf . '" target="_blank">
                                <i class="fa fa-file-text" style="font-size: 35px;text-align:center;"></i>
                            </a>';
                }
                return '-';
            })   
            ->addColumn('file_pdf_2', function ($trx_analisis_desain) {
                if ($trx_analisis_desain->file_pdf_2) {
                    return '<a href="/storage/assets/pdf/' . $trx_analisis_desain->file_pdf_2 . '" target="_blank">
                                <i class="fa fa-file-text" style="font-size: 35px;text-align:center;"></i>
                            </a>';
                }
                return '-';
            })       
            ->addColumn('detail_pengisian', function ($trx_user_acceptance_testing) {
                return '<a href="/detail-user-acceptance-testing?id=' . $trx_user_acceptance_testing->id_user_acceptance_testing . '" target="_blank" class="btn btn-primary btn-sm">Detail Pengisian Testing</a>';
            })     
            ->editColumn('jenis_aplikasi', function($row) {
                $jenisaplikasiArray = json_decode($row->jenis_aplikasi, true); // Mengembalikan sebagai array asosiatif
                if (is_array($jenisaplikasiArray)) {
                    return implode(', ', $jenisaplikasiArray);
                }
                return $row->pengguna; // Kembalikan nilai asli jika tidak dalam format array
            })
            ->rawColumns(['aksi', 'select_all', 'approval_status', 'kebutuhan_fungsional', 'kebutuhan_nonfungsional','file_pdf','file_pdf_2','detail_pengisian','jenis_aplikasi'])
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
        // $trx_user_acceptance_testing = UserAcceptanceTesting::create($request->all());
        // $trx_user_acceptance_testing->save();
        // $end_trx_user_acceptance_testing = $trx_user_acceptance_testing->save();
        $data = $request->all();

        // $getNamaProyek = PerencanaanProyek::leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_persetujuan_pengembangan', '=', 'trx_perencanaan_proyek.id_persetujuan_pengembangan')
        // ->leftJoin('trx_permintaan_pengembangan as tpp3', 'tpp2.id_permintaan_pengembangan', '=', 'tpp3.id_permintaan_pengembangan')
        // ->where('tpp3.id_permintaan_pengembangan', $request->id_permintaan_pengembangan)
        // ->select('trx_perencanaan_proyek.nomor_proyek')
        // ->first(); 

        // $data['nomor_proyek'] = $getNamaProyek->nomor_proyek;

        // Cek Validasi Sudah Pengisian Tahap Sebelumnya atau Belum
        $sql_validasi = "SELECT 
                            flag_status.id_permintaan, 
                            tpp.nomor_dokumen, 
                            tpp.latar_belakang, 
                            tpp.tujuan, 
                            MAX(flag_status.flag) AS max_flag
                        FROM 
                            flag_status
                        LEFT JOIN trx_permintaan_pengembangan AS tpp ON tpp.id_permintaan_pengembangan = flag_status.id_permintaan
                        LEFT JOIN trx_quality_assurance_testing AS tqat ON tqat.id_permintaan_pengembangan = tpp.id_permintaan_pengembangan
                        -- WHERE id_permintaan = $request->id_permintaan_pengembangan AND tpk.progress = 100 AND tpk.is_approve = 1 
                        WHERE id_permintaan = $request->id_permintaan_pengembangan AND tqat.is_approve = 1 
                        GROUP BY 
                            flag_status.id_permintaan, 
                            tpp.nomor_dokumen, 
                            tpp.latar_belakang, 
                            tpp.tujuan
                        HAVING 
                            MAX(flag_status.flag) = 7;
                        ";

        $result = DB::select($sql_validasi);

        $data['tanggal_disiapkan'] = now();
        $data['created_by'] = auth()->user()->id;

        // if (count($result) > 0) {
            $trx_user_acceptance_testing = UserAcceptanceTesting::create($data);
            $id_permintaan_pengembangan = $trx_user_acceptance_testing->id_permintaan_pengembangan;
            $lastId = $trx_user_acceptance_testing->id_user_acceptance_testing;

            FlagStatus::create([
                'kat_modul' => 8,
                'id_permintaan' => $id_permintaan_pengembangan,
                'nama_modul' => "User Acceptance Testing",
                'id_tabel' => $lastId,
                'flag' => 8
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
    public function show($id)
    {
        $trx_user_acceptance_testing = UserAcceptanceTesting::find($id);

        return response()->json($trx_user_acceptance_testing);
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
    public function update(Request $request, $id_user_acceptance_testing)
    {
        $trx_user_acceptance_testing = UserAcceptanceTesting::find($id_user_acceptance_testing);
        $trx_user_acceptance_testing->update($request->all());

        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_user_acceptance_testing)
    {
        $trx_user_acceptance_testing = UserAcceptanceTesting::find($id_user_acceptance_testing);
        $trx_user_acceptance_testing->delete();

        return response(null, 204);
    }

    public function deleteSelected(Request $request)
    {
        $ids = $request->id_user_acceptance_testing;
        UserAcceptanceTesting::whereIn('id_user_acceptance_testing', $ids)->delete();
        return response()->json('Data berhasil dihapus', 200);
    }

    public function cetakDokumen(Request $request)
    {
        set_time_limit(300);

        // $dataUserAcceptanceTesting = UserAcceptanceTesting::whereIn('id_user_acceptance_testing', $request->id_user_acceptance_testing)->get();
        $idUserAcceptanceTesting = $request->query();
        $id_user_acceptance_testing = key($idUserAcceptanceTesting); 
        $id_user_acceptance_testing = (int) $id_user_acceptance_testing;

        $dataUserAcceptanceTesting = UserAcceptanceTesting::leftJoin('trx_permintaan_pengembangan','trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_user_acceptance_testing.id_permintaan_pengembangan')
        ->leftJoin('trx_quality_assurance_testing','trx_quality_assurance_testing.id_permintaan_pengembangan', '=', 'trx_user_acceptance_testing.id_permintaan_pengembangan')
        ->select([
            'trx_permintaan_pengembangan.nomor_dokumen', 
            'trx_quality_assurance_testing.nama_aplikasi', 
            'trx_permintaan_pengembangan.jenis_aplikasi', 
            'trx_user_acceptance_testing.nama_penyetuju', 
            'trx_user_acceptance_testing.tanggal_pengujian', 
            'trx_user_acceptance_testing.path_qrcode_penyetuju', 
        ])
        ->where('trx_user_acceptance_testing.id_user_acceptance_testing', [$id_user_acceptance_testing])
        ->get();

        $detailUAT = DetailPengujian::whereIn('id_user_acceptance_testing', [$id_user_acceptance_testing])->get();

        $no  = 1;

        $pdf = PDF::loadView('user_acceptance_testing.dokumen_rev', compact('dataUserAcceptanceTesting', 'no', 'detailUAT'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('User Acceptance Testing (UAT).pdf');
    }

    public function cetakDokumenPerencanaan(Request $request)
    {
        set_time_limit(300);

        // $dataUserAcceptanceTesting = UserAcceptanceTesting::whereIn('id_user_acceptance_testing', $request->id_user_acceptance_testing)->get();
        $idUserAcceptanceTesting = $request->query();
        $id_user_acceptance_testing = key($idUserAcceptanceTesting); 
        $id_user_acceptance_testing = (int) $id_user_acceptance_testing;

        $dataUserAcceptanceTesting = UserAcceptanceTesting::leftJoin('trx_permintaan_pengembangan','trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_user_acceptance_testing.id_permintaan_pengembangan')
        ->select([
            'trx_permintaan_pengembangan.nomor_dokumen', 
            'trx_user_acceptance_testing.nama_aplikasi', 
            'trx_user_acceptance_testing.jenis_aplikasi', 
            'trx_user_acceptance_testing.kebutuhan_fungsional', 
            'trx_user_acceptance_testing.unit_pemilik_proses_bisnis', 
            'trx_user_acceptance_testing.lokasi_pengujian', 
            'trx_user_acceptance_testing.tgl_pengujian', 
            'trx_user_acceptance_testing.manual_book', 
            'trx_user_acceptance_testing.jabatan_penyusun', 
            'trx_user_acceptance_testing.jabatan_penyetuju', 
            'trx_user_acceptance_testing.tgl_disusun', 
            'trx_user_acceptance_testing.tanggal_disetujui'
        ])
        ->get();

        $no  = 1;

        $pdf = PDF::loadView('user_acceptance_testing.dokumenperencanaan', compact('dataUserAcceptanceTesting', 'no'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('Perencanaan User Acceptance Testing (UAT).pdf');
    }

    // For Update Progress Project
    public function editProgress($id)
    {
        $trx_user_acceptance_testing = UserAcceptanceTesting::leftJoin('trx_permintaan_pengembangan', 'trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_user_acceptance_testing.id_permintaan_pengembangan')
            ->select(
                'trx_user_acceptance_testing.id_permintaan_pengembangan', 
                'trx_permintaan_pengembangan.nomor_dokumen', 
                'trx_user_acceptance_testing.progress',
            )
            ->where('trx_user_acceptance_testing.id_user_acceptance_testing', $id)
            ->first();

        // Kirim data ke response JSON
        return response()->json($trx_user_acceptance_testing);
    }

    public function updateProgress(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'progress' => 'required|integer|min:0|max:100', // Validasi progress
            'nomor_dokumen' => 'required|string|max:255', // Validasi nomor dokumen
        ]);

        // Cari data permintaan pengembangan berdasarkan ID
        $trx_user_acceptance_testing = UserAcceptanceTesting::findOrFail($id);

        // Update progress
        $trx_user_acceptance_testing->progress = $request->progress; // Pastikan ada kolom 'progress' di tabel
        $trx_user_acceptance_testing->save(); // Simpan perubahan

        // Kembali dengan respon sukses
        return redirect()->route('user_acceptance_testing.index');
    }

    // Method untuk melakukan approve proyek
    public function approveProyek($id)
    {
        // Ambil data proyek berdasarkan id
        $proyek = UserAcceptanceTesting::findOrFail($id);

        // Cek apakah progress sudah 100% dan file_pdf sudah terisi
        // if (!empty($proyek->file_pdf)) {
        // if ($proyek->progress == 100) {
            // Update status proyek menjadi approved (Anda dapat menambah field status_approval di tabel)
            $proyek->is_approve = 1;
            $proyek->approve_at = now(); // Set tanggal persetujuan saat ini
            $proyek->approve_by = auth()->user()->id; // Set tanggal persetujuan saat ini
            $proyek->tanggal_disetujui = now(); // Set tanggal persetujuan saat ini
                    // Content for the QR code
            $qrContent ='User Acceptance Testing, Penyetuju :' . auth()->user()->name . 
                        ', Nomor Dokumen : ' . $proyek->nomor_dokumen .
                        ', NIK Karyawan : ' . $proyek->nik_penyetuju . 
                        ', Tanggal Disetujui : ' . now(); 
            
            // Sanitize nomor_dokumen to remove slashes and create a valid filename
            $safeNomorDokumen = str_replace('/', '', $proyek->nomor_dokumen);  // Remove slashes
            $fileName = '' . 'qr_code_useracceptancetesting_penyetuju_' . $proyek->id_permintaan_pengembangan . '_' . $safeNomorDokumen . '.png';
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
        return response()->json(['error' => 'Proyek belum memenuhi syarat untuk di-approve.'], 400);
    }

    public function updatePDF(Request $request, $id_user_acceptance_testing)
    {
        // Temukan data berdasarkan ID
        $trx_user_acceptance_testing = UserAcceptanceTesting::findOrFail($id_user_acceptance_testing);

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
            $trx_user_acceptance_testing->file_pdf = $filename;

            // Update data di database
            $trx_user_acceptance_testing->save();

            return response()->json('File PDF berhasil diperbarui', 200);
        }

        return response()->json('Tidak ada file yang diupload', 400);
    }

    public function updatePDF2(Request $request, $id_user_acceptance_testing)
    {
        // Temukan data berdasarkan ID
        $trx_user_acceptance_testing = UserAcceptanceTesting::findOrFail($id_user_acceptance_testing);

        // Validasi input file
        $request->validate([
            'file_pdf_2' => 'required|file|mimes:pdf|max:2048', // Aturan validasi untuk file PDF
        ]);

        // Periksa apakah ada file PDF yang diupload
        if ($request->hasFile('file_pdf_2')) {
            $file = $request->file('file_pdf_2');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->move(public_path('storage/assets/pdf2'), $filename);

            // Simpan nama file baru ke kolom `file_pdf`
            $trx_user_acceptance_testing->file_pdf_2 = $filename;

            // Update data di database
            $trx_user_acceptance_testing->save();

            return response()->json('File PDF berhasil diperbarui', 200);
        }

        return response()->json('Tidak ada file yang diupload', 400);
    }
}

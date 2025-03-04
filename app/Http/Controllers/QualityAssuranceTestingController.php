<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\QualityAssuranceTesting;
use App\Models\DetailQAT;
use App\Models\DetailPengujiQAT;
use App\Models\PermintaanPengembangan;
use App\Models\PerencanaanProyek;
use App\Models\MasterUnit;
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

class QualityAssuranceTestingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $nama_permintaan_terpakai = QualityAssuranceTesting::pluck('id_permintaan_pengembangan')->toArray();
        $master_unit = MasterUnit::pluck('unit_id','unit_name')->toArray();
        // $trx_permintaan_pengembangan = PermintaanPengembangan::whereNotIn('id_permintaan_pengembangan', $nama_permintaan_terpakai)->pluck('nomor_dokumen', 'id_permintaan_pengembangan');
        // Menggunakan select untuk mengambil kolom nomor dokumen dan judul
        $trx_permintaan_pengembangan = PermintaanPengembangan::leftJoin('trx_persetujuan_pengembangan', 'trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan')
        ->whereNotIn('trx_permintaan_pengembangan.id_permintaan_pengembangan', $nama_permintaan_terpakai)
        ->whereIn('trx_permintaan_pengembangan.id_permintaan_pengembangan', function ($query) {
            $query->select('id_permintaan_pengembangan')
                ->from('trx_persetujuan_pengembangan');
        })
        // Menambahkan logika if untuk level 1
        ->when(auth()->user()->level != 1, function ($query) {
            return $query->where('trx_permintaan_pengembangan.created_by', auth()->id());
        })
        ->select('trx_permintaan_pengembangan.id_permintaan_pengembangan', 'trx_permintaan_pengembangan.nomor_dokumen', 'trx_permintaan_pengembangan.judul') // Pilih kolom yang diinginkan
        ->get(); // Ambil data dalam bentuk koleksi    
        
        return view('quality_assurance_testing.index', compact('trx_permintaan_pengembangan','master_unit'));
    }

    public function index2()
    {
        $nama_permintaan_terpakai = QualityAssuranceTesting::pluck('id_permintaan_pengembangan')->toArray();
        $master_unit = MasterUnit::pluck('unit_id','unit_name')->toArray();
        // $trx_permintaan_pengembangan = PermintaanPengembangan::whereNotIn('id_permintaan_pengembangan', $nama_permintaan_terpakai)->pluck('nomor_dokumen', 'id_permintaan_pengembangan');
        $trx_permintaan_pengembangan = PermintaanPengembangan::leftJoin('trx_persetujuan_pengembangan', 'trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan')
        ->whereNotIn('trx_permintaan_pengembangan.id_permintaan_pengembangan', $nama_permintaan_terpakai)
        ->whereIn('trx_permintaan_pengembangan.id_permintaan_pengembangan', function ($query) {
            $query->select('id_permintaan_pengembangan')
                ->from('trx_persetujuan_pengembangan');
        })
        ->pluck('trx_permintaan_pengembangan.nomor_dokumen', 'trx_permintaan_pengembangan.id_permintaan_pengembangan');
        
        return view('quality_assurance_testing.index2', compact('trx_permintaan_pengembangan','master_unit'));
    }

    public function data()
    {
        // $trx_quality_assurance_testing = QualityAssuranceTesting::orderBy('id_quality_assurance_testing', 'desc')->get();

        $trx_quality_assurance_testing = QualityAssuranceTesting::leftJoin('trx_permintaan_pengembangan', 'trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_quality_assurance_testing.id_permintaan_pengembangan')
        ->leftJoin('trx_persetujuan_pengembangan', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan', '=', 'trx_permintaan_pengembangan.id_permintaan_pengembangan')
        ->leftJoin('trx_perencanaan_proyek', 'trx_perencanaan_proyek.id_persetujuan_pengembangan', '=', 'trx_persetujuan_pengembangan.id_persetujuan_pengembangan')
        ->select(
            'trx_quality_assurance_testing.*',
            'trx_perencanaan_proyek.nomor_proyek',
            'trx_permintaan_pengembangan.id_permintaan_pengembangan',
            'trx_permintaan_pengembangan.jenis_aplikasi'
        );   
        if (auth()->user()->level != 1) {
            $trx_quality_assurance_testing = $trx_quality_assurance_testing
                ->where('trx_quality_assurance_testing.nik_penyetuju', auth()->user()->nik)
                ->orWhere('trx_quality_assurance_testing.created_by', auth()->user()->id);
        } 
        $trx_quality_assurance_testing = $trx_quality_assurance_testing
            ->orderByRaw("trx_quality_assurance_testing.is_approve asc,trx_quality_assurance_testing.approve_at desc, trx_quality_assurance_testing.created_at desc")
            ->get();
        return datatables()
            ->of($trx_quality_assurance_testing)
            ->addIndexColumn()
            ->addColumn('select_all', function ($trx_quality_assurance_testing) {
                return '
                    <input type="checkbox" name="id_quality_assurance_testing[]" value="'. $trx_quality_assurance_testing->id_quality_assurance_testing .'">
                ';
            })
            ->addColumn('aksi', function ($trx_quality_assurance_testing) {
                // Cek apakah progress sudah 100% dan file PDF sudah terisi
                $isApproved = (int) $trx_quality_assurance_testing->is_approve != 1 && empty($trx_quality_assurance_testing->file_pdf);
                $isApproved2 = (int) $trx_quality_assurance_testing->is_approve == 1 && empty($trx_quality_assurance_testing->file_pdf);

                // Menentukan apakah pengguna telah menyetujui
                if(auth()->user()->level == 1){
                    $alreadyApproved = "";
                }
                
                if (auth()->user()->level == 2 || auth()->user()->level == 3) {
                    if (auth()->user()->nik == $trx_quality_assurance_testing->nik_penyetuju) {
                        $alreadyApproved = $trx_quality_assurance_testing->is_approve == 1;
                        if ($alreadyApproved) {
                            $approveButton = '<button type="button" class="btn btn-xs btn-success btn-flat" disabled><i class="fa fa-check"></i> Approved</button>';
                        } else {
                            $approveButton = '<button type="button" onclick="approveProyek(`'. route('quality_assurance_testing.approveProyek', $trx_quality_assurance_testing->id_quality_assurance_testing) .'`)" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-check"></i> Approve</button>';
                        }
                    } else {
                        $alreadyApproved = false;
                        $approveButton = '';
                    }
                }else{
                    $alreadyApproved = '';
                    $approveButton = '';
                }

                $uploadButton = ($trx_quality_assurance_testing->is_approve == 1 && auth()->user()->id == 1)
                        ? '<button onclick="UploadPDF(`'. route('quality_assurance_testing.updatePDF', $trx_quality_assurance_testing->id_quality_assurance_testing) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-upload"></i></button>'
                        : '';

                return '
                        <div class="btn-group">
                            ' . (auth()->user()->level == 1 || auth()->user()->level == 5 ? '
                                <button type="button" onclick="deleteData(`' . route('quality_assurance_testing.destroy', $trx_quality_assurance_testing->id_quality_assurance_testing) . '`)" class="btn btn-xs btn-danger btn-flat">
                                    <i class="fa fa-trash"></i>
                                </button>
                                    ' . ($isApproved ? '
                                    <button type="button" onclick="editForm(`' . route('quality_assurance_testing.update', $trx_quality_assurance_testing->id_quality_assurance_testing) . '`)" class="btn btn-xs btn-info btn-flat">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    ': '') . '
                                    ' . ($isApproved2 ? $uploadButton : '') . '
                            ' : '') . '
                            <button onclick="cetakDokumen(`' . route('quality_assurance_testing.cetakDokumen', $trx_quality_assurance_testing->id_quality_assurance_testing) . '`)" class="btn btn-info btn-xs btn-flat">
                                <i class="fa fa-download"></i> Cetak Dokumen
                            </button>
                            ' . (auth()->user()->level == 2 || auth()->user()->level == 3 || auth()->user()->level == 4 ? $approveButton : '') . '
                        </div>
                       ';   
            })
            ->addColumn('file_pdf', function ($trx_quality_assurance_testing) {
                if ($trx_quality_assurance_testing->file_pdf) {
                    return '<a href="/storage/assets/pdf/' . $trx_quality_assurance_testing->file_pdf . '" target="_blank">Lihat PDF</a>';
                }
                return '-';
            })
            ->addColumn('approval_status', function ($trx_quality_assurance_testing) {
                if ($trx_quality_assurance_testing->is_approve === 1 && $trx_quality_assurance_testing->file_pdf != null) {
                    return '<b>File Dokumen Akhir sudah diupload</b>';
                } elseif ($trx_quality_assurance_testing->is_approve === 1) {
                    return '<b>Sudah di Approve Penyetuju</b>';
                } elseif ($trx_quality_assurance_testing->is_approve !== 1) {
                    return '<b>Menunggu Approval Penyetuju</b>';
                } else {
                    return '';
                }
            })
            ->editColumn('jenis_aplikasi', function($row) {
                $jenisaplikasiArray = json_decode($row->jenis_aplikasi, true); // Mengembalikan sebagai array asosiatif
                if (is_array($jenisaplikasiArray)) {
                    return implode(', ', $jenisaplikasiArray);
                }
                return $row->pengguna; // Kembalikan nilai asli jika tidak dalam format array
            })
            ->addColumn('detail_pengisian', function ($trx_quality_assurance_testing) {
                if ($trx_quality_assurance_testing->is_approve == 1) {
                    // If approved, return the disabled button
                    return '<button class="btn btn-primary btn-sm" disabled>Detail Pengisian Testing</button>';
                } else {
                    // If not approved, return the normal button with a link
                    return '<a href="/detail-quality-assurance-testing?id=' . $trx_quality_assurance_testing->id_quality_assurance_testing . '" target="_blank" class="btn btn-primary btn-sm">Detail Pengisian Testing</a>';
                }
            })
            ->addColumn('detail_ttd', function ($trx_quality_assurance_testing) {
                // Check if the 'is_approve' is 1 (approved)
                if ($trx_quality_assurance_testing->is_approve == 1) {
                    // If approved, return the disabled button
                    return '<button class="btn btn-primary btn-sm" disabled>Detail TTD</button>';
                } else {
                    // If not approved, return the normal button with a link
                    return '<a href="/detail-ttd-qat?id=' . $trx_quality_assurance_testing->id_quality_assurance_testing . '" target="_blank" class="btn btn-primary btn-sm">Detail TTD</a>';
                }
            })
            ->rawColumns(['aksi', 'select_all','file_pdf','approval_status','jenis_aplikasi','detail_pengisian','detail_ttd'])
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

        // $getNamaProyek = PerencanaanProyek::leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_persetujuan_pengembangan', '=', 'trx_perencanaan_proyek.id_persetujuan_pengembangan')
        // ->leftJoin('trx_permintaan_pengembangan as tpp3', 'tpp2.id_permintaan_pengembangan', '=', 'tpp3.id_permintaan_pengembangan')
        // ->where('tpp3.id_permintaan_pengembangan', $request->id_permintaan_pengembangan)
        // ->select('trx_perencanaan_proyek.nomor_proyek')
        // ->first(); 

        // $data['nomor_proyek'] = $getNamaProyek->nomor_proyek;
        // $data['jenis_aplikasi'] = json_encode($request->input('jenis_aplikasi'));

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
                        -- LEFT JOIN trx_pengembangan_aplikasi AS tpk ON tpk.id_permintaan_pengembangan = tpp.id_permintaan_pengembangan
                        LEFT JOIN trx_analisis_desain AS tad ON tad.id_permintaan_pengembangan = tpp.id_permintaan_pengembangan
                        -- WHERE id_permintaan = $request->id_permintaan_pengembangan AND tpk.progress = 100 AND tpk.is_approve = 1 
                        WHERE id_permintaan = $request->id_permintaan_pengembangan AND tad.is_approve = 1 
                        GROUP BY 
                            flag_status.id_permintaan, 
                            tpp.nomor_dokumen, 
                            tpp.latar_belakang, 
                            tpp.tujuan
                        HAVING 
                            MAX(flag_status.flag) = 5;
                        ";

        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->move(public_path('storage/assets/lampiran'), $filename);
            $data['lampiran'] = $filename;
        } else {
            $data['lampiran'] = null;
        }

        if ($request->hasFile('file_pdf')) {
            $file = $request->file('file_pdf');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('assets/pdf', $filename, 'public');
            $data['file_pdf'] = $filename;
        }

        $data['tanggal_disiapkan'] = now();
        $data['created_by'] = auth()->user()->id;

        // Content for the QR code
        $qrContent ='Quality Assurance Testing, Pemohon :' . $request->nama_pemohon . 
                    ', Nomor Dokumen : ' . $nomor_dokumen->nomor_dokumen .
                    ', NIK Karyawan : ' . $request->nik_pemohon . 
                    ', Tanggal Disetujui : ' . now(); 
    
        // Sanitize nomor_dokumen to remove slashes and create a valid filename
        $safeNomorDokumen = str_replace('/', '', $nomor_dokumen->nomor_dokumen);  // Remove slashes
        $fileName = '' . 'qr_code_qualityassurancetesting_pemohon_'. $safeNomorDokumen . '.png';
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
            $trx_quality_assurance_testing = QualityAssuranceTesting::create($data);
            $id_permintaan_pengembangan = $trx_quality_assurance_testing->id_permintaan_pengembangan;
            $lastId = $trx_quality_assurance_testing->id_quality_assurance_testing;

            FlagStatus::create([
                'kat_modul' => 7,
                'id_permintaan' => $id_permintaan_pengembangan,
                'nama_modul' => "Quality Assurance Testing",
                'id_tabel' => $lastId,
                'flag' => 7
            ]);

            return response()->json('Data berhasil disimpan', 200);
        }else{
            echo "Tambah Data Gagal, Karena Anda Melewati Tahapan Sebelumnya";
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
        $trx_quality_assurance_testing = QualityAssuranceTesting::find($id);

        return response()->json($trx_quality_assurance_testing);
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
    public function update(Request $request, $id_quality_assurance_testing)
    {
        $trx_quality_assurance_testing = QualityAssuranceTesting::findOrFail($id_quality_assurance_testing);
        // Simpan data lainnya yang tidak terkait dengan file
        $data = $request->except(['lampiran', 'file_pdf']);  // Semua data kecuali file

        // Proses file lampiran jika ada
        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->move(public_path('storage/assets/lampiran/'), $filename);
            $data['lampiran'] = $filename;
        } else {
            // Jika tidak ada file baru, biarkan data yang lama tetap ada
            if (!$request->has('lampiran')) {
                $data['lampiran'] = $trx_quality_assurance_testing->lampiran;
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
                $data['file_pdf'] = $trx_quality_assurance_testing->file_pdf;
            }
        }

        $trx_quality_assurance_testing->update($data);
        return response()->json('Data berhasil diperbarui', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_quality_assurance_testing)
    {
        $trx_quality_assurance_testing = QualityAssuranceTesting::find($id_quality_assurance_testing);
        $trx_quality_assurance_testing->delete();

        return response(null, 204);
    }

    public function deleteSelected(Request $request)
    {
        $ids = $request->id_quality_assurance_testing;
        QualityAssuranceTesting::whereIn('id_quality_assurance_testing', $ids)->delete();
        return response()->json('Data berhasil dihapus', 200);
    }

    public function cetakDokumen(Request $request)
    {
        set_time_limit(300);

        // $dataQAT = QualityAssuranceTesting::whereIn('id_quality_assurance_testing', $request->id_quality_assurance_testing)->get();
        $no  = 1;
        
        $idQualityAssuranceTesting = $request->query();
        $id_quality_assurance_testing = key($idQualityAssuranceTesting); 
        $id_quality_assurance_testing = (int) $id_quality_assurance_testing;

        $dataQAT  = QualityAssuranceTesting::join('trx_permintaan_pengembangan', 'trx_quality_assurance_testing.id_permintaan_pengembangan', '=', 'trx_permintaan_pengembangan.id_permintaan_pengembangan')
                    ->join('trx_persetujuan_pengembangan', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan', '=', 'trx_permintaan_pengembangan.id_permintaan_pengembangan')
                    ->join('trx_perencanaan_kebutuhan', 'trx_perencanaan_kebutuhan.id_persetujuan_pengembangan', '=', 'trx_persetujuan_pengembangan.id_persetujuan_pengembangan')
                    ->whereIn('trx_quality_assurance_testing.id_quality_assurance_testing', [$id_quality_assurance_testing])
                    ->select('trx_quality_assurance_testing.*', 'trx_permintaan_pengembangan.nomor_dokumen', 'trx_permintaan_pengembangan.jenis_aplikasi', 'trx_perencanaan_kebutuhan.kebutuhan_nonfungsional')
                    ->get();
        $detailQAT = DetailQAT::whereIn('id_quality_assurance_testing', [$id_quality_assurance_testing])->get();
        $detailPengujiQAT = DetailPengujiQAT::whereIn('id_quality_assurance_testing', [$id_quality_assurance_testing])->get();

        $pdf = PDF::loadView('quality_assurance_testing.dokumen_rev', compact('dataQAT', 'detailQAT', 'detailPengujiQAT', 'no'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('Quality Assurance Testing (QAT).pdf');
    }

    public function updatePDF(Request $request, $id_quality_assurance_testing)
    {
        // Temukan data berdasarkan ID
        $trx_quality_assurance_testing = QualityAssuranceTesting::findOrFail($id_quality_assurance_testing);

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
            $trx_quality_assurance_testing->file_pdf = $filename;

            // Update data di database
            $trx_quality_assurance_testing->save();

            return response()->json('File PDF berhasil diperbarui', 200);
        }

        return response()->json('Tidak ada file yang diupload', 400);
    }

    // For Update Progress Project
    public function editProgress($id)
    {
        $trx_quality_assurance_testing = QualityAssuranceTesting::leftJoin('trx_permintaan_pengembangan', 'trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_quality_assurance_testing.id_permintaan_pengembangan')
        ->leftJoin('trx_persetujuan_pengembangan', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan', '=', 'trx_permintaan_pengembangan.id_permintaan_pengembangan')
        ->select(
            'trx_quality_assurance_testing.*',
            'trx_permintaan_pengembangan.id_permintaan_pengembangan',
            'trx_permintaan_pengembangan.nomor_dokumen',
        )
        ->where('trx_quality_assurance_testing.id_quality_assurance_testing', $id)
        ->first();

        // Kirim data ke response JSON
        return response()->json($trx_quality_assurance_testing);
    }

    public function updateProgress(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'progress' => 'required|integer|min:0|max:100', // Validasi progress
            'nomor_dokumen' => 'required|string|max:255', // Validasi nomor dokumen
        ]);

        // Cari data permintaan pengembangan berdasarkan ID
        $trx_quality_assurance_testing = QualityAssuranceTesting::findOrFail($id);

        // Update progress
        $trx_quality_assurance_testing->progress = $request->progress; // Pastikan ada kolom 'progress' di tabel
        $trx_quality_assurance_testing->save(); // Simpan perubahan

        // Kembali dengan respon sukses
        return redirect()->route('quality_assurance_testing.index');
    }

    // Method untuk melakukan approve proyek
    public function approveProyek($id)
    {
        // Ambil data proyek berdasarkan id
        $proyek = QualityAssuranceTesting::findOrFail($id);

        // Cek apakah progress sudah 100% dan file_pdf sudah terisi
        // if (!empty($proyek->file_pdf)) {
        // if ($proyek->progress == 100) {
            // Update status proyek menjadi approved (Anda dapat menambah field status_approval di tabel)
            $proyek->is_approve = 1;
            $proyek->approve_at = now(); // Set tanggal persetujuan saat ini
            $proyek->approve_by = auth()->user()->id; // Set tanggal persetujuan saat ini
            $proyek->tanggal_disetujui = now(); // Set tanggal persetujuan saat ini
                    // Content for the QR code
            $qrContent ='Quality Assurance Testing, Penyetuju :' . auth()->user()->name . 
                        ', Nomor Dokumen : ' . $proyek->nomor_dokumen .
                        ', NIK Karyawan : ' . $proyek->nik_penyetuju . 
                        ', Tanggal Disetujui : ' . now(); 
            
            // Sanitize nomor_dokumen to remove slashes and create a valid filename
            $safeNomorDokumen = str_replace('/', '', $proyek->nomor_dokumen);  // Remove slashes
            $fileName = '' . 'qr_code_qualityassurancetesting_penyetuju_' . $proyek->id_permintaan_pengembangan . '_' . $safeNomorDokumen . '.png';
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
}

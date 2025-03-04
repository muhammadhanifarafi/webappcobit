<?php

namespace App\Http\Controllers;

use App\Models\PengembanganAplikasi;
use App\Models\PerencanaanProyek;
use App\Models\PermintaanPengembangan;
use App\Models\FlagStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PengembanganAplikasiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $nama_permintaan_terpakai = PengembanganAplikasi::pluck('id_permintaan_pengembangan')->toArray();
        // $trx_permintaan_pengembangan = PermintaanPengembangan::whereNotIn('id_permintaan_pengembangan', $nama_permintaan_terpakai)->pluck('nomor_dokumen', 'id_permintaan_pengembangan');
        $trx_permintaan_pengembangan = PermintaanPengembangan::leftJoin('trx_persetujuan_pengembangan', 'trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan')
        ->whereNotIn('trx_permintaan_pengembangan.id_permintaan_pengembangan', $nama_permintaan_terpakai)
        ->whereIn('trx_permintaan_pengembangan.id_permintaan_pengembangan', function ($query) {
            $query->select('id_permintaan_pengembangan')
                ->from('trx_persetujuan_pengembangan');
        })
        ->pluck('trx_permintaan_pengembangan.nomor_dokumen', 'trx_permintaan_pengembangan.id_permintaan_pengembangan');
        
        return view('pengembangan_aplikasi.index', compact('trx_permintaan_pengembangan'));
    }

    public function data()
    {
        $trx_pengembangan_aplikasi = PengembanganAplikasi::leftJoin('trx_permintaan_pengembangan', 'trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_pengembangan_aplikasi.id_permintaan_pengembangan')
        ->leftJoin('trx_persetujuan_pengembangan', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan', '=', 'trx_permintaan_pengembangan.id_permintaan_pengembangan')
        ->leftJoin('trx_perencanaan_proyek', 'trx_perencanaan_proyek.id_persetujuan_pengembangan', '=', 'trx_persetujuan_pengembangan.id_persetujuan_pengembangan')
        ->select(
            'trx_pengembangan_aplikasi.id_pengembangan_aplikasi', 
            'trx_permintaan_pengembangan.nomor_dokumen',
            'trx_permintaan_pengembangan.file_pdf as file_pdf_permintaan',
            'trx_perencanaan_proyek.file_pdf as file_pdf_proyek',
            'trx_perencanaan_proyek.nomor_proyek',
            'trx_pengembangan_aplikasi.memo',
            'trx_pengembangan_aplikasi.is_approve',
        )
        ->orderBy('trx_pengembangan_aplikasi.id_pengembangan_aplikasi', 'asc')
        ->get();

        return datatables()
            ->of($trx_pengembangan_aplikasi)
            ->addIndexColumn()
            ->addColumn('select_all', function ($trx_pengembangan_aplikasi) {
                return '
                    <input type="checkbox" name="id_pengembangan_aplikasi[]" value="'. $trx_pengembangan_aplikasi->id_pengembangan_aplikasi .'">
                ';
            })
            ->addColumn('aksi', function ($trx_pengembangan_aplikasi) {
                // Cek apakah progress sudah 100% dan file PDF sudah terisi
                // $isApproved = $trx_pengembangan_aplikasi->progress == 100 && !empty($trx_pengembangan_aplikasi->file_pdf);
                $alreadyApproved = (int) $trx_pengembangan_aplikasi->is_approve == 1; // Tambahkan kondisi untuk status approval
                // Ubah teks dan tombol berdasarkan kondisi approval
                $approveButton = $alreadyApproved
                    ? '<button type="button" class="btn btn-xs btn-success btn-flat" disabled><i class="fa fa-check"></i> Approved</button>' // Jika sudah di-approve
                    : '<button type="button" onclick="approveProyek(`'. route('pengembangan_aplikasi.approveProyek', $trx_pengembangan_aplikasi->id_pengembangan_aplikasi) .'`)" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-check"></i> Approve</button>'; // Tampilkan tombol Approve tanpa pengecekan $isApproved                

                    // <button onclick="cetakDokumen(`'.route('pengembangan_aplikasi.cetakDokumen', $trx_pengembangan_aplikasi->id_pengembangan_aplikasi) .'`)" class="btn btn-info btn-xs btn-flat">
                    //     <i class="fa fa-download"></i> Cetak Dokumen
                    // </button>
                    // <button onclick="UploadPDF(`'. route('pengembangan_aplikasi.updatePDF', $trx_pengembangan_aplikasi->id_pengembangan_aplikasi) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-upload"></i></button>
                    // <button onclick="viewForm(`'. route('pengembangan_aplikasi.view', $trx_pengembangan_aplikasi->id_pengembangan_aplikasi) .'`)" class="btn btn-xs btn-primary btn-flat"><i class="fa fa-eye"></i></button>
                return '
                <div class="btn-group">
                    <button onclick="deleteData(`'. route('pengembangan_aplikasi.destroy', $trx_pengembangan_aplikasi->id_pengembangan_aplikasi) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                    ' . (!$alreadyApproved ? '
                    <button onclick="editForm(`'. route('pengembangan_aplikasi.update', $trx_pengembangan_aplikasi->id_pengembangan_aplikasi) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                    ' : '') . '
                    '. $approveButton .'
                </div>
                ';

                // return '
                // <div class="btn-group">
                //     <button onclick="deleteData(`'. route('pengembangan_aplikasi.destroy', $trx_pengembangan_aplikasi->id_pengembangan_aplikasi) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                //     ' . (!$isApproved ? '
                //     <button onclick="editForm(`'. route('pengembangan_aplikasi.update', $trx_pengembangan_aplikasi->id_pengembangan_aplikasi) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                //     <button onclick="updateProgressForm(`'. route('pengembangan_aplikasi.editProgress', $trx_pengembangan_aplikasi->id_pengembangan_aplikasi) .'`)" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-tasks"></i> Update Progress</button>
                //     ' : '') . '
                //     '. $approveButton .'
                // </div>
                // ';
            })
            ->addColumn('nomor_dokumen', function ($trx_pengembangan_aplikasi) {
                if ($trx_pengembangan_aplikasi->file_pdf_permintaan) {
                    return '<a href="/storage/assets/pdf/' . $trx_pengembangan_aplikasi->file_pdf_permintaan . '" target="_blank">' . $trx_pengembangan_aplikasi->nomor_dokumen . '</a>';
                }
                return '-';
            })
            ->addColumn('nomor_proyek', function ($trx_pengembangan_aplikasi) {
                if ($trx_pengembangan_aplikasi->file_pdf_proyek) {
                    return '<a href="/storage/assets/pdf/' . $trx_pengembangan_aplikasi->file_pdf_proyek . '" target="_blank">' . $trx_pengembangan_aplikasi->nomor_proyek . '</a>';
                }
                return '-';
            })
            ->rawColumns(['aksi', 'select_all','nomor_dokumen','nomor_proyek'])
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
                        LEFT JOIN trx_analisis_desain AS tad ON tad.id_permintaan_pengembangan 
                                = tpp.id_permintaan_pengembangan
                        -- WHERE id_permintaan = $request->id_permintaan_pengembangan AND tad.progress = 100 AND tad.is_approve = 1
                        WHERE id_permintaan = $request->id_permintaan_pengembangan AND tad.is_approve = 1
                        GROUP BY 
                            flag_status.id_permintaan, 
                            tpp.nomor_dokumen, 
                            tpp.latar_belakang, 
                            tpp.tujuan
                        HAVING 
                            MAX(flag_status.flag) = 5;
                        ";

        $result = DB::select($sql_validasi);

        // if ($request->hasFile('memo')) {
        //     $file = $request->file('memo');
        //     $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
        //     $path = $file->move(public_path('storage/assets/memo'), $filename);
        //     $data['memo'] = $filename;
        // }
        
        if ($request->hasFile('file_pdf')) {
            $file = $request->file('file_pdf');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('assets/pdf', $filename, 'public');
            $data['file_pdf'] = $filename;
        }

        if (count($result) > 0) {
            $trx_pengembangan_aplikasi = PengembanganAplikasi::create($data);
            $id_permintaan_pengembangan = $trx_pengembangan_aplikasi->id_permintaan_pengembangan;
            $lastId = $trx_pengembangan_aplikasi->id_pengembangan_aplikasi;

            FlagStatus::create([
                'kat_modul' => 6,
                'id_permintaan' => $id_permintaan_pengembangan,
                'nama_modul' => "Pengembangan Aplikasi",
                'id_tabel' => $lastId,
                'flag' => 6
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
    public function show($id_pengembangan_aplikasi)
    {
        $trx_pengembangan_aplikasi = PengembanganAplikasi::find($id_pengembangan_aplikasi);

        return response()->json($trx_pengembangan_aplikasi);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id_pengembangan_aplikasi)
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
    public function update(Request $request, $id_pengembangan_aplikasi)
    {
        $trx_pengembangan_aplikasi = PengembanganAplikasi::find($id_pengembangan_aplikasi)->update($request->all());

        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_pengembangan_aplikasi)
    {
        $trx_pengembangan_aplikasi = PengembanganAplikasi::find($id_pengembangan_aplikasi);
        $trx_pengembangan_aplikasi->delete();

        return response(null, 204);
    }

    public function cetakDokumen(Request $request)
    {
        set_time_limit(300);

        // $dataanalisis = PengembanganAplikasi::whereIn('id_pengembangan_aplikasi', $request->id_pengembangan_aplikasi)->get();

        $idPerencanaanKebutuhan = $request->query();
        $id_pengembangan_aplikasi = key($idPerencanaanKebutuhan); 
        $id_pengembangan_aplikasi = (int) $id_pengembangan_aplikasi;

        $dataanalisis = PengembanganAplikasi::whereIn('id_pengembangan_aplikasi', [$id_pengembangan_aplikasi])->get();
        $no  = 1;
        $pdf = PDF::loadView('pengembangan_aplikasi.dokumen', compact('dataanalisis', 'no'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('analisis.pdf');
    }

    public function deleteSelected(Request $request)
    {
        $ids = $request->id_pengembangan_aplikasi;
        PengembanganAplikasi::whereIn('id_pengembangan_aplikasi', $ids)->delete();
        return response()->json('Data berhasil dihapus', 200);
    }

    public function view($id)
    {
        $trx_pengembangan_aplikasi = PengembanganAplikasi::leftJoin('trx_permintaan_pengembangan', 'trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_pengembangan_aplikasi.id_permintaan_pengembangan')
        ->leftJoin('trx_persetujuan_pengembangan', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan', '=', 'trx_permintaan_pengembangan.id_permintaan_pengembangan')
        ->select(
            'trx_pengembangan_aplikasi.id_pengembangan_aplikasi', 
            'trx_persetujuan_pengembangan.memo'
        )
        ->where('trx_pengembangan_aplikasi.id_pengembangan_aplikasi', $id) // Menggunakan kondisi where pada id_perencanaan_proyek
        ->first(); // Mengambil satu hasil pertama dari query

        return response()->json($trx_pengembangan_aplikasi);
    }

    public function updatePDF(Request $request, $id_pengembangan_aplikasi)
    {
        // Temukan data berdasarkan ID
        $trx_pengembangan_aplikasi = PengembanganAplikasi::findOrFail($id_pengembangan_aplikasi);

        // Validasi input file
        $request->validate([
            'file_pdf' => 'required|file|mimes:pdf|max:2048', // Aturan validasi untuk file PDF
        ]);

        // Periksa apakah ada file PDF yang diupload
        if ($request->hasFile('file_pdf')) {
            $file = $request->file('file_pdf');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('assets/pdf', $filename, 'public');

            // Simpan nama file baru ke kolom `file_pdf`
            $trx_pengembangan_aplikasi->file_pdf = $filename;

            // Update data di database
            $trx_pengembangan_aplikasi->save();

            return response()->json('File PDF berhasil diperbarui', 200);
        }

        return response()->json('Tidak ada file yang diupload', 400);
    }

    // For Update Progress Project
    public function editProgress($id)
    {
        $trx_pengembangan_aplikasi = PengembanganAplikasi::leftJoin('trx_permintaan_pengembangan', 'trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_pengembangan_aplikasi.id_permintaan_pengembangan')
            ->leftJoin('trx_persetujuan_pengembangan', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan', '=', 'trx_permintaan_pengembangan.id_permintaan_pengembangan')
            ->select(
                'trx_pengembangan_aplikasi.id_pengembangan_aplikasi', 
                'trx_permintaan_pengembangan.memo'
            )
            ->where('trx_pengembangan_aplikasi.id_pengembangan_aplikasi', $id)
            ->first();

        // Kirim data ke response JSON
        return response()->json($trx_pengembangan_aplikasi);
    }

    public function updateProgress(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'progress' => 'required|integer|min:0|max:100', // Validasi progress
            'nomor_dokumen' => 'required|string|max:255', // Validasi nomor dokumen
        ]);

        // Cari data permintaan pengembangan berdasarkan ID
        $trx_pengembangan_aplikasi = PengembanganAplikasi::findOrFail($id);

        // Update progress
        $trx_pengembangan_aplikasi->progress = $request->progress; // Pastikan ada kolom 'progress' di tabel
        $trx_pengembangan_aplikasi->save(); // Simpan perubahan

        // Kembali dengan respon sukses
        return redirect()->route('pengembangan_aplikasi.index');
    }

    // Method untuk melakukan approve proyek
    public function approveProyek($id)
    {
        // Ambil data proyek berdasarkan id
        $proyek = PengembanganAplikasi::findOrFail($id);

        // Cek apakah progress sudah 100% dan file_pdf sudah terisi
        // if ($proyek->progress == 100 && !empty($proyek->file_pdf)) {
        // if (!empty($proyek->file_pdf)) {
            // Update status proyek menjadi approved (Anda dapat menambah field status_approval di tabel)
            $proyek->is_approve = 1;
            $proyek->approve_at = now(); // Set tanggal persetujuan saat ini
            $proyek->approve_by = auth()->user()->name; // Set tanggal persetujuan saat ini
            $proyek->save();

            return response()->json(['success' => 'Proyek berhasil di-approve.']);
        // }

        // Jika belum memenuhi syarat approval, kembalikan pesan error
        return response()->json(['error' => 'Proyek belum memenuhi syarat untuk di-approve.'], 400);
    }
}
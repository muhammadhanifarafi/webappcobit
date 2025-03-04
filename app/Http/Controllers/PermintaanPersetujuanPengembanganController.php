<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\PermintaanPersetujuanPengembangan;
use App\Models\FlagStatus;
use Barryvdh\DomPDF\Facade\Pdf;

class PermintaanPersetujuanPengembanganController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('permintaan_persetujuan_pengembangan.index');
    }

    public function data()
    {
        $trx_permintaan_persetujuan_pengembangan = PermintaanPersetujuanPengembangan::orderBy('id_permintaan_pengembangan', 'asc')->get();

        return datatables()
            ->of($trx_permintaan_persetujuan_pengembangan)
            ->addIndexColumn()
            ->addColumn('select_all', function ($trx_permintaan_persetujuan_pengembangan) {
                return '
                    <input type="checkbox" name="id_permintaan_pengembangan[]" value="'. $trx_permintaan_persetujuan_pengembangan->id_permintaan_pengembangan .'">
                ';
            })
            ->addColumn('aksi', function ($trx_permintaan_persetujuan_pengembangan) {
                $id_permintaan_pengembangan = $trx_permintaan_persetujuan_pengembangan->id_permintaan_pengembangan;
                // Cek apakah progress sudah 100% dan file PDF sudah terisi
                // $isApproved = $trx_permintaan_persetujuan_pengembangan->progress == 100 && !empty($trx_permintaan_persetujuan_pengembangan->file_pdf);
                $isApproved = !empty($trx_permintaan_persetujuan_pengembangan->file_pdf);
                $alreadyApproved = (int) $trx_permintaan_persetujuan_pengembangan->is_approve === 1; // Tambahkan kondisi untuk status approval
                // Ubah teks dan tombol berdasarkan kondisi approval
                $approveButton = $alreadyApproved
                    ? '<button type="button" class="btn btn-xs btn-success btn-flat" disabled><i class="fa fa-check"></i> Approved</button>' // Jika sudah di-approve
                    : ($isApproved 
                        ? '<button type="button" onclick="approveProyek(`'. route('permintaan_persetujuan_pengembangan.approveProyek', $id_permintaan_pengembangan) .'`)" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-check"></i> Approve</button>'
                        : ''); // Jika belum memenuhi syarat approval, tampilkan tombol Approve

                return '
                <div class="btn-group">
                    <button onclick="deleteData(`'. route('permintaan_persetujuan_pengembangan.destroy', $trx_permintaan_persetujuan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                    ' . (!$isApproved ? '
                    <button onclick="editForm(`'. route('permintaan_persetujuan_pengembangan.update', $trx_permintaan_persetujuan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                    <button onclick="UploadPDFPermintaan(`'. route('permintaan_persetujuan_pengembangan.updatePDF', $trx_permintaan_persetujuan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-upload">Upload PDF Permintaan</i></button>
                    ' : '') . '
                    '. $approveButton .'
                    <button onclick="cetakDokumen(`'.route('permintaan_persetujuan_pengembangan.cetakDokumen', $trx_permintaan_persetujuan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-info btn-xs btn-flat">
                        <i class="fa fa-download"></i> Cetak Dokumen
                    </button>
                    <button onclick="viewForm(`'. route('permintaan_persetujuan_pengembangan.view', $trx_permintaan_persetujuan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-xs btn-primary btn-flat"><i class="fa fa-eye"></i></button>
                </div>
                ';

                // return '
                // <div class="btn-group">
                //     <button onclick="deleteData(`'. route('permintaan_pengembangan.destroy', $trx_permintaan_persetujuan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                //     ' . (!$isApproved ? '
                //     <button onclick="editForm(`'. route('permintaan_pengembangan.update', $trx_permintaan_persetujuan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                //     <button onclick="updateProgressForm(`'. route('permintaan_pengembangan.editProgress', $trx_permintaan_persetujuan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-tasks"></i> Update Progress</button>
                //     ' : '') . '
                //     '. $approveButton .'
                //     <button onclick="cetakDokumen(`'.route('permintaan_pengembangan.cetakDokumen', $trx_permintaan_persetujuan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-info btn-xs btn-flat">
                //         <i class="fa fa-download"></i> Cetak Dokumen
                //     </button>
                //     <button onclick="UploadPDF(`'. route('permintaan_pengembangan.updatePDF', $trx_permintaan_persetujuan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-upload"></i></button>
                //     <button onclick="viewForm(`'. route('permintaan_pengembangan.view', $trx_permintaan_persetujuan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-xs btn-primary btn-flat"><i class="fa fa-eye"></i></button>
                // </div>
                // ';
            })
            ->addColumn('file_pdf', function ($trx_permintaan_persetujuan_pengembangan) {
                if ($trx_permintaan_persetujuan_pengembangan->file_pdf) {
                    return '<a href="/storage/assets/pdf/' . $trx_permintaan_persetujuan_pengembangan->file_pdf . '" target="_blank">Lihat PDF</a>';
                }
                return '-';
            })
            ->editColumn('latar_belakang', function($row) {
                return $row->latar_belakang; // Data dengan HTML
            })
            ->editColumn('tujuan', function($row) {
                return $row->tujuan; // Data dengan HTML jika ada
            })
            ->editColumn('jenis_aplikasi', function($row) {
                $jenisaplikasiArray = json_decode($row->jenis_aplikasi, true); // Mengembalikan sebagai array asosiatif
                if (is_array($jenisaplikasiArray)) {
                    return implode(', ', $jenisaplikasiArray);
                }
                return $row->pengguna; // Kembalikan nilai asli jika tidak dalam format array
            })
            ->editColumn('pengguna', function($row) {
                $penggunaArray = json_decode($row->pengguna, true); // Mengembalikan sebagai array asosiatif
                if (is_array($penggunaArray)) {
                    return implode(', ', $penggunaArray);
                }
                return $row->pengguna; // Kembalikan nilai asli jika tidak dalam format array
            })
            ->rawColumns(['aksi', 'select_all','file_pdf','latar_belakang', 'tujuan'])
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
        $data = [
            'latar_belakang' => $request->input('latar_belakang'),
            'tujuan' => $request->input('tujuan'),
            'target_implementasi_sistem' => $request->input('target_implementasi_sistem'),
            'fungsi_sistem_informasi' => $request->input('fungsi_sistem_informasi'),
            'jenis_aplikasi' => json_encode($request->input('jenis_aplikasi')),
            'pengguna' => json_encode($request->input('pengguna')),
            'uraian_permintaan_tambahan' => $request->input('uraian_permintaan_tambahan'),
            'lampiran' => json_encode($request->input('lampiran')),
            'pic' => $request->input('pic'),
            'nama_pemohon' => $request->input('nama_pemohon'),
            'jabatan_pemohon' => $request->input('jabatan_pemohon'),
            'tanggal_disiapkan' => $request->input('tanggal_disiapkan'),
            'nama' => $request->input('nama_penyetuju'),
            'jabatan' => $request->input('jabatan_penyetuju'),
            'tanggal_disetujui' => $request->input('tanggal_disetujui')
        ];

        $lastNoDocument = PermintaanPersetujuanPengembangan::orderBy('nomor_dokumen', 'desc')->first();

        if (!$lastNoDocument || date('Y') != date('Y', strtotime($lastNoDocument->created_at))) {
            $number = 1;
        } else {
            $number = intval($lastNoDocument->nomor_dokumen) + 1;
        }
        
        $formattedNumber = str_pad($number, 4, '0', STR_PAD_LEFT);
        
        $months = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $currentMonth = $months[date('n') - 1];
        
        $currentYear = date('Y');
        
        $data['nomor_dokumen'] = "{$formattedNumber}/{$currentMonth}/DTI/{$currentYear}";      

        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();

            $path = $file->storeAs('assets/lampiran', $filename, 'public');

            $data['lampiran'] = $filename;
        }

        if ($request->hasFile('file_pdf')) {
            $file = $request->file('file_pdf');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('assets/pdf', $filename, 'public');
            $data['file_pdf'] = $filename;
        }

        $trx_permintaan_persetujuan_pengembangan = PermintaanPersetujuanPengembangan::create($data);
        $lastId = $trx_permintaan_persetujuan_pengembangan->id_permintaan_pengembangan;

        FlagStatus::create([
            'kat_modul' => 1,
            'id_permintaan' => $lastId,
            'nama_modul' => "Permintaan Pengembangan",
            'id_tabel' => $lastId,
            'flag' => 1
        ]);

        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $trx_permintaan_persetujuan_pengembangan = PermintaanPersetujuanPengembangan::find($id);

        return response()->json($trx_permintaan_persetujuan_pengembangan);
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

    public function editProgress($id)
    {
        // Cari data permintaan pengembangan berdasarkan ID
        $PermintaanPersetujuanPengembangan = PermintaanPersetujuanPengembangan::findOrFail($id);

        // Kirim data ke response JSON
        return response()->json($PermintaanPersetujuanPengembangan);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_permintaan_pengembangan)
    {
        $trx_permintaan_persetujuan_pengembangan = PermintaanPersetujuanPengembangan::findOrFail($id_permintaan_pengembangan);
        $data = $request->all();

        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');

            $path = $file->storeAs('assets/lampiran', $file->getClientOriginalName(), 'public');

            $data['lampiran'] = 'storage/' . $path;
        }

        $trx_permintaan_persetujuan_pengembangan->update($data);
        return response()->json('Data berhasil diperbarui', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_permintaan_pengembangan)
    {
        $trx_permintaan_persetujuan_pengembangan = PermintaanPersetujuanPengembangan::find($id_permintaan_pengembangan);
        $trx_permintaan_persetujuan_pengembangan->delete();

        return response(null, 204);
    }

    public function deleteSelected(Request $request)
    {
        $ids = $request->id_permintaan_pengembangan;
        PermintaanPersetujuanPengembangan::whereIn('id_permintaan_pengembangan', $ids)->delete();
        return response()->json('Data berhasil dihapus', 200);
    }

    public function cetakDokumen(Request $request)
    {
        set_time_limit(300);

        $idPermintaan = $request->query();
        $id_permintaan_pengembangan = key($idPermintaan); 
        $id_permintaan_pengembangan = (int) $id_permintaan_pengembangan;
        
        $datapermintaan = PermintaanPersetujuanPengembangan::whereIn('id_permintaan_pengembangan', [$id_permintaan_pengembangan])->get();
        $no  = 1;

        $pdf = PDF::loadView('permintaan_pengembangan.dokumen', compact('datapermintaan', 'no'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('permintaan.pdf');
    }
    public function view($id)
    {
        $trx_permintaan_persetujuan_pengembangan = PermintaanPersetujuanPengembangan::findOrFail($id);
        return response()->json($trx_permintaan_persetujuan_pengembangan);
    }

    public function updatePDFPermintaan(Request $request, $id_permintaan_pengembangan)
    {
        // Temukan data berdasarkan ID
        $trx_permintaan_persetujuan_pengembangan = PermintaanPersetujuanPengembangan::findOrFail($id_permintaan_pengembangan);

        // Validasi input file
        $request->validate([
            'file_pdf_permintaan' => 'required|file|mimes:pdf|max:2048', // Aturan validasi untuk file PDF
        ]);

        // Periksa apakah ada file PDF yang diupload
        if ($request->hasFile('file_pdf_permintaan')) {
            $file = $request->file('file_pdf_permintaan');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('assets/pdf', $filename, 'public');

            // Simpan nama file baru ke kolom `file_pdf_permintaan`
            $trx_permintaan_persetujuan_pengembangan->file_pdf_permintaan = $filename;

            // Update data di database
            $trx_permintaan_persetujuan_pengembangan->save();

            return response()->json('File PDF berhasil diperbarui', 200);
        }

        return response()->json('Tidak ada file yang diupload', 400);
    }

    public function updatePDFPersetujuan(Request $request, $id_permintaan_pengembangan)
    {
        // Temukan data berdasarkan ID
        $trx_permintaan_persetujuan_pengembangan = PermintaanPersetujuanPengembangan::findOrFail($id_permintaan_pengembangan);

        // Validasi input file
        $request->validate([
            'file_pdf_persetujuan' => 'required|file|mimes:pdf|max:2048', // Aturan validasi untuk file PDF
        ]);

        // Periksa apakah ada file PDF yang diupload
        if ($request->hasFile('file_pdf_persetujuan')) {
            $file = $request->file('file_pdf_persetujuan');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('assets/pdf', $filename, 'public');

            // Simpan nama file baru ke kolom `file_pdf_persetujuan`
            $trx_permintaan_persetujuan_pengembangan->file_pdf_persetujuan = $filename;

            // Update data di database
            $trx_permintaan_persetujuan_pengembangan->save();

            return response()->json('File PDF berhasil diperbarui', 200);
        }

        return response()->json('Tidak ada file yang diupload', 400);
    }

    public function updateProgress(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'progress' => 'required|integer|min:0|max:100', // Validasi progress
            'nomor_dokumen' => 'required|string|max:255', // Validasi nomor dokumen
        ]);

        // Cari data permintaan pengembangan berdasarkan ID
        $PermintaanPersetujuanPengembangan = PermintaanPersetujuanPengembangan::findOrFail($id);

        // Update progress
        $PermintaanPersetujuanPengembangan->progress = $request->progress; // Pastikan ada kolom 'progress' di tabel
        $PermintaanPersetujuanPengembangan->save(); // Simpan perubahan

        // Kembali dengan respon sukses
        return redirect()->route('permintaan_pengembangan.index');
    }

    // Method untuk melakukan approve proyek
    public function approveProyek($id)
    {
        // Ambil data proyek berdasarkan id
        $proyek = PermintaanPersetujuanPengembangan::findOrFail($id);

        // Cek apakah progress sudah 100% dan file_pdf sudah terisi
        // if ($proyek->progress == 100 && !empty($proyek->file_pdf)) {
        if (!empty($proyek->file_pdf)) {
            // Update status proyek menjadi approved (Anda dapat menambah field status_approval di tabel)
            if(auth()->user()->level == 2){
                $proyek->is_approve = 1;
                $proyek->approve_at = now(); // Set tanggal persetujuan saat ini
                $proyek->approve_by = auth()->user()->id; // Set tanggal persetujuan saat ini
                $proyek->save();
    
                return response()->json(['success' => 'Proyek berhasil di-approve.']);
            }else if(auth()->user()->level == 3){
                $proyek->is_approve_penyetuju = 1;
                $proyek->approve_at_penyetuju = now(); // Set tanggal persetujuan saat ini
                $proyek->approve_by_penyetuju = auth()->user()->id; // Set tanggal persetujuan saat ini
                $proyek->save();

                FlagStatus::create([
                    'kat_modul' => 2,
                    'id_permintaan' => $proyek->id_permintaan_pengembangan,
                    'nama_modul' => "Persetujuan Pengembangan",
                    'id_tabel' => $proyek->id_permintaan_pengembangan,
                    'flag' => 2
                ]);

                return response()->json(['success' => 'Proyek berhasil di-approve.']);
            }
            
            // $proyek->is_approve = 1;
            // $proyek->approve_at = now(); // Set tanggal persetujuan saat ini
            // $proyek->approve_by = auth()->user()->name; // Set tanggal persetujuan saat ini

            // $folderPath = storage_path('app/public/signatures/'); // create signatures folder in public directory
            // $image_parts = explode(";base64,", $request->signed);
            // $image_type_aux = explode("image/", $image_parts[0]);
            // $image_type = $image_type_aux[1];
            // $image_base64 = base64_decode($image_parts[1]);
            // $file = $folderPath . uniqid() . '.' . $image_type;
            // file_put_contents($file, $image_base64);
        }

        // Jika belum memenuhi syarat approval, kembalikan pesan error
        return response()->json(['error' => 'Proyek belum memenuhi syarat untuk di-approve.'], 400);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\PermintaanPengembangan;
use App\Models\Persetujuan;
use App\Models\PersetujuanAlasan;
use App\Models\PersetujuanPengembangan;
use App\Models\PerencanaanProyek;
use App\Models\PerencanaanKebutuhan;
use App\Models\AnalisisDesain;
use App\Models\QualityAssuranceTesting;
use App\Models\UserAcceptanceTesting;
use App\Models\SerahTerimaAplikasi;
use App\Models\Users;
use App\Models\Setting;
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

class PermintaanPengembanganController extends Controller
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
    // Approved
    public function index()
    {
        $mst_persetujuan = Persetujuan::all()->pluck('nama_persetujuan', 'id_mst_persetujuan');
        $mst_persetujuanalasan = PersetujuanAlasan::all()->pluck('nama_alasan', 'id_mst_persetujuanalasan');

        return view('permintaan_pengembangan.index', compact('mst_persetujuan', 'mst_persetujuanalasan'));
    }

    // Pending Approval
    public function index2()
    {
        $mst_persetujuan = Persetujuan::all()->pluck('nama_persetujuan', 'id_mst_persetujuan');
        $mst_persetujuanalasan = PersetujuanAlasan::all()->pluck('nama_alasan', 'id_mst_persetujuanalasan');

        return view('permintaan_pengembangan.index2', compact('mst_persetujuan', 'mst_persetujuanalasan'));
    }

    // For VP Akses (Different Page)
    public function index3()
    {
        $mst_persetujuan = Persetujuan::all()->pluck('nama_persetujuan', 'id_mst_persetujuan');
        $mst_persetujuanalasan = PersetujuanAlasan::all()->pluck('nama_alasan', 'id_mst_persetujuanalasan');

        return view('permintaan_pengembangan.index3', compact('mst_persetujuan', 'mst_persetujuanalasan'));
    }

    // Form Permintaan Pengembangan
    public function forminputpengembangan()
    {
        $mst_persetujuan = Persetujuan::all()->pluck('nama_persetujuan', 'id_mst_persetujuan');
        $mst_persetujuanalasan = PersetujuanAlasan::all()->pluck('nama_alasan', 'id_mst_persetujuanalasan');

        return view('permintaan_pengembangan.form', compact('mst_persetujuan', 'mst_persetujuanalasan'));
    }

    public function data()
    {
        $trx_permintaan_pengembangan = PermintaanPengembangan::orderByRaw("trx_permintaan_pengembangan.is_approve asc, trx_permintaan_pengembangan.approve_at desc, trx_permintaan_pengembangan.created_at desc");
        $trx_permintaan_pengembangan = $trx_permintaan_pengembangan->where(function($query) {
            $query->where('is_approve', '=', 1);
        });
        if (auth()->user()->level == 5) {
            if(auth()->user()->level == 2 || auth()->user()->level == 3 || auth()->user()->level == 4 || auth()->user()->level == 6){
                $trx_permintaan_pengembangan = $trx_permintaan_pengembangan->where('nik_penyetuju', auth()->user()->nik);
            }
            $trx_permintaan_pengembangan = $trx_permintaan_pengembangan->where('created_by', auth()->user()->id);
        }    
            
        $trx_permintaan_pengembangan = $trx_permintaan_pengembangan->get();
        
        return datatables()
            ->of($trx_permintaan_pengembangan)
            ->addIndexColumn()
            ->addColumn('select_all', function ($trx_permintaan_pengembangan) {
                return '
                    <input type="checkbox" name="id_permintaan_pengembangan[]" value="'. $trx_permintaan_pengembangan->id_permintaan_pengembangan .'">
                ';
            })
            ->addColumn('aksi', function ($trx_permintaan_pengembangan) {
                $id_permintaan_pengembangan = $trx_permintaan_pengembangan->id_permintaan_pengembangan;
                $isApproved = (int) $trx_permintaan_pengembangan->is_approve != 1 && empty($trx_permintaan_pengembangan->file_pdf);
                $isApproved2 = (int) $trx_permintaan_pengembangan->is_approve == 1 && empty($trx_permintaan_pengembangan->file_pdf);
                
                // Menentukan apakah pengguna telah menyetujui
                if(auth()->user()->level == 1){
                    $alreadyApproved = "";
                }
                
                if (auth()->user()->level == 2 || auth()->user()->level == 3) {
                    if (auth()->user()->nik == $trx_permintaan_pengembangan->nik_penyetuju) {
                        $alreadyApproved = $trx_permintaan_pengembangan->is_approve == 1;
                        if ($alreadyApproved) {
                            $approveButton = '<button type="button" class="btn btn-xs btn-success btn-flat" disabled><i class="fa fa-check"></i> Approved</button>';
                        } else {
                            $approveButton = '<button type="button" onclick="approveProyek(`'. route('permintaan_pengembangan.approveProyek', $trx_permintaan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-check"></i> Approve</button>';
                        }
                    } else {
                        $alreadyApproved = false;
                        $approveButton = '';
                    }
                }else{
                    $alreadyApproved = '';
                    $approveButton = '';
                }

                $uploadButton = ($trx_permintaan_pengembangan->is_approve == 1 && auth()->user()->id == 1)
                        ? '<button onclick="UploadPDF(`'. route('permintaan_pengembangan.updatePDF', $trx_permintaan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-upload"></i></button>'
                        : '';

                $persetujuanExists = DB::table('trx_persetujuan_pengembangan')
                                     ->where('id_permintaan_pengembangan', $trx_permintaan_pengembangan->id_permintaan_pengembangan)
                                     ->exists();

                return '
                        <div class="btn-group">
                            ' . (auth()->user()->level == 1 || auth()->user()->level == 5 ? '
                                <button type="button" onclick="deleteData(`' . route('permintaan_pengembangan.destroy', $trx_permintaan_pengembangan->id_permintaan_pengembangan) . '`)" class="btn btn-xs btn-danger btn-flat">
                                    <i class="fa fa-trash"></i>
                                </button>
                                    ' . ($isApproved ? '
                                    <button type="button" onclick="editForm(`' . route('permintaan_pengembangan.update', $trx_permintaan_pengembangan->id_permintaan_pengembangan) . '`)" class="btn btn-xs btn-info btn-flat">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    ': '') . '
                                    ' . ($isApproved2 ? $uploadButton : '') . '
                            ' : '') . '
                            <button onclick="cetakDokumen(`' . route('permintaan_pengembangan.cetakDokumen', $trx_permintaan_pengembangan->id_permintaan_pengembangan) . '`)" class="btn btn-info btn-xs btn-flat">
                                <i class="fa fa-download"></i> Cetak Dokumen <br> Permintaan Pengembangan
                            </button>         
                            ' . ($persetujuanExists ? '
                            <button onclick="cetakDokumenPersetujuan(`' . route('permintaan_pengembangan.cetakDokumenPersetujuan', $trx_permintaan_pengembangan->id_permintaan_pengembangan) . '`)" class="btn btn-info btn-xs btn-flat">
                                <i class="fa fa-download"></i> Cetak Dokumen <br> Persetujuan Pengembangan
                            </button>
                            ' : '') . '
                            <button type="button" onclick="viewForm(`' . route('permintaan_pengembangan.view', $trx_permintaan_pengembangan->id_permintaan_pengembangan) . '`)" class="btn btn-xs btn-primary btn-flat">
                                <i class="fa fa-eye"></i>
                            </button>
                            ' . (auth()->user()->level == 2 || auth()->user()->level == 3 || auth()->user()->level == 4 ? $approveButton : '') . '
                        </div>
                       ';   

                // return '
                // <div class="btn-group">
                //     <button onclick="deleteData(`'. route('permintaan_pengembangan.destroy', $trx_permintaan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                //     ' . (!$isApproved ? '
                //     <button onclick="editForm(`'. route('permintaan_pengembangan.update', $trx_permintaan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                //     <button onclick="updateProgressForm(`'. route('permintaan_pengembangan.editProgress', $trx_permintaan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-tasks"></i> Update Progress</button>
                //     ' : '') . '
                //     '. $approveButton .'
                //     <button onclick="cetakDokumen(`'.route('permintaan_pengembangan.cetakDokumen', $trx_permintaan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-info btn-xs btn-flat">
                //         <i class="fa fa-download"></i> Cetak Dokumen
                //     </button>
                //     <button onclick="UploadPDF(`'. route('permintaan_pengembangan.updatePDF', $trx_permintaan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-upload"></i></button>
                //     <button onclick="viewForm(`'. route('permintaan_pengembangan.view', $trx_permintaan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-xs btn-primary btn-flat"><i class="fa fa-eye"></i></button>
                // </div>
                // ';
            })
            ->addColumn('file_pdf', function ($trx_permintaan_pengembangan) {
                if ($trx_permintaan_pengembangan->file_pdf) {
                    return '<a href="/public/storage/assets/pdf/' . $trx_permintaan_pengembangan->file_pdf . '" target="_blank">Lihat PDF</a>';
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
            ->addColumn('unit_pemohon', function ($trx_permintaan_pengembangan) {
                $unit_pemohon = DB::table('sitmsemployee')
                                ->where('employee_id', $trx_permintaan_pengembangan->nik_pemohon)
                                ->first();

                return $unit_pemohon->working_unit_name;
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
            ->addColumn('user_created', function ($trx_permintaan_pengembangan) {
                $unit_pemohon = DB::table('users')
                                ->where('id', $trx_permintaan_pengembangan->created_by)
                                ->first();

                return $unit_pemohon->name;
            })   
            ->rawColumns(['aksi', 'select_all','file_pdf','latar_belakang','tujuan','approval_status', 'unit_pemohon','user_created'])
            ->make(true);
    }

    public function data2()
    {
        $trx_permintaan_pengembangan = PermintaanPengembangan::orderBy('id_permintaan_pengembangan', 'desc');
        $trx_permintaan_pengembangan = $trx_permintaan_pengembangan->where(function($query) {
            $query->where('is_approve', '!=', 1)
                  ->orWhereNull('is_approve');
        });
        if (auth()->user()->level == 5) {
            if(auth()->user()->level == 2 || auth()->user()->level == 3 || auth()->user()->level == 4 || auth()->user()->level == 6){
                $trx_permintaan_pengembangan = $trx_permintaan_pengembangan->where('nik_penyetuju', auth()->user()->nik);
            }
            $trx_permintaan_pengembangan = $trx_permintaan_pengembangan
            // ->where('nik_penyetuju', auth()->user()->nik)
            // ->orWhere('created_by', auth()->user()->id);
            ->where('created_by', auth()->user()->id);
        }       

        $trx_permintaan_pengembangan = $trx_permintaan_pengembangan->get();        
        
        return datatables()
            ->of($trx_permintaan_pengembangan)
            ->addIndexColumn()
            ->addColumn('select_all', function ($trx_permintaan_pengembangan) {
                return '
                    <input type="checkbox" name="id_permintaan_pengembangan[]" value="'. $trx_permintaan_pengembangan->id_permintaan_pengembangan .'">
                ';
            })
            ->addColumn('aksi', function ($trx_permintaan_pengembangan) {
                $id_permintaan_pengembangan = $trx_permintaan_pengembangan->id_permintaan_pengembangan;
                $isApproved = (int) $trx_permintaan_pengembangan->is_approve != 1 && empty($trx_permintaan_pengembangan->file_pdf);
                $isApproved2 = (int) $trx_permintaan_pengembangan->is_approve == 1 && empty($trx_permintaan_pengembangan->file_pdf);
                
                // Menentukan apakah pengguna telah menyetujui
                if(auth()->user()->level == 1){
                    $alreadyApproved = "";
                }
                
                if (auth()->user()->level == 2 || auth()->user()->level == 3) {
                    if (auth()->user()->nik == $trx_permintaan_pengembangan->nik_penyetuju) {
                        $alreadyApproved = $trx_permintaan_pengembangan->is_approve == 1;
                        if ($alreadyApproved) {
                            $approveButton = '<button type="button" class="btn btn-xs btn-success btn-flat" disabled><i class="fa fa-check"></i> Approved</button>';
                        } else {
                            $approveButton = '<button type="button" onclick="approveProyek(`'. route('permintaan_pengembangan.approveProyek', $trx_permintaan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-check"></i> Approve</button>';
                        }
                    } else {
                        $alreadyApproved = false;
                        $approveButton = '';
                    }
                }else{
                    $alreadyApproved = '';
                    $approveButton = '';
                }

                $uploadButton = ($trx_permintaan_pengembangan->is_approve == 1 && auth()->user()->id == 1)
                        ? '<button onclick="UploadPDF(`'. route('permintaan_pengembangan.updatePDF', $trx_permintaan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-upload"></i></button>'
                        : '';

                return '
                        <div class="btn-group">
                            ' . (auth()->user()->level == 1 || auth()->user()->level == 5 ? '
                                <button type="button" onclick="deleteData(`' . route('permintaan_pengembangan.destroy', $trx_permintaan_pengembangan->id_permintaan_pengembangan) . '`)" class="btn btn-xs btn-danger btn-flat">
                                    <i class="fa fa-trash"></i>
                                </button>
                                    ' . ($isApproved ? '
                                    <button type="button" onclick="editForm(`' . route('permintaan_pengembangan.update', $trx_permintaan_pengembangan->id_permintaan_pengembangan) . '`)" class="btn btn-xs btn-info btn-flat">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    ': '') . '
                                    ' . ($isApproved2 ? $uploadButton : '') . '
                            ' : '') . '
                            <button onclick="cetakDokumen(`' . route('permintaan_pengembangan.cetakDokumen', $trx_permintaan_pengembangan->id_permintaan_pengembangan) . '`)" class="btn btn-info btn-xs btn-flat">
                                <i class="fa fa-download"></i> Cetak Dokumen
                            </button>
                            <button type="button" onclick="viewForm(`' . route('permintaan_pengembangan.view', $trx_permintaan_pengembangan->id_permintaan_pengembangan) . '`)" class="btn btn-xs btn-primary btn-flat">
                                <i class="fa fa-eye"></i>
                            </button>
                            ' . (auth()->user()->level == 2 || auth()->user()->level == 3 || auth()->user()->level == 4 ? $approveButton : '') . '
                        </div>
                       ';   

                // return '
                // <div class="btn-group">
                //     <button onclick="deleteData(`'. route('permintaan_pengembangan.destroy', $trx_permintaan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                //     ' . (!$isApproved ? '
                //     <button onclick="editForm(`'. route('permintaan_pengembangan.update', $trx_permintaan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                //     <button onclick="updateProgressForm(`'. route('permintaan_pengembangan.editProgress', $trx_permintaan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-tasks"></i> Update Progress</button>
                //     ' : '') . '
                //     '. $approveButton .'
                //     <button onclick="cetakDokumen(`'.route('permintaan_pengembangan.cetakDokumen', $trx_permintaan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-info btn-xs btn-flat">
                //         <i class="fa fa-download"></i> Cetak Dokumen
                //     </button>
                //     <button onclick="UploadPDF(`'. route('permintaan_pengembangan.updatePDF', $trx_permintaan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-upload"></i></button>
                //     <button onclick="viewForm(`'. route('permintaan_pengembangan.view', $trx_permintaan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-xs btn-primary btn-flat"><i class="fa fa-eye"></i></button>
                // </div>
                // ';
            })
            ->addColumn('file_pdf', function ($trx_permintaan_pengembangan) {
                if ($trx_permintaan_pengembangan->file_pdf) {
                    return '<a href="/public/storage/assets/pdf/' . $trx_permintaan_pengembangan->file_pdf . '" target="_blank">Lihat PDF</a>';
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
            ->addColumn('unit_pemohon', function ($trx_permintaan_pengembangan) {
                $unit_pemohon = DB::table('sitmsemployee')
                                ->where('employee_id', $trx_permintaan_pengembangan->nik_pemohon)
                                ->first();

                return $unit_pemohon->working_unit_name;
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
            ->addColumn('user_created', function ($trx_permintaan_pengembangan) {
                $unit_pemohon = DB::table('users')
                                ->where('id', $trx_permintaan_pengembangan->created_by)
                                ->first();

                return $unit_pemohon->name;
            })   
            ->rawColumns(['aksi', 'select_all','file_pdf','latar_belakang','tujuan','approval_status', 'unit_pemohon','user_created'])
            ->make(true);
    }

    public function data3()
    {
        $trx_permintaan_pengembangan = [];

        foreach (PermintaanPengembangan::all() as $data) {
            if (auth()->user()->level == 2 || auth()->user()->level == 1) {
                $trx_permintaan_pengembangan[] = $data;
            } elseif (auth()->user()->level == 3 && (auth()->user()->nik == $data->nik_penyetuju || auth()->user()->nik == $data->nik_pemverifikasi)) {
                $trx_permintaan_pengembangan[] = $data;
            } elseif (auth()->user()->id == $data->created_by) {
                $trx_permintaan_pengembangan[] = $data;
            }
        }
        
        $trx_permintaan_pengembangan = collect($trx_permintaan_pengembangan)->sortByDesc('id_permintaan_pengembangan');
        
        return datatables()
            ->of($trx_permintaan_pengembangan)
            ->addIndexColumn()
            ->addColumn('select_all', function ($trx_permintaan_pengembangan) {
                return '
                    <input type="checkbox" name="id_permintaan_pengembangan[]" value="'. $trx_permintaan_pengembangan->id_permintaan_pengembangan .'">
                ';
            })
            ->addColumn('aksi', function ($trx_permintaan_pengembangan) {
                $id_permintaan_pengembangan = $trx_permintaan_pengembangan->id_permintaan_pengembangan;
                $isApproved = (int) $trx_permintaan_pengembangan->is_approve != 1 && empty($trx_permintaan_pengembangan->file_pdf);
                $isApproved2 = (int) $trx_permintaan_pengembangan->is_approve == 1 && empty($trx_permintaan_pengembangan->file_pdf);
                
                // Menentukan apakah pengguna telah menyetujui
                if(auth()->user()->level == 1){
                    $alreadyApproved = "";
                }
                
                if (auth()->user()->level == 2 || auth()->user()->level == 3) {
                    if (auth()->user()->nik == $trx_permintaan_pengembangan->nik_penyetuju) {
                        $alreadyApproved = $trx_permintaan_pengembangan->is_approve == 1;
                        if ($alreadyApproved) {
                            $approveButton = '<button type="button" class="btn btn-xs btn-success btn-flat" disabled><i class="fa fa-check"></i> Approved</button>';
                        } else {
                            $approveButton = '<button type="button" onclick="approveProyek(`'. route('permintaan_pengembangan.approveProyek', $trx_permintaan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-check"></i> Approve</button>';
                        }
                    } else {
                        $alreadyApproved = false;
                        $approveButton = '';
                    }
                }else{
                    $alreadyApproved = '';
                    $approveButton = '';
                }

                $uploadButton = ($trx_permintaan_pengembangan->is_approve == 1 && auth()->user()->id == 1)
                        ? '<button onclick="UploadPDF(`'. route('permintaan_pengembangan.updatePDF', $trx_permintaan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-upload"></i></button>'
                        : '';

                return '
                        <div class="btn-group">
                            ' . (auth()->user()->level == 1 || auth()->user()->level == 5 ? '
                                <button type="button" onclick="deleteData(`' . route('permintaan_pengembangan.destroy', $trx_permintaan_pengembangan->id_permintaan_pengembangan) . '`)" class="btn btn-xs btn-danger btn-flat">
                                    <i class="fa fa-trash"></i>
                                </button>
                                    ' . ($isApproved ? '
                                    <button type="button" onclick="editForm(`' . route('permintaan_pengembangan.update', $trx_permintaan_pengembangan->id_permintaan_pengembangan) . '`)" class="btn btn-xs btn-info btn-flat">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    ': '') . '
                                    ' . ($isApproved2 ? $uploadButton : '') . '
                            ' : '') . '
                            <button onclick="cetakDokumen(`' . route('permintaan_pengembangan.cetakDokumen', $trx_permintaan_pengembangan->id_permintaan_pengembangan) . '`)" class="btn btn-info btn-xs btn-flat">
                                <i class="fa fa-download"></i> Cetak Dokumen
                            </button>
                            <button type="button" onclick="viewForm(`' . route('permintaan_pengembangan.view', $trx_permintaan_pengembangan->id_permintaan_pengembangan) . '`)" class="btn btn-xs btn-primary btn-flat">
                                <i class="fa fa-eye"></i>
                            </button>
                            ' . (auth()->user()->level == 2 || auth()->user()->level == 3 || auth()->user()->level == 4 ? $approveButton : '') . '
                        </div>
                       ';   

                // return '
                // <div class="btn-group">
                //     <button onclick="deleteData(`'. route('permintaan_pengembangan.destroy', $trx_permintaan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                //     ' . (!$isApproved ? '
                //     <button onclick="editForm(`'. route('permintaan_pengembangan.update', $trx_permintaan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                //     <button onclick="updateProgressForm(`'. route('permintaan_pengembangan.editProgress', $trx_permintaan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-tasks"></i> Update Progress</button>
                //     ' : '') . '
                //     '. $approveButton .'
                //     <button onclick="cetakDokumen(`'.route('permintaan_pengembangan.cetakDokumen', $trx_permintaan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-info btn-xs btn-flat">
                //         <i class="fa fa-download"></i> Cetak Dokumen
                //     </button>
                //     <button onclick="UploadPDF(`'. route('permintaan_pengembangan.updatePDF', $trx_permintaan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-upload"></i></button>
                //     <button onclick="viewForm(`'. route('permintaan_pengembangan.view', $trx_permintaan_pengembangan->id_permintaan_pengembangan) .'`)" class="btn btn-xs btn-primary btn-flat"><i class="fa fa-eye"></i></button>
                // </div>
                // ';
            })
            ->addColumn('file_pdf', function ($trx_permintaan_pengembangan) {
                if ($trx_permintaan_pengembangan->file_pdf) {
                    return '<a href="/public/storage/assets/pdf/' . $trx_permintaan_pengembangan->file_pdf . '" target="_blank">Lihat PDF</a>';
                }
                return '-';
            })
            ->addColumn('approval_status', function ($trx_permintaan_pengembangan) {
                // Mendapatkan nik pengguna yang sedang login
                $nikLogin = auth()->user()->nik; // Pastikan menggunakan metode yang sesuai untuk mendapatkan nik pengguna yang sedang login
                
                $html = '<div class="box box-solid box-default">
                            <div class="box-body">
                                <table class="table table-bordered">';
            
                // Helper function for rendering the approval status and buttons
                $renderApproval = function ($status, $route, $is_approve, $label, $nikPenyetuju = null, $is_approve_pemverifikasi = null, $nikPemverifikasi = null) use ($nikLogin, $trx_permintaan_pengembangan) {
                    $labelClass = $is_approve != 1 ? 'label label-warning' : 'label label-success';
                    $labelText = $is_approve != 1 ? 'Menunggu Persetujuan Penyetuju <b><span style="color:black"> (' . $trx_permintaan_pengembangan->nama_penyetuju . ')</span></b>' : 'Sudah Disetujui Penyetuju <b><span style="color:black"> (' . $trx_permintaan_pengembangan->approve_by . ')</span></b>';
                    $buttonText = $is_approve != 1 ? 'Approve' : 'Approved';
                    $buttonClass = $is_approve != 1 ? 'btn-warning' : 'btn-success';
                    $buttonDisabled = $is_approve != 1 ? '' : 'disabled';

                    // Jika nik_penyetuju sama dengan nik login, maka tombol approve aktif
                    if ($nikPenyetuju && $nikPenyetuju != $nikLogin) {
                        $buttonDisabled = 'disabled'; // Disable tombol jika nik_pemverifikasi tidak sesuai dengan nik login
                    }
            
                    // Cek juga nik_pemverifikasi
                    if ($nikPemverifikasi && $nikPemverifikasi != $nikLogin) {
                        $buttonDisabled = 'disabled'; // Disable tombol jika nik_pemverifikasi tidak sesuai dengan nik login
                    }
            
                    return "
                        <tr>
                            <td>{$label}</td>
                            <td><span class='{$labelClass}'>{$labelText}</span></td>
                            <td>
                                <button onclick=\"cetakDokumen('{$route}')\" class=\"btn btn-info btn-xs btn-flat\">
                                    <i class=\"fa fa-download\"></i> Cetak Dokumen
                                </button>
                            </td>
                            " . (auth()->user()->level == 2 ? "
                            <td>
                            <button type=\"button\" onclick=\"approveProyek('{$route}')\" class=\"btn btn-xs {$buttonClass} btn-flat\" {$buttonDisabled}>
                                <i class=\"fa fa-check\"></i> {$buttonText}
                            </button>
                            </td>
                            " : "") . "
                        </tr>
                    ";
                };
            
                // Permintaan Pengembangan
                $html .= $renderApproval(
                    'Permintaan Pengembangan', 
                    route('permintaan_pengembangan.cetakDokumen', $trx_permintaan_pengembangan->id_permintaan_pengembangan), 
                    $trx_permintaan_pengembangan->is_approve, 
                    'Permintaan Pengembangan',
                    $trx_permintaan_pengembangan->nik_penyetuju
                );
            
                // Persetujuan Pengembangan
                $persetujuanPengembangan = PersetujuanPengembangan::where('id_permintaan_pengembangan', $trx_permintaan_pengembangan->id_permintaan_pengembangan)->first();
                $html .= $persetujuanPengembangan ? 
                    $renderApproval(
                        'Persetujuan Pengembangan', 
                        route('persetujuan_pengembangan.cetakDokumen', $persetujuanPengembangan->id_persetujuan_pengembangan), 
                        $persetujuanPengembangan->is_approve, 
                        'Persetujuan Pengembangan',
                        $persetujuanPengembangan->nik_penyetuju
                    ) : 
                    '<tr>
                    <td colspan="2">Persetujuan Pengembangan</td>
                    <td colspan="2" class="text-left"><span class="text-danger font-weight-bold" style="background-color: yellow;">Belum ada persetujuan</span></td>
                    </tr>';
            
                // Perencanaan Proyek (dengan pengecekan is_approve_pemverifikasi dan nik_pemverifikasi)
                $perencanaanProyek = PerencanaanProyek::where('id_persetujuan_pengembangan', $persetujuanPengembangan ? $persetujuanPengembangan->id_persetujuan_pengembangan : null)->first();
                $html .= $perencanaanProyek ? 
                    $renderApproval(
                        'Perencanaan Proyek', 
                        route('perencanaan_proyek.cetakDokumen', $perencanaanProyek->id_perencanaan_proyek), 
                        $perencanaanProyek->is_approve, 
                        'Perencanaan Proyek',
                        $perencanaanProyek->nik_penyetuju,
                        $perencanaanProyek->is_approve_pemverifikasi, // Menambahkan pengecekan is_approve_pemverifikasi
                        $perencanaanProyek->nik_pemverifikasi // Menambahkan pengecekan nik_pemverifikasi
                    ) : 
                    '<tr>
                     <td colspan="2">Perencanaan Proyek</td>
                     <td colspan="2" class="text-left"><span class="text-danger font-weight-bold" style="background-color: yellow;">Belum ada perencanaan proyek</td></tr>';
            
                // Perencanaan Kebutuhan (dengan pengecekan is_approve_pemverifikasi dan nik_pemverifikasi)
                $perencanaanKebutuhan = PerencanaanKebutuhan::where('id_persetujuan_pengembangan', $persetujuanPengembangan ? $persetujuanPengembangan->id_persetujuan_pengembangan : null)->first();
                $html .= $perencanaanKebutuhan ? 
                    $renderApproval(
                        'Perencanaan Kebutuhan', 
                        route('perencanaan_kebutuhan.cetakDokumen', $perencanaanKebutuhan->id_perencanaan_kebutuhan), 
                        $perencanaanKebutuhan->is_approve, 
                        'Perencanaan Kebutuhan',
                        $perencanaanKebutuhan->nik_penyetuju,
                        $perencanaanKebutuhan->is_approve_pemverifikasi, // Menambahkan pengecekan is_approve_pemverifikasi
                        $perencanaanKebutuhan->nik_pemverifikasi // Menambahkan pengecekan nik_pemverifikasi
                    ) : 
                    '<tr>
                    <td colspan="2">Perencanaan Kebutuhan</td>
                    <td colspan="2" class="text-left"><span class="text-danger font-weight-bold" style="background-color: yellow;">Belum ada perencanaan kebutuhan</td></tr>';
            
                // Analisis dan Desain
                $analisisDesain = AnalisisDesain::where('id_permintaan_pengembangan', $trx_permintaan_pengembangan->id_permintaan_pengembangan)->first();
                $html .= $analisisDesain ? 
                    $renderApproval(
                        'Analisis dan Desain', 
                        route('analisis_desain.cetakDokumen', $analisisDesain->id_analisis_desain), 
                        $analisisDesain->is_approve, 
                        'Analisis dan Desain',
                        $analisisDesain->nik_penyetuju
                    ) : 
                    '<tr>
                    <td colspan="2">Analisis dan Desain</td>
                    <td colspan="2" class="text-left"><span class="text-danger font-weight-bold" style="background-color: yellow;">Belum ada analisis dan desain</td></tr>';
            
                // Quality Assurance Testing
                $qualityAssuranceTesting = QualityAssuranceTesting::where('id_permintaan_pengembangan', $trx_permintaan_pengembangan->id_permintaan_pengembangan)->first();
                $html .= $qualityAssuranceTesting ? 
                    $renderApproval(
                        'Quality Assurance Testing', 
                        route('quality_assurance_testing.cetakDokumen', $qualityAssuranceTesting->id_quality_assurance_testing), 
                        $qualityAssuranceTesting->is_approve, 
                        'Quality Assurance Testing',
                        $qualityAssuranceTesting->nik_penyetuju
                    ) : 
                    '<tr>
                    <td colspan="2">Quality Assurance Testing</td>
                    <td colspan="2" class="text-left"><span class="text-danger font-weight-bold" style="background-color: yellow;">Belum ada quality assurance testing</td></tr>';
            
                // Serah Terima Aplikasi
                $serahTerimaAplikasi = SerahTerimaAplikasi::where('id_permintaan_pengembangan', $trx_permintaan_pengembangan->id_permintaan_pengembangan)->first();
                $html .= $serahTerimaAplikasi ? 
                    $renderApproval(
                        'Serah Terima Aplikasi', 
                        route('serah_terima_aplikasi.cetakDokumen', $serahTerimaAplikasi->id_serah_terima_aplikasi), 
                        $serahTerimaAplikasi->is_approve, 
                        'Serah Terima Aplikasi',
                        $serahTerimaAplikasi->nik_penyetuju
                    ) : 
                    '<tr>
                    <td colspan="2">Serah Terima Aplikasi</td>
                    <td colspan="2" class="text-left"><span class="text-danger font-weight-bold" style="background-color: yellow;">Belum ada serah terima aplikasi</td></tr>';
                
                $html .= '<tr>
                    <td colspan="4" class="text-left">
                        <button onclick="cetakReportSummary(`' . route('permintaan_pengembangan.cetakDokumenSummary', $trx_permintaan_pengembangan->id_permintaan_pengembangan) . '`)" class="btn btn-info btn-xs btn-flat">
                            <i class="fa fa-download"></i> Cetak Report Project
                        </button>
                    </td>
                  </tr>';

                  
                $html .= '</table>
                        </div>
                    </div>';
            
                return $html;
            })                           
            ->addColumn('unit_pemohon', function ($trx_permintaan_pengembangan) {
                $unit_pemohon = DB::table('sitmsemployee')
                                ->where('employee_id', $trx_permintaan_pengembangan->nik_pemohon)
                                ->first();

                return $unit_pemohon->working_unit_name;
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
            ->addColumn('user_created', function ($trx_permintaan_pengembangan) {
                $unit_pemohon = DB::table('users')
                                ->where('id', $trx_permintaan_pengembangan->created_by)
                                ->first();

                return $unit_pemohon->name;
            })   
            ->rawColumns(['aksi', 'select_all','file_pdf','latar_belakang','tujuan','approval_status', 'unit_pemohon','user_created'])
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
        $jenis_kepala_bagian = $request->input('jenis_kepala_bagian');

        if($jenis_kepala_bagian == 'IT Bisnis'){
            $q_get_avp = DB::table('sitmsemployee')
            ->select('full_name', 'employee_id', 'position_name')
            ->where('working_unit_id', 20)
            ->where('position_name', 'LIKE', '%AVP IT Business%')
            ->first();
        }else if($jenis_kepala_bagian == 'IT Korporat'){
            $q_get_avp = DB::table('sitmsemployee')
            ->select('full_name', 'employee_id', 'position_name')
            ->where('working_unit_id', 20)
            ->where('position_name', 'LIKE', '%AVP IT Korporasi%')
            ->first();
        }

        $data = [
            'judul' => $request->input('judul'),
            'latar_belakang' => $request->input('latar_belakang'),
            'tujuan' => $request->input('tujuan'),
            'target_implementasi_sistem' => $request->input('target_implementasi_sistem'),
            'fungsi_sistem_informasi' => $request->input('fungsi_sistem_informasi'),
            'jenis_aplikasi' => json_encode($request->input('jenis_aplikasi')),
            'pengguna' => json_encode($request->input('pengguna')),
            'jenis_kepala_bagian' => $request->input('jenis_kepala_bagian'),
            'uraian_permintaan_tambahan' => $request->input('uraian_permintaan_tambahan'),
            'lampiran' => json_encode($request->input('lampiran')),
            // 'pic' => $request->input('pic'),
            'nama_pemohon' => $request->input('nama_pemohon'),
            'jabatan_pemohon' => $request->input('jabatan_pemohon'),
            'nik_pemohon' => $request->input('nik_pemohon'),
            'tanggal_disiapkan' => now(),
            // AVP DTI
            'nama_penyetuju' => $q_get_avp->full_name,
            'nik_penyetuju' => $q_get_avp->employee_id,
            'jabatan_penyetuju' => $q_get_avp->position_name,
            // 'tanggal_disetujui' => $request->input('tanggal_disetujui'),
            'created_by' => auth()->user()->id,
        ];

        // Ambil nomor dokumen terakhir berdasarkan id terakhir
        $lastNoDocument = PermintaanPengembangan::orderBy('id_permintaan_pengembangan', 'desc')->first();

        if (!$lastNoDocument) {
            // Jika tidak ada dokumen sebelumnya, nomor mulai dari 1
            $number = 1;
        } else {
            // Ambil nomor urut dari dokumen terakhir
            preg_match('/^(\d+)/', $lastNoDocument->nomor_dokumen, $matches);
            $number = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        }

        // Format nomor dokumen dengan padding 4 digit
        $formattedNumber = str_pad($number, 4, '0', STR_PAD_LEFT);

        // Tentukan bulan dan tahun sekarang
        $months = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $currentMonth = $months[date('n') - 1];
        $currentYear = date('Y');

        // Set nomor dokumen yang baru
        $data['nomor_dokumen'] = "{$formattedNumber}/{$currentMonth}/DTI/{$currentYear}";

        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->move(public_path('storage/assets/lampiran'), $filename);
            $data['lampiran'] = $filename;
        } else {
            $data['lampiran'] = null;
        }
        
        if ($request->hasFile('lampiran_2')) {
            $file = $request->file('lampiran_2');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->move(public_path('storage/assets/lampiran'), $filename);
            $data['lampiran_2'] = $filename;
        } else {
            $data['lampiran_2'] = null;
        }

        if ($request->hasFile('file_pdf')) {
            $file = $request->file('file_pdf');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->move(public_path('storage/assets/pdf'), $filename);
            $data['file_pdf'] = $filename;
        }

        // Content for the QR code
        $qrContent ='Permintaan Pengembangan, Pemohon :' . $request->input('nama_pemohon') . 
                    ', Nomor Dokumen : ' . $data['nomor_dokumen'] .
                    ', NIK Karyawan : ' . $request->input('nik_pemohon') . 
                    ', Tanggal Disiapkan : ' . now(); 
    
        // Sanitize nomor_dokumen to remove slashes and create a valid filename
        $safeNomorDokumen = str_replace('/', '', $data['nomor_dokumen']);  // Remove slashes
        $fileName = '' . 'qr_code_permintaanpengembangan_pemohon_'. $safeNomorDokumen . '.png';
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

        $trx_permintaan_pengembangan = PermintaanPengembangan::create($data);
        $lastId = $trx_permintaan_pengembangan->id_permintaan_pengembangan;

        FlagStatus::create([
            'kat_modul' => 1,
            'id_permintaan' => $lastId,
            'nama_modul' => "Permintaan Pengembangan",
            'id_tabel' => $lastId,
            'flag' => 1
        ]);

        FlagStatus::create([
            'kat_modul' => 2,
            'id_permintaan' => $lastId,
            'nama_modul' => "Persetujuan Pengembangan",
            'id_tabel' => $lastId,
            'flag' => 2
        ]);

        $user = Users::where('nik', $data['nik_penyetuju'])->first();
        if ($user) {
            $data['no_telp'] = $user->no_telp;
        }

        // Kirim pesan WhatsApp
        $message = "Permintaan Pengembangan *{$data['judul']}* telah diajukan oleh *{$data['nama_pemohon']}* "
                . "\ndengan nomor dokumen *{$data['nomor_dokumen']}* pada *" . now()->format('d F Y H:i:s') . "* "
                . "\ndan sedang menunggu untuk diapprove oleh *{$data['nama_penyetuju']}*.";

        $this->whatsAppService->sendWhatsAppMessage($data['no_telp'], $message);

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
        $trx_permintaan_pengembangan = PermintaanPengembangan::find($id);

        return response()->json($trx_permintaan_pengembangan);
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
        $permintaanPengembangan = PermintaanPengembangan::findOrFail($id);

        // Kirim data ke response JSON
        return response()->json($permintaanPengembangan);
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
        $trx_permintaan_pengembangan = PermintaanPengembangan::findOrFail($id_permintaan_pengembangan);
        // Simpan data lainnya yang tidak terkait dengan file
        $data = $request->except(['lampiran', 'lampiran_2', 'file_pdf']);  // Semua data kecuali file

        // Proses file lampiran jika ada
        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->move(public_path('storage/assets/lampiran/'), $filename);
            $data['lampiran'] = $filename;
        } else {
            // Jika tidak ada file baru, biarkan data yang lama tetap ada
            if (!$request->has('lampiran_1')) {
                $data['lampiran'] = $trx_permintaan_pengembangan->lampiran_1;
            }
        }

        // Proses file lampiran_2 jika ada
        if ($request->hasFile('lampiran_2')) {
            $file = $request->file('lampiran_2');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->move(public_path('storage/assets/lampiran/'), $filename);
            $data['lampiran_2'] = $filename;
        } else {
            // Jika tidak ada file baru, biarkan data yang lama tetap ada
            if (!$request->has('lampiran_2')) {
                $data['lampiran_2'] = $trx_permintaan_pengembangan->lampiran_2;
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
                $data['file_pdf'] = $trx_permintaan_pengembangan->file_pdf;
            }
        }

        $trx_permintaan_pengembangan->update($data);
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
        $trx_permintaan_pengembangan = PermintaanPengembangan::find($id_permintaan_pengembangan);
        $trx_permintaan_pengembangan->delete();

        return response(null, 204);
    }

    public function deleteSelected(Request $request)
    {
        $ids = $request->id_permintaan_pengembangan;
        PermintaanPengembangan::whereIn('id_permintaan_pengembangan', $ids)->delete();
        return response()->json('Data berhasil dihapus', 200);
    }

    public function cetakDokumen(Request $request)
    {
        set_time_limit(300);

        $idPermintaan = $request->query();
        $id_permintaan_pengembangan = key($idPermintaan); 
        $id_permintaan_pengembangan = (int) $id_permintaan_pengembangan;
        
        $datapermintaan = PermintaanPengembangan::whereIn('id_permintaan_pengembangan', [$id_permintaan_pengembangan])->get();
        $no  = 1;

        $pdf = PDF::loadView('permintaan_pengembangan.dokumen', compact('datapermintaan', 'no'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('permintaan.pdf');
    }

    public function cetakDokumenSummary(Request $request)
    {
        set_time_limit(300);

        $idPermintaan = $request->query();
        $id_permintaan_pengembangan = key($idPermintaan); 
        $id_permintaan_pengembangan = (int) $id_permintaan_pengembangan;
        
        $datapermintaan = PermintaanPengembangan::leftJoin('users', 'users.id', '=', 'trx_permintaan_pengembangan.created_by')
            ->select('trx_permintaan_pengembangan.*', 'users.name as nama_pengaju')
            ->where('trx_permintaan_pengembangan.id_permintaan_pengembangan', $id_permintaan_pengembangan)
            ->get();

        $trx_permintaan_pengembangan = PermintaanPengembangan::where('id_permintaan_pengembangan', $id_permintaan_pengembangan)->get();
        $trx_persetujuan_pengembangan = PersetujuanPengembangan::where('id_permintaan_pengembangan', $id_permintaan_pengembangan)->get();
        
        $trx_perencanaan_proyek = PerencanaanProyek::join('trx_persetujuan_pengembangan', 'trx_persetujuan_pengembangan.id_persetujuan_pengembangan', '=', 'trx_perencanaan_proyek.id_persetujuan_pengembangan')
            ->join('trx_permintaan_pengembangan', 'trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan')
            ->select('trx_perencanaan_proyek.*')
            ->where('trx_permintaan_pengembangan.id_permintaan_pengembangan', $id_permintaan_pengembangan)
            ->get();

        $trx_perencanaan_kebutuhan = PerencanaanKebutuhan::join('trx_persetujuan_pengembangan', 'trx_persetujuan_pengembangan.id_persetujuan_pengembangan', '=', 'trx_perencanaan_kebutuhan.id_persetujuan_pengembangan')
            ->join('trx_permintaan_pengembangan', 'trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan')
            ->select('trx_perencanaan_kebutuhan.*')
            ->where('trx_permintaan_pengembangan.id_permintaan_pengembangan', $id_permintaan_pengembangan)
            ->get();

        $trx_persetujuan_pengembangan = PersetujuanPengembangan::where('id_permintaan_pengembangan', $id_permintaan_pengembangan)->get();
        $trx_analisis_desain = AnalisisDesain::where('id_permintaan_pengembangan', $id_permintaan_pengembangan)->get();
        $trx_user_acceptance_testing = UserAcceptanceTesting::where('id_permintaan_pengembangan', $id_permintaan_pengembangan)->get();
        $trx_quality_assurance_testing = QualityAssuranceTesting::where('id_permintaan_pengembangan', $id_permintaan_pengembangan)->get();
        $trx_serah_terima_aplikasi = serahterimaaplikasi::where('id_permintaan_pengembangan', $id_permintaan_pengembangan)->get();
        $setting = Setting::get();

        $no  = 1;

        $pdf = PDF::loadView('permintaan_pengembangan.dokumensumarry', compact('datapermintaan', 'trx_permintaan_pengembangan', 'trx_persetujuan_pengembangan', 'trx_perencanaan_proyek', 'trx_perencanaan_kebutuhan', 'trx_analisis_desain', 'trx_user_acceptance_testing', 'trx_quality_assurance_testing', 'trx_serah_terima_aplikasi', 'setting', 'no'));

        $pdf->setPaper('a4', 'landscape');
        return $pdf->stream('permintaan.pdf');
    }

    
    public function cetakAllDokumenSummary(Request $request)
    {
        set_time_limit(300);
        
        $datapermintaan = PermintaanPengembangan::leftJoin('users', 'users.id', '=', 'trx_permintaan_pengembangan.created_by')
            ->select('trx_permintaan_pengembangan.*', 'users.name as nama_pengaju')
            ->get();

        $trx_permintaan_pengembangan = PermintaanPengembangan::get();
        $trx_persetujuan_pengembangan = PersetujuanPengembangan::get();
        $trx_perencanaan_proyek = PerencanaanProyek::join('trx_persetujuan_pengembangan', 'trx_persetujuan_pengembangan.id_persetujuan_pengembangan', '=', 'trx_perencanaan_proyek.id_persetujuan_pengembangan')
            ->join('trx_permintaan_pengembangan', 'trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan')
            ->select('trx_perencanaan_proyek.*')
            ->get();

        $trx_perencanaan_kebutuhan = PerencanaanKebutuhan::join('trx_persetujuan_pengembangan', 'trx_persetujuan_pengembangan.id_persetujuan_pengembangan', '=', 'trx_perencanaan_kebutuhan.id_persetujuan_pengembangan')
            ->join('trx_permintaan_pengembangan', 'trx_permintaan_pengembangan.id_permintaan_pengembangan', '=', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan')
            ->select('trx_perencanaan_kebutuhan.*')
            ->get();

        $trx_analisis_desain = AnalisisDesain::get();
        $trx_user_acceptance_testing = UserAcceptanceTesting::get();
        $trx_quality_assurance_testing = QualityAssuranceTesting::get();
        $trx_serah_terima_aplikasi = serahterimaaplikasi::get();
        $setting = Setting::get();

        $no  = 1;

        $pdf = PDF::loadView('permintaan_pengembangan.dokumenallsumarry', compact('datapermintaan', 'trx_permintaan_pengembangan', 'trx_persetujuan_pengembangan', 'trx_perencanaan_proyek', 'trx_perencanaan_kebutuhan', 'trx_analisis_desain', 'trx_user_acceptance_testing', 'trx_quality_assurance_testing', 'trx_serah_terima_aplikasi', 'setting', 'no'));

        $pdf->setPaper('a4', 'landscape');
        return $pdf->stream('permintaan.pdf');
    }

    public function cetakDokumenPersetujuan(Request $request)
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
            ->whereIn('trx_persetujuan_pengembangan.id_permintaan_pengembangan', [$ids])
            ->get();

        $pdf = PDF::loadView('persetujuan_pengembangan.dokumen', compact('datapersetujuan'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('persetujuan.pdf');
    }
    public function view($id)
    {
        $trx_permintaan_pengembangan = PermintaanPengembangan::findOrFail($id);
        return response()->json($trx_permintaan_pengembangan);
    }

    public function updatePDF(Request $request, $id_permintaan_pengembangan)
    {
        // Temukan data berdasarkan ID
        $trx_permintaan_pengembangan = PermintaanPengembangan::findOrFail($id_permintaan_pengembangan);

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
            $trx_permintaan_pengembangan->file_pdf = $filename;

            // Update data di database
            $trx_permintaan_pengembangan->save();

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
        $permintaanPengembangan = PermintaanPengembangan::findOrFail($id);

        // Update progress
        $permintaanPengembangan->progress = $request->progress; // Pastikan ada kolom 'progress' di tabel
        $permintaanPengembangan->save(); // Simpan perubahan

        // Kembali dengan respon sukses
        return redirect()->route('permintaan_pengembangan.index');
    }

    // Method untuk melakukan approve proyek
    public function approveProyek(Request $request, $id)
    {
        // Ambil data proyek berdasarkan id
        $proyek = PermintaanPengembangan::findOrFail($id);
        $persetujuan_pengembangan_approve = new PersetujuanPengembangan();
    
        // Update status proyek menjadi approved
        $proyek->is_approve = 1;
        $proyek->approve_at = now(); // Set tanggal persetujuan saat ini
        $proyek->approve_by = auth()->user()->name; // Set tanggal persetujuan saat ini
        $proyek->tanggal_disetujui = now(); // Set tanggal persetujuan saat ini
    
        // Save Persetujuan Pengembangan
        $persetujuan_pengembangan_approve->id_permintaan_pengembangan = $proyek->id_permintaan_pengembangan;
        $persetujuan_pengembangan_approve->id_mst_persetujuan = $proyek->id_mst_persetujuan;
        $persetujuan_pengembangan_approve->id_mst_persetujuanalasan = $proyek->id_mst_persetujuanalasan;
        $persetujuan_pengembangan_approve->nama_pemohon = $proyek->nama_pemohon;
        $persetujuan_pengembangan_approve->jabatan_pemohon = $proyek->jabatan_pemohon;
        $persetujuan_pengembangan_approve->tanggal_disiapkan = $proyek->tanggal_disiapkan;
        $persetujuan_pengembangan_approve->nama_penyetuju = $proyek->nama_penyetuju;
        $persetujuan_pengembangan_approve->jabatan_penyetuju = $proyek->jabatan_penyetuju;
        $persetujuan_pengembangan_approve->tanggal_disetujui = now();
        $persetujuan_pengembangan_approve->file_pdf = $proyek->file_pdf;
        $persetujuan_pengembangan_approve->progress = '0';
        $persetujuan_pengembangan_approve->is_approve = 1;
        $persetujuan_pengembangan_approve->approve_at = now();
        $persetujuan_pengembangan_approve->approve_by = auth()->user()->name;
        $persetujuan_pengembangan_approve->is_approve_penyetuju = null;
        $persetujuan_pengembangan_approve->approve_at_penyetuju = null;
        $persetujuan_pengembangan_approve->approve_by_penyetuju = null;
        $persetujuan_pengembangan_approve->nik_pemohon = $proyek->nik_pemohon;
        $persetujuan_pengembangan_approve->nik_penyetuju = $proyek->nik_penyetuju;
        $persetujuan_pengembangan_approve->created_by = $proyek->created_by;
        $persetujuan_pengembangan_approve->path_qrcode_pemohon = $proyek->path_qrcode_pemohon;
    
        $persetujuan_pengembangan_approve->id_mst_persetujuan = $request->id_mst_persetujuan;
        $persetujuan_pengembangan_approve->id_mst_persetujuanalasan = $request->id_mst_persetujuanalasan;
    
        // Content for the QR code
        $qrContent ='Permintaan Pengembangan Persetujan Pengembangan
                     <br> Penyetuju :' . auth()->user()->name . 
                    '<br> Nomor Dokumen : ' . $proyek->nomor_dokumen .
                    '<br> NIK Karyawan : ' . $proyek->nik_penyetuju . 
                    '<br> Tanggal Disetujui : ' . now(); 
    
        // Sanitize nomor_dokumen to remove slashes and create a valid filename
        $safeNomorDokumen = str_replace('/', '', $proyek->nomor_dokumen);
        $fileName = '' . 'qr_code_permintaanpengembangan_penyetuju_' . $proyek->id_permintaan_pengembangan . '_' . $safeNomorDokumen . '.png';
        $filePath = 'storage/assets/qrcode/' . $fileName;
    
        // Create QR code instance with additional settings
        $qrCode = new QrCode(
            data: $qrContent,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: 500,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );
    
        // Write the QR code with label (no logo here)
        $writer = new PngWriter();
    
        // Generate the image content of the QR code and save to file
        $result = $writer->write($qrCode, null, null); 
    
        // Save the QR code image to the specified file path
        $filePathWithPublicPath = public_path($filePath);
        file_put_contents($filePathWithPublicPath, $result->getString());  
    
        // Store the path to the database
        $persetujuan_pengembangan_approve->path_qrcode_penyetuju = $filePath;
        $proyek->path_qrcode_penyetuju = $filePath;
    
        // Menyimpan data ke tabel Proyek
        $persetujuan_pengembangan_approve->save();
        $proyek->save();

        // Cari data user berdasarkan nik pemohon
        $user = Users::where('nik', $proyek->nik_pemohon)->first();
        
        if ($user) {
            // Update no_telp di tabel Proyek
            $proyek->no_telp = $user->no_telp;
        } else {
            // Kembali dengan respon error jika tidak ada data user
            return response()->json(['error' => 'Tidak ada data user dengan NIK ' . $proyek->nik_pemohon], 404);
        }
    
        // Kirim pesan WhatsApp
        $message = "Permintaan Pengembangan *{$proyek->judul}* telah berhasil disetujui oleh *{$proyek->nama_penyetuju}* "
                 . "\ndengan nomor dokumen *{$proyek->nomor_dokumen}* pada *" . now()->format('j F Y H:i') . "*.";
        $this->whatsAppService->sendWhatsAppMessage($proyek->no_telp, $message);
    
        return response()->json(['success' => 'QR code generated, saved, and WhatsApp message sent successfully.']);
    }    
}

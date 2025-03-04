<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Member;
use App\Models\Pembelian;
use App\Models\Pengeluaran;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\Supplier;
use App\Models\PermintaanPengembangan;
use App\Models\PersetujuanPengembangan;
use App\Models\PerencanaanKebutuhan;
use App\Models\AnalisisDesain;
use App\Models\PerencanaanProyek;
use App\Models\UserAcceptanceTesting;
use App\Models\serahterimaaplikasi;
use App\Models\QualityAssuranceTesting;
use App\Models\PemohonPeverifikasiPenyetuju;
use App\Models\FlagStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $kategori = Kategori::count();
        $trx_permintaan_pengembangan = PermintaanPengembangan::count();
        $trx_perencanaan_proyek = PerencanaanProyek::count();
        $trx_perencanaan_kebutuhan = PerencanaanKebutuhan::count();
        $trx_persetujuan_pengembangan = PersetujuanPengembangan::count();
        $produk = Produk::count();
        $trx_analisis_desain = AnalisisDesain::count();
        $trx_user_acceptance_testing = UserAcceptanceTesting::count();
        $trx_quality_assurance_testing = QualityAssuranceTesting::count();
        $trx_serah_terima_aplikasi = serahterimaaplikasi::count();
        $supplier = Supplier::count();
        $member = Member::count();

        $tanggal_awal = date('Y-m-01');
        $tanggal_akhir = date('Y-m-d');

        $data_tanggal = array();
        $data_pendapatan = array();

        while (strtotime($tanggal_awal) <= strtotime($tanggal_akhir)) {
            $data_tanggal[] = (int) substr($tanggal_awal, 8, 2);

            $total_penjualan = Penjualan::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('bayar');
            $total_pembelian = Pembelian::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('bayar');
            $total_pengeluaran = Pengeluaran::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('nominal');

            $pendapatan = $total_penjualan - $total_pembelian - $total_pengeluaran;
            $data_pendapatan[] += $pendapatan;

            $tanggal_awal = date('Y-m-d', strtotime("+1 day", strtotime($tanggal_awal)));
        }

        $tanggal_awal = date('Y-m-01');

        if (auth()->user()->level == 1) {
            return view('admin.dashboard', compact('kategori', 'trx_perencanaan_proyek', 'trx_persetujuan_pengembangan' ,'trx_permintaan_pengembangan', 'trx_perencanaan_kebutuhan', 'trx_analisis_desain', 'trx_user_acceptance_testing','trx_quality_assurance_testing','trx_serah_terima_aplikasi','produk', 'supplier', 'member', 'tanggal_awal', 'tanggal_akhir', 'data_tanggal', 'data_pendapatan'));
        } else {
            return view('kasir.dashboard');
        }
    }

    public function index2()
    {
        $kategori = Kategori::count();
        $trx_permintaan_pengembangan = PermintaanPengembangan::count();
        $trx_perencanaan_proyek = PerencanaanProyek::count();
        $trx_perencanaan_kebutuhan = PerencanaanKebutuhan::count();
        $trx_persetujuan_pengembangan = PersetujuanPengembangan::count();
        $produk = Produk::count();
        $trx_analisis_desain = AnalisisDesain::count();
        $trx_user_acceptance_testing = UserAcceptanceTesting::count();
        $trx_quality_assurance_testing = QualityAssuranceTesting::count();
        $trx_serah_terima_aplikasi = serahterimaaplikasi::count();
        $supplier = Supplier::count();
        $member = Member::count();

        $tanggal_awal = date('Y-m-01');
        $tanggal_akhir = date('Y-m-d');

        $data_tanggal = array();
        $data_pendapatan = array();

        while (strtotime($tanggal_awal) <= strtotime($tanggal_akhir)) {
            $data_tanggal[] = (int) substr($tanggal_awal, 8, 2);

            $total_penjualan = Penjualan::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('bayar');
            $total_pembelian = Pembelian::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('bayar');
            $total_pengeluaran = Pengeluaran::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('nominal');

            $pendapatan = $total_penjualan - $total_pembelian - $total_pengeluaran;
            $data_pendapatan[] += $pendapatan;

            $tanggal_awal = date('Y-m-d', strtotime("+1 day", strtotime($tanggal_awal)));
        }

        $tanggal_awal = date('Y-m-01');

        // Revision For Dashboard Dev Progress Project 
        $trx_perencanaan_proyek_data = PerencanaanProyek::all();

        if (auth()->user()->level == 1) {
            return view('admin.dashboard_dev_tanggal', compact('kategori', 'trx_perencanaan_proyek', 'trx_persetujuan_pengembangan' ,'trx_permintaan_pengembangan', 'trx_perencanaan_kebutuhan', 'trx_analisis_desain', 'trx_user_acceptance_testing','trx_quality_assurance_testing','trx_serah_terima_aplikasi','produk', 'supplier', 'member', 'tanggal_awal', 'tanggal_akhir', 'data_tanggal', 'data_pendapatan', 'trx_perencanaan_proyek_data'));
        } else {
            return view('kasir.dashboard_dev_tanggal');
        }
    }

    public function dashboard_calculate()
    {
        // Ambil data proyek dari database
        $projects = PerencanaanProyek::select('tanggal_disiapkan', 'tanggal_disetujui')->get()->toArray();

        // Format data proyek agar siap untuk perhitungan progress
        $formattedProjects = array_map(function($project) {
            return [
                'mulai' => $project['tanggal_disiapkan'],
                'selesai' => $project['tanggal_disetujui']
            ];
        }, $projects);

        // Persentase tetap untuk setiap tahap
        $persentase_tahap = [
            'Tahap A' => 5,
            'Tahap B' => 20,
            'Tahap C' => 60,
            'Tahap D' => 10,
            'Tahap E' => 5,
        ];

        // Tanggal saat ini
        $tanggal_sekarang = date('Y-m-d'); // Misalnya tanggal sekarang

        // Fungsi untuk menghitung progress
        function hitungProgress($tanggal_mulai, $tanggal_selesai, $tanggal_sekarang, $persentase_tahap) {
            // Durasi total proyek dalam detik
            $durasi_total = strtotime($tanggal_selesai) - strtotime($tanggal_mulai);
            $waktu_sekarang = strtotime($tanggal_sekarang) - strtotime($tanggal_mulai);

            if ($waktu_sekarang < 0) {
                return ['progress' => 0, 'tahap_sekarang' => 'Proyek belum dimulai', 'progress_per_tahap' => []];
            }

            // Hitung waktu akhir setiap tahap berdasarkan persentase
            $batas_waktu_tahap = [];
            $waktu_akumulasi = 0;
            foreach ($persentase_tahap as $tahap => $persentase) {
                $waktu_akumulasi += $durasi_total * ($persentase / 100);
                $batas_waktu_tahap[$tahap] = $waktu_akumulasi;
            }

            // Progress dinamis dan progress per tahap
            $progress_total = 0;
            $tahap_sekarang = '';
            $progress_per_tahap = [];
            
            foreach ($batas_waktu_tahap as $tahap => $batas_waktu) {
                if ($waktu_sekarang <= $batas_waktu) {
                    // Hitung progress dalam tahap saat ini
                    $durasi_tahap = $batas_waktu - ($progress_total / 100 * $durasi_total);
                    $progress_dalam_tahap = ($waktu_sekarang - $progress_total / 100 * $durasi_total) / $durasi_tahap * 100;
                    
                    $tahap_sekarang = $tahap;
                    $progress_total += $progress_dalam_tahap * ($persentase_tahap[$tahap] / 100);
                    
                    // Simpan progress per tahap
                    $progress_per_tahap[$tahap] = round($progress_dalam_tahap, 2);
                    
                    // Cek jika progress sudah mendekati 90% dari tahap tersebut
                    if ($progress_dalam_tahap >= 90) {
                        $progress_per_tahap[$tahap] .= " (Warning: Mendekati batas tahap $tahap)";
                    }
                    break;
                } else {
                    // Jika tahap ini sudah dilewati, tambahkan persentase penuh dari tahap ini
                    $progress_total += $persentase_tahap[$tahap];
                    $progress_per_tahap[$tahap] = 100; // Tahap ini sudah selesai
                }
            }

            // Kembalikan hasil perhitungan
            return [
                'progress_total' => round($progress_total, 2),
                'tahap_sekarang' => $tahap_sekarang,
                'progress_per_tahap' => $progress_per_tahap
            ];
        }

        // Hitung progress setiap proyek
        foreach ($formattedProjects as $index => $project) {
            $progress = hitungProgress($project['mulai'], $project['selesai'], $tanggal_sekarang, $persentase_tahap);
            
            echo "<br>";

            echo "Proyek " . ($index + 1) . ":\n";
            echo "Progress total: " . $progress['progress_total'] . "%\n";
            
            // Tampilkan progress per tahap
            foreach ($persentase_tahap as $tahap => $persentase) {
                $progress_tahap = isset($progress['progress_per_tahap'][$tahap]) ? $progress['progress_per_tahap'][$tahap] : 'Belum dimulai';
                echo "$tahap: Progress $progress_tahap selesai\n";
            }
            
            echo "----------------------\n";
        }
    }

    public function dashboard_data()
    {
        $kategori = Kategori::count();
        $trx_permintaan_pengembangan = PermintaanPengembangan::count();
        $trx_perencanaan_proyek = PerencanaanProyek::count();
        $trx_perencanaan_kebutuhan = PerencanaanKebutuhan::count();
        $trx_persetujuan_pengembangan = PersetujuanPengembangan::count();
        $produk = Produk::count();
        $trx_analisis_desain = AnalisisDesain::count();
        $trx_user_acceptance_testing = UserAcceptanceTesting::count();
        $trx_quality_assurance_testing = QualityAssuranceTesting::count();
        $trx_serah_terima_aplikasi = serahterimaaplikasi::count();
        $supplier = Supplier::count();
        $member = Member::count();

        $tanggal_awal = date('Y-m-01');
        $tanggal_akhir = date('Y-m-d');

        $data_tanggal = array();
        $data_pendapatan = array();

        while (strtotime($tanggal_awal) <= strtotime($tanggal_akhir)) {
            $data_tanggal[] = (int) substr($tanggal_awal, 8, 2);

            $total_penjualan = Penjualan::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('bayar');
            $total_pembelian = Pembelian::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('bayar');
            $total_pengeluaran = Pengeluaran::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('nominal');

            $pendapatan = $total_penjualan - $total_pembelian - $total_pengeluaran;
            $data_pendapatan[] += $pendapatan;

            $tanggal_awal = date('Y-m-d', strtotime("+1 day", strtotime($tanggal_awal)));
        }

        $tanggal_awal = date('Y-m-01');

        // Revision For Dashboard Dev Progress Project 
        $trx_perencanaan_proyek_data = PerencanaanProyek::all();

        if (auth()->user()->level == 1) {
            return view('admin.dashboard_dev_data', compact('kategori', 'trx_perencanaan_proyek', 'trx_persetujuan_pengembangan' ,'trx_permintaan_pengembangan', 'trx_perencanaan_kebutuhan', 'trx_analisis_desain', 'trx_user_acceptance_testing','trx_quality_assurance_testing','trx_serah_terima_aplikasi','produk', 'supplier', 'member', 'tanggal_awal', 'tanggal_akhir', 'data_tanggal', 'data_pendapatan', 'trx_perencanaan_proyek_data'));
        } else {
            return view('kasir.dashboard_dev_data');
        }
    }

    public function dashboard_data_ver2()
    {
        $kategori = Kategori::count();
        $trx_permintaan_pengembangan =  DB::table('flag_status')
                                        ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                        ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                        ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan')
                                        ->having(DB::raw('MAX(flag_status.flag)'), '=', 1)
                                        ->count();

        $trx_persetujuan_pengembangan = DB::table('flag_status')
                                        ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                        ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                        ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                        ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan')
                                        ->having(DB::raw('MAX(flag_status.flag)'), '=', 2)
                                        ->count();

        $trx_perencanaan_proyek  =  DB::table('flag_status')
                                    ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                    ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                    ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                    ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                                    ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan')
                                    ->having(DB::raw('MAX(flag_status.flag)'), '=', 3)
                                    ->count();

        $trx_perencanaan_kebutuhan = DB::table('flag_status')
                                     ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                     ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                     ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                     ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                                     ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan')
                                     ->having(DB::raw('MAX(flag_status.flag)'), '=', 4)
                                     ->count();
                                     
        $trx_analisis_desain  = DB::table('flag_status')
                                ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                ->leftJoin('trx_analisis_desain as tad', 'tad.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
                                ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan')
                                ->having(DB::raw('MAX(flag_status.flag)'), '=', 5)
                                ->count();

        $trx_user_acceptance_testing  = DB::table('flag_status')
                                        ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                        ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                        ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                        ->leftJoin('trx_quality_assurance_testing as tqat', 'tqat.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
                                        ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan')
                                        ->having(DB::raw('MAX(flag_status.flag)'), '=', 6)
                                        ->count();
                                        
        $trx_quality_assurance_testing = DB::table('flag_status')
                                         ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                         ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                         ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                         ->leftJoin('trx_user_acceptance_testing as tuat', 'tuat.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
                                         ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan')
                                         ->having(DB::raw('MAX(flag_status.flag)'), '=', 7)
                                         ->count();
                                         
        $trx_serah_terima_aplikasi  =   DB::table('flag_status')
                                        ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                        ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                        ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                        ->leftJoin('trx_serah_terima_aplikasi as tsta', 'tsta.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
                                        ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan')
                                        ->having(DB::raw('MAX(flag_status.flag)'), '=', 8)
                                        ->count();

        // Tidak Memakai Progress Status Live Step (Sedang Berjalan)
        // $trx_permintaan_pengembangan =  DB::table('trx_permintaan_pengembangan')
        //                                 ->count();

        // $trx_persetujuan_pengembangan = DB::table('trx_persetujuan_pengembangan')
        //                                 ->count();

        // $trx_perencanaan_proyek  =  DB::table('trx_perencanaan_proyek')
        //                             ->count();

        // $trx_perencanaan_kebutuhan = DB::table('trx_perencanaan_kebutuhan')
        //                              ->count();
                                     
        // $trx_analisis_desain  = DB::table('trx_analisis_desain')
        //                         ->count();

        // $trx_user_acceptance_testing  = DB::table('trx_quality_assurance_testing')
        //                                 ->count();
                                        
        // $trx_quality_assurance_testing = DB::table('trx_user_acceptance_testing')
        //                                  ->count();
                                         
        // $trx_serah_terima_aplikasi  =   DB::table('trx_serah_terima_aplikasi')
        //                                 ->count();
        

        // Contoh memakai Array
        // foreach 
        // $trx[1] = PermintaanPengembangan::count();

        $produk = Produk::count();
        $supplier = Supplier::count();
        $member = Member::count();

        $tanggal_awal = date('Y-m-01');
        $tanggal_akhir = date('Y-m-d');

        $data_tanggal = array();
        $data_pendapatan = array();

        while (strtotime($tanggal_awal) <= strtotime($tanggal_akhir)) {
            $data_tanggal[] = (int) substr($tanggal_awal, 8, 2);

            $total_penjualan = Penjualan::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('bayar');
            $total_pembelian = Pembelian::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('bayar');
            $total_pengeluaran = Pengeluaran::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('nominal');

            $pendapatan = $total_penjualan - $total_pembelian - $total_pengeluaran;
            $data_pendapatan[] += $pendapatan;

            $tanggal_awal = date('Y-m-d', strtotime("+1 day", strtotime($tanggal_awal)));
        }

        $tanggal_awal = date('Y-m-01');

        // Revision For Dashboard Dev Progress Project 
        $trx_perencanaan_proyek_data = PerencanaanProyek::all();

        if (auth()->user()->level == 1) {
            return view('admin.dashboard_dev_data_ver2', compact('kategori', 'trx_perencanaan_proyek', 
            'trx_persetujuan_pengembangan' ,'trx_permintaan_pengembangan', 'trx_perencanaan_kebutuhan', 
            'trx_analisis_desain', 'trx_user_acceptance_testing','trx_quality_assurance_testing',
            'trx_serah_terima_aplikasi','produk', 'supplier', 'member', 'tanggal_awal', 'tanggal_akhir', 
            'data_tanggal',
             'data_pendapatan', 'trx_perencanaan_proyek_data'));
        } else {
            return view('kasir.dashboard_dev_data_ver2');
        }
    }

    public function dashboard_data_ver3()
    {
        $kategori = Kategori::count();
            // For Data in Table
            $data_permintaan_pengembangan = DB::table('flag_status')
                                ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpp.progress', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpp.progress')
                                ->having(DB::raw('MAX(flag_status.flag)'), '=', 1)
                                ->get();

            $data_persetujuan_pengembangan = DB::table('flag_status')
                                ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpp2.progress', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpp2.progress')
                                ->having(DB::raw('MAX(flag_status.flag)'), '=', 2)
                                ->get();

            $data_perencanaan_proyek = DB::table('flag_status')
                                ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                                ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan')
                                ->having(DB::raw('MAX(flag_status.flag)'), '=', 3)
                                ->get();

            $data_perencanaan_kebutuhan = DB::table('flag_status')
                                ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                                ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan')
                                ->having(DB::raw('MAX(flag_status.flag)'), '=', 4)
                                ->get();

            $data_analisis_desain = DB::table('flag_status')
                                ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                ->leftJoin('trx_analisis_desain as tad', 'tad.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
                                ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan')
                                ->having(DB::raw('MAX(flag_status.flag)'), '=', 5)
                                ->get();

            $data_quality_assurance_testing =  DB::table('flag_status')
                                ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                ->leftJoin('trx_quality_assurance_testing as tqat', 'tqat.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
                                ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan')
                                ->having(DB::raw('MAX(flag_status.flag)'), '=', 6)
                                ->get();

            $data_user_acceptance_testing = DB::table('flag_status')
                                ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                ->leftJoin('trx_user_acceptance_testing as tuat', 'tuat.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
                                ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan')
                                ->having(DB::raw('MAX(flag_status.flag)'), '=', 7)
                                ->get();

            $data_serah_terima_aplikasi = DB::table('flag_status')
                                ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                ->leftJoin('trx_serah_terima_aplikasi as tsta', 'tsta.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
                                ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan')
                                ->having(DB::raw('MAX(flag_status.flag)'), '=', 8)
                                ->get();

        // Dashboard Summary With One Query
        // 
        $subQuery    = DB::table('flag_status')
                       ->select('id_permintaan', DB::raw('MAX(flag) as max_flag'))
                       ->groupBy('id_permintaan');

        // Query utama menggunakan Eloquent
        $trx_permintaan_pengembangan = FlagStatus::select(
                            DB::raw('COUNT(DISTINCT flag_status.id_permintaan) as all_task'), // Total permintaan dengan flag = 1
                            DB::raw('SUM(CASE WHEN tpp.progress = 100 THEN 1 ELSE 0 END) as done'), // Jumlah task dengan progress 100%
                            // DB::raw('SUM(CASE WHEN tpp.tanggal_akhir < NOW() THEN 1 ELSE 0 END) as overtime'), // Task yang melewati tanggal akhir
                            DB::raw('COUNT(DISTINCT CASE WHEN subquery.max_flag = 1 AND tpp.progress < 100 THEN flag_status.id_permintaan ELSE NULL END) as on_progress') // Task yang on progress
                        )
                        ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                        ->leftJoinSub($subQuery, 'subquery', function($join) {
                            $join->on('subquery.id_permintaan', '=', 'flag_status.id_permintaan');
                        })
                        ->where('flag_status.flag', 1)
                        ->first();

        // Query utama menggunakan Eloquent untuk setiap status
        $trx_persetujuan_pengembangan = FlagStatus::select(
                    DB::raw('COUNT(DISTINCT flag_status.id_permintaan) as total_persetujuan'), // Total permintaan dengan flag = 2
                    DB::raw('COUNT(DISTINCT CASE WHEN subquery.max_flag = 2 THEN flag_status.id_permintaan ELSE NULL END) as persetujuan_on_progress') // Task yang dalam proses persetujuan
                )
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->leftJoinSub($subQuery, 'subquery', function($join) {
                    $join->on('subquery.id_permintaan', '=', 'flag_status.id_permintaan');
                })
                ->where('flag_status.flag', 2)
                ->first();

        $trx_perencanaan_proyek = FlagStatus::select(
                    DB::raw('COUNT(DISTINCT flag_status.id_permintaan) as total_perencanaan'), // Total permintaan dengan flag = 3
                    DB::raw('COUNT(DISTINCT CASE WHEN subquery.max_flag = 3 THEN flag_status.id_permintaan ELSE NULL END) as perencanaan_on_progress') // Task yang dalam proses perencanaan
                )
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->leftJoinSub($subQuery, 'subquery', function($join) {
                    $join->on('subquery.id_permintaan', '=', 'flag_status.id_permintaan');
                })
                ->where('flag_status.flag', 3)
                ->first();

        $trx_perencanaan_kebutuhan = FlagStatus::select(
                    DB::raw('COUNT(DISTINCT flag_status.id_permintaan) as total_kebutuhan'), // Total permintaan dengan flag = 4
                    DB::raw('COUNT(DISTINCT CASE WHEN subquery.max_flag = 4 THEN flag_status.id_permintaan ELSE NULL END) as kebutuhan_on_progress') // Task yang dalam proses perencanaan kebutuhan
                )
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->leftJoinSub($subQuery, 'subquery', function($join) {
                    $join->on('subquery.id_permintaan', '=', 'flag_status.id_permintaan');
                })
                ->where('flag_status.flag', 4)
                ->first();

        $trx_analisis_desain =  FlagStatus::select(
                    DB::raw('COUNT(DISTINCT flag_status.id_permintaan) as all_task'), // Total permintaan dengan flag = 1
                    DB::raw('SUM(CASE WHEN tad.progress = 100 THEN 1 ELSE 0 END) as done'), // Jumlah task dengan progress 100%
                    // DB::raw('SUM(CASE WHEN tpp.tanggal_akhir < NOW() THEN 1 ELSE 0 END) as overtime'), // Task yang melewati tanggal akhir
                    DB::raw('COUNT(DISTINCT CASE WHEN subquery.max_flag = 5 AND tad.progress < 100 THEN flag_status.id_permintaan ELSE NULL END) as on_progress') // Task yang on progress
                )
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_analisis_desain as tad', 'tad.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
                ->leftJoinSub($subQuery, 'subquery', function($join) {
                    $join->on('subquery.id_permintaan', '=', 'flag_status.id_permintaan');
                })
                ->where('flag_status.flag', 5)
                ->first();

        $trx_user_acceptance_testing = FlagStatus::select(
                    DB::raw('COUNT(DISTINCT flag_status.id_permintaan) as total_uat'), // Total permintaan dengan flag = 6
                    DB::raw('COUNT(DISTINCT CASE WHEN subquery.max_flag = 6 THEN flag_status.id_permintaan ELSE NULL END) as uat_on_progress') // Task yang dalam proses UAT
                )
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->leftJoinSub($subQuery, 'subquery', function($join) {
                    $join->on('subquery.id_permintaan', '=', 'flag_status.id_permintaan');
                })
                ->where('flag_status.flag', 6)
                ->first();

        $trx_quality_assurance_testing = FlagStatus::select(
                    DB::raw('COUNT(DISTINCT flag_status.id_permintaan) as total_qat'), // Total permintaan dengan flag = 7
                    DB::raw('COUNT(DISTINCT CASE WHEN subquery.max_flag = 7 THEN flag_status.id_permintaan ELSE NULL END) as qat_on_progress') // Task yang dalam proses QA Testing
                )
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->leftJoinSub($subQuery, 'subquery', function($join) {
                    $join->on('subquery.id_permintaan', '=', 'flag_status.id_permintaan');
                })
                ->where('flag_status.flag', 7)
                ->first();

        $trx_serah_terima_aplikasi = FlagStatus::select(
                    DB::raw('COUNT(DISTINCT flag_status.id_permintaan) as all_task'), // Total permintaan dengan flag = 1
                    DB::raw('SUM(CASE WHEN tsta.progress = 100 THEN 1 ELSE 0 END) as done'), // Jumlah task dengan progress 100%
                    // DB::raw('SUM(CASE WHEN tpp.tanggal_akhir < NOW() THEN 1 ELSE 0 END) as overtime'), // Task yang melewati tanggal akhir
                    DB::raw('COUNT(DISTINCT CASE WHEN subquery.max_flag = 8 AND tsta.progress < 100 THEN flag_status.id_permintaan ELSE NULL END) as on_progress') // Task yang on progress
                )
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_serah_terima_aplikasi as tsta', 'tsta.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
                ->leftJoinSub($subQuery, 'subquery', function($join) {
                    $join->on('subquery.id_permintaan', '=', 'flag_status.id_permintaan');
                })
                ->where('flag_status.flag', 8)
                ->first();

        // Contoh memakai Array
        // foreach 
        // $trx[1] = PermintaanPengembangan::count();

        $produk = Produk::count();
        $supplier = Supplier::count();
        $member = Member::count();

        $tanggal_awal = date('Y-m-01');
        $tanggal_akhir = date('Y-m-d');

        $data_tanggal = array();
        $data_pendapatan = array();

        while (strtotime($tanggal_awal) <= strtotime($tanggal_akhir)) {
            $data_tanggal[] = (int) substr($tanggal_awal, 8, 2);

            $total_penjualan = Penjualan::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('bayar');
            $total_pembelian = Pembelian::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('bayar');
            $total_pengeluaran = Pengeluaran::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('nominal');

            $pendapatan = $total_penjualan - $total_pembelian - $total_pengeluaran;
            $data_pendapatan[] += $pendapatan;

            $tanggal_awal = date('Y-m-d', strtotime("+1 day", strtotime($tanggal_awal)));
        }

        $tanggal_awal = date('Y-m-01');

        // Revision For Dashboard Dev Progress Project 
        $trx_perencanaan_proyek_data = PerencanaanProyek::all();

        if (auth()->user()->level == 1) {
            return view('admin.dashboard_dev_data_ver3', compact('kategori', 'trx_perencanaan_proyek', 
            'trx_persetujuan_pengembangan' ,'trx_permintaan_pengembangan', 'trx_perencanaan_kebutuhan', 
            'trx_analisis_desain', 'trx_user_acceptance_testing','trx_quality_assurance_testing',
            'trx_serah_terima_aplikasi','produk', 'supplier', 'member', 'tanggal_awal', 'tanggal_akhir', 
            'data_tanggal','data_pendapatan', 'trx_perencanaan_proyek_data','data_perencanaan_proyek', 
            'data_persetujuan_pengembangan' ,'data_permintaan_pengembangan', 'data_perencanaan_kebutuhan', 
            'data_analisis_desain', 'data_user_acceptance_testing','data_quality_assurance_testing',
            'data_serah_terima_aplikasi'));
        } else {
            return view('kasir.dashboard_dev_data_ver3');
        }
    }

    public function dashboard_data_ver4()
    {
        $totalProject = DB::table('trx_permintaan_pengembangan as tpp')
                        ->leftjoin('trx_persetujuan_pengembangan as tpp2', 'tpp.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
                        ->leftjoin('trx_perencanaan_proyek as tpp3', 'tpp2.id_persetujuan_pengembangan', '=', 'tpp3.id_persetujuan_pengembangan')
                        ->leftjoin('trx_perencanaan_kebutuhan as tpp4', 'tpp2.id_persetujuan_pengembangan', '=', 'tpp4.id_persetujuan_pengembangan')
                        ->leftjoin('trx_analisis_desain as tpp5', 'tpp.id_permintaan_pengembangan', '=', 'tpp5.id_permintaan_pengembangan')
                        ->leftjoin('trx_quality_assurance_testing as tpp6', 'tpp.id_permintaan_pengembangan', '=', 'tpp6.id_permintaan_pengembangan')
                        ->leftjoin('trx_user_acceptance_testing as tpp7', 'tpp.id_permintaan_pengembangan', '=', 'tpp7.id_permintaan_pengembangan')
                        ->when(auth()->user()->level == 3, function ($query) {
                            // Jika level == 3, memeriksa nik_penyetuju di semua tabel yang relevan
                            return $query->where('tpp.nik_penyetuju', auth()->user()->nik)
                            ->orWhere('tpp2.nik_penyetuju', auth()->user()->nik)
                            ->orWhere('tpp3.nik_penyetuju', auth()->user()->nik)
                            ->orWhere('tpp4.nik_penyetuju', auth()->user()->nik)
                            ->orWhere('tpp5.nik_penyetuju', auth()->user()->nik)
                            ->orWhere('tpp6.nik_penyetuju', auth()->user()->nik)
                            ->orWhere('tpp7.nik_penyetuju', auth()->user()->nik);
                        })
                        ->when(auth()->user()->level == 1, function ($query) {
                            return $query;
                        }, function ($query) {
                            // Jika bukan level 3, memeriksa berdasarkan created_by
                            return $query->where('tpp.created_by', auth()->user()->id)
                                        ->orWhere('tpp2.created_by', auth()->user()->id)
                                        ->orWhere('tpp3.created_by', auth()->user()->id)
                                        ->orWhere('tpp4.created_by', auth()->user()->id)
                                        ->orWhere('tpp5.created_by', auth()->user()->id)
                                        ->orWhere('tpp6.created_by', auth()->user()->id)
                                        ->orWhere('tpp7.created_by', auth()->user()->id);
                        })
                        // Mengambil hanya nomor dokumen yang unik
                        ->distinct('tpp.id_permintaan_pengembangan')
                        ->count();        
            
            // For Data in Table
            $data_permintaan_pengembangan = DB::table('flag_status')
                                ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', 'tpp.progress', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                ->when(auth()->user()->level == 3, function ($query) {
                                    return $query->where('tpp.nik_penyetuju', auth()->user()->nik);
                                }, function ($query) {
                                    return $query->where('tpp.created_by', auth()->user()->id); 
                                })
                                ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', 'tpp.progress')
                                ->having(DB::raw('MAX(flag_status.flag)'), '=', 1)
                                ->get();

            $data_persetujuan_pengembangan = DB::table('flag_status')
                                ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', 'tpp2.progress', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                ->when(auth()->user()->level == 3, function ($query) {
                                    return $query->where('tpp2.nik_penyetuju', auth()->user()->nik);
                                }, function ($query) {
                                    return $query->where('tpp2.created_by', auth()->user()->id); 
                                })
                                ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', 'tpp2.progress')
                                ->having(DB::raw('MAX(flag_status.flag)'), '=', 2)
                                ->get();

            $data_perencanaan_proyek = DB::table('flag_status')
                                ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                                ->when(auth()->user()->level == 3, function ($query) {
                                    return $query->where('tpp3.nik_penyetuju', auth()->user()->nik);
                                }, function ($query) {
                                    return $query->where('tpp3.created_by', auth()->user()->id); 
                                })
                                ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan')
                                ->having(DB::raw('MAX(flag_status.flag)'), '=', 3)
                                ->get();

            $data_perencanaan_kebutuhan = DB::table('flag_status')
                                ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                                ->when(auth()->user()->level == 3, function ($query) {
                                    return $query->where('tpk.nik_penyetuju', auth()->user()->nik);
                                }, function ($query) {
                                    return $query->where('tpk.created_by', auth()->user()->id); 
                                })
                                ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan')
                                ->having(DB::raw('MAX(flag_status.flag)'), '=', 4)
                                ->get();

            $data_analisis_desain = DB::table('flag_status')
                                ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                ->leftJoin('trx_analisis_desain as tad', 'tad.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
                                ->when(auth()->user()->level == 3, function ($query) {
                                    return $query->where('tad.nik_penyetuju', auth()->user()->nik);
                                }, function ($query) {
                                    return $query->where('tad.created_by', auth()->user()->id); 
                                })
                                ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan')
                                ->having(DB::raw('MAX(flag_status.flag)'), '=', 5)
                                ->get();

            $data_quality_assurance_testing =  DB::table('flag_status')
                                ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                ->leftJoin('trx_quality_assurance_testing as tqat', 'tqat.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
                                ->when(auth()->user()->level == 3, function ($query) {
                                    return $query->where('tqat.nik_penyetuju', auth()->user()->nik);
                                }, function ($query) {
                                    return $query->where('tqat.created_by', auth()->user()->id); 
                                })
                                ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan')
                                ->having(DB::raw('MAX(flag_status.flag)'), '=', 6)
                                ->get();

            $data_user_acceptance_testing = DB::table('flag_status')
                                ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                ->leftJoin('trx_user_acceptance_testing as tuat', 'tuat.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
                                ->when(auth()->user()->level == 3, function ($query) {
                                    return $query->where('tuat.nik_penyetuju', auth()->user()->nik);
                                }, function ($query) {
                                    return $query->where('tuat.created_by', auth()->user()->id); 
                                })
                                ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan')
                                ->having(DB::raw('MAX(flag_status.flag)'), '=', 7)
                                ->get();

            $data_serah_terima_aplikasi = DB::table('flag_status')
                                ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                ->leftJoin('trx_serah_terima_aplikasi as tsta', 'tsta.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
                                ->when(auth()->user()->level == 3, function ($query) {
                                    return $query->where('tsta.nik_penyetuju', auth()->user()->nik);
                                }, function ($query) {
                                    return $query->where('tsta.created_by', auth()->user()->id); 
                                })
                                ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan')
                                ->having(DB::raw('MAX(flag_status.flag)'), '=', 8)
                                ->get();

        // Dashboard Summary With One Query
        $kategori   = Kategori::count();
        // 
        $subQuery2  = DB::table('flag_status')
                      ->select('id_permintaan', DB::raw('MAX(flag) as max_flag'))
                      ->groupBy('id_permintaan');

        // // Query utama menggunakan Eloquent
        // $trx_permintaan_pengembangan = FlagStatus::select(
        //                     DB::raw('COUNT(DISTINCT flag_status.id_permintaan) as all_task'), // Total permintaan dengan flag = 1
        //                     DB::raw('SUM(CASE WHEN tpp.progress = 100 THEN 1 ELSE 0 END) as done'), // Jumlah task dengan progress 100%
        //                     // DB::raw('SUM(CASE WHEN tpp.tanggal_akhir < NOW() THEN 1 ELSE 0 END) as overtime'), // Task yang melewati tanggal akhir
        //                     DB::raw('COUNT(DISTINCT CASE WHEN subquery.max_flag = 1 AND tpp.progress < 100 THEN flag_status.id_permintaan ELSE NULL END) as on_progress') // Task yang on progress
        //                 )
        //                 ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
        //                 ->leftJoinSub($subQuery2, 'subquery', function($join) {
        //                     $join->on('subquery.id_permintaan', '=', 'flag_status.id_permintaan');
        //                 })
        //                 ->where('flag_status.flag', 1)
        //                 ->first();

        // // Query utama menggunakan Eloquent untuk setiap status
        // $trx_persetujuan_pengembangan = FlagStatus::select(
        //             DB::raw('COUNT(DISTINCT flag_status.id_permintaan) as total_persetujuan'), // Total permintaan dengan flag = 2
        //             DB::raw('COUNT(DISTINCT CASE WHEN subquery.max_flag = 2 THEN flag_status.id_permintaan ELSE NULL END) as persetujuan_on_progress') // Task yang dalam proses persetujuan
        //         )
        //         ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
        //         ->leftJoinSub($subQuery2, 'subquery', function($join) {
        //             $join->on('subquery.id_permintaan', '=', 'flag_status.id_permintaan');
        //         })
        //         ->where('flag_status.flag', 2)
        //         ->first();

        // $trx_perencanaan_proyek = FlagStatus::select(
        //             DB::raw('COUNT(DISTINCT flag_status.id_permintaan) as total_perencanaan'), // Total permintaan dengan flag = 3
        //             DB::raw('COUNT(DISTINCT CASE WHEN subquery.max_flag = 3 THEN flag_status.id_permintaan ELSE NULL END) as perencanaan_on_progress') // Task yang dalam proses perencanaan
        //         )
        //         ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
        //         ->leftJoinSub($subQuery2, 'subquery', function($join) {
        //             $join->on('subquery.id_permintaan', '=', 'flag_status.id_permintaan');
        //         })
        //         ->where('flag_status.flag', 3)
        //         ->first();
        
        $trx_permintaan_pengembangan =  DB::table('flag_status')
                ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', DB::raw('MAX(flag_status.flag) AS max_flag'))
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->when(auth()->user()->level == 1, function ($query) {
                    return $query;
                })
                ->when(auth()->user()->level == 3, function ($query) {
                    return $query->where('tpp.nik_penyetuju', auth()->user()->nik);
                }, function ($query) {
                    return $query->where('tpp.created_by', auth()->user()->id); 
                })
                ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan')
                ->having(DB::raw('MAX(flag_status.flag)'), '=', 1)
                ->count();

        // Is Approve
        $is_approve_permintaan_pengembangan  =  PermintaanPengembangan::where('is_approve', 1)
                                                ->when(true, function ($query) {
                                                    // Mengecek level user
                                                    if (auth()->user()->level == 1) {
                                                        // Jika level adalah 1, tidak ada perubahan pada query
                                                        return $query;
                                                    } elseif (auth()->user()->level == 3) {
                                                        // Jika level adalah 3, tambahkan filter berdasarkan nik_penyetuju
                                                        return $query->where('nik_penyetuju', auth()->user()->nik);
                                                    } else {
                                                        // Jika level bukan 1 atau 3, tambahkan filter berdasarkan created_by
                                                        return $query->where('created_by', auth()->user()->id);
                                                    }
                                                })->count();

        // Not Approve
        $is_not_approve_permintaan_pengembangan = PermintaanPengembangan::where('is_approve', NULL)
                                                        ->when(true, function ($query) {
                                                            // Mengecek level user
                                                            if (auth()->user()->level == 1) {
                                                                // Jika level adalah 1, tidak ada perubahan pada query
                                                                return $query;
                                                            } elseif (auth()->user()->level == 3) {
                                                                // Jika level adalah 3, filter berdasarkan nik_penyetuju
                                                                return $query->where('nik_penyetuju', auth()->user()->nik);
                                                            } else {
                                                                // Jika level bukan 3, filter berdasarkan created_by
                                                                return $query->where('created_by', auth()->user()->id);
                                                            }
                                                        })->count();    

                        $trx_persetujuan_pengembangan = DB::table('flag_status')
                                                        ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                                        ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                                        ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                                        ->when(true, function ($query) {
                                                            // Mengecek level user
                                                            if (auth()->user()->level == 1) {
                                                                // Jika level adalah 1, filter berdasarkan created_by
                                                                return $query->where('tpp2.created_by', auth()->user()->id);
                                                            } elseif (auth()->user()->level == 3) {
                                                                // Jika level adalah 3, filter berdasarkan nik_penyetuju
                                                                return $query->where('tpp2.nik_penyetuju', auth()->user()->nik);
                                                            } else {
                                                                // Jika level selain 1 dan 3, filter berdasarkan created_by
                                                                return $query->where('tpp2.created_by', auth()->user()->id);
                                                            }
                                                        })
                                                        ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan')
                                                        ->having(DB::raw('MAX(flag_status.flag)'), '=', 2)
                                                        ->count();                                                    
        
        // Is Approve
        $is_approve_persetujuan_pengembangan = PersetujuanPengembangan::where('is_approve', 1)
                                                ->when(true, function ($query) {
                                                    // Mengecek level user
                                                    if (auth()->user()->level == 1) {
                                                        // Jika level adalah 1, filter berdasarkan created_by
                                                        return $query;
                                                    } elseif (auth()->user()->level == 3) {
                                                        // Jika level adalah 3, filter berdasarkan nik_penyetuju
                                                        return $query->where('nik_penyetuju', auth()->user()->nik);
                                                    } else {
                                                        // Jika level selain 1 atau 3, filter berdasarkan created_by
                                                        return $query->where('created_by', auth()->user()->id);
                                                    }
                                                })
                                                ->count();    
        // Not Approve
        $is_not_approve_persetujuan_pengembangan  = PersetujuanPengembangan::where('is_approve', NULL)
                                                    ->when(auth()->user()->level == 1, function ($query) {
                                                        // Jika level adalah 1, kembalikan query tanpa filter khusus
                                                        return $query;
                                                    })
                                                    ->when(auth()->user()->level == 3, function ($query) {
                                                        // Jika level adalah 3, filter berdasarkan nik_penyetuju
                                                        return $query->where('nik_penyetuju', auth()->user()->nik);
                                                    }, function ($query) {
                                                        // Untuk level lainnya (selain 1 dan 3), filter berdasarkan created_by
                                                        return $query->where('created_by', auth()->user()->id);
                                                    })
                                                    ->count();    

        $trx_perencanaan_proyek_x =  DB::table('flag_status')
                ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', DB::raw('MAX(flag_status.flag) AS max_flag'))
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan')
                ->having(DB::raw('MAX(flag_status.flag)'), '=', 3)
                ->count();
        
        // Is Approve
        $is_approve_perencanaan_proyek = PerencanaanProyek::where('is_approve', 1)
                                        ->when(auth()->user()->level == 3, function ($query) {
                                            return $query->where('nik_penyetuju', auth()->user()->nik);
                                        }, function ($query) {
                                            return $query->where('created_by', auth()->user()->id); 
                                        })->count();

        // Not Approve
        $is_not_approve_perencanaan_proyek = PerencanaanProyek::where('is_approve', NULL)
                                             ->when(auth()->user()->level == 3, function ($query) {
                                                return $query->where('nik_penyetuju', auth()->user()->nik);
                                             }, function ($query) {
                                                return $query->where('created_by', auth()->user()->id); 
                                             })->count();

        // $trx_perencanaan_kebutuhan = FlagStatus::select(
        //             DB::raw('COUNT(DISTINCT flag_status.id_permintaan) as total_kebutuhan'), // Total permintaan dengan flag = 4
        //             DB::raw('COUNT(DISTINCT CASE WHEN subquery.max_flag = 4 THEN flag_status.id_permintaan ELSE NULL END) as kebutuhan_on_progress') // Task yang dalam proses perencanaan kebutuhan
        //         )
        //         ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
        //         ->leftJoinSub($subQuery2, 'subquery', function($join) {
        //             $join->on('subquery.id_permintaan', '=', 'flag_status.id_permintaan');
        //         })
        //         ->where('flag_status.flag', 4)
        //         ->first();

        $trx_perencanaan_proyek = FlagStatus::select(
            'flag_status.id_permintaan',
            'tpp.nomor_dokumen',
            'tpp.judul',
            'tpp.tujuan',
            'tpp3.progress',
            'tpp.pic',
            DB::raw('MAX(flag_status.flag) as max_flag'),
        
            // Hitung total hijau
            DB::raw("
                COUNT(DISTINCT 
                    CASE 
                        WHEN (
                            -- Kondisi Hijau
                            DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) <= 0.85 * (5 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                        ) 
                        THEN 1 
                        ELSE NULL 
                    END
                ) as total_hijau"),
        
            // Hitung total kuning
            DB::raw("
                COUNT(DISTINCT 
                    CASE 
                        WHEN (
                            -- Kondisi Kuning
                            DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) > 0.85 * (5 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                            AND NOW() <= DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                        ) 
                        THEN 1 
                        ELSE NULL 
                    END
                ) as total_kuning"),
        
            // Hitung total merah
            DB::raw("
                COUNT(DISTINCT 
                    CASE 
                        WHEN (
                            -- Kondisi Merah
                            NOW() > DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                        ) 
                        THEN 1 
                        ELSE NULL 
                    END
                ) as total_merah"),
        
            DB::raw('MAX(flag_status.flag) as max_flag')
        )
        ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
        ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
        ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
        ->when(auth()->user()->level == 1, function ($query) {
            // Jika level adalah 1, kembalikan query tanpa filter khusus
            return $query;
        })
        ->when(auth()->user()->level == 3, function ($query) {
            // Jika level adalah 3, filter berdasarkan nik_penyetuju atau nik_pemverifikasi
            return $query->where('tpp3.nik_penyetuju', auth()->user()->nik)
                         ->orWhere('tpp3.nik_pemverifikasi', auth()->user()->nik);
        }, function ($query) {
            // Untuk level lainnya, filter berdasarkan created_by
            return $query->where('tpp3.created_by', auth()->user()->id); 
        })
        ->groupBy(
            'flag_status.id_permintaan',
            'tpp.nomor_dokumen',
            'tpp.judul',
            'tpp.tujuan',
            'tpp3.progress',
            'tpp.pic',
            'tpp3.tanggal_mulai',
            'tpp3.target_selesai'
        )
        ->having(DB::raw('MAX(flag_status.flag)'), '=', 3)
        ->first();
        
        $trx_perencanaan_proyek_history = DB::table('flag_status')
                                        ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', 'tpp3.progress', 'tpp.pic', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                        ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                        ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                        ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                                        ->when(auth()->user()->level == 3, function ($query) {
                                            return $query->where('tpp3.nik_penyetuju', auth()->user()->nik);
                                        }, function ($query) {
                                            return $query->where('tpp3.created_by', auth()->user()->id); 
                                        })
                                        ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', 'tpp3.progress', 'tpp.pic')
                                        ->having(DB::raw('MAX(flag_status.flag)'), '=', 3)
                                        ->count();

                                        $trx_perencanaan_kebutuhan = FlagStatus::select(
                                            'flag_status.id_permintaan',
                                            'tpp.nomor_dokumen',
                                            'tpp.judul',
                                            'tpp.tujuan',
                                            'tpk.progress',
                                            'tpp.pic',
                                            DB::raw('MAX(flag_status.flag) as max_flag'),
        
                                            // Hitung total hijau
                                            DB::raw("
                                                COUNT(DISTINCT 
                                                    CASE 
                                                        WHEN (
                                                            -- Kondisi Hijau
                                                            DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) <= 0.85 * (5 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                                        ) 
                                                        THEN 1 
                                                        ELSE NULL 
                                                    END
                                                ) as total_hijau"),
        
                                            // Hitung total kuning
                                            DB::raw("
                                                COUNT(DISTINCT 
                                                    CASE 
                                                        WHEN (
                                                            -- Kondisi Kuning
                                                            DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) > 0.85 * (5 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                                            AND NOW() <= DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                                        ) 
                                                        THEN 1 
                                                        ELSE NULL 
                                                    END
                                                ) as total_kuning"),
        
                                            // Hitung total merah
                                            DB::raw("
                                                COUNT(DISTINCT 
                                                    CASE 
                                                        WHEN (
                                                            -- Kondisi Merah
                                                            NOW() > DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                                        ) 
                                                        THEN 1 
                                                        ELSE NULL 
                                                    END
                                                ) as total_merah"),
        
                                            DB::raw('MAX(flag_status.flag) as max_flag')
                                        )
                                        ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                        ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                        ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                                        ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                                        ->when(auth()->user()->level == 1, function ($query) {
                                            // Jika level adalah 1, kembalikan query tanpa filter
                                            return $query;
                                        })
                                        ->when(auth()->user()->level == 3, function ($query) {
                                            // Jika level adalah 3, filter berdasarkan nik_penyetuju
                                            return $query->where('tpk.nik_penyetuju', auth()->user()->nik);
                                        }, function ($query) {
                                            // Untuk level lainnya, filter berdasarkan created_by
                                            return $query->where('tpk.created_by', auth()->user()->id); 
                                        })
                                        ->groupBy(
                                            'flag_status.id_permintaan',
                                            'tpp.nomor_dokumen',
                                            'tpp.judul',
                                            'tpp.tujuan',
                                            'tpk.progress',
                                            'tpp.pic',
                                            'tpp3.tanggal_mulai',
                                            'tpp3.target_selesai'
                                        )
                                        ->having(DB::raw('MAX(flag_status.flag)'), '=', 4)
                                        ->first();        
        
        $trx_perencanaan_kebutuhan_history = DB::table('flag_status')
                                            ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', 'tpk.progress', 'tpp.pic', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                            ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                            ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                            ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                                            ->when(auth()->user()->level == 3, function ($query) {
                                                return $query->where('tpk.nik_penyetuju', auth()->user()->nik);
                                            }, function ($query) {
                                                return $query->where('tpk.created_by', auth()->user()->id); 
                                            })
                                            ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', 'tpk.progress', 'tpp.pic')
                                            ->having(DB::raw('MAX(flag_status.flag)'), '=', 4)
                                            ->count();
        
        $trx_perencanaan_kebutuhan_data =  FlagStatus::select(
                                            'flag_status.id_permintaan',
                                            'tpp.nomor_dokumen',
                                            'tpp.judul',
                                            'tpp.tujuan',
                                            'tad.progress',
                                            'tpp.pic',
                                            DB::raw('MAX(flag_status.flag) as max_flag'),
                                            // Hitung total hijau
                                            DB::raw("
                                            COUNT(DISTINCT 
                                                CASE 
                                                    WHEN (
                                                        -- Kondisi Hijau
                                                        DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) <= 0.85 * (5 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                                    ) 
                                                    THEN 1 
                                                    ELSE NULL 
                                                END
                                            ) as total_hijau"),
                                            // Hitung total kuning
                                            DB::raw("
                                                COUNT(DISTINCT 
                                                    CASE 
                                                        WHEN (
                                                            -- Kondisi Kuning
                                                            DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) > 0.85 * (5 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                                            AND NOW() <= DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                                        ) 
                                                        THEN 1 
                                                        ELSE NULL 
                                                    END
                                                ) as total_kuning"),

                                            // Hitung total merah
                                            DB::raw("
                                                COUNT(DISTINCT 
                                                    CASE 
                                                        WHEN (
                                                            -- Kondisi Merah
                                                            NOW() > DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                                        ) 
                                                        THEN 1 
                                                        ELSE NULL 
                                                    END
                                                ) as total_merah"),
                                            DB::raw('MAX(flag_status.flag) as max_flag')
                                        )
                                        ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                        ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                        ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                                        ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                                        ->leftJoin('trx_analisis_desain as tad', 'tad.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                        ->when(auth()->user()->level == 3, function ($query) {
                                            return $query->where('tad.nik_penyetuju', auth()->user()->nik);
                                        }, function ($query) {
                                            return $query->where('tad.created_by', auth()->user()->id); 
                                        })
                                        ->groupBy(
                                            'flag_status.id_permintaan',
                                            'tpp.nomor_dokumen',
                                            'tpp.judul',
                                            'tpp.tujuan',
                                            'tad.progress',
                                            'tpp.pic',
                                            'tpp3.tanggal_mulai',
                                            'tpp3.target_selesai'
                                        )
                                        ->having(DB::raw('MAX(flag_status.flag)'), '=', 5)
                                        ->first();

                                        $trx_analisis_desain = FlagStatus::select(
                                            'flag_status.id_permintaan',
                                            'tpp.nomor_dokumen',
                                            'tpp.judul',
                                            'tpp.tujuan',
                                            'tad.progress',
                                            'tpp.pic',
                                            DB::raw('MAX(flag_status.flag) as max_flag'),

                                            // Hitung total hijau
                                            DB::raw("
                                                COUNT(DISTINCT 
                                                    CASE 
                                                        WHEN (
                                                            -- Kondisi Hijau
                                                            DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) <= 0.85 * (25 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                                        ) 
                                                        THEN 1 
                                                        ELSE NULL 
                                                    END
                                                ) as total_hijau"),

                                            // Hitung total kuning
                                            DB::raw("
                                                COUNT(DISTINCT 
                                                    CASE 
                                                        WHEN (
                                                            -- Kondisi Kuning
                                                            DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) > 0.85 * (25 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                                            AND NOW() <= DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                                        ) 
                                                        THEN 1 
                                                        ELSE NULL 
                                                    END
                                                ) as total_kuning"),

                                            // Hitung total merah
                                            DB::raw("
                                                COUNT(DISTINCT 
                                                    CASE 
                                                        WHEN (
                                                            -- Kondisi Merah
                                                            NOW() > DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 25 / 100) DAY)
                                                        ) 
                                                        THEN 1 
                                                        ELSE NULL 
                                                    END
                                                ) as total_merah"),

                                            DB::raw('MAX(flag_status.flag) as max_flag')
                                        )
                                        ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                        ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                        ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                                        ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                                        ->leftJoin('trx_analisis_desain as tad', 'tad.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                        ->when(auth()->user()->level == 1, function ($query) {
                                            // Jika level adalah 1, kembalikan query tanpa filter
                                            return $query;
                                        })
                                        ->when(auth()->user()->level == 3, function ($query) {
                                            // Jika level adalah 3, filter berdasarkan nik_penyetuju
                                            return $query->where('tad.nik_penyetuju', auth()->user()->nik);
                                        }, function ($query) {
                                            // Untuk level lainnya, filter berdasarkan created_by
                                            return $query->where('tad.created_by', auth()->user()->id); 
                                        })
                                        ->groupBy(
                                            'flag_status.id_permintaan',
                                            'tpp.nomor_dokumen',
                                            'tpp.judul',
                                            'tpp.tujuan',
                                            'tad.progress',
                                            'tpp.pic',
                                            'tpp3.tanggal_mulai',
                                            'tpp3.target_selesai'
                                        )
                                        ->having(DB::raw('MAX(flag_status.flag)'), '=', 5)
                                        ->first();

        $trx_analisis_desain_history  = DB::table('flag_status')
                                        ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', 'tad.progress', 'tpp.pic', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                        ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                        ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                        ->leftJoin('trx_analisis_desain as tad', 'tad.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
                                        ->when(auth()->user()->level == 3, function ($query) {
                                            return $query->where('tad.nik_penyetuju', auth()->user()->nik);
                                        }, function ($query) {
                                            return $query->where('tad.created_by', auth()->user()->id); 
                                        })
                                        ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', 'tad.progress', 'tpp.pic')
                                        ->having(DB::raw('MAX(flag_status.flag)'), '=', 5)
                                        ->count();

        // Test
        $trx_pengembangan_aplikasi =   FlagStatus::select(
                    'flag_status.id_permintaan',
                    'tpp.nomor_dokumen',
                    'tpp.judul',
                    'tpp.tujuan',
                    'tpa.progress',
                    'tpp.pic',
                    DB::raw('MAX(flag_status.flag) as max_flag'),
                    // Hitung total hijau
                    DB::raw("
                    COUNT(DISTINCT 
                        CASE 
                            WHEN (
                                -- Kondisi Hijau
                                DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) <= 0.85 * (50 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                            ) 
                            THEN 1 
                            ELSE NULL 
                        END
                    ) as total_hijau"),
                    // Hitung total kuning
                    DB::raw("
                        COUNT(DISTINCT 
                            CASE 
                                WHEN (
                                    -- Kondisi Kuning
                                    DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) > 0.85 * (50 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                    AND NOW() <= DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                ) 
                                THEN 1 
                                ELSE NULL 
                            END
                        ) as total_kuning"),

                    // Hitung total merah
                    DB::raw("
                        COUNT(DISTINCT 
                            CASE 
                                WHEN (
                                    -- Kondisi Merah
                                    NOW() > DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                ) 
                                THEN 1 
                                ELSE NULL 
                            END
                        ) as total_merah"),
                    DB::raw('MAX(flag_status.flag) as max_flag')
                )
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                ->leftJoin('trx_analisis_desain as tad', 'tad.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_pengembangan_aplikasi as tpa', 'tpa.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->when(auth()->user()->level == 3, function ($query) {
                    return $query->where('tpa.nik_penyetuju', auth()->user()->nik);
                }, function ($query) {
                    return $query->where('tpa.created_by', auth()->user()->id); 
                })
                ->groupBy(
                    'flag_status.id_permintaan',
                    'tpp.nomor_dokumen',
                    'tpp.judul',
                    'tpp.tujuan',
                    'tpa.progress',
                    'tpp.pic',
                    'tpp3.tanggal_mulai',
                    'tpp3.target_selesai'
                )
                ->having(DB::raw('MAX(flag_status.flag)'), '=', 6)
                ->first();

        $trx_pengembangan_aplikasi_history= DB::table('flag_status')
                ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', 'tpa.progress', 'tpp.pic', DB::raw('MAX(flag_status.flag) AS max_flag'))
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_pengembangan_aplikasi as tpa', 'tpa.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
                ->when(auth()->user()->level == 3, function ($query) {
                    return $query->where('tpa.nik_penyetuju', auth()->user()->nik);
                }, function ($query) {
                    return $query->where('tpa.created_by', auth()->user()->id); 
                })
                ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', 'tpa.progress', 'tpp.pic')
                ->having(DB::raw('MAX(flag_status.flag)'), '=', 6)
                ->count();

        $trx_user_acceptance_testing    =   FlagStatus::select(
                                                'flag_status.id_permintaan',
                                                'tpp.nomor_dokumen',
                                                'tpp.judul',
                                                'tpp.tujuan',
                                                'tad.progress',
                                                'tpp.pic',
                                                DB::raw('MAX(flag_status.flag) as max_flag'),
                                                // Hitung total hijau
                                                DB::raw("
                                                COUNT(DISTINCT 
                                                    CASE 
                                                        WHEN (
                                                            -- Kondisi Hijau
                                                            DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) <= 0.85 * (50 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                                        ) 
                                                        THEN 1 
                                                        ELSE NULL 
                                                    END
                                                ) as total_hijau"),
                                                // Hitung total kuning
                                                DB::raw("
                                                    COUNT(DISTINCT 
                                                        CASE 
                                                            WHEN (
                                                                -- Kondisi Kuning
                                                                DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) > 0.85 * (50 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                                                AND NOW() <= DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                                            ) 
                                                            THEN 1 
                                                            ELSE NULL 
                                                        END
                                                    ) as total_kuning"),

                                                // Hitung total merah
                                                DB::raw("
                                                    COUNT(DISTINCT 
                                                        CASE 
                                                            WHEN (
                                                                -- Kondisi Merah
                                                                NOW() > DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                                            ) 
                                                            THEN 1 
                                                            ELSE NULL 
                                                        END
                                                    ) as total_merah"),
                                                DB::raw('MAX(flag_status.flag) as max_flag')
                                            )
                                            ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                            ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                            ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                                            ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                                            ->leftJoin('trx_analisis_desain as tad', 'tad.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                            ->leftJoin('trx_user_acceptance_testing as tuat', 'tuat.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                            ->when(auth()->user()->level == 3, function ($query) {
                                                return $query->where('tuat.nik_penyetuju', auth()->user()->nik);
                                            }, function ($query) {
                                                return $query->where('tuat.created_by', auth()->user()->id); 
                                            })
                                            ->groupBy(
                                                'flag_status.id_permintaan',
                                                'tpp.nomor_dokumen',
                                                'tpp.judul',
                                                'tpp.tujuan',
                                                'tad.progress',
                                                'tpp.pic',
                                                'tpp3.tanggal_mulai',
                                                'tpp3.target_selesai'
                                            )
                                            ->having(DB::raw('MAX(flag_status.flag)'), '=', 8)
                                            ->first();

        $trx_user_acceptance_testing_history= DB::table('flag_status')
                                            ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', 'tuat.progress', 'tpp.pic', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                            ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                            ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                            ->leftJoin('trx_user_acceptance_testing as tuat', 'tuat.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
                                            ->when(auth()->user()->level == 3, function ($query) {
                                                return $query->where('tuat.nik_penyetuju', auth()->user()->nik);
                                            }, function ($query) {
                                                return $query->where('tuat.created_by', auth()->user()->id); 
                                            })
                                            ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', 'tuat.progress', 'tpp.pic')
                                            ->having(DB::raw('MAX(flag_status.flag)'), '=', 8)
                                            ->count();

        $trx_analisis_desain_history    =   DB::table('flag_status')
                                            ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', 'tad.progress', 'tpp.pic', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                            ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                            ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                            ->leftJoin('trx_analisis_desain as tad', 'tad.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
                                            ->when(auth()->user()->level == 3, function ($query) {
                                                return $query->where('tad.nik_penyetuju', auth()->user()->nik);
                                            }, function ($query) {
                                                return $query->where('tad.created_by', auth()->user()->id); 
                                            })
                                            ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', 'tad.progress', 'tpp.pic')
                                            ->having(DB::raw('MAX(flag_status.flag)'), '=', 5)
                                            ->count();

        $trx_quality_assurance_testing = FlagStatus::select(
                                                'flag_status.id_permintaan',
                                                'tpp.nomor_dokumen',
                                                'tpp.judul',
                                                'tpp.tujuan',
                                                'tqat.progress',
                                                'tpp.pic',
                                                DB::raw('MAX(flag_status.flag) as max_flag'),
                                                // Hitung total hijau
                                                DB::raw("
                                                COUNT(DISTINCT 
                                                    CASE 
                                                        WHEN (
                                                            -- Kondisi Hijau
                                                            DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) <= 0.85 * (5 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                                        ) 
                                                        THEN 1 
                                                        ELSE NULL 
                                                    END
                                                ) as total_hijau"),
                                                // Hitung total kuning
                                                DB::raw("
                                                    COUNT(DISTINCT 
                                                        CASE 
                                                            WHEN (
                                                                -- Kondisi Kuning
                                                                DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) > 0.85 * (5 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                                                AND NOW() <= DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                                            ) 
                                                            THEN 1 
                                                            ELSE NULL 
                                                        END
                                                    ) as total_kuning"),

                                                // Hitung total merah
                                                DB::raw("
                                                    COUNT(DISTINCT 
                                                        CASE 
                                                            WHEN (
                                                                -- Kondisi Merah
                                                                NOW() > DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                                            ) 
                                                            THEN 1 
                                                            ELSE NULL 
                                                        END
                                                    ) as total_merah"),
                                                DB::raw('MAX(flag_status.flag) as max_flag')
                                            )
                                            ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                            ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                            ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                                            ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                                            ->leftJoin('trx_analisis_desain as tad', 'tad.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                            ->leftJoin('trx_user_acceptance_testing as tuat', 'tuat.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                            ->leftJoin('trx_quality_assurance_testing as tqat', 'tqat.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                            ->when(auth()->user()->level == 3, function ($query) {
                                                return $query->where('tqat.nik_penyetuju', auth()->user()->nik);
                                            })
                                            ->when(auth()->user()->level == 1, function ($query) {
                                                return $query;
                                            }, function ($query) {
                                                return $query->where('tqat.created_by', auth()->user()->id); 
                                            })
                                            ->groupBy(
                                                'flag_status.id_permintaan',
                                                'tpp.nomor_dokumen',
                                                'tpp.judul',
                                                'tpp.tujuan',
                                                'tqat.progress',
                                                'tpp.pic',
                                                'tpp3.tanggal_mulai',
                                                'tpp3.target_selesai'
                                            )
                                            ->having(DB::raw('MAX(flag_status.flag)'), '=', 7)
                                            ->first();
        
        $trx_quality_assurance_testing_history = DB::table('flag_status')
                                                ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', 'tqat.progress', 'tpp.pic', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                                ->leftJoin('trx_quality_assurance_testing as tqat', 'tqat.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
                                                ->when(auth()->user()->level == 3, function ($query) {
                                                    return $query->where('tqat.nik_penyetuju', auth()->user()->nik);
                                                })
                                                ->when(auth()->user()->level == 1, function ($query) {
                                                    return $query;
                                                }, function ($query) {
                                                    return $query->where('tqat.created_by', auth()->user()->id); 
                                                })
                                                ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', 'tqat.progress', 'tpp.pic')
                                                ->having(DB::raw('MAX(flag_status.flag)'), '=', 7)
                                                ->count();
        
        $trx_serah_terima_aplikasi = FlagStatus::select(
                                                'flag_status.id_permintaan',
                                                'tpp.nomor_dokumen',
                                                'tpp.judul',
                                                'tpp.tujuan',
                                                'tsta.progress',
                                                'tpp.pic',
                                                DB::raw('MAX(flag_status.flag) as max_flag'),
                                                // Hitung total hijau
                                                DB::raw("
                                                COUNT(DISTINCT 
                                                    CASE 
                                                        WHEN (
                                                            -- Kondisi Hijau
                                                            DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) <= 0.85 * (5 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                                        ) 
                                                        THEN 1 
                                                        ELSE NULL 
                                                    END
                                                ) as total_hijau"),
                                                // Hitung total kuning
                                                DB::raw("
                                                    COUNT(DISTINCT 
                                                        CASE 
                                                            WHEN (
                                                                -- Kondisi Kuning
                                                                DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) > 0.85 * (5 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                                                AND NOW() <= DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                                            ) 
                                                            THEN 1 
                                                            ELSE NULL 
                                                        END
                                                    ) as total_kuning"),

                                                // Hitung total merah
                                                DB::raw("
                                                    COUNT(DISTINCT 
                                                        CASE 
                                                            WHEN (
                                                                -- Kondisi Merah
                                                                NOW() > DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                                            ) 
                                                            THEN 1 
                                                            ELSE NULL 
                                                        END
                                                    ) as total_merah"),
                                                DB::raw('MAX(flag_status.flag) as max_flag')
                                            )
                                            ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                            ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                            ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                                            ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                                            ->leftJoin('trx_analisis_desain as tad', 'tad.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                            ->leftJoin('trx_user_acceptance_testing as tuat', 'tuat.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                            ->leftJoin('trx_quality_assurance_testing as tqat', 'tqat.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                            ->leftJoin('trx_serah_terima_aplikasi as tsta', 'tsta.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                            ->when(auth()->user()->level == 3, function ($query) {
                                                return $query->where('tsta.nik_penyetuju', auth()->user()->nik);
                                            }, function ($query) {
                                                return $query->where('tsta.created_by', auth()->user()->id); 
                                            })
                                            ->groupBy(
                                                'flag_status.id_permintaan',
                                                'tpp.nomor_dokumen',
                                                'tpp.judul',
                                                'tpp.tujuan',
                                                'tsta.progress',
                                                'tpp.pic',
                                                'tpp3.tanggal_mulai',
                                                'tpp3.target_selesai'
                                            )
                                            ->having(DB::raw('MAX(flag_status.flag)'), '=', 9)
                                            ->first();
                                            
        $trx_serah_terima_aplikasi_history = DB::table('flag_status')
                                            ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', 'tsta.progress', 'tpp.pic', DB::raw('MAX(flag_status.flag) AS max_flag'))
                                            ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                            ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                            ->leftJoin('trx_serah_terima_aplikasi as tsta', 'tsta.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
                                            ->when(auth()->user()->level == 3, function ($query) {
                                                return $query->where('tsta.nik_penyetuju', auth()->user()->nik);
                                            }, function ($query) {
                                                return $query->where('tsta.created_by', auth()->user()->id); 
                                            })
                                            ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', 'tsta.progress', 'tpp.pic')
                                            ->having(DB::raw('MAX(flag_status.flag)'), '=', 9)
                                            ->count();

        // $trx_user_acceptance_testing = FlagStatus::select(
        //             DB::raw('COUNT(DISTINCT flag_status.id_permintaan) as total_uat'), // Total permintaan dengan flag = 6
        //             DB::raw('COUNT(DISTINCT CASE WHEN subquery.max_flag = 6 THEN flag_status.id_permintaan ELSE NULL END) as uat_on_progress') // Task yang dalam proses UAT
        //         )
        //         ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
        //         ->leftJoinSub($subQuery2, 'subquery', function($join) {
        //             $join->on('subquery.id_permintaan', '=', 'flag_status.id_permintaan');
        //         })
        //         ->where('flag_status.flag', 6)
        //         ->first();

        // $trx_quality_assurance_testing = FlagStatus::select(
        //             DB::raw('COUNT(DISTINCT flag_status.id_permintaan) as total_qat'), // Total permintaan dengan flag = 7
        //             DB::raw('COUNT(DISTINCT CASE WHEN subquery.max_flag = 7 THEN flag_status.id_permintaan ELSE NULL END) as qat_on_progress') // Task yang dalam proses QA Testing
        //         )
        //         ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
        //         ->leftJoinSub($subQuery2, 'subquery', function($join) {
        //             $join->on('subquery.id_permintaan', '=', 'flag_status.id_permintaan');
        //         })
        //         ->where('flag_status.flag', 7)
        //         ->first();

        // $trx_serah_terima_aplikasi = FlagStatus::select(
        //             DB::raw('COUNT(DISTINCT flag_status.id_permintaan) as all_task'), // Total permintaan dengan flag = 1
        //             DB::raw('SUM(CASE WHEN tsta.progress = 100 THEN 1 ELSE 0 END) as done'), // Jumlah task dengan progress 100%
        //             // DB::raw('SUM(CASE WHEN tpp.tanggal_akhir < NOW() THEN 1 ELSE 0 END) as overtime'), // Task yang melewati tanggal akhir
        //             DB::raw('COUNT(DISTINCT CASE WHEN subquery.max_flag = 8 AND tsta.progress < 100 THEN flag_status.id_permintaan ELSE NULL END) as on_progress') // Task yang on progress
        //         )
        //         ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
        //         ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
        //         ->leftJoin('trx_serah_terima_aplikasi as tsta', 'tsta.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
        //         ->leftJoinSub($subQuery2, 'subquery', function($join) {
        //             $join->on('subquery.id_permintaan', '=', 'flag_status.id_permintaan');
        //         })
        //         ->where('flag_status.flag', 8)
        //         ->first();

        // Contoh memakai Array
        // foreach 
        // $trx[1] = PermintaanPengembangan::count();

        $produk = Produk::count();
        $supplier = Supplier::count();
        $member = Member::count();

        $tanggal_awal = date('Y-m-01');
        $tanggal_akhir = date('Y-m-d');

        $data_tanggal = array();
        $data_pendapatan = array();

        while (strtotime($tanggal_awal) <= strtotime($tanggal_akhir)) {
            $data_tanggal[] = (int) substr($tanggal_awal, 8, 2);

            $total_penjualan = Penjualan::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('bayar');
            $total_pembelian = Pembelian::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('bayar');
            $total_pengeluaran = Pengeluaran::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('nominal');

            $pendapatan = $total_penjualan - $total_pembelian - $total_pengeluaran;
            $data_pendapatan[] += $pendapatan;

            $tanggal_awal = date('Y-m-d', strtotime("+1 day", strtotime($tanggal_awal)));
        }

        $tanggal_awal = date('Y-m-01');

        // Revision For Dashboard Dev Progress Project 
        $trx_perencanaan_proyek_data = PerencanaanProyek::all();

        if (auth()->user()->level == 1) {
            return view('admin.dashboard_dev_data_ver4', compact('kategori', 'totalProject', 'trx_perencanaan_proyek', 
            'trx_persetujuan_pengembangan' ,'trx_permintaan_pengembangan', 'is_approve_permintaan_pengembangan', 'is_not_approve_permintaan_pengembangan', 'trx_perencanaan_kebutuhan', 
            'trx_analisis_desain', 'trx_user_acceptance_testing','trx_quality_assurance_testing',
            'is_approve_persetujuan_pengembangan', 'is_not_approve_persetujuan_pengembangan', 'is_approve_perencanaan_proyek', 'is_not_approve_perencanaan_proyek',
            'trx_serah_terima_aplikasi','produk', 'supplier', 'member', 'tanggal_awal', 'tanggal_akhir', 
            'data_tanggal','data_pendapatan', 'trx_perencanaan_proyek_data','data_perencanaan_proyek', 
            'data_persetujuan_pengembangan' ,'data_permintaan_pengembangan', 'data_perencanaan_kebutuhan', 
            'data_analisis_desain', 'data_user_acceptance_testing','data_quality_assurance_testing',
            'trx_pengembangan_aplikasi', 'data_serah_terima_aplikasi','trx_perencanaan_kebutuhan_history', 'trx_analisis_desain_history', 'trx_user_acceptance_testing_history', 'trx_quality_assurance_testing_history', 'trx_serah_terima_aplikasi_history', 'trx_perencanaan_proyek_history', 'trx_pengembangan_aplikasi_history'));
        } else {
            return view('admin.dashboard_dev_data_ver4', compact('kategori', 'totalProject', 'trx_perencanaan_proyek', 
            'trx_persetujuan_pengembangan' ,'trx_permintaan_pengembangan', 'is_approve_permintaan_pengembangan', 'is_not_approve_permintaan_pengembangan', 'trx_perencanaan_kebutuhan', 
            'trx_analisis_desain', 'trx_user_acceptance_testing','trx_quality_assurance_testing',
            'is_approve_persetujuan_pengembangan', 'is_not_approve_persetujuan_pengembangan', 'is_approve_perencanaan_proyek', 'is_not_approve_perencanaan_proyek',
            'trx_serah_terima_aplikasi','produk', 'supplier', 'member', 'tanggal_awal', 'tanggal_akhir', 
            'data_tanggal','data_pendapatan', 'trx_perencanaan_proyek_data','data_perencanaan_proyek', 
            'data_persetujuan_pengembangan' ,'data_permintaan_pengembangan', 'data_perencanaan_kebutuhan', 
            'data_analisis_desain', 'data_user_acceptance_testing','data_quality_assurance_testing',
            'trx_pengembangan_aplikasi', 'data_serah_terima_aplikasi','trx_perencanaan_kebutuhan_history', 'trx_analisis_desain_history', 'trx_user_acceptance_testing_history', 'trx_quality_assurance_testing_history', 'trx_serah_terima_aplikasi_history', 'trx_perencanaan_proyek_history', 'trx_pengembangan_aplikasi_history'));
        }
    }

    public function getDetail($id)
    {
        // Without ID
        if($id == 1){    
            $data = DB::table('flag_status')
            ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpp.progress', 'tpp.pic', DB::raw('MAX(flag_status.flag) AS max_flag'))
            ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
            ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpp.progress', 'tpp.pic')
            ->having(DB::raw('MAX(flag_status.flag)'), '=', 1)
            ->get();
            
            return response()->json($data);
        }else if($id == 2){
            $data = DB::table('flag_status')
            ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpp2.progress', 'tpp.pic', DB::raw('MAX(flag_status.flag) AS max_flag'))
            ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
            ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
            ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpp2.progress', 'tpp.pic')
            ->having(DB::raw('MAX(flag_status.flag)'), '=', 2)
            ->get();

            return response()->json($data);
        }else if($id == 3){
            $data = DB::table('flag_status')
            ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpp3.progress', 'tpp.pic', DB::raw('MAX(flag_status.flag) AS max_flag'))
            ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
            ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
            ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
            ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpp3.progress', 'tpp.pic')
            ->having(DB::raw('MAX(flag_status.flag)'), '=', 3)
            ->get();

            return response()->json($data);
        }else if($id == 4){
            $data = DB::table('flag_status')
            ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpk.progress', 'tpp.pic', DB::raw('MAX(flag_status.flag) AS max_flag'))
            ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
            ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
            ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
            ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpk.progress', 'tpp.pic')
            ->having(DB::raw('MAX(flag_status.flag)'), '=', 4)
            ->get();

            return response()->json($data);
        }else if($id == 5){
            $data = DB::table('flag_status')
            ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tad.progress', 'tpp.pic', DB::raw('MAX(flag_status.flag) AS max_flag'))
            ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
            ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
            ->leftJoin('trx_analisis_desain as tad', 'tad.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
            ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tad.progress', 'tpp.pic')
            ->having(DB::raw('MAX(flag_status.flag)'), '=', 5)
            ->get();

            return response()->json($data);
        }else if($id == 6){
            $data = DB::table('flag_status')
            ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tqat.progress', 'tpp.pic', DB::raw('MAX(flag_status.flag) AS max_flag'))
            ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
            ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
            ->leftJoin('trx_quality_assurance_testing as tqat', 'tqat.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
            ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tqat.progress', 'tpp.pic')
            ->having(DB::raw('MAX(flag_status.flag)'), '=', 6)
            ->get();

            return response()->json($data);
        }else if($id == 7){
            $data = DB::table('flag_status')
            ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tuat.progress', 'tpp.pic', DB::raw('MAX(flag_status.flag) AS max_flag'))
            ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
            ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
            ->leftJoin('trx_user_acceptance_testing as tuat', 'tuat.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
            ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tuat.progress', 'tpp.pic')
            ->having(DB::raw('MAX(flag_status.flag)'), '=', 7)
            ->get();

            return response()->json($data);
        }else if($id == 8){
            $data = DB::table('flag_status')
            ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tsta.progress', 'tpp.pic', DB::raw('MAX(flag_status.flag) AS max_flag'))
            ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
            ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
            ->leftJoin('trx_serah_terima_aplikasi as tsta', 'tsta.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
            ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tsta.progress', 'tpp.pic')
            ->having(DB::raw('MAX(flag_status.flag)'), '=', 8)
            ->get();

            return response()->json($data);
        }

        // if($id == 1){    
        //     $data = DB::table('flag_status')
        //     ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpp.progress', 'tpp.pic')
        //     ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
        //     ->where('flag_status.flag', '=', 1)
        //     ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpp.progress', 'tpp.pic')
        //     ->get();
            
        //     return response()->json($data);
        // }else if($id == 2){
        //     $data = DB::table('flag_status')
        //     ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpp2.progress', 'tpp.pic')
        //     ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
        //     ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
        //     ->where('flag_status.flag', '=', 2)
        //     ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpp2.progress', 'tpp.pic')
        //     ->get();

        //     return response()->json($data);
        // }else if($id == 3){
        //     $data = DB::table('flag_status')
        //     ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpp3.progress', 'tpp.pic')
        //     ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
        //     ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
        //     ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
        //     ->where('flag_status.flag', '=', 3)
        //     ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpp3.progress', 'tpp.pic')
        //     ->get();

        //     return response()->json($data);
        // }else if($id == 4){
        //     $data = DB::table('flag_status')
        //     ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpk.progress', 'tpp.pic')
        //     ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
        //     ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
        //     ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
        //     ->where('flag_status.flag', '=', 4)
        //     ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpk.progress', 'tpp.pic')
        //     ->get();

        //     return response()->json($data);
        // }else if($id == 5){
        //     $data = DB::table('flag_status')
        //     ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tad.progress', 'tpp.pic')
        //     ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
        //     ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
        //     ->leftJoin('trx_analisis_desain as tad', 'tad.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
        //     ->where('flag_status.flag', '=', 5)
        //     ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tad.progress', 'tpp.pic')
        //     ->get();

        //     return response()->json($data);
        // }else if($id == 6){
        //     $data = DB::table('flag_status')
        //     ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tuat.progress', 'tpp.pic')
        //     ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
        //     ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
        //     ->leftJoin('trx_user_acceptance_testing as tuat', 'tuat.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
        //     ->where('flag_status.flag', '=', 6)
        //     ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tuat.progress', 'tpp.pic')
        //     ->get();

        //     return response()->json($data);
        // }else if($id == 7){
        //     $data = DB::table('flag_status')
        //     ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tqat.progress', 'tpp.pic')
        //     ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
        //     ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
        //     ->leftJoin('trx_quality_assurance_testing as tqat', 'tqat.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
        //     ->where('flag_status.flag', '=', 7)
        //     ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tqat.progress', 'tpp.pic')
        //     ->get();

        //     return response()->json($data);
        // }else if($id == 8){
        //     $data = DB::table('flag_status')
        //     ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tsta.progress', 'tpp.pic')
        //     ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
        //     ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
        //     ->leftJoin('trx_serah_terima_aplikasi as tsta', 'tsta.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
        //     ->where('flag_status.flag', '=', 8)
        //     ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tsta.progress', 'tpp.pic')
        //     ->get();

        //     return response()->json($data);
        // }
    }
    
    public function getDetail2($id, $id2)
    {
        // Permintaan Pengembangan
        if($id == 1){   
            if ($id2 == 1) {
                $data = DB::table('flag_status')
                ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', 'tpp.progress', 'tpp.pic', DB::raw('MAX(flag_status.flag) AS max_flag'))
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->when(auth()->user()->level == 3, function ($query) {
                    return $query->where('tpp.nik_penyetuju', auth()->user()->nik);
                })
                ->when(auth()->user()->level == 1, function ($query) {
                    return $query;
                }, function ($query) {
                    return $query->where('tpp.created_by', auth()->user()->id); 
                })
                ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', 'tpp.progress', 'tpp.pic')
                ->having(DB::raw('MAX(flag_status.flag)'), '=', 1)
                ->get();

                return response()->json($data);
            }else if ($id2 == 2) {
                $data = PermintaanPengembangan::where('is_approve', 1)
                ->when(auth()->user()->level == 1, function ($query) {
                    return $query;
                })
                ->when(auth()->user()->level == 3, function ($query) {
                    return $query->where('nik_penyetuju', auth()->user()->nik);
                }, function ($query) {
                    return $query->where('created_by', auth()->user()->id); 
                })
                ->get();

                return response()->json($data);
            }else if ($id2 == 3) {
                $data = PermintaanPengembangan::where('is_approve', NULL)                
                ->when(auth()->user()->level == 3, function ($query) {
                    return $query->where('nik_penyetuju', auth()->user()->nik);
                })
                ->when(auth()->user()->level == 1, function ($query) {
                    return $query;
                }, function ($query) {
                    return $query->where('created_by', auth()->user()->id); 
                })
                ->get();

                return response()->json($data);
            }
        // Persetujuan Pengembangan
        }else if($id == 2){   
            if ($id2 == 1) {
                $data = DB::table('flag_status')
                ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', 'tpp2.progress', 'tpp.pic', DB::raw('MAX(flag_status.flag) AS max_flag'))
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->when(auth()->user()->level == 1, function ($query) {
                    return $query;
                })
                ->when(auth()->user()->level == 3, function ($query) {
                    return $query->where('tpp.nik_penyetuju', auth()->user()->nik);
                }, function ($query) {
                    return $query->where('tpp2.created_by', auth()->user()->id); 
                })
                ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.judul', 'tpp.tujuan', 'tpp2.progress', 'tpp.pic')
                ->having(DB::raw('MAX(flag_status.flag)'), '=', 2)
                ->get();

                return response()->json($data);
            }else if ($id2 == 2) {
                $data = PersetujuanPengembangan::join('trx_permintaan_pengembangan', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan', '=', 'trx_permintaan_pengembangan.id_permintaan_pengembangan')
                ->where('trx_persetujuan_pengembangan.is_approve', 1) 
                ->when(auth()->user()->level == 1, function ($query) {
                    return $query;
                })
                ->when(auth()->user()->level == 3, function ($query) {
                    return $query->where('trx_persetujuan_pengembangan.nik_penyetuju', auth()->user()->nik);
                }, function ($query) {
                    return $query->where('trx_persetujuan_pengembangan.created_by', auth()->user()->id); 
                })
                ->select('trx_permintaan_pengembangan.*', 'trx_persetujuan_pengembangan.is_approve') 
                ->get();

                return response()->json($data);
            }else if ($id2 == 3) {
                $data = PersetujuanPengembangan::join('trx_permintaan_pengembangan', 'trx_persetujuan_pengembangan.id_permintaan_pengembangan', '=', 'trx_permintaan_pengembangan.id_permintaan_pengembangan')
                ->where('trx_persetujuan_pengembangan.is_approve', NULL)
                ->when(auth()->user()->level == 3, function ($query) {
                    return $query->where('trx_persetujuan_pengembangan.nik_penyetuju', auth()->user()->nik);
                })
                ->when(auth()->user()->level == 1, function ($query) {
                    return $query;
                }, function ($query) {
                    return $query->where('trx_persetujuan_pengembangan.created_by', auth()->user()->id); 
                })
                ->select('trx_permintaan_pengembangan.*', 'trx_persetujuan_pengembangan.is_approve')
                ->get();

                return response()->json($data);
            }
        // Perencanaan Proyek
        }else if($id == 3){   
            if ($id2 == 1) {
                $data = FlagStatus::select(
                            'flag_status.id_permintaan',
                            'tpp3.nomor_proyek',
                            'tpp.judul',
                            'tpp.tujuan',
                            'tpp3.progress',
                            'tpp.pic',
                            DB::raw('MAX(flag_status.flag) as max_flag'),
                            // Hitung total hijau
                            DB::raw("
                                                        CASE 
                                                            WHEN (
                                                                -- Kondisi Hijau
                                                                DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) <= 0.85 * (5 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                                            ) 
                                                            THEN 1 
                                                            ELSE NULL 
                                                        END as total_hijau
                                                    ")
                                                )
                                                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                                ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                                                ->when(auth()->user()->level == 3, function ($query) {
                                                    return $query->where('tpp3.nik_penyetuju', auth()->user()->nik);
                                                }, function ($query) {
                                                    return $query->where('tpp3.created_by', auth()->user()->id); 
                                                })
                                                ->groupBy(
                                                    'flag_status.id_permintaan',
                                                    'tpp3.nomor_proyek',  // Menambahkan kolom ini ke GROUP BY
                                                    'tpp.judul', // Menambahkan kolom ini ke GROUP BY
                                                    'tpp.tujuan',         // Menambahkan kolom ini ke GROUP BY
                                                    'tpp3.progress',
                                                    'tpp.pic',
                                                    'tpp3.tanggal_mulai',
                                                    'tpp3.target_selesai'
                                                )
                                                ->having(DB::raw('MAX(flag_status.flag)'), '=', 3)
                                                ->havingRaw('CASE 
                                                                WHEN (
                                                                    -- Kondisi Hijau
                                                                    DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) <= 0.85 * (5 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                                                ) 
                                                                THEN 1 
                                                                ELSE NULL 
                                                            END IS NOT NULL
                                                            ')
                                                ->get();
                
                        return response()->json($data);
            }else if ($id2 == 2) {
                $data = FlagStatus::select(
                            'flag_status.id_permintaan',
                            'tpp3.nomor_proyek',
                            'tpp.judul',
                            'tpp.tujuan',
                            'tpp3.progress',
                            'tpp.pic',
                        DB::raw('MAX(flag_status.flag) as max_flag'),
                        // Hitung total kuning
                        DB::raw("
                                CASE 
                                    WHEN (
                                        -- Kondisi Kuning
                                        DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) > 0.85 * (5 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                        AND NOW() <= DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                    ) 
                                    THEN 1 
                                    ELSE NULL 
                                END as total_kuning 
                            "),
                        DB::raw('MAX(flag_status.flag) as max_flag')
                    )
                    ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                    ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                    ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                    ->when(auth()->user()->level == 3, function ($query) {
                        return $query->where('tpp3.nik_penyetuju', auth()->user()->nik);
                    }, function ($query) {
                        return $query->where('tpp3.created_by', auth()->user()->id); 
                    })
                    ->groupBy(
                        'flag_status.id_permintaan',
                        'tpp3.nomor_proyek',
                        'tpp.judul',
                        'tpp.tujuan',
                        'tpp3.progress',
                        'tpp.pic',
                        'tpp3.tanggal_mulai',
                        'tpp3.target_selesai'
                    )
                    ->having(DB::raw('MAX(flag_status.flag)'), '=', 3)
                    ->havingRaw('CASE 
                                WHEN (
                                    -- Kondisi Kuning
                                    DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) > 0.85 * (5 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                    AND NOW() <= DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                ) 
                                THEN 1 
                                ELSE NULL 
                            END IS NOT NULL
                            ')
                    ->get();

                return response()->json($data);
            }else if ($id2 == 3) {
                $data = FlagStatus::select(
                            'flag_status.id_permintaan',
                            'tpp3.nomor_proyek',
                            'tpp.judul',
                            'tpp.tujuan',
                            'tpp3.progress',
                            'tpp.pic',
                        DB::raw('MAX(flag_status.flag) as max_flag'),
                        // Hitung total merah
                        DB::raw("
                                CASE 
                                    WHEN (
                                        -- Kondisi Merah
                                        NOW() > DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                    ) 
                                    THEN 1 
                                    ELSE NULL 
                                END as total_merah
                            "),
                        DB::raw('MAX(flag_status.flag) as max_flag')
                    )
                    ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                    ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                    ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                    ->when(auth()->user()->level == 3, function ($query) {
                        return $query->where('tpp3.nik_penyetuju', auth()->user()->nik);
                    }, function ($query) {
                        return $query->where('tpp3.created_by', auth()->user()->id); 
                    })
                    ->groupBy(
                        'flag_status.id_permintaan',
                        'tpp3.nomor_proyek',
                        'tpp.judul',
                        'tpp.tujuan',
                        'tpp3.progress',
                        'tpp.pic',
                        'tpp3.tanggal_mulai',
                        'tpp3.target_selesai'
                    )
                    ->having(DB::raw('MAX(flag_status.flag)'), '=', 3)
                    ->havingRaw('
                                CASE 
                                    WHEN (
                                        -- Kondisi Merah
                                        NOW() > DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                    ) 
                                    THEN 1 
                                    ELSE NULL 
                                END IS NOT NULL
                                ')
                    ->get();

                return response()->json($data);
            }
        // Perencanaan Kebutuhan
        }else if($id == 4){   
            if ($id2 == 1) {
                            $data  = FlagStatus::select(
                                        'flag_status.id_permintaan',
                                        'tpp3.nomor_proyek',
                                        'tpp.judul',
                                        'tpp.tujuan',
                                        'tpk.progress',
                                        'tpp.pic',
                                    DB::raw('MAX(flag_status.flag) as max_flag'),
                                    // Hitung total hijau
                                    DB::raw(" 
                                        CASE 
                                            WHEN (
                                                -- Kondisi Hijau
                                                DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) <= 0.85 * (5 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                            ) 
                                            THEN 1 
                                            ELSE NULL 
                                        END as total_hijau
                                        "),
                                    DB::raw('MAX(flag_status.flag) as max_flag')
                                )
                                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                                ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                                ->when(auth()->user()->level == 3, function ($query) {
                                    return $query->where('tpk.nik_penyetuju', auth()->user()->nik);
                                }, function ($query) {
                                    return $query->where('tpk.created_by', auth()->user()->id); 
                                })
                                ->groupBy(
                                    'flag_status.id_permintaan',
                                    'tpp3.nomor_proyek',
                                    'tpp.judul',
                                    'tpp.tujuan',
                                    'tpk.progress',
                                    'tpp.pic',
                                    'tpp3.tanggal_mulai',
                                    'tpp3.target_selesai'
                                )
                                ->having(DB::raw('MAX(flag_status.flag)'), '=', 4)
                                ->havingRaw('CASE 
                                                WHEN (
                                                    -- Kondisi Hijau
                                                    DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) <= 0.85 * (5 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                                ) 
                                                THEN 1 
                                                ELSE NULL 
                                            END IS NOT NULL')
                                ->get();

                return response()->json($data);
            }else if ($id2 == 2) {
                $data   =   FlagStatus::select(
                                'flag_status.id_permintaan',
                                'tpp3.nomor_proyek',
                                'tpp.judul',
                                'tpp.tujuan',
                                'tpk.progress',
                                'tpp.pic',
                            DB::raw('MAX(flag_status.flag) as max_flag'),
                            // Hitung total kuning
                            DB::raw(" 
                                    CASE 
                                        WHEN (
                                            -- Kondisi Kuning
                                            DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) > 0.85 * (5 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                            AND NOW() <= DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                        ) 
                                        THEN 1 
                                        ELSE NULL 
                                    END as total_kuning 
                                    "),
                            DB::raw('MAX(flag_status.flag) as max_flag')
                        )
                        ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                        ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                        ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                        ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                        ->when(auth()->user()->level == 3, function ($query) {
                            return $query->where('tpk.nik_penyetuju', auth()->user()->nik);
                        }, function ($query) {
                            return $query->where('tpk.created_by', auth()->user()->id); 
                        })
                        ->groupBy(
                            'flag_status.id_permintaan',
                            'tpp3.nomor_proyek',
                            'tpp.judul',
                            'tpp.tujuan',
                            'tpk.progress',
                            'tpp.pic',
                            'tpp3.tanggal_mulai',
                            'tpp3.target_selesai'
                        )
                        ->having(DB::raw('MAX(flag_status.flag)'), '=', 4)
                        ->havingRaw('CASE 
                                        WHEN (
                                            -- Kondisi Kuning
                                            DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) > 0.85 * (5 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                            AND NOW() <= DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                        ) 
                                        THEN 1 
                                        ELSE NULL 
                                     END IS NOT NULL')
                        ->get();

            return response()->json($data);
            }else if ($id2 == 3) {
                    $data  = FlagStatus::select(
                                        'flag_status.id_permintaan',
                                        'tpp3.nomor_proyek',
                                        'tpp.judul',
                                        'tpp.tujuan',
                                        'tpk.progress',
                                        'tpp.pic',
                                    DB::raw('MAX(flag_status.flag) as max_flag'),
                                    // Hitung total merah
                                    DB::raw(" 
                                            CASE 
                                                WHEN (
                                                    -- Kondisi Merah
                                                    NOW() > DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                                ) 
                                                THEN 1 
                                                ELSE NULL 
                                            END as total_merah
                                            "),
                                    DB::raw('MAX(flag_status.flag) as max_flag')
                                )
                                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                                ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                                ->when(auth()->user()->level == 3, function ($query) {
                                    return $query->where('tpk.nik_penyetuju', auth()->user()->nik);
                                }, function ($query) {
                                    return $query->where('tpk.created_by', auth()->user()->id); 
                                })
                                ->groupBy(
                                    'flag_status.id_permintaan',
                                    'tpp3.nomor_proyek',
                                    'tpp.judul',
                                    'tpp.tujuan',
                                    'tpk.progress',
                                    'tpp.pic',
                                    'tpp3.tanggal_mulai',
                                    'tpp3.target_selesai'
                                )
                                ->having(DB::raw('MAX(flag_status.flag)'), '=', 4)
                                ->havingRaw('CASE 
                                                WHEN (
                                                    -- Kondisi Merah
                                                    NOW() > DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                                ) 
                                                THEN 1 
                                                ELSE NULL 
                                            END IS NOT NULL')
                                ->get();
                                
                return response()->json($data);
            }
        // Analisis Desain
        }else if($id == 5){   
            if ($id2 == 1) {
                $data = FlagStatus::select(
                            'flag_status.id_permintaan',
                            'tpp3.nomor_proyek',
                            'tpp.judul',
                            'tpp.tujuan',
                            'tad.progress',
                            'tpp.pic',
                            DB::raw('MAX(flag_status.flag) as max_flag'),
                            // Hitung total hijau
                            DB::raw("
                                        CASE 
                                            WHEN (
                                                -- Kondisi Hijau
                                                DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) <= 0.85 * (5 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                            ) 
                                            THEN 1 
                                            ELSE NULL 
                                        END as total_hijau"),
                            DB::raw('MAX(flag_status.flag) as max_flag')
                        )
                        ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                        ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                        ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                        ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                        ->leftJoin('trx_analisis_desain as tad', 'tad.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                        ->when(auth()->user()->level == 3, function ($query) {
                            return $query->where('tpk.nik_penyetuju', auth()->user()->nik);
                        }, function ($query) {
                            return $query->where('tpk.created_by', auth()->user()->id); 
                        })
                        ->groupBy(
                            'flag_status.id_permintaan',
                            'tpp3.nomor_proyek',
                            'tpp.judul',
                            'tpp.tujuan',
                            'tad.progress',
                            'tpp.pic',
                            'tpp3.tanggal_mulai',
                            'tpp3.target_selesai'
                        )
                        ->having(DB::raw('MAX(flag_status.flag)'), '=', 5)
                        ->havingRaw('CASE 
                                        WHEN (
                                            DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) <= 0.85 * (25 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                        ) 
                                        THEN 1 
                                        ELSE NULL 
                                    END IS NOT NULL')
                        ->get();
                
                return response()->json($data);
            }else if ($id2 == 2) {
                $data = FlagStatus::select(
                    'flag_status.id_permintaan',
                    'tpp3.nomor_proyek',
                    'tpp.judul',
                    'tpp.tujuan',
                    'tad.progress',
                    'tpp.pic',
                    DB::raw('MAX(flag_status.flag) as max_flag'),
                    // Hitung total kuning
                    DB::raw("
                            CASE 
                                WHEN (
                                    DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) > 0.85 * (5 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                    AND NOW() <= DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                ) 
                                THEN 1 
                                ELSE NULL 
                            END as total_kuning
                            "),
                    DB::raw('MAX(flag_status.flag) as max_flag')
                )
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                ->leftJoin('trx_analisis_desain as tad', 'tad.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->when(auth()->user()->level == 3, function ($query) {
                    return $query->where('tad.nik_penyetuju', auth()->user()->nik);
                }, function ($query) {
                    return $query->where('tad.created_by', auth()->user()->id); 
                })
                ->groupBy(
                    'flag_status.id_permintaan',
                    'tpp3.nomor_proyek',
                    'tpp.judul',
                    'tpp.tujuan',
                    'tad.progress',
                    'tpp.pic',
                    'tpp3.tanggal_mulai',
                    'tpp3.target_selesai'
                )
                ->having(DB::raw('MAX(flag_status.flag)'), '=', 5)
                ->havingRaw('CASE 
                            WHEN (
                                DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) > 0.85 * (5 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                AND NOW() <= DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                            ) 
                            THEN 1 
                            ELSE NULL 
                        END IS NOT NULL')
                ->get();
                
                return response()->json($data);
            }else if ($id2 == 3) {
                $data = FlagStatus::select(
                    'flag_status.id_permintaan',
                    'tpp3.nomor_proyek',
                    'tpp.judul',
                    'tpp.tujuan',
                    'tad.progress',
                    'tpp.pic',
                    DB::raw('MAX(flag_status.flag) as max_flag'),
                    // Hitung total merah
                    DB::raw("
                            CASE 
                                WHEN (
                                    NOW() > DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                ) 
                                THEN 1 
                                ELSE NULL 
                            END as total_merah
                            "),
                    DB::raw('MAX(flag_status.flag) as max_flag')
                )
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                ->leftJoin('trx_analisis_desain as tad', 'tad.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->when(auth()->user()->level == 3, function ($query) {
                    return $query->where('tad.nik_penyetuju', auth()->user()->nik);
                }, function ($query) {
                    return $query->where('tad.created_by', auth()->user()->id); 
                })
                ->groupBy(
                    'flag_status.id_permintaan',
                    'tpp3.nomor_proyek',
                    'tpp.judul',
                    'tpp.tujuan',
                    'tad.progress',
                    'tpp.pic',
                    'tpp3.tanggal_mulai',
                    'tpp3.target_selesai'
                )
                ->having(DB::raw('MAX(flag_status.flag)'), '=', 5)
                ->havingRaw('CASE 
                                WHEN (
                                    NOW() > DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                ) 
                                THEN 1 
                                ELSE NULL 
                            END IS NOT NULL')
                ->get();
                
                return response()->json($data);
            }
        // Pengembangan Aplikasi
        }else if($id == 6){   
            if ($id2 == 1) {
            $data      =       FlagStatus::select(
                                                        'flag_status.id_permintaan',
                                                        'tpp3.nomor_proyek',
                                                        'tpp.judul',
                                                        'tpp.tujuan',
                                                        'tpa.progress',
                                                        'tpp.pic',
                                                    DB::raw('MAX(flag_status.flag) as max_flag'),
                                                    // Hitung total hijau
                                                    DB::raw("
                                                    COUNT(DISTINCT 
                                                        CASE 
                                                            WHEN (
                                                                -- Kondisi Hijau
                                                                DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) <= 0.85 * (50 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                                            ) 
                                                            THEN 1 
                                                            ELSE NULL 
                                                        END
                                                    ) as total_hijau"),
                                                    DB::raw('MAX(flag_status.flag) as max_flag')
                                                )
                                                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                                ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                                                ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                                                ->leftJoin('trx_analisis_desain as tad', 'tad.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                                ->leftJoin('trx_pengembangan_aplikasi as tpa', 'tpa.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                                ->when(auth()->user()->level == 3, function ($query) {
                                                    return $query->where('tpa.nik_penyetuju', auth()->user()->nik);
                                                }, function ($query) {
                                                    return $query->where('tpa.created_by', auth()->user()->id); 
                                                })
                                                ->groupBy(
                                                    'flag_status.id_permintaan',
                                                    'tpp3.nomor_proyek',
                                                    'tpp.judul',
                                                    'tpp.tujuan',
                                                    'tpa.progress',
                                                    'tpp.pic',
                                                    'tpp3.tanggal_mulai',
                                                    'tpp3.target_selesai'
                                                )
                                                ->having(DB::raw('MAX(flag_status.flag)'), '=', 6)
                                                ->havingRaw('CASE 
                                                                WHEN (
                                                                    DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) <= 0.85 * (50 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                                                ) 
                                                                THEN 1 
                                                                ELSE NULL 
                                                            END IS NOT NULL')
                                                ->get();

                    return response()->json($data);
            }else if ($id2 == 2) {
            $data   =      FlagStatus::select(
                                                    'flag_status.id_permintaan',
                                                    'tpp3.nomor_proyek',
                                                    'tpp.judul',
                                                    'tpp.tujuan',
                                                    'tpa.progress',
                                                    'tpp.pic',
                                                DB::raw('MAX(flag_status.flag) as max_flag'),
                                                // Hitung total kuning
                                                DB::raw("
                                                    COUNT(DISTINCT 
                                                        CASE 
                                                            WHEN (
                                                                -- Kondisi Kuning
                                                                DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) > 0.85 * (50 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                                                AND NOW() <= DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                                            ) 
                                                            THEN 1 
                                                            ELSE NULL 
                                                        END
                                                    ) as total_kuning"),
                                                DB::raw('MAX(flag_status.flag) as max_flag')
                                            )
                                            ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                            ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                            ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                                            ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                                            ->leftJoin('trx_analisis_desain as tad', 'tad.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                            ->leftJoin('trx_pengembangan_aplikasi as tpa', 'tpa.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                            ->when(auth()->user()->level == 3, function ($query) {
                                                return $query->where('tpa.nik_penyetuju', auth()->user()->nik);
                                            }, function ($query) {
                                                return $query->where('tpa.created_by', auth()->user()->id); 
                                            })
                                            ->groupBy(
                                                'flag_status.id_permintaan',
                                                'tpp3.nomor_proyek',
                                                'tpp.judul',
                                                'tpp.tujuan',
                                                'tpa.progress',
                                                'tpp.pic',
                                                'tpp3.tanggal_mulai',
                                                'tpp3.target_selesai'
                                            )
                                            ->having(DB::raw('MAX(flag_status.flag)'), '=', 6)
                                            ->havingRaw('
                                                CASE 
                                                    WHEN (
                                                        -- Kondisi Kuning
                                                        DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) > 0.85 * (50 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                                        AND NOW() <= DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                                    ) 
                                                    THEN 1 
                                                    ELSE NULL 
                                                END IS NOT NULL
                                            ')
                                            ->get();

                                return response()->json($data);
            }else if ($id2 == 3) {
                                $data   =  FlagStatus::select(
                                                    'flag_status.id_permintaan',
                                                    'tpp3.nomor_proyek',
                                                    'tpp.judul',
                                                    'tpp.tujuan',
                                                    'tpa.progress',
                                                    'tpp.pic',
                                                DB::raw('MAX(flag_status.flag) as max_flag'),
                                                // Hitung total hijau
                                                // Hitung total merah
                                                DB::raw("
                                                    COUNT(DISTINCT 
                                                        CASE 
                                                            WHEN (
                                                                -- Kondisi Merah
                                                                NOW() > DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                                            ) 
                                                            THEN 1 
                                                            ELSE NULL 
                                                        END
                                                    ) as total_merah"),
                                                DB::raw('MAX(flag_status.flag) as max_flag')
                                            )
                                            ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                                            ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                            ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                                            ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                                            ->leftJoin('trx_analisis_desain as tad', 'tad.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                            ->leftJoin('trx_pengembangan_aplikasi as tpa', 'tpa.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                                            ->when(auth()->user()->level == 3, function ($query) {
                                                return $query->where('tpa.nik_penyetuju', auth()->user()->nik);
                                            }, function ($query) {
                                                return $query->where('tpa.created_by', auth()->user()->id); 
                                            })
                                            ->groupBy(
                                                'flag_status.id_permintaan',
                                                'tpp3.nomor_proyek',
                                                'tpp.judul',
                                                'tpp.tujuan',
                                                'tpa.progress',
                                                'tpp.pic',
                                                'tpp3.tanggal_mulai',
                                                'tpp3.target_selesai'
                                            )
                                            ->having(DB::raw('MAX(flag_status.flag)'), '=', 6)
                                            ->havingRaw('
                                                    CASE 
                                                        WHEN (
                                                            -- Kondisi Merah
                                                            NOW() > DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                                        ) 
                                                        THEN 1 
                                                        ELSE NULL 
                                                    END IS NOT NULL
                                            ')
                                            ->get();

                                return response()->json($data);
            }
        // User Acceptance Testing
        }else if($id == 8){   
            if ($id2 == 1) {
                $data    =   FlagStatus::select(
                    'flag_status.id_permintaan',
                    'tpp3.nomor_proyek',
                    'tpp.judul',
                    'tpp.tujuan',
                    'tad.progress',
                    'tpp.pic',
                    DB::raw('MAX(flag_status.flag) as max_flag'),
                    // Hitung total hijau
                    DB::raw("
                    COUNT(DISTINCT
                        CASE
                            WHEN (
                                -- Kondisi Hijau
                                DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) <= 0.85 * (50 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                            )
                            THEN 1
                            ELSE NULL
                        END
                    ) as total_hijau"),
                    DB::raw('MAX(flag_status.flag) as max_flag')
                )
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                ->leftJoin('trx_analisis_desain as tad', 'tad.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_user_acceptance_testing as tuat', 'tuat.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->when(auth()->user()->level == 3, function ($query) {
                    return $query->where('tuat.nik_penyetuju', auth()->user()->nik);
                }, function ($query) {
                    return $query->where('tuat.created_by', auth()->user()->id); 
                })
                ->groupBy(
                    'flag_status.id_permintaan',
                    'tpp3.nomor_proyek',
                    'tpp.judul',
                    'tpp.tujuan',
                    'tad.progress',
                    'tpp.pic',
                    'tpp3.tanggal_mulai',
                    'tpp3.target_selesai'
                )
                ->having(DB::raw('MAX(flag_status.flag)'), '=', 8)
                ->havingRaw('
                    CASE
                        WHEN (
                            -- Kondisi Hijau
                            DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) <= 0.85 * (50 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                        )
                        THEN 1
                        ELSE NULL
                    END IS NOT NULL
                ')
                ->get();
            }else if ($id2 == 2) {
                $data    =   FlagStatus::select(
                    'flag_status.id_permintaan',
                    'tpp3.nomor_proyek',
                    'tpp.judul',
                    'tpp.tujuan',
                    'tad.progress',
                    'tpp.pic',
                    DB::raw('MAX(flag_status.flag) as max_flag'),
                    // Hitung total kuning
                    DB::raw("
                        COUNT(DISTINCT
                            CASE
                                WHEN (
                                    -- Kondisi Kuning
                                    DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) > 0.85 * (50 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                    AND NOW() <= DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                )
                                THEN 1
                                ELSE NULL
                            END
                        ) as total_kuning"),
                    DB::raw('MAX(flag_status.flag) as max_flag')
                )
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                ->leftJoin('trx_analisis_desain as tad', 'tad.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_user_acceptance_testing as tuat', 'tuat.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->when(auth()->user()->level == 3, function ($query) {
                    return $query->where('tuat.nik_penyetuju', auth()->user()->nik);
                }, function ($query) {
                    return $query->where('tuat.created_by', auth()->user()->id); 
                })
                ->groupBy(
                    'flag_status.id_permintaan',
                    'tpp3.nomor_proyek',
                    'tpp.judul',
                    'tpp.tujuan',
                    'tad.progress',
                    'tpp.pic',
                    'tpp3.tanggal_mulai',
                    'tpp3.target_selesai'
                )
                ->having(DB::raw('MAX(flag_status.flag)'), '=', 8)
                ->havingRaw('
                    CASE
                        WHEN (
                            -- Kondisi Kuning
                            DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) > 0.85 * (50 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                            AND NOW() <= DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                        )
                        THEN 1
                        ELSE NULL
                    END IS NOT NULL
                ')
                ->get();
            }else if ($id2 == 3) {
                $data    =   FlagStatus::select(
                    'flag_status.id_permintaan',
                    'tpp3.nomor_proyek',
                    'tpp.judul',
                    'tpp.tujuan',
                    'tad.progress',
                    'tpp.pic',
                    DB::raw('MAX(flag_status.flag) as max_flag'),
                    // Hitung total merah
                    DB::raw("
                        COUNT(DISTINCT
                            CASE
                                WHEN (
                                    -- Kondisi Merah
                                    NOW() > DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                )
                                THEN 1
                                ELSE NULL
                            END
                        ) as total_merah"),
                    DB::raw('MAX(flag_status.flag) as max_flag')
                )
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                ->leftJoin('trx_analisis_desain as tad', 'tad.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_user_acceptance_testing as tuat', 'tuat.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->when(auth()->user()->level == 3, function ($query) {
                    return $query->where('tuat.nik_penyetuju', auth()->user()->nik);
                }, function ($query) {
                    return $query->where('tuat.created_by', auth()->user()->id); 
                })
                ->groupBy(
                    'flag_status.id_permintaan',
                    'tpp3.nomor_proyek',
                    'tpp.judul',
                    'tpp.tujuan',
                    'tad.progress',
                    'tpp.pic',
                    'tpp3.tanggal_mulai',
                    'tpp3.target_selesai'
                )
                ->having(DB::raw('MAX(flag_status.flag)'), '=', 8)
                ->havingRaw('
                    CASE
                        WHEN (
                            -- Kondisi Merah
                            NOW() > DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                        )
                        THEN 1
                        ELSE NULL
                    END IS NOT NULL
                ')
                ->get();
            }
        // Quality Assurance Testing
        }else if($id == 7){   
            if ($id2 == 1) {
                $data = FlagStatus::select(
                    'flag_status.id_permintaan',
                    'tpp3.nomor_proyek',
                    'tpp.judul',
                    'tpp.tujuan',
                    'tqat.progress',
                    'tpp.pic',
                    DB::raw('MAX(flag_status.flag) as max_flag'),
                    // Hitung total hijau
                    DB::raw("
                    COUNT(DISTINCT
                        CASE
                            WHEN (
                                -- Kondisi Hijau
                                DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) <= 0.85 * (5 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                            )
                            THEN 1
                            ELSE NULL
                        END
                    ) as total_hijau"),
                    DB::raw('MAX(flag_status.flag) as max_flag')
                )
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                ->leftJoin('trx_analisis_desain as tad', 'tad.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_user_acceptance_testing as tuat', 'tuat.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_quality_assurance_testing as tqat', 'tqat.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->when(auth()->user()->level == 3, function ($query) {
                    return $query->where('tqat.nik_penyetuju', auth()->user()->nik);
                })
                ->when(auth()->user()->level == 1, function ($query) {
                    return $query;
                }, function ($query) {
                    return $query->where('tqat.created_by', auth()->user()->id); 
                })
                ->groupBy(
                    'flag_status.id_permintaan',
                    'tpp3.nomor_proyek',
                    'tpp.judul',
                    'tpp.tujuan',
                    'tqat.progress',
                    'tpp.pic',
                    'tpp3.tanggal_mulai',
                    'tpp3.target_selesai'
                )
                ->having(DB::raw('MAX(flag_status.flag)'), '=', 7)     
                ->havingRaw('
                    CASE
                        WHEN (
                            -- Kondisi Hijau
                            DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) <= 0.85 * (5 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                        )
                        THEN 1
                        ELSE NULL
                    END IS NOT NULL
                ')
                ->get();

                return response()->json($data);
            }else if ($id2 == 2) {
                $data = FlagStatus::select(
                    'flag_status.id_permintaan',
                    'tpp3.nomor_proyek',
                    'tpp.judul',
                    'tpp.tujuan',
                    'tqat.progress',
                    'tpp.pic',
                    DB::raw('MAX(flag_status.flag) as max_flag'),
                    // Hitung total kuning
                    DB::raw("
                        COUNT(DISTINCT
                            CASE
                                WHEN (
                                    -- Kondisi Kuning
                                    DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) > 0.85 * (5 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                    AND NOW() <= DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                )
                                THEN 1
                                ELSE NULL
                            END
                        ) as total_kuning"),
                    DB::raw('MAX(flag_status.flag) as max_flag')
                )
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                ->leftJoin('trx_analisis_desain as tad', 'tad.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_user_acceptance_testing as tuat', 'tuat.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_quality_assurance_testing as tqat', 'tqat.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->when(auth()->user()->level == 3, function ($query) {
                    return $query->where('tqat.nik_penyetuju', auth()->user()->nik);
                })
                ->when(auth()->user()->level == 1, function ($query) {
                    return $query;
                }, function ($query) {
                    return $query->where('tqat.created_by', auth()->user()->id); 
                })
                ->groupBy(
                    'flag_status.id_permintaan',
                    'tpp3.nomor_proyek',
                    'tpp.judul',
                    'tpp.tujuan',
                    'tqat.progress',
                    'tpp.pic',
                    'tpp3.tanggal_mulai',
                    'tpp3.target_selesai'
                )
                ->having(DB::raw('MAX(flag_status.flag)'), '=', 7)     
                ->havingRaw('
                        CASE
                            WHEN (
                                -- Kondisi Kuning
                                DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) > 0.85 * (5 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                AND NOW() <= DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                            )
                            THEN 1
                            ELSE NULL
                        END IS NOT NULL
                ')
                ->get();

                return response()->json($data);
            }else if ($id2 == 3) {
                $data = FlagStatus::select(
                    'flag_status.id_permintaan',
                    'tpp3.nomor_proyek',
                    'tpp.judul',
                    'tpp.tujuan',
                    'tqat.progress',
                    'tpp.pic',
                    DB::raw('MAX(flag_status.flag) as max_flag'),
                    // Hitung total merah
                    DB::raw("
                        COUNT(DISTINCT
                            CASE
                                WHEN (
                                    -- Kondisi Merah
                                    NOW() > DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                )
                                THEN 1
                                ELSE NULL
                            END
                        ) as total_merah"),
                    DB::raw('MAX(flag_status.flag) as max_flag')
                )
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                ->leftJoin('trx_analisis_desain as tad', 'tad.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_user_acceptance_testing as tuat', 'tuat.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_quality_assurance_testing as tqat', 'tqat.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->when(auth()->user()->level == 3, function ($query) {
                    return $query->where('tqat.nik_penyetuju', auth()->user()->nik);
                })
                ->when(auth()->user()->level == 1, function ($query) {
                    return $query;
                }, function ($query) {
                    return $query->where('tqat.created_by', auth()->user()->id); 
                })
                ->groupBy(
                    'flag_status.id_permintaan',
                    'tpp3.nomor_proyek',
                    'tpp.judul',
                    'tpp.tujuan',
                    'tqat.progress',
                    'tpp.pic',
                    'tpp3.tanggal_mulai',
                    'tpp3.target_selesai'
                )
                ->having(DB::raw('MAX(flag_status.flag)'), '=', 7)     
                ->havingRaw('
                        CASE 
                            WHEN (
                                -- Kondisi Merah
                                NOW() > DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                            ) 
                            THEN 1 
                            ELSE NULL 
                        END IS NOT NULL
                ')
                ->get();

                return response()->json($data);
            }
        // Berita Acara Serah Terima
        }else if($id == 9){   
            if ($id2 == 1) {
                $data = FlagStatus::select(
                    'flag_status.id_permintaan',
                    'tpp3.nomor_proyek',
                    'tpp.judul',
                    'tpp.tujuan',
                    'tsta.progress',
                    'tpp.pic',
                    DB::raw('MAX(flag_status.flag) as max_flag'),
                    // Hitung total hijau
                    DB::raw("
                    COUNT(DISTINCT
                        CASE
                            WHEN (
                                -- Kondisi Hijau
                                DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) <= 0.85 * (5 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                            )
                            THEN 1
                            ELSE NULL
                        END
                    ) as total_hijau"),
                )
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                ->leftJoin('trx_analisis_desain as tad', 'tad.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_user_acceptance_testing as tuat', 'tuat.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_quality_assurance_testing as tqat', 'tqat.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_serah_terima_aplikasi as tsta', 'tsta.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->when(auth()->user()->level == 3, function ($query) {
                    return $query->where('tsta.nik_penyetuju', auth()->user()->nik);
                }, function ($query) {
                    return $query->where('tsta.created_by', auth()->user()->id); 
                })
                ->groupBy(
                    'flag_status.id_permintaan',
                    'tpp3.nomor_proyek',
                    'tpp.judul',
                    'tpp.tujuan',
                    'tsta.progress',
                    'tpp.pic',
                    'tpp3.tanggal_mulai',
                    'tpp3.target_selesai'
                )
                ->having(DB::raw('MAX(flag_status.flag)'), '=', 9)
                ->havingRaw('
                    CASE
                        WHEN (
                            -- Kondisi Hijau
                            DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) <= 0.85 * (5 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                        )
                        THEN 1
                        ELSE NULL
                    END IS NOT NULL
                ')
                ->get();

                return response()->json($data);

            }else if ($id2 == 2) {
                $data = FlagStatus::select(
                    'flag_status.id_permintaan',
                    'tpp3.nomor_proyek',
                    'tpp.judul',
                    'tpp.tujuan',
                    'tsta.progress',
                    'tpp.pic',
                    DB::raw('MAX(flag_status.flag) as max_flag'),
                    // Hitung total kuning
                    DB::raw("
                        COUNT(DISTINCT
                            CASE
                                WHEN (
                                    -- Kondisi Kuning
                                    DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) > 0.85 * (5 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                                    AND NOW() <= DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                )
                                THEN 1
                                ELSE NULL
                            END
                        ) as total_kuning"),
                    DB::raw('MAX(flag_status.flag) as max_flag')
                )
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                ->leftJoin('trx_analisis_desain as tad', 'tad.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_user_acceptance_testing as tuat', 'tuat.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_quality_assurance_testing as tqat', 'tqat.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_serah_terima_aplikasi as tsta', 'tsta.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->when(auth()->user()->level == 3, function ($query) {
                    return $query->where('tsta.nik_penyetuju', auth()->user()->nik);
                }, function ($query) {
                    return $query->where('tsta.created_by', auth()->user()->id); 
                })
                ->groupBy(
                    'flag_status.id_permintaan',
                    'tpp3.nomor_proyek',
                    'tpp.judul',
                    'tpp.tujuan',
                    'tsta.progress',
                    'tpp.pic',
                    'tpp3.tanggal_mulai',
                    'tpp3.target_selesai'
                )
                ->having(DB::raw('MAX(flag_status.flag)'), '=', 9)
                ->havingRaw('
                    CASE
                        WHEN (
                            -- Kondisi Kuning
                            DATEDIFF(NOW(), DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 5 / 100) DAY)) > 0.85 * (5 / 100.0) * DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai)
                            AND NOW() <= DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                        )
                        THEN 1
                        ELSE NULL
                    END IS NOT NULL
                ')
                ->get();

                return response()->json($data);
            }else if ($id2 == 3) {
                $data = FlagStatus::select(
                    'flag_status.id_permintaan',
                    'tpp3.nomor_proyek',
                    'tpp.judul',
                    'tpp.tujuan',
                    'tsta.progress',
                    'tpp.pic',
                    DB::raw('MAX(flag_status.flag) as max_flag'),
                    // Hitung total merah
                    DB::raw("
                        COUNT(DISTINCT
                            CASE
                                WHEN (
                                    -- Kondisi Merah
                                    NOW() > DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                )
                                THEN 1
                                ELSE NULL
                            END
                        ) as total_merah"),
                    DB::raw('MAX(flag_status.flag) as max_flag')
                )
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                ->leftJoin('trx_analisis_desain as tad', 'tad.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_user_acceptance_testing as tuat', 'tuat.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_quality_assurance_testing as tqat', 'tqat.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_serah_terima_aplikasi as tsta', 'tsta.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->when(auth()->user()->level == 3, function ($query) {
                    return $query->where('tsta.nik_penyetuju', auth()->user()->nik);
                }, function ($query) {
                    return $query->where('tsta.created_by', auth()->user()->id); 
                })
                ->groupBy(
                    'flag_status.id_permintaan',
                    'tpp3.nomor_proyek',
                    'tpp.judul',
                    'tpp.tujuan',
                    'tsta.progress',
                    'tpp.pic',
                    'tpp3.tanggal_mulai',
                    'tpp3.target_selesai'
                )
                ->having(DB::raw('MAX(flag_status.flag)'), '=', 9)
                ->havingRaw('
                            CASE
                                WHEN (
                                    -- Kondisi Merah
                                    NOW() > DATE_ADD(tpp3.tanggal_mulai, INTERVAL (DATEDIFF(tpp3.target_selesai, tpp3.tanggal_mulai) * 85 / 100) DAY)
                                )
                                THEN 1
                                ELSE NULL
                            END IS NOT NULL
                            ')
                ->get();

                return response()->json($data);
            }
        }
    }

    public function detailtaskdashboard($id){
        if($id == 1){
            return redirect()->route('permintaan_pengembangan.index');
        }else if($id == 2){
            return redirect()->route('persetujuan_pengembangan.index');
        }else if($id == 3){
            return redirect()->route('perencanaan_proyek.index');
        }else if($id == 4){
            return redirect()->route('perencanaan_kebutuhan.index');
        }else if($id == 5){
            return redirect()->route('analisis_desain.index');
        }else if($id == 6){
            return redirect()->route('pengembangan_aplikasi.index');
        }else if($id == 7){
            return redirect()->route('quality_assurance_testing.index');
        }else if($id == 8){
            return redirect()->route('user_acceptance_testing.index');
        }else if($id == 9){
            return redirect()->route('serah_terima_aplikasi.index');
        }
    }

    public function getNamaPemohonPenyetuju()
    {
        $pemohonpenyetuju = PemohonPeverifikasiPenyetuju::orderBy('full_name', 'asc')->get();

        return response()->json([
            'pemohonpenyetuju' => $pemohonpenyetuju
        ]);
    }

    public function getNamaPemohon()
    {
        // $pemohon = PemohonPeverifikasiPenyetuju::whereNotIn('position_level_name', ['BOD-1'])
        $pemohon = PemohonPeverifikasiPenyetuju::whereNotIn('employee_status', ['Kontrak-Project Based', 'Alihdaya/ Outsourcing', 'Kontrak-MPS'])
                   ->get();

        return response()->json([
            'pemohon' => $pemohon
        ]);
    }

    public function getNamaPemverifikasi()
    {
        $pemverifikasi = PemohonPeverifikasiPenyetuju::whereIn('position_level_name', ['BOD-1', 'BOD-2', 'BOD-3'])
                       ->whereIn('working_unit_id', [20])
                       ->get();    

        return response()->json([
            'pemverifikasi' => $pemverifikasi
        ]);
    }

    public function getNamaPenyetuju()
    {
        $penyetuju = PemohonPeverifikasiPenyetuju::whereIn('position_level_name', ['BOD-1', 'BOD-2'])->get();

        return response()->json([
            'penyetuju' => $penyetuju
        ]);
    }

    public function getIdentityByNik($id)
    {
        $pemohonpenyetuju = PemohonPeverifikasiPenyetuju::find($id); 
        
        return response()->json([
            'pemohonpenyetuju' => $pemohonpenyetuju
        ]);
    }

    public function getIdentityByNikPemohon($id)
    {
        $pemohon = PemohonPeverifikasiPenyetuju::select('penyetuju')->find($id); 
        
        return response()->json([
            'pemohon' => $pemohon
        ]);
    }

    public function getIdentityByNikPeverifikasi($id)
    {
        $peverifikasi = PemohonPeverifikasiPenyetuju::select('penyetuju')->find($id); 
        
        return response()->json([
            'peverifikasi' => $peverifikasi
        ]);
    }

    public function getIdentityByNikPenyetuju($id)
    {
        $penyetuju = PemohonPeverifikasiPenyetuju::select('penyetuju')->find($id);
        
        return response()->json([
            'penyetuju' => $penyetuju
        ]);
    }
}

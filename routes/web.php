<?php

use App\Http\Controllers\{
    DashboardController,
    KategoriController,
    LaporanController,
    ProdukController,
    MemberController,
    PengeluaranController,
    PembelianController,
    PembelianDetailController,
    PenjualanController,
    PenjualanDetailController,
    PermintaanPengembanganController,
    SettingController,
    SupplierController,
    UserController,
    PerencanaanKebutuhanController,
    PengembanganAplikasiController,
    AnalisisDesainController,
    PersetujuanPengembanganController,
    UserAcceptanceTestingController,
    UserAcceptanceTestingDevController,
    QualityAssuranceTestingController,
    DetailQualityAssuranceTestingController,
    DetailUserAcceptanceTestingController,
    DetailTTDQualityAssuranceTestingController,
    SerahTerimaAplikasiController
    // PermintaanPersetujuanPengembanganController
};
use App\Http\Controllers\PerencanaanProyekController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/foo', function () {
   Artisan::call('storage:link');
});

Route::group(['middleware' => 'auth'], function () {
    // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::group(['middleware' => 'level:2,3'], function () {
        Route::get('/dashboard', [PermintaanPengembanganController::class, 'index3'])->name('dashboard');
    });

    Route::get('/dashboard', [PermintaanPengembanganController::class, 'index2'])->name('dashboard');

    Route::get('/dashboard/get-nama-pemohon-penyetuju', [DashboardController::class, 'getNamaPemohonPenyetuju'])->name('getNamaPemohonPenyetuju');
    
    Route::get('/dashboard/get-nama-pemohon', [DashboardController::class, 'getNamaPemohon'])->name('getNamaPemohon');
    Route::get('/dashboard/get-nama-pemverifikasi', [DashboardController::class, 'getNamaPemverifikasi'])->name('getNamaPemverifikasi');
    Route::get('/dashboard/get-nama-penyetuju', [DashboardController::class, 'getNamaPenyetuju'])->name('getNamaPenyetuju');
    
    Route::get('/dashboard/get-identity-by-nik/{id}', [DashboardController::class, 'getIdentityByNik'])->name('getIdentityByNik');

    Route::get('/dashboard/get-identity-by-nik-pemohon/{id}', [DashboardController::class, 'getIdentityByNikPemohon'])->name('getIdentityByNikPemohon');
    Route::get('/dashboard/get-identity-by-nik-penyetuju/{id}', [DashboardController::class, 'getIdentityByNikPenyetuju'])->name('getIdentityByPenyetuju');
    
    // Route::get('/dashboard/dashboard_dev', [DashboardController::class, 'index2'])->name('dashboard_dev');
    Route::get('/dashboard/dashboard_data_tanggal', [DashboardController::class, 'index2'])->name('dashboard_data_tanggal');
    Route::get('/dashboard/dashboard_calculate', [DashboardController::class, 'dashboard_calculate'])->name('dashboard_calculate');
    Route::get('/dashboard/dashboard_data', [DashboardController::class, 'dashboard_data'])->name('dashboard_data');
    Route::get('/dashboard/dashboard_data_ver2', [DashboardController::class, 'dashboard_data_ver2'])->name('dashboard_data_ver2');
    Route::get('/dashboard/dashboard_data_ver3', [DashboardController::class, 'dashboard_data_ver3'])->name('dashboard_data_ver3');
    Route::get('/dashboard/dashboard_data_ver4', [DashboardController::class, 'dashboard_data_ver4'])->name('dashboard_data_ver4');
    Route::get('/dashboard/getDetail/{id}', [DashboardController::class, 'getDetail'])->name('getDetail');
    Route::get('/dashboard/getDetail2/{id}/{id2}', [DashboardController::class, 'getDetail2'])->name('getDetail2');

    Route::get('/dashboard/detailtaskdashboard/{id}', [DashboardController::class, 'detailtaskdashboard'])->name('detailtaskdashboard');
    Route::get('/permintaan_pengembangan/index3', [PermintaanPengembanganController::class, 'index3'])->name('permintaan_pengembangan.index3');
    Route::get('/permintaan_pengembangan/data3', [PermintaanPengembanganController::class, 'data3'])->name('permintaan_pengembangan.data3');
    
    
    Route::group(['middleware' => 'level:1,2,3,4,5'], function () {
        Route::get('/kategori/data', [KategoriController::class, 'data'])->name('kategori.data');
        Route::post('/kategori/delete-selected', [KategoriController::class, 'deleteSelected'])->name('kategori.delete_selected');
        Route::resource('/kategori', KategoriController::class);

        Route::get('/perencanaan_proyek', [PerencanaanProyekController::class, 'index'])->name('perencanaan_proyek.index');
        Route::get('/perencanaan_proyek/data', [PerencanaanProyekController::class, 'data'])->name('perencanaan_proyek.data');
        Route::get('perencanaan_proyek/updateProgress/{id}', [PerencanaanProyekController::class, 'editProgress'])->name('perencanaan_proyek.editProgress');
        Route::put('perencanaan_proyek/updateProgress/{id}', [PerencanaanProyekController::class, 'updateProgress'])->name('perencanaan_proyek.updateProgress');
        Route::delete('perencanaan_proyek/delete_selected', [PerencanaanProyekController::class, 'deleteSelected'])->name('perencanaan_proyek.delete_selected');
        Route::post('/perencanaan_proyek/update-pdf/{id}', [PerencanaanProyekController::class, 'updatePDF'])->name('perencanaan_proyek.updatePDF');
        Route::post('/perencanaan_proyek/cetak-dokumen', [PerencanaanProyekController::class, 'cetakDokumen'])->name('perencanaan_proyek.cetakDokumen');
        Route::get('/perencanaan_proyek/{id}/view', [PerencanaanProyekController::class, 'view'])->name('perencanaan_proyek.view');
        Route::post('/perencanaan-proyek/approve/{id}', [PerencanaanProyekController::class, 'approveProyek'])->name('perencanaan_proyek.approveProyek');
        Route::resource('/perencanaan_proyek', PerencanaanProyekController::class);

        Route::get('/persetujuan_pengembangan', [PersetujuanPengembanganController::class, 'index'])->name('persetujuan_pengembangan.index');
        Route::get('/persetujuan_pengembangan/data', [PersetujuanPengembanganController::class, 'data'])->name('persetujuan_pengembangan.data');
        Route::get('persetujuan_pengembangan/updateProgress/{id}', [PersetujuanPengembanganController::class, 'editProgress'])->name('persetujuan_pengembangan.editProgress');
        Route::put('persetujuan_pengembangan/updateProgress/{id}', [PersetujuanPengembanganController::class, 'updateProgress'])->name('persetujuan_pengembangan.updateProgress');
        Route::delete('persetujuan_pengembangan/delete_selected', [PersetujuanPengembanganController::class, 'deleteSelected'])->name('persetujuan_pengembangan.delete_selected');
        Route::post('/persetujuan_pengembangan/update-pdf/{id}', [PersetujuanPengembanganController::class, 'updatePDF'])->name('persetujuan_pengembangan.updatePDF');
        Route::post('/persetujuan_pengembangan/cetak-dokumen', [PersetujuanPengembanganController::class, 'cetakDokumen'])->name('persetujuan_pengembangan.cetakDokumen');
        Route::get('/get-alasan-persetujuan/{id}', [PersetujuanPengembanganController::class, 'getAlasanPersetujuan']);
        Route::get('/persetujuan_pengembangan/{id}/view', [PersetujuanPengembanganController::class, 'view'])->name('persetujuan_pengembangan.view');
        Route::get('persetujuan_pengembangan/approveForm/{id}', [PersetujuanPengembanganController::class, 'editApprove'])->name('persetujuan_pengembangan.editApprove');
        Route::post('/persetujuan_pengembangan/approve/{id}', [PersetujuanPengembanganController::class, 'approveProyek'])->name('persetujuan_pengembangan.approveProyek');
        Route::get('/persetujuan_pengembangan/get-persetujuanalasan/{id}', [PersetujuanPengembanganController::class, 'getPersetujuanAlasan'])->name('persetujuan_pengembangan.getPersetujuanAlasan');
        Route::resource('/persetujuan_pengembangan', PersetujuanPengembanganController::class);

        Route::get('/permintaan_pengembangan', [PermintaanPengembanganController::class, 'index'])->name('permintaan_pengembangan.index');
        Route::get('/permintaan_pengembangan/index2', [PermintaanPengembanganController::class, 'index2'])->name('permintaan_pengembangan.index2');
        Route::get('/permintaan_pengembangan/forminputpengembangan', [PermintaanPengembanganController::class, 'forminputpengembangan'])->name('permintaan_pengembangan.forminputpengembangan');
        // Route::get('/permintaan_pengembangan/generate', [PermintaanPengembanganController::class, 'generate'])->name('permintaan_pengembangan.generate');
        Route::get('/permintaan_pengembangan/data', [PermintaanPengembanganController::class, 'data'])->name('permintaan_pengembangan.data');
        Route::get('/permintaan_pengembangan/data2', [PermintaanPengembanganController::class, 'data2'])->name('permintaan_pengembangan.data2');
        Route::get('permintaan_pengembangan/updateProgress/{id}', [PermintaanPengembanganController::class, 'editProgress'])->name('permintaan_pengembangan.editProgress');
        Route::put('permintaan_pengembangan/updateProgress/{id}', [PermintaanPengembanganController::class, 'updateProgress'])->name('permintaan_pengembangan.updateProgress');
        Route::delete('permintaan_pengembangan/delete_selected', [PermintaanPengembanganController::class, 'deleteSelected'])->name('permintaan_pengembangan.delete_selected');
        Route::post('/permintaan_pengembangan/update-pdf/{id}', [PermintaanPengembanganController::class, 'updatePDF'])->name('permintaan_pengembangan.updatePDF');
        Route::post('/permintaan_pengembangan/cetak-dokumen', [PermintaanPengembanganController::class, 'cetakDokumen'])->name('permintaan_pengembangan.cetakDokumen');
        Route::get('/permintaan_pengembangan/cetak-dokumen-summary', [PermintaanPengembanganController::class, 'cetakDokumenSummary'])->name('permintaan_pengembangan.cetakDokumenSummary');
        Route::get('/permintaan_pengembangan/cetak-all-dokumen-summary', [PermintaanPengembanganController::class, 'cetakAllDokumenSummary'])->name('permintaan_pengembangan.cetakAllDokumenSummary');
        Route::post('/permintaan_pengembangan/cetak-dokumen-persetujuan', [PermintaanPengembanganController::class, 'cetakDokumenPersetujuan'])->name('permintaan_pengembangan.cetakDokumenPersetujuan');
        Route::get('/permintaan_pengembangan/{id}/view', [PermintaanPengembanganController::class, 'view'])->name('permintaan_pengembangan.view');
        Route::post('/permintaan_pengembangan/approve/{id}', [PermintaanPengembanganController::class, 'approveProyek'])->name('permintaan_pengembangan.approveProyek');
        Route::resource('/permintaan_pengembangan', PermintaanPengembanganController::class);

        // Route::get('/permintaan_persetujuan_pengembangan', [PermintaanPersetujuanPengembanganController::class, 'index'])->name('permintaan_persetujuan_pengembangan.index');
        // Route::get('/permintaan_persetujuan_pengembangan/data', [PermintaanPersetujuanPengembanganController::class, 'data'])->name('permintaan_persetujuan_pengembangan.data');
        // Route::get('permintaan_persetujuan_pengembangan/updateProgress/{id}', [PermintaanPersetujuanPengembanganController::class, 'editProgress'])->name('permintaan_persetujuan_pengembangan.editProgress');
        // Route::put('permintaan_persetujuan_pengembangan/updateProgress/{id}', [PermintaanPersetujuanPengembanganController::class, 'updateProgress'])->name('permintaan_persetujuan_pengembangan.updateProgress');
        // Route::delete('permintaan_persetujuan_pengembangan/delete_selected', [PermintaanPersetujuanPengembanganController::class, 'deleteSelected'])->name('permintaan_persetujuan_pengembangan.delete_selected');
        // Route::post('/permintaan_persetujuan_pengembangan/update-pdf/{id}', [PermintaanPersetujuanPengembanganController::class, 'updatePDF'])->name('permintaan_persetujuan_pengembangan.updatePDF');
        // Route::post('/permintaan_persetujuan_pengembangan/cetak-dokumen', [PermintaanPersetujuanPengembanganController::class, 'cetakDokumen'])->name('permintaan_persetujuan_pengembangan.cetakDokumen');
        // Route::get('/permintaan_persetujuan_pengembangan/{id}/view', [PermintaanPersetujuanPengembanganController::class, 'view'])->name('permintaan_persetujuan_pengembangan.view');
        // Route::post('/permintaan_persetujuan_pengembangan/approve/{id}', [PermintaanPersetujuanPengembanganController::class, 'approveProyek'])->name('permintaan_persetujuan_pengembangan.approveProyek');
        // Route::resource('/permintaan_persetujuan_pengembangan', PermintaanPersetujuanPengembanganController::class);

        Route::get('/serah_terima_aplikasi', [SerahTerimaAplikasiController::class, 'index'])->name('serah_terima_aplikasi.index');
        Route::get('/serah_terima_aplikasi/data', [SerahTerimaAplikasiController::class, 'data'])->name('serah_terima_aplikasi.data');
        Route::get('serah_terima_aplikasi/updateProgress/{id}', [SerahTerimaAplikasiController::class, 'editProgress'])->name('serah_terima_aplikasi.editProgress');
        Route::put('serah_terima_aplikasi/updateProgress/{id}', [SerahTerimaAplikasiController::class, 'updateProgress'])->name('serah_terima_aplikasi.updateProgress');
        Route::delete('serah_terima_aplikasi/delete_selected', [SerahTerimaAplikasiController::class, 'deleteSelected'])->name('serah_terima_aplikasi.delete_selected');
        Route::post('/serah_terima_aplikasi/update-pdf/{id}', [SerahTerimaAplikasiController::class, 'updatePDF'])->name('serah_terima_aplikasi.updatePDF');
        Route::post('/serah_terima_aplikasi/cetak-dokumen', [SerahTerimaAplikasiController::class, 'cetakDokumen'])->name('serah_terima_aplikasi.cetakDokumen');
        Route::get('/serah_terima_aplikasi/{id}/view', [SerahTerimaAplikasiController::class, 'view'])->name('serah_terima_aplikasi.view');
        Route::post('/serah_terima_aplikasi/approve/{id}', [SerahTerimaAplikasiController::class, 'approveProyek'])->name('serah_terima_aplikasi.approveProyek');
        Route::resource('/serah_terima_aplikasi', SerahTerimaAplikasiController::class);

        Route::get('/perencanaan_kebutuhan', [PerencanaanKebutuhanController::class, 'index'])->name('perencanaan_kebutuhan.index');
        Route::get('/perencanaan_kebutuhan/data', [PerencanaanKebutuhanController::class, 'data'])->name('perencanaan_kebutuhan.data');
        Route::get('perencanaan_kebutuhan/updateProgress/{id}', [PerencanaanKebutuhanController::class, 'editProgress'])->name('perencanaan_kebutuhan.editProgress');
        Route::put('perencanaan_kebutuhan/updateProgress/{id}', [PerencanaanKebutuhanController::class, 'updateProgress'])->name('perencanaan_kebutuhan.updateProgress');
        Route::delete('perencanaan_kebutuhan/delete_selected', [PerencanaanKebutuhanController::class, 'deleteSelected'])->name('perencanaan_kebutuhan.delete_selected');
        Route::post('/perencanaan_kebutuhan/update-pdf/{id}', [PerencanaanKebutuhanController::class, 'updatePDF'])->name('perencanaan_kebutuhan.updatePDF');
        Route::post('/perencanaan_kebutuhan/cetak-dokumen', [PerencanaanKebutuhanController::class, 'cetakDokumen'])->name('perencanaan_kebutuhan.cetakDokumen');
        Route::get('/perencanaan_kebutuhan/{id}/view', [PerencanaanKebutuhanController::class, 'view'])->name('perencanaan_kebutuhan.view');
        Route::post('/perencanaan_kebutuhan/approve/{id}', [PerencanaanKebutuhanController::class, 'approveProyek'])->name('perencanaan_kebutuhan.approveProyek');
        Route::resource('/perencanaan_kebutuhan', PerencanaanKebutuhanController::class);

        Route::get('/analisis_desain', [AnalisisDesainController::class, 'index'])->name('analisis_desain.index');
        Route::get('/analisis_desain/data', [AnalisisDesainController::class, 'data'])->name('analisis_desain.data');
        Route::get('analisis_desain/updateProgress/{id}', [AnalisisDesainController::class, 'editProgress'])->name('analisis_desain.editProgress');
        Route::put('analisis_desain/updateProgress/{id}', [AnalisisDesainController::class, 'updateProgress'])->name('analisis_desain.updateProgress');
        Route::delete('analisis_desain/delete_selected', [AnalisisDesainController::class, 'deleteSelected'])->name('analisis_desain.delete_selected');
        Route::post('/analisis_desain/update-pdf/{id}', [AnalisisDesainController::class, 'updatePDF'])->name('analisis_desain.updatePDF');
        Route::post('/analisis_desain/cetak-dokumen', [AnalisisDesainController::class, 'cetakDokumen'])->name('analisis_desain.cetakDokumen');
        Route::get('/analisis_desain/{id}/view', [AnalisisDesainController::class, 'view'])->name('analisis_desain.view');
        Route::post('/analisis_desain/approve/{id}', [AnalisisDesainController::class, 'approveProyek'])->name('analisis_desain.approveProyek');
        Route::resource('/analisis_desain', AnalisisDesainController::class);

        Route::get('/pengembangan_aplikasi', [PengembanganAplikasiController::class, 'index'])->name('pengembangan_aplikasi.index');
        Route::get('/pengembangan_aplikasi/data', [PengembanganAplikasiController::class, 'data'])->name('pengembangan_aplikasi.data');
        Route::get('pengembangan_aplikasi/updateProgress/{id}', [PengembanganAplikasiController::class, 'editProgress'])->name('pengembangan_aplikasi.editProgress');
        Route::put('pengembangan_aplikasi/updateProgress/{id}', [PengembanganAplikasiController::class, 'updateProgress'])->name('pengembangan_aplikasi.updateProgress');
        Route::delete('pengembangan_aplikasi/delete_selected', [PengembanganAplikasiController::class, 'deleteSelected'])->name('pengembangan_aplikasi.delete_selected');
        Route::post('/pengembangan_aplikasi/update-pdf/{id}', [PengembanganAplikasiController::class, 'updatePDF'])->name('pengembangan_aplikasi.updatePDF');
        Route::post('/pengembangan_aplikasi/cetak-dokumen', [PengembanganAplikasiController::class, 'cetakDokumen'])->name('pengembangan_aplikasi.cetakDokumen');
        Route::get('/pengembangan_aplikasi/{id}/view', [PengembanganAplikasiController::class, 'view'])->name('pengembangan_aplikasi.view');
        Route::post('/pengembangan_aplikasi/approve/{id}', [PengembanganAplikasiController::class, 'approveProyek'])->name('pengembangan_aplikasi.approveProyek');
        Route::resource('/pengembangan_aplikasi', PengembanganAplikasiController::class);

        Route::get('/user_acceptance_testing', [UserAcceptanceTestingController::class, 'index'])->name('user_acceptance_testing.index');
        Route::get('/user_acceptance_testing/data', [UserAcceptanceTestingController::class, 'data'])->name('user_acceptance_testing.data');
        Route::get('user_acceptance_testing/updateProgress/{id}', [UserAcceptanceTestingController::class, 'editProgress'])->name('user_acceptance_testing.editProgress');
        Route::put('user_acceptance_testing/updateProgress/{id}', [UserAcceptanceTestingController::class, 'updateProgress'])->name('user_acceptance_testing.updateProgress');
        Route::delete('user_acceptance_testing/delete_selected', [UserAcceptanceTestingController::class, 'deleteSelected'])->name('user_acceptance_testing.delete_selected');
        Route::post('/user_acceptance_testing/update-pdf/{id}', [UserAcceptanceTestingController::class, 'updatePDF'])->name('user_acceptance_testing.updatePDF');
        Route::post('/user_acceptance_testing/cetak-dokumen-perencanaan', [UserAcceptanceTestingController::class, 'cetakDokumenPerencanaan'])->name('user_acceptance_testing.cetakDokumenPerencanaan');
        Route::post('/user_acceptance_testing/cetak-dokumen', [UserAcceptanceTestingController::class, 'cetakDokumen'])->name('user_acceptance_testing.cetakDokumen');
        Route::post('/user_acceptance_testing/approve/{id}', [UserAcceptanceTestingController::class, 'approveProyek'])->name('user_acceptance_testing.approveProyek');
        Route::resource('/user_acceptance_testing', UserAcceptanceTestingController::class);
        
        Route::get('/user_acceptance_testing_dev', [UserAcceptanceTestingDevController::class, 'index'])->name('user_acceptance_testing_dev.index');
        Route::get('/user_acceptance_testing_dev/data', [UserAcceptanceTestingDevController::class, 'data'])->name('user_acceptance_testing_dev.data');
        Route::get('user_acceptance_testing_dev/updateProgress/{id}', [UserAcceptanceTestingDevController::class, 'editProgress'])->name('user_acceptance_testing_dev.editProgress');
        Route::put('user_acceptance_testing_dev/updateProgress/{id}', [UserAcceptanceTestingDevController::class, 'updateProgress'])->name('user_acceptance_testing_dev.updateProgress');
        Route::delete('user_acceptance_testing_dev/delete_selected', [UserAcceptanceTestingDevController::class, 'deleteSelected'])->name('user_acceptance_testing_dev.delete_selected');
        Route::post('/user_acceptance_testing_dev/update-pdf/{id}', [UserAcceptanceTestingDevController::class, 'updatePDF'])->name('user_acceptance_testing_dev.updatePDF');
        Route::post('/user_acceptance_testing_dev/cetak-dokumen-perencanaan', [UserAcceptanceTestingDevController::class, 'cetakDokumenPerencanaan'])->name('user_acceptance_testing_dev.cetakDokumenPerencanaan');
        Route::post('/user_acceptance_testing_dev/cetak-dokumen', [UserAcceptanceTestingDevController::class, 'cetakDokumen'])->name('user_acceptance_testing_dev.cetakDokumen');
        Route::post('/user_acceptance_testing_dev/approve/{id}', [UserAcceptanceTestingDevController::class, 'approveProyek'])->name('user_acceptance_testing_dev.approveProyek');
        Route::resource('/user_acceptance_testing_dev', UserAcceptanceTestingDevController::class);

        Route::get('/quality_assurance_testing', [QualityAssuranceTestingController::class, 'index'])->name('quality_assurance_testing.index');
        Route::get('/quality_assurance_testing/index2', [QualityAssuranceTestingController::class, 'index2'])->name('quality_assurance_testing.index2');
        Route::get('/quality_assurance_testing/data', [QualityAssuranceTestingController::class, 'data'])->name('quality_assurance_testing.data');
        Route::get('quality_assurance_testing/updateProgress/{id}', [QualityAssuranceTestingController::class, 'editProgress'])->name('quality_assurance_testing.editProgress');
        Route::put('quality_assurance_testing/updateProgress/{id}', [QualityAssuranceTestingController::class, 'updateProgress'])->name('quality_assurance_testing.updateProgress');
        Route::delete('quality_assurance_testing/delete_selected', [QualityAssuranceTestingController::class, 'deleteSelected'])->name('quality_assurance_testing.delete_selected');
        Route::post('/quality_assurance_testing/update-pdf/{id}', [QualityAssuranceTestingController::class, 'updatePDF'])->name('quality_assurance_testing.updatePDF');
        Route::post('/quality_assurance_testing/cetak-dokumen', [QualityAssuranceTestingController::class, 'cetakDokumen'])->name('quality_assurance_testing.cetakDokumen');
        Route::post('/quality_assurance_testing/approve/{id}', [QualityAssuranceTestingController::class, 'approveProyek'])->name('quality_assurance_testing.approveProyek');
        Route::resource('/quality_assurance_testing', QualityAssuranceTestingController::class);
        
        Route::get('/detail-quality-assurance-testing/create/{id_quality_assurance_testing}', [DetailQualityAssuranceTestingController::class, 'create'])->name('detail-quality-assurance-testing.create');
        Route::get('/detail-quality-assurance-testing/edit/{id}/{detail_id}', [DetailQualityAssuranceTestingController::class, 'edit'])->name('detail-quality-assurance-testing.edit');
        Route::resource('detail-quality-assurance-testing', DetailQualityAssuranceTestingController::class);
        Route::get('/detail-user-acceptance-testing/create/{id_user_acceptance_testing}', [DetailUserAcceptanceTestingController::class, 'create'])->name('detail-user-acceptance-testing.create');
        Route::get('/detail-user-acceptance-testing/edit/{id}/{detail_id}', [DetailUserAcceptanceTestingController::class, 'edit'])->name('detail-user-acceptance-testing.edit');
        Route::resource('detail-user-acceptance-testing', DetailUserAcceptanceTestingController::class);
        Route::get('/detail-ttd-qat/create/{id_quality_assurance_testing}', [DetailTTDQualityAssuranceTestingController::class, 'create'])->name('detail-ttd-qat.create');
        Route::resource('detail-ttd-qat', DetailTTDQualityAssuranceTestingController::class);

        Route::get('/produk/data', [ProdukController::class, 'data'])->name('produk.data');
        Route::post('/produk/delete-selected', [ProdukController::class, 'deleteSelected'])->name('produk.delete_selected');
        Route::post('/produk/cetak-barcode', [ProdukController::class, 'cetakBarcode'])->name('produk.cetak_barcode');
        Route::resource('/produk', ProdukController::class);

        Route::get('/member/data', [MemberController::class, 'data'])->name('member.data');
        Route::post('/member/cetak-member', [MemberController::class, 'cetakMember'])->name('member.cetak_member');
        Route::resource('/member', MemberController::class);

        Route::get('/supplier/data', [SupplierController::class, 'data'])->name('supplier.data');
        Route::resource('/supplier', SupplierController::class);

        Route::get('/pengeluaran/data', [PengeluaranController::class, 'data'])->name('pengeluaran.data');
        Route::resource('/pengeluaran', PengeluaranController::class);

        Route::get('/pembelian/data', [PembelianController::class, 'data'])->name('pembelian.data');
        Route::get('/pembelian/{id}/create', [PembelianController::class, 'create'])->name('pembelian.create');
        Route::resource('/pembelian', PembelianController::class)
            ->except('create');

        Route::get('/pembelian_detail/{id}/data', [PembelianDetailController::class, 'data'])->name('pembelian_detail.data');
        Route::get('/pembelian_detail/loadform/{diskon}/{total}', [PembelianDetailController::class, 'loadForm'])->name('pembelian_detail.load_form');
        Route::resource('/pembelian_detail', PembelianDetailController::class)
            ->except('create', 'show', 'edit');

        Route::get('/penjualan/data', [PenjualanController::class, 'data'])->name('penjualan.data');
        Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
        Route::get('/penjualan/{id}', [PenjualanController::class, 'show'])->name('penjualan.show');
        Route::delete('/penjualan/{id}', [PenjualanController::class, 'destroy'])->name('penjualan.destroy');
    });

    Route::group(['middleware' => 'level:1,2'], function () {
        Route::get('/transaksi/baru', [PenjualanController::class, 'create'])->name('transaksi.baru');
        Route::post('/transaksi/simpan', [PenjualanController::class, 'store'])->name('transaksi.simpan');
        Route::get('/transaksi/selesai', [PenjualanController::class, 'selesai'])->name('transaksi.selesai');
        Route::get('/transaksi/nota-kecil', [PenjualanController::class, 'notaKecil'])->name('transaksi.nota_kecil');
        Route::get('/transaksi/nota-besar', [PenjualanController::class, 'notaBesar'])->name('transaksi.nota_besar');

        Route::get('/transaksi/{id}/data', [PenjualanDetailController::class, 'data'])->name('transaksi.data');
        Route::get('/transaksi/loadform/{diskon}/{total}/{diterima}', [PenjualanDetailController::class, 'loadForm'])->name('transaksi.load_form');
        Route::resource('/transaksi', PenjualanDetailController::class)
            ->except('create', 'show', 'edit');
    });

    Route::group(['middleware' => 'level:1'], function () {
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/data/{awal}/{akhir}', [LaporanController::class, 'data'])->name('laporan.data');
        Route::get('/laporan/pdf/{awal}/{akhir}', [LaporanController::class, 'exportPDF'])->name('laporan.export_pdf');

        Route::get('/user/data', [UserController::class, 'data'])->name('user.data');
        Route::resource('/user', UserController::class);

        Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
        Route::get('/setting/first', [SettingController::class, 'show'])->name('setting.show');
        Route::post('/setting', [SettingController::class, 'update'])->name('setting.update');
    });

    Route::group(['middleware' => 'level:1,2,3,5'], function () {
        Route::get('/profil', [UserController::class, 'profil'])->name('user.profil');
        Route::post('/profil', [UserController::class, 'updateProfil'])->name('user.update_profil');
    });

    // For Generate Storage Link
    // Route::get('/generate', function(){
    //     \Illuminate\Support\Facades\Artisan::call('storage:link');
    //     echo 'ok';
    //  });
});

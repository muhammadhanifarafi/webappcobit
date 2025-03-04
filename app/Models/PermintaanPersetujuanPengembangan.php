<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanPersetujuanPengembangan extends Model
{
    use HasFactory;

    protected $table = 'trx_permintaan_persetujuan_pengembangan';
    protected $primaryKey = 'id_permintaan_pengembangan';
    protected $fillable = [
        'nomor_dokumen','latar_belakang','tujuan','target_implementasi_sistem','fungsi_sistem_informasi','jenis_aplikasi','pengguna','uraian_permintaan_tambahan',
        'lampiran','nama_pemohon','jabatan_pemohon','tanggal_disiapkan','nama','jabatan','tanggal_disetujui', 'file_pdf_permintaan', 'file_pdf_persetujuan', 'progress', 'pic', 'is_approve'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanPengembangan extends Model
{
    use HasFactory;

    protected $table = 'trx_permintaan_pengembangan';
    protected $primaryKey = 'id_permintaan_pengembangan';
    protected $fillable = [
        'id_permintaan_pengembangan',
        'judul',
        'nomor_dokumen',
        'latar_belakang',
        'tujuan',
        'target_implementasi_sistem',
        'fungsi_sistem_informasi',
        'jenis_aplikasi',
        'pengguna',
        'jenis_kepala_bagian',
        'uraian_permintaan_tambahan',
        'lampiran',
        'lampiran_2',
        'nama_pemohon',
        'jabatan_pemohon',
        'tanggal_disiapkan',
        'nama_penyetuju',
        'jabatan_penyetuju',
        'tanggal_disetujui',
        'file_pdf',
        'pic',
        'progress',
        'created_at',
        'updated_at',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_approve',
        'approve_at',
        'approve_by',
        'is_approve_penyetuju',
        'approve_at_penyetuju',
        'approve_by_penyetuju',
        'path_qrcode_pemohon',
        'path_qrcode_penyetuju',
        'nik_pemohon',
        'nik_penyetuju',
        'created_by'
    ];    
}

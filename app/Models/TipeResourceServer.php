<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipeResourceServer extends Model
{
    use HasFactory;

    protected $table = 'mst_tipe_resource_server';  // Ganti dengan nama tabel yang sesuai
    protected $primaryKey = 'id';               // Pastikan primary key sesuai
    protected $fillable = ['nama', 'deskripsi'];  // Daftar kolom yang bisa diisi massal

    public $timestamps = false;  // Jika tabel tidak memiliki kolom created_at dan updated_at
}   
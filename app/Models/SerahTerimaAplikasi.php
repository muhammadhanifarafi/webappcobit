<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SerahTerimaAplikasi extends Model
{
    use HasFactory;

    protected $table = 'trx_serah_terima_aplikasi';
    protected $primaryKey = 'id_serah_terima_aplikasi';
    protected $guarded = [];
}
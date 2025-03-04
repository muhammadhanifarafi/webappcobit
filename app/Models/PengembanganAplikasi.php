<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengembanganAplikasi extends Model
{
    use HasFactory;

    protected $table = 'trx_pengembangan_aplikasi';
    protected $primaryKey = 'id_pengembangan_aplikasi';
    protected $guarded = [];  
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalisisDesain extends Model
{
    use HasFactory;

    protected $table = 'trx_analisis_desain';
    protected $primaryKey = 'id_analisis_desain';
    protected $guarded = [];
}

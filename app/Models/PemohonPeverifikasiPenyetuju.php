<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemohonPeverifikasiPenyetuju extends Model
{
    use HasFactory;
    protected $table = 'sitmsemployee';
    protected $primaryKey = 'employee_id';
    protected $guarded = [];
}
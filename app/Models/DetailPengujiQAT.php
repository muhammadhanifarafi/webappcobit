<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPengujiQAT extends Model
{
    use HasFactory;
    protected $table = 'detail_penguji_quality_assurance_testing';
    protected $primaryKey = 'id_penguji_qat';
    protected $guarded = [];  
}

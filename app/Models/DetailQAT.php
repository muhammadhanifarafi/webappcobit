<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailQAT extends Model
{
    use HasFactory;
    protected $table = 'detail_quality_assurance_testing';
    protected $primaryKey = 'id_detail_quality_assurance_testing';
    protected $guarded = [];  
}

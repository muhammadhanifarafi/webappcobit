<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPengujian extends Model
{
    use HasFactory;
    protected $table = 'detail_user_acceptance_testing';
    protected $primaryKey = 'id_detail_user_acceptance_testing';
    protected $guarded = [];  
}

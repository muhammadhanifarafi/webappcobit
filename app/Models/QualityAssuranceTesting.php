<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QualityAssuranceTesting extends Model
{
    use HasFactory;

    protected $table = 'trx_quality_assurance_testing';
    protected $primaryKey = 'id_quality_assurance_testing';
    protected $guarded = [];
}

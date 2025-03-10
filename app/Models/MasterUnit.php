<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterUnit extends Model
{
    use HasFactory;

    protected $table = 'master_unit';
    protected $primaryKey = 'unit_id';
    protected $guarded = [];
}
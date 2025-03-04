<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAcceptanceTesting extends Model
{
    use HasFactory;

    protected $table = 'trx_user_acceptance_testing';
    protected $primaryKey = 'id_user_acceptance_testing';
    protected $guarded = [];
}

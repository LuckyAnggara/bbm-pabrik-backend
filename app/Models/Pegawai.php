<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pegawai extends Model
{
    use HasFactory, SoftDeletes;

       protected $fillable = [
        'name',
        'jabatan',
        'gaji',
        'uang_makan',
        'bonus',
        'created_by',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabaRugi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor',
        'account',
        'class',
        'balance',
        'created_at'
    ];
}
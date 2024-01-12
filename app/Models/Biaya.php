<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Biaya extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'tanggal_transaksi',
        'kategori',
        'jumlah',

        'created_by'
    ];

    protected $casts = [
        'tanggal_transaksi' => 'datetime:d F Y',
    ];
}

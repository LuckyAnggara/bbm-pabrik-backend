<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pembelian extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $casts = [
        'created_at' => 'datetime:d F Y',
    ];

    public function detail()
    {
        return $this->hasMany(DetailPembelian::class, 'pembelian_id', 'id');
    }
}

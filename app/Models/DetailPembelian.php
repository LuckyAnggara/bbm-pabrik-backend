<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPembelian extends Model
{
    use HasFactory;

        protected $fillable = [
        'pembelian_id',
        'item_id',
        'jumlah',
        'harga',
        
    ];

     public function item()
    {
        return $this->hasOne(Item::class, 'id', 'item_id');
    }
}

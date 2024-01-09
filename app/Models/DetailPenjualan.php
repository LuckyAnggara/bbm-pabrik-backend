<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    use HasFactory;

        protected $fillable = [
        'penjualan_id',
        'item_id',
        'jumlah',
        'harga',
        
    ];

     public function item()
    {
        return $this->hasOne(Item::class, 'id', 'item_id');
    }
}

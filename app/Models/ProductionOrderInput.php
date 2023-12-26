<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductionOrderInput extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'production_id',
        'item_id',
        'estimate_quantity',
        'real_quantity',
    ];

    public function item()
    {
        return $this->hasOne(Item::class, 'id', 'item_id');
    }
}

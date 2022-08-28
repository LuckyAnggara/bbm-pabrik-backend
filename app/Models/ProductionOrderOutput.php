<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductionOrderOutput extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'production_id',
        'item_id',
        'type_id',
        'target_quantity',
        'real_quantity',
    ];

    public function item()
    {
        return $this->hasOne(Item::class, 'id', 'item_id');
    }

    public function type()
    {
        return $this->hasOne(ItemType::class, 'id', 'type_id');
    }

}

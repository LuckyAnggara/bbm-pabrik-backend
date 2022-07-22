<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mutation extends Model
{
    use SoftDeletes, HasFactory;
    protected $fillable = [
        'item_id',
        'warehouse_id',
        'debit',
        'kredit',
        'saldo',
    ];

    public function item()
    {
        return $this->hasOne(Item::class, 'id', 'item_id');
    }

    public function warehouse()
    {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }
}

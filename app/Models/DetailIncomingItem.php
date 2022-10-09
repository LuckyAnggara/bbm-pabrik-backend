<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailIncomingItem extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'master_id',
        'item_id',
        'qty',
    ];

    public function item()
    {
        return $this->hasOne(Item::class, 'id', 'item_id');
    }
}

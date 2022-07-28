<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'type_id',
        'unit_id',
        'warehouse_id',
        'created_by',
    ];

    protected $appends = ['balance'];

    public function type()
    {
        return $this->hasOne(ItemType::class, 'id', 'type_id');
    }

    public function unit()
    {
        return $this->hasOne(ItemUnit::class, 'id', 'unit_id');
    }

    public function warehouse()
    {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function mutation()
    {
        return $this->hasMany(Mutation::class, 'item_id', 'id')->orderBy('created_at');
    }

    public function getBalanceAttribute()
    {
        return 0;
    }
}

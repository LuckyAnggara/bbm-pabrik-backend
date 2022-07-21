<?php

namespace App\Models;

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
    ];

    // public function type()
    // {
    //     return $this->hasOne(Type::class, 'id', 'type_id');
    // }

    // public function unit()
    // {
    //     return $this->hasOne(Unit::class, 'id', 'unit_id');
    // }
}

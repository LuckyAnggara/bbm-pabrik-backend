<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionOrderMachine extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'production_id',
        'machine_id',
        'usage_meter',
    ];

    public function machine()
    {
        return $this->hasOne(Machine::class, 'id', 'machine_id');
    }
}
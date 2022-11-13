<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionOrderOverhead extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'production_id',
        'overhead_id',
        'usage_meter',
    ];

    public function overhead()
    {
        return $this->hasOne(Overhead::class, 'id', 'overhead_id');
    }
}

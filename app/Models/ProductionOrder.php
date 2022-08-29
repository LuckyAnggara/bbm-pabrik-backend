<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionOrder extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'sequence',
        'pic_name',
        'customer_name',
        'notes',
        'status',
        'target_date',
        'order_date',
        'created_by',
        'created_at',
    ];

    protected $dates = [
        'order_date',
        'target_date',
    ];

    public function input()
    {
        return $this->hasMany(ProductionOrderInput::class, 'production_id', 'id')->orderBy('created_at');
    }

    public function output()
    {
        return $this->hasMany(ProductionOrderOutput::class, 'production_id', 'id')->orderBy('created_at');
    }

    
    public function timeline()
    {
        return $this->hasMany(ProductionOrderTimeline::class, 'production_id', 'id')->orderBy('created_at');
    }
    
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }


}

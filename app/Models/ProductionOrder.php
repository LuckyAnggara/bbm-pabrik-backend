<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionOrder extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'shift',
        'sequence',
        'pic_name',
        'customer_name',
        'notes',
        'status',
        'jenis_hasil',
        'target_date',
        'shipping_id',
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

    public function jenis()
    {
        return $this->hasOne(ItemType::class, 'id', 'jenis_hasil');
    }

    public function output()
    {
        return $this->hasMany(ProductionOrderOutput::class, 'production_id', 'id')->orderBy('created_at');
    }

    public function machine()
    {
        return $this->hasMany(ProductionOrderMachine::class, 'production_id', 'id')->orderBy('created_at');
    }

    public function overhead()
    {
        return $this->hasMany(ProductionOrderOverhead::class, 'production_id', 'id')->orderBy('created_at');
    }

    public function timeline()
    {
        return $this->hasMany(ProductionOrderTimeline::class, 'production_id', 'id')->orderBy('created_at');
    }
    public function pegawai()
    {
        return $this->hasMany(ProductionOrderPegawai::class, 'production_id', 'id')->orderBy('created_at');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }



    public function shipping()
    {
        return $this->hasOne(Shipping::class, 'id', 'shipping_id');
    }
}

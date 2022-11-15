<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipping extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'master_exit_item_id',
        'is_po',
        'production_order_id',
        'driver_name',
        'police_number',
        'created_by',
        'man_power_name',
        'shipping_date',
        'receiving_date',
        'receiver_name',
        'proof',
    ];
}

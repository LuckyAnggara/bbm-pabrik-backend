<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionOrderTimeline extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'production_id',
        'status',
        'created_by',
        'notes',
    ];



    public function user()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

}

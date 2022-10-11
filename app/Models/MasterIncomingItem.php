<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterIncomingItem extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'notes',
        'mutation_code',
        'data_date',
        'type',
        'admin_name',
        'no_pol',
        'driver_name',
        'created_by',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function detail()
    {
        return $this->hasMany(DetailIncomingItem::class, 'master_id', 'id');
    }
}

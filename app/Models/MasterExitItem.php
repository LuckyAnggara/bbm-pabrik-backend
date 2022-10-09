<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterExitItem extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'notes',
        'mutation_code',
        'data_date',
        'type',
        'created_by',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function detail()
    {
        return $this->hasMany(DetailExitItem::class, 'master_id', 'id');
    }
}

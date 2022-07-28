<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemType extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'created_by'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}

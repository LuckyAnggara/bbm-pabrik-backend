<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Warehouse extends Model
{
    use SoftDeletes, HasFactory;
    protected $fillable = [
        'name',
        'location',
        'created_by'
    ];   

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}

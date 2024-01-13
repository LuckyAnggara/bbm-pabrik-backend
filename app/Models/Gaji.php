<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gaji extends Model
{
    use HasFactory;
    protected $fillable = [
        'pegawai_id',
        'gaji',
        'uang_makan',
        'bonus',
        'created_by',
        'created_at'
    ];

         protected $casts = [
        'created_at' => 'datetime:d F Y',
    ];

        public function pegawai()
    {
        return $this->hasOne(Pegawai::class, 'id', 'pegawai_id');
    }


}

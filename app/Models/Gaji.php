<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gaji extends Model
{
    use HasFactory;
    protected $fillable = [
        'pegawai_id',
        'jam_kerja',
        'gaji',
        'uang_makan',
        'bonus',
        'potongan',
        'created_by',
        'created_at'
    ];
    
    protected $appends = ['total'];

   
    public function pegawai()
    {
        return $this->hasOne(Pegawai::class, 'id', 'pegawai_id');
    }

    public function getTotalAttribute()
    {
        $total = ($this->jam_kerja * $this->gaji) + $this->uang_makan + $this->bonus - $this->potongan;
        return $total;
    }

}

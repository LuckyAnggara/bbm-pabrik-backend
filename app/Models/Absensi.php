<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'pin',
        'start_time',
        'end_time',
        'shift_type',
        'tanggal_data',
        'status_scan'
    ];

    public function pegawai()
    {
        return $this->hasOne(Pegawai::class, 'pin', 'pin');
    }
}

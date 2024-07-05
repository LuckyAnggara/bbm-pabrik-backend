<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'pin',
        'scan_date',
        'jam_masuk',
        'jam_pulang',
        'verify',
        'status_scan',
        'tanggal_data'
    ];

    public function pegawai()
    {
        return $this->hasOne(Pegawai::class, 'pin', 'pin');
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'pin',
        'scan_date',
        'start_time',
        'end_time',
        'shift_type',
        'tanggal_data',
        'status_scan',
        'missing',
    ];


    protected $appends = ['jamKerja'];

    public function pegawai()
    {
        return $this->hasOne(Pegawai::class, 'pin', 'pin');
    }

 public function getJamKerjaAttribute()
    {

        $startDate = Carbon::parse($this->start_time);
        $endDate = Carbon::parse($this->end_time);
        
        $hours = $startDate->diffInHours($endDate);
        return $hours;
    }

    
}

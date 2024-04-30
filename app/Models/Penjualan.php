<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penjualan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nomor_faktur',
        'pelanggan_id',
        'nama_pelanggan',
        'alamat',
        'nomor_telepon',
        'sub_total',
        'diskon',
        'pajak',
        'total',
        'status',
        'created_at',
        'created_by',
    ];

     protected $casts = [
        'created_at' => 'datetime:d F Y',
    ];

    

    public function detail()
    {
        return $this->hasMany(DetailPenjualan::class, 'penjualan_id', 'id');
    }

    public static function generateFakturNumber()
    {
        $date = now();
        $formattedDate = $date->format('Y/m/d');
        $latestInvoice = self::where('nomor_faktur', 'like','BBM-SLS-' . $formattedDate . '%')->latest()->first();

   
        if (!$latestInvoice) {
            $number = 1;
        } else {
            $latestNumber = explode('/', $latestInvoice->nomor_faktur)[3];
            $number = intval($latestNumber) + 1;
        }

        $fakturNumber = 'BBM-SLS-' . $formattedDate . '/' . str_pad($number, 4, '0', STR_PAD_LEFT);

        return $fakturNumber;
    }

        public function pelanggan()
    {
        return $this->hasOne(Pelanggan::class, 'id', 'pelanggan_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}

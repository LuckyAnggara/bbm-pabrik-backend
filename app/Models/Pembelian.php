<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pembelian extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nomor_faktur',
        'nama_supplier',
        'sub_total',
        'diskon',
        'pajak',
        'total',
        'created_at',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime:d F Y',
    ];

    public function detail()
    {
        return $this->hasMany(DetailPembelian::class, 'pembelian_id', 'id');
    }


    public static function generateFakturNumber()
    {
        $date = now();
        $formattedDate = $date->format('Y/m/d');
        $latestInvoice = self::where('nomor_faktur', 'like','BBM-' . $formattedDate . '%')->latest()->first();

        if (!$latestInvoice) {
            $number = 1;
        } else {
            $latestNumber = explode('/', $latestInvoice->nomor_faktur)[3];
            $number = intval($latestNumber) + 1;
        }

        $fakturNumber = 'BBM-' . $formattedDate . '/' . str_pad($number, 4, '0', STR_PAD_LEFT);

        return $fakturNumber;
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
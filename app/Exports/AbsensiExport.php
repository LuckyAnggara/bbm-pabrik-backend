<?php

namespace App\Exports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


// class AbsensiExport implements FromQuery
// {
//     use Exportable;

//     public function forMonth(int $month)
//     {
//         $this->month = $month;
//         return $this;
//     }
    
//     public function forPin(int $pin)
//     {
//         $this->pin = $pin;
//         return $this;
//     }
//     public function query()
//     {
//         return Absensi::query()->where('pin', $this->pin)->whereMonth('tanggal_data', $this->month);
//     }

    
// }

class AbsensiExport implements FromView
{
    use Exportable;

    public function __construct(int $pin, int $month)
    {
        $this->pin = $pin;
        $this->month = $month;
    }
    
    public function view(): View
    {
        $data = Absensi::where('pin', $this->pin)
           ->when($this->month, function ($query, $month) {
                return $query->whereMonth('tanggal_data', $month);
            })
        ->get();
        return view('laporan.absensi', [
            'absensi' => $data
        ]);
    }
}

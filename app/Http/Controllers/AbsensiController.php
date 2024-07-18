<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use App\Models\Absensi;
use App\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
            use Barryvdh\DomPDF\Facade\Pdf;


class AbsensiController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 5000);
        $startDate = $request->input('start-date', Carbon::now());
        $endDate = $request->input('end-date', Carbon::now());

        $data = Absensi::with('pegawai')->whereBetween('tanggal_data', [$startDate, $endDate])
            ->orderBy('tanggal_data')
            ->latest()
            ->paginate($perPage);


        // $data = Pegawai::with(['absensi' => function ($query) use ($startDate, $endDate) {
        //     $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
        //     $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
        //     $query->whereBetween('tanggal_data', [$startDate, $endDate]);
        // }])
        //     // ->orderBy('tanggal_data', 'desc')
        //     ->latest()
        //     ->paginate($perPage);

        // $data = Pegawai::selectRaw('pegawais.*, absensis.*')
        //     ->join('absensis', 'pegawais.pin', '=', 'absensis.pin') // Join with pegawais table
        //     ->whereBetween('absensis.tanggal_data', [$startDate, $endDate])
        //     ->groupBy('pegawais.pin') // Filter by date range
        //     ->orderBy('absensis.tanggal_data', 'desc') // Order by tanggal_data
        //     ->paginate($perPage);

        return $this->sendResponse($data, 'Data fetched');
    }

    public function show($id, Request $request)
    {
        $month = $request->input('month', Carbon::now());
        $year = $request->input('year', Carbon::now());

        
        $data = Absensi::where('pin', $id)
           ->when($month, function ($query) use ($month, $year) {
                return $query->whereMonth('tanggal_data', $month)->whereYear('tanggal_data',$year);
            })
        ->get();
        if ($data) {
            return $this->sendResponse($data, 'Data fetched');
        }
        return $this->sendError('Data not found');
    }

         public function test($id, Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $monthName = Carbon::create()->month($month)->translatedFormat('F');
        $pegawai = Pegawai::where('pin', $id)->first();
        
        $data = Absensi::where('pin', $id)
           ->when($month, function ($query) use ($month, $year) {
                return $query->whereMonth('tanggal_data', $month)->whereYear('tanggal_data',$year);
            })
        ->get();
        if ($data) {
            return view('laporan.absensi',['absensi' => $data, 'bulan' => $monthName, 'pegawai'=> $pegawai]);
        }
        return $this->sendError('Data not found');
    }

  
    static function getDataAbsensi(Request $request)
    {
        $startDate = $request->date;
        $endDate = $request->date;

        $url = 'https://developer.fingerspot.io/api/get_attlog';
        $data = '{"trans_id":"1", "cloud_id":"' . env('ABSEN_CLOUD_ID') . '", "start_date":"' . $startDate . '", "end_date":"' . $endDate . '"}';
        $authorization = 'Authorization: Bearer ' . env('ABSEN_TOKEN');

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', $authorization]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        $final = json_decode($result);

        $data = $final->data;

        // Ubah data JSON menjadi Collection
        $collection = collect($data);

        // Filter dan ambil absensi paling pagi untuk setiap pin pada hari yang sama
        $filteredData = $collection->groupBy(function ($item) {
            // Group by pin and date (YYYY-MM-DD)
            return $item->pin . '_' . Carbon::parse($item->scan_date)->format('Y-m-d');
        })->map(function ($items) {
            // Ambil absensi masuk (scan_status = 0) paling pagi
            $masuk = $items->where('status_scan', 0)->sortBy('scan_date')->first();

            // Ambil absensi pulang (scan_status = 1) paling malam
            $pulang = $items->where('status_scan', 1)->sortByDesc('scan_date')->first();

            // Gabungkan hasil masuk dan pulang
            return collect([$masuk, $pulang])->filter();
        })->flatten(1);

        return $filteredData;
    }

    public function fetchAndStoreData(Request $request)
    {
        $date = '2024-07-06';
        $existing = Absensi::whereDate('tanggal_data', $date)->get();

        if ($existing) {
            foreach ($existing as $key => $value) {
                $value->delete();
            }
        }

        $url = 'https://developer.fingerspot.io/api/get_attlog';
        $data = '{"trans_id":"1", "cloud_id":"' . env('ABSEN_CLOUD_ID') . '", "start_date":"' . $date . '", "end_date":"' . $date . '"}';
        $authorization = 'Authorization: Bearer ' . env('ABSEN_TOKEN');

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', $authorization]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        $final = json_decode($result);

        $data = $final->data;

        if (isset($data)) {

            foreach ($data as $record) {
                $existingRecord = Absensi::where('pin', $record->pin)
                    ->whereDate('jam_masuk', '=', date('Y-m-d', strtotime($record->scan_date)))
                    ->first();

                if ($existingRecord) {
                    if ($record->status_scan == 1) {
                        $existingRecord->jam_pulang = $record->scan_date;
                    }
                    $existingRecord->save();
                } else {
                    Absensi::create([
                        'pin' => $record->pin,
                        'scan_date' => $record->scan_date,
                        'jam_masuk' => $record->status_scan == 0 ? $record->scan_date : null,
                        'jam_pulang' => $record->status_scan == 1 ? $record->scan_date : null,
                        'verify' => $record->verify,
                        'status_scan' => $record->status_scan,
                        'tanggal_data' => $date,
                    ]);
                }
            }
        }
        return response()->json(['message' => 'Data fetched and stored successfully.']);
    }

    public function resetMesin()
    {
        $url = 'https://developer.fingerspot.io/api/restart_device';
        $data = '{"trans_id":"1", "cloud_id":"' . env('ABSEN_CLOUD_ID');
        $authorization = 'Authorization: Bearer ' . env('ABSEN_TOKEN');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', $authorization]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function getAllPin()
    {
        $url = 'https://developer.fingerspot.io/api/get_all_pin';
        $data = '{"trans_id":"1", "cloud_id":"' . env('ABSEN_CLOUD_ID') . '", "start_date":"2024-07-03", "end_date":"2024-07-03"}';
        $authorization = 'Authorization: Bearer ' . env('ABSEN_TOKEN');

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', $authorization]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function getPin(Request $request)
    {
        $pin = $request->pin();

        $url = 'https://developer.fingerspot.io/api/get_userinfo';
        $data = '{"trans_id":"1", "cloud_id":"' . env('ABSEN_CLOUD_ID') . '","pin":"' . $pin . '"}';
        $authorization = 'Authorization: Bearer ' . env('ABSEN_TOKEN');

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', $authorization]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    static function fetchPagi(Request $request)
    {
        $date = $request->date;
        $getAbsenData = AbsensiController::getDataAbsensi($request);


        try {
            DB::beginTransaction();
            foreach ($getAbsenData as $entry) {
                $pin = $entry->pin;
                $scanDate = $entry->scan_date;
                $statusScan = $entry->status_scan;
                $hour = date('H', strtotime($scanDate));

                // Cek apakah ini adalah shift masuk atau keluar
                if ($statusScan == 0) {
                    // Masuk
                    $exist = Absensi::where('pin', $pin)->whereDate('tanggal_data', $date)->first();
                    if ($exist) {
                        $time = $exist->start_time < $scanDate ? $exist->start_time : $scanDate;
                        $exist->start_time = $time;
                        $exist->save();
                    } else {
                        Absensi::create([
                            'pin' => $pin,
                            'scan_date' => $scanDate,
                            'shift_type' => ($hour >= 7 && $hour < 19) ? 'PAGI' : 'MALAM',
                            'status_scan' => $statusScan,
                            'start_time' => $scanDate,
                            'tanggal_data' => $scanDate,
                        ]);
                    }
                } elseif ($statusScan == 1) {
                    // Keluar
                    $shift = Absensi::where('pin', $pin)
                        ->whereNull('end_time')
                        ->orderBy('start_time', 'desc')
                        ->first();
                    if ($shift) {
                        $shift->end_time = $scanDate;
                        $shift->status_scan = $statusScan;
                        $shift->save();
                    }
                }
            }
            // AbsensiController::handleMissingScans();

            DB::commit();
            return 'Data fetched Absensi '. $date;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function handleMissingScans(Request $request)
    {
        $perPage = $request->input('limit', 5000);
        $startDate = $request->input('start-date', Carbon::now());
        $endDate = $request->input('end-date', Carbon::now());

        try {

            $currentDate = Carbon::parse($startDate)->startOfDay();
            $toDate = Carbon::parse($endDate)->endOfDay()->subDays(1);
            DB::beginTransaction();
            while ($currentDate->lte($toDate)) {
                $datum = Absensi::whereDate('tanggal_data', $currentDate)
                    ->whereNull('end_time')
                    ->get();

                foreach ($datum as $data) {
                    if ($data->shift_type == 'PAGI') {
                        $data->end_time = $currentDate->copy()->setTime(18, 0, 0);
                    } elseif ($data->shift_type == 'MALAM') {
                        $data->end_time = $currentDate->copy()->addDay()->setTime(6, 0, 0);
                    }
                    $data->save();
                }

                // Move to the next day
                $currentDate->addDay();
            }

            DB::commit();
            $data = Absensi::with('pegawai')->whereBetween('tanggal_data', [$startDate, $endDate])
                ->latest()
                ->paginate($perPage);

            return $this->sendResponse($data, 'Data fetched');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    static function fetchDaily()
    {
        // $endDate = Carbon::now();
        // $startDate = Carbon::now()->subDays(1);
        
        $endDate = Carbon::createFromDate("2024-07-11");
        $startDate =  Carbon::createFromDate("2024-07-10");

        try {
            DB::beginTransaction();
            $existing = Absensi::whereBetween('tanggal_data', [$startDate, $endDate])->get();
            if ($existing) {
                foreach ($existing as $key => $value) {
                    $value->delete();
                }
            }


            return $data;

            foreach ($data as $entry) {
                $pin = $entry->pin;
                $scanDate = $entry->scan_date;
                $statusScan = $entry->status_scan;
                $hour = date('H', strtotime($scanDate));

                // Cek apakah ini adalah shift masuk atau keluar
                if ($statusScan == 0) {
                    // Masuk

                    Absensi::create([
                        'pin' => $pin,
                        'shift_type' => ($hour >= 7 && $hour < 19) ? 'PAGI' : 'MALAM',
                        'start_time' => $scanDate,
                    ]);
                } elseif ($statusScan == 1) {
                    // Keluar
                    $shift = Absensi::where('pin', $pin)
                        ->whereNull('end_time')
                        ->orderBy('start_time', 'desc')
                        ->first();
                    if ($shift) {
                        $shift->end_time = $scanDate;
                        $shift->save();
                    }
                }
            }
            DB::commit();

            return 'Data fetched Absensi ';




            // if (count($data) > 0) {
            //     foreach ($data as $record) {
            //         $existingRecord = Absensi::where('pin', $record->pin)
            //             ->whereDate('jam_masuk', '=', date('Y-m-d', strtotime($record->scan_date)))
            //             ->first();

            //         if ($existingRecord) {
            //             if ($record->status_scan == 1) {
            //                 $existingRecord->jam_pulang = $record->scan_date;
            //             }
            //             $existingRecord->save();
            //         } else {
            //             Absensi::create([
            //                 'pin' => $record->pin,
            //                 'scan_date' => $record->scan_date,
            //                 'jam_masuk' => $record->status_scan == 0 ? $record->scan_date : Carbon::createFromTime('10', '0', '0'),
            //                 'jam_pulang' => $record->status_scan == 1 ? $record->scan_date : Carbon::createFromTime('18', '0', '0'),
            //                 'verify' => $record->verify,
            //                 'status_scan' => $record->status_scan,
            //                 'tanggal_data' => $date,
            //                 'tanggal_tarik' => $date,
            //             ]);
            //         }
            //     }
            // } else {
            //     return 'No Data';
            // }
            // return 'Data fetched Absensi ' . $date;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getShiftType($scanDate)
    {
        $hour = date('H', strtotime($scanDate));
        return ($hour >= 7 && $hour < 19) ? 'PAGI' : 'MALAM';
    }

    public function getAbsenForGaji(Request $request)
    {
        $startDate = $request->input('start-date', Carbon::now());
        $endDate = $request->input('end-date', Carbon::now());

        $pegawai = Pegawai::all();

        foreach ($pegawai as $key => $value) {
            $absen = Absensi::where('pin', $value->pin)
                ->whereBetween('tanggal_data', [$startDate, $endDate])
                ->get();

            $totalJamKerja = 0;
            foreach ($absen as $key => $v) {
                $jamMasuk = Carbon::parse($v->start_time);
                $jamPulang = Carbon::parse($v->end_time);
                $jamKerja = $jamMasuk->diffInHours($jamPulang);
                $totalJamKerja += $jamKerja;
            }

            $value->total_jam_kerja = $totalJamKerja;
        }
        return $this->sendResponse($pegawai, 'Data fetched');
    }
}

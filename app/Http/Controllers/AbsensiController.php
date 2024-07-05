<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class AbsensiController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 5000);
        $startDate = $request->input('start-date', Carbon::now());
        $endDate = $request->input('end-date', Carbon::now());

        $data = Absensi::with('pegawai')->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->format('Y-m-d 00:00:00');
            $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->format('Y-m-d 23:59:59');
            return $query->whereBetween('tanggal_data', [$startDate, $endDate]);
        })
            // ->where('created_by', Auth::user()->id)
            ->orderBy('tanggal_data', 'desc')
            ->latest()
            ->paginate($perPage);

        return $this->sendResponse($data, 'Data fetched');
    }

    public function getDataAbsensi(Request $request)
    {
        $start_date = $request->start_date;
        $form_date = $request->from_date;

        $url = 'https://developer.fingerspot.io/api/get_attlog';
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

    public function fetchAndStoreData(Request $request)
    {
        $date = '2024-07-03';
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

    static function fetchDaily()
    {
        $date = Carbon::now();
        try {
            $existing = Absensi::whereDate('tanggal_data', $date)->get();
            if ($existing) {
                foreach ($existing as $key => $value) {
                    $value->delete();
                }
            }

            $url = 'https://developer.fingerspot.io/api/get_attlog';
            $data = '{"trans_id":"1", "cloud_id":"' . env('ABSEN_CLOUD_ID') . '", "start_date":"' . $date->format('Y-m-d') . '", "end_date":"' . $date->format('Y-m-d') . '"}';
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
            return 'Data fetched Absensi ' . $date;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}

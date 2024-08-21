<?php

namespace App\Http\Controllers\Api;

use App\Models\Gaji;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GajiController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 1000);
        $fromDate = Carbon::createFromFormat('Y-m-d', $request->input('start-date'))->format('Y-m-d 00:00:00');
        $toDate = Carbon::createFromFormat('Y-m-d', $request->input('end-date'))->format('Y-m-d 23:59:59');

    
            $data = Gaji::select(
                DB::raw('DATE(created_at) as tanggal'),
                DB::raw('SUM(gaji * jam_kerja + bonus + uang_makan - IFNULL(potongan, 0)) as total_gaji')
            )
            -> when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
            return $query->whereBetween('created_at', [$fromDate, $toDate]);
        })
            ->orderBy('tanggal', 'desc')

            ->groupBy(DB::raw('DATE(created_at)'))
              ->paginate($perPage);
           

        return $this->sendResponse($data, 'Data fetched');
    }

    public function store(Request $request)
    {
        $data = json_decode($request->getContent());
        $tanggal = Carbon::createFromFormat('Y-m-d', $data->created_at)->format('Y-m-d 00:00:00');
        try {
            DB::beginTransaction();
            $master[] = null;
            foreach ($data->detail as $key => $value) {
                if ($value->bayarkan == true) {
                    $master[] = Gaji::create([
                        'pegawai_id' => $value->id,
                        'jam_kerja' => $value->total_jam_kerja,
                        'gaji' => $value->gaji,
                        'uang_makan' => $value->uang_makan,
                        'bonus' => $value->bonus,
                        'potongan' => $value->potongan,
                        'start_date'=>$data->start_date,
                        'end_date'=>$data->end_date,
                        'created_by' => Auth::id(),
                        'created_at' => $tanggal,
                    ]);
                }
            }

            DB::commit();
            return $this->sendResponse($master, 'Data created');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Terjadi kesalahan', $e->getMessage(), 500);
        }
    }

    public function destroy($created_at)
    {
        DB::beginTransaction();
        try {
            $data = Gaji::whereDate('created_at', $created_at)->get();
            if ($data) {
                foreach ($data as $key => $value) {
                    $value->delete();
                }
                DB::commit();
                return $this->sendResponse($data, 'Data berhasil dihapus', 200);
            } else {
                return $this->sendError('', 'Data tidak ditemukan', 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Terjadi kesalahan', $e->getMessage(), 500);
        }
    }

    public function showTanggalGaji($id)
    {
        $data = Gaji::where('pegawai_id', $id)->select( DB::raw('DATE(created_at) as tanggal')) ->orderBy('tanggal', 'desc')->limit(10)->get();
         return $this->sendResponse($data, 'Data fetched');
    }
}

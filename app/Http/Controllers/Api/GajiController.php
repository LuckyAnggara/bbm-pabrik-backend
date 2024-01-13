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
    $fromDate = Carbon::createFromFormat('Y-m-d',  $request->input('start-date'))->format('Y-m-d 00:00:00');
    $toDate = Carbon::createFromFormat('Y-m-d', $request->input('end-date'))->format('Y-m-d 23:59:59');

        $data = Gaji::when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
                return $query
                    ->whereBetween('created_at', [$fromDate, $toDate]);
            })
              ->select(
                'created_at',
                DB::raw('SUM(gaji) as total_gaji'),
                DB::raw('SUM(uang_makan) as total_uang_makan'),
                DB::raw('SUM(bonus) as total_bonus')
            )
            ->orderBy('created_at', 'desc')
            ->groupBy('created_at') // Group by kolom yang dipilih
            ->latest()
            ->paginate($perPage);

        return $this->sendResponse($data, 'Data fetched');
    }

    public function store(Request $request)
    {
        $data = json_decode($request->getContent());
        $tanggal = Carbon::createFromFormat('Y-m-d',  $data->created_at)->format('Y-m-d 00:00:00');
        try {
            DB::beginTransaction();
            foreach ($data->detail as $key => $value) {
                if($value->bayarkan == true){
                     $master[] = Gaji::create([
                'pegawai_id' => $value->id,
                'gaji' => $value->gaji,
                'uang_makan' => $value->uang_makan,
                'bonus' => $value->bonus,
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
            $data = Gaji::whereDate('created_at',$created_at)->get();
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
}

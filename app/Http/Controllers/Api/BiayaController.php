<?php

namespace App\Http\Controllers\Api;

use App\Models\Biaya;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BiayaController extends BaseController
{
    public function index(Request $request)
    {


        $perPage = $request->input('limit', 1000);
        $name = $request->input('query');
                  $fromDate = Carbon::createFromFormat('Y-m-d',  $request->input('start-date'))->format('Y-m-d 00:00:00');
                $toDate = Carbon::createFromFormat('Y-m-d', $request->input('end-date'))->format('Y-m-d 23:59:59');

        $data = Biaya::when($name, function ($query, $name) {
            return $query
                ->where('nama', 'like', '%' . $name . '%')
                ->orWhere('kategori', 'like', '%' . $name . '%')
                ->orWhere('jumlah', 'like', '%' . $name . '%');
        })
            ->when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {



                return $query
                    ->whereBetween('tanggal_transaksi', [$fromDate, $toDate]);
            })



            ->orderBy('created_at', 'desc')
            ->latest()
            ->paginate($perPage);

        return $this->sendResponse($data, 'Data fetched');
    }

    public function store(Request $request)
    {
        $data = json_decode($request->getContent());

        try {
            DB::beginTransaction();
            $master = Biaya::create([
                'nama' => $data->nama,
                'kategori' => $data->kategori,
                'tanggal_transaksi' => $data->tanggal_transaksi,
                'jumlah' => $data->jumlah,
                'created_by' => Auth::id(),
            ]);

            DB::commit();
            return $this->sendResponse($master, 'Data created');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Terjadi kesalahan', $e->getMessage(), 500);
        }
    }


    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = Biaya::find($id);
            if ($data) {
                $data->delete();
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

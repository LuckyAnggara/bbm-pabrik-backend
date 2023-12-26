<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\DetailPenjualan;
use App\Models\Penjualan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenjualanController extends BaseController
{
     public function index(Request $request)
    {
        $perPage = $request->input('limit', 1000);
        $name = $request->input('query');
        $tahun = $request->input('tahun');  
        $startDate = $request->input('start-date');
        $endDate = $request->input('end-date');


        $data = Penjualan::with('group')->when($tahun, function ($query, $tahun) {
            return $query->whereYear('created_at', $tahun);
        })
           ->when($name, function ($query, $name) {
            return $query->where('nomor_faktur', 'like', '%' . $name . '%')
                ->orWhere('nama_supplier', 'like', '%' . $name . '%')
                ->orWhere('total', 'like', '%' . $name . '%');
        })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $startDate = Carbon::createFromFormat('d M Y', $startDate)->format('Y-m-d 00:00:00');
                $endDate = Carbon::createFromFormat('d M Y', $endDate)->format('Y-m-d 23:59:59');
                return $query->whereBetween('start_at', [$startDate, $endDate]);
            })
             ->where('created_by', Auth::user()->id)
            ->orderBy('created_at', 'asc')
            ->latest()
            ->paginate($perPage);

        return $this->sendResponse($data, 'Data fetched');

    }

    function store(Request $request)
    {
       
    }

    public function show($uuid)
    {
       
    }

    public function update(Request $request, $id)
    {
       
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $penjualan = Penjualan::find($id);
            if ($penjualan) {
                $saleDetails = DetailPenjualan::where('penjualan_id', $penjualan->id)->get();
                // foreach ($saleDetails as $key => $detail) {
                //     $detail->id = $detail->item_id;
                //     $detail->penjualan = false;
                //     $user = new stdClass();
                //     $user->branch_id = $penjualan->branch_id;
                //     $user->id = $penjualan->created_by;
                //     $notes = 'Hapus Transaksi #' . $penjualan->faktur;
                //     MutationController::create($detail, $user, $notes, '');
                //     // $detail->delete();
                // }
                $penjualan->delete();
                DB::commit();
                return $this->sendResponse($saleDetails, 'Data penjualan berhasil dihapus', 200);
            } else {
                return $this->sendError('', 'Data penjualan tidak ditemukan', 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Terjadi kesalahan', $e->getMessage(), 500);
        }
    }
}

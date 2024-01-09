<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\DetailPembelian;
use App\Models\Pembelian;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembelianController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 1000);
        $name = $request->input('query');
        $tahun = $request->input('tahun');
        $startDate = $request->input('start-date');
        $endDate = $request->input('end-date');

        $data = Pembelian::with('detail')
            ->when($tahun, function ($query, $tahun) {
                return $query->whereYear('created_at', $tahun);
            })
            ->when($name, function ($query, $name) {
                return $query
                    ->where('nomor_faktur', 'like', '%' . $name . '%')
                    ->orWhere('nama_supplier', 'like', '%' . $name . '%')
                    ->orWhere('total', 'like', '%' . $name . '%');
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->format('Y-m-d 00:00:00');
                $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->format('Y-m-d 23:59:59');
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            // ->where('created_by', Auth::user()->id)
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
            $master = Pembelian::create([
                'nomor_faktur' => $data->nomor_faktur,
                'nama_supplier' => $data->nama_supplier,
                'sub_total' => $data->total,
                'pajak' => $data->pajak,
                'diskon' => $data->diskon,
                'ongkir' => $data->ongkir,
                'total' => $data->total - $data->diskon + $data->ongkir + $data->pajak,
                'created_at' => $data->tanggal_transaksi ?? Carbon::now(),
                'created_by' => Auth::id(),
            ]);

            if ($master) {
                foreach ($data->cart as $key => $value) {
                    DetailPembelian::create([
                        'pembelian_id' => $master->id,
                        'item_id' => $value->id,
                        'jumlah' => $value->jumlah,
                        'harga' => $value->harga,
                    ]);

                    $item = MutationController::mutationItem($value->id, $value->jumlah, 'DEBIT',  'Pembelian Item : #' . $master->nomor_faktur, 1);
                }
            }
             DB::commit();
            return $this->sendResponse($master, 'Data created');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Terjadi kesalahan', $e->getMessage(), 500);
        }
    }


    public function show($id)
    {
        $result = Pembelian::where('id', $id)
            ->with(['detail.item','user'])
            ->first();
        if ($result) {
            return $this->sendResponse($result, 'Data fetched');
        }
        return $this->sendError('Data not found');
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = Pembelian::find($id);
            if ($data) {
                $detail = DetailPembelian::where('pembelian_id', $id)->get();
                foreach ($detail as $key => $value) {
                    $value->delete();
                    $item = MutationController::mutationItem($value->item_id, $value->jumlah, 'KREDIT',  'Hapus Pembelian Item : #' . $data->nomor_faktur, 1);
                }
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

        public function generateFaktur()
    {
        return Pembelian::generateFakturNumber();
    }
}

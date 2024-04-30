<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\DetailPenjualan;
use App\Models\Penjualan;
use App\Models\Pin;
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

        $data = Penjualan::with('detail','pelanggan')
            ->when($tahun, function ($query, $tahun) {
                return $query->whereYear('created_at', $tahun);
            })
            ->when($name, function ($query, $name) {
                return $query
                    ->where('nomor_faktur', 'like', '%' . $name . '%')
                    ->orWhere('nama_pelanggan', 'like', '%' . $name . '%')
                    ->orWhere('total', 'like', '%' . $name . '%');
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->format('Y-m-d 00:00:00');
                $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->format('Y-m-d 23:59:59');
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            // ->where('created_by', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->latest()
            ->paginate($perPage);

        return $this->sendResponse($data, 'Data fetched');
    }

    public function store(Request $request)
    {
        $data = json_decode($request->getContent());

        try {
            DB::beginTransaction();
            $master = Penjualan::create([
                'nomor_faktur' => $data->nomor_faktur,
                'pelanggan_id' => $data->pelanggan_tetap == true ? $data->pelanggan->id : null,
                'nama_pelanggan' => $data->pelanggan_tetap == false ? $data->nama_pelanggan : null,
                'alamat' => $data->pelanggan_tetap == false ? $data->alamat : null,
                'nomor_telepon' => $data->pelanggan_tetap == false ? $data->nomor_telepon : null,
                'sub_total' => $data->total,
                'pajak' => $data->pajak,
                'diskon' => $data->diskon,
                'ongkir' => $data->ongkir,
                'status' => $data->status ?? 'LUNAS',
                'total' => $data->total - $data->diskon + $data->ongkir + $data->pajak,
                'created_at' => $data->tanggal_transaksi ?? Carbon::now(),
                'created_by' => Auth::id(),
            ]);

            if ($master) {
                foreach ($data->cart as $key => $value) {
                    DetailPenjualan::create([
                        'penjualan_id' => $master->id,
                        'item_id' => $value->id,
                        'jumlah' => $value->jumlah,
                        'harga' => $value->harga,
                    ]);

                    $item = MutationController::mutationItem($value->id, $value->jumlah, 'KREDIT',  'Penjualan Item : #' . $master->nomor_faktur, 1);
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
        $result = Penjualan::where('id', $id)
            ->with(['detail.item.unit','user','pelanggan'])
            ->first();
        if ($result) {
            return $this->sendResponse($result, 'Data fetched');
        }
        return $this->sendError('Data not found');
    }

    public function destroy($id, Request $request)
    {
        DB::beginTransaction();
        $data = json_decode($request->getContent());

        
        try {
            $data = Penjualan::find($id);
            if ($data) {
                $detail = DetailPenjualan::where('penjualan_id', $id)->get();
                foreach ($detail as $key => $value) {
                    $value->delete();
                    if($data->retur == 1){
                    $item = MutationController::mutationItem($value->item_id, $value->jumlah, 'DEBIT',  'Hapus Penjualan Item : #' . $data->nomor_faktur, 1);
                    }
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
    
    // HANYA UPDATE STATUS
    public function verifikasi(Request $request)
    {
        $data = json_decode($request->getContent());
        $auth = Pin::where('pin', $data->pin)->first();
        
        if($auth){
try {
            DB::beginTransaction();
            $penjualan = Penjualan::findOrFail($data->id);
            if($penjualan){
                $penjualan->status = 'TERVERIFIKASI';
                $penjualan->save();
                $detail = DetailPenjualan::where('penjualan_id', $data->id)->get();
                foreach ($detail as $key => $value) {
                   $item = MutationController::mutationItem($value->id, $value->jumlah, 'DEBIT',  'Penjualan Item : #' . $penjualan->nomor_faktur, 1, $auth->pegawai_id);
                }
            }

             DB::commit();
            return $this->sendResponse($data, 'Penjualan terverifikasi', 200);
         }catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Terjadi kesalahan', $e->getMessage(), 500);
        }
        }
         return $this->sendError('Terjadi kesalahan', 'PIN Salah', 500);
    }

    public function showPenjualan($id)
    {
        $result = Penjualan::where('id', $id)
            ->with(['detail.item.unit','user','pelanggan'])
            ->first();
        if ($result) {
            return $this->sendResponse($result, 'Data fetched');
        }
        return $this->sendError('Data not found');
    }

    public function generateFaktur()
    {
        return Penjualan::generateFakturNumber();
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PegawaiController extends BaseController
{
     public function index(Request $request)
    {
        $perPage = $request->input('limit', 1000);
        $name = $request->input('query');

        $data = Pegawai::when($name, function ($query, $name) {
                return $query
                    ->where('name', 'like', '%' . $name . '%')
                    ->orWhere('jabatan', 'like', '%' . $name . '%')
                    ->orWhere('gaji', 'like', '%' . $name . '%');
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
            $master = Pegawai::create([
                'name' => $data->name,
                'jabatan' => $data->jabatan,
                'gaji' => $data->gaji,
                'uang_makan' => $data->uang_makan,
                'bonus' => $data->bonus,
                'created_by' => Auth::id(),
            ]);

             DB::commit();
            return $this->sendResponse($master, 'Data created');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Terjadi kesalahan', $e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        $data = json_decode($request->getContent());
        $result = Pegawai::where('id', $id)->first();
        try {
            DB::beginTransaction();

                $result->name  = $data->name;
                $result->jabatan  = $data->jabatan;
                $result->gaji  = $data->gaji;
                $result->uang_makan  = $data->uang_makan;
                $result->bonus  = $data->bonus;
                $result->save();

             DB::commit();
            return $this->sendResponse($result, 'Data updated');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Terjadi kesalahan', $e->getMessage(), 500);
        }
    }


    public function show($id)
    {
        $result = Pegawai::where('id', $id)->first();
        if ($result) {
            return $this->sendResponse($result, 'Data fetched');
        }
        return $this->sendError('Data not found');
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = Pegawai::find($id);
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

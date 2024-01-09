<?php

namespace App\Http\Controllers\Api;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PelangganController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 1000);
        $name = $request->input('query');

        $data = Pelanggan::when($name, function ($query, $name) {
                return $query
                    ->where('name', 'like', '%' . $name . '%')
                    ->orWhere('alamat', 'like', '%' . $name . '%')
                    ->orWhere('nomor_telepon', 'like', '%' . $name . '%');
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
            $master = Pelanggan::create([
                'name' => $data->name,
                'alamat' => $data->alamat,
                'nomor_telepon' => $data->nomor_telepon,
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
        $result = Pelanggan::where('id', $id)->first();
        try {
            DB::beginTransaction();

                $result->name  = $data->name;
                $result->alamat  = $data->alamat;
                $result->nomor_telepon  = $data->nomor_telepon;
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
        $result = Pelanggan::where('id', $id)->first();
        if ($result) {
            return $this->sendResponse($result, 'Data fetched');
        }
        return $this->sendError('Data not found');
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = Pelanggan::find($id);
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

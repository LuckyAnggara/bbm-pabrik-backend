<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use App\Models\Mutation;
use Illuminate\Http\Request;
use App\Http\Resources\ItemResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ItemController extends BaseController
{

    public function index(Request $request)
    {
        $limit = $request->input('limit', 5);
        $name = $request->input('name');
        $type = $request->input('type');
        $balance_nol = $request->input('balance_nol');
        $warehouseId = $request->input('warehouse_id');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $item = Item::with(['type', 'unit', 'warehouse', 'user']);
        if ($type) {
            $item->where('type_id', $type);
        }
        if ($name) {
            $item->where('name', 'like', '%' . $name . '%');
        }
        if ($warehouseId) {
            $item->where('warehouse_id', $warehouseId);
        }
        if($balance_nol){
            $item->whereNot('balance', 0);
        }       
        $result = $item->latest()->paginate($limit);
        
        if (!is_null($fromDate) && !is_null($toDate)) {
            $fromDate = Carbon::createFromFormat('Y-m-d', $fromDate)->startOfDay();
            $toDate = Carbon::createFromFormat('Y-m-d', $toDate)->endOfDay();
            $result->each(function($value) use ( $fromDate, $toDate){
                $value->balance = 0;
                $mutation = Mutation::where('item_id', $value->id)->whereBetween('created_at', [$fromDate, $toDate])->orderBy('id', 'desc')->first();
                if ($mutation) {
                    return $value->balance = $mutation->balance;
                }
            });
        }

        



        return $this->sendResponse($result, 'Data fetched');
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $item = Item::create([
            'name' => $request->name,
            'type_id' => $request->type_id,
            'unit_id' => $request->unit_id,
            'warehouse_id' => $request->warehouse_id,
            'created_by' =>  Auth::id(),
        ]);

        if ($item) {
            //membuat saldo awal
            $mutation = new Mutation;
            $mutation->item_id = $item->id;
            // $mutation->warehouse_id = $item->warehouse_id;
            $mutation->debit = 0;
            $mutation->kredit = 0;
            $mutation->balance = 0;
            $mutation->notes = 'saldo awal';
            $mutation->created_by = Auth::id();
            $mutation->save();
        }
        return $this->sendResponse((new ItemResource($item)), 'Data created');
    }

    public function show($id)
    {
        $item = Item::with(['type', 'unit', 'warehouse', 'user',])->where('id', $id)->first();
        if (is_null($item)) {
            return $this->sendError('Data does not exist.');
        }
        return $this->sendResponse(new ItemResource($item), 'Data fetched');
    }

    public function update(Request $request, $id)
    {
        $data = json_decode($request->getContent());
        $result = Item::where('id', $id)->first();

      
    

           try {
            DB::beginTransaction();

                     $result->name  = $data->name;
         $result->type_id  = $data->type_id;
        $result->unit_id  = $data->unit_id;
        $result->save();

             DB::commit();
            return $this->sendResponse($result, 'Data updated');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Terjadi kesalahan', $e->getMessage(), 500);
        }




        return $this->sendResponse($result, 'Data updated');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        // if ($item) {
        //     Mutation::where('item_id', $item->id)->delete();
        // }
        return $this->sendResponse([], 'Data deleted');
    }
}

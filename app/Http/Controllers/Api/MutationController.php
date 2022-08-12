<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use App\Models\Mutation;
use Illuminate\Http\Request;
use App\Http\Resources\ItemResource;
use App\Http\Resources\MutationResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\WarehouseResource;
use Database\Factories\WarehouseFactory;
use Illuminate\Support\Facades\DB;

class MutationController extends BaseController
{
    public function index(Request $request)
    {
        $warehouseId = $request->input('warehouse_id');
        $limit = $request->input('limit', 5);
        $name = $request->input('name');

        $item = Item::with(['type', 'unit', 'warehouse', 'user', 'mutation']);

        if ($name) {
            $item->where('name', 'like', '%' . $name . '%');
        }

        return $this->sendResponse($item->latest()->paginate($limit), 'Data fetched');
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'item_id' => 'required',
            'created_by' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $item = Mutation::create([
            'item_id' => $request->item_id,
            'debit' => $request->debit,
            'kredit' => $request->kredit,
            'warehouse_id' => $request->warehouse_id,
            'created_by' => $request->created_by,
        ]);
        return $this->sendResponse(new MutationResource($item), 'Data created');
    }

    public function show($id, Request $request)
    {
        $warehouseId = $request->input('warehouse_id');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');


        $item = Item::with('type', 'unit', 'warehouse', 'user')->with('mutation', function ($query) use ($warehouseId, $fromDate, $toDate) {
            DB::statement(DB::raw('set @balance=0'));
            $query->selectRaw('warehouse_id, item_id, notes, debit, kredit, created_at ,(@balance := @balance + (debit - kredit)) as balance');
            if (!is_null($warehouseId)) {
                $query->where('warehouse_id', '=', $warehouseId);
                if (!is_null($fromDate) && !is_null($toDate)) {
                    $query->whereBetween('created_at', [$fromDate, $toDate]);
                }
                // $query->with('user');
                // $query->saldo_akumulasi();
            } else {
                if (!is_null($fromDate) && !is_null($toDate)) {
                    $query->whereBetween('created_at', [$fromDate, $toDate]);
                }
            }
        })->where('id', $id)->first();

        return $this->sendResponse($item, 'Data fetched');
    }

    public function update(Request $request, Mutation $mutation)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'item_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $mutation->item_id = $input['item_id'];
        $mutation->warehouse_id = $input['warehouse_id'];
        $mutation->debit = $input['debit'];
        $mutation->kredit = $input['kredit'];
        $mutation->save();

        return $this->sendResponse(new WarehouseResource($mutation), 'Data updated');
    }

    public function destroy(Mutation $mutation)
    {
        $mutation->delete();
        return $this->sendResponse([], 'Data deleted');
    }
}

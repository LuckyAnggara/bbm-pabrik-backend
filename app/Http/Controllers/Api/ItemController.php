<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Resources\ItemResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;
use App\Models\Mutation;
use Illuminate\Support\Facades\DB;

class ItemController extends BaseController
{

    public function index(Request $request)
    {
        $limit = $request->input('limit', 5);
        $name = $request->input('name');
        $warehouseId = $request->input('warehouse_id');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $item = Item::with(['type', 'unit', 'warehouse', 'user'])->with('mutation', function ($query) use ($warehouseId, $fromDate, $toDate) {
            if (!is_null($fromDate) && !is_null($toDate)) {
                $query->whereBetween('created_at', [$fromDate, $toDate]);
            }
            $query->saldo = 0;
        });

        if ($name) {
            $item->where('name', 'like', '%' . $name . '%');
        }
        if ($warehouseId) {
            $item->where('warehouse_id', $warehouseId);
        }
        $data = $item->latest()->paginate();

        foreach ($data as $key => $value) {
            $value->saldo = 1000;
        }

        return $this->sendResponse($data, 'Data fetched');
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
        $item = Item::create($input);
        if ($item) {
            //membuat saldo awal
            $mutation = new Mutation;
            $mutation->item_id = $item->id;
            $mutation->warehouse_id = $item->warehouse_id;
            $mutation->debit = 0;
            $mutation->kredit = 0;
            $mutation->notes = 'saldo awal';
            $mutation->created_by = 1;
            $mutation->save();
        }
        return $this->sendResponse(new ItemResource($item), 'Data created');
    }

    public function show($id)
    {
        $item = Item::with(['type', 'unit', 'warehouse', 'user',])->where('id', $id)->first();
        if (is_null($item)) {
            return $this->sendError('Data does not exist.');
        }
        return $this->sendResponse(new ItemResource($item), 'Data fetched');
    }

    public function update(Request $request, Item $item)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        $item->name = $input['name'];
        $item->save();

        return $this->sendResponse(new ItemResource($item), 'Data updated');
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

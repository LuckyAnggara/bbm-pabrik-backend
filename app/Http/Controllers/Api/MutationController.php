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
use Illuminate\Support\Collection;
use App\Models\MasterExitItem;
use App\Models\MasterIncomingItem;
use Database\Factories\WarehouseFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MutationController extends BaseController
{
    //MASTER MUTASI
    public function indexMaster(Request $request)
    {
        $limit = $request->input('limit', 5);
        $typeData = $request->input('type-data', 'debit');
        $name = $request->input('name');

        $incoming = MasterIncomingItem::with('user', 'detail.item.unit');

        if ($name) {
            $incoming->where('mutation_code', 'like', '%' . $name . '%');
            $incoming->orWhere('created_at', 'like', '%' . $name . '%');
            $incoming->orWhere('notes', 'like', '%' . $name . '%');
            $incoming->orWhere('type', 'like', '%' . $name . '%');
        }

        $exit = MasterExitItem::with('user', 'detail.item.unit');

        if ($name) {
            $exit->where('mutation_code', 'like', '%' . $name . '%');
            $exit->orWhere('created_at', 'like', '%' . $name . '%');
            $exit->orWhere('notes', 'like', '%' . $name . '%');
            $exit->orWhere('type', 'like', '%' . $name . '%');
        }

        // $data = $exit->union($incoming);

        if ($typeData == 'debit') {
            $data = $incoming->latest()->paginate($limit);
            return $this->sendResponse($data, 'Data fetched');
        }
        $data = $exit->latest()->paginate($limit);
        return $this->sendResponse($data, 'Data fetched');
    }

    public function showMaster($id, Request $request)
    {
        $type = $request->input('type');
        if ($type == 'debit') {
            $data = MasterIncomingItem::with('user', 'detail.item')->find($id);
        } else {
            $data = MasterExitItem::with('user', 'detail.item')->find($id);
        }
        if ($data) {
            return $this->sendResponse($data, 'Data fetched');
        }
        return $this->sendResponse(null, 'Data not Found');
    }

    //FOR SATUAN
    public function index(Request $request)
    {
        // $warehouseId = $request->input('warehouse_id');
        $limit = $request->input('limit', 5);
        $name = $request->input('name');

        $item = Item::with(['type', 'unit', 'user', 'mutation']);

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
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $item = Mutation::create([
            'item_id' => $request->item_id,
            'debit' => $request->debit,
            'kredit' => $request->kredit,
            // 'warehouse_id' => $request->warehouse_id,
            'created_by' => Auth::id(),
        ]);
        return $this->sendResponse(new MutationResource($item), 'Data created');
    }

    public function show($id, Request $request)
    {
        // $warehouseId = $request->input('warehouse_id');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $item = Item::with('type', 'unit', 'user')->with('mutation', function ($query) use ($fromDate, $toDate) {
            DB::statement(DB::raw('set @balance=0'));
            $query->selectRaw('id,item_id, notes, debit, kredit, created_at ,(@balance := @balance + (debit - kredit)) as balance');
            // if (!is_null($warehouseId)) {
            // $query->where('warehouse_id', '=', $warehouseId);
            if (!is_null($fromDate) && !is_null($toDate)) {
                $query->whereBetween('created_at', [$fromDate, $toDate]);
            }
            // } else {
            //     if (!is_null($fromDate) && !is_null($toDate)) {
            //         $query->whereBetween('created_at', [$fromDate, $toDate]);
            //     }
            // }
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
        // $mutation->warehouse_id = $input['warehouse_id'];
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

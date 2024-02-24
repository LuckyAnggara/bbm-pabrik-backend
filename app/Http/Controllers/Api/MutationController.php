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
use Carbon\Carbon;
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


        if ($typeData == 'debit') {
            $data = MasterIncomingItem::with('user');
        } else {
            $data = MasterExitItem::with('user');
        }
            $data->when($name, function ($query, $name) {
                return $query
                    ->where('notes', 'like', '%' . $name . '%')
                    ->orWhere('mutation_code', 'like', '%' . $name . '%');
            });

     

        return $this->sendResponse($data->latest()->paginate($limit), 'Data fetched');
    }

    public function showMaster($id, Request $request)
    {
        $type = $request->input('type-data');
        if ($type == 'debit') {
            $data = MasterIncomingItem::with('user', 'detail.item.type', 'detail.item.unit')->find($id);
        } else {
            $data = MasterExitItem::with('user', 'detail.item.type', 'detail.item.unit')->find($id);
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
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $limit = $request->input('limit', 10);
        $name = $request->input('name');
        
        $mutation = Mutation::where('item_id', $id)
                    ->when($name, function ($query, $name) {
                return $query
                    ->where('notes', 'like', '%' . $name . '%');
            });
        if ($fromDate && $toDate) {
            $fromDate = Carbon::createFromFormat('Y-m-d', $fromDate)->startOfDay();
            $toDate = Carbon::createFromFormat('Y-m-d', $toDate)->endOfDay();
            $mutation->whereBetween('created_at', [$fromDate, $toDate])->orderBy('id', 'desc');
        }
        $mutation->orderBy('id', 'desc');


        return $this->sendResponse($mutation->latest()->paginate($limit), 'Data fetched');
    }

    // public function update(Request $request, Mutation $mutation)
    // {
    //     $input = $request->all();
    //     $validator = Validator::make($input, [
    //         'item_id' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError($validator->errors());
    //     }

    //     $mutation->item_id = $input['item_id'];
    //     // $mutation->warehouse_id = $input['warehouse_id'];
    //     $mutation->debit = $input['debit'];
    //     $mutation->kredit = $input['kredit'];
    //     $mutation->save();

    //     return $this->sendResponse(new WarehouseResource($mutation), 'Data updated');
    // }

    // public function destroy(Mutation $mutation)
    // {
    //     $mutation->delete();
    //     return $this->sendResponse([], 'Data deleted');
    // }

    public static function mutationItem($id, $qty, $type, $note, $warehouse, $authId = null)
    {
        
        $item = Item::find($id);
        $mutation = Mutation::create([
            'item_id' => $id,
            'debit' => $type == 'DEBIT' ? $qty : 0,
            'kredit' =>  $type == 'KREDIT' ? $qty : 0,
            'balance' => $type == 'DEBIT' ? ($item->balance + $qty) : ($item->balance - $qty),
            'notes' => $note,
            // 'warehouse_id' => $warehouse,
            'created_by' => $authId == null ? Auth::id() : $authId,
        ]);

        if ($mutation) {
            $item->balance = $type == 'DEBIT' ? ($item->balance + $qty) : ($item->balance - $qty);
            $item->save();

            return true;
        }
    }
}

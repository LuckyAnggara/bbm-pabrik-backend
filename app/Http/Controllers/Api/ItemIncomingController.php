<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\MasterItemIncomingResource;
use App\Models\DetailIncomingItem;
use App\Models\MasterIncomingItem;
use App\Models\Mutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ItemIncomingController extends BaseController
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 5);
        $name = $request->input('name');

        $itemUnit = MasterIncomingItem::with(['user']);
        if ($name) {
            $itemUnit->where('name', 'like', '%' . $name . '%');
        }

        return $this->sendResponse($itemUnit->latest()->paginate($limit), 'Data fetched');
    }

    public function store(Request $request)
    {
        $data = $request->data;
        $validator = Validator::make($data, [
            'tanggal' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        $data = MasterIncomingItem::create([
            'data_date' => $data->tanggal,
            'notes' => $data->notes,
            'created_by' =>  Auth::id(),
        ]);

        if ($data) {
            foreach ($request->detail as $key => $detail) {
                $detail = DetailIncomingItem::creat([
                    'master_id' => $data->id,
                    'item_id' => $detail->item_id,
                    'qty' => $detail->qty,
                ]);

                $item = Mutation::create([
                    'item_id' => $detail->item_id,
                    'debit' => $detail->qty,
                    'kredit' => 0,
                    'warehouse_id' => 1,
                    'created_by' =>  Auth::id(),
                ]);
            }
        }
        return $this->sendResponse(new MasterItemIncomingResource($data), 'Data created');
    }
}

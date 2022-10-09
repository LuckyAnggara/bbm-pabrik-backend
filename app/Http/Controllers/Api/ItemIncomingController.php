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
use Carbon\Carbon;

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

        $data = MasterIncomingItem::create([
            'mutation_code'=> Carbon::now()->timestamp,
            'data_date' => $data['tanggal'],
            'notes' => $data['notes'],
            'created_by' =>  Auth::id(),
        ]);

        if ($data) {
            foreach ($request->detail as $key => $detail) {
                if (isset($detail['qty'])) {
                    $dd = DetailIncomingItem::create([
                        'master_id' => $data->id,
                        'item_id' => $detail['id'],
                        'qty' => !isset($detail['qty']) ? 0 : $detail['qty'],
                    ]);

                    $item = Mutation::create([
                        'item_id' => $dd->item_id,
                        'debit' => $dd->qty,
                        'kredit' => 0,
                        'warehouse_id' => 1,
                        'created_by' =>  Auth::id(),
                        'notes' => $data->notes. ' - ' . '#'. $data->mutation_code
                    ]);
                }
            }
        }
        return $this->sendResponse(new MasterItemIncomingResource($data), 'Data created');
    }
}

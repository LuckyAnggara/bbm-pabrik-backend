<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\MasterExitResource;
use App\Models\DetailExitItem;
use App\Models\MasterExitItem;
use App\Models\Mutation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemExitController extends BaseController
{
    public function index(Request $request)
    {
    }

    public function store(Request $request)
    {
        $data = $request->data;

        $data = MasterExitItem::create([
            'mutation_code' => Carbon::now()->timestamp,
            'data_date' => $data['tanggal'],
            'notes' => $data['notes'],
            'created_by' =>  Auth::id(),
        ]);

        if ($data) {
            foreach ($request->detail as $key => $detail) {
                if (isset($detail['qty'])) {
                    $dd = DetailExitItem::create([
                        'master_id' => $data->id,
                        'item_id' => $detail['id'],
                        'qty' => !isset($detail['qty']) ? 0 : $detail['qty'],
                    ]);

                    $item = Mutation::create([
                        'item_id' => $dd->item_id,
                        'debit' => 0,
                        'kredit' => $dd->qty,
                        'warehouse_id' => 1,
                        'created_by' =>  Auth::id(),
                        'notes' => $data->notes . ' - ' . '#' . $data->mutation_code
                    ]);
                }
            }
        }
        return $this->sendResponse(new MasterExitResource($data), 'Data created');
    }
}

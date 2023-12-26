<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Api\MutationController;
use App\Http\Resources\MasterExitResource;
use App\Models\DetailExitItem;
use App\Models\MasterExitItem;
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
                    $item = MutationController::mutationItem($dd->item_id, $dd->qty, 'KREDIT', $data->notes . ' - ' . '#' . $data->mutation_code, 1);
                }
            }
        }
        return $this->sendResponse(new MasterExitResource($data), 'Data created');
    }

    public function destroy($id)
    {
        $masterExitItem = MasterExitItem::findOrFail($id);
        if($masterExitItem){
            $detailItem = DetailExitItem::where('master_id', $masterExitItem->id)->get();
            if($detailItem){
                foreach ($detailItem as $key => $detail) {
                    MutationController::mutationItem($detail->item_id, $detail->qty, 'DEBIT','Penghapusan Transaksi - #'.$masterExitItem->mutation_code,1);
                }
            }
            $masterExitItem->delete();

            return $this->sendResponse([], 'Data deleted');
        }
        return $this->sendError([], 'Data Not Found');
    }
}

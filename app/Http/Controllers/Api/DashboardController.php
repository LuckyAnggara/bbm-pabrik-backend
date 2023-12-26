<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ProductionOrder;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{

    public function itemCount(Request $request)
    {
        $data['bahan_baku'] = Item::where('type_id', 1)->count();
        $data['barang_jadi'] = Item::where('type_id', 2)->count();
        $data['barang_lainnya'] = Item::where('type_id', 3)->count();
        return $this->sendResponse($data, 'Data fetched');
    }

    public function productionCount(Request $request)
    {
        $data['on_progress'] = ProductionOrder::where('status', '==', 'NEW ORDER')
            ->orWhere('status', 'WORK IN PROGRESS')
            ->count();
        $data['done'] = ProductionOrder::where('status', 'DONE PRODUCTION')
            ->orWhere('status', 'WAREHOUSE')
            ->orWhere('status', 'SHIPPING')
            ->orWhere('status', 'RETUR')
            ->orWhere('status', 'RECEIVE')
            ->count();

        return $this->sendResponse($data, 'Data fetched');
    }

    public function shippingCount(Request $request)
    {
        $data['on_progress'] = ProductionOrder::where('status', '==', 'SHIPPING')
            ->orWhere('status', 'RETUR')
            ->count();
        $data['done'] = ProductionOrder::where('status', 'RECEIVE')->count();

        return $this->sendResponse($data, 'Data fetched');
    }
}

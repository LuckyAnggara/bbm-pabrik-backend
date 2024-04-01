<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\ProductionOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{

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

    public function bisnis(Request $request)
    {
        $dataSales = $request->input('data-sales');
        $dataPurchasing = $request->input('data-purchasing');

        if ($dataSales == 1) {
            $data['sales'] = Penjualan::whereDate('created_at', Carbon::now())->sum('total');
        } else if ($dataSales == 2) {
            $data['sales'] = Penjualan::whereMonth('created_at', Carbon::now())->sum('total');
        } else if ($dataSales == 3) {
            $data['sales'] = Penjualan::whereYear('created_at', Carbon::now())->sum('total');
        }

        if ($dataPurchasing == 1) {
            $data['purchasing'] = Pembelian::whereDate('created_at', Carbon::now())->sum('total');
        } else if ($dataPurchasing == 2) {
            $data['purchasing'] = Pembelian::whereMonth('created_at', Carbon::now())->sum('total');
        } else if ($dataPurchasing == 3) {
            $data['purchasing'] = Pembelian::whereYear('created_at', Carbon::now())->sum('total');
        }



        // $data['purchasing'] = Pembelian::when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
        //     $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->format('Y-m-d 00:00:00');
        //     $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->format('Y-m-d 23:59:59');
        //     return $query->whereBetween('created_at', [$startDate, $endDate]);
        // })->sum('total');

        return $this->sendResponse($data, 'Data fetched');
    }
}

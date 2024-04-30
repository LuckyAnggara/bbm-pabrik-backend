<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use App\Models\Gaji;
use App\Models\Item;
use App\Models\Mutation;
use App\Models\ProductionOrder;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use stdClass;

class ReportController extends BaseController
{
    public function reportProduction(Request $request)
    {

        $id = $request->input('id');
        $item = ProductionOrder::with(['input.item.unit', 'output.item.unit', 'output.item.type', 'machine', 'overhead', 'timeline.user', 'user'])->where('id', $id)->first();
 
        if ($item) {

            switch ($item->status) {
                case "DONE PRODUCTION":
                    $done_production = true;
                    break;
                case "WAREHOUSE":
                    $done_production = true;
                    break;
                case "SHIPPING":
                    $done_production = true;
                    break;
                case "RETUR":
                    $done_production = true;
                    break;
                case "RECEIVE":
                    $done_production = true;
                    break;
                default:
                    $done_production = false;
            }

            return view('production.report', [
                'data' => $item,
                'pic_production' => $request->input('pic_production'),
                'done_production' => $done_production
            ]);

            // $pdf = PDF::loadView('production.report', [
            //     'data' => $item,
            //     'pic_production' => $request->input('pic_production')
            // ]);

            // return $pdf->download('production_report' . $item['sequence'] . '.pdf');
        }
        return $this->sendError('Data not found');
    }


    public function reportItem(Request $request)
    {
        $warehouseId = $request->input('warehouse_id');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $item = Item::with(['type', 'unit', 'warehouse', 'user']);

        $result = $item->get();

        if (!is_null($fromDate) && !is_null($toDate)) {
            $fromDate = Carbon::createFromFormat('Y-m-d', $fromDate)->startOfDay();
            $toDate = Carbon::createFromFormat('Y-m-d', $toDate)->endOfDay();
            $result->each(function ($value) use ($fromDate, $toDate) {
                $value->balance = 0;
                $mutation = Mutation::where('item_id', $value->id)->whereBetween('created_at', [$fromDate, $toDate])->orderBy('id', 'desc')->first();
                if ($mutation) {
                    return $value->balance = $mutation->balance;
                }
            });
        }

        if ($warehouseId) {
            $item->where('warehouse_id', $warehouseId);
            $warehouse = Warehouse::find($warehouseId);
            $warehouseShow = false;
        } else {
            $warehouse = new stdClass();
            $warehouse->name = 'SEMUA';
            $warehouseShow = true;
        }

        return view('item.report', [
            'data' => $result,
            'from_date' => Carbon::parse($fromDate)->format('d F Y'),
            'to_date' => Carbon::parse($toDate)->format('d F Y'),
            'warehouse' => $warehouse,
            'warehouseShow' => $warehouseShow,
        ]);


        // $pdf = PDF::loadView('item.report', [
        //     'data' => $result,
        //     'from_date' => Carbon::parse($fromDate)->format('d F Y'),
        //     'to_date' => Carbon::parse($toDate)->format('d F Y'),
        //     'warehouse' => $warehouse,
        //     'warehouseShow' => $warehouseShow,
        // ]);

        // return $pdf->download('laporan persediaan.pdf');
    }

    public function reportMutation(Request $request)
    {
        $id = $request->input('id');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $name = $request->input('name');

        if (!$fromDate && !$toDate) {
            $fromDate == '01-01-2022';
            $toDate == '2022-11-02';
        }



        $item = Item::with('type', 'unit', 'user')->where('id', $id)->first();
        $mutation = Mutation::where('item_id', $id);

            $mutation = Mutation::where('item_id', $id)
                    ->when($name, function ($query, $name) {
                return $query
                    ->where('notes', 'like', '%' . $name . '%');
            });

        if ($fromDate && $toDate) {
            $fromDate = Carbon::createFromFormat('Y-m-d', $fromDate)->startOfDay();
            $toDate = Carbon::createFromFormat('Y-m-d', $toDate)->endOfDay();
        } else {
            $fromDate = Carbon::now()->startOfMonth();
            $toDate = Carbon::now();
        }
        $mutation->whereBetween('created_at', [$fromDate, $toDate]);
        $mutation->orderBy('id', 'desc');

        return view('mutation.report', [
            'data_item' => $item,
            'data_mutation' => $mutation->get(),
            'from_date' => Carbon::parse($fromDate)->format('d F Y'),
            'to_date' => Carbon::parse($toDate)->format('d F Y'),
        ]);


        // $pdf = PDF::loadView('mutation.report', [
        //     'data_item' => $item,
        //     'data_mutation' => $mutation->get(),
        //     'from_date' => Carbon::parse($fromDate)->format('d F Y'),
        //     'to_date' => Carbon::parse($toDate)->format('d F Y'),
        // ]);

        // return $pdf->download('laporan persediaan.pdf');
        // $pdf = PDF::loadView('myPDF');

        // return $pdf->download('Laporan Mutasi ' . $item->name . '.pdf');
    }

     public function reportGaji($created_at)
    {
        $data = Gaji::with('pegawai')->whereDate('created_at',$created_at)->get();
       
        if($data){
            return view('bisnis.gaji', [
                'data' => $data,
                'tanggal'=>Carbon::parse($created_at)->format('d F Y')

            ]);

            // $pdf = PDF::loadView('production.report', [
            //     'data' => $item,
            //     'pic_production' => $request->input('pic_production')
            // ]);

            // return $pdf->download('production_report' . $item['sequence'] . '.pdf');
        }
        return $this->sendError('Data not found');
    }

}

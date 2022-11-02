<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use App\Models\Item;
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

        // $id = $request->input('id');
        // $item = ProductionOrder::with('input.item.unit', 'output.item.unit', 'output.item.type', 'timeline.user', 'user')->where('id', $id)->first();
        // if ($item) {
        //     return view('production.report',[
        //         'data' => $item,
        //     ]);
        // }
        // return $this->sendError('Data not found');
        // return view('404');

        $item = $request->all();
        // return $item;

        return view('production.report', [
            'data' => $item,
        ]);

        $pdf = PDF::loadView('production.report', [
            'data' => $item,
        ]);

        return $pdf->download('production_report' . $item['sequence'] . '.pdf');
        // $pdf = PDF::loadView('myPDF');

        // return $pdf->download('nicesnippets.pdf');
    }


    public function reportItem(Request $request)
    {
        $warehouseId = $request->input('warehouse_id');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $data = Item::with('type', 'unit', 'warehouse')->with('mutation', function ($query) use ($fromDate, $toDate) {
            if (!is_null($fromDate) && !is_null($toDate)) {
                $query->whereBetween('created_at', [$fromDate, $toDate]);
            }
            $query->balance = 0;
        })->get();


        if ($warehouseId) {
            $data->where('warehouse_id', $warehouseId);
            $warehouse = Warehouse::find($warehouseId);
            $warehouseShow = false;
        } else {
            $warehouse = new stdClass();
            $warehouse->name = 'SEMUA';
            $warehouseShow = true;
        }

        foreach ($data as $key => $value) {
            $splitDebitColumn = array_column($value->mutation->toArray(), 'debit');
            $splitKreditColumn = array_column($value->mutation->toArray(), 'kredit');
            $debit = array_sum($splitDebitColumn);
            $kredit = array_sum($splitKreditColumn);
            $value->balance = $debit - $kredit;
        }

        // return $data;
        // return view('item.report', ['data' => $data]);

        $pdf = PDF::loadView('item.report', [
            'data' => $data,
            'from_date' => Carbon::parse($fromDate)->format('d F Y'),
            'to_date' => Carbon::parse($toDate)->format('d F Y'),
            'warehouse' => $warehouse,
            'warehouseShow' => $warehouseShow,
        ]);

        return $pdf->download('laporan persediaan.pdf');
        // $pdf = PDF::loadView('myPDF');

        // return $pdf->download('nicesnippets.pdf');
    }

    public function reportMutation(Request $request)
    {
        $id = $request->input('id');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        if (!$fromDate && !$toDate) {
            $fromDate == '01-01-2022';
            $toDate == '2022-11-02';
        }

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

        return view('mutation.report', [
            'data' => $item,
            'from_date' => Carbon::parse($fromDate)->format('d F Y'),
            'to_date' => Carbon::parse($toDate)->format('d F Y'),
        ]);

        return $this->sendResponse($item, 'Data fetched');

        // $pdf = PDF::loadView('item.report', [
        //     'data' => $data,
        //     'from_date' => Carbon::parse($fromDate)->format('d F Y'),
        //     'to_date' => Carbon::parse($toDate)->format('d F Y'),
        //     'warehouse' => $warehouse,
        //     'warehouseShow' => $warehouseShow,
        // ]);

        // return $pdf->download('laporan persediaan.pdf');
        // $pdf = PDF::loadView('myPDF');

        // return $pdf->download('nicesnippets.pdf');
    }
}

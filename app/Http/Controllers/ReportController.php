<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use PDF;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function reportProduction()
    {
        return view('production.report');

        $pdf = PDF::loadView('production.report');

        return $pdf->download('production_report.pdf');
        // $pdf = PDF::loadView('myPDF');

        // return $pdf->download('nicesnippets.pdf');
    }

    public function reportItem(Request $request)
    {
        $warehouseId = $request->input('warehouse_id');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $data = Item::with('type', 'unit')->with('mutation', function ($query) use ($fromDate, $toDate) {
            if (!is_null($fromDate) && !is_null($toDate)) {
                $query->whereBetween('created_at', [$fromDate, $toDate]);
            }
            $query->balance = 0;
        })->get();

        $warehouse = array(
            'name' => 'SEMUA'
        );

        if ($warehouseId) {
            $data->where('warehouse_id', $warehouseId);
            $warehouse = Warehouse::find($warehouseId);
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
        ]);

        return $pdf->download('laporan persediaan.pdf');
        // $pdf = PDF::loadView('myPDF');

        // return $pdf->download('nicesnippets.pdf');
    }
}

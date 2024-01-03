<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use App\Models\Item;
use App\Models\Mutation;
use App\Models\Pembelian;
use App\Models\ProductionOrder;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use stdClass;

class FakturController extends BaseController
{


    public function pembelian($id)
    {

        $result = Pembelian::where('id', $id)
            ->with(['detail.item.unit', 'user'])
            ->first();
        if ($result) {
            return view('faktur.pembelian', ['data' => $result, 'notes' => 'NOTES']);
        }
        return $this->sendError('Data not found');





        // $pdf = PDF::loadView('item.report', [
        //     'data' => $result,
        //     'from_date' => Carbon::parse($fromDate)->format('d F Y'),
        //     'to_date' => Carbon::parse($toDate)->format('d F Y'),
        //     'warehouse' => $warehouse,
        //     'warehouseShow' => $warehouseShow,
        // ]);

        // return $pdf->download('laporan persediaan.pdf');
    }
}

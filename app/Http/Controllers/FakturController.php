<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use App\Models\Item;
use App\Models\Mutation;
use App\Models\Pembelian;
use App\Models\Penjualan;
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
    }

    public function penjualan($id)
    {

        $result = Penjualan::where('id', $id)
            ->with(['detail.item.unit', 'user','pelanggan'])
            ->first();
        if ($result) {
            return view('faktur.penjualan', ['data' => $result, 'notes' => 'NOTES']);
        }
        return $this->sendError('Data not found');
    }
}
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use App\Models\Biaya;
use App\Models\Gaji;
use App\Models\Item;
use App\Models\Mutation;
use App\Models\Penjualan;
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
        $item = ProductionOrder::with(['input.item.unit', 'output.item.unit', 'output.item.type', 'machine', 'overhead', 'timeline.user', 'user'])
            ->where('id', $id)
            ->first();

        if ($item) {
            switch ($item->status) {
                case 'DONE PRODUCTION':
                    $done_production = true;
                    break;
                case 'WAREHOUSE':
                    $done_production = true;
                    break;
                case 'SHIPPING':
                    $done_production = true;
                    break;
                case 'RETUR':
                    $done_production = true;
                    break;
                case 'RECEIVE':
                    $done_production = true;
                    break;
                default:
                    $done_production = false;
            }

            return view('production.report', [
                'data' => $item,
                'pic_production' => $request->input('pic_production'),
                'done_production' => $done_production,
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
                $mutation = Mutation::where('item_id', $value->id)
                    ->whereBetween('created_at', [$fromDate, $toDate])
                    ->orderBy('id', 'desc')
                    ->first();
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

        $mutation = Mutation::where('item_id', $id)->when($name, function ($query, $name) {
            return $query->where('notes', 'like', '%' . $name . '%');
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
        $data = Gaji::with('pegawai')->whereDate('created_at', $created_at)->get();

        if ($data) {
            return view('bisnis.gaji', [
                'data' => $data,
                'tanggal' => Carbon::parse($created_at)->format('d F Y'),
            ]);

            // $pdf = PDF::loadView('production.report', [
            //     'data' => $item,
            //     'pic_production' => $request->input('pic_production')
            // ]);

            // return $pdf->download('production_report' . $item['sequence'] . '.pdf');
        }
        return $this->sendError('Data not found');
    }

    public function reportBiaya($created_at)
    {
        $data = Biaya::whereDate('created_at', $created_at)->get();

        if ($data) {
            return view('bisnis.biaya', [
                'data' => $data,
                'tanggal' => Carbon::parse($created_at)->format('d F Y'),
            ]);

            // $pdf = PDF::loadView('production.report', [
            //     'data' => $item,
            //     'pic_production' => $request->input('pic_production')
            // ]);

            // return $pdf->download('production_report' . $item['sequence'] . '.pdf');
        }
        return $this->sendError('Data not found');
    }

    function reportPersediaanProduksi (Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::now()->format('d F Y'));
        // return $tanggal;
        $tanggal2 = Carbon::createFromFormat('d F Y', $tanggal)->format('Y-m-d');

        $result = Mutation::with('item.type')
            ->when($tanggal2, function ($query, $tanggal2) {
                return $query->whereDate('created_at', $tanggal2);
            })
            ->get();

        return view('laporan.persediaan', ['data' => $result,  'tanggal' => $tanggal]);
    }

      function bisnisHome (Request $request)
    {
       
        return view('laporan.bisnis');
    }

     function reportLabaRugiHarian (Request $request)
    {
         $tanggal = $request->input('tanggal', Carbon::now()->format('d F Y'));
        // return $tanggal;
        $tanggal2 = Carbon::createFromFormat('d F Y', $tanggal)->format('Y-m-d');
        return view('laporan.labarugiharian',['tanggal' => $tanggal]);
    }

    function generateReport(Request $request){
        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $d = $request->d;

        $tanggal = date($tahun . '-' . $bulan . '-' . $d);
        $fromDate = Carbon::parse($tanggal)->startOfDay();
        $toDate = Carbon::parse($tanggal)->endOfDay();

        $biaya = Biaya::selectRaw('kategori, sum(jumlah) as jumlah')
            ->with('nama')
            ->groupBy('kategori')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->get();

        $totalBiaya = 0;
        foreach ($biaya as $key => $b) {
            $totalBiaya = $totalBiaya + $b->jumlah;
        }

        $gaji = Gaji::selectRaw('sum(gaji) as gaji, sum(bonus) as bonus, sum(uang_makan) as uang_makan')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->first();

        
        $totalGaji = 0;
        foreach ($gaji as $key => $b) {
            $totalGaji = $totalGaji + $b->gaji + $b->bonus + $b->uang_makan;
        }

        $totalPenjualan = Penjualan::selectRaw('sum(total_penjualan) as total_penjualan')
            ->selectRaw('sum(diskon) as diskon')
            ->selectRaw('sum(ongkir) as ongkir')
            ->whereBetween('tanggal_transaksi', [$fromDate, $toDate])
            ->first();

        // $returPenjualan = ReturPenjualan::selectRaw('sum(retur_grand_total) as retur_total')
        //     ->whereBetween('tanggal_transaksi', [$fromDate, $toDate])
        //     ->first();

        // $pembelian = Pembelian::selectRaw('sum(total_harga) as total_pembelian')
        //     ->whereBetween('tanggal_input', [$fromDate, $toDate])
        //     ->first();

        // $persediaanAwal = $this->persediaan($fromDate->subDay());
        // $persediaanAkhir = $this->persediaan($fromDate->addDay());

        // $data[0] = [
        //     'nomor' => 1,
        //     'account' => 'PENJUALAN',
        //     'class' => 'fw-bold',
        //     'balance' => $totalPenjualan->total_penjualan ?? 0,
        // ];
        // $data[1] = [
        //     'nomor' => 2,
        //     'class' => 'text-danger',
        //     'account' => 'RETUR PENJUALAN',
        //     'balance' => $returPenjualan->retur_total == null ? 0 : $returPenjualan->retur_total,
        // ];
        // $data[2] = [
        //     'nomor' => 3,
        //     'class' => 'fw-bold',
        //     'account' => 'TOTAL PENJUALAN (1-2)',
        //     'balance' => $totalPenjualan->total_penjualan - $totalPenjualan->diskon - $data[1]['balance'],
        // ];
        // $data[3] = [
        //     'nomor' => 4,
        //     'class' => '',
        //     'account' => 'PERSEDIAAN AWAL',
        //     'balance' => $persediaanAwal,
        // ];
        // $data[4] = [
        //     'nomor' => 5,
        //     'class' => '',
        //     'account' => 'TOTAL PEMBELIAN',
        //     'balance' => $pembelian->total_pembelian == null ? 0 : $pembelian->total_pembelian,
        // ];
        // $data[5] = [
        //     'nomor' => 6,
        //     'class' => '',
        //     'account' => 'PERSEDIAAN AKHIR',
        //     'balance' => $persediaanAkhir,
        // ];
        // $data[6] = [
        //     'nomor' => 7,
        //     'class' => 'fw-bold text-danger',
        //     'account' => 'HARGA POKOK PENJUALAN (4+5-6)',
        //     'balance' => $persediaanAwal + $pembelian->total_pembelian - $persediaanAkhir,
        // ];
        // $data[7] = [
        //     'nomor' => 8,
        //     'class' => 'fw-bold',
        //     'account' => 'TOTAL PENDAPATAN (3-7)',
        //     'balance' => $data[2]['balance'] - $data[6]['balance'],
        // ];
        // $data[8] = [
        //     'nomor' => 9,
        //     'class' => 'text-danger',
        //     'account' => 'BIAYA OPERASIONAL',
        //     'balance' => $totalBiaya,
        // ];
        // $data[9] = [
        //     'nomor' => 10,
        //     'class' => 'text-danger',
        //     'account' => 'GAJI',
        //     'balance' => $gaji->total ?? 0,
        // ];
        // $data[10] = [
        //     'nomor' => 11,
        //     'class' => 'fw-bold',
        //     'account' => 'LABA / RUGI (8-9-10)',
        //     'balance' => $data[7]['balance'] - $data[8]['balance'] - $data[9]['balance'],
        // ];

        foreach ($data as $key => $d) {
            LabaRugi::create([
                'nomor' => $d['nomor'],
                'account' => $d['account'],
                'class' => $d['class'],
                'balance' => $d['balance'],
                'created_at' => $tanggal,
            ]);
        }
        return 'Sukses';
    }
}

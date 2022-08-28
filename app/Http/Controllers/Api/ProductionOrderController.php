<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\ProductionOrderResource;
use App\Models\ProductionOrder;
use App\Models\ProductionOrderInput;
use App\Models\ProductionOrderOutput;
use App\Models\ProductionOrderTimeline;

class ProductionOrderController extends BaseController
{
    public function index(Request $request)
    {
       
        $limit = $request->input('limit', 5);
        $name = $request->input('name');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $item = ProductionOrder::with(['input.item.unit', 'output.item.unit', 'timeline.user', 'user']);
        if($fromDate && $toDate){
            $item->whereBetween('created_at', [$fromDate, $toDate]);
        }
        if ($name) {
            $item->where('sequence', 'like', '%' . $name . '%')
            ->orWhere('customer_name', 'like', '%' . $name . '%')
            ->orWhere('status', 'like', '%' . $name . '%')
            ->orWhere('pic_name', 'like', '%' . $name . '%');
        }
     
        $data = $item->latest()->paginate($limit);

        return $this->sendResponse($data, 'Data fetched');
    }

    public function show($id)
    {
        $item = ProductionOrder::with('input.item.unit', 'output.item.unit', 'timeline.user', 'user')->where('id', $id)->first();
        if($item){
            return $this->sendResponse($item, 'Data fetched');
        }
            return $this->sendError('Data not found');
    }

    public function getSequenceNumber()
    {
        $lastNumber = ProductionOrder::latest();
        if($lastNumber->count() == 0){
            $lastNumber = 0;
        }else{
            // $lastNumber = 1;5
            $lastNumber = $lastNumber->first()->id;
        }
        $getDate = date('Ymd');
        return $getDate.$lastNumber;
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'order_date' => 'required',
            'pic_name' => 'required',
            'customer_name' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $sequence = $this->getSequenceNumber();
        $productionOrder = ProductionOrder::create([
            'sequence'=> $sequence,
            'pic_name'=> $input['pic_name'],
            'customer_name'=> $input['customer_name'],
            'notes'=> $input['notes'],
            'status'=> 'NEW ORDER',
            'target_date'=> $input['target_date'],
            'order_date'=> $input['order_date'],
            'created_by'=> '1'
        ]);

        if($productionOrder)
        {
            $timeline = ProductionOrderTimeline::create([
                'production_id'=> $productionOrder->id,
                'status' => 'NEW ORDER',
                'notes'=>  $productionOrder->notes,
                'created_by'=> $productionOrder->created_by
            ]);
            
            $productionOrder['timeline'] = $timeline;
            $POInput = [];
            $POOutput = [];
            foreach ($input['input'] as $key => $value) {
               $POInput[]= ProductionOrderInput::create([
                    'production_id'=> $productionOrder->id,
                    'item_id'=> $value['id'],
                    'estimate_quantity'=> $value['estimate_quantity'],
                ]);
            }
                $productionOrder['input'] = $POInput;
            foreach ($input['output'] as $key => $value) {
                $POOutput[]= ProductionOrderOutput::create([
                    'production_id'=> $productionOrder->id,
                    'item_id'=> $value['id'],
                    'type_id'=> $value['type_id'],
                    'target_quantity'=> $value['target_quantity'],
                    'real_quantity'=> 0,
                ]);
            }
                $productionOrder['output'] = $POOutput;
            $productionOrder['output'] = $POOutput;
        }
        // // PO singkatan Production Order
        // if ($item) {
        //     //membuat saldo awal
        //     $mutation = new Mutation;
        //     $mutation->item_id = $item->id;
        //     $mutation->warehouse_id = $item->warehouse_id;
        //     $mutation->debit = 0;
        //     $mutation->kredit = 0;
        //     $mutation->notes = 'saldo awal';
        //     $mutation->created_by = 1;
        //     $mutation->save();
        // }
        return $this->sendResponse($productionOrder, 'Data created');
    }

    public function destroy(ProductionOrder $productionOrder)
    {
        $productionOrder->delete();
        if ($productionOrder) {
            ProductionOrderInput::where('production_id', $productionOrder->id)->delete();
            ProductionOrderOutput::where('production_id', $productionOrder->id)->delete();
            ProductionOrderTimeline::where('production_id', $productionOrder->id)->delete();
        }
        return $this->sendResponse([], 'Data deleted');
    }
}

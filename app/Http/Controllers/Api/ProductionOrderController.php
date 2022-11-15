<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Api\MutationController;
use App\Http\Resources\ProductionOrderResource;
use App\Models\Mutation;
use App\Models\ProductionOrder;
use App\Models\ProductionOrderInput;
use App\Models\ProductionOrderMachine;
use App\Models\ProductionOrderOutput;
use App\Models\ProductionOrderOverhead;
use App\Models\ProductionOrderTimeline;
use App\Models\Shipping;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ProductionOrderController extends BaseController
{
    public function index(Request $request)
    {

        $limit = $request->input('limit', 5);
        $name = $request->input('name');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $item = ProductionOrder::with(['input.item.unit', 'output.item.unit', 'machine.machine', 'overhead.overhead', 'timeline.user', 'user']);
        if ($fromDate && $toDate) {
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
        $item = ProductionOrder::with('input.item.unit', 'output.item.unit', 'output.item.type', 'machine.machine', 'overhead.overhead', 'timeline.user', 'user')->where('id', $id)->first();
        if ($item) {
            return $this->sendResponse(new ProductionOrderResource($item), 'Data fetched');
        }
        return $this->sendError('Data not found');
    }

    // UPDATE DATA DARI NEW ORDER
    public function update(Request $request, $id)
    {
        $input = $request->all();

        $productionOrder = ProductionOrder::findOrFail($id);
        if ($productionOrder) {

            $productionOrder->order_date = $input['order_date'];
            $productionOrder->customer_name = $input['customer_name'];
            $productionOrder->pic_name = $input['pic_name'];
            $productionOrder->notes = $input['notes'];
            $productionOrder->target_date = $input['target_date'];
            $productionOrder->save();

            ProductionOrderInput::where('production_id', $productionOrder->id)->delete();
            ProductionOrderOutput::where('production_id', $productionOrder->id)->delete();

            $timeline = ProductionOrderTimeline::create([
                'production_id' => $productionOrder->id,
                'status' => "UPDATE ORDER",
                'notes' =>  'data di perbaharui',
                'created_by' => Auth::id()
            ]);
            $productionOrder['timeline'] = $timeline;
            $POInput = [];
            $POOutput = [];
            $POMachine = [];
            $POOverhead = [];
            foreach ($input['input'] as $key => $value) {
                $POInput[] = ProductionOrderInput::create([
                    'production_id' => $productionOrder->id,
                    'item_id' => $value['id'],
                    'estimate_quantity' => $value['estimate_quantity'],
                    'real_quantity' => $value['estimate_quantity'],
                ]);
            }
            $productionOrder['input'] = $POInput;
            foreach ($input['output'] as $key => $value) {
                $POOutput[] = ProductionOrderOutput::create([
                    'production_id' => $productionOrder->id,
                    'item_id' => $value['id'],
                    'type_id' => $value['type_id'],
                    'target_quantity' => $value['target_quantity'],
                    'real_quantity' => 0,
                ]);
            }
            foreach ($input['machine'] as $key => $value) {
                $POMachine[] = ProductionOrderMachine::create([
                    'production_id' => $productionOrder->id,
                    'machine_id' => $value['id'],
                    'usage_meter' => $value['usage_meter'],
                ]);
            }
            foreach ($input['overhead'] as $key => $value) {
                $POOverhead[] = ProductionOrderOverhead::create([
                    'production_id' => $productionOrder->id,
                    'overhead_id' => $value['id'],
                    'usage_meter' => $value['usage_meter'],
                ]);
            }
            $productionOrder['input'] = $POInput;
            $productionOrder['output'] = $POOutput;
            $productionOrder['machine'] = $POMachine;
            $productionOrder['overhead'] = $POOverhead;
        }

        return $this->sendResponse(new ProductionOrderResource($productionOrder), 'Data updated');
    }

    // UPDATE DATA SETELAH ORDER BERSTATUS SELESAI
    public function updateData(Request $request)
    {
        $input = $request->all();
        $dataOrder = $input['data_order'];
        $updateOrder = $input['update_order'];
        $updateInput = $input['update_input'];

        $productionOrder = ProductionOrder::findOrFail($dataOrder['id']);
        if ($productionOrder) {
            $updateOutput = [];
            $newOutput = [];
            foreach ($dataOrder['output'] as $key => $output) {
                $data = ProductionOrderOutput::findOrFail($output['id']);
                if ($data) {
                    $data->real_quantity = $output['real_quantity'];
                    $data->save();
                    $updateOutput[] = $data;
                } else {
                    $updateOutput[] = 'Data ID: ' . $output['id'] . ' Gagal Update!';
                }
            }

            foreach ($updateOrder as $key => $value) {
                $newOutput[] = ProductionOrderOutput::create([
                    'production_id' => $productionOrder->id,
                    'item_id' => $value['id'],
                    'type_id' => $value['type_id'],
                    'target_quantity' => 0,
                    'real_quantity' => $value['real_quantity'],
                ]);
            }

            foreach ($updateInput as $key => $value) {
                $newInput = ProductionOrderInput::find($value['id']);
                $newInput->real_quantity = $value['real_quantity'];
                $newInput->save();


                if ($newInput->estimate_quantity > $newInput->real_quantity) {
                    $balance = $newInput->estimate_quantity - $newInput->real_quantity;
                    $item = MutationController::mutationItem($newInput->item_id, $balance, 'DEBIT',  'Sisa pemakaian bahan baku produksi nomor : ' . $productionOrder->sequence, 1);
                } else {
                    $balance = $newInput->real_quantity - $newInput->estimate_quantity;
                    $item = MutationController::mutationItem($newInput->item_id, $balance, 'KREDIT',  'Kekurangan bahan baku untuk produksi : ' . $productionOrder->sequence, 1);
                }
            }

            $timeline = ProductionOrderTimeline::create([
                'production_id' => $productionOrder->id,
                'status' => "DONE PRODUCTION",
                'notes' =>  'Order telah selesai dikerjakan',
                'created_by' =>  Auth::id(),
            ]);

            $productionOrder->status = 'DONE PRODUCTION';
            $productionOrder->save();

            $productionOrder->output = $updateOrder;
            $productionOrder->new_output = $newOutput;
        }

        return $this->sendResponse($productionOrder, 'Data updated');
    }

    // UPDATE DATA KE WAREHOUSES
    public function updateWarehouse(Request $request)
    {
        $input = $request->all();
        $productionOrder = ProductionOrder::findOrFail($input['id']);
        if ($productionOrder) {
            foreach ($productionOrder['output'] as $key => $output) {

                $item = MutationController::mutationItem($output->item_id, $output->real_quantity, 'DEBIT',  'Hasil produksi nomor : ' . $productionOrder->sequence, 1);
            }

            // foreach ($productionOrder['input'] as $key => $input) {
            //     $item = MutationController::mutationItem($input->item_id, $input->estimate_quantity, 'KREDIT',  'Bahan untuk produksi nomor : ' . $productionOrder->sequence, 1);
            // }

            $timeline = ProductionOrderTimeline::create([
                'production_id' => $productionOrder->id,
                'status' => "WAREHOUSE",
                'notes' =>  'Hasil Produksi telah dikirim ke Gudang',
                'created_by' => Auth::id()
            ]);
            $productionOrder->status = 'WAREHOUSE';
            $productionOrder->save();
        }

        return $this->sendResponse($productionOrder, 'Data updated');
    }

    // UPDATE ITEM DI KIRIM
    public function updateShipping(Request $request)
    {
        $productionOrder = ProductionOrder::with('output')->findOrFail($request['id']);
        if ($productionOrder) {

            $shipping = ShippingController::store($request, true);

            if (!$shipping) {
                return $this->sendResponse(null, 'Error', 201);
            }

            $productionOrder->shipping_id = $shipping->id;
            $productionOrder->save();

            foreach ($productionOrder->output as $key => $output) {
                $item = MutationController::mutationItem($output->item_id, $output->real_quantity, 'KREDIT',  'Shipping Item ke Pelanggan Nomor : ' . $productionOrder->sequence, 1);
            }

            $timeline = ProductionOrderTimeline::create([
                'production_id' => $productionOrder->id,
                'status' => "SHIPPING",
                'notes' =>  'Item sedang dalam perjalanan di kirim ke Pelanggan',
                'created_by' => Auth::id()
            ]);
            $productionOrder->status = 'SHIPPING';
            $productionOrder->save();
        }
        if ($request['nopol'] == '') {
            return $this->sendResponse($productionOrder, 'Data updated');
        }
    }

    // UPDATE ITEM DI RETUR
    public function returShipping(Request $request)
    {
        $productionOrder = ProductionOrder::with('output')->findOrFail($request['id']);
        if ($productionOrder) {
            foreach ($productionOrder->output as $key => $output) {
                $item = MutationController::mutationItem($output->item_id, $output->real_quantity, 'DEBIT',  'Retur Item dari Pelanggan Nomor : ' . $productionOrder->sequence, 1);
            }

            $timeline = ProductionOrderTimeline::create([
                'production_id' => $productionOrder->id,
                'status' => "RETUR",
                'notes' =>  'Item di Retur dari Pelanggan',
                'created_by' => Auth::id()
            ]);
            $productionOrder->status = 'RETUR';
            $productionOrder->save();
        }
        if ($request['nopol'] == '') {
            return $this->sendResponse($productionOrder, 'Data updated');
        }
    }

    // UPDATE ITEM DI RETUR
    public function receiveShipping(Request $request)
    {
        $productionOrder = ProductionOrder::with('output')->findOrFail($request['id']);
        if ($productionOrder) {

            $shipping = Shipping::find($productionOrder->shipping_id);

            if ($shipping) {
                $timeline = ProductionOrderTimeline::create([
                    'production_id' => $productionOrder->id,
                    'status' => "RECEIVE",
                    'notes' =>  'Item di telah di terima Pelanggan',
                    'created_by' => Auth::id()
                ]);

                $shipping->receiving_date = Carbon::now();
                $shipping->save();

                $productionOrder->status = 'RECEIVE';
                $productionOrder->save();
            } else {
                return $this->sendResponse(null, 'Data Not Found', 201);
            }
        }
        if ($request['nopol'] == '') {
            return $this->sendResponse($productionOrder, 'Data updated');
        }
    }

    // HANYA UPDATE STATUS
    public function updateStatus(Request $request)
    {
        $input = $request->all();
        $productionOrder = ProductionOrder::findOrFail($input['id']);
        if ($productionOrder) {
            $productionOrder->status = $input['status'];
            $productionOrder->save();

            if ($input['status'] == 'WORK IN PROGRESS') {
                foreach ($productionOrder['input'] as $key => $dd) {
                    $item = MutationController::mutationItem($dd->item_id, $dd->estimate_quantity, 'KREDIT',  'Bahan baku untuk produksi nomor : ' . $productionOrder->sequence, 1);
                }
            }

            $timeline = ProductionOrderTimeline::create([
                'production_id' => $productionOrder->id,
                'status' => $input['status'],
                'notes' =>  'status diperbaharui',
                'created_by' => $productionOrder->created_by
            ]);
            $productionOrder['timeline'] = $timeline;
        }
        return $this->sendResponse(new ProductionOrderResource($productionOrder), 'Data updated');
    }

    public function getSequenceNumber()
    {
        $lastNumber = ProductionOrder::latest();
        if ($lastNumber->count() == 0) {
            $lastNumber = 0;
        } else {
            // $lastNumber = 1;5
            $lastNumber = $lastNumber->first()->id;
        }
        $getDate = date('Ymd');
        return $getDate . $lastNumber;
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
            'sequence' => $sequence,
            'pic_name' => $input['pic_name'],
            'customer_name' => $input['customer_name'],
            'notes' => $input['notes'],
            'status' => 'NEW ORDER',
            'target_date' => $input['target_date'],
            'order_date' => $input['order_date'],
            'created_by' => '1'
        ]);

        if ($productionOrder) {
            $timeline = ProductionOrderTimeline::create([
                'production_id' => $productionOrder->id,
                'status' => 'NEW ORDER',
                'notes' =>  'NEW ORDER telah di buat',
                'created_by' => $productionOrder->created_by
            ]);

            $productionOrder['timeline'] = $timeline;
            $POInput = [];
            $POOutput = [];
            $POMachine = [];
            $POOverhead = [];
            foreach ($input['input'] as $key => $value) {
                $POInput[] = ProductionOrderInput::create([
                    'production_id' => $productionOrder->id,
                    'item_id' => $value['id'],
                    'estimate_quantity' => $value['estimate_quantity'],
                    'real_quantity' => $value['estimate_quantity'],
                ]);
            }
            $productionOrder['input'] = $POInput;
            foreach ($input['output'] as $key => $value) {
                $POOutput[] = ProductionOrderOutput::create([
                    'production_id' => $productionOrder->id,
                    'item_id' => $value['id'],
                    'type_id' => $value['type_id'],
                    'target_quantity' => $value['target_quantity'],
                    'real_quantity' => 0,
                ]);
            }
            foreach ($input['machine'] as $key => $value) {
                $POMachine[] = ProductionOrderMachine::create([
                    'production_id' => $productionOrder->id,
                    'machine_id' => $value['id'],
                    'usage_meter' => $value['usage_meter'],
                ]);
            }
            foreach ($input['overhead'] as $key => $value) {
                $POOverhead[] = ProductionOrderOverhead::create([
                    'production_id' => $productionOrder->id,
                    'overhead_id' => $value['id'],
                    'usage_meter' => $value['usage_meter'],
                ]);
            }
            $productionOrder['input'] = $POInput;
            $productionOrder['output'] = $POOutput;
            $productionOrder['machine'] = $POMachine;
            $productionOrder['overhead'] = $POOverhead;
        }

        return $this->sendResponse($productionOrder, 'Data created');
    }

    public function destroy(ProductionOrder $productionOrder)
    {
        $productionOrder->delete();
        if ($productionOrder) {
            ProductionOrderInput::where('production_id', $productionOrder->id)->delete();
            ProductionOrderOutput::where('production_id', $productionOrder->id)->delete();
            ProductionOrderMachine::where('production_id', $productionOrder->id)->delete();
            ProductionOrderOverhead::where('production_id', $productionOrder->id)->delete();
            ProductionOrderTimeline::where('production_id', $productionOrder->id)->delete();
        }
        return $this->sendResponse([], 'Data deleted');
    }
}

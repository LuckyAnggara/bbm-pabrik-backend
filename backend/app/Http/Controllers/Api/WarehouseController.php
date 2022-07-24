<?php

namespace App\Http\Controllers\Api;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Http\Resources\WarehouseResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;

class WarehouseController extends BaseController
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 5);
        $name = $request->input('name');

        $warehouse = Warehouse::with(['user']);
        if($name)
        {
            $warehouse->where('name','like','%'.$name.'%');
        }

        return $this->sendResponse($warehouse->latest()->paginate($limit), 'Data fetched');
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
        ]);
        if($validator->fails())
        {
            return $this->sendError($validator->errors());
        }
        $itemType = Warehouse::create($input);
        return $this->sendResponse(new WarehouseResource($itemType), 'Data created');
    }

   
    public function show($id)
    {
        $warehouse = Warehouse::with(['user'])->where('id', $id)->first();
        if(is_null($warehouse))
        {
            return $this->sendError('Data does not exist.');
        }
        return $this->sendResponse(new WarehouseResource($warehouse), 'Data fetched');
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
        ]);
        
        if($validator->fails())
        {
            return $this->sendError($validator->errors());
        }
        $warehouse->name = $input['name'];
        $warehouse->abbreviation = $input['abbreviation'];
        $warehouse->save();

        return $this->sendResponse(new WarehouseResource($warehouse), 'Data updated');
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();
        return $this->sendResponse(new WarehouseResource($warehouse), 'Data deleted');
    }
}

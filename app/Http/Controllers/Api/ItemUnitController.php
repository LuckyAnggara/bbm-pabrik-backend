<?php

namespace App\Http\Controllers\Api;

use App\Models\ItemUnit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ItemUnitResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ItemUnitController extends BaseController
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 5);
        $name = $request->input('name');

        $itemUnit = ItemUnit::with(['user']);
        if ($name) {
            $itemUnit->where('name', 'like', '%' . $name . '%');
        }

        return $this->sendResponse($itemUnit->latest()->paginate(), 'Data fetched');
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        $itemUnit = ItemUnit::create([
            'name' => $input['name'],
            'created_by' => Auth::id()
        ]);

        return $this->sendResponse(new ItemUnitResource($itemUnit), 'Data created');
    }


    public function show($id)
    {
        $itemUnit = ItemUnit::with(['user'])->where('id', $id)->first();
        if (is_null($itemUnit)) {
            return $this->sendError('Data does not exist.');
        }
        return $this->sendResponse(new ItemUnitResource($itemUnit), 'Data fetched');
    }

    public function update(Request $request, ItemUnit $itemUnit)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        $itemUnit->name = $input['name'];
        $itemUnit->abbreviation = $input['abbreviation'];
        $itemUnit->save();

        return $this->sendResponse(new ItemUnitResource($itemUnit), 'Data updated');
    }

    public function destroy(ItemUnit $itemUnit)
    {
        $itemUnit->delete();
        return $this->sendResponse(new ItemUnitResource($itemUnit), 'Data deleted');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Models\ItemType;
use Illuminate\Http\Request;
use App\Http\Resources\ItemTypeResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Support\Facades\Auth;

class ItemTypeController extends BaseController
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 5);
        $name = $request->input('name');

        $itemType = ItemType::with(['user']);
        if ($name) {
            $itemType->where('name', 'like', '%' . $name . '%');
        }

        return $this->sendResponse($itemType->latest()->paginate(), 'Data fetched');
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
        $itemType = ItemType::create([
            'name' => $input['name'],
            'created_by' => Auth::id()
        ]);
        return $this->sendResponse(new ItemTypeResource($itemType), 'Data created');
    }

    public function show($id)
    {
        $itemType = ItemType::with(['user'])->where('id', $id)->first();
        if (is_null($itemType)) {
            return $this->sendError('Data does not exist.');
        }
        return $this->sendResponse(new ItemTypeResource($itemType), 'Data fetched');
    }

    public function update(Request $request, ItemType $itemType)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        $itemType->name = $input['name'];
        $itemType->save();

        return $this->sendResponse(new ItemTypeResource($itemType), 'Data updated');
    }

    public function destroy(ItemType $itemType)
    {
        $itemType->delete();
        return $this->sendResponse(new ItemTypeResource($itemType), 'Data deleted');
    }
}

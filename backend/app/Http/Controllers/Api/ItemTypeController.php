<?php

namespace App\Http\Controllers\Api;

use App\Models\ItemType;
use Illuminate\Http\Request;
use App\Http\Resources\ItemTypeResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;

class ItemTypeController extends BaseController
{
    public function index()
    {
        $itemType = ItemType::all();
        return $this->sendResponse(ItemTypeResource::collection($itemType), 'Posts fetched');
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
        $itemType = ItemType::create($input);
        return $this->sendResponse(new ItemTypeResource($itemType), 'Post created');
    }

    public function show($id)
    {
        $itemType = ItemType::find($id);
        if(is_null($itemType))
        {
            return $this->sendError('Post does not exist.');
        }
        return $this->sendResponse(new ItemTypeResource($itemType), 'Post fetched');
    }

    public function update(Request $request, ItemType $itemType)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
        ]);
        
        if($validator->fails())
        {
            return $this->sendError($validator->errors());
        }
        $itemType->name = $input['name'];
        $itemType->save();

        return $this->sendResponse(new ItemTypeResource($itemType), 'Post updated');
    }

    public function destroy(ItemType $itemType)
    {
        $itemType->delete();
        return $this->sendResponse([], 'Post deleted');
    }
}

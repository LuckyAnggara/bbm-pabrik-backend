<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Resources\ItemResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;

class ItemController extends BaseController
{

    public function index()
    {
        $item = Item::all();
        return $this->sendResponse(ItemResource::collection($item), 'Posts fetched');
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
        $item = Item::create($input);
        return $this->sendResponse(new ItemResource($item), 'Post created');
    }

    public function show($id)
    {
        $item = Item::find($id);
        if(is_null($item))
        {
            return $this->sendError('Post does not exist.');
        }
        return $this->sendResponse(new ItemResource($item), 'Post fetched');
    }

    public function update(Request $request, Item $item)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
        ]);
        
        if($validator->fails())
        {
            return $this->sendError($validator->errors());
        }
        $item->name = $input['name'];
        $item->save();

        return $this->sendResponse(new ItemResource($item), 'Post updated');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return $this->sendResponse([], 'Post deleted');
    }
}

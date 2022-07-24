<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Resources\ItemResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;

class ItemController extends BaseController
{

    public function index(Request $request)
    {
        $limit = $request->input('limit', 5);
        $name = $request->input('name');

        $item = Item::with(['type','unit','warehouse','user']);
        if($name)
        {
            $item->where('name','like','%'.$name.'%');
        }

        return $this->sendResponse($item->latest()->paginate($limit), 'Data fetched');
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
        return $this->sendResponse(new ItemResource($item), 'Data created');
    }

    public function show($id)
    {
        $item = Item::with(['type','unit','warehouse','user',])->where('id', $id)->first();
        if(is_null($item))
        {
            return $this->sendError('Data does not exist.');
        }
        return $this->sendResponse(new ItemResource($item), 'Data fetched');
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

        return $this->sendResponse(new ItemResource($item), 'Data updated');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return $this->sendResponse([], 'Data deleted');
    }
}

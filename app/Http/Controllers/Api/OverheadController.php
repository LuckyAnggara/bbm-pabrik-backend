<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Overhead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OverheadController extends BaseController
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 5);

        return $this->sendResponse(Overhead::latest()->paginate($limit), 'Data fetched');
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
        $overhead = Overhead::create([
            'name' => $input['name'],
            'unit' => $input['unit'],
            'created_by' => Auth::id()
        ]);
        return $this->sendResponse($overhead, 'Data created');
    }

    public function show($id)
    {
    }

    public function update(Request $request, Overhead $overhead)
    {
    }

    public function destroy(Overhead $overhead)
    {
    }
}

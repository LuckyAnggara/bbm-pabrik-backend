<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Machine;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MachineController extends BaseController
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 5);
        return $this->sendResponse(Machine::latest()->paginate(), 'Data fetched');
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
        $machine = Machine::create([
            'name' => $input['name'],
            'unit' => $input['unit'],
            'usage_capacity' => $input['usage_capacity'],
            'created_by' => Auth::id()
        ]);
        return $this->sendResponse($machine, 'Data created');
    }

    public function show($id)
    {
    }

    public function update(Request $request, Machine $item)
    {
    }

    public function destroy(Machine $machine)
    {
        $machine->delete();
        return $this->sendResponse($machine, 'Data deleted');
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Api\BaseController;
use App\Models\Machine;
use Illuminate\Http\Request;

class MachineController extends BaseController
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 5);

        return $this->sendResponse(Machine::latest()->paginate($limit), 'Data fetched');
    }

    public function store(Request $request)
    {
       
    }

    public function show($id)
    {
        
    }

    public function update(Request $request, Machine $item)
    {
        
    }

    public function destroy(Machine $item)
    {

    }
}

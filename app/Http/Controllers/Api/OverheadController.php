<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Api\BaseController;
use App\Models\Overhead;
use Illuminate\Http\Request;

class OverheadController extends BaseController
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 5);

        return $this->sendResponse(Overhead::latest()->paginate($limit), 'Data fetched');
    }

    public function store(Request $request)
    {
       
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

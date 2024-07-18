<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use App\Models\Sales;
use Illuminate\Http\Request;

class SalesController extends BaseController
{
      public function index(Request $request)
    {
        $data = Sales::all();

        return $this->sendResponse($data, 'Data fetched');
    }
}

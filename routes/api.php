<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\ItemTypeController;
use App\Http\Controllers\Api\ItemUnitController;
use App\Http\Controllers\Api\MutationController;
use App\Http\Controllers\Api\WarehouseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [AuthController::class, 'signin']);
Route::post('register', [AuthController::class, 'signup']);

Route::resource('items', ItemController::class);
Route::resource('item-types', ItemTypeController::class);
Route::resource('item-units', ItemUnitController::class);
Route::resource('warehouses', WarehouseController::class);
Route::resource('mutations', MutationController::class);


Route::middleware('auth:sanctum')->group(function () {
    Route::resource('blogs', BlogController::class);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

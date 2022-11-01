<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\API\ItemExitController;
use App\Http\Controllers\API\ItemIncomingController;
use App\Http\Controllers\Api\ItemTypeController;
use App\Http\Controllers\Api\ItemUnitController;
use App\Http\Controllers\Api\MutationController;
use App\Http\Controllers\API\ProductionOrderController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

// Route::post('login', [AuthController::class, 'signin']);
// Route::post('register', [AuthController::class, 'signup']);


Route::get('mutations/master', [MutationController::class, 'indexMaster']);
Route::get('mutations/master/{id}', [MutationController::class, 'showMaster']);
Route::get('report/production', [ReportController::class, 'reportProduction']);
Route::post('report/production2', [ReportController::class, 'reportProduction2']);
Route::get('report/item', [ReportController::class, 'reportItem']);
Route::get('dashboard/items', [DashboardController::class, 'itemCount']);
Route::get('dashboard/productions', [DashboardController::class, 'productionCount']);


Route::middleware('auth:sanctum')->group(function () {
    Route::resource('items', ItemController::class);
    Route::resource('item-types', ItemTypeController::class);
    Route::resource('item-units', ItemUnitController::class);
    Route::resource('warehouses', WarehouseController::class);
    Route::resource('mutations', MutationController::class);
    Route::resource('production-order', ProductionOrderController::class);

    Route::post('production-order/update-status', [ProductionOrderController::class, 'updateStatus']);
    Route::post('production-order/update-data', [ProductionOrderController::class, 'updateData']);
    Route::post('production-order/update-warehouse', [ProductionOrderController::class, 'updateWarehouse']);
    Route::post('production-order/update-shipping', [ProductionOrderController::class, 'updateShipping']);

    Route::post('mutation-incoming/store', [ItemIncomingController::class, 'store']);
    Route::post('mutation-exit/store', [ItemExitController::class, 'store']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return Auth::user();
});

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::get('logout', 'logout');
});

<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BiayaController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\GajiController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\ItemExitController;
use App\Http\Controllers\Api\ItemIncomingController;
use App\Http\Controllers\Api\ItemTypeController;
use App\Http\Controllers\Api\ItemUnitController;
use App\Http\Controllers\Api\MachineController;
use App\Http\Controllers\Api\MutationController;
use App\Http\Controllers\Api\OverheadController;
use App\Http\Controllers\Api\PegawaiController;
use App\Http\Controllers\Api\PelangganController;
use App\Http\Controllers\Api\ProductionOrderController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\Api\PembelianController;
use App\Http\Controllers\Api\PenjualanController;
use App\Http\Controllers\FakturController;
use App\Http\Controllers\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Api Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Api routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your Api!
|
*/

// Route::post('login', [AuthController::class, 'signin']);
// Route::post('register', [AuthController::class, 'signup']);


Route::get('mutations/master', [MutationController::class, 'indexMaster']);
Route::get('mutations/master/{id}', [MutationController::class, 'showMaster']);
Route::get('report/production', [ReportController::class, 'reportProduction']);
Route::get('report/mutation', [ReportController::class, 'reportMutation']);
Route::get('report/item', [ReportController::class, 'reportItem']);
Route::get('report/gaji/{created_at}', [ReportController::class, 'reportGaji']);

Route::get('dashboard/items', [DashboardController::class, 'itemCount']);
Route::get('dashboard/productions', [DashboardController::class, 'productionCount']);
Route::get('dashboard/shipping', [DashboardController::class, 'shippingCount']);

Route::get('faktur/pembelian/{id}', [FakturController::class, 'pembelian']);
Route::get('faktur/penjualan/{id}', [FakturController::class, 'penjualan']);
Route::get('faktur/print/penjualan/{id}', [FakturController::class, 'makeFaktur']);
Route::get('faktur/print/suratjalan/{id}', [FakturController::class, 'makeSuratJalan']);
Route::get('faktur', [FakturController::class, 'makeFont']);


Route::get('pembelian/faktur', [PembelianController::class, 'generateFaktur']);
Route::get('penjualan/faktur', [PenjualanController::class, 'generateFaktur']);

Route::post('verifikasi/penjualan', [PenjualanController::class, 'verifikasi']);
Route::get('verifikasi/penjualan/{id}', [PenjualanController::class, 'showPenjualan']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/user', [AuthController::class, 'user'])->name('user');
    
    Route::resource('item-types', ItemTypeController::class);
    Route::resource('item-units', ItemUnitController::class);
    Route::resource('machines', MachineController::class);
    Route::resource('overheads', OverheadController::class);
    Route::resource('warehouses', WarehouseController::class);
    Route::resource('mutations', MutationController::class);
    Route::resource('production-order', ProductionOrderController::class);
    Route::resource('items', ItemController::class);
    Route::resource('mutation-incoming', ItemIncomingController::class);
    Route::resource('mutation-exit', ItemExitController::class);
    Route::resource('biaya', BiayaController::class);
    Route::resource('gaji', GajiController::class);

    Route::resource('pegawai', PegawaiController::class);
    Route::resource('pelanggan', PelangganController::class);


    Route::post('production-order/update-status', [ProductionOrderController::class, 'updateStatus']);
    Route::post('production-order/update-data', [ProductionOrderController::class, 'updateData']);
    Route::post('production-order/update-warehouse', [ProductionOrderController::class, 'updateWarehouse']);
    Route::post('production-order/update-shipping', [ProductionOrderController::class, 'updateShipping']);
    Route::post('production-order/retur-shipping', [ProductionOrderController::class, 'returShipping']);
    Route::post('production-order/receive-shipping', [ProductionOrderController::class, 'receiveShipping']);

    Route::post('mutation-exit/store', [ItemExitController::class, 'store']);
    Route::resource('penjualan', PenjualanController::class);

    Route::resource('pembelian', PembelianController::class);
});



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return Auth::user();
});

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::get('logout', 'logout');
});

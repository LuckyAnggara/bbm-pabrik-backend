<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\MutationController;
use App\Http\Controllers\LabaRugiController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/report/persediaan/produksi', [ReportController::class, 'reportProductions'])->name('laporan-produksi');
Route::get('/report/bisnis', [ReportController::class, 'bisnisHome'])->name('bisnis-home');
Route::get('/report/bisnis/labarugi/harian', [ReportController::class, 'reportLabaRugiHarian'])->name('report-laba-rugi-harian');

Route::get('/laba-rugi-harian', [LabaRugiController::class, 'generateLabaRugiHarian'])->name('labarugiharian');

Route::get('/migrate', function () {
    Artisan::call('migrate:refresh --seed --force');
    return 'ok';
});

// Route::get('/fetch', [AbsensiController::class, 'fetchAndStoreData']);
Route::get('/fetch', [AbsensiController::class, 'fetchDaily']);
Route::get('/fetch-pagi', [AbsensiController::class, 'fetchPagi']);
Route::get('/fetch-manual', [AbsensiController::class, 'fetchManual']);


Route::webhooks('paystack/webhook');

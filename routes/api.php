<?php

use App\Http\Controllers\AkunController;
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


Route::get('/all-user', [AkunController::class, 'allUser']);
Route::get('/select/{user}', [AkunController::class, 'selectUser']);
Route::post('/copy-akses', [AkunController::class, 'copyAkses']);
Route::post('/copy-banyak-akses', [AkunController::class, 'copyBuatBanyak']);

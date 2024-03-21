<?php

use App\Http\Controllers\AkunController;
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

Route::get('/', [AkunController::class, 'allUser']);
Route::get('/cari', [AkunController::class, 'allUser']);
Route::get('/akun/{idUser}', [AkunController::class, 'selectUser']);
Route::post('/akun/{idUser}', [AkunController::class, 'editSatuUser']);
Route::post('/copy-akses', [AkunController::class, 'copyAkses']);
Route::get('/delete-user/{user}', [AkunController::class, 'deleteUser']);
Route::get('/update-user/{user}', [AkunController::class, 'gantiPassword']);
Route::post('/copy-akses', [AkunController::class, 'copyAkses']);
Route::post('/buat-akun', [AkunController::class, 'buatUser']);
Route::get('/petugas', [AkunController::class, 'listPetugas']);

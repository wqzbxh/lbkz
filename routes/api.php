<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/index', [\App\Http\Controllers\Xcx\IndexController::class, 'index'])->name('xcx.index');

Route::any('/gzh_check_signature', [\App\Http\Controllers\Gzh\Lbkz::class, 'checkSignature'])->name('gzh.check_signature');

Route::get('/gzh_get_access_token', [\App\Http\Controllers\Gzh\Lbkz::class, 'getAccessToken'])->name('gzh.get_access_token');
Route::get('/gzh_create_menu', [\App\Http\Controllers\Gzh\Lbkz::class, 'createMenu'])->name('gzh.create_menu');





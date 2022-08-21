<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CoinController;
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


Route::get('/',[CoinController::class,'getBalance'])->name('get.balance');
Route::post('/withdraw',[CoinController::class,'postWithdraw'])->name('post.withdraw');

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
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
Route::post('/payment_first', [App\Http\Controllers\PaymentController::class, 'payment_first_method']);
Route::post('/payment_second', [App\Http\Controllers\PaymentController::class, 'payment_second_method']);

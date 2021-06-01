<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\BitrixController;

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

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('customers', CustomerController::class);

Route::get('orders', [OrderController::class, 'index'])->name('orders.index');

Route::delete('orders/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');

Route::get('orders/{id}', [OrderController::class, 'show'])->name('orders.show');

Route::get('bitrix/{id}', [BitrixController::class, 'upload'])->name('bitrix.upload');

Auth::routes();

Route::post('/', [CustomerController::class, 'getClientInformation'])->name('getcustomer');

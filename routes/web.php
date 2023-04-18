<?php

use App\Http\Controllers\PaymentController;
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

Route::get('/paypal_view', [PaymentController::class,'PaymentView'])->name('payment.view');
Route::post('paypal/payment', [PaymentController::class,'payment'])->name('paypal.payment');
Route::get('paypal/success', [PaymentController::class,'success'])->name('paypal.success');
Route::get('paypal/cancel', [PaymentController::class,'cancel'])->name('paypal.cancel');
Route::get('/transaction/index', [PaymentController::class,'index'])->name('transaction.index');

<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Apply throttle middleware to all web routes
Route::middleware('throttle:6000,1')->group(function () {

Route::get('/', function () {
    return view('welcome');
});


// admin route-lar
require __DIR__.'/admin.php';

// payment nəticə səhifələri
Route::view('/payment/success', 'payment.success')->name('payment.success');
Route::view('/payment/cancel', 'payment.cancel')->name('payment.cancel');
Route::view('/payment/decline', 'payment.decline')->name('payment.decline');

}); // Close throttle middleware group


Route::get('/run-currency-update', function () {
    Artisan::call('currencies:update');

    return nl2br(Artisan::output());
});
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// admin route-lar
require __DIR__.'/admin.php';

// payment nəticə səhifələri
Route::view('/payment/success', 'payment.success')->name('payment.success');
Route::view('/payment/cancel', 'payment.cancel')->name('payment.cancel');
Route::view('/payment/decline', 'payment.decline')->name('payment.decline');

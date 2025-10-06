<?php

use App\Http\Controllers\Admin\PaymentController;
Route::get('orders', [PaymentController::class, 'index'])->name('orders.index');

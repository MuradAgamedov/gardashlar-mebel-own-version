<?php
use App\Http\Controllers\Admin\DeliveryPaymentController;

Route::get('delivery-payment/edit', [DeliveryPaymentController::class, 'edit'])->name('delivery-payment.edit');
Route::put('delivery-payment/update', [DeliveryPaymentController::class, 'update'])->name('delivery-payment.update');
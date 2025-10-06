<?php
use App\Http\Controllers\Admin\CreditCardController;

Route::resource('credit-cards', CreditCardController::class);
Route::post('credit-cards/reorder', [CreditCardController::class, 'reorder'])->name('credit-cards.reorder');

<?php
use App\Http\Controllers\Admin\HomePageDiscountController;

Route::get('home-page-discount/edit', [HomePageDiscountController::class, 'edit'])->name('home-page-discount.edit');
Route::put('home-page-discount/update', [HomePageDiscountController::class, 'update'])->name('home-page-discount.update');
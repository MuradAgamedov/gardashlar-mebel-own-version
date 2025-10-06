<?php
use App\Http\Controllers\Admin\HeroCategoryController;
use App\Http\Controllers\Admin\HeroProductController;

Route::resource('hero-categories', HeroCategoryController::class);
Route::post('hero-categories/reorder', [HeroCategoryController::class, 'reorder'])->name('hero-categories.reorder');

Route::resource('hero-products', HeroProductController::class);
Route::post('hero-products/reorder', [HeroProductController::class, 'reorder'])->name('hero-products.reorder');
Route::post('hero-products/search-products', [HeroProductController::class, 'searchProducts'])->name('hero-products.search-products');
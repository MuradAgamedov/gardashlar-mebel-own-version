<?php

use App\Http\Controllers\Admin\PopupAdController;

// PopupAd CRUD
Route::resource('popup-ads', PopupAdController::class);

// PopupAd sıralama (order)
Route::post('popup-ads/reorder', [PopupAdController::class, 'reorder'])
        ->name('popup-ads.reorder');

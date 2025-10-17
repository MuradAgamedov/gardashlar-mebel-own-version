<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SeoPageController;

Route::prefix('seo-pages')->name('seo-pages.')->group(function () {
    Route::get('/', [SeoPageController::class, 'index'])->name('index');
    Route::get('/create', [SeoPageController::class, 'create'])->name('create');
    Route::post('/', [SeoPageController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [SeoPageController::class, 'edit'])->name('edit');
    Route::put('/{id}', [SeoPageController::class, 'update'])->name('update');
    Route::delete('/{id}', [SeoPageController::class, 'destroy'])->name('destroy');
});

<?php

use App\Http\Controllers\Admin\AllSeoPagesController;

Route::get('all-seo-pages', [AllSeoPagesController::class, 'edit'])->name('all-seo-pages.edit');
Route::put('all-seo-pages', [AllSeoPagesController::class, 'update'])->name('all-seo-pages.update');

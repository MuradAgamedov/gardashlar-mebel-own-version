<?php

use App\Http\Controllers\Admin\CatalogController;

Route::resource('catalogs', CatalogController::class);
Route::post('catalogs/reorder', [CatalogController::class, 'reorder'])->name('catalogs.reorder');
<?php

use App\Http\Controllers\Admin\PageController;
Route::resource('pages', PageController::class);
Route::post('pages/generate-slug', [PageController::class, 'generateSlug'])->name('pages.generate-slug');
Route::delete('pages/{page}/opengraph-image', [PageController::class, 'deleteOpenGraphImage'])->name('pages.delete-opengraph-image');
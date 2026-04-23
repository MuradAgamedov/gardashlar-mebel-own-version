<?php
use App\Http\Controllers\Admin\NewsCategoryController;
use App\Http\Controllers\Admin\NewsPostController;
use App\Http\Controllers\Admin\NewsImageController;

Route::resource('news-categories', NewsCategoryController::class);
Route::post('news-categories/reorder', [NewsCategoryController::class, 'reorder'])->name('news-categories.reorder');
Route::post('news-categories/bulk-delete', [NewsCategoryController::class, 'bulkDelete'])->name('news-categories.bulk-delete');

Route::resource('news-posts', NewsPostController::class);
Route::post('news-posts/bulk-delete', [NewsPostController::class, 'bulkDelete'])->name('news-posts.bulk-delete');

Route::get('news-posts/{newsPost}/images', [NewsImageController::class, 'index'])->name('news-posts.images.index');
Route::post('news-posts/{newsPost}/images', [NewsImageController::class, 'store'])->name('news-posts.images.store');
Route::put('news-posts/{newsPost}/images/{newsImage}', [NewsImageController::class, 'update'])->name('news-posts.images.update');
Route::delete('news-posts/{newsPost}/images/{newsImage}', [NewsImageController::class, 'destroy'])->name('news-posts.images.destroy');
Route::post('news-posts/{newsPost}/images/reorder', [NewsImageController::class, 'reorder'])->name('news-posts.images.reorder');

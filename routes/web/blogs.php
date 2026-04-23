<?php
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\BlogImageController;

Route::resource('blog-categories', BlogCategoryController::class);
Route::post('blog-categories/reorder', [BlogCategoryController::class, 'reorder'])->name('blog-categories.reorder');
Route::post('blog-categories/bulk-delete', [BlogCategoryController::class, 'bulkDelete'])->name('blog-categories.bulk-delete');

Route::resource('blog-posts', BlogPostController::class);
Route::post('blog-posts/bulk-delete', [BlogPostController::class, 'bulkDelete'])->name('blog-posts.bulk-delete');

Route::get('blog-posts/{blogPost}/images', [BlogImageController::class, 'index'])->name('blog-posts.images.index');


Route::post('blog-posts/{blogPost}/images', [BlogImageController::class, 'store'])->name('blog-posts.images.store');
Route::put('blog-posts/{blogPost}/images/{blogImage}', [BlogImageController::class, 'update'])->name('blog-posts.images.update');
Route::delete('blog-posts/{blogPost}/images/{blogImage}', [BlogImageController::class, 'destroy'])->name('blog-posts.images.destroy');
Route::post('blog-posts/{blogPost}/images/reorder', [BlogImageController::class, 'reorder'])->name('blog-posts.images.reorder');
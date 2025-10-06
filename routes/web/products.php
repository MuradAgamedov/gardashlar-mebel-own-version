<?php

use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductTypeController;
use App\Http\Controllers\Admin\ProductTypeProductController;
use App\Http\Controllers\Admin\ProductPriceFilterController;
use App\Http\Controllers\Admin\HomePageCategoryController;
use App\Http\Controllers\Admin\ProductPageCategoryController;
use App\Http\Controllers\Admin\ProductColorController;
use App\Http\Controllers\Admin\ProductTagController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductAccordionController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\ProductDetailController;
use App\Http\Controllers\Admin\ProductDetailValueController;
use App\Http\Controllers\Admin\ProductDetailsValuesDetailController;
use App\Http\Controllers\Admin\CommentController;
      
Route::resource('product-categories', ProductCategoryController::class);
Route::post('product-categories/reorder', [ProductCategoryController::class, 'reorder'])->name('product-categories.reorder');
Route::post('product-categories/delete-selected', [ProductCategoryController::class, 'deleteSelected'])->name('product-categories.delete-selected');

Route::resource('product-types', ProductTypeController::class);

Route::get('product-types/{productType}/products', [ProductTypeProductController::class, 'index'])->name('product-type-products.index');
Route::post('product-types/{productType}/products/{product}/toggle-homepage', [ProductTypeProductController::class, 'toggleHomePage'])->name('product-type-products.toggle-homepage');
Route::get('product-types/{productType}/products/search', [ProductTypeProductController::class, 'search'])->name('product-type-products.search');
Route::post('product-types/reorder', [ProductTypeController::class, 'reorder'])->name('product-types.reorder');

Route::resource('product-price-filters', ProductPriceFilterController::class);
Route::post('product-price-filters/reorder', [ProductPriceFilterController::class, 'reorder'])->name('product-price-filters.reorder');


Route::resource('home-page-categories', HomePageCategoryController::class);
Route::post('home-page-categories/reorder', [HomePageCategoryController::class, 'reorder'])->name('home-page-categories.reorder');





// Product Page Categories routes
Route::resource('product-page-categories', ProductPageCategoryController::class);
Route::post('product-page-categories/reorder', [ProductPageCategoryController::class, 'reorder'])->name('product-page-categories.reorder');

// Product Colors routes
Route::delete('product-colors/bulk-delete', [ProductColorController::class, 'bulkDelete'])->name('product-colors.bulk-delete');
Route::resource('product-colors', ProductColorController::class);

// Product Tags routes
Route::resource('product-tags', ProductTagController::class);
Route::post('product-tags/reorder', [ProductTagController::class, 'reorder'])->name('product-tags.reorder');
Route::post('product-tags/generate-slug', [ProductTagController::class, 'generateSlug'])->name('product-tags.generate-slug');

// Products
Route::delete('products/bulk-delete', [ProductController::class, 'bulkDelete'])->name('products.bulk-delete');
Route::post('products/reorder', [ProductController::class, 'reorder'])->name('products.reorder');
Route::post('products/generate-slug', [ProductController::class, 'generateSlug'])->name('products.generate-slug');
Route::resource('products', ProductController::class);

// Product Accordion routes
Route::resource('products.accordions', ProductAccordionController::class);
Route::post('products/{product}/accordions/reorder', [ProductAccordionController::class, 'reorder'])->name('products.accordions.reorder');

Route::resource('products.images', ProductImageController::class);
Route::post('products/{product}/images/reorder', [ProductImageController::class, 'reorder'])->name('products.images.reorder');

Route::resource('products.details', ProductDetailController::class);
Route::post('products/{product}/details/reorder', [ProductDetailController::class, 'reorder'])->name('products.details.reorder');
Route::post('products/{product}/details/bulk-delete', [ProductDetailController::class, 'bulkDelete'])->name('products.details.bulk-delete');

Route::resource('products.detail-values', ProductDetailValueController::class);
Route::post('products/{product}/detail-values/reorder', [ProductDetailValueController::class, 'reorder'])->name('products.detail-values.reorder');
Route::post('products/{product}/detail-values/bulk-delete', [ProductDetailValueController::class, 'bulkDelete'])->name('products.detail-values.bulk-delete');

Route::resource('products.detail-values.details', ProductDetailsValuesDetailController::class);
Route::post('products/{product}/detail-values/{detailValue}/details/reorder', [ProductDetailsValuesDetailController::class, 'reorder'])->name('products.detail-values.details.reorder');
Route::post('products/{product}/detail-values/{detailValue}/details/bulk-delete', [ProductDetailsValuesDetailController::class, 'bulkDelete'])->name('products.detail-values.details.bulk-delete');



Route::resource('images', ImageController::class);
Route::post('products/{product}/images/{image}/set-main', [ProductImageController::class, 'setMainImage'])->name('products.images.set-main');




Route::get('comments', [CommentController::class, 'index'])->name('comments.index');
Route::get('admin/comments/{comment}/read', [CommentController::class, 'markAsRead'])->name('comments.read');



Route::delete('comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
Route::get('admin/comments/{comment}/approve', [CommentController::class, 'toggleApprove'])
    ->name('comments.approve');
// Şərh cavabları
Route::get('admin/comments/{comment}/answers', [CommentController::class, 'answers'])->name('comments.answers');

// Cavab əlavə etmək
Route::post('admin/comments/{comment}/answers', [CommentController::class, 'storeAnswer'])->name('comments.answers.store');

// Cavabı silmək
Route::delete('admin/comments/answers/{answer}', [CommentController::class, 'destroyAnswer'])->name('comments.answers.destroy');
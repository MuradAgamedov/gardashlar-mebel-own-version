<?php
use App\Http\Controllers\Admin\ActionController;


Route::resource('actions', ActionController::class);
Route::get('actions/{action}/products', [ActionController::class, 'products'])->name('actions.products');
Route::post('actions/{action}/add-product', [ActionController::class, 'addProduct'])->name('actions.add-product');
Route::delete('actions/{action}/remove-product', [ActionController::class, 'removeProduct'])->name('actions.remove-product');
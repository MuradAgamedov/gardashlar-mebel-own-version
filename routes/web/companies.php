<?php
use App\Http\Controllers\Admin\CompanyController;

Route::resource('campaigns', CompanyController::class);
Route::post('campaigns/reorder', [CompanyController::class, 'reorder'])->name('companies.reorder');
Route::get('campaigns/{company}/actions', [CompanyController::class, 'actions'])->name('companies.actions');
Route::post('campaigns/{company}/add-product-to-action', [CompanyController::class, 'addProductToAction'])->name('companies.add-product-to-action');
Route::delete('campaigns/{company}/remove-product-from-action', [CompanyController::class, 'removeProductFromAction'])->name('companies.remove-product-from-action');

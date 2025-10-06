<?php
use App\Http\Controllers\Admin\CompanyController;

Route::resource('companies', CompanyController::class);
Route::post('companies/reorder', [CompanyController::class, 'reorder'])->name('companies.reorder');
Route::get('companies/{company}/actions', [CompanyController::class, 'actions'])->name('companies.actions');
Route::post('companies/{company}/add-product-to-action', [CompanyController::class, 'addProductToAction'])->name('companies.add-product-to-action');
Route::delete('companies/{company}/remove-product-from-action', [CompanyController::class, 'removeProductFromAction'])->name('companies.remove-product-from-action');

<?php
use App\Http\Controllers\Admin\CompanyController;

Route::resource('campaigns', CompanyController::class);
Route::post('campaigns/reorder', [CompanyController::class, 'reorder'])->name('campaigns.reorder');
Route::get('campaigns/{company}/actions', [CompanyController::class, 'actions'])->name('campaigns.actions');
Route::post('campaigns/{company}/add-product-to-action', [CompanyController::class, 'addProductToAction'])->name('campaigns.add-product-to-action');
Route::delete('campaigns/{company}/remove-product-from-action', [CompanyController::class, 'removeProductFromAction'])->name('campaigns.remove-product-from-action');

<?php
use App\Http\Controllers\Admin\CompanyController;

Route::resource('campaigns', CompanyController::class)->parameters([
    'campaigns' => 'campaign'
]);
Route::post('campaigns/reorder', [CompanyController::class, 'reorder'])->name('campaigns.reorder');
Route::get('campaigns/{campaign}/actions', [CompanyController::class, 'actions'])->name('campaigns.actions');
Route::post('campaigns/{campaign}/add-product-to-action', [CompanyController::class, 'addProductToAction'])->name('campaigns.add-product-to-action');
Route::delete('campaigns/{campaign}/remove-product-from-action', [CompanyController::class, 'removeProductFromAction'])->name('campaigns.remove-product-from-action');

<?php
use App\Http\Controllers\Admin\SocialNetworkController;

Route::resource('social-networks', SocialNetworkController::class);
Route::post('social-networks/reorder', [SocialNetworkController::class, 'reorder'])->name('social-networks.reorder');
Route::post('social-networks/{socialNetwork}/toggle-status', [SocialNetworkController::class, 'toggleStatus'])->name('social-networks.toggle-status');

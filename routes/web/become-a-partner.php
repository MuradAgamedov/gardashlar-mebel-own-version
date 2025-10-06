<?php
use App\Http\Controllers\Admin\BecomeAPartnerController;
use App\Http\Controllers\Admin\BecomeAPartnerAdvantageController;

Route::get('become-a-partner/edit', [BecomeAPartnerController::class, 'edit'])->name('become-a-partner.edit');
Route::put('become-a-partner/update', [BecomeAPartnerController::class, 'update'])->name('become-a-partner.update');
Route::resource('become-a-partner-advantages', BecomeAPartnerAdvantageController::class);
Route::post('become-a-partner-advantages/reorder', [BecomeAPartnerAdvantageController::class, 'reorder'])->name('become-a-partner-advantages.reorder');
Route::post('become-a-partner-advantages/delete-selected', [BecomeAPartnerAdvantageController::class, 'deleteSelected'])->name('become-a-partner-advantages.delete-selected');
        
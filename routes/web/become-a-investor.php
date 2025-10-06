<?php
use App\Http\Controllers\Admin\BecomeAInvestorController;
use App\Http\Controllers\Admin\BecomeAInvestorAdvantageController;


Route::get('become-a-investor/edit', [BecomeAInvestorController::class, 'edit'])->name('become-a-investor.edit');
Route::put('become-a-investor/update', [BecomeAInvestorController::class, 'update'])->name('become-a-investor.update');

Route::resource('become-a-investor-advantages', BecomeAInvestorAdvantageController::class);
Route::post('become-a-investor-advantages/reorder', [BecomeAInvestorAdvantageController::class, 'reorder'])->name('become-a-investor-advantages.reorder');
Route::post('become-a-investor-advantages/delete-selected', [BecomeAInvestorAdvantageController::class, 'deleteSelected'])->name('become-a-investor-advantages.delete-selected');
        
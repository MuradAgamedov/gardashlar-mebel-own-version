<?php
use App\Http\Controllers\Admin\BecomeAInvestorFormController;

Route::resource('become-a-investor-forms', BecomeAInvestorFormController::class)->only(['index', 'show', 'destroy']);
Route::post('become-a-investor-forms/delete-selected', [BecomeAInvestorFormController::class, 'deleteSelected'])->name('become-a-investor-forms.delete-selected');
Route::post('become-a-investor-forms/{id}/mark-as-read', [BecomeAInvestorFormController::class, 'markAsRead'])->name('become-a-investor-forms.mark-as-read');
        
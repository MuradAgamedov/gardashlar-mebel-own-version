<?php
use App\Http\Controllers\Admin\BecomeAPartnerFormController;

Route::resource('become-a-partner-forms', BecomeAPartnerFormController::class)->only(['index', 'show', 'destroy']);
Route::post('become-a-partner-forms/delete-selected', [BecomeAPartnerFormController::class, 'deleteSelected'])->name('become-a-partner-forms.delete-selected');
Route::post('become-a-partner-forms/{id}/mark-as-read', [BecomeAPartnerFormController::class, 'markAsRead'])->name('become-a-partner-forms.mark-as-read');

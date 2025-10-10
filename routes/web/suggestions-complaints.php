<?php

use App\Http\Controllers\Admin\SuggestionsComplaintsController;
use App\Http\Controllers\Admin\SuggestionsComplaintsFormController;

Route::get('suggestions-complaints/edit', [SuggestionsComplaintsController::class, 'edit'])->name('suggestions-complaints.edit');
Route::put('suggestions-complaints/update', [SuggestionsComplaintsController::class, 'update'])->name('suggestions-complaints.update');

Route::resource('suggestions-complaints-forms', SuggestionsComplaintsFormController::class)
    ->only(['index', 'show', 'destroy']);

Route::post('suggestions-complaints-forms/delete-selected', [SuggestionsComplaintsFormController::class, 'deleteSelected'])->name('suggestions-complaints-forms.delete-selected');
Route::post('suggestions-complaints-forms/{id}/mark-as-read', [SuggestionsComplaintsFormController::class, 'markAsRead'])->name('suggestions-complaints-forms.mark-as-read');

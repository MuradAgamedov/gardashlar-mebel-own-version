<?php
use App\Http\Controllers\Admin\SuggestAPlaceController;
use App\Http\Controllers\Admin\SuggestAPlaceFormController;

Route::get('suggest-a-place/edit', [SuggestAPlaceController::class, 'edit'])->name('suggest-a-place.edit');
Route::put('suggest-a-place/update', [SuggestAPlaceController::class, 'update'])->name('suggest-a-place.update');

Route::resource('suggest-a-place-forms', SuggestAPlaceFormController::class)->only(['index', 'show', 'destroy']);
Route::post('suggest-a-place-forms/delete-selected', [SuggestAPlaceFormController::class, 'deleteSelected'])->name('suggest-a-place-forms.delete-selected');
Route::post('suggest-a-place-forms/{id}/mark-as-read', [SuggestAPlaceFormController::class, 'markAsRead'])->name('suggest-a-place-forms.mark-as-read');

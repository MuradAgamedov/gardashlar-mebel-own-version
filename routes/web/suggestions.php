<?php
use App\Http\Controllers\Admin\SuggestionController;

Route::resource('suggestions', SuggestionController::class);
Route::post('suggestions/reorder', [SuggestionController::class, 'reorder'])->name('suggestions.reorder');
Route::post('suggestions/delete-selected', [SuggestionController::class, 'deleteSelected'])->name('suggestions.deleteSelected');
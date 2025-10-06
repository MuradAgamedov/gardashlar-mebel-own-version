<?php
use App\Http\Controllers\Admin\FaqController;

Route::resource('faqs', FaqController::class);
Route::post('faqs/reorder', [FaqController::class, 'reorder'])->name('faqs.reorder');
Route::delete('faqs/delete-selected', [FaqController::class, 'deleteSelected'])->name('faqs.delete-selected');
Route::post('faqs/delete-selected', [FaqController::class, 'deleteSelected'])->name('faqs.delete-selected.post');
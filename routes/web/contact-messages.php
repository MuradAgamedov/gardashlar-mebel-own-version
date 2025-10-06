<?php
use App\Http\Controllers\Admin\ContactMessageController;


Route::resource('contact-messages', ContactMessageController::class)->only(['index', 'show', 'destroy']);
Route::post('contact-messages/delete-selected', [ContactMessageController::class, 'deleteSelected'])->name('contact-messages.delete-selected');
Route::post('contact-messages/{id}/mark-as-read', [ContactMessageController::class, 'markAsRead'])->name('contact-messages.mark-as-read');
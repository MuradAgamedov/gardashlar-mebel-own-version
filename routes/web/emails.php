<?php

use App\Http\Controllers\Admin\EmailController;
     
Route::resource('emails', EmailController::class);
Route::post('emails/reorder', [EmailController::class, 'reorder'])->name('emails.reorder');
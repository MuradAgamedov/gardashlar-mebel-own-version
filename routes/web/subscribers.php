<?php

use App\Http\Controllers\Admin\SubscriberController;
      
Route::resource('subscribers', SubscriberController::class)->only(['index', 'destroy']);
Route::post('subscribers/bulk-delete', [SubscriberController::class, 'bulkDelete'])->name('subscribers.bulk-delete');
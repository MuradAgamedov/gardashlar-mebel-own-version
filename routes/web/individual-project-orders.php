<?php
use App\Http\Controllers\Admin\IndividualProjectOrderController;

Route::resource('individual-project-orders', IndividualProjectOrderController::class);
Route::post('individual-project-orders/reorder', [IndividualProjectOrderController::class, 'reorder'])->name('individual-project-orders.reorder');
Route::delete('individual-project-orders/delete-selected', [IndividualProjectOrderController::class, 'deleteSelected'])->name('individual-project-orders.delete-selected');
        
<?php
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ServiceAdvantageController;
use App\Http\Controllers\Admin\ServiceStaticDataController;
use App\Http\Controllers\Admin\ServiceFormController;

Route::resource('services', ServiceController::class);
Route::post('services/reorder', [ServiceController::class, 'updateOrder'])->name('services.reorder');
Route::post('services/delete-selected', [ServiceController::class, 'deleteSelected'])->name('services.delete-selected');
Route::post('services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('services.toggle-status');

Route::get('services/{serviceId}/advantages', [ServiceAdvantageController::class, 'index'])->name('service-advantages.index');
Route::get('services/{serviceId}/advantages/create', [ServiceAdvantageController::class, 'create'])->name('service-advantages.create');
Route::post('services/{serviceId}/advantages', [ServiceAdvantageController::class, 'store'])->name('service-advantages.store');
Route::get('services/{serviceId}/advantages/{advantageId}/edit', [ServiceAdvantageController::class, 'edit'])->name('service-advantages.edit');
Route::put('services/{serviceId}/advantages/{advantageId}', [ServiceAdvantageController::class, 'update'])->name('service-advantages.update');
Route::delete('services/{serviceId}/advantages/{advantageId}', [ServiceAdvantageController::class, 'destroy'])->name('service-advantages.destroy');
Route::post('services/{serviceId}/advantages/reorder', [ServiceAdvantageController::class, 'updateOrder'])->name('service-advantages.reorder');
Route::post('services/{serviceId}/advantages/{advantageId}/toggle-status', [ServiceAdvantageController::class, 'toggleStatus'])->name('service-advantages.toggle-status');


Route::get('service-static-data/edit', [ServiceStaticDataController::class, 'edit'])->name('service-static-data.edit');
Route::put('service-static-data/update', [ServiceStaticDataController::class, 'update'])->name('service-static-data.update');



Route::post('service-forms/{id}/mark-as-read', [ServiceFormController::class, 'markAsRead'])->name('service-forms.markAsRead');
Route::post('service-forms/delete-selected', [ServiceFormController::class, 'deleteSelected'])->name('service-forms.deleteSelected');

// Service Forms Routes
Route::resource('service-forms', ServiceFormController::class)->names([
    'index' => 'service-forms.index',
    'show' => 'service-forms.show',
    'destroy' => 'service-forms.destroy'
]);

   
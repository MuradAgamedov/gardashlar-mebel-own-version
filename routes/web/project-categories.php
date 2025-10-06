<?php
use App\Http\Controllers\Admin\ProjectCategoryController;


Route::resource('project-categories', ProjectCategoryController::class)->except(['show']);
Route::post('project-categories/reorder', [ProjectCategoryController::class, 'reorder'])->name('project-categories.reorder');
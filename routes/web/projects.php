<?php
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ProjectVideoController;
use App\Http\Controllers\Admin\ProjectImageController;
use App\Http\Controllers\Admin\HomePageProjectCategoryController;
use App\Http\Controllers\Admin\HomePageProjectController;
use App\Http\Controllers\Admin\ProjectCategoryController;
  
Route::resource('projects', ProjectController::class);
Route::post('projects/reorder', [ProjectController::class, 'reorder'])->name('projects.reorder');
Route::post('projects/bulk-delete', [ProjectController::class, 'bulkDelete'])->name('projects.bulk-delete');

Route::resource('project-categories', ProjectCategoryController::class);
Route::post('project-categories/reorder', [ProjectCategoryController::class, 'reorder'])->name('project-categories.reorder');
Route::post('project-categories/bulk-delete', [ProjectCategoryController::class, 'bulkDelete'])->name('project-categories.bulk-delete');

Route::resource('projects.videos', ProjectVideoController::class);
Route::post('projects/{project}/videos/reorder', [ProjectVideoController::class, 'reorder'])->name('projects.videos.reorder');

Route::resource('projects.images', ProjectImageController::class);
Route::post('projects/{project}/images/reorder', [ProjectImageController::class, 'reorder'])->name('projects.images.reorder');
Route::post('projects/{project}/images/{image}/set-main', [ProjectImageController::class, 'setMainImage'])->name('projects.images.set-main');


Route::get('home-page-projects/get-by-category', [HomePageProjectController::class, 'getProjectsByCategory'])->name('home-page-projects.get-by-category');
Route::resource('home-page-projects', HomePageProjectController::class);
Route::post('home-page-projects/reorder', [HomePageProjectController::class, 'reorder'])->name('home-page-projects.reorder');

Route::resource('home-page-project-categories', HomePageProjectCategoryController::class)->except(['show']);
Route::post('home-page-project-categories/reorder', [HomePageProjectCategoryController::class, 'reorder'])->name('home-page-project-categories.reorder');

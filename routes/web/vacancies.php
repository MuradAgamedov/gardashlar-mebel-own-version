<?php

use App\Http\Controllers\Admin\VacancyController;
use App\Http\Controllers\Admin\VacancyRequirementController;
use App\Http\Controllers\Admin\VacancyFormController;

Route::resource('vacancies', VacancyController::class);
Route::post('vacancies/reorder', [VacancyController::class, 'reorder'])->name('vacancies.reorder');

Route::resource('vacancy-requirements', VacancyRequirementController::class);
Route::get('vacancy-requirements/create/{vacancy_id}', [VacancyRequirementController::class, 'createForVacancy'])->name('vacancy-requirements.create-for-vacancy');
Route::post('vacancy-requirements/reorder', [VacancyRequirementController::class, 'reorder'])->name('vacancy-requirements.reorder');


Route::resource('vacancy-forms', VacancyFormController::class)->only(['index', 'show', 'destroy']);
Route::patch('vacancy-forms/{id}/mark-read', [VacancyFormController::class, 'markAsRead'])->name('vacancy-forms.mark-read');
Route::patch('vacancy-forms/{id}/mark-unread', [VacancyFormController::class, 'markAsUnread'])->name('vacancy-forms.mark-unread');
Route::get('vacancy-forms/{id}/download-file', [VacancyFormController::class, 'downloadFile'])->name('vacancy-forms.download-file');

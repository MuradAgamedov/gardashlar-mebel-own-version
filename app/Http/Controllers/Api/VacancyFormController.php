<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\VacancyForm\StoreVacancyFormRequest;
use App\Interfaces\Services\Front\VacancyFormServiceInterface;
use App\Interfaces\Services\Front\VacancyServiceInterface;
use App\Services\FileService;
use App\Helpers\DictionaryHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VacancyFormController extends Controller
{
    protected FileService $fileService;
    protected VacancyFormServiceInterface $vacancyFormService;
    protected VacancyServiceInterface $vacancyService;

    public function __construct(
        FileService $fileService,
        VacancyFormServiceInterface $vacancyFormService,
        VacancyServiceInterface $vacancyService
    ) {
        $this->fileService = $fileService;
        $this->vacancyFormService = $vacancyFormService;
        $this->vacancyService = $vacancyService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $forms = $this->vacancyFormService->all();
            return $this->success($forms, 'Vacancy forms retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverError('Error retrieving vacancy forms: ' . $e->getMessage());
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $form = $this->vacancyFormService->find((int)$id);
            if (!$form) {
                return $this->notFound('Vacancy form not found');
            }

            if (!$form->is_read) {
                $form->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);
            }

            return $this->success($form, 'Vacancy form retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverError('Error retrieving vacancy form: ' . $e->getMessage());
        }
    }

    public function markAsRead($id): JsonResponse
    {
        try {
            $form = $this->vacancyFormService->find((int)$id);
            if (!$form) {
                return $this->notFound('Vacancy form not found');
            }

            $form->update([
                'is_read' => true,
                'read_at' => now()
            ]);

            return $this->success(null, 'Vacancy form marked as read');
        } catch (\Exception $e) {
            return $this->serverError('Error marking vacancy form as read: ' . $e->getMessage());
        }
    }

    public function store(StoreVacancyFormRequest $request): JsonResponse
    {
        try {
            $lang = $request->header('locale', 'az');

            // Check if vacancy exists and active
            $vacancy = $this->vacancyService->first([], [
                'id' => $request->vacancy_id,
                'status' => true
            ]);

            // Check expiration on vacancy if needed
            if (!$vacancy || ($vacancy->expire_date && \Carbon\Carbon::parse($vacancy->expire_date)->isPast())) {
                return $this->error(DictionaryHelper::trans('vacancy_not_available', $lang), 404);
            }

            // File upload
            $filePath = null;
            if ($request->hasFile('file')) {
                $filePath = $this->fileService->upload(
                    $request->file('file'),
                    'vacancy-forms',
                    [
                        'generate_unique_name' => false,
                        'allowed_types' => ['pdf', 'doc', 'docx'],
                    ]
                );

                if (!$filePath) {
                    return $this->serverError(DictionaryHelper::trans('file_upload_failed', $lang));
                }
            }

            // Create record
            $form = $this->vacancyFormService->create([
                'vacancy_id' => $request->vacancy_id,
                'name' => trim($request->name),
                'mobile' => trim($request->mobile),
                'email' => trim($request->email),
                'message' => trim($request->message),
                'file' => $filePath,
                'is_read' => false,
            ]);

            return $this->created([
                'id' => $form->id,
                'vacancy_id' => $form->vacancy_id,
                'name' => $form->name,
                'mobile' => $form->mobile,
                'email' => $form->email,
                'message' => $form->message,
                'file' => $form->file,
                'created_at' => $form->created_at,
            ], DictionaryHelper::trans('form_submitted_success', $lang));

        } catch (\Exception $e) {
            return $this->serverError(DictionaryHelper::trans('form_submit_error', $request->get('lang', 'az')) . ': ' . $e->getMessage());
        }
    }
}

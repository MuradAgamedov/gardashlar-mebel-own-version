<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SuggestionsComplaintsForm\StoreSuggestionsComplaintsFormRequest;
use App\Http\Resources\Api\SuggestionsComplaintsFormResource;
use App\Interfaces\Services\Front\SuggestionsComplaintsFormServiceInterface;
use App\Services\FileService;
use Illuminate\Http\JsonResponse;
use App\Helpers\DictionaryHelper;

class SuggestionsComplaintsFormController extends Controller
{
    protected FileService $fileService;
    protected SuggestionsComplaintsFormServiceInterface $suggestionsComplaintsFormService;

    public function __construct(
        FileService $fileService,
        SuggestionsComplaintsFormServiceInterface $suggestionsComplaintsFormService
    ) {
        $this->fileService = $fileService;
        $this->suggestionsComplaintsFormService = $suggestionsComplaintsFormService;
    }

    public function store(StoreSuggestionsComplaintsFormRequest $request): JsonResponse
    {
        try {
            $lang = $request->get('lang', 'az');

            $filePath = null;
            if ($request->hasFile('file')) {
                $filePath = $this->fileService->upload(
                    $request->file('file'),
                    'suggestions-complaints-forms',
                    [
                        'generate_unique_name' => false,
                        'allowed_types' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'jpg', 'jpeg', 'png'],
                    ]
                );

                if (!$filePath) {
                    return $this->serverError(DictionaryHelper::trans('file_upload_failed', $lang));
                }
            }

            $form = $this->suggestionsComplaintsFormService->create([
                'name' => trim($request->name),
                'mobile' => trim($request->mobile),
                'email' => trim($request->email),
                'file' => $filePath,
                'message' => trim($request->message),
            ]);

            return $this->created(new SuggestionsComplaintsFormResource($form), DictionaryHelper::trans('form_submitted_success', $lang));

        } catch (\Exception $e) {
            return $this->serverError(DictionaryHelper::trans('form_submit_error', $request->get('lang', 'az')) . ': ' . $e->getMessage());
        }
    }
}

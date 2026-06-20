<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ServiceForm\StoreServiceFormRequest;
use App\Http\Resources\Api\ServiceFormResource;
use App\Interfaces\Services\Front\ServiceServiceInterface;
use App\Interfaces\Services\Front\ServiceFormServiceInterface;
use App\Services\FileService;
use App\Helpers\DictionaryHelper;
use Illuminate\Http\JsonResponse;

class ServiceFormController extends Controller
{
    protected FileService $fileService;
    protected ServiceServiceInterface $serviceService;
    protected ServiceFormServiceInterface $serviceFormService;

    public function __construct(
        FileService $fileService,
        ServiceServiceInterface $serviceService,
        ServiceFormServiceInterface $serviceFormService
    ) {
        $this->fileService = $fileService;
        $this->serviceService = $serviceService;
        $this->serviceFormService = $serviceFormService;
    }

    public function store(StoreServiceFormRequest $request): JsonResponse
    {
        try {
            $lang = $request->get('lang', 'az');

            // Check if service exists and active
            $service = $this->serviceService->first(['status' => true], ['id' => $request->service_id]);
            if (!$service) {
                return $this->notFound(DictionaryHelper::trans('service_not_available', $lang));
            }

            // Handle file upload
            $filePath = null;
            if ($request->hasFile('file')) {
                $filePath = $this->fileService->upload(
                    $request->file('file'),
                    'service-forms',
                    [
                        'generate_unique_name' => false,
                        'allowed_types' => ['pdf', 'doc', 'docx'],
                    ]
                );

                if (!$filePath) {
                    return $this->serverError(DictionaryHelper::trans('file_upload_failed', $lang));
                }
            }

            // Create form submission
            $form = $this->serviceFormService->create([
                'service_id' => $request->service_id,
                'name' => trim($request->name),
                'mobile' => trim($request->mobile),
                'email' => trim($request->email),
                'message' => trim($request->message),
                'file' => $filePath,
                'is_read' => false,
            ]);

            return $this->created(new ServiceFormResource($form), DictionaryHelper::trans('form_submitted_success', $lang));

        } catch (\Exception $e) {
            return $this->serverError(DictionaryHelper::trans('form_submit_error', $request->get('lang', 'az')) . ': ' . $e->getMessage());
        }
    }
}

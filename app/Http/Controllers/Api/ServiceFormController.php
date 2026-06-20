<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ServiceForm\StoreServiceFormRequest;
use App\Models\ServiceForm;
use App\Models\Service;
use App\Services\FileService;
use App\Helpers\DictionaryHelper;

class ServiceFormController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function store(StoreServiceFormRequest $request)
    {
        try {
            $lang = $request->get('lang', 'az');

            // Check if service exists and active
            $service = Service::active()->find($request->service_id);
            if (!$service) {
                return response()->json([
                    'success' => false,
                    'message' => DictionaryHelper::trans('service_not_available', $lang),
                ], 404);
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
                    return response()->json([
                        'success' => false,
                        'message' => DictionaryHelper::trans('file_upload_failed', $lang),
                        'data' => null,
                    ], 500);
                }
            }

            // Create form submission
            $form = ServiceForm::create([
                'service_id' => $request->service_id,
                'name' => trim($request->name),
                'mobile' => trim($request->mobile),
                'email' => trim($request->email),
                'message' => trim($request->message),
                'file' => $filePath,
                'is_read' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => DictionaryHelper::trans('form_submitted_success', $lang),
                'data' => [
                    'id' => $form->id,
                    'service_id' => $form->service_id,
                    'name' => $form->name,
                    'mobile' => $form->mobile,
                    'email' => $form->email,
                    'message' => $form->message,
                    'file' => $form->file,
                    'created_at' => $form->created_at,
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => DictionaryHelper::trans('form_submit_error', $request->get('lang', 'az')) . ': ' . $e->getMessage(),
            ], 500);
        }
    }
}

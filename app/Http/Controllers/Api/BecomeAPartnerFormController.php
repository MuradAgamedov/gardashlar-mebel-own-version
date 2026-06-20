<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BecomeAPartnerForm\StoreBecomeAPartnerFormRequest;
use App\Interfaces\Services\Front\BecomeAPartnerFormServiceInterface;
use App\Services\FileService;
use Illuminate\Http\JsonResponse;

class BecomeAPartnerFormController extends Controller
{
    protected FileService $fileService;
    protected BecomeAPartnerFormServiceInterface $becomeAPartnerFormService;

    public function __construct(
        FileService $fileService,
        BecomeAPartnerFormServiceInterface $becomeAPartnerFormService
    ) {
        $this->fileService = $fileService;
        $this->becomeAPartnerFormService = $becomeAPartnerFormService;
    }

    public function store(StoreBecomeAPartnerFormRequest $request): JsonResponse
    {
        try {
            // Handle file upload
            $filePath = null;
            if ($request->hasFile('file')) {
                $filePath = $this->fileService->upload(
                    $request->file('file'),
                    'become-a-partner-forms',
                    [
                        'generate_unique_name' => false, // Keep original filename
                        'allowed_types' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'],
                    ]
                );
                
                if (!$filePath) {
                    return $this->serverError('Failed to upload file.');
                }
            }

            // Create form
            $form = $this->becomeAPartnerFormService->create([
                'name' => trim($request->name),
                'mobile' => trim($request->mobile),
                'email' => trim($request->email),
                'file' => $filePath,
                'message' => trim($request->message),
            ]);

            return $this->created(['id' => $form->id], 'Form submitted successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error submitting form: ' . $e->getMessage());
        }
    }
}

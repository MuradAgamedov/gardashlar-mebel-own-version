<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BecomeAInvestorForm\StoreBecomeAInvestorFormRequest;
use App\Interfaces\Services\Front\BecomeAInvestorFormServiceInterface;
use App\Services\FileService;
use Illuminate\Http\JsonResponse;

class BecomeAInvestorFormController extends Controller
{
    protected FileService $fileService;
    protected BecomeAInvestorFormServiceInterface $becomeAInvestorFormService;

    public function __construct(
        FileService $fileService,
        BecomeAInvestorFormServiceInterface $becomeAInvestorFormService
    ) {
        $this->fileService = $fileService;
        $this->becomeAInvestorFormService = $becomeAInvestorFormService;
    }

    public function store(StoreBecomeAInvestorFormRequest $request): JsonResponse
    {
        try {
            // Handle file upload
            $filePath = null;
            if ($request->hasFile('file')) {
                $filePath = $this->fileService->upload(
                    $request->file('file'),
                    'become-a-investor-forms',
                    [
                        'generate_unique_name' => false,
                        'allowed_types' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'],
                    ]
                );
            }

            // Create form submission
            $form = $this->becomeAInvestorFormService->create([
                'name' => $request->name,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'message' => $request->message,
                'file' => $filePath,
                'is_read' => false
            ]);

            return $this->created([
                'id' => $form->id,
                'name' => $form->name,
                'mobile' => $form->mobile,
                'email' => $form->email,
                'message' => $form->message,
                'file' => $form->file,
                'created_at' => $form->created_at
            ], 'Form submitted successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error submitting form: ' . $e->getMessage());
        }
    }
}

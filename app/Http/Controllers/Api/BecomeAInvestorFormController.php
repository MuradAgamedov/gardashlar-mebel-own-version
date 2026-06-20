<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BecomeAInvestorForm\StoreBecomeAInvestorFormRequest;
use App\Models\BecomeAInvestorForm;
use App\Services\FileService;


class BecomeAInvestorFormController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

  
    public function store(StoreBecomeAInvestorFormRequest $request)
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
            $form = BecomeAInvestorForm::create([
                'name' => $request->name,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'message' => $request->message,
                'file' => $filePath,
                'is_read' => false
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Form submitted successfully',
                'data' => [
                    'id' => $form->id,
                    'name' => $form->name,
                    'mobile' => $form->mobile,
                    'email' => $form->email,
                    'message' => $form->message,
                    'file' => $form->file,
                    'created_at' => $form->created_at
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error submitting form: ' . $e->getMessage()
            ], 500);
        }
    }
}

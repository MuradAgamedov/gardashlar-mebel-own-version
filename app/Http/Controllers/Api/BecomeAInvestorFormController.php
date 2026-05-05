<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BecomeAInvestorForm;
use App\Models\Service;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Become A Investor Forms",
 *     description="API Endpoints for Become A Investor Forms"
 * )
 */
class BecomeAInvestorFormController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

  
    public function store(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'mobile' => 'required|string|max:20',
                'email' => 'required|email|max:255',
                'message' => 'required|string|max:1000',
                'file' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,txt'
            ], [
                // 'name.required' => 'Name is required',
                // 'mobile.required' => 'Mobile number is required',
                // 'email.required' => 'Email is required',
                // 'email.email' => 'Please enter a valid email address',
                // 'message.required' => 'Message is required',
                // 'file.max' => 'File size must not exceed 10MB',
                // 'file.mimes' => 'File must be PDF, DOC, DOCX, XLS, XLSX, or TXT format'

                'name.required' => dict('name_required'),
                'name.max' => dict('name_max'),
                'mobile.required' => dict('mobile_required'),
                'mobile.max' => dict('mobile_max'),
                'email.required' => dict('email_required'),
                'email.email' => dict('email_email'),
                'email.max' => dict('email_max'),
                'file.file' => dict('file_invalid'),
                'file.mimes' => dict('file_mimes'),
                'file.max' => dict('file_max'),
                'message.required' => dict('message_required'),
                'message.max' => dict('message_max'),
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }



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

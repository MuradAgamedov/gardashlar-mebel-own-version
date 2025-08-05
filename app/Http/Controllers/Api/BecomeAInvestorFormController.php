<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BecomeAInvestorForm;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Become A Investor Form", description="API Endpoints for Become A Investor Form")
 */
class BecomeAInvestorFormController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * @OA\Post(
     *     path="/api/become-a-investor-form",
     *     operationId="storeBecomeAInvestorForm",
     *     tags={"Become A Investor Form"},
     *     summary="Submit become a investor form",
     *     description="Submit a new become a investor form with file upload",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="John Doe", description="Full name"),
     *                 @OA\Property(property="mobile", type="string", example="+994501234567", description="Mobile number"),
     *                 @OA\Property(property="email", type="string", format="email", example="john@example.com", description="Email address"),
     *                 @OA\Property(property="file", type="string", format="binary", description="Uploaded file (optional)"),
     *                 @OA\Property(property="message", type="string", example="I want to become an investor", description="Application message")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Form submitted successfully", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=true), @OA\Property(property="message", type="string", example="Form submitted successfully"), @OA\Property(property="data", type="object", @OA\Property(property="id", type="integer", example=1)))),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=false), @OA\Property(property="message", type="string", example="Validation failed"), @OA\Property(property="errors", type="object"))),
     *     @OA\Response(response=500, description="Internal server error", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=false), @OA\Property(property="message", type="string", example="Error submitting form"), @OA\Property(property="data", type="object")))
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'mobile' => 'required|string|max:20',
                'email' => 'required|email|max:255',
                'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,txt|max:10240',
                'message' => 'required|string|max:2000',
            ], [
                'name.required' => 'Name is required.',
                'name.max' => 'Name may not be greater than 255 characters.',
                'mobile.required' => 'Mobile number is required.',
                'mobile.max' => 'Mobile number may not be greater than 20 characters.',
                'email.required' => 'Email is required.',
                'email.email' => 'Email must be a valid email address.',
                'email.max' => 'Email may not be greater than 255 characters.',
                'file.file' => 'File must be a valid file.',
                'file.mimes' => 'File must be a file of type: pdf, doc, docx, xls, xlsx, txt.',
                'file.max' => 'File may not be greater than 10MB.',
                'message.required' => 'Message is required.',
                'message.max' => 'Message may not be greater than 2000 characters.',
            ]);

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

                if (!$filePath) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload file.',
                        'data' => null
                    ], 500);
                }
            }

            $form = BecomeAInvestorForm::create([
                'name' => trim($request->name),
                'mobile' => trim($request->mobile),
                'email' => trim($request->email),
                'file' => $filePath,
                'message' => trim($request->message),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Form submitted successfully',
                'data' => [
                    'id' => $form->id
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error submitting form: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}

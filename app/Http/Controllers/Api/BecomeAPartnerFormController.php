<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BecomeAPartnerForm\StoreBecomeAPartnerFormRequest;
use App\Models\BecomeAPartnerForm;
use App\Services\FileService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;


class BecomeAPartnerFormController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * @OA\Post(
     *     path="/api/become-a-partner-form",
     *     operationId="storeBecomeAPartnerForm",
     *     tags={"Become A Partner Form"},
     *     summary="Submit become a partner form",
     *     description="Submit a new become a partner form with file upload",
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
     *                 @OA\Property(property="message", type="string", example="I want to become a partner", description="Application message")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Form submitted successfully", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=true), @OA\Property(property="message", type="string", example="Form submitted successfully"), @OA\Property(property="data", type="object", @OA\Property(property="id", type="integer", example=1)))),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=false), @OA\Property(property="message", type="string", example="Validation failed"), @OA\Property(property="errors", type="object"))),
     *     @OA\Response(response=500, description="Internal server error", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=false), @OA\Property(property="message", type="string", example="Error submitting form"), @OA\Property(property="data", type="object")))
     * )
     */
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
            $form = BecomeAPartnerForm::create([
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

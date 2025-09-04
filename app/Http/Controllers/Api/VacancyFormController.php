<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VacancyForm;
use App\Models\Vacancy;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Vacancy Forms",
 *     description="API Endpoints for Vacancy Forms"
 * )
 */
class VacancyFormController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * @OA\Post(
     *     path="/api/vacancy-forms",
     *     summary="Submit vacancy application form",
     *     description="Submit a new vacancy application form with file upload",
     *     tags={"Vacancy Forms"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"vacancy_id", "name", "mobile", "email", "message"},
     *                 @OA\Property(property="vacancy_id", type="integer", example=1, description="Vacancy ID"),
     *                 @OA\Property(property="name", type="string", example="John Doe", description="Full name"),
     *                 @OA\Property(property="mobile", type="string", example="+994501234567", description="Mobile number"),
     *                 @OA\Property(property="email", type="string", format="email", example="john@example.com", description="Email address"),
     *                 @OA\Property(property="message", type="string", example="I want to apply for this position", description="Application message"),
     *                 @OA\Property(property="file", type="string", format="binary", description="Optional file upload (PDF, DOC, DOCX, max 10MB)")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Form submitted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Form submitted successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="vacancy_id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="mobile", type="string", example="+994501234567"),
     *                 @OA\Property(property="email", type="string", example="john@example.com"),
     *                 @OA\Property(property="message", type="string", example="I want to apply for this position"),
     *                 @OA\Property(property="file", type="string", example="vacancy-forms/file.pdf"),
     *                 @OA\Property(property="created_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Vacancy not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Vacancy not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error submitting form")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'vacancy_id' => 'required|integer|exists:vacancies,id',
                'name' => 'required|string|max:255',
                'mobile' => 'required|string|max:20',
                'email' => 'required|email|max:255',
                'message' => 'required|string|max:1000',
                'file' => 'nullable|file|max:10240|mimes:pdf,doc,docx'
            ], [
                'vacancy_id.required' => 'Vacancy ID is required',
                'vacancy_id.exists' => 'Vacancy not found',
                'name.required' => 'Name is required',
                'mobile.required' => 'Mobile number is required',
                'email.required' => 'Email is required',
                'email.email' => 'Please enter a valid email address',
                'message.required' => 'Message is required',
                'file.max' => 'File size must not exceed 10MB',
                'file.mimes' => 'File must be PDF, DOC, or DOCX format'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if vacancy exists and is active
            $vacancy = Vacancy::where('id', $request->vacancy_id)
                ->active()
                ->notExpired()
                ->first();

            if (!$vacancy) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vacancy not found or not available for applications'
                ], 404);
            }

            // Handle file upload
            $filePath = null;
            if ($request->hasFile('file')) {
                $filePath = $this->fileService->upload($request->file('file'), 'vacancy-forms');
            }

            // Create form submission
            $form = VacancyForm::create([
                'vacancy_id' => $request->vacancy_id,
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
                    'vacancy_id' => $form->vacancy_id,
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

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SuggestAPlaceForm;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Suggest A Place Form", description="API Endpoints for Suggest A Place Form")
 */
class SuggestAPlaceFormController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * @OA\Post(
     *     path="/api/suggest-a-place-form",
     *     operationId="storeSuggestAPlaceForm",
     *     tags={"Suggest A Place Form"},
     *     summary="Submit suggest a place form",
     *     description="Submit a new suggest a place form with file upload",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="name_surname", type="string", example="John Doe", description="Full name and surname"),
     *                 @OA\Property(property="email", type="string", format="email", example="john@example.com", description="Email address"),
     *                 @OA\Property(property="area_sqm", type="string", example="150", description="Area in square meters"),
     *                 @OA\Property(property="mobile", type="string", example="+994501234567", description="Mobile number"),
     *                 @OA\Property(property="address", type="string", example="Baku, Azerbaijan", description="Address"),
     *                 @OA\Property(property="floor_count", type="string", example="3", description="Number of floors"),
     *                 @OA\Property(property="is_repaired", type="boolean", example=true, description="Repair status (true=repaired, false=unrepaired)"),
     *                 @OA\Property(property="has_warehouse", type="boolean", example=true, description="Has warehouse area (true=yes, false=no)"),
     *                 @OA\Property(property="has_parking", type="boolean", example=false, description="Has parking (true=yes, false=no)"),
     *                 @OA\Property(property="details", type="string", example="Detailed description of the place", description="Detailed information"),
     *                 @OA\Property(property="file", type="string", format="binary", description="Uploaded file (optional)")
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
                'name_surname' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'area_sqm' => 'required|string|max:50',
                'mobile' => 'required|string|max:20',
                'address' => 'required|string|max:500',
                'floor_count' => 'required|string|max:50',
                'is_repaired' => 'required|boolean',
                'has_warehouse' => 'required|boolean',
                'has_parking' => 'required|boolean',
                'details' => 'required|string|max:2000',
                'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,txt,jpg,jpeg,png|max:10240',
            ], [
                'name_surname.required' => 'Name and surname is required.',
                'name_surname.max' => 'Name and surname may not be greater than 255 characters.',
                'email.required' => 'Email is required.',
                'email.email' => 'Email must be a valid email address.',
                'email.max' => 'Email may not be greater than 255 characters.',
                'area_sqm.required' => 'Area is required.',
                'area_sqm.max' => 'Area may not be greater than 50 characters.',
                'mobile.required' => 'Mobile number is required.',
                'mobile.max' => 'Mobile number may not be greater than 20 characters.',
                'address.required' => 'Address is required.',
                'address.max' => 'Address may not be greater than 500 characters.',
                'floor_count.required' => 'Floor count is required.',
                'floor_count.max' => 'Floor count may not be greater than 50 characters.',
                'is_repaired.required' => 'Repair status is required.',
                'is_repaired.boolean' => 'Repair status must be true or false.',
                'has_warehouse.required' => 'Warehouse status is required.',
                'has_warehouse.boolean' => 'Warehouse status must be true or false.',
                'has_parking.required' => 'Parking status is required.',
                'has_parking.boolean' => 'Parking status must be true or false.',
                'details.required' => 'Details are required.',
                'details.max' => 'Details may not be greater than 2000 characters.',
                'file.file' => 'File must be a valid file.',
                'file.mimes' => 'File must be a file of type: pdf, doc, docx, xls, xlsx, txt, jpg, jpeg, png.',
                'file.max' => 'File may not be greater than 10MB.',
            ]);

            $filePath = null;
            if ($request->hasFile('file')) {
                $filePath = $this->fileService->upload(
                    $request->file('file'),
                    'suggest-a-place-forms',
                    [
                        'generate_unique_name' => false,
                        'allowed_types' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'jpg', 'jpeg', 'png'],
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

            $form = SuggestAPlaceForm::create([
                'name_surname' => trim($request->name_surname),
                'email' => trim($request->email),
                'area_sqm' => trim($request->area_sqm),
                'mobile' => trim($request->mobile),
                'address' => trim($request->address),
                'floor_count' => trim($request->floor_count),
                'is_repaired' => $request->boolean('is_repaired'),
                'has_warehouse' => $request->boolean('has_warehouse'),
                'has_parking' => $request->boolean('has_parking'),
                'details' => trim($request->details),
                'file' => $filePath,
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

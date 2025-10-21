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

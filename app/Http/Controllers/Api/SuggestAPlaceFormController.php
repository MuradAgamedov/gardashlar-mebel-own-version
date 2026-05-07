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
            $lang = $request->get('lang', 'az'); // İstəyə görə lang parametri ilə tərcümə dilini götürürük

            $request->validate([
                'name_surname' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'area_sqm' => 'required|string|max:50',
                'mobile' => [
                    'required',
                    'string',
                    'max:20',
                    'regex:/^(?:\+\d{1,3}[-\s]?)?(?:\(?\d{2,4}\)?[-\s]?){2,4}\d{2,4}$/'
                ],
                'address' => 'required|string|max:500',
                'floor_count' => 'required|string|max:50',
                'is_repaired' => 'required|boolean',
                'has_warehouse' => 'required|boolean',
                'has_parking' => 'required|boolean',
                'details' => 'required|string|max:2000',
                'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,txt,jpg,jpeg,png|max:10240',
            ], [
                'name_surname.required' => dict('name_surname_required'),
                'name_surname.max' => dict('name_surname_max'),

                'email.required' => dict('email_required'),
                'email.email' => dict('email_email'),
                'email.max' => dict('email_max'),

                'area_sqm.required' => dict('area_required'),
                'area_sqm.max' => dict('area_max'),

                'mobile.required' => dict('mobile_required'),
                'mobile.max' => dict('mobile_max'),
                'mobile.regex' => dict('mobile_invalid'),

                'address.required' => dict('address_required'),
                'address.max' => dict('address_max'),

                'floor_count.required' => dict('floor_count_required'),
                'floor_count.max' => dict('floor_count_max'),

                'is_repaired.required' => dict('is_repaired_required'),
                'is_repaired.boolean' => dict('is_repaired_boolean'),

                'has_warehouse.required' => dict('has_warehouse_required'),
                'has_warehouse.boolean' => dict('has_warehouse_boolean'),

                'has_parking.required' => dict('has_parking_required'),
                'has_parking.boolean' => dict('has_parking_boolean'),

                'details.required' => dict('details_required'),
                'details.max' => dict('details_max'),

                'file.file' => dict('file_invalid'),
                'file.mimes' => dict('file_mimes'),
                'file.max' => dict('file_max'),
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
                        'message' => dict('file_upload_failed'),
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
                'message' => dict('form_submitted_success'),
                'data' => [
                    'id' => $form->id
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => dict('validation_failed', $request->get('lang', 'az')),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => dict('form_submit_error', $request->get('lang', 'az')) . ': ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}

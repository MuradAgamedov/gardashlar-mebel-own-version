<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SuggestAPlaceForm;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Helpers\DictionaryHelper;
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
                'name_surname.required' => DictionaryHelper::trans('name_surname_required', $lang),
                'name_surname.max' => DictionaryHelper::trans('name_surname_max', $lang),

                'email.required' => DictionaryHelper::trans('email_required', $lang),
                'email.email' => DictionaryHelper::trans('email_email', $lang),
                'email.max' => DictionaryHelper::trans('email_max', $lang),

                'area_sqm.required' => DictionaryHelper::trans('area_required', $lang),
                'area_sqm.max' => DictionaryHelper::trans('area_max', $lang),

                'mobile.required' => DictionaryHelper::trans('mobile_required', $lang),
                'mobile.max' => DictionaryHelper::trans('mobile_max', $lang),
                'mobile.regex' => DictionaryHelper::trans('mobile_invalid', $lang),

                'address.required' => DictionaryHelper::trans('address_required', $lang),
                'address.max' => DictionaryHelper::trans('address_max', $lang),

                'floor_count.required' => DictionaryHelper::trans('floor_count_required', $lang),
                'floor_count.max' => DictionaryHelper::trans('floor_count_max', $lang),

                'is_repaired.required' => DictionaryHelper::trans('is_repaired_required', $lang),
                'is_repaired.boolean' => DictionaryHelper::trans('is_repaired_boolean', $lang),

                'has_warehouse.required' => DictionaryHelper::trans('has_warehouse_required', $lang),
                'has_warehouse.boolean' => DictionaryHelper::trans('has_warehouse_boolean', $lang),

                'has_parking.required' => DictionaryHelper::trans('has_parking_required', $lang),
                'has_parking.boolean' => DictionaryHelper::trans('has_parking_boolean', $lang),

                'details.required' => DictionaryHelper::trans('details_required', $lang),
                'details.max' => DictionaryHelper::trans('details_max', $lang),

                'file.file' => DictionaryHelper::trans('file_invalid', $lang),
                'file.mimes' => DictionaryHelper::trans('file_mimes', $lang),
                'file.max' => DictionaryHelper::trans('file_max', $lang),
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
                        'message' => DictionaryHelper::trans('file_upload_failed', $lang),
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
                'message' => DictionaryHelper::trans('form_submitted_success', $lang),
                'data' => [
                    'id' => $form->id
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => DictionaryHelper::trans('validation_failed', $request->get('lang', 'az')),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => DictionaryHelper::trans('form_submit_error', $request->get('lang', 'az')) . ': ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}

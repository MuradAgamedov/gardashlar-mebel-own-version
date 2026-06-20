<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SuggestAPlaceForm\StoreSuggestAPlaceFormRequest;
use App\Models\SuggestAPlaceForm;
use App\Services\FileService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class SuggestAPlaceFormController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function store(StoreSuggestAPlaceFormRequest $request): JsonResponse
    {
        try {
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

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => dict('form_submit_error', $request->get('lang', 'az')) . ': ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}

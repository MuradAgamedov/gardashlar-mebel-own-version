<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SuggestionsComplaintsForm;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Helpers\DictionaryHelper;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Suggestions & Complaints Form", description="API Endpoints for Suggestions & Complaints Form")
 */
class SuggestionsComplaintsFormController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $lang = $request->get('lang', 'az');

            $request->validate([
                'name' => 'required|string|max:255',
                'mobile' => [
                    'required',
                    'string',
                    'max:20',
                    'regex:/^(?:\+\d{1,3}[-\s]?)?(?:\(?\d{2,4}\)?[-\s]?){2,4}\d{2,4}$/'
                ],
                'email' => 'required|email|max:255',
                'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,txt,jpg,jpeg,png|max:10240',
                'message' => 'required|string|max:2000',
            ], [
                'name.required' => DictionaryHelper::trans('name_required', $lang),
                'name.max' => DictionaryHelper::trans('name_max', $lang),

                'mobile.required' => DictionaryHelper::trans('mobile_required', $lang),
                'mobile.max' => DictionaryHelper::trans('mobile_max', $lang),
                'mobile.regex' => DictionaryHelper::trans('mobile_invalid', $lang),

                'email.required' => DictionaryHelper::trans('email_required', $lang),
                'email.email' => DictionaryHelper::trans('email_email', $lang),
                'email.max' => DictionaryHelper::trans('email_max', $lang),

                'file.file' => DictionaryHelper::trans('file_invalid', $lang),
                'file.mimes' => DictionaryHelper::trans('file_mimes', $lang),
                'file.max' => DictionaryHelper::trans('file_max', $lang),

                'message.required' => DictionaryHelper::trans('message_required', $lang),
                'message.max' => DictionaryHelper::trans('message_max', $lang),
            ]);

            $filePath = null;
            if ($request->hasFile('file')) {
                $filePath = $this->fileService->upload(
                    $request->file('file'),
                    'suggestions-complaints-forms',
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

            $form = SuggestionsComplaintsForm::create([
                'name' => trim($request->name),
                'mobile' => trim($request->mobile),
                'email' => trim($request->email),
                'file' => $filePath,
                'message' => trim($request->message),
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

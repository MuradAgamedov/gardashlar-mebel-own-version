<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceForm;
use App\Models\Service;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\DictionaryHelper;

class ServiceFormController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function store(Request $request)
    {
        try {
            $lang = $request->get('lang', 'az');

            // Validate request
            $validator = Validator::make($request->all(), [
                'service_id' => 'required|integer|exists:services,id',
                'name' => 'required|string|max:255',
                'mobile' => [
                    'required',
                    'string',
                    'max:20',
                    'regex:/^(?:\+\d{1,3}[-\s]?)?(?:\(?\d{2,4}\)?[-\s]?){2,4}\d{2,4}$/'
                ],
                'email' => 'required|email|max:255',
                'message' => 'required|string|max:1000',
                'file' => 'nullable|file|max:10240|mimes:pdf,doc,docx',
            ], [
                'service_id.required' => DictionaryHelper::trans('service_id_required', $lang),
                'service_id.exists' => DictionaryHelper::trans('service_not_found', $lang),

                'name.required' => DictionaryHelper::trans('name_required', $lang),
                'name.max' => DictionaryHelper::trans('name_max', $lang),

                'mobile.required' => DictionaryHelper::trans('mobile_required', $lang),
                'mobile.max' => DictionaryHelper::trans('mobile_max', $lang),
                'mobile.regex' => DictionaryHelper::trans('mobile_invalid', $lang),

                'email.required' => DictionaryHelper::trans('email_required', $lang),
                'email.email' => DictionaryHelper::trans('email_email', $lang),
                'email.max' => DictionaryHelper::trans('email_max', $lang),

                'message.required' => DictionaryHelper::trans('message_required', $lang),
                'message.max' => DictionaryHelper::trans('message_max', $lang),

                'file.max' => DictionaryHelper::trans('file_max', $lang),
                'file.mimes' => DictionaryHelper::trans('file_mimes', $lang),
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => DictionaryHelper::trans('validation_failed', $lang),
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Check if service exists and active
            $service = Service::active()->find($request->service_id);
            if (!$service) {
                return response()->json([
                    'success' => false,
                    'message' => DictionaryHelper::trans('service_not_available', $lang),
                ], 404);
            }

            // Handle file upload
            $filePath = null;
            if ($request->hasFile('file')) {
                $filePath = $this->fileService->upload(
                    $request->file('file'),
                    'service-forms',
                    [
                        'generate_unique_name' => false,
                        'allowed_types' => ['pdf', 'doc', 'docx'],
                    ]
                );

                if (!$filePath) {
                    return response()->json([
                        'success' => false,
                        'message' => DictionaryHelper::trans('file_upload_failed', $lang),
                        'data' => null,
                    ], 500);
                }
            }

            // Create form submission
            $form = ServiceForm::create([
                'service_id' => $request->service_id,
                'name' => trim($request->name),
                'mobile' => trim($request->mobile),
                'email' => trim($request->email),
                'message' => trim($request->message),
                'file' => $filePath,
                'is_read' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => DictionaryHelper::trans('form_submitted_success', $lang),
                'data' => [
                    'id' => $form->id,
                    'service_id' => $form->service_id,
                    'name' => $form->name,
                    'mobile' => $form->mobile,
                    'email' => $form->email,
                    'message' => $form->message,
                    'file' => $form->file,
                    'created_at' => $form->created_at,
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => DictionaryHelper::trans('form_submit_error', $request->get('lang', 'az')) . ': ' . $e->getMessage(),
            ], 500);
        }
    }
}

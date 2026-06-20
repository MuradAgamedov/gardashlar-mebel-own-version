<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhoneType;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\PhoneTypeServiceInterface;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use App\Http\Requests\Admin\PhoneType\StorePhoneTypeRequest;
use App\Http\Requests\Admin\PhoneType\UpdatePhoneTypeRequest;

class PhoneTypeController extends Controller
{
    public function __construct(
        protected PhoneTypeServiceInterface $phoneTypeService,
        protected LanguageServiceInterface $languageService
    )
    {
    }

    public function index()
    {
        $phoneTypes = $this->phoneTypeService->getPhoneTypes();
        $languages = $this->languageService->getAllLanguages();
        return view('admin.phone-types.index', compact('phoneTypes', 'languages'));
    }

    public function create()
    {
        $languages = $this->languageService->getAllLanguages();
        return view('admin.phone-types.create', compact('languages'));
    }

    public function store(StorePhoneTypeRequest $request)
    {
        try {
            $languages = $this->languageService->getAllLanguages();
            
            $this->phoneTypeService->createPhoneType(
                ['status' => $request->input('status', false)],
                $request->input('translations'),
                $languages
            );

            return redirect()->route('admin.phones.index')
                ->with('success', __('Phone type created successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error creating phone type: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }

    public function edit(PhoneType $phoneType)
    {
        $phoneType->load('translates.language');
        $languages = $this->languageService->getAllLanguages();
        return view('admin.phone-types.edit', compact('phoneType', 'languages'));
    }

    public function update(UpdatePhoneTypeRequest $request, PhoneType $phoneType)
    {
        try {
            $languages = $this->languageService->getAllLanguages();
            
            $this->phoneTypeService->updatePhoneType(
                $phoneType,
                ['status' => $request->input('status', false)],
                $request->input('translations'),
                $languages
            );

            return redirect()->route('admin.phones.index')
                ->with('success', __('Phone type updated successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error updating phone type: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }

    public function destroy(PhoneType $phoneType)
    {
        try {
            $this->phoneTypeService->deletePhoneType($phoneType);
            return redirect()->route('admin.phones.index')
                ->with('success', __('Phone type deleted successfully!'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error deleting phone type: :error', ['error' => $e->getMessage()]));
        }
    }

    public function reorder(Request $request)
    {
        try {
            $this->phoneTypeService->reorderPhoneTypes($request->input('phone_types', []));
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
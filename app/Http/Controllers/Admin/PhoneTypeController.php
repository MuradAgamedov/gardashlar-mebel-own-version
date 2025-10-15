<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhoneType;
use Illuminate\Http\Request;
use App\Repositories\PhoneTypeRepository;
use App\Repositories\LanguageRepository;
use App\Http\Requests\Admin\PhoneType\StorePhoneTypeRequest;
use App\Http\Requests\Admin\PhoneType\UpdatePhoneTypeRequest;

class PhoneTypeController extends Controller
{
    public function __construct(
        public PhoneTypeRepository $repository,
        public LanguageRepository $languageRepository
    )
    {
    }

    public function index()
    {
        $phoneTypes = $this->repository->getPhoneTypes();
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.phone-types.index', compact('phoneTypes', 'languages'));
    }

    public function create()
    {
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.phone-types.create', compact('languages'));
    }

    public function store(StorePhoneTypeRequest $request)
    {
        try {
            $languages = $this->languageRepository->getAllLanguages();
            
            $this->repository->createPhoneType(
                ['status' => $request->input('status', false)],
                $request->input('translations'),
                $languages
            );

            return redirect()->route('admin.phones.index')
                ->with('success', 'Phone type created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating phone type: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(PhoneType $phoneType)
    {
        $phoneType->load('translates.language');
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.phone-types.edit', compact('phoneType', 'languages'));
    }

    public function update(UpdatePhoneTypeRequest $request, PhoneType $phoneType)
    {
        try {
            $languages = $this->languageRepository->getAllLanguages();
            
            $this->repository->updatePhoneType(
                $phoneType,
                ['status' => $request->input('status', false)],
                $request->input('translations'),
                $languages
            );

            return redirect()->route('admin.phones.index')
                ->with('success', 'Phone type updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating phone type: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(PhoneType $phoneType)
    {
        try {
            $this->repository->deletePhoneType($phoneType);
            return redirect()->route('admin.phones.index')
                ->with('success', 'Phone type deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting phone type: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        try {
            $this->repository->reorderPhoneTypes($request->input('phone_types', []));
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
} 
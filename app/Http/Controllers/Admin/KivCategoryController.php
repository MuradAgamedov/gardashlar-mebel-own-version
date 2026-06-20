<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\KivCategory\ReorderKivCategoryRequest;

use App\Http\Controllers\Controller;
use App\Models\KivCategory;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\KivCategoryServiceInterface;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use App\Http\Requests\Admin\KivCategory\StoreKivCategoryRequest;
use App\Http\Requests\Admin\KivCategory\UpdateKivCategoryRequest;

class KivCategoryController extends Controller
{
    public function __construct(
        protected KivCategoryServiceInterface $kivCategoryService,
        protected LanguageServiceInterface $languageService
    )
    {
    }

    public function index()
    {
        $categories = $this->kivCategoryService->getCategories();
        $languages = $this->languageService->getAllLanguages();
        return view('admin.kiv-categories.index', compact('categories', 'languages'));
    }

    public function create()
    {
        $languages = $this->languageService->getAllLanguages();
        return view('admin.kiv-categories.create', compact('languages'));
    }

    public function store(StoreKivCategoryRequest $request)
    {
        $languages = $this->languageService->getAllLanguages();
        
        $this->kivCategoryService->createKivCategory(
            ['status' => $request->input('status')],
            $request->input('translations'),
            $languages
        );

        return redirect()->route('admin.kiv-categories.index')
            ->with('success', 'KIV Category created successfully!');
    }

    public function edit(KivCategory $kivCategory)
    {
        $kivCategory->load('translates.language');
        $languages = $this->languageService->getAllLanguages();
        return view('admin.kiv-categories.edit', compact('kivCategory', 'languages'));
    }

    public function update(UpdateKivCategoryRequest $request, KivCategory $kivCategory)
    {
        $languages = $this->languageService->getAllLanguages();
        
        $this->kivCategoryService->updateKivCategory(
            $kivCategory,
            ['status' => $request->input('status')],
            $request->input('translations'),
            $languages
        );

        return redirect()->route('admin.kiv-categories.index')
            ->with('success', 'KIV Category updated successfully!');
    }

    public function destroy(KivCategory $kivCategory)
    {
        try {
            $this->kivCategoryService->deleteKivCategory($kivCategory);
            return redirect()->route('admin.kiv-categories.index')
                ->with('success', 'KIV Category deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting KIV category: ' . $e->getMessage());
        }
    }

    public function reorder(ReorderKivCategoryRequest $request)
    {
        $request->validated();

        try {
            $this->kivCategoryService->reorderKivCategories($request->categories);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
} 
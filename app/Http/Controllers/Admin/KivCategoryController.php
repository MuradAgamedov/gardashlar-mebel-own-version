<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KivCategory;
use Illuminate\Http\Request;
use App\Repositories\KivCategoryRepository;
use App\Repositories\LanguageRepository;
use App\Http\Requests\Admin\KivCategory\StoreKivCategoryRequest;
use App\Http\Requests\Admin\KivCategory\UpdateKivCategoryRequest;

class KivCategoryController extends Controller
{
    public function __construct(
        public KivCategoryRepository $repository,
        public LanguageRepository $languageRepository
    )
    {
    }

    public function index()
    {
        $categories = $this->repository->getCategories();
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.kiv-categories.index', compact('categories', 'languages'));
    }

    public function create()
    {
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.kiv-categories.create', compact('languages'));
    }

    public function store(StoreKivCategoryRequest $request)
    {
        $languages = $this->languageRepository->getAllLanguages();
        
        $this->repository->createKivCategory(
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
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.kiv-categories.edit', compact('kivCategory', 'languages'));
    }

    public function update(UpdateKivCategoryRequest $request, KivCategory $kivCategory)
    {
        $languages = $this->languageRepository->getAllLanguages();
        
        $this->repository->updateKivCategory(
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
            $this->repository->deleteKivCategory($kivCategory);
            return redirect()->route('admin.kiv-categories.index')
                ->with('success', 'KIV Category deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting KIV category: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*' => 'required|integer|exists:kiv_categories,id',
        ]);

        try {
            $this->repository->reorderKivCategories($request->categories);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
} 
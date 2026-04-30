<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Repositories\ProductTypeRepository;
use App\Repositories\LanguageRepository;

class ProductTypeController extends Controller
{
    public function __construct(
        public ProductTypeRepository $repository,
        public LanguageRepository $languageRepository
    )
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $types = $this->repository->getTypes($request);
        return view('admin.product-types.index', compact('types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->languageRepository->getActiveLanguages();
        return view('admin.product-types.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'status' => 'nullable',
            'translates' => 'required|array|min:1',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
        ], [
            'translates.required' => 'At least one language translation is required.',
            'translates.*.title.required' => 'Title is required for all languages.',
        ]);

        try {
            // Create the type
            $type = ProductType::create([
                'status' => $request->has('status') ? true : false,
            ]);

            // Create translations for each language
            foreach ($request->translates as $translate) {
                $type->translates()->create([
                    'language_id' => $translate['language_id'],
                    'title' => trim($translate['title']),
                ]);
            }

            return redirect()->route('admin.product-types.index')
                ->with('success', __('Product type created successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error creating product type: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductType $productType)
    {
        $productType->load('translates.language');
        return view('admin.product-types.show', compact('productType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductType $productType)
    {
        $productType->load('translates.language');
        $languages = $this->languageRepository->getActiveLanguages();
        return view('admin.product-types.edit', compact('productType', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductType $productType)
    {
        // Validate the request
        $request->validate([
            'status' => 'nullable',
            'translates' => 'required|array|min:1',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
        ], [
            'translates.required' => 'At least one language translation is required.',
            'translates.*.title.required' => 'Title is required for all languages.',
        ]);

        try {
            // Update the type
            $productType->update([
                'status' => $request->has('status') ? true : false,
            ]);

            // Update translations for each language
            foreach ($request->translates as $translate) {
                $translation = $productType->translates()
                    ->where('language_id', $translate['language_id'])
                    ->first();

                if ($translation) {
                    $translation->update([
                        'title' => trim($translate['title']),
                    ]);
                } else {
                    // Create new translation if doesn't exist
                    $productType->translates()->create([
                        'language_id' => $translate['language_id'],
                        'title' => trim($translate['title']),
                    ]);
                }
            }

            return redirect()->route('admin.product-types.index')
                ->with('success', __('Product type updated successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error updating product type: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductType $productType)
    {
        try {
            $productType->delete();
            return redirect()->route('admin.product-types.index')
                ->with('success', __('Product type deleted successfully!'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error deleting product type: :error', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Reorder types
     */
    public function reorder(Request $request)
    {
        try {
            $items = $request->input('items', []);
            
            foreach ($items as $index => $itemId) {
                ProductType::where('id', $itemId)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
} 
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductAccordion;
use App\Models\ProductAccordionTranslate;
use App\Models\Product;
use App\Models\Language;
use Illuminate\Http\Request;

class ProductAccordionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Product $product)
    {
        $accordions = $product->accordions()->with(['translates.language'])->ordered()->get();
        
        return view('admin.product-accordions.index', compact('product', 'accordions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Product $product)
    {
        $languages = Language::all();
        
        return view('admin.product-accordions.create', compact('product', 'languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
            'status' => 'boolean'
        ]);

        try {
            $accordion = ProductAccordion::create([
                'product_id' => $product->id,
                'status' => $request->boolean('status', true)
            ]);

            // Create translations
            foreach ($request->input('translations') as $translation) {
                ProductAccordionTranslate::create([
                    'product_accordion_id' => $accordion->id,
                    'language_id' => $translation['language_id'],
                    'title' => $translation['title'],
                    'description' => $translation['description'] ?? null
                ]);
            }

            return redirect()->route('admin.products.accordions.index', $product)
                ->with('success', 'Accordion added successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product, ProductAccordion $accordion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product, ProductAccordion $accordion)
    {
        $accordion->load(['translates.language']);
        $languages = Language::all();
        
        return view('admin.product-accordions.edit', compact('product', 'accordion', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product, ProductAccordion $accordion)
    {
        $request->validate([
            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
            'status' => 'boolean'
        ]);

        try {
            $accordion->update([
                'status' => $request->boolean('status', true)
            ]);

            // Update translations
            foreach ($request->input('translations') as $translation) {
                ProductAccordionTranslate::updateOrCreate(
                    [
                        'product_accordion_id' => $accordion->id,
                        'language_id' => $translation['language_id']
                    ],
                    [
                        'title' => $translation['title'],
                        'description' => $translation['description'] ?? null
                    ]
                );
            }

            return redirect()->route('admin.products.accordions.index', $product)
                ->with('success', 'Accordion updated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product, ProductAccordion $accordion)
    {
        try {
            $accordion->delete();
            return redirect()->route('admin.products.accordions.index', $product)
                ->with('success', 'Accordion deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Reorder accordions
     */
    public function reorder(Request $request, Product $product)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'exists:product_accordions,id'
        ]);

        try {
            foreach ($request->input('items') as $index => $id) {
                ProductAccordion::where('id', $id)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}

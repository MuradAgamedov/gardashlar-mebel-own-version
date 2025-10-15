<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductAccordion;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Repositories\LanguageRepository;
use App\Repositories\ProductAccordionRepository;
use App\Http\Requests\Admin\ProductAccordion\StoreProductAccordionRequest;
use App\Http\Requests\Admin\ProductAccordion\UpdateProductAccordionRequest;

class ProductAccordionController extends Controller
{
    public function __construct(
        public LanguageRepository $languageRepository,
        public ProductAccordionRepository $repository
    )
    {
    }
    public function index(Product $product)
    {
        $accordions = $this->repository->getProductAccordions($product);
        return view('admin.product-accordions.index', compact('product', 'accordions'));
    }

    public function create(Product $product)
    {
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.product-accordions.create', compact('product', 'languages'));
    }

    public function store(StoreProductAccordionRequest $request, Product $product)
    {
        try {
            $this->repository->createProductAccordion(
                $product,
                ['status' => $request->boolean('status', true)],
                $request->input('translations')
            );

            return redirect()->route('admin.products.accordions.index', $product)
                ->with('success', 'Accordion added successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    public function edit(Product $product, ProductAccordion $accordion)
    {
        $accordion->load(['translates.language']);
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.product-accordions.edit', compact('product', 'accordion', 'languages'));
    }

    public function update(UpdateProductAccordionRequest $request, Product $product, ProductAccordion $accordion)
    {
        try {
            $this->repository->updateProductAccordion(
                $accordion,
                ['status' => $request->boolean('status', true)],
                $request->input('translations')
            );

            return redirect()->route('admin.products.accordions.index', $product)
                ->with('success', 'Accordion updated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product, ProductAccordion $accordion)
    {
        try {
            $this->repository->deleteProductAccordion($accordion);
            return redirect()->route('admin.products.accordions.index', $product)
                ->with('success', 'Accordion deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request, Product $product)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'exists:product_accordions,id'
        ]);

        try {
            $this->repository->reorderProductAccordions($request->input('items'));
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}

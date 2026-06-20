<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ProductAccordion\ReorderProductAccordionRequest;

use App\Http\Controllers\Controller;
use App\Models\ProductAccordion;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use App\Interfaces\Services\Admin\ProductAccordionServiceInterface;
use App\Http\Requests\Admin\ProductAccordion\StoreProductAccordionRequest;
use App\Http\Requests\Admin\ProductAccordion\UpdateProductAccordionRequest;

class ProductAccordionController extends Controller
{
    public function __construct(
        protected LanguageServiceInterface $languageService,
        protected ProductAccordionServiceInterface $productAccordionService
    )
    {
    }
    public function index(Product $product, Request $request)
    {
        $accordions = $this->productAccordionService->getProductAccordions($product);
        $currentPage = $request->get('page', 1);
        return view('admin.product-accordions.index', compact('product', 'accordions', 'currentPage'));
    }

    public function create(Product $product)
    {
        $languages = $this->languageService->getAllLanguages();
        return view('admin.product-accordions.create', compact('product', 'languages'));
    }

    public function store(StoreProductAccordionRequest $request, Product $product)
    {
        try {
            // === Create accordion for current product ===
            $accordion = $this->productAccordionService->createProductAccordion(
                $product,
                ['status' => $request->boolean('status', true)],
                $request->input('translations')
            );

            // === Synchronize same slug products ===
            // Get current product's slugs
            $currentProductSlugs = $product->translates->pluck('slug')->toArray();

            // Find all products with same slugs (excluding current product)
            $sameSlugProducts = Product::whereHas('translates', function ($q) use ($currentProductSlugs) {
                $q->whereIn('slug', $currentProductSlugs);
            })->where('id', '!=', $product->id)->get();

            foreach ($sameSlugProducts as $sameProduct) {
                // Create corresponding accordion with same data
                $this->productAccordionService->createProductAccordion(
                    $sameProduct,
                    ['status' => $request->boolean('status', true)],
                    $request->input('translations')
                );
            }

            return redirect()->route('admin.products.accordions.index', $product)
                ->with('success', 'Accordion added successfully. Same slug products synchronized.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    public function edit(Product $product, ProductAccordion $accordion)
    {
        $accordion->load(['translates.language']);
        $languages = $this->languageService->getAllLanguages();
        return view('admin.product-accordions.edit', compact('product', 'accordion', 'languages'));
    }

    public function update(UpdateProductAccordionRequest $request, Product $product, ProductAccordion $accordion)
    {
        try {
            // === Update current product accordion ===
            $this->productAccordionService->updateProductAccordion(
                $accordion,
                ['status' => $request->boolean('status', true)],
                $request->input('translations')
            );

            // === Synchronize same slug products ===
            // Get current product's slugs
            $currentProductSlugs = $product->translates->pluck('slug')->toArray();

            // Find all products with same slugs (excluding current product)
            $sameSlugProducts = Product::whereHas('translates', function ($q) use ($currentProductSlugs) {
                $q->whereIn('slug', $currentProductSlugs);
            })->where('id', '!=', $product->id)->get();

            foreach ($sameSlugProducts as $sameProduct) {
                // Find corresponding accordion by order
                $correspondingAccordion = $sameProduct->accordions()
                    ->where('order', $accordion->order)
                    ->first();

                if ($correspondingAccordion) {
                    // Update corresponding accordion with same data
                    $this->productAccordionService->updateProductAccordion(
                        $correspondingAccordion,
                        ['status' => $request->boolean('status', true)],
                        $request->input('translations')
                    );
                }
            }

            return redirect()->route('admin.products.accordions.index', $product)
                ->with('success', 'Accordion updated successfully. Same slug products synchronized.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product, ProductAccordion $accordion)
    {
        try {
            // Store the order for synchronization
            $accordionOrder = $accordion->order;

            // === Delete current product accordion ===
            $this->productAccordionService->deleteProductAccordion($accordion);

            // === Synchronize same slug products ===
            // Get current product's slugs
            $currentProductSlugs = $product->translates->pluck('slug')->toArray();

            // Find all products with same slugs (excluding current product)
            $sameSlugProducts = Product::whereHas('translates', function ($q) use ($currentProductSlugs) {
                $q->whereIn('slug', $currentProductSlugs);
            })->where('id', '!=', $product->id)->get();

            foreach ($sameSlugProducts as $sameProduct) {
                // Find corresponding accordion by order
                $correspondingAccordion = $sameProduct->accordions()
                    ->where('order', $accordionOrder)
                    ->first();

                if ($correspondingAccordion) {
                    // Delete corresponding accordion
                    $this->productAccordionService->deleteProductAccordion($correspondingAccordion);
                }
            }

            return redirect()->route('admin.products.accordions.index', $product)
                ->with('success', 'Accordion deleted successfully. Same slug products synchronized.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    public function reorder(ReorderProductAccordionRequest $request, Product $product)
    {
        $request->validated();

        try {
            // === Reorder current product accordions ===
            $this->productAccordionService->reorderProductAccordions($request->input('items'));

            // === Synchronize same slug products ===
            // Get current product's slugs
            $currentProductSlugs = $product->translates->pluck('slug')->toArray();

            // Find all products with same slugs (excluding current product)
            $sameSlugProducts = Product::whereHas('translates', function ($q) use ($currentProductSlugs) {
                $q->whereIn('slug', $currentProductSlugs);
            })->where('id', '!=', $product->id)->get();

            // Get the new order mapping from current product
            $currentAccordions = ProductAccordion::whereIn('id', $request->input('items'))->get();
            $orderMapping = [];
            foreach ($request->input('items') as $index => $id) {
                $accordion = $currentAccordions->where('id', $id)->first();
                if ($accordion) {
                    $orderMapping[$accordion->order] = $index + 1;
                }
            }

            foreach ($sameSlugProducts as $sameProduct) {
                // Get accordions for this product and reorder them based on the mapping
                $sameProductAccordions = $sameProduct->accordions()->get();
                
                foreach ($sameProductAccordions as $accordion) {
                    if (isset($orderMapping[$accordion->order])) {
                        $accordion->update(['order' => $orderMapping[$accordion->order]]);
                    }
                }
            }

            return response()->json(['success' => true, 'message' => 'Accordions reordered successfully. Same slug products synchronized.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use App\Models\Product;
use App\Models\ProductColor;
use App\Services\ImageService;
use Illuminate\Http\Request;
use App\Repositories\ProductImageRepository;

class ProductImageController extends Controller
{
    protected $imageService;

    public function __construct(
        ImageService $imageService,
        public ProductImageRepository $repository
    )
    {
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Product $product, Request $request)
    {
        $images = $this->repository->getProductImages($product, $request);
        $colors = $this->repository->getAllColors();
        
        return view('admin.product-images.index', compact('product', 'images', 'colors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Product $product)
    {
        $colors = $this->repository->getAllColors();
        return view('admin.product-images.create', compact('product', 'colors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Product $product)
    {
        dd($request->all());
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp',
            'product_color_id' => 'nullable|exists:product_colors,id'
        ]);

        try {
            foreach ($request->file('images') as $imageFile) {
                $imagePath = $this->imageService->upload($imageFile, 'products');
                
                $this->repository->createImage(
                    $product->id,
                    $request->input('product_color_id'),
                    $imagePath
                );
            }

            return redirect()->route('admin.products.images.index', $product)
                ->with('success', 'Images uploaded successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product, ProductImage $image)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product, ProductImage $image)
    {
        $colors = $this->repository->getAllColors();
        return view('admin.product-images.edit', compact('product', 'image', 'colors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product, ProductImage $image)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'status' => 'boolean',
            'product_color_id' => 'nullable|exists:product_colors,id'
        ]);

        try {
            $data = [
                'status' => $request->boolean('status', true),
                'product_color_id' => $request->input('product_color_id')
            ];

            // Update image if new one is uploaded
            if ($request->hasFile('image')) {
                // Delete old image
                if ($image->image) {
                    $this->imageService->delete($image->image);
                }
                
                // Upload new image
                $imagePath = $this->imageService->upload($request->file('image'), 'products');
                $data['image'] = $imagePath;
            }

            $image->update($data);

            return redirect()->route('admin.products.images.index', $product)
                ->with('success', 'Image updated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product, ProductImage $image)
    {
        try {
            $image->delete();
            return redirect()->route('admin.products.images.index', $product)
                ->with('success', 'Image deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Reorder images
     */
    public function reorder(Request $request, Product $product)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'exists:product_images,id'
        ]);

        try {
            foreach ($request->input('items') as $index => $id) {
                $this->repository->updateOrder($id, $index + 1);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Set main image
     */
    public function setMainImage(Product $product, ProductImage $image)
    {
        try {
            // Remove main flag from all other images of this product
            $this->repository->removeMainFromOthers($product->id, $image->id);

            // Set this image as main
            $image->update(['is_main' => true]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}

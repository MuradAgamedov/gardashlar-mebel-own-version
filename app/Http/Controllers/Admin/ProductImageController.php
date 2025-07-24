<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use App\Models\Product;
use App\Services\ImageService;
use Illuminate\Http\Request;

class ProductImageController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Product $product)
    {
        $images = $product->images()->ordered()->get();
        
        return view('admin.product-images.index', compact('product', 'images'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Product $product)
    {
        return view('admin.product-images.create', compact('product'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        try {
            foreach ($request->file('images') as $imageFile) {
                $imagePath = $this->imageService->upload($imageFile, 'products');
                
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $imagePath,
                    'status' => true
                ]);
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
        return view('admin.product-images.edit', compact('product', 'image'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product, ProductImage $image)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'boolean'
        ]);

        try {
            $data = [
                'status' => $request->boolean('status', true)
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
                ProductImage::where('id', $id)->update(['order' => $index + 1]);
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
            ProductImage::where('product_id', $product->id)
                ->where('id', '!=', $image->id)
                ->update(['is_main' => false]);

            // Set this image as main
            $image->update(['is_main' => true]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}

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
        $currentPage = $request->get('page', 1);
        
        return view('admin.product-images.index', compact('product', 'images', 'colors', 'currentPage'));
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
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp',
            'product_color_id' => 'nullable|exists:product_colors,id'
        ]);
        

        try {
            $uploadedImages = [];

            // === Upload images for current product ===
            foreach ($request->file('images') as $imageFile) {
                $imagePath = $this->imageService->upload($imageFile, 'products');
                
                $productImage = ProductImage::create([
                    'product_id' => $product->id,
                    'product_color_id' => $request->input('product_color_id'),
                    'image' => $imagePath,
                    'status' => true
                ]);

                $uploadedImages[] = $imagePath;
            }

            // === Synchronize same slug products ===
            // Get current product's slugs
            $currentProductSlugs = $product->translates->pluck('slug')->toArray();

            // Find all products with same slugs (excluding current product)
            $sameSlugProducts = Product::whereHas('translates', function ($q) use ($currentProductSlugs) {
                $q->whereIn('slug', $currentProductSlugs);
            })->where('id', '!=', $product->id)->get();

            foreach ($sameSlugProducts as $sameProduct) {
                // Create corresponding images for same slug products
                foreach ($uploadedImages as $imagePath) {
                    ProductImage::create([
                        'product_id' => $sameProduct->id,
                        'product_color_id' => $request->input('product_color_id'),
                        'image' => $imagePath,
                        'status' => true
                    ]);
                }
            }

            return redirect()->route('admin.products.images.index', $product)
                ->with('success', 'Images uploaded successfully. Same slug products synchronized.');

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

            $newImagePath = null;

            // Update image if new one is uploaded
            if ($request->hasFile('image')) {
                // Delete old image
                if ($image->image) {
                    $this->imageService->delete($image->image);
                }
                
                // Upload new image
                $imagePath = $this->imageService->upload($request->file('image'), 'products');
                $data['image'] = $imagePath;
                $newImagePath = $imagePath;
            }

            // === Update current product image ===
            $image->update($data);

            // === Synchronize same slug products ===
            // Get current product's slugs
            $currentProductSlugs = $product->translates->pluck('slug')->toArray();

            // Find all products with same slugs (excluding current product)
            $sameSlugProducts = Product::whereHas('translates', function ($q) use ($currentProductSlugs) {
                $q->whereIn('slug', $currentProductSlugs);
            })->where('id', '!=', $product->id)->get();

            foreach ($sameSlugProducts as $sameProduct) {
                // Find corresponding image by order
                $correspondingImage = $sameProduct->images()
                    ->where('order', $image->order)
                    ->first();

                if ($correspondingImage) {
                    $updateData = [
                        'status' => $request->boolean('status', true),
                        'product_color_id' => $request->input('product_color_id')
                    ];

                    // If new image was uploaded, use the same image path
                    if ($newImagePath) {
                        $updateData['image'] = $newImagePath;
                    }

                    $correspondingImage->update($updateData);
                }
            }

            return redirect()->route('admin.products.images.index', $product)
                ->with('success', 'Image updated successfully. Same slug products synchronized.');

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
            // Store the order for synchronization
            $imageOrder = $image->order;

            // === Delete current product image ===
            $image->delete();

            // === Synchronize same slug products ===
            // Get current product's slugs
            $currentProductSlugs = $product->translates->pluck('slug')->toArray();

            // Find all products with same slugs (excluding current product)
            $sameSlugProducts = Product::whereHas('translates', function ($q) use ($currentProductSlugs) {
                $q->whereIn('slug', $currentProductSlugs);
            })->where('id', '!=', $product->id)->get();

            foreach ($sameSlugProducts as $sameProduct) {
                // Find corresponding image by order
                $correspondingImage = $sameProduct->images()
                    ->where('order', $imageOrder)
                    ->first();

                if ($correspondingImage) {
                    // Delete corresponding image
                    $correspondingImage->delete();
                }
            }

            return redirect()->route('admin.products.images.index', $product)
                ->with('success', 'Image deleted successfully. Same slug products synchronized.');
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
            // === Reorder current product images ===
            foreach ($request->input('items') as $index => $id) {
                ProductImage::where('id', $id)->update(['order' => $index + 1]);
            }

            // === Synchronize same slug products ===
            // Get current product's slugs
            $currentProductSlugs = $product->translates->pluck('slug')->toArray();

            // Find all products with same slugs (excluding current product)
            $sameSlugProducts = Product::whereHas('translates', function ($q) use ($currentProductSlugs) {
                $q->whereIn('slug', $currentProductSlugs);
            })->where('id', '!=', $product->id)->get();

            // Get the new order mapping from current product
            $currentImages = ProductImage::whereIn('id', $request->input('items'))->get();
            $orderMapping = [];
            foreach ($request->input('items') as $index => $id) {
                $image = $currentImages->where('id', $id)->first();
                if ($image) {
                    $orderMapping[$image->order] = $index + 1;
                }
            }

            foreach ($sameSlugProducts as $sameProduct) {
                // Get images for this product and reorder them based on the mapping
                $sameProductImages = $sameProduct->images()->get();
                
                foreach ($sameProductImages as $image) {
                    if (isset($orderMapping[$image->order])) {
                        $image->update(['order' => $orderMapping[$image->order]]);
                    }
                }
            }

            return response()->json(['success' => true, 'message' => 'Images reordered successfully. Same slug products synchronized.']);
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
            // === Set main image for current product ===
            // Remove main flag from all other images of this product
            ProductImage::where('product_id', $product->id)
                ->where('id', '!=', $image->id)
                ->update(['is_main' => false]);

            // Set this image as main
            $image->update(['is_main' => true]);

            // === Synchronize same slug products ===
            // Get current product's slugs
            $currentProductSlugs = $product->translates->pluck('slug')->toArray();

            // Find all products with same slugs (excluding current product)
            $sameSlugProducts = Product::whereHas('translates', function ($q) use ($currentProductSlugs) {
                $q->whereIn('slug', $currentProductSlugs);
            })->where('id', '!=', $product->id)->get();

            foreach ($sameSlugProducts as $sameProduct) {
                // Find corresponding image by order
                $correspondingImage = $sameProduct->images()
                    ->where('order', $image->order)
                    ->first();

                if ($correspondingImage) {
                    // Remove main flag from all other images of this product
                    ProductImage::where('product_id', $sameProduct->id)
                        ->where('id', '!=', $correspondingImage->id)
                        ->update(['is_main' => false]);

                    // Set corresponding image as main
                    $correspondingImage->update(['is_main' => true]);
                }
            }

            return response()->json(['success' => true, 'message' => 'Main image set successfully. Same slug products synchronized.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}

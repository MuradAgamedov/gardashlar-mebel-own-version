<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogImage;
use App\Models\BlogPost;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BlogImageController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of blog images for a specific blog post.
     */
    public function index(BlogPost $blogPost)
    {
        $images = $blogPost->images()->get();
        
        return view('admin.blog-images.index', compact('blogPost', 'images'));
    }

    /**
     * Store a newly created blog image.
     */
    public function store(Request $request, BlogPost $blogPost)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'images.required' => 'At least one image is required.',
            'images.*.image' => 'Each file must be a valid image.',
            'images.*.mimes' => 'Each image must be a JPEG, PNG, JPG, GIF, or WEBP file.',
            'images.*.max' => 'Each image size cannot exceed 2MB.',
        ]);

        try {
            $uploadedCount = 0;
            
            foreach ($request->file('images') as $image) {
                $imageUrl = $this->imageService->upload($image, 'blog-images');
                
                BlogImage::create([
                    'blog_post_id' => $blogPost->id,
                    'image_url' => $imageUrl,
                ]);
                
                $uploadedCount++;
            }

            $message = $uploadedCount === 1 ? 'Image uploaded successfully!' : "{$uploadedCount} images uploaded successfully!";
            
            return redirect()->route('admin.blog-posts.images.index', $blogPost)
                ->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Error uploading images: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified blog image.
     */
    public function update(Request $request, BlogPost $blogPost, BlogImage $blogImage)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'image.required' => 'Image is required.',
            'image.image' => 'File must be a valid image.',
            'image.mimes' => 'Image must be a JPEG, PNG, JPG, GIF, or WEBP file.',
            'image.max' => 'Image size cannot exceed 2MB.',
        ]);

        try {
            // Delete old image
            if ($blogImage->image_url) {
                $this->imageService->delete($blogImage->image_url);
            }

            // Upload new image
            $imageUrl = $this->imageService->upload($request->file('image'), 'blog-images');
            
            $blogImage->update([
                'image_url' => $imageUrl,
            ]);

            return redirect()->route('admin.blog-posts.images.index', $blogPost)
                ->with('success', 'Image updated successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating image: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified blog image.
     */
    public function destroy(BlogPost $blogPost, BlogImage $blogImage)
    {
        try {
            // Delete image file
            if ($blogImage->image_url) {
                $this->imageService->delete($blogImage->image_url);
            }

            $blogImage->delete();

            return redirect()->route('admin.blog-posts.images.index', $blogPost)
                ->with('success', 'Image deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting image: ' . $e->getMessage());
        }
    }

    /**
     * Reorder blog images.
     */
    public function reorder(Request $request, BlogPost $blogPost)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*.id' => 'required|integer|exists:blog_images,id',
            'images.*.order' => 'required|integer|min:0',
        ]);

        try {
            foreach ($request->images as $imageData) {
                BlogImage::where('id', $imageData['id'])
                    ->where('blog_post_id', $blogPost->id)
                    ->update(['order' => $imageData['order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Images reordered successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error reordering images: ' . $e->getMessage()
            ], 500);
        }
    }
}

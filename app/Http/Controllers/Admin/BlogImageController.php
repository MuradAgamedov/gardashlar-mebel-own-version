<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogImage;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Repositories\BlogImageRepository;

class BlogImageController extends Controller
{
    public function __construct(public BlogImageRepository $repository)
    {
    }

    public function index(BlogPost $blogPost)
    {
        $images = $this->repository->getImagesByPost($blogPost);
        return view('admin.blog-images.index', compact('blogPost', 'images'));
    }

    public function store(Request $request, BlogPost $blogPost)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        try {
            $uploadedCount = $this->repository->storeImages($blogPost, $request->file('images'));
            $message = $uploadedCount === 1 ? 'Image uploaded successfully!' : "{$uploadedCount} images uploaded successfully!";
            
            return redirect()->route('admin.blog-posts.images.index', $blogPost)
                ->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Error uploading images: ' . $e->getMessage());
        }
    }

    public function update(Request $request, BlogPost $blogPost, BlogImage $blogImage)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        try {
            $this->repository->updateImage($blogImage, $request->file('image'));
            return redirect()->route('admin.blog-posts.images.index', $blogPost)
                ->with('success', 'Image updated successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating image: ' . $e->getMessage());
        }
    }

    public function destroy(BlogPost $blogPost, BlogImage $blogImage)
    {
        try {
            $this->repository->deleteImage($blogImage);
            return redirect()->route('admin.blog-posts.images.index', $blogPost)
                ->with('success', 'Image deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting image: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request, BlogPost $blogPost)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*.id' => 'required|integer|exists:blog_images,id',
            'images.*.order' => 'required|integer|min:0',
        ]);

        try {
            $this->repository->reorderImages($blogPost, $request->images);
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

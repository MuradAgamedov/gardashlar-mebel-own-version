<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\BlogImage\ReorderBlogImageRequest;

use App\Http\Requests\Admin\BlogImage\UpdateBlogImageRequest;

use App\Http\Requests\Admin\BlogImage\StoreBlogImageRequest;

use App\Http\Controllers\Controller;
use App\Models\BlogImage;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Interfaces\Services\Admin\BlogImageServiceInterface;

class BlogImageController extends Controller
{
    public function __construct(protected BlogImageServiceInterface $blogImageService)
    {
    }

    public function index(BlogPost $blogPost)
    {
        $images = $this->blogImageService->getImagesByPost($blogPost);
        return view('admin.blog-images.index', compact('blogPost', 'images'));
    }

    public function store(StoreBlogImageRequest $request, BlogPost $blogPost)
    {
        $request->validated();

        try {
            $uploadedCount = $this->blogImageService->storeImages($blogPost, $request->file('images'));
            $message = $uploadedCount === 1 ? 'Image uploaded successfully!' : "{$uploadedCount} images uploaded successfully!";
            
            return redirect()->route('admin.blog-posts.images.index', $blogPost)
                ->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Error uploading images: ' . $e->getMessage());
        }
    }

    public function update(UpdateBlogImageRequest $request, BlogPost $blogPost, BlogImage $blogImage)
    {
        $request->validated();

        try {
            $this->blogImageService->updateImage($blogImage, $request->file('image'));
            return redirect()->route('admin.blog-posts.images.index', $blogPost)
                ->with('success', 'Image updated successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating image: ' . $e->getMessage());
        }
    }

    public function destroy(BlogPost $blogPost, BlogImage $blogImage)
    {
        try {
            $this->blogImageService->deleteImage($blogImage);
            return redirect()->route('admin.blog-posts.images.index', $blogPost)
                ->with('success', 'Image deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting image: ' . $e->getMessage());
        }
    }

    public function reorder(ReorderBlogImageRequest $request, BlogPost $blogPost)
    {
        $request->validated();

        try {
            $this->blogImageService->reorderImages($blogPost, $request->images);
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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsPost;
use App\Models\NewsImage;
use Illuminate\Http\Request;
use App\Repositories\NewsPostRepository;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;

class NewsImageController extends Controller
{
    public function __construct(
        public NewsPostRepository $newsPostRepository,
        public ImageService $imageService
    )
    {
    }

    public function index(NewsPost $newsPost)
    {
        $newsPost->load(['images' => fn($q) => $q->ordered()]);
        return view('admin.news-images.index', compact('newsPost'));
    }

    public function store(Request $request, NewsPost $newsPost): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        try {
            $imagePath = $this->imageService->upload($request->file('image'), 'news-images');
            
            $maxOrder = $newsPost->images()->max('order') ?? 0;
            
            $newsImage = $newsPost->images()->create([
                'image_url' => $imagePath,
                'order' => $maxOrder + 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'image' => [
                    'id' => $newsImage->id,
                    'image_url' => $newsImage->image_url,
                    'order' => $newsImage->order,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading image: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, NewsPost $newsPost, NewsImage $newsImage): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        try {
            // Delete old image
            if ($newsImage->image_url) {
                $this->imageService->delete($newsImage->image_url);
            }

            // Upload new image
            $imagePath = $this->imageService->upload($request->file('image'), 'news-images');
            
            $newsImage->update(['image_url' => $imagePath]);

            return response()->json([
                'success' => true,
                'message' => 'Image updated successfully',
                'image' => [
                    'id' => $newsImage->id,
                    'image_url' => $newsImage->image_url,
                    'order' => $newsImage->order,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating image: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(NewsPost $newsPost, NewsImage $newsImage): JsonResponse
    {
        try {
            // Delete image file
            if ($newsImage->image_url) {
                $this->imageService->delete($newsImage->image_url);
            }

            // Delete database record
            $newsImage->delete();

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting image: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reorder(Request $request, NewsPost $newsPost): JsonResponse
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'required|integer|exists:news_images,id',
        ]);

        try {
            foreach ($request->images as $index => $imageId) {
                NewsImage::where('id', $imageId)
                    ->where('news_post_id', $newsPost->id)
                    ->update(['order' => $index + 1]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Images reordered successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error reordering images: ' . $e->getMessage()
            ], 500);
        }
    }
}

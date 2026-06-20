<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\NewsImage\ReorderNewsImageRequest;

use App\Http\Requests\Admin\NewsImage\UpdateNewsImageRequest;

use App\Http\Requests\Admin\NewsImage\StoreNewsImageRequest;

use App\Http\Controllers\Controller;
use App\Models\NewsPost;
use App\Models\NewsImage;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\NewsPostServiceInterface;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;

class NewsImageController extends Controller
{
    public function __construct(
        protected NewsPostServiceInterface $newsPostService,
        protected ImageService $imageService
    )
    {
    }

    public function index(NewsPost $news_post)
    {
        $news_post->load(['images' => fn($q) => $q->ordered()]);
        return view('admin.news-images.index', compact('news_post'));
    }

    public function store(StoreNewsImageRequest $request, NewsPost $news_post)
    {
        $request->validated();

        try {
            $uploadedCount = 0;
            $maxOrder = $news_post->images()->max('order') ?? 0;
            
            foreach ($request->file('images') as $index => $image) {
                $imagePath = $this->imageService->upload($image, 'news-images');
                
                $news_post->images()->create([
                    'image_url' => $imagePath,
                    'order' => $maxOrder + $index + 1,
                ]);
                
                $uploadedCount++;
            }

            return redirect()->back()->with('success', $uploadedCount . ' images uploaded successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error uploading images: ' . $e->getMessage())->withInput();
        }
    }

    public function update(UpdateNewsImageRequest $request, NewsPost $news_post, NewsImage $news_image): JsonResponse
    {
        $request->validated();

        try {
            // Delete old image
            if ($news_image->image_url) {
                $this->imageService->delete($news_image->image_url);
            }

            // Upload new image
            $imagePath = $this->imageService->upload($request->file('image'), 'news-images');
            
            $news_image->update(['image_url' => $imagePath]);

            return response()->json([
                'success' => true,
                'message' => 'Image updated successfully',
                'image' => [
                    'id' => $news_image->id,
                    'image_url' => $news_image->getImageUrlAttribute(),
                    'order' => $news_image->order,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating image: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(NewsPost $news_post, NewsImage $news_image)
    {
        try {
            // Delete image file
            if ($news_image->image_url) {
                $this->imageService->delete($news_image->image_url);
            }

            // Delete database record
            $news_image->delete();

            return redirect()->back()->with('success', 'Image deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting image: ' . $e->getMessage());
        }
    }

    public function reorder(ReorderNewsImageRequest $request, NewsPost $news_post): JsonResponse
    {
        $request->validated();

        try {
            foreach ($request->images as $index => $imageId) {
                NewsImage::where('id', $imageId)
                    ->where('news_post_id', $news_post->id)
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

<?php
namespace App\Repositories;

use App\Models\BlogImage;
use App\Models\BlogPost;
use App\Repositories\Global\BaseRepository;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;

class BlogImageRepository extends BaseRepository
{
    protected $imageService;

    public function __construct(public BlogImage $blogImage, ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function getImagesByPost(BlogPost $blogPost)
    {
        return $blogPost->images()->get();
    }

    public function storeImages(BlogPost $blogPost, array $images)
    {
        return DB::transaction(function () use ($blogPost, $images) {
            $uploadedCount = 0;
            
            foreach ($images as $image) {
                $imageUrl = $this->imageService->upload($image, 'blog-images');
                
                BlogImage::create([
                    'blog_post_id' => $blogPost->id,
                    'image_url' => $imageUrl,
                ]);
                
                $uploadedCount++;
            }

            return $uploadedCount;
        });
    }

    public function updateImage(BlogImage $blogImage, $imageFile)
    {
        return DB::transaction(function () use ($blogImage, $imageFile) {
            if ($blogImage->image_url) {
                $this->imageService->delete($blogImage->image_url);
            }

            $imageUrl = $this->imageService->upload($imageFile, 'blog-images');
            
            $blogImage->update(['image_url' => $imageUrl]);

            return $blogImage;
        });
    }

    public function deleteImage(BlogImage $blogImage)
    {
        return DB::transaction(function () use ($blogImage) {
            if ($blogImage->image_url) {
                $this->imageService->delete($blogImage->image_url);
            }

            return $blogImage->delete();
        });
    }

    public function reorderImages(BlogPost $blogPost, array $images)
    {
        return DB::transaction(function () use ($blogPost, $images) {
            foreach ($images as $imageData) {
                BlogImage::where('id', $imageData['id'])
                    ->where('blog_post_id', $blogPost->id)
                    ->update(['order' => $imageData['order']]);
            }
            return true;
        });
    }
}

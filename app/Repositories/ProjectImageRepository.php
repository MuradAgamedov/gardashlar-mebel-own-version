<?php

namespace App\Repositories;

use App\Models\ProjectImage;
use App\Repositories\Global\BaseRepository;
use Illuminate\Support\Facades\DB;

class ProjectImageRepository extends BaseRepository
{
    protected $imageService;

    public function __construct(
        public ProjectImage $projectImage,
        \App\Services\ImageService $imageService
    ) {
        $this->imageService = $imageService;
    }

    public function getOrderedImages($projectId)
    {
        return $this->projectImage->where('project_id', $projectId)
            ->orderBy('order')
            ->get();
    }

    public function createImages($projectId, $images, $status = true)
    {
        return DB::transaction(function () use ($projectId, $images, $status) {
            $maxOrder = $this->projectImage->where('project_id', $projectId)->max('order') ?? 0;
            $uploadedImages = [];

            foreach ($images as $index => $image) {
                $imagePath = $this->imageService->upload($image, 'projects');
                
                $projectImage = $this->projectImage->create([
                    'project_id' => $projectId,
                    'image' => $imagePath,
                    'is_main_image' => false,
                    'order' => $maxOrder + $index + 1,
                    'status' => $status
                ]);

                $uploadedImages[] = $projectImage;
            }

            return $uploadedImages;
        });
    }

    public function updateImage($image, $data, $newImage = null)
    {
        return DB::transaction(function () use ($image, $data, $newImage) {
            if ($newImage) {
                if ($image->image) {
                    $this->imageService->delete($image->image);
                }
                $data['image'] = $this->imageService->upload($newImage, 'projects');
            }

            return $image->update($data);
        });
    }

    public function deleteImage($image)
    {
        return DB::transaction(function () use ($image) {
            if ($image->image) {
                $this->imageService->delete($image->image);
            }
            return $image->delete();
        });
    }

    public function setMainImage($projectId, $imageId)
    {
        // Unmark all other images as main
        $this->projectImage->where('project_id', $projectId)
            ->update(['is_main_image' => false]);

        // Mark this image as main
        return $this->projectImage->where('id', $imageId)
            ->update(['is_main_image' => true]);
    }

    public function reorderImages($projectId, $itemIds)
    {
        $projectImageIds = $this->projectImage->where('project_id', $projectId)
            ->pluck('id')
            ->toArray();

        $validItems = array_intersect($itemIds, $projectImageIds);
        
        if (count($validItems) !== count($itemIds)) {
            return false;
        }

        foreach ($validItems as $index => $itemId) {
            $this->projectImage->where('id', $itemId)->update(['order' => $index + 1]);
        }

        return true;
    }

    public function getMaxOrder($projectId)
    {
        return $this->projectImage->where('project_id', $projectId)
            ->max('order') ?? 0;
    }
}

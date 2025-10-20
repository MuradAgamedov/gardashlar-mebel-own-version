<?php

namespace App\Repositories;

use App\Models\ProjectImage;
use App\Repositories\Global\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

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

    public function getMaxOrder($projectId)
    {
        return $this->projectImage->where('project_id', $projectId)
            ->max('order') ?? 0;
    }

    /**
     * Create multiple project images
     *
     * @param int $projectId
     * @param array|UploadedFile $images
     * @param bool $status
     * @return array
     */
    public function createImages($projectId, $images, $status = true)
    {
        $uploadedImages = [];
        $currentOrder = $this->getMaxOrder($projectId) + 1;

        if (!is_array($images)) {
            $images = [$images];
        }

        foreach ($images as $image) {
            try {
                $path = $this->imageService->upload($image, 'projects');
                
                $projectImage = $this->projectImage->create([
                    'project_id' => $projectId,
                    'image' => $path,
                    'order' => $currentOrder++,
                    'status' => $status
                ]);

                $uploadedImages[] = $projectImage;
            } catch (\Exception $e) {
                // Log the error and continue with next image
                \Log::error('Error uploading project image: ' . $e->getMessage());
                continue;
            }
        }

        return $uploadedImages;
    }

    /**
     * Update a project image
     *
     * @param ProjectImage $image
     * @param array $data
     * @param UploadedFile|null $newImage
     * @return void
     */
    public function updateImage(ProjectImage $image, array $data, $newImage = null)
    {
        if ($newImage) {
            // Delete old image
            if (Storage::disk('public')->exists($image->image)) {
                Storage::disk('public')->delete($image->image);
            }
            
            // Upload new image
            $data['image'] = $this->imageService->upload($newImage, 'projects');
        }

        $image->update($data);
    }

    /**
     * Delete a project image
     *
     * @param ProjectImage $image
     * @return bool
     */
    public function deleteImage(ProjectImage $image)
    {
        // Delete the image file
        if (Storage::disk('public')->exists($image->image)) {
            Storage::disk('public')->delete($image->image);
        }
        
        return $image->delete();
    }

    /**
     * Set main image for project
     *
     * @param int $projectId
     * @param int $imageId
     * @return void
     */
    public function setMainImage($projectId, $imageId)
    {
        // Reset all images to not main
        $this->projectImage->where('project_id', $projectId)
            ->update(['is_main' => false]);

        // Set the selected image as main
        $this->projectImage->where('id', $imageId)
            ->where('project_id', $projectId)
            ->update(['is_main' => true]);
    }

    /**
     * Reorder project images
     *
     * @param int $projectId
     * @param array $itemIds
     * @return bool
     */
    public function reorderImages($projectId, array $itemIds)
    {
        // Verify all images belong to the project
        $validCount = $this->projectImage->where('project_id', $projectId)
            ->whereIn('id', $itemIds)
            ->count();

        if ($validCount !== count($itemIds)) {
            return false;
        }

        $case = 'CASE id ';
        $ids = [];
        
        foreach ($itemIds as $order => $id) {
            $case .= 'WHEN ' . $id . ' THEN ' . ($order + 1) . ' ';
            $ids[] = $id;
        }
        
        $case .= 'ELSE `order` END';
        
        return $this->projectImage->whereIn('id', $ids)
            ->update([
                'order' => DB::raw($case)
            ]) > 0;
    }
}

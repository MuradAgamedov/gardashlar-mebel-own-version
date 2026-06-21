<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ProjectImageRepositoryInterface;
use App\Models\ProjectImage;
use App\Repositories\BaseRepository;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProjectImageRepository extends BaseRepository implements ProjectImageRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(ProjectImage $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

    public function getOrderedImages($projectId)
    {
        return $this->model->newQuery()
            ->where('project_id', $projectId)
            ->orderBy('order')
            ->get();
    }

    public function getMaxOrder($projectId)
    {
        return $this->model->newQuery()->where('project_id', $projectId)->max('order') ?? 0;
    }

    public function createImages($projectId, $images, $status = true)
    {
        $uploadedImages = [];
        $currentOrder   = $this->getMaxOrder($projectId) + 1;

        if (!is_array($images)) {
            $images = [$images];
        }

        foreach ($images as $image) {
            try {
                $path = $this->imageService->upload($image, 'projects');

                $projectImage = $this->model->newQuery()->create([
                    'project_id' => $projectId,
                    'image'      => $path,
                    'order'      => $currentOrder++,
                    'status'     => $status,
                ]);

                $uploadedImages[] = $projectImage;
            } catch (\Exception $e) {
                \Log::error('Error uploading project image: ' . $e->getMessage());
                continue;
            }
        }

        return $uploadedImages;
    }

    public function updateImage(ProjectImage $image, array $data, $newImage = null)
    {
        if ($newImage) {
            if (Storage::disk('public')->exists($image->image)) {
                Storage::disk('public')->delete($image->image);
            }
            $data['image'] = $this->imageService->upload($newImage, 'projects');
        }

        $image->update($data);
    }

    public function deleteImage(ProjectImage $image)
    {
        if (Storage::disk('public')->exists($image->image)) {
            Storage::disk('public')->delete($image->image);
        }
        return $image->delete();
    }

    public function setMainImage($projectId, $imageId)
    {
        $this->model->newQuery()->where('project_id', $projectId)->update(['is_main_image' => false]);
        $this->model->newQuery()->where('id', $imageId)->where('project_id', $projectId)->update(['is_main_image' => true]);
    }

    public function reorderImages($projectId, array $itemIds)
    {
        $validCount = $this->model->newQuery()
            ->where('project_id', $projectId)
            ->whereIn('id', $itemIds)
            ->count();

        if ($validCount !== count($itemIds)) {
            return false;
        }

        $case = 'CASE id ';
        $ids  = [];

        foreach ($itemIds as $order => $id) {
            $case .= 'WHEN ' . $id . ' THEN ' . ($order + 1) . ' ';
            $ids[] = $id;
        }

        $case .= 'ELSE `order` END';

        return $this->model->newQuery()->whereIn('id', $ids)->update([
            'order' => DB::raw($case),
        ]) > 0;
    }
}

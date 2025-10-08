<?php
namespace App\Repositories;

use App\Models\ProjectImage;
use App\Repositories\Global\BaseRepository;

class ProjectImageRepository extends BaseRepository
{
    public function __construct(public ProjectImage $projectImage)
    {
    }

    // Şəkilləri sıralanmış şəkildə gətir
    public function getOrderedImages($projectId)
    {
        return $this->projectImage->where('project_id', $projectId)
            ->orderBy('order')
            ->get();
    }

    // Əsas şəkli təyin et
    public function setMainImage($projectId, $imageId)
    {
        // Bütün şəkilləri əsas olaraq işarələmə
        $this->projectImage->where('project_id', $projectId)
            ->update(['is_main_image' => false]);

        // Seçilən şəkli əsas et
        return $this->projectImage->where('id', $imageId)
            ->update(['is_main_image' => true]);
    }

    // Sıralamanı yenilə
    public function updateOrder($projectId, $itemId, $order)
    {
        return $this->projectImage->where('project_id', $projectId)
            ->where('id', $itemId)
            ->update(['order' => $order]);
    }

    // Şəkillərin sırasını yenilə (toplu)
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
            $this->updateOrder($projectId, $itemId, $index + 1);
        }

        return true;
    }

    // Maksimum sıra nömrəsini gətir
    public function getMaxOrder($projectId)
    {
        return $this->projectImage->where('project_id', $projectId)
            ->max('order') ?? 0;
    }
}

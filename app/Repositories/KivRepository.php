<?php
namespace App\Repositories;

use App\Models\Kiv;
use App\Models\KivCategory;
use App\Repositories\Global\BaseRepository;

class KivRepository extends BaseRepository
{
    public function __construct(public Kiv $kiv)
    {
    }

    public function getKivs($request)
    {
        $query = $this->kiv->with('category.translates');
        
        if ($request->filled('category_id')) {
            $query->where('kiv_category_id', $request->category_id);
        }
        
        return $query->orderBy('order')->get();
    }

    public function getCategories()
    {
        return KivCategory::with('translates')->orderBy('order')->get();
    }

    public function createKiv(array $data, $imageFile, $imageService)
    {
        $imagePath = null;
        if ($imageFile) {
            $imagePath = $imageService->upload($imageFile, 'kivs', [
                'max_size' => 2048,
                'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            ]);
            
            if (!$imagePath) {
                throw new \Exception('Failed to upload image.');
            }
        }

        return $this->kiv->create([
            'image' => $imagePath,
            'video_link' => $data['video_link'],
            'kiv_category_id' => $data['kiv_category_id'],
            'status' => $data['status'],
        ]);
    }

    public function updateKiv(Kiv $kiv, array $data, $imageFile, $imageService)
    {
        $imageData = [];
        if ($imageFile) {
            $uploadedPath = $imageService->update($imageFile, $kiv->image, 'kivs', [
                'max_size' => 2048,
                'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            ]);
            
            if ($uploadedPath) {
                $imageData['image'] = $uploadedPath;
            } else {
                throw new \Exception('Failed to upload image.');
            }
        }

        $kiv->update(array_merge($imageData, [
            'video_link' => $data['video_link'],
            'kiv_category_id' => $data['kiv_category_id'],
            'status' => $data['status'],
        ]));

        return $kiv;
    }

    public function deleteKiv(Kiv $kiv)
    {
        return $kiv->delete();
    }

    public function reorderKivs(array $kivs)
    {
        foreach ($kivs as $index => $kivId) {
            $this->kiv->where('id', $kivId)->update(['order' => $index + 1]);
        }
        return true;
    }
}

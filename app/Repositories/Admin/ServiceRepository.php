<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ServiceRepositoryInterface;
use App\Models\Service;
use App\Models\ServiceTranslate;
use App\Repositories\BaseRepository;
use App\Services\ImageService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ServiceRepository extends BaseRepository implements ServiceRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(Service $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

    public function getServices($request): Collection
    {
        $query = $this->query()->with('translates.language');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('translates', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('card_description', 'like', "%{$search}%")
                  ->orWhere('main_description', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('order')->get();
    }

    public function createService(array $data, array $translates): Model
    {
        return DB::transaction(function () use ($data, $translates) {
            $service = $this->create($data);

            foreach ($translates as $translate) {
                $service->translates()->create([
                    'language_id' => $translate['language_id'],
                    'title' => trim($translate['title']),
                    'slug' => trim($translate['slug'] ?? ''),
                    'card_description' => trim($translate['card_description']),
                    'main_description' => trim($translate['main_description']),
                    'form_top_text' => trim($translate['form_top_text'] ?? ''),
                    'advantages_title' => trim($translate['advantages_title'] ?? ''),
                    'seo_title' => trim($translate['seo_title'] ?? ''),
                    'seo_keywords' => trim($translate['seo_keywords'] ?? ''),
                    'seo_description' => trim($translate['seo_description'] ?? ''),
                ]);
            }

            return $service->load('translates.language');
        });
    }

    public function updateService(int $id, array $data, array $translates): Model
    {
        return DB::transaction(function () use ($id, $data, $translates) {
            $service = $this->findOrFail($id);
            $service->update($data);

            foreach ($translates as $translate) {
                $service->translates()->updateOrCreate(
                    ['language_id' => $translate['language_id']],
                    [
                        'title' => trim($translate['title']),
                        'slug' => trim($translate['slug'] ?? ''),
                        'card_description' => trim($translate['card_description']),
                        'main_description' => trim($translate['main_description']),
                        'form_top_text' => trim($translate['form_top_text'] ?? ''),
                        'advantages_title' => trim($translate['advantages_title'] ?? ''),
                        'seo_title' => trim($translate['seo_title'] ?? ''),
                        'seo_keywords' => trim($translate['seo_keywords'] ?? ''),
                        'seo_description' => trim($translate['seo_description'] ?? ''),
                    ]
                );
            }

            return $service->load('translates.language');
        });
    }

    public function updateServiceOrder(array $services): bool
    {
        return DB::transaction(function () use ($services) {
            foreach ($services as $serviceData) {
                $this->query()->where('id', $serviceData['id'])
                    ->update(['order' => $serviceData['order']]);
            }
            return true;
        });
    }

    public function bulkDelete(array $ids): int
    {
        return DB::transaction(function () use ($ids) {
            ServiceTranslate::whereIn('service_id', $ids)->delete();
            return $this->query()->whereIn('id', $ids)->delete();
        });
    }
}

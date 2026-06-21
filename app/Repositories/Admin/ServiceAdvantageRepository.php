<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ServiceAdvantageRepositoryInterface;
use App\Models\Language;
use App\Models\Service;
use App\Models\ServiceAdvantage;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class ServiceAdvantageRepository extends BaseRepository implements ServiceAdvantageRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(ServiceAdvantage $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

    public function getServiceAdvantages($serviceId)
    {
        return $this->model->newQuery()
            ->with(['translates.language'])
            ->where('service_id', $serviceId)
            ->orderBy('order')
            ->get();
    }

    public function findWithTranslations($id)
    {
        return $this->model->newQuery()->with(['translates.language'])->findOrFail($id);
    }

    public function getServiceWithTranslations($serviceId)
    {
        return Service::with(['translates.language'])->findOrFail($serviceId);
    }

    public function getActiveLanguages()
    {
        return Language::where('status', 1)->orderBy('order')->get();
    }
}

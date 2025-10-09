<?php

namespace App\Repositories;

use App\Models\RoluteeteAction;
use App\Models\Language;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;

class RoluteeteActionRepository
{
    protected $model;
    protected $language;
    protected $imageService;

    public function __construct(RoluteeteAction $model, Language $language, ImageService $imageService)
    {
        $this->model = $model;
        $this->language = $language;
        $this->imageService = $imageService;
    }

    /**
     * Get all actions with translations for index page
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllForIndex()
    {
        return $this->model->with('translates.language')
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Get active languages for forms
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveLanguages()
    {
        return $this->language->all();
    }
}

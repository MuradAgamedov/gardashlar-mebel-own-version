<?php

namespace App\Repositories;

use App\Models\RoluteeteAction;
use App\Models\RoluteeteActionTranslate;
use App\Models\Language;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoluteeteActionRepository
{
    protected $model;
    protected $translationModel;
    protected $language;
    protected $imageService;

    public function __construct(RoluteeteAction $model, Language $language, ImageService $imageService)
    {
        $this->model = $model;
        $this->translationModel = new RoluteeteActionTranslate();
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

    /**
     * Find action by ID with translations
     *
     * @param int $id
     * @return \App\Models\RoluteeteAction|null
     */
    public function findWithTranslations($id)
    {
        return $this->model->with('translates.language')->findOrFail($id);
    }
}

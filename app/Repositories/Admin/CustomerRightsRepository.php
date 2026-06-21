<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\CustomerRightsRepositoryInterface;
use App\Models\CustomerRights;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Cache;

class CustomerRightsRepository extends BaseRepository implements CustomerRightsRepositoryInterface
{
    public function __construct(CustomerRights $model)
    {
        parent::__construct($model);
    }

    public function updateCustomerRights(array $translations, $languages)
    {
        $customerRights = $this->model->first();

        if (!$customerRights) {
            $customerRights = $this->model->create(['status' => true]);
        }

        foreach ($languages as $language) {
            $translationData = $translations[$language->id] ?? [];

            $customerRights->translates()->updateOrCreate(
                ['language_id' => $language->id],
                ['content' => trim($translationData['content'] ?? '')]
            );
        }

        $this->clearCache($languages);

        return $customerRights;
    }

    protected function clearCache($languages)
    {
        foreach ($languages as $language) {
            Cache::forget("customer_rights_{$language->id}");
        }
    }
}

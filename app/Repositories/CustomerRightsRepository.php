<?php
namespace App\Repositories;

use App\Models\CustomerRights;
use App\Repositories\Global\BaseRepository;
use Illuminate\Support\Facades\Cache;

class CustomerRightsRepository extends BaseRepository
{
    public function __construct(public CustomerRights $customerRights)
    {
    }

    public function getFirstOrCreate()
    {
        $customerRights = $this->customerRights->with(['translates.language'])->first();
        
        if (!$customerRights) {
            $customerRights = $this->customerRights->create(['status' => true]);
        }
        
        return $customerRights;
    }

    public function updateCustomerRights(array $translations, $languages)
    {
        $customerRights = $this->customerRights->first();
        
        if (!$customerRights) {
            $customerRights = $this->customerRights->create(['status' => true]);
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

    private function clearCache($languages)
    {
        foreach ($languages as $language) {
            Cache::forget("customer_rights_{$language->id}");
        }
    }
}

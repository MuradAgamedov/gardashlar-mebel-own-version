<?php
namespace App\Repositories;

use App\Models\DeliveryPayment;
use App\Repositories\Global\BaseRepository;
use Illuminate\Support\Facades\Cache;

class DeliveryPaymentRepository extends BaseRepository
{
    public function __construct(public DeliveryPayment $deliveryPayment)
    {
    }

    public function getFirstOrCreate()
    {
        $deliveryPayment = $this->deliveryPayment->with(['translates.language'])->first();
        
        if (!$deliveryPayment) {
            $deliveryPayment = $this->deliveryPayment->create(['status' => true]);
        }
        
        return $deliveryPayment;
    }

    public function updateDeliveryPayment(array $translations, $languages)
    {
        $deliveryPayment = $this->deliveryPayment->first();
        
        if (!$deliveryPayment) {
            $deliveryPayment = $this->deliveryPayment->create(['status' => true]);
        }

        foreach ($languages as $language) {
            $translationData = $translations[$language->id] ?? [];
            
            $deliveryPayment->translates()->updateOrCreate(
                ['language_id' => $language->id],
                ['content' => trim($translationData['content'] ?? '')]
            );
        }

        $this->clearCache($languages);

        return $deliveryPayment;
    }

    private function clearCache($languages)
    {
        foreach ($languages as $language) {
            Cache::forget("delivery_payment_{$language->id}");
        }
    }
}

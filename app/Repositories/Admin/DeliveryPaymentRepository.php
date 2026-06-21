<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\DeliveryPaymentRepositoryInterface;
use App\Models\DeliveryPayment;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Cache;

class DeliveryPaymentRepository extends BaseRepository implements DeliveryPaymentRepositoryInterface
{
    public function __construct(DeliveryPayment $model)
    {
        parent::__construct($model);
    }

    public function updateDeliveryPayment(array $translations, $languages)
    {
        $deliveryPayment = $this->model->first();

        if (!$deliveryPayment) {
            $deliveryPayment = $this->model->create(['status' => true]);
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

    protected function clearCache($languages)
    {
        foreach ($languages as $language) {
            Cache::forget("delivery_payment_{$language->id}");
        }
    }
}

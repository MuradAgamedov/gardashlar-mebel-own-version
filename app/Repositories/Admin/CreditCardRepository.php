<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\CreditCardRepositoryInterface;
use App\Models\CreditCard;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class CreditCardRepository extends BaseRepository implements CreditCardRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(CreditCard $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

    public function getCreditCards()
    {
        return $this->GetData($this->creditCard, [])->get();
    }

    public function createCreditCard(array $data, $imageFile = null)
    {
        $imagePath = $this->handleImageUpload($imageFile, 'credit-cards', $this->imageService);

        $lastOrder = $this->creditCard->max('order') ?? 0;

        return $this->creditCard->create([
            'title' => $data['title'],
            'months' => $data['months'] ?? null,
            'order' => $lastOrder + 1,
            'image' => $imagePath,
        ]);
    }

    public function updateCreditCard(CreditCard $creditCard, array $data, $imageFile = null)
    {
        $imagePath = $this->handleImageUpdate($creditCard, $imageFile, 'credit-cards', $this->imageService);

        $creditCard->update([
            'title' => $data['title'],
            'months' => $data['months'] ?? null,
            'image' => $imagePath ?: $creditCard->image,
        ]);

        return $creditCard;
    }

    public function deleteCreditCard(CreditCard $creditCard)
    {
        $this->handleImageDelete($creditCard->image, $this->imageService);
        return $creditCard->delete();
    }

    public function reorderCreditCards(array $creditCards)
    {
        return $this->UpdateOrder($this->creditCard, $creditCards);
    }
}

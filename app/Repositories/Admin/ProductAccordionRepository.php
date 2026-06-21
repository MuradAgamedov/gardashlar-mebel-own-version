<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ProductAccordionRepositoryInterface;
use App\Models\ProductAccordion;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class ProductAccordionRepository extends BaseRepository implements ProductAccordionRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(ProductAccordion $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

    public function getProductAccordions($product)
    {
        return $product->accordions()->with(['translates.language'])->ordered()->get();
    }

    public function createProductAccordion($product, array $data, array $translations)
    {
        $accordion = $this->productAccordion->create([
            'product_id' => $product->id,
            'status' => $data['status']
        ]);

        foreach ($translations as $translation) {
            $accordion->translates()->create([
                'language_id' => $translation['language_id'],
                'title' => $translation['title'],
                'description' => $translation['description'] ?? null
            ]);
        }

        return $accordion;
    }

    public function updateProductAccordion(ProductAccordion $accordion, array $data, array $translations)
    {
        $accordion->update(['status' => $data['status']]);

        foreach ($translations as $translation) {
            $accordion->translates()->updateOrCreate(
                ['language_id' => $translation['language_id']],
                [
                    'title' => $translation['title'],
                    'description' => $translation['description'] ?? null
                ]
            );
        }

        return $accordion;
    }

    public function deleteProductAccordion(ProductAccordion $accordion)
    {
        return $accordion->delete();
    }

    public function reorderProductAccordions(array $items)
    {
        foreach ($items as $index => $id) {
            $this->productAccordion->where('id', $id)->update(['order' => $index + 1]);
        }
        return true;
    }
}

<?php

namespace App\Repositories;

use App\Models\ProductDetailValue;
use App\Models\ProductDetailValueTranslate;
use App\Repositories\Global\BaseRepository;
use Illuminate\Support\Facades\DB;

class ProductDetailValueRepository extends BaseRepository
{
    public function getValuesByProductId($productId)
    {
        return ProductDetailValue::with(['translates.language'])
            ->where('product_id', $productId)
            ->ordered()
            ->get();
    }

    public function createValue($data, $productId)
    {
        return DB::transaction(function () use ($data, $productId) {
            $detailValue = ProductDetailValue::create([
                'product_id' => $productId,
                'order' => ProductDetailValue::where('product_id', $productId)->max('order') + 1
            ]);

            $this->createTranslations($detailValue->id, $data['translates']);

            return $detailValue->load('translates.language');
        });
    }

    public function updateValue($detailValue, $data)
    {
        return DB::transaction(function () use ($detailValue, $data) {
            $detailValue->translates()->delete();
            $this->createTranslations($detailValue->id, $data['translates']);
            return $detailValue->load('translates.language');
        });
    }

    public function deleteValue($detailValue)
    {
        return DB::transaction(function () use ($detailValue) {
            $detailValue->translates()->delete();
            return $detailValue->delete();
        });
    }

    public function bulkDelete($ids)
    {
        return DB::transaction(function () use ($ids) {
            $detailValues = ProductDetailValue::whereIn('id', $ids)->get();
            
            foreach ($detailValues as $detailValue) {
                $detailValue->translates()->delete();
                $detailValue->delete();
            }

            return true;
        });
    }

    public function reorder($items)
    {
        return DB::transaction(function () use ($items) {
            foreach ($items as $index => $id) {
                ProductDetailValue::where('id', $id)->update(['order' => $index + 1]);
            }
            return true;
        });
    }

    protected function createTranslations($detailValueId, $translations)
    {
        foreach ($translations as $translation) {
            ProductDetailValueTranslate::create([
                'product_detail_value_id' => $detailValueId,
                'language_id' => $translation['language_id'],
                'keyword' => $translation['keyword'],
            ]);
        }
    }
}

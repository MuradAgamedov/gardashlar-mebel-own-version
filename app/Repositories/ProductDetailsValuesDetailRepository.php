<?php

namespace App\Repositories;

use App\Models\ProductDetailsValuesDetail;
use App\Models\ProductDetailsValuesDetailTranslate;
use App\Repositories\Global\BaseRepository;
use Illuminate\Support\Facades\DB;

class ProductDetailsValuesDetailRepository extends BaseRepository
{
    public function getDetailsByValueId($detailValueId)
    {
        return ProductDetailsValuesDetail::with(['translates.language'])
            ->where('product_detail_value_id', $detailValueId)
            ->ordered()
            ->get();
    }

    public function createDetail($data, $detailValueId)
    {
        return DB::transaction(function () use ($data, $detailValueId) {
            $detail = ProductDetailsValuesDetail::create([
                'product_detail_value_id' => $detailValueId,
                'order' => ProductDetailsValuesDetail::where('product_detail_value_id', $detailValueId)->max('order') + 1
            ]);

            foreach ($data['translates'] as $translate) {
                $this->createTranslation($detail->id, $translate);
            }

            return $detail->load('translates.language');
        });
    }

    public function updateDetail($detail, $data)
    {
        return DB::transaction(function () use ($detail, $data) {
            // Delete existing translations
            $detail->translates()->delete();

            // Create new translations
            foreach ($data['translates'] as $translate) {
                $this->createTranslation($detail->id, $translate);
            }

            return $detail->load('translates.language');
        });
    }

    public function deleteDetail($detail)
    {
        return DB::transaction(function () use ($detail) {
            $detail->translates()->delete();
            return $detail->delete();
        });
    }

    public function bulkDelete($ids)
    {
        return DB::transaction(function () use ($ids) {
            $details = ProductDetailsValuesDetail::whereIn('id', $ids)->get();
            
            foreach ($details as $detail) {
                $detail->translates()->delete();
                $detail->delete();
            }

            return true;
        });
    }

    public function reorder($items)
    {
        return DB::transaction(function () use ($items) {
            foreach ($items as $index => $id) {
                ProductDetailsValuesDetail::where('id', $id)->update(['order' => $index + 1]);
            }
            return true;
        });
    }

    protected function createTranslation($detailId, $translateData)
    {
        return ProductDetailsValuesDetailTranslate::create([
            'product_details_values_detail_id' => $detailId,
            'language_id' => $translateData['language_id'],
            'keyword' => $translateData['keyword'],
            'value' => $translateData['value'],
        ]);
    }
}

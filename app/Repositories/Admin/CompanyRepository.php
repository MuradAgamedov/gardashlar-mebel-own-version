<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\CompanyRepositoryInterface;
use App\Models\Company;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class CompanyRepository extends BaseRepository implements CompanyRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(Company $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

    public function getCompanies()
    {
        return $this->GetData($this->company, ['translates.language'])->get();
    }

    public function createCompany(array $data, array $translates, $request)
    {
        return DB::transaction(function () use ($data, $translates, $request) {
            $companyData = [
                'status' => $data['status'] ?? true
            ];
            
            if (isset($data['discount_percent'])) {
                $companyData['discount_percent'] = $data['discount_percent'];
            }
            
            $company = $this->company->create($companyData);

            foreach ($translates as $translate) {
                $cardImagePath = $this->imageService->upload(
                    $request->file("translates.{$translate['language_id']}.card_image"), 
                    'companies/card'
                );
                $innerImagePath = $this->imageService->upload(
                    $request->file("translates.{$translate['language_id']}.inner_image"), 
                    'companies/inner'
                );

                CompanyTranslate::create([
                    'company_id' => $company->id,
                    'language_id' => $translate['language_id'],
                    'title' => $translate['title'],
                    'slug' => !empty($translate['slug']) ? Str::slug($translate['slug']) : Str::slug($translate['title']),
                    'card_image' => $cardImagePath,
                    'card_image_alt_text' => $translate['card_image_alt_text'] ?? null,
                    'inner_image' => $innerImagePath,
                    'inner_image_alt_text' => $translate['inner_image_alt_text'] ?? null,
                    'description' => $translate['description'],
                ]);
            }

            return $company->load('translates');
        });
    }

    public function updateCompany(Company $company, array $data, array $translates, $request)
    {
        return DB::transaction(function () use ($company, $data, $translates, $request) {
            $companyData = ['status' => $data['status'] ?? true];
            
            if (isset($data['discount_percent'])) {
                $companyData['discount_percent'] = $data['discount_percent'];
            }
            
            $company->update($companyData);

            foreach ($translates as $translate) {
                $translateData = [
                    'title' => $translate['title'],
                    'slug' => !empty($translate['slug']) ? Str::slug($translate['slug']) : Str::slug($translate['title']),
                    'card_image_alt_text' => $translate['card_image_alt_text'] ?? null,
                    'inner_image_alt_text' => $translate['inner_image_alt_text'] ?? null,
                    'description' => $translate['description'],
                ];

                if ($request->hasFile("translates.{$translate['language_id']}.card_image")) {
                    $existingTranslate = $company->translates->where('language_id', $translate['language_id'])->first();
                    if ($existingTranslate && $existingTranslate->card_image) {
                        $this->imageService->delete($existingTranslate->card_image);
                    }
                    $translateData['card_image'] = $this->imageService->upload(
                        $request->file("translates.{$translate['language_id']}.card_image"), 
                        'companies/card'
                    );
                }

                if ($request->hasFile("translates.{$translate['language_id']}.inner_image")) {
                    $existingTranslate = $company->translates->where('language_id', $translate['language_id'])->first();
                    if ($existingTranslate && $existingTranslate->inner_image) {
                        $this->imageService->delete($existingTranslate->inner_image);
                    }
                    $translateData['inner_image'] = $this->imageService->upload(
                        $request->file("translates.{$translate['language_id']}.inner_image"), 
                        'companies/inner'
                    );
                }

                CompanyTranslate::updateOrCreate(
                    ['company_id' => $company->id, 'language_id' => $translate['language_id']],
                    $translateData
                );
            }

            return $company->load('translates');
        });
    }

    public function deleteCompany(Company $company)
    {
        return DB::transaction(function () use ($company) {
            foreach ($company->translates as $translate) {
                if ($translate->card_image) {
                    $this->imageService->delete($translate->card_image);
                }
                if ($translate->inner_image) {
                    $this->imageService->delete($translate->inner_image);
                }
            }
            return $company->delete();
        });
    }

    public function reorderCompanies(array $order)
    {
        foreach ($order as $index => $id) {
            $this->company->where('id', $id)->update(['order' => $index + 1]);
        }
        return true;
    }

    public function addProductToCompany(Company $company, int $productId)
    {
        $existing = CompanyProduct::where('company_id', $company->id)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            return false;
        }

        // Get the product to calculate discount price
        $product = \App\Models\Product::find($productId);
        if (!$product) {
            return false;
        }

        // Calculate discount price based on company's discount_percent
        $discountPrice = 0;
        if ($company->discount_percent && $product->price) {
            $discountPrice = $product->price * (1 - ($company->discount_percent / 100));
        }

        // Update product's discount price
        $product->update(['discount_price' => $discountPrice]);

        CompanyProduct::create([
            'company_id' => $company->id,
            'product_id' => $productId,
            'action_id' => null,
            'is_action_product' => false,
        ]);

        return true;
    }

    public function removeProductFromCompany(Company $company, int $productId)
    {
        // Get the product to reset discount price
        $product = \App\Models\Product::find($productId);
        if ($product) {
            // Set discount price to 0 when removing from company
            $product->update(['discount_price' => 0]);
        }

        return CompanyProduct::where('company_id', $company->id)
            ->where('product_id', $productId)
            ->delete();
    }
}

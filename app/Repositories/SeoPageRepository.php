<?php
namespace App\Repositories;

use App\Models\SeoPage;
use App\Models\ProductCategory;
use App\Repositories\Global\BaseRepository;

class SeoPageRepository extends BaseRepository
{
    public function __construct(public SeoPage $seoPage)
    {
    }

    public function getSeoPages()
    {
        return $this->seoPage->with(['translates.language', 'category.translates'])->orderBy('id', 'desc')->get();
    }

    public function findSeoPage($id)
    {
        return $this->seoPage->with(['translates.language', 'category.translates'])->findOrFail($id);
    }

    public function getActiveCategories()
    {
        return ProductCategory::with(['translates' => function($query) {
            $query->where('language_id', 1); // Default language
        }])->where('status', true)->get();
    }

    public function createSeoPage(array $data, array $translates)
    {
        $seoPage = $this->seoPage->create($data);

        foreach ($translates as $translate) {
            $seoPage->translates()->create([
                'language_id' => $translate['language_id'],
                'slug' => trim($translate['slug']),
                'title' => trim($translate['title']),
                'page_header' => trim($translate['page_header'] ?? ''),
                'page_footer' => trim($translate['page_footer'] ?? ''),
                'meta_title' => trim($translate['meta_title'] ?? ''),
                'meta_description' => trim($translate['meta_description'] ?? ''),
                'meta_keywords' => trim($translate['meta_keywords'] ?? ''),
            ]);
        }

        return $seoPage;
    }

    public function updateSeoPage($id, array $data, array $translates)
    {
        $seoPage = $this->seoPage->findOrFail($id);
        $seoPage->update($data);
        $seoPage->translates()->delete();

        foreach ($translates as $translate) {
            $seoPage->translates()->create([
                'language_id' => $translate['language_id'],
                'slug' => trim($translate['slug']),
                'title' => trim($translate['title']),
                'page_header' => trim($translate['page_header'] ?? ''),
                'page_footer' => trim($translate['page_footer'] ?? ''),
                'meta_title' => trim($translate['meta_title'] ?? ''),
                'meta_description' => trim($translate['meta_description'] ?? ''),
                'meta_keywords' => trim($translate['meta_keywords'] ?? ''),
            ]);
        }

        return $seoPage;
    }

    public function deleteSeoPage($id)
    {
        $seoPage = $this->seoPage->findOrFail($id);
        $seoPage->translates()->delete();
        return $seoPage->delete();
    }
}

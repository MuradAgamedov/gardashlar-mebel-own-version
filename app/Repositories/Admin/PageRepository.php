<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\PageRepositoryInterface;
use App\Models\Page;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class PageRepository extends BaseRepository implements PageRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(Page $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

    public function getPages()
    {
        return $this->page->with(['translates.language'])->orderBy('order')->get();
    }

    public function findPage($id)
    {
        return $this->page->with(['translates.language'])->findOrFail($id);
    }

    public function createPage(array $data, array $translates)
    {
        $page = $this->page->create($data);

        foreach ($translates as $translate) {
            $page->translates()->create([
                'language_id' => $translate['language_id'],
                'title' => trim($translate['title']),
                'slug' => trim($translate['slug']),
                'seo_title' => trim($translate['seo_title'] ?? ''),
                'seo_keywords' => trim($translate['seo_keywords'] ?? ''),
                'seo_description' => trim($translate['seo_description'] ?? ''),
                'opengraph_title' => trim($translate['opengraph_title'] ?? ''),
                'opengraph_description' => trim($translate['opengraph_description'] ?? ''),
                'opengraph_image_alt_text' => trim($translate['opengraph_image_alt_text'] ?? ''),
            ]);
        }

        return $page;
    }

    public function updatePage($id, array $data, array $translates)
    {
        $page = $this->page->findOrFail($id);
        $page->update($data);
        $page->translates()->delete();

        foreach ($translates as $translate) {
            $page->translates()->create([
                'language_id' => $translate['language_id'],
                'title' => trim($translate['title']),
                'slug' => trim($translate['slug']),
                'seo_title' => trim($translate['seo_title'] ?? ''),
                'seo_keywords' => trim($translate['seo_keywords'] ?? ''),
                'seo_description' => trim($translate['seo_description'] ?? ''),
                'opengraph_title' => trim($translate['opengraph_title'] ?? ''),
                'opengraph_description' => trim($translate['opengraph_description'] ?? ''),
                'opengraph_image_alt_text' => trim($translate['opengraph_image_alt_text'] ?? ''),
            ]);
        }

        return $page;
    }

    public function deletePage($id)
    {
        $page = $this->page->findOrFail($id);
        
        $activePagesCount = $this->page->where('status', true)->count();
        if ($activePagesCount <= 1 && $page->status) {
            throw new \Exception('Cannot delete the only active page. Please activate another page first.');
        }
        
        return $page->delete();
    }
}

<?php
namespace App\Repositories;

use App\Models\Page;
use App\Repositories\Global\BaseRepository;

class PageRepository extends BaseRepository
{
    public function __construct(public Page $page)
    {
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

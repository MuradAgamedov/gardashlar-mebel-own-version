<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\SeoPage\UpdateSeoPageRequest;

use App\Http\Requests\Admin\SeoPage\StoreSeoPageRequest;

use App\Http\Controllers\Controller;
use App\Models\SeoPage;
use App\Interfaces\Services\Admin\SeoPageServiceInterface;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SeoPageController extends Controller
{
    public function __construct(
        protected SeoPageServiceInterface $seoPageService,
        protected LanguageServiceInterface $languageService
    )
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $seoPages = $this->seoPageService->getSeoPages();
        return view('admin.seo-pages.index', compact('seoPages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = $this->seoPageService->getActiveCategories();
        $languages = $this->languageService->getAllLanguages();
        
        return view('admin.seo-pages.create', compact('categories', 'languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSeoPageRequest $request)
    {
        $request->validated();

        $data = [
            'category_id' => $request->category_id,
            'status' => $request->boolean('status', true),
        ];

        $translates = [];
        foreach ($request->translates as $translate) {
            // Boş olan dilləri keç (title və slug tam boşdursa saxlamırıq)
            $title = $translate['title'] ?? null;
            $slug  = $translate['slug'] ?? null;

            if (empty($title) && empty($slug)) {
                continue;
            }

            $translates[] = [
                'language_id' => $translate['language_id'],
                'title' => $title,
                'slug' => Str::slug($slug),
                'page_footer' => $translate['page_footer'] ?? '',
                'meta_title' => $translate['meta_title'] ?? '',
                'meta_description' => $translate['meta_description'] ?? '',
                'meta_keywords' => $translate['meta_keywords'] ?? '',
            ];
        }

        // Ən azı bir dildə tərcümə olmalıdır
        if (empty($translates)) {
            return back()
                ->withErrors(['translates' => __('At least one language must have Title and Slug filled.')])
                ->withInput();
        }

        $this->seoPageService->createSeoPage($data, $translates);

        return redirect()->route('admin.seo-pages.index')
            ->with('success', __('SEO Page created successfully!'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $seoPage = $this->seoPageService->findSeoPage($id);
        $categories = $this->seoPageService->getActiveCategories();
        $languages = $this->languageService->getAllLanguages();
        
        return view('admin.seo-pages.edit', compact('seoPage', 'categories', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSeoPageRequest $request, $id)
    {
        $request->validated();

        $data = [
            'category_id' => $request->category_id,
            'status' => $request->boolean('status', true),
        ];

        $translates = [];
        foreach ($request->translates as $translate) {
            $translates[] = [
                'language_id' => $translate['language_id'],
                'title' => $translate['title'],
                'slug' => Str::slug($translate['slug']),
                'page_footer' => $translate['page_footer'] ?? '',
                'meta_title' => $translate['meta_title'] ?? '',
                'meta_description' => $translate['meta_description'] ?? '',
                'meta_keywords' => $translate['meta_keywords'] ?? '',
            ];
        }

        $this->seoPageService->updateSeoPage($id, $data, $translates);

        return redirect()->route('admin.seo-pages.index')
            ->with('success', __('SEO Page updated successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->seoPageService->deleteSeoPage($id);

        return redirect()->route('admin.seo-pages.index')
            ->with('success', __('SEO Page deleted successfully!'));
    }
}

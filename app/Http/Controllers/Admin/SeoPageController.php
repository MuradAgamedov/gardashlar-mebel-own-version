<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoPage;
use App\Models\Language;
use App\Repositories\SeoPageRepository;
use App\Repositories\LanguageRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SeoPageController extends Controller
{
    public function __construct(
        public SeoPageRepository $repository,
        public LanguageRepository $languageRepository
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $seoPages = $this->repository->getSeoPages();
        return view('admin.seo-pages.index', compact('seoPages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = $this->repository->getActiveCategories();
        $languages = $this->languageRepository->getAllLanguages();
        
        return view('admin.seo-pages.create', compact('categories', 'languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'nullable|exists:product_categories,id',
            'status' => 'boolean',
            'translates' => 'required|array',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string',
            'translates.*.slug' => 'required|string|max:255|unique:seo_page_translates,slug',
            'translates.*.page_footer' => 'nullable|string',
            'translates.*.meta_title' => 'nullable|string',
            'translates.*.meta_description' => 'nullable|string',
            'translates.*.meta_keywords' => 'nullable|string',
        ]);

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

        $this->repository->createSeoPage($data, $translates);

        return redirect()->route('admin.seo-pages.index')
            ->with('success', 'SEO Page created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $seoPage = $this->repository->findSeoPage($id);
        $categories = $this->repository->getActiveCategories();
        $languages = $this->languageRepository->getAllLanguages();
        
        return view('admin.seo-pages.edit', compact('seoPage', 'categories', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'nullable|exists:product_categories,id',
            'status' => 'boolean',
            'translates' => 'required|array',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string',
            'translates.*.slug' => 'required|string|max:255|unique:seo_page_translates,slug,' . $id . ',seo_page_id',
            'translates.*.page_footer' => 'nullable|string',
            'translates.*.meta_title' => 'nullable|string',
            'translates.*.meta_description' => 'nullable|string',
            'translates.*.meta_keywords' => 'nullable|string',
        ]);

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

        $this->repository->updateSeoPage($id, $data, $translates);

        return redirect()->route('admin.seo-pages.index')
            ->with('success', 'SEO Page updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->repository->deleteSeoPage($id);

        return redirect()->route('admin.seo-pages.index')
            ->with('success', 'SEO Page deleted successfully!');
    }
}

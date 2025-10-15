<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\PageRepository;
use App\Repositories\LanguageRepository;
use App\Http\Requests\Admin\Page\StorePageRequest;
use App\Http\Requests\Admin\Page\UpdatePageRequest;

class PageController extends Controller
{
    public function __construct(
        public PageRepository $repository,
        public LanguageRepository $languageRepository
    )
    {
    }

    public function index()
    {
        $pages = $this->repository->getPages();
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        $languages = $this->languageRepository->getActiveLanguages();
        return view('admin.pages.create', compact('languages'));
    }

    public function store(StorePageRequest $request)
    {
        try {
            $this->repository->createPage([
                'status' => $request->has('status') ? true : false,
                'keyword' => trim($request->input('keyword', ''))
            ], $request->translates);

            return redirect()->route('admin.pages.index')
                ->with('success', 'Page created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating page: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(string $id)
    {
        $page = $this->repository->findPage($id);
        return view('admin.pages.show', compact('page'));
    }

    public function edit(string $id)
    {
        $page = $this->repository->findPage($id);
        $languages = $this->languageRepository->getActiveLanguages();
        return view('admin.pages.edit', compact('page', 'languages'));
    }

    public function update(UpdatePageRequest $request, string $id)
    {
        try {
            $this->repository->updatePage($id, [
                'status' => $request->has('status') ? true : false,
                'keyword' => trim($request->input('keyword', ''))
            ], $request->translates);

            return redirect()->route('admin.pages.index')
                ->with('success', 'Page updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating page: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $this->repository->deletePage($id);
            return redirect()->route('admin.pages.index')
                ->with('success', 'Page deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting page: ' . $e->getMessage());
        }
    }

    public function generateSlug(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'language_id' => 'required|exists:languages,id',
                'page_id' => 'nullable|exists:pages,id',
            ]);

            return response()->json(['success' => true, 'slug' => '']);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating slug: ' . $e->getMessage()
            ], 500);
        }
    }
}

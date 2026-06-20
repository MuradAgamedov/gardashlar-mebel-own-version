<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Catalog\ReorderCatalogRequest;

use App\Http\Controllers\Controller;
use App\Models\Catalog;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\CatalogServiceInterface;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use App\Http\Requests\Admin\Catalog\StoreCatalogRequest;
use App\Http\Requests\Admin\Catalog\UpdateCatalogRequest;

class CatalogController extends Controller
{
    public function __construct(
        protected CatalogServiceInterface $catalogService,
        protected LanguageServiceInterface $languageService
    )
    {
    }

    public function index()
    {
        $catalogs = $this->catalogService->getCatalogs();
        $languages = $this->languageService->getAllLanguages();
        return view('admin.catalogs.index', compact('catalogs', 'languages'));
    }

    public function create()
    {
        $languages = $this->languageService->getAllLanguages();
        return view('admin.catalogs.create', compact('languages'));
    }

    public function store(StoreCatalogRequest $request)
    {
        try {
            $this->catalogService->createCatalog(
                ['status' => $request->input('status', true)],
                $request->input('translations'),
                $request->file('catalog_pdf')
            );

            return redirect()->route('admin.catalogs.index')
                ->with('success', __('Catalog created successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error creating catalog: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }

    public function edit(Catalog $catalog)
    {
        $catalog->load('translates.language');
        $languages = $this->languageService->getAllLanguages();
        return view('admin.catalogs.edit', compact('catalog', 'languages'));
    }

    public function update(UpdateCatalogRequest $request, Catalog $catalog)
    {
        try {
            $this->catalogService->updateCatalog(
                $catalog,
                ['status' => $request->input('status', true)],
                $request->input('translations'),
                $request->file('catalog_pdf')
            );

            return redirect()->route('admin.catalogs.index')
                ->with('success', __('Catalog updated successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error updating catalog: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }

    public function destroy(Catalog $catalog)
    {
        try {
            $this->catalogService->deleteCatalog($catalog);
            return redirect()->route('admin.catalogs.index')
                ->with('success', __('Catalog deleted successfully!'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error deleting catalog: :error', ['error' => $e->getMessage()]));
        }
    }

    public function reorder(ReorderCatalogRequest $request)
    {
        $request->validated();

        try {
            $this->catalogService->reorderCatalogs($request->catalogs);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

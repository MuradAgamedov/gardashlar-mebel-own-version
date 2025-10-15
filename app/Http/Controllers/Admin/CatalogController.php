<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Catalog;
use Illuminate\Http\Request;
use App\Repositories\CatalogRepository;
use App\Repositories\LanguageRepository;
use App\Http\Requests\Admin\Catalog\StoreCatalogRequest;
use App\Http\Requests\Admin\Catalog\UpdateCatalogRequest;

class CatalogController extends Controller
{
    public function __construct(
        public CatalogRepository $repository,
        public LanguageRepository $languageRepository
    )
    {
    }

    public function index()
    {
        $catalogs = $this->repository->getCatalogs();
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.catalogs.index', compact('catalogs', 'languages'));
    }

    public function create()
    {
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.catalogs.create', compact('languages'));
    }

    public function store(StoreCatalogRequest $request)
    {
        try {
            $this->repository->createCatalog(
                ['status' => $request->input('status', true)],
                $request->input('translations'),
                $request->file('catalog_pdf')
            );

            return redirect()->route('admin.catalogs.index')
                ->with('success', 'Catalog created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating catalog: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Catalog $catalog)
    {
        $catalog->load('translates.language');
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.catalogs.edit', compact('catalog', 'languages'));
    }

    public function update(UpdateCatalogRequest $request, Catalog $catalog)
    {
        try {
            $this->repository->updateCatalog(
                $catalog,
                ['status' => $request->input('status', true)],
                $request->input('translations'),
                $request->file('catalog_pdf')
            );

            return redirect()->route('admin.catalogs.index')
                ->with('success', 'Catalog updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating catalog: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Catalog $catalog)
    {
        try {
            $this->repository->deleteCatalog($catalog);
            return redirect()->route('admin.catalogs.index')
                ->with('success', 'Catalog deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting catalog: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'catalogs' => 'required|array',
            'catalogs.*' => 'required|integer|exists:catalogs,id',
        ]);

        try {
            $this->repository->reorderCatalogs($request->catalogs);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

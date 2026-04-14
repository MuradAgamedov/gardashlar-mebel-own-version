<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Repositories\CompanyRepository;
use App\Repositories\LanguageRepository;
use App\Http\Requests\Admin\Company\StoreCompanyRequest;
use App\Http\Requests\Admin\Company\UpdateCompanyRequest;

class CompanyController extends Controller
{
    public function __construct(
        public CompanyRepository $repository,
        public LanguageRepository $languageRepository
    )
    {
    }

    public function index()
    {
        $companies = $this->repository->getCompanies();
        return view('admin.companies.index', compact('companies'));
    }

    public function create()
    {
        $languages = $this->languageRepository->getActiveLanguages();
        return view('admin.campaigns.create', compact('languages'));
    }

    public function store(StoreCompanyRequest $request)
    {
        try {
            $this->repository->createCompany(
                ['status' => $request->input('status', true)],
                $request->input('translates'),
                $request
            );

            return redirect()->route('admin.campaigns.index')
                ->with('success', 'Kampaniya uğurla əlavə edildi.');

        } catch (\Exception $e) {
            return back()->with('error', 'Xəta baş verdi: ' . $e->getMessage());
        }
    }

    public function edit(Company $campaign)
    {
        $campaign->load('translates');
        $languages = $this->languageRepository->getActiveLanguages();
        return view('admin.companies.edit', ['company' => $campaign, 'languages' => $languages]);
    }

    public function update(UpdateCompanyRequest $request, Company $campaign)
    {
        try {
            $this->repository->updateCompany(
                $campaign,
                ['status' => $request->input('status', true)],
                $request->input('translates'),
                $request
            );

            return redirect()->route('admin.campaigns.index')
                ->with('success', 'Kampaniya uğurla yeniləndi.');

        } catch (\Exception $e) {
            return back()->with('error', 'Xəta baş verdi: ' . $e->getMessage());
        }
    }

    public function destroy(Company $campaign)
    {
        try {
            $this->repository->deleteCompany($campaign);
            return redirect()->route('admin.campaigns.index')
                ->with('success', 'Kampaniya uğurla silindi.');

        } catch (\Exception $e) {
            return back()->with('error', 'Xəta baş verdi: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:companies,id'
        ]);

        try {
            $this->repository->reorderCompanies($request->input('order'));
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function actions(Company $campaign, Request $request)
    {
        $campaign->load(['translates.language']);
        
        $query = Product::with(['translates.language', 'companyProducts' => function($q) use ($campaign) {
            $q->where('company_id', $campaign->id);
        }]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('translates', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(20);
        return view('admin.companies.actions', ['company' => $campaign, 'products' => $products]);
    }

    public function addProductToAction(Request $request, Company $campaign)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        try {
            $result = $this->repository->addProductToCompany($campaign, $request->product_id);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product is already added to this company'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product added to company successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding product: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeProductFromAction(Request $request, Company $campaign)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        try {
            $this->repository->removeProductFromCompany($campaign, $request->product_id);

            return response()->json([
                'success' => true,
                'message' => 'Product removed from company successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing product: ' . $e->getMessage()
            ], 500);
        }
    }
}

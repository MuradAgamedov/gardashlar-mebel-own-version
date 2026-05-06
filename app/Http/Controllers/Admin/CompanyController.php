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
        return view('admin.companies.create', compact('languages'));
    }

    public function store(StoreCompanyRequest $request)
    {
        try {
            $this->repository->createCompany(
                [
                    'status' => $request->input('status', true),
                    'discount_percent' => $request->input('discount_percent')
                ],
                $request->input('translates'),
                $request
            );

            return redirect()->route('admin.campaigns.index')
                ->with('success', __('Campaign created successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));
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
                [
                    'status' => $request->input('status', true),
                    'discount_percent' => $request->input('discount_percent')
                ],
                $request->input('translates'),
                $request
            );

            return redirect()->route('admin.campaigns.index')
                ->with('success', __('Campaign updated successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));
        }
    }

    public function destroy(Company $campaign)
    {
        try {
            $this->repository->deleteCompany($campaign);
            return redirect()->route('admin.campaigns.index')
                ->with('success', __('Campaign deleted successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));
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
        
        // Get all categories for selection
        $categories = ProductCategory::with(['translates.language', 'children' => function($q) {
            $q->with(['translates.language']);
        }])
        ->whereNull('parent_id')
        ->active()
        ->ordered()
        ->get();
        
        return view('admin.companies.actions', [
            'company' => $campaign, 
            'products' => $products,
            'categories' => $categories
        ]);
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

    public function addCategoryProductsToAction(Request $request, Company $campaign)
    {
        $request->validate([
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:product_categories,id',
            'include_subcategories' => 'boolean'
        ]);

        try {
            $categoryIds = $request->category_ids;
            $includeSubcategories = $request->boolean('include_subcategories', false);

            // If include_subcategories is true, get all descendant categories
            if ($includeSubcategories) {
                $allCategoryIds = collect($categoryIds);
                
                foreach ($categoryIds as $categoryId) {
                    $category = ProductCategory::find($categoryId);
                    if ($category) {
                        $descendants = $this->getAllDescendantIds($category);
                        $allCategoryIds = $allCategoryIds->merge($descendants);
                    }
                }
                
                $categoryIds = $allCategoryIds->unique()->toArray();
            }

            // Get all products from the selected categories
            $products = Product::whereHas('categories', function($query) use ($categoryIds) {
                $query->whereIn('product_categories.id', $categoryIds);
            })
            ->whereDoesntHave('companyProducts', function($query) use ($campaign) {
                $query->where('company_id', $campaign->id);
            })
            ->get();

            $addedCount = 0;
            foreach ($products as $product) {
                $this->repository->addProductToCompany($campaign, $product->id);
                $addedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully added {$addedCount} products to the campaign",
                'added_count' => $addedCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding category products: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all descendant category IDs recursively
     */
    private function getAllDescendantIds($category)
    {
        $ids = collect();
        
        foreach ($category->children as $child) {
            $ids->push($child->id);
            $ids = $ids->merge($this->getAllDescendantIds($child));
        }
        
        return $ids;
    }
}

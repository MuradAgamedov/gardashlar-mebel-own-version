<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\Company;
use App\Models\CompanyProduct;
use App\Models\CompanyTranslate;
use App\Models\Language;
use App\Models\Product;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index()
    {
        $companies = Company::with('translates.language')->ordered()->get();
        return view('admin.companies.index', compact('companies'));
    }

    public function create()
    {
        $languages = Language::getActiveLanguages();
        return view('admin.companies.create', compact('languages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'translates' => 'required|array',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
            'translates.*.card_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'translates.*.card_image_alt_text' => 'nullable|string|max:255',
            'translates.*.inner_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'translates.*.inner_image_alt_text' => 'nullable|string|max:255',
            'translates.*.description' => 'required|string',
            'status' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $company = Company::create([
                'status' => $request->input('status', true)
            ]);

            foreach ($request->input('translates') as $translate) {
                $cardImagePath = $this->imageService->upload($request->file("translates.{$translate['language_id']}.card_image"), 'companies/card');
                $innerImagePath = $this->imageService->upload($request->file("translates.{$translate['language_id']}.inner_image"), 'companies/inner');

                CompanyTranslate::create([
                    'company_id' => $company->id,
                    'language_id' => $translate['language_id'],
                    'title' => $translate['title'],
                    'card_image' => $cardImagePath,
                    'card_image_alt_text' => $translate['card_image_alt_text'] ?? null,
                    'inner_image' => $innerImagePath,
                    'inner_image_alt_text' => $translate['inner_image_alt_text'] ?? null,
                    'description' => $translate['description'],
                ]);
            }

            DB::commit();

            return redirect()->route('admin.companies.index')
                ->with('success', 'Kompaniya uğurla əlavə edildi.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Xəta baş verdi: ' . $e->getMessage());
        }
    }

    public function edit(Company $company)
    {
        $company->load('translates');
        $languages = Language::getActiveLanguages();
        return view('admin.companies.edit', compact('company', 'languages'));
    }

    public function update(Request $request, Company $company)
    {
        $request->validate([
            'translates' => 'required|array',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
            'translates.*.card_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'translates.*.card_image_alt_text' => 'nullable|string|max:255',
            'translates.*.inner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'translates.*.inner_image_alt_text' => 'nullable|string|max:255',
            'translates.*.description' => 'required|string',
            'status' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $company->status = $request->input('status', true);
            $company->save();

            foreach ($request->input('translates') as $translate) {
                $translateData = [
                    'title' => $translate['title'],
                    'card_image_alt_text' => $translate['card_image_alt_text'] ?? null,
                    'inner_image_alt_text' => $translate['inner_image_alt_text'] ?? null,
                    'description' => $translate['description'],
                ];

                // Handle card image
                if ($request->hasFile("translates.{$translate['language_id']}.card_image")) {
                    $existingTranslate = $company->translates->where('language_id', $translate['language_id'])->first();
                    if ($existingTranslate && $existingTranslate->card_image) {
                        $this->imageService->delete($existingTranslate->card_image);
                    }
                    $translateData['card_image'] = $this->imageService->upload($request->file("translates.{$translate['language_id']}.card_image"), 'companies/card');
                }

                // Handle inner image
                if ($request->hasFile("translates.{$translate['language_id']}.inner_image")) {
                    $existingTranslate = $company->translates->where('language_id', $translate['language_id'])->first();
                    if ($existingTranslate && $existingTranslate->inner_image) {
                        $this->imageService->delete($existingTranslate->inner_image);
                    }
                    $translateData['inner_image'] = $this->imageService->upload($request->file("translates.{$translate['language_id']}.inner_image"), 'companies/inner');
                }

                CompanyTranslate::updateOrCreate(
                    [
                        'company_id' => $company->id,
                        'language_id' => $translate['language_id']
                    ],
                    $translateData
                );
            }

            DB::commit();

            return redirect()->route('admin.companies.index')
                ->with('success', 'Kompaniya uğurla yeniləndi.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Xəta baş verdi: ' . $e->getMessage());
        }
    }

    public function destroy(Company $company)
    {
        try {
            // Delete all images
            foreach ($company->translates as $translate) {
                if ($translate->card_image) {
                    $this->imageService->delete($translate->card_image);
                }
                if ($translate->inner_image) {
                    $this->imageService->delete($translate->inner_image);
                }
            }

            $company->delete();

            return redirect()->route('admin.companies.index')
                ->with('success', 'Kompaniya uğurla silindi.');

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
            foreach ($request->input('order') as $index => $id) {
                Company::where('id', $id)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show company actions and products management
     */
    public function actions(Company $company, Request $request)
    {
        $company->load(['translates.language', 'actions.translates.language']);
        
        // Get all actions for this company
        $actions = $company->actions()
            ->with(['translates.language', 'companyProducts.product.translates.language'])
            ->ordered()
            ->get();

        // Get all products with pagination and search
        $query = Product::with(['translates.language', 'companyProducts' => function($q) use ($company) {
            $q->where('company_id', $company->id);
        }]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('translates', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(20);

        return view('admin.companies.actions', compact('company', 'actions', 'products'));
    }

    /**
     * Add product to action
     */
    public function addProductToAction(Request $request, Company $company)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'action_id' => 'required|exists:actions,id',
        ]);

        try {
            // Check if product is already in this action
            $existing = CompanyProduct::where('company_id', $company->id)
                ->where('action_id', $request->action_id)
                ->where('product_id', $request->product_id)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product is already in this action'
                ], 400);
            }

            CompanyProduct::create([
                'company_id' => $company->id,
                'product_id' => $request->product_id,
                'action_id' => $request->action_id,
                'is_action_product' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product added to action successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove product from action
     */
    public function removeProductFromAction(Request $request, Company $company)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'action_id' => 'required|exists:actions,id',
        ]);

        try {
            CompanyProduct::where('company_id', $company->id)
                ->where('action_id', $request->action_id)
                ->where('product_id', $request->product_id)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product removed from action successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing product: ' . $e->getMessage()
            ], 500);
        }
    }
}

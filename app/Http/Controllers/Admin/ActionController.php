<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\ActionTranslate;
use App\Models\Company;
use App\Models\CompanyProduct;
use App\Models\Language;
use App\Models\Product;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActionController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Action::with(['company', 'translates.language']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('translates', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by company
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $actions = $query->ordered()->paginate(15);
        $companies = Company::with('translates.language')->active()->ordered()->get();

        return view('admin.actions.index', compact('actions', 'companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::with('translates.language')->active()->ordered()->get();
        $languages = Language::getActiveLanguages();
        
        return view('admin.actions.create', compact('companies', 'languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'icon' => 'nullable|string|max:255',
            'status' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'translates' => 'required|array',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
            'translates.*.description' => 'nullable|string',
            'translates.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'translates.*.image_alt_text' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $action = Action::create([
                'company_id' => $request->company_id,
                'icon' => $request->icon,
                'status' => $request->boolean('status', true),
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);

            foreach ($request->translates as $translateData) {
                $translate = new ActionTranslate($translateData);
                
                if (isset($translateData['image'])) {
                    $imagePath = $this->imageService->storeImage($translateData['image'], 'actions');
                    $translate->image = $imagePath;
                }
                
                $action->translates()->save($translate);
            }

            DB::commit();

            return redirect()->route('admin.actions.index')
                ->with('success', 'Action created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating action: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Action $action)
    {
        $action->load(['company', 'translates.language', 'companyProducts.product.translates.language']);
        
        return view('admin.actions.show', compact('action'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Action $action)
    {
        $action->load(['translates.language']);
        $companies = Company::with('translates.language')->active()->ordered()->get();
        $languages = Language::getActiveLanguages();
        
        return view('admin.actions.edit', compact('action', 'companies', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Action $action)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'icon' => 'nullable|string|max:255',
            'status' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'translates' => 'required|array',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
            'translates.*.description' => 'nullable|string',
            'translates.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'translates.*.image_alt_text' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $action->update([
                'company_id' => $request->company_id,
                'icon' => $request->icon,
                'status' => $request->boolean('status', true),
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);

            // Update translations
            foreach ($request->translates as $translateData) {
                $translate = $action->translates()
                    ->where('language_id', $translateData['language_id'])
                    ->first();

                if ($translate) {
                    if (isset($translateData['image'])) {
                        // Delete old image
                        if ($translate->image) {
                            $this->imageService->deleteImage($translate->image);
                        }
                        
                        $imagePath = $this->imageService->storeImage($translateData['image'], 'actions');
                        $translateData['image'] = $imagePath;
                    }
                    
                    $translate->update($translateData);
                } else {
                    $translate = new ActionTranslate($translateData);
                    
                    if (isset($translateData['image'])) {
                        $imagePath = $this->imageService->storeImage($translateData['image'], 'actions');
                        $translate->image = $imagePath;
                    }
                    
                    $action->translates()->save($translate);
                }
            }

            DB::commit();

            return redirect()->route('admin.actions.index')
                ->with('success', 'Action updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating action: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Action $action)
    {
        try {
            DB::beginTransaction();

            // Delete images
            foreach ($action->translates as $translate) {
                if ($translate->image) {
                    $this->imageService->deleteImage($translate->image);
                }
            }

            $action->delete();

            DB::commit();

            return redirect()->route('admin.actions.index')
                ->with('success', 'Action deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error deleting action: ' . $e->getMessage());
        }
    }

    /**
     * Show products for action management
     */
    public function products(Action $action, Request $request)
    {
        $action->load(['company', 'translates.language']);

        // Get all products with pagination and search
        $query = Product::with(['translates.language', 'companyProducts' => function($q) use ($action) {
            $q->where('action_id', $action->id);
        }]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('translates', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(20);

        return view('admin.actions.products', compact('action', 'products'));
    }

    /**
     * Add product to action
     */
    public function addProduct(Request $request, Action $action)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        try {
            // Check if product is already in this action
            $existing = CompanyProduct::where('action_id', $action->id)
                ->where('product_id', $request->product_id)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product is already in this action'
                ], 400);
            }

            CompanyProduct::create([
                'company_id' => $action->company_id,
                'product_id' => $request->product_id,
                'action_id' => $action->id,
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
    public function removeProduct(Request $request, Action $action)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        try {
            CompanyProduct::where('action_id', $action->id)
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

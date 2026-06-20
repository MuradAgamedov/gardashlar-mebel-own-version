<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Action\AddProductActionRequest;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\CompanyProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use App\Interfaces\Services\Admin\CompanyServiceInterface;
use App\Interfaces\Services\Admin\ActionServiceInterface;
use App\Http\Requests\Admin\Action\StoreActionRequest;
use App\Http\Requests\Admin\Action\UpdateActionRequest;



class ActionController extends Controller
{
    public function __construct(protected LanguageServiceInterface $languageService, protected ActionServiceInterface $actionService, protected CompanyServiceInterface $companyService)
    {
    }

    public function index(Request $request)
    {
        $actions = $this->actionService->getAction($request);
        $companies = $this->companyService->getCompanies();
        return view('admin.actions.index', compact('actions', 'companies'));
    }

    public function create()
    {
        $companies = $this->companyService->getCompanies();
        $languages = $this->languageService->getActiveLanguages();
        return view('admin.actions.create', compact('companies', 'languages'));
    }

    public function store(StoreActionRequest $request)
    {
        try {
            $this->actionService->createAction(
                [
                    'company_id' => $request->company_id,
                    'icon' => $request->icon,
                    'status' => $request->boolean('status', true),
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                ],
                $request->translates
            );

            return redirect()->route('admin.actions.index')
                ->with('success', __('Action created successfully.'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('Error creating action: :error', ['error' => $e->getMessage()]));
        }
    }

    public function show(Action $action)
    {
        $action->load(['company', 'translates.language', 'companyProducts.product.translates.language']);
        return view('admin.actions.show', compact('action'));
    }

    public function edit(Action $action)
    {
        $action->load(['translates.language']);
        $companies = $this->companyService->getCompanies();
        $languages = $this->languageService->getActiveLanguages();
        return view('admin.actions.edit', compact('action', 'companies', 'languages'));
    }

    public function update(UpdateActionRequest $request, Action $action)
    {
        try {
            $this->actionService->updateAction(
                $action,
                [
                    'company_id' => $request->company_id,
                    'icon' => $request->icon,
                    'status' => $request->boolean('status', true),
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                ],
                $request->translates
            );

            return redirect()->route('admin.actions.index')
                ->with('success', __('Action updated successfully.'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('Error updating action: :error', ['error' => $e->getMessage()]));
        }
    }

    public function destroy(Action $action)
    {
        try {
            $this->actionService->deleteAction($action);

            return redirect()->route('admin.actions.index')
                ->with('success', __('Action deleted successfully.'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error deleting action: :error', ['error' => $e->getMessage()]));
        }
    }

    public function products(Action $action, Request $request)
    {
        $action->load(['company', 'translates.language']);

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

    public function addProduct(AddProductActionRequest $request, Action $action)
    {
        $request->validated();

        try {
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

    public function removeProduct(Request $request, Action $action)
    {
        $request->validated();

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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductColor;
use Illuminate\Http\Request;
use App\Repositories\ProductColorRepository;
use App\Http\Requests\Admin\ProductColor\StoreProductColorRequest;
use App\Http\Requests\Admin\ProductColor\UpdateProductColorRequest;

class ProductColorController extends Controller
{
    public function __construct(public ProductColorRepository $repository)
    {
    }
    public function index(Request $request)
    {
        $productColors = $this->repository->getProductColors($request);
        return view('admin.product-colors.index', compact('productColors'));
    }

    public function create()
    {
        return view('admin.product-colors.create');
    }

    public function store(StoreProductColorRequest $request)
    {
        try {
            $this->repository->createProductColor($request->input('color_code'));
            return redirect()->route('admin.product-colors.index')
                ->with('success', __('Color added successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));
        }
    }

    public function edit(ProductColor $productColor)
    {
        return view('admin.product-colors.edit', compact('productColor'));
    }

    public function update(UpdateProductColorRequest $request, ProductColor $productColor)
    {
        try {
            $this->repository->updateProductColor($productColor, $request->input('color_code'));
            return redirect()->route('admin.product-colors.index')
                ->with('success', __('Color updated successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));
        }
    }

    public function destroy(ProductColor $productColor)
    {
        try {
            $this->repository->deleteProductColor($productColor);
            return redirect()->route('admin.product-colors.index')
                ->with('success', __('Color deleted successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            $count = $this->repository->bulkDeleteProductColors($request->input('ids', []));
            return response()->json(['success' => true, 'message' => __('Selected colors deleted!')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}

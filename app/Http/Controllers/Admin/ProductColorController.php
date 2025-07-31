<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductColor;
use Illuminate\Http\Request;

class ProductColorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ProductColor::query();
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('color_code', 'like', "%{$search}%");
        }
        
        $productColors = $query->orderBy('created_at', 'desc')->paginate(30);
        
        return view('admin.product-colors.index', compact('productColors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.product-colors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'color_code' => 'required|string|unique:product_colors,color_code'
        ]);

        // Custom validation for color format
        $colorCode = $request->input('color_code');
        if (!preg_match('/^(#[0-9A-F]{6}|rgba?\([^)]+\))$/i', $colorCode)) {
            return back()->withErrors(['color_code' => 'Invalid color format. Use hex (#FF0000) or rgba (rgba(255,0,0,0.5)) format.'])->withInput();
        }

        try {
            ProductColor::create([
                'color_code' => $colorCode
            ]);

            return redirect()->route('admin.product-colors.index')
                ->with('success', 'Color added successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductColor $productColor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductColor $productColor)
    {
        return view('admin.product-colors.edit', compact('productColor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductColor $productColor)
    {
        $request->validate([
            'color_code' => 'required|string|unique:product_colors,color_code,' . $productColor->id
        ]);

        // Custom validation for color format
        $colorCode = $request->input('color_code');
        if (!preg_match('/^(#[0-9A-F]{6}|rgba?\([^)]+\))$/i', $colorCode)) {
            return back()->withErrors(['color_code' => 'Invalid color format. Use hex (#FF0000) or rgba (rgba(255,0,0,0.5)) format.'])->withInput();
        }

        try {
            $productColor->update([
                'color_code' => $colorCode
            ]);

            return redirect()->route('admin.product-colors.index')
                ->with('success', 'Color updated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductColor $productColor)
    {
        try {
            $productColor->delete();

            return redirect()->route('admin.product-colors.index')
                ->with('success', 'Color deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete selected colors
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!empty($ids)) {
            ProductColor::whereIn('id', $ids)->delete();
            return response()->json(['success' => true, 'message' => 'Seçilən rənglər silindi!']);
        }
        return response()->json(['success' => false, 'message' => 'Heç bir rəng seçilməyib!']);
    }
}

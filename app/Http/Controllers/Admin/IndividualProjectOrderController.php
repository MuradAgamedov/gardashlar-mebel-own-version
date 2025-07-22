<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IndividualProjectOrder;
use App\Models\IndividualProjectOrderTranslate;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndividualProjectOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $individualProjectOrders = IndividualProjectOrder::with('translates.language')->ordered()->get();

        return view('admin.individual-project-orders.index', compact('individualProjectOrders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::getActiveLanguages();
        return view('admin.individual-project-orders.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'translates' => 'required|array',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
            'translates.*.description' => 'required|string',
            'status' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            // Create individual project order
            $individualProjectOrder = IndividualProjectOrder::create([
                'status' => $request->input('status', true)
            ]);

            // Create translates
            foreach ($request->input('translates') as $translate) {
                IndividualProjectOrderTranslate::create([
                    'individual_project_order_id' => $individualProjectOrder->id,
                    'language_id' => $translate['language_id'],
                    'title' => $translate['title'],
                    'description' => $translate['description'],
                ]);
            }

            DB::commit();

            return redirect()->route('admin.individual-project-orders.index')
                ->with('success', 'Individual project order successfully added.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(IndividualProjectOrder $individualProjectOrder)
    {
        $individualProjectOrder->load('translates');
        $languages = Language::getActiveLanguages();
        
        return view('admin.individual-project-orders.edit', compact('individualProjectOrder', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, IndividualProjectOrder $individualProjectOrder)
    {
        $request->validate([
            'translates' => 'required|array',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
            'translates.*.description' => 'required|string',
            'status' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            // Update individual project order
            $individualProjectOrder->update([
                'status' => $request->input('status', true)
            ]);

            // Delete existing translates
            $individualProjectOrder->translates()->delete();

            // Create new translates
            foreach ($request->input('translates') as $translate) {
                IndividualProjectOrderTranslate::create([
                    'individual_project_order_id' => $individualProjectOrder->id,
                    'language_id' => $translate['language_id'],
                    'title' => $translate['title'],
                    'description' => $translate['description'],
                ]);
            }

            DB::commit();

            return redirect()->route('admin.individual-project-orders.index')
                ->with('success', 'Individual project order successfully updated.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(IndividualProjectOrder $individualProjectOrder)
    {
        try {
            DB::beginTransaction();

            // Delete translates first (due to foreign key constraint)
            $individualProjectOrder->translates()->delete();
            
            // Delete the individual project order
            $individualProjectOrder->delete();

            DB::commit();

            return redirect()->route('admin.individual-project-orders.index')
                ->with('success', 'Individual project order successfully deleted.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Reorder individual project orders
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:individual_project_orders,id'
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->input('order') as $index => $id) {
                IndividualProjectOrder::where('id', $id)->update(['order' => $index + 1]);
            }

            DB::commit();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}

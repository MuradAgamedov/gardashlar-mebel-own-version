<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Repositories\CurrencyRepository;

class CurrencyController extends Controller
{
    public function __construct(public CurrencyRepository $repository)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $currencies = $this->repository->getCurrencies();
        return view('admin.currencies.index', compact('currencies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.currencies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|max:3|unique:currencies,code',
            'course' => 'required|numeric|min:0.0001|max:999999.9999',
        ]);

        try {
            // Get the highest order and add 1 for new item
            $maxOrder = Currency::max('order') ?? 0;
            
            Currency::create([
                'code' => strtoupper($request->code),
                'course' => $request->course,
                'order' => $maxOrder + 1,
                'status' => $request->has('status')
            ]);

            return redirect()->route('admin.currencies.index')
                ->with('success', 'Currency created successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating currency: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Currency $currency): View
    {
        return view('admin.currencies.edit', compact('currency'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Currency $currency): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|max:3|unique:currencies,code,' . $currency->id,
            'course' => 'required|numeric|min:0.0001|max:999999.9999',
        ]);

        try {
            $currency->update([
                'code' => strtoupper($request->code),
                'course' => $request->course,
                'status' => $request->has('status')
            ]);

            return redirect()->route('admin.currencies.index')
                ->with('success', 'Currency updated successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating currency: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Currency $currency): RedirectResponse
    {
        try {
            $currency->delete();
            return redirect()->route('admin.currencies.index')
                ->with('success', 'Currency deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting currency: ' . $e->getMessage());
        }
    }

    /**
     * Reorder currencies
     */
    public function reorder(Request $request): RedirectResponse
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*' => 'integer|exists:currencies,id'
        ]);

        try {
            foreach ($request->orders as $order => $id) {
                Currency::where('id', $id)->update(['order' => $order]);
            }

            return redirect()->route('admin.currencies.index')
                ->with('success', 'Currencies reordered successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error reordering currencies: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status
     */
    public function toggleStatus(Currency $currency): RedirectResponse
    {
        try {
            $currency->update(['status' => !$currency->status]);
            
            $status = $currency->status ? 'enabled' : 'disabled';
            return redirect()->route('admin.currencies.index')
                ->with('success', "Currency {$status} successfully.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating currency status: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete currencies
     */
    public function bulkDelete(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:currencies,id'
        ]);

        try {
            $deletedCount = Currency::whereIn('id', $request->ids)->delete();
            
            return redirect()->route('admin.currencies.index')
                ->with('success', "{$deletedCount} currency(ies) deleted successfully.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting currencies: ' . $e->getMessage());
        }
    }

    /**
     * Bulk toggle status currencies
     */
    public function bulkToggleStatus(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:currencies,id'
        ]);

        try {
            $currencies = Currency::whereIn('id', $request->ids)->get();
            $updatedCount = 0;

            foreach ($currencies as $currency) {
                $currency->update(['status' => !$currency->status]);
                $updatedCount++;
            }
            
            return redirect()->route('admin.currencies.index')
                ->with('success', "{$updatedCount} currency(ies) status toggled successfully.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error toggling currency status: ' . $e->getMessage());
        }
    }
}

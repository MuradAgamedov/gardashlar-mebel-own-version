<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Repositories\CurrencyRepository;
use App\Http\Requests\Admin\Currency\StoreCurrencyRequest;
use App\Http\Requests\Admin\Currency\UpdateCurrencyRequest;

class CurrencyController extends Controller
{
    public function __construct(public CurrencyRepository $repository)
    {
    }

    public function index(): View
    {
        $currencies = $this->repository->getCurrencies();
        return view('admin.currencies.index', compact('currencies'));
    }

    public function create(): View
    {
        return view('admin.currencies.create');
    }

    public function store(StoreCurrencyRequest $request): RedirectResponse
    {
        try {
            $this->repository->createCurrency([
                'code' => $request->code,
                'course' => $request->course,
                'status' => $request->has('status')
            ]);

            return redirect()->route('admin.currencies.index')
                ->with('success', 'Currency created successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating currency: ' . $e->getMessage());
        }
    }

    public function edit(Currency $currency): View
    {
        return view('admin.currencies.edit', compact('currency'));
    }

    public function update(UpdateCurrencyRequest $request, Currency $currency): RedirectResponse
    {
        try {
            $this->repository->updateCurrency($currency, [
                'code' => $request->code,
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

    public function destroy(Currency $currency): RedirectResponse
    {
        try {
            $this->repository->deleteCurrency($currency);
            return redirect()->route('admin.currencies.index')
                ->with('success', 'Currency deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting currency: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request): RedirectResponse
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*' => 'integer|exists:currencies,id'
        ]);

        try {
            $this->repository->reorderCurrencies($request->orders);
            return redirect()->route('admin.currencies.index')
                ->with('success', 'Currencies reordered successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error reordering currencies: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Currency $currency): RedirectResponse
    {
        try {
            $this->repository->toggleStatus($currency);
            $status = $currency->fresh()->status ? 'enabled' : 'disabled';
            return redirect()->route('admin.currencies.index')
                ->with('success', "Currency {$status} successfully.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating currency status: ' . $e->getMessage());
        }
    }

    public function bulkDelete(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:currencies,id'
        ]);

        try {
            $deletedCount = $this->repository->bulkDelete($request->ids);
            return redirect()->route('admin.currencies.index')
                ->with('success', "{$deletedCount} currency(ies) deleted successfully.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting currencies: ' . $e->getMessage());
        }
    }

    public function bulkToggleStatus(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:currencies,id'
        ]);

        try {
            $updatedCount = $this->repository->bulkToggleStatus($request->ids);
            return redirect()->route('admin.currencies.index')
                ->with('success', "{$updatedCount} currency(ies) status toggled successfully.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error toggling currency status: ' . $e->getMessage());
        }
    }
}

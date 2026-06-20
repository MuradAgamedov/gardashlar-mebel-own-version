<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Currency\BulkDeleteCurrencyRequest;

use App\Http\Requests\Admin\Currency\ReorderCurrencyRequest;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Interfaces\Services\Admin\CurrencyServiceInterface;
use App\Http\Requests\Admin\Currency\StoreCurrencyRequest;
use App\Http\Requests\Admin\Currency\UpdateCurrencyRequest;

class CurrencyController extends Controller
{
    public function __construct(protected CurrencyServiceInterface $currencyService)
    {
    }

    public function index(): View
    {
        $currencies = $this->currencyService->getCurrencies();
        return view('admin.currencies.index', compact('currencies'));
    }

    public function create(): View
    {
        return view('admin.currencies.create');
    }

    public function store(StoreCurrencyRequest $request): RedirectResponse
    {
        try {
            $this->currencyService->createCurrency([
                'code' => $request->code,
                'course' => $request->course,
                'status' => $request->has('status')
            ]);

            return redirect()->route('admin.currencies.index')
                ->with('success', __('Currency created successfully.'));

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', __('Error creating currency: :error', ['error' => $e->getMessage()]));
        }
    }

    public function edit(Currency $currency): View
    {
        return view('admin.currencies.edit', compact('currency'));
    }

    public function update(UpdateCurrencyRequest $request, Currency $currency): RedirectResponse
    {
        try {
            $this->currencyService->updateCurrency($currency, [
                'code' => $request->code,
                'course' => $request->course,
                'status' => $request->status == '1'
            ]);

            return redirect()->route('admin.currencies.index')
                ->with('success', __('Currency updated successfully.'));

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', __('Error updating currency: :error', ['error' => $e->getMessage()]));
        }
    }

    public function destroy(Currency $currency): RedirectResponse
    {
        try {
            $this->currencyService->deleteCurrency($currency);
            return redirect()->route('admin.currencies.index')
                ->with('success', __('Currency deleted successfully.'));
        } catch (\Exception $e) {
            return back()->with('error', __('Error deleting currency: :error', ['error' => $e->getMessage()]));
        }
    }

    public function reorder(ReorderCurrencyRequest $request): RedirectResponse
    {
        $request->validated();

        try {
            $this->currencyService->reorderCurrencies($request->orders);
            return redirect()->route('admin.currencies.index')
                ->with('success', __('Currencies reordered successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error reordering currencies: :error', ['error' => $e->getMessage()]));
        }
    }

    public function toggleStatus(Currency $currency): RedirectResponse
    {
        try {
            $this->currencyService->toggleStatus($currency);
            $status = $currency->fresh()->status ? 'enabled' : 'disabled';
            return redirect()->route('admin.currencies.index')
                ->with('success', __('Currency :status successfully.', ['status' => $status]));

        } catch (\Exception $e) {
            return back()->with('error', __('Error updating currency status: :error', ['error' => $e->getMessage()]));
        }
    }

    public function bulkDelete(BulkDeleteCurrencyRequest $request): RedirectResponse
    {
        $request->validated();

        try {
            $deletedCount = $this->currencyService->bulkDelete($request->ids);
            return redirect()->route('admin.currencies.index')
                ->with('success', __(':count currency(ies) deleted successfully.', ['count' => $deletedCount]));

        } catch (\Exception $e) {
            return back()->with('error', __('Error deleting currencies: :error', ['error' => $e->getMessage()]));
        }
    }

    public function bulkToggleStatus(Request $request): RedirectResponse
    {
        $request->validated();

        try {
            $updatedCount = $this->currencyService->bulkToggleStatus($request->ids);
            return redirect()->route('admin.currencies.index')
                ->with('success', __(':count currency(ies) status toggled successfully.', ['count' => $updatedCount]));

        } catch (\Exception $e) {
            return back()->with('error', __('Error toggling currency status: :error', ['error' => $e->getMessage()]));
        }
    }
}

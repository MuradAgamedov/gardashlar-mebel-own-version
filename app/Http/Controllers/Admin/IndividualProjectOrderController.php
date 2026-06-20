<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\IndividualProjectOrder\DeleteSelectedIndividualProjectOrderRequest;

use App\Http\Requests\Admin\IndividualProjectOrder\ReorderIndividualProjectOrderRequest;

use App\Http\Controllers\Controller;
use App\Models\IndividualProjectOrder;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\IndividualProjectOrderServiceInterface;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use App\Http\Requests\Admin\IndividualProjectOrder\StoreIndividualProjectOrderRequest;
use App\Http\Requests\Admin\IndividualProjectOrder\UpdateIndividualProjectOrderRequest;

class IndividualProjectOrderController extends Controller
{
    public function __construct(
        protected IndividualProjectOrderServiceInterface $individualProjectOrderService,
        protected LanguageServiceInterface $languageService
    )
    {
    }

    public function index(Request $request)
    {
        $individualProjectOrders = $this->individualProjectOrderService->getIndividualProjectOrders($request);
        return view('admin.individual-project-orders.index', compact('individualProjectOrders'));
    }

    public function create()
    {
        $languages = $this->languageService->getActiveLanguages();
        return view('admin.individual-project-orders.create', compact('languages'));
    }

    public function store(StoreIndividualProjectOrderRequest $request)
    {
        try {
            $this->individualProjectOrderService->createIndividualProjectOrder(
                ['status' => $request->input('status', true)],
                $request->input('translates')
            );

            return redirect()->route('admin.individual-project-orders.index')
                ->with('success', __('Individual project order successfully added.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));
        }
    }

    public function edit(IndividualProjectOrder $individualProjectOrder)
    {
        $individualProjectOrder->load('translates');
        $languages = $this->languageService->getActiveLanguages();
        return view('admin.individual-project-orders.edit', compact('individualProjectOrder', 'languages'));
    }

    public function update(UpdateIndividualProjectOrderRequest $request, IndividualProjectOrder $individualProjectOrder)
    {
        try {
            $this->individualProjectOrderService->updateIndividualProjectOrder(
                $individualProjectOrder,
                ['status' => $request->input('status', true)],
                $request->input('translates')
            );

            return redirect()->route('admin.individual-project-orders.index')
                ->with('success', __('Individual project order successfully updated.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));
        }
    }

    public function destroy(IndividualProjectOrder $individualProjectOrder)
    {
        try {
            $this->individualProjectOrderService->deleteIndividualProjectOrder($individualProjectOrder);
            return redirect()->route('admin.individual-project-orders.index')
                ->with('success', __('Individual project order successfully deleted.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));
        }
    }

    public function reorder(ReorderIndividualProjectOrderRequest $request)
    {
        $request->validated();

        try {
            $this->individualProjectOrderService->reorderIndividualProjectOrders($request->input('order'));
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteSelected(DeleteSelectedIndividualProjectOrderRequest $request)
    {
        $request->validated();

        try {
            $count = $this->individualProjectOrderService->deleteSelected($request->ids);
            return redirect()->route('admin.individual-project-orders.index')
                ->with('success', __(':count individual project order(s) deleted successfully.', ['count' => $count]));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));
        }
    }
}

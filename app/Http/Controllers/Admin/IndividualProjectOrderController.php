<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IndividualProjectOrder;
use Illuminate\Http\Request;
use App\Repositories\IndividualProjectOrderRepository;
use App\Repositories\LanguageRepository;
use App\Http\Requests\Admin\IndividualProjectOrder\StoreIndividualProjectOrderRequest;
use App\Http\Requests\Admin\IndividualProjectOrder\UpdateIndividualProjectOrderRequest;

class IndividualProjectOrderController extends Controller
{
    public function __construct(
        public IndividualProjectOrderRepository $repository,
        public LanguageRepository $languageRepository
    )
    {
    }

    public function index(Request $request)
    {
        $individualProjectOrders = $this->repository->getIndividualProjectOrders($request);
        return view('admin.individual-project-orders.index', compact('individualProjectOrders'));
    }

    public function create()
    {
        $languages = $this->languageRepository->getActiveLanguages();
        return view('admin.individual-project-orders.create', compact('languages'));
    }

    public function store(StoreIndividualProjectOrderRequest $request)
    {
        try {
            $this->repository->createIndividualProjectOrder(
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
        $languages = $this->languageRepository->getActiveLanguages();
        return view('admin.individual-project-orders.edit', compact('individualProjectOrder', 'languages'));
    }

    public function update(UpdateIndividualProjectOrderRequest $request, IndividualProjectOrder $individualProjectOrder)
    {
        try {
            $this->repository->updateIndividualProjectOrder(
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
            $this->repository->deleteIndividualProjectOrder($individualProjectOrder);
            return redirect()->route('admin.individual-project-orders.index')
                ->with('success', __('Individual project order successfully deleted.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:individual_project_orders,id'
        ]);

        try {
            $this->repository->reorderIndividualProjectOrders($request->input('order'));
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteSelected(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:individual_project_orders,id'
        ]);

        try {
            $count = $this->repository->deleteSelected($request->ids);
            return redirect()->route('admin.individual-project-orders.index')
                ->with('success', __(':count individual project order(s) deleted successfully.', ['count' => $count]));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));
        }
    }
}

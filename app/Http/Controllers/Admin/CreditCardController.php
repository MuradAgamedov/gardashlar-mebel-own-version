<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreditCard;
use Illuminate\Http\Request;
use App\Repositories\CreditCardRepository;
use App\Http\Requests\Admin\CreditCard\StoreCreditCardRequest;
use App\Http\Requests\Admin\CreditCard\UpdateCreditCardRequest;

class CreditCardController extends Controller
{
    public function __construct(public CreditCardRepository $repository)
    {
    }

    public function index()
    {
        $creditCards = $this->repository->getCreditCards();
        return view('admin.CreditCard.index', compact('creditCards'));
    }

    public function create()
    {
        return view('admin.CreditCard.create');
    }

    public function store(StoreCreditCardRequest $request)
    {
        try {
            $this->repository->createCreditCard(
                [
                    'title' => $request->input('title'),
                    'months' => $request->input('months'),
                ],
                $request->file('image')
            );

            return redirect()->route('admin.credit-cards.index')
                ->with('success', __('Credit Card created successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error creating credit card: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }

    public function edit(CreditCard $creditCard)
    {
        return view('admin.CreditCard.edit', compact('creditCard'));
    }

    public function update(UpdateCreditCardRequest $request, CreditCard $creditCard)
    {
        try {
            $this->repository->updateCreditCard(
                $creditCard,
                [
                    'title' => $request->input('title'),
                    'months' => $request->input('months'),
                ],
                $request->file('image')
            );

            return redirect()->route('admin.credit-cards.index')
                ->with('success', __('Credit Card updated successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error updating credit card: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }

    public function destroy(CreditCard $creditCard)
    {
        try {
            $this->repository->deleteCreditCard($creditCard);
            return redirect()->route('admin.credit-cards.index')
                ->with('success', __('Credit Card deleted successfully!'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error deleting credit card: :error', ['error' => $e->getMessage()]));
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'credit_cards' => 'required|array',
            'credit_cards.*' => 'required|integer|exists:credit_cards,id',
        ]);

        try {
            $this->repository->reorderCreditCards($request->credit_cards);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

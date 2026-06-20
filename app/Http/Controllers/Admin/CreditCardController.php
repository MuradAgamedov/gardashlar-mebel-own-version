<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CreditCard\ReorderCreditCardRequest;

use App\Http\Controllers\Controller;
use App\Models\CreditCard;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\CreditCardServiceInterface;
use App\Http\Requests\Admin\CreditCard\StoreCreditCardRequest;
use App\Http\Requests\Admin\CreditCard\UpdateCreditCardRequest;

class CreditCardController extends Controller
{
    public function __construct(protected CreditCardServiceInterface $creditCardService)
    {
    }

    public function index()
    {
        $creditCards = $this->creditCardService->getCreditCards();
        return view('admin.CreditCard.index', compact('creditCards'));
    }

    public function create()
    {
        return view('admin.CreditCard.create');
    }

    public function store(StoreCreditCardRequest $request)
    {
        try {
            $this->creditCardService->createCreditCard(
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
            $this->creditCardService->updateCreditCard(
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
            $this->creditCardService->deleteCreditCard($creditCard);
            return redirect()->route('admin.credit-cards.index')
                ->with('success', __('Credit Card deleted successfully!'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error deleting credit card: :error', ['error' => $e->getMessage()]));
        }
    }

    public function reorder(ReorderCreditCardRequest $request)
    {
        $request->validated();

        try {
            $this->creditCardService->reorderCreditCards($request->credit_cards);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

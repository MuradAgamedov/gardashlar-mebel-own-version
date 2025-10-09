<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreditCard;
use App\Services\ImageService;
use Illuminate\Http\Request;
use App\Repositories\CreditCardRepository;

class CreditCardController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService, public CreditCardRepository $repository)
    {
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $creditCards = $this->repository->getCreditCards();
        return view('admin.CreditCard.index', compact('creditCards'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.CreditCard.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'title'  => 'required|string|max:255',
            'months' => 'nullable|string|max:255',
            'image'  => 'nullable|image|mimes:jpg,jpeg,png,gif,webp',
        ]);
       
        try {
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $this->imageService->upload(
                    $request->file('image'),
                    'credit-cards',
                    [
                        'max_size' => 2048,
                        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                    ]
                );
            }

            // Yeni kart əlavə ediləndə order ən sonda getsin
            $lastOrder = CreditCard::max('order') ?? 0;

            CreditCard::create([
                'title'  => $request->input('title'),
                'months' => $request->input('months'),
                'order'  => $lastOrder + 1,
                'image'  => $imagePath,
            ]);

            return redirect()->route('admin.credit-cards.index')
                ->with('success', 'Credit Card created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating credit card: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CreditCard $creditCard)
    {
        return view('admin.CreditCard.edit', compact('creditCard'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CreditCard $creditCard)
    {
        dd(1);

        $request->validate([
            'title'  => 'required|string|max:255',
            'months' => 'nullable|string|max:255',
            'image'  => 'nullable|image|mimes:jpg,jpeg,png,gif,webp',
        ]);

        try {
            $updateData = [
                'title'  => $request->input('title'),
                'months' => $request->input('months'),
            ];

            if ($request->hasFile('image')) {
                $uploadedPath = $this->imageService->update(
                    $request->file('image'),
                    $creditCard->image,
                    'credit-cards',
                    [
                        'max_size' => 2048,
                        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                    ]
                );

                if ($uploadedPath) {
                    $updateData['image'] = $uploadedPath;
                }
            }

            $creditCard->update($updateData);

            return redirect()->route('admin.credit-cards.index')
                ->with('success', 'Credit Card updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating credit card: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CreditCard $creditCard)
    {
        try {
            $creditCard->delete();
            return redirect()->route('admin.credit-cards.index')
                ->with('success', 'Credit Card deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting credit card: ' . $e->getMessage());
        }
    }

    /**
     * Reorder credit cards (drag & drop)
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'credit_cards' => 'required|array',
            'credit_cards.*' => 'required|integer|exists:credit_cards,id',
        ]);

        try {
            foreach ($request->credit_cards as $index => $cardId) {
                CreditCard::where('id', $cardId)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

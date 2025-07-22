<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryPayment;
use App\Models\Language;
use Illuminate\Http\Request;

class DeliveryPaymentController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function edit()
    {
        $deliveryPayment = DeliveryPayment::with(['translates.language'])->first();
        
        if (!$deliveryPayment) {
            $deliveryPayment = DeliveryPayment::create(['status' => true]);
        }
        
        $languages = Language::all();
        
        return view('admin.delivery-payment.edit', compact('deliveryPayment', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $languages = Language::all();

        // Translation validation - only for languages that have data
        foreach ($languages as $index => $language) {
            $translationData = $request->input("translations.{$index}", []);
            
            // Only validate if title or content is provided
            if (!empty($translationData['title']) || !empty($translationData['content'])) {
                $request->validate([
                    "translations.{$index}.title" => 'required|string|max:255',
                    "translations.{$index}.content" => 'required|string',
                ], [
                    "translations.{$index}.title.required" => "Title is required for {$language->lang_code}.",
                    "translations.{$index}.title.max" => "Title may not be greater than 255 characters for {$language->lang_code}.",
                    "translations.{$index}.content.required" => "Content is required for {$language->lang_code}.",
                ]);
            }
        }

        try {
            $deliveryPayment = DeliveryPayment::first();
            
            if (!$deliveryPayment) {
                $deliveryPayment = DeliveryPayment::create([
                    'status' => true,
                ]);
            }

            // Update or create translations - only for languages that have data
            foreach ($languages as $index => $language) {
                $translationData = $request->input("translations.{$index}", []);
                
                // Only update if title or content is provided
                if (!empty($translationData['title']) || !empty($translationData['content'])) {
                    $deliveryPayment->translates()->updateOrCreate(
                        ['language_id' => $language->id],
                        [
                            'title' => trim($translationData['title'] ?? ''),
                            'content' => trim($translationData['content'] ?? ''),
                        ]
                    );
                }
            }

            return redirect()->back()
                ->with('success', 'Delivery & Payment updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating delivery & payment: ' . $e->getMessage())
                ->withInput();
        }
    }
}

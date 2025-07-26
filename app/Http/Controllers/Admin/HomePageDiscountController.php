<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomePageDiscount;
use App\Models\Language;
use App\Services\ImageService;
use Illuminate\Http\Request;

class HomePageDiscountController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Display the specified resource.
     */
    public function edit()
    {
        $homePageDiscount = HomePageDiscount::with(['translates.language'])->first();
        
        if (!$homePageDiscount) {
            $homePageDiscount = HomePageDiscount::create();
        }
        
        $languages = Language::all();
        
        return view('admin.home-page-discount.edit', compact('homePageDiscount', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $languages = Language::all();
        
        // Translation validation
        foreach ($languages as $language) {
            $request->validate([
                "translations.{$language->id}.title" => 'required|string|max:255',
                "translations.{$language->id}.subtitle" => 'required|string|max:255',
                "translations.{$language->id}.button_link" => 'required|string|max:255',
            ], [
                "translations.{$language->id}.title.required" => "Title is required for {$language->lang_code}.",
                "translations.{$language->id}.title.max" => "Title may not be greater than 255 characters for {$language->lang_code}.",
                "translations.{$language->id}.subtitle.required" => "Subtitle is required for {$language->lang_code}.",
                "translations.{$language->id}.subtitle.max" => "Subtitle may not be greater than 255 characters for {$language->lang_code}.",
                "translations.{$language->id}.button_link.required" => "Button link is required for {$language->lang_code}.",
                "translations.{$language->id}.button_link.max" => "Button link may not be greater than 255 characters for {$language->lang_code}.",
            ]);
        }

        // Background image validation
        if ($request->hasFile('bg_image')) {
            $request->validate([
                'bg_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);
        }

        try {
            $homePageDiscount = HomePageDiscount::first();
            
            if (!$homePageDiscount) {
                $homePageDiscount = HomePageDiscount::create();
            }

            // Handle background image upload
            if ($request->hasFile('bg_image')) {
                $imagePath = $this->imageService->update(
                    $request->file('bg_image'), 
                    $homePageDiscount->bg_image, 
                    'home-page-discount'
                );
                $homePageDiscount->update(['bg_image' => $imagePath]);
            }

            // Update translations
            foreach ($languages as $language) {
                $translationData = $request->input("translations.{$language->id}", []);
                
                $homePageDiscount->translates()->updateOrCreate(
                    ['language_id' => $language->id],
                    [
                        'title' => trim($translationData['title'] ?? ''),
                        'subtitle' => trim($translationData['subtitle'] ?? ''),
                        'button_link' => trim($translationData['button_link'] ?? ''),
                    ]
                );
            }

            return redirect()->back()
                ->with('success', 'Home Page Discount section updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating home page discount section: ' . $e->getMessage())
                ->withInput();
        }
    }
}

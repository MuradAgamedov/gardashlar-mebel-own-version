<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BecomeAPartnerAdvantage;
use App\Models\Language;
use App\Services\ImageService;
use Illuminate\Http\Request;

class BecomeAPartnerAdvantageController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $advantages = BecomeAPartnerAdvantage::with('translates.language')->orderBy('order')->get();
        $languages = Language::all();
        
        return view('admin.become-a-partner-advantages.index', compact('advantages', 'languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::all();
        return view('admin.become-a-partner-advantages.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $languages = Language::all();
        
        // Base validation
        $request->validate([
            'icon' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,svg|max:2048',
            'status' => 'boolean',
        ], [
            'icon.image' => 'Icon must be an image file.',
            'icon.mimes' => 'Icon must be a file of type: jpg, jpeg, png, gif, webp, svg.',
            'icon.max' => 'Icon may not be greater than 2MB.',
            'status.boolean' => 'Status must be true or false.',
        ]);

        // Translation validation
        foreach ($languages as $language) {
            $request->validate([
                "translations.{$language->id}.icon_alt_text" => 'required|string|max:255',
                "translations.{$language->id}.title" => 'required|string|max:255',
                "translations.{$language->id}.description" => 'required|string|max:1000',
            ], [
                "translations.{$language->id}.icon_alt_text.required" => "Icon alt text is required for {$language->lang_code}.",
                "translations.{$language->id}.icon_alt_text.max" => "Icon alt text may not be greater than 255 characters for {$language->lang_code}.",
                "translations.{$language->id}.title.required" => "Title is required for {$language->lang_code}.",
                "translations.{$language->id}.title.max" => "Title may not be greater than 255 characters for {$language->lang_code}.",
                "translations.{$language->id}.description.required" => "Description is required for {$language->lang_code}.",
                "translations.{$language->id}.description.max" => "Description may not be greater than 1000 characters for {$language->lang_code}.",
            ]);
        }

        try {
            // Handle icon upload
            $iconPath = null;
            if ($request->hasFile('icon')) {
                $iconPath = $this->imageService->upload(
                    $request->file('icon'),
                    'become-a-partner-advantages',
                    [
                        'max_size' => 2048,
                        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
                    ]
                );
                
                if (!$iconPath) {
                    return redirect()->back()
                        ->with('error', 'Failed to upload icon.')
                        ->withInput();
                }
            }

            // Create advantage
            $advantage = BecomeAPartnerAdvantage::create([
                'icon' => $iconPath,
                'status' => $request->input('status'),
            ]);

            // Create translations
            foreach ($languages as $language) {
                $translationData = $request->input("translations.{$language->id}", []);
                
                $advantage->translates()->create([
                    'language_id' => $language->id,
                    'icon_alt_text' => trim($translationData['icon_alt_text'] ?? ''),
                    'title' => trim($translationData['title'] ?? ''),
                    'description' => trim($translationData['description'] ?? ''),
                ]);
            }

            return redirect()->route('admin.become-a-partner.edit')
                ->with('success', 'Advantage created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating advantage: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BecomeAPartnerAdvantage $becomeAPartnerAdvantage)
    {
        $becomeAPartnerAdvantage->load('translates.language');
        $languages = Language::all();
        
        return view('admin.become-a-partner-advantages.edit', compact('becomeAPartnerAdvantage', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BecomeAPartnerAdvantage $becomeAPartnerAdvantage)
    {
        $languages = Language::all();
        
        // Base validation
        $request->validate([
            'icon' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,svg|max:2048',
            'status' => 'boolean',
        ], [
            'icon.image' => 'Icon must be an image file.',
            'icon.mimes' => 'Icon must be a file of type: jpg, jpeg, png, gif, webp, svg.',
            'icon.max' => 'Icon may not be greater than 2MB.',
            'status.boolean' => 'Status must be true or false.',
        ]);

        // Translation validation
        foreach ($languages as $language) {
            $request->validate([
                "translations.{$language->id}.icon_alt_text" => 'required|string|max:255',
                "translations.{$language->id}.title" => 'required|string|max:255',
                "translations.{$language->id}.description" => 'required|string|max:1000',
            ], [
                "translations.{$language->id}.icon_alt_text.required" => "Icon alt text is required for {$language->lang_code}.",
                "translations.{$language->id}.icon_alt_text.max" => "Icon alt text may not be greater than 255 characters for {$language->lang_code}.",
                "translations.{$language->id}.title.required" => "Title is required for {$language->lang_code}.",
                "translations.{$language->id}.title.max" => "Title may not be greater than 255 characters for {$language->lang_code}.",
                "translations.{$language->id}.description.required" => "Description is required for {$language->lang_code}.",
                "translations.{$language->id}.description.max" => "Description may not be greater than 1000 characters for {$language->lang_code}.",
            ]);
        }

        try {
            // Handle icon upload
            $imageData = [];
            if ($request->hasFile('icon')) {
                $uploadedPath = $this->imageService->update(
                    $request->file('icon'),
                    $becomeAPartnerAdvantage->icon,
                    'become-a-partner-advantages',
                    [
                        'max_size' => 2048,
                        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
                    ]
                );
                
                if ($uploadedPath) {
                    $imageData['icon'] = $uploadedPath;
                } else {
                    return redirect()->back()
                        ->with('error', 'Failed to upload icon.')
                        ->withInput();
                }
            }

            // Update advantage
            $becomeAPartnerAdvantage->update(array_merge($imageData, [
                'status' => $request->input('status'),
            ]));

            // Update translations
            foreach ($languages as $language) {
                $translationData = $request->input("translations.{$language->id}", []);
                
                $becomeAPartnerAdvantage->translates()->updateOrCreate(
                    ['language_id' => $language->id],
                    [
                        'icon_alt_text' => trim($translationData['icon_alt_text'] ?? ''),
                        'title' => trim($translationData['title'] ?? ''),
                        'description' => trim($translationData['description'] ?? ''),
                    ]
                );
            }

            return redirect()->route('admin.become-a-partner.edit')
                ->with('success', 'Advantage updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating advantage: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BecomeAPartnerAdvantage $becomeAPartnerAdvantage)
    {
        try {
            $becomeAPartnerAdvantage->delete();
            return redirect()->route('admin.become-a-partner.edit')
                ->with('success', 'Advantage deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting advantage: ' . $e->getMessage());
        }
    }

    /**
     * Reorder advantages
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'advantages' => 'required|array',
            'advantages.*' => 'required|integer|exists:become_a_partner_advantages,id',
        ]);

        try {
            foreach ($request->advantages as $index => $advantageId) {
                BecomeAPartnerAdvantage::where('id', $advantageId)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete selected advantages
     */
    public function deleteSelected(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:become_a_partner_advantages,id'
        ]);

        try {
            // Delete translations first
            \App\Models\BecomeAPartnerAdvantageTranslate::whereIn('become_a_partner_advantage_id', $request->ids)->delete();
            
            // Delete advantages
            BecomeAPartnerAdvantage::whereIn('id', $request->ids)->delete();

            return redirect()->route('admin.become-a-partner.edit')
                ->with('success', count($request->ids) . ' advantage(s) deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }
}

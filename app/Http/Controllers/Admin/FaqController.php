<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Language;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Faq::with(['translates.language']);
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->whereHas('translates', function($q) use ($searchTerm) {
                $q->where('question', 'like', '%' . $searchTerm . '%')
                  ->orWhere('answer', 'like', '%' . $searchTerm . '%');
            });
        }
        
        $faqs = $query->ordered()->get();
        return view('admin.faqs.index', compact('faqs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::all();
        return view('admin.faqs.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $languages = Language::all();

        // Translation validation - only for languages that have data
        foreach ($languages as $index => $language) {
            $translationData = $request->input("translations.{$index}", []);
            
            // Only validate if question or answer is provided
            if (!empty($translationData['question']) || !empty($translationData['answer'])) {
                $request->validate([
                    "translations.{$index}.question" => 'required|string|max:255',
                    "translations.{$index}.answer" => 'required|string',
                ], [
                    "translations.{$index}.question.required" => "Question is required for {$language->lang_code}.",
                    "translations.{$index}.question.max" => "Question may not be greater than 255 characters for {$language->lang_code}.",
                    "translations.{$index}.answer.required" => "Answer is required for {$language->lang_code}.",
                ]);
            }
        }

        try {
            // Get the highest order number
            $maxOrder = Faq::max('order') ?? 0;
            
            $faq = Faq::create([
                'order' => $maxOrder + 1,
                'status' => $request->input('status', true),
            ]);

            // Create translations - only for languages that have data
            foreach ($languages as $index => $language) {
                $translationData = $request->input("translations.{$index}", []);
                
                // Only create if question or answer is provided
                if (!empty($translationData['question']) || !empty($translationData['answer'])) {
                    $faq->translates()->create([
                        'language_id' => $language->id,
                        'question' => trim($translationData['question'] ?? ''),
                        'answer' => trim($translationData['answer'] ?? ''),
                    ]);
                }
            }

            return redirect()->route('admin.faqs.index')
                ->with('success', 'FAQ created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating FAQ: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Faq $faq)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Faq $faq)
    {
        $faq->load(['translates.language']);
        $languages = Language::all();
        return view('admin.faqs.edit', compact('faq', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Faq $faq)
    {
        $languages = Language::all();

        // Translation validation - only for languages that have data
        foreach ($languages as $index => $language) {
            $translationData = $request->input("translations.{$index}", []);
            
            // Only validate if question or answer is provided
            if (!empty($translationData['question']) || !empty($translationData['answer'])) {
                $request->validate([
                    "translations.{$index}.question" => 'required|string|max:255',
                    "translations.{$index}.answer" => 'required|string',
                ], [
                    "translations.{$index}.question.required" => "Question is required for {$language->lang_code}.",
                    "translations.{$index}.question.max" => "Question may not be greater than 255 characters for {$language->lang_code}.",
                    "translations.{$index}.answer.required" => "Answer is required for {$language->lang_code}.",
                ]);
            }
        }

        try {
            $faq->update([
                'status' => $request->input('status', true),
            ]);

            // Update or create translations - only for languages that have data
            foreach ($languages as $index => $language) {
                $translationData = $request->input("translations.{$index}", []);
                
                // Only update if question or answer is provided
                if (!empty($translationData['question']) || !empty($translationData['answer'])) {
                    $faq->translates()->updateOrCreate(
                        ['language_id' => $language->id],
                        [
                            'question' => trim($translationData['question'] ?? ''),
                            'answer' => trim($translationData['answer'] ?? ''),
                        ]
                    );
                }
            }

            return redirect()->route('admin.faqs.index')
                ->with('success', 'FAQ updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating FAQ: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faq $faq)
    {
        try {
            $faq->delete();
            return redirect()->route('admin.faqs.index')
                ->with('success', 'FAQ deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting FAQ: ' . $e->getMessage());
        }
    }

    /**
     * Reorder FAQs
     */
    public function reorder(Request $request)
    {
        try {
            $faqs = $request->input('faqs', []);
            
            foreach ($faqs as $faqData) {
                Faq::where('id', $faqData['id'])->update(['order' => $faqData['order']]);
            }
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}

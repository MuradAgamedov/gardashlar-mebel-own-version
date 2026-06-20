<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Faq\StoreFaqRequest;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\FaqServiceInterface;
use App\Interfaces\Services\Admin\LanguageServiceInterface;

class FaqController extends Controller
{
    public function __construct(
        protected FaqServiceInterface $faqService,
        protected LanguageServiceInterface $languageService
    )
    {
    }

    public function index(Request $request)
    {
        $faqs = $this->faqService->getFaqs($request);
        return view('admin.faqs.index', compact('faqs'));
    }

    public function create()
    {
        $languages = $this->languageService->getAllLanguages();
        return view('admin.faqs.create', compact('languages'));
    }

    public function store(StoreFaqRequest $request)
    {
        $languages = $this->languageService->getAllLanguages();

        foreach ($languages as $index => $language) {
            $translationData = $request->input("translations.{$index}", []);
            
            if (!empty($translationData['question']) || !empty($translationData['answer'])) {
                $request->validated();
            }
        }

        try {
            $translations = [];
            foreach ($languages as $index => $language) {
                $translationData = $request->input("translations.{$index}", []);
                if (!empty($translationData['question']) || !empty($translationData['answer'])) {
                    $translations[$index] = array_merge($translationData, ['language_id' => $language->id]);
                }
            }

            $this->faqService->createFaq(
                ['status' => $request->input('status', true)],
                $translations
            );

            return redirect()->route('admin.faqs.index')
                ->with('success', 'FAQ created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating FAQ: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Faq $faq)
    {
        //
    }

    public function edit(Faq $faq)
    {
        $faq->load(['translates.language']);
        $languages = $this->languageService->getAllLanguages();
        return view('admin.faqs.edit', compact('faq', 'languages'));
    }

    public function update(Request $request, Faq $faq)
    {
        $languages = $this->languageService->getAllLanguages();

        foreach ($languages as $index => $language) {
            $translationData = $request->input("translations.{$index}", []);
            
            if (!empty($translationData['question']) || !empty($translationData['answer'])) {
                $request->validated();
            }
        }

        try {
            $translations = [];
            foreach ($languages as $index => $language) {
                $translationData = $request->input("translations.{$index}", []);
                if (!empty($translationData['question']) || !empty($translationData['answer'])) {
                    $translations[$index] = array_merge($translationData, ['language_id' => $language->id]);
                }
            }

            $this->faqService->updateFaq(
                $faq,
                ['status' => $request->input('status', true)],
                $translations
            );

            return redirect()->route('admin.faqs.index')
                ->with('success', 'FAQ updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating FAQ: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Faq $faq)
    {
        try {
            $this->faqService->deleteFaq($faq);
            return redirect()->route('admin.faqs.index')
                ->with('success', 'FAQ deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting FAQ: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        try {
            $this->faqService->reorderFaqs($request->input('faqs', []));
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function deleteSelected(Request $request)
    {
        try {
            $ids = json_decode($request->input('ids'), true);
            
            if (empty($ids)) {
                return redirect()->back()->with('error', 'No FAQs selected for deletion.');
            }
            
            $count = $this->faqService->deleteSelected($ids);
            
            return redirect()->route('admin.faqs.index')
                ->with('success', $count . ' FAQ(s) deleted successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting FAQs: ' . $e->getMessage());
        }
    }
}

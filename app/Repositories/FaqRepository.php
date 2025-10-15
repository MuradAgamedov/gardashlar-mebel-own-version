<?php
namespace App\Repositories;

use App\Models\Faq;
use App\Repositories\Global\BaseRepository;

class FaqRepository extends BaseRepository
{

    public function __construct(public Faq $faq)
    {
    }

    public function getFaqs($request)
    {
        $query = $this->faq->with(['translates.language']);
        
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->whereHas('translates', function($q) use ($searchTerm) {
                $q->where('question', 'like', '%' . $searchTerm . '%')
                  ->orWhere('answer', 'like', '%' . $searchTerm . '%');
            });
        }
        
        return $query->ordered()->get();
    }

    public function createFaq(array $data, array $translations)
    {
        $maxOrder = $this->faq->max('order') ?? 0;
        
        $faq = $this->faq->create([
            'order' => $maxOrder + 1,
            'status' => $data['status'] ?? true,
        ]);

        foreach ($translations as $index => $translationData) {
            if (!empty($translationData['question']) || !empty($translationData['answer'])) {
                $faq->translates()->create([
                    'language_id' => $translationData['language_id'],
                    'question' => trim($translationData['question'] ?? ''),
                    'answer' => trim($translationData['answer'] ?? ''),
                ]);
            }
        }

        return $faq;
    }

    public function updateFaq(Faq $faq, array $data, array $translations)
    {
        $faq->update(['status' => $data['status'] ?? true]);

        foreach ($translations as $index => $translationData) {
            if (!empty($translationData['question']) || !empty($translationData['answer'])) {
                $faq->translates()->updateOrCreate(
                    ['language_id' => $translationData['language_id']],
                    [
                        'question' => trim($translationData['question'] ?? ''),
                        'answer' => trim($translationData['answer'] ?? ''),
                    ]
                );
            }
        }

        return $faq;
    }

    public function deleteFaq(Faq $faq)
    {
        return $faq->delete();
    }

    public function reorderFaqs(array $faqs)
    {
        foreach ($faqs as $faqData) {
            $this->faq->where('id', $faqData['id'])->update(['order' => $faqData['order']]);
        }
        return true;
    }

    public function deleteSelected(array $ids)
    {
        return $this->faq->whereIn('id', $ids)->delete();
    }
}

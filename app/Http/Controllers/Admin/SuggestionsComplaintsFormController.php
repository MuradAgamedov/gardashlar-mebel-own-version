<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuggestionsComplaintsForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Interfaces\Services\Admin\SuggestionsComplaintsFormServiceInterface;

class SuggestionsComplaintsFormController extends Controller
{
    public function __construct(
        protected SuggestionsComplaintsFormServiceInterface $suggestionsComplaintsFormService
    ) {
    }


    public function index()
    {
        $forms = SuggestionsComplaintsForm::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.suggestions-complaints-forms.index', compact('forms'));
    }

    public function show($id)
    {
        $form = $this->suggestionsComplaintsFormService->findOrFail($id);
        
        // Mark as read if not already read
        if (!$form->is_read) {
            $form->update(['is_read' => true]);
        }
        
        return view('admin.suggestions-complaints-forms.show', compact('form'));
    }

    public function destroy($id)
    {
        $form = $this->suggestionsComplaintsFormService->findOrFail($id);
        $form->delete();
        
        return redirect()->route('admin.suggestions-complaints-forms.index')
            ->with('success', __('Form deleted successfully'));
    }

    public function deleteSelected(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => __('No forms selected')]);
        }
        
        SuggestionsComplaintsForm::whereIn('id', $ids)->delete();
        
        return response()->json(['success' => true, 'message' => __('Selected forms deleted successfully')]);
    }

    public function markAsRead($id)
    {
        $form = $this->suggestionsComplaintsFormService->findOrFail($id);
        $form->update(['is_read' => true]);
        
        return response()->json(['success' => true, 'message' => __('Form marked as read')]);
    }
} 
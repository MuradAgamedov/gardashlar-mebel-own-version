<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\SuggestAPlaceForm\DeleteSelectedSuggestAPlaceFormRequest;

use App\Http\Controllers\Controller;
use App\Models\SuggestAPlaceForm;
use Illuminate\Http\Request;

use App\Interfaces\Services\Admin\SuggestAPlaceFormServiceInterface;

class SuggestAPlaceFormController extends Controller
{
    public function __construct(
        protected SuggestAPlaceFormServiceInterface $suggestAPlaceFormService
    ) {
    }


    public function index(Request $request)
    {
        $query = SuggestAPlaceForm::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name_surname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // Filter by read status
        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->unread();
            } elseif ($request->status === 'read') {
                $query->read();
            }
        }

        $forms = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.suggest-a-place-forms.index', compact('forms'));
    }

    public function show(SuggestAPlaceForm $suggestAPlaceForm)
    {
        // Mark as read if not already read
        if (!$suggestAPlaceForm->is_read) {
            $suggestAPlaceForm->markAsRead();
        }

        return view('admin.suggest-a-place-forms.show', compact('suggestAPlaceForm'));
    }

    public function destroy(SuggestAPlaceForm $suggestAPlaceForm)
    {
        try {
            $suggestAPlaceForm->delete();
            return redirect()->route('admin.suggest-a-place-forms.index')
                ->with('success', __('Form deleted successfully!'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Error deleting form: :error', ['error' => $e->getMessage()]));
        }
    }

    public function markAsRead($id)
    {
        try {
            $form = $this->suggestAPlaceFormService->findOrFail($id);
            $form->markAsRead();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteSelected(DeleteSelectedSuggestAPlaceFormRequest $request)
    {
        $request->validated();

        try {
            SuggestAPlaceForm::whereIn('id', $request->ids)->delete();
            return redirect()->route('admin.suggest-a-place-forms.index')
                ->with('success', __(':count form(s) deleted successfully.', ['count' => count($request->ids)]));
        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));
        }
    }
}

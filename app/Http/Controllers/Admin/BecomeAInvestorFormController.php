<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BecomeAInvestorForm;
use Illuminate\Http\Request;
use App\Repositories\BecomeAInvestorFormRepository;

class BecomeAInvestorFormController extends Controller
{
    public function __construct(public BecomeAInvestorFormRepository $repository)
    {
    }

    public function index(Request $request)
    {
        $forms = $this->repository->getForms($request);
        return view('admin.become-a-investor-forms.index', compact('forms'));
    }

    public function show(BecomeAInvestorForm $becomeAInvestorForm)
    {
        if (!$becomeAInvestorForm->is_read) {
            $this->repository->markAsRead($becomeAInvestorForm);
        }
        return view('admin.become-a-investor-forms.show', compact('becomeAInvestorForm'));
    }

    public function destroy(BecomeAInvestorForm $becomeAInvestorForm)
    {
        try {
            $this->repository->deleteForm($becomeAInvestorForm);
            return redirect()->route('admin.become-a-investor-forms.index')
                ->with('success', __('Form deleted successfully!'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Error deleting form: :error', ['error' => $e->getMessage()]));
        }
    }

    public function markAsRead($id)
    {
        try {
            $form = BecomeAInvestorForm::findOrFail($id);
            $this->repository->markAsRead($form);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteSelected(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:become_a_investor_forms,id'
        ]);

        try {
            $this->repository->deleteSelected($request->ids);
            return redirect()->route('admin.become-a-investor-forms.index')
                ->with('success', __(':count form(s) deleted successfully.', ['count' => count($request->ids)]));
        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));
        }
    }
}

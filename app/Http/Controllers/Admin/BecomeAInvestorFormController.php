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
        // Mark as read if not already read
        if (!$becomeAInvestorForm->is_read) {
            $becomeAInvestorForm->markAsRead();
        }

        return view('admin.become-a-investor-forms.show', compact('becomeAInvestorForm'));
    }

    public function destroy(BecomeAInvestorForm $becomeAInvestorForm)
    {
        try {
            $becomeAInvestorForm->delete();
            return redirect()->route('admin.become-a-investor-forms.index')
                ->with('success', 'Form deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting form: ' . $e->getMessage());
        }
    }

    public function markAsRead($id)
    {
        try {
            $form = BecomeAInvestorForm::findOrFail($id);
            $form->markAsRead();
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
            BecomeAInvestorForm::whereIn('id', $request->ids)->delete();
            return redirect()->route('admin.become-a-investor-forms.index')
                ->with('success', count($request->ids) . ' form(s) deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }
}

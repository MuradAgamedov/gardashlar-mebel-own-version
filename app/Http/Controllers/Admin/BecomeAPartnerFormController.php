<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BecomeAPartnerForm;
use Illuminate\Http\Request;
use App\Repositories\BecomeAPartnerFormRepository;

class BecomeAPartnerFormController extends Controller
{
    public function __construct(public BecomeAPartnerFormRepository $repository)
    {
    }

    public function index(Request $request)
    {
        $forms = $this->repository->getForms($request);
        return view('admin.become-a-partner-forms.index', compact('forms'));
    }

    public function show(BecomeAPartnerForm $becomeAPartnerForm)
    {
        if (!$becomeAPartnerForm->is_read) {
            $this->repository->markAsRead($becomeAPartnerForm);
        }
        return view('admin.become-a-partner-forms.show', compact('becomeAPartnerForm'));
    }

    public function destroy(BecomeAPartnerForm $becomeAPartnerForm)
    {
        try {
            $this->repository->deleteForm($becomeAPartnerForm);
            return redirect()->route('admin.become-a-partner-forms.index')
                ->with('success', 'Form deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting form: ' . $e->getMessage());
        }
    }

    public function markAsRead($id)
    {
        try {
            $form = BecomeAPartnerForm::findOrFail($id);
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
            'ids.*' => 'integer|exists:become_a_partner_forms,id'
        ]);

        try {
            $this->repository->deleteSelected($request->ids);
            return redirect()->route('admin.become-a-partner-forms.index')
                ->with('success', count($request->ids) . ' form(s) deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }
}

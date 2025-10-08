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

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $forms = $this->repository->getForms($request);
        
        return view('admin.become-a-partner-forms.index', compact('forms'));
    }

    /**
     * Display the specified resource.
     */
    public function show(BecomeAPartnerForm $becomeAPartnerForm)
    {
        // Mark as read when viewed
        if (!$becomeAPartnerForm->is_read) {
            $becomeAPartnerForm->markAsRead();
        }
        
        return view('admin.become-a-partner-forms.show', compact('becomeAPartnerForm'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BecomeAPartnerForm $becomeAPartnerForm)
    {
        try {
            $becomeAPartnerForm->delete();
            return redirect()->route('admin.become-a-partner-forms.index')
                ->with('success', 'Form deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting form: ' . $e->getMessage());
        }
    }

    /**
     * Mark form as read
     */
    public function markAsRead($id)
    {
        try {
            $form = BecomeAPartnerForm::findOrFail($id);
            $form->markAsRead();
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete selected forms
     */
    public function deleteSelected(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:become_a_partner_forms,id'
        ]);

        try {
            BecomeAPartnerForm::whereIn('id', $request->ids)->delete();

            return redirect()->route('admin.become-a-partner-forms.index')
                ->with('success', count($request->ids) . ' form(s) deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }
}

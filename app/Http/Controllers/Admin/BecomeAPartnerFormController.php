<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\BecomeAPartnerForm\DeleteSelectedBecomeAPartnerFormRequest;

use App\Http\Controllers\Controller;
use App\Models\BecomeAPartnerForm;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\BecomeAPartnerFormServiceInterface;

class BecomeAPartnerFormController extends Controller
{
    public function __construct(protected BecomeAPartnerFormServiceInterface $becomeAPartnerFormService)
    {
    }

    public function index(Request $request)
    {
        $forms = $this->becomeAPartnerFormService->getForms($request);
        return view('admin.become-a-partner-forms.index', compact('forms'));
    }

    public function show(BecomeAPartnerForm $becomeAPartnerForm)
    {
        if (!$becomeAPartnerForm->is_read) {
            $this->becomeAPartnerFormService->markAsRead($becomeAPartnerForm);
        }
        return view('admin.become-a-partner-forms.show', compact('becomeAPartnerForm'));
    }

    public function destroy(BecomeAPartnerForm $becomeAPartnerForm)
    {
        try {
            $this->becomeAPartnerFormService->deleteForm($becomeAPartnerForm);
            return redirect()->route('admin.become-a-partner-forms.index')
                ->with('success', __('Form deleted successfully!'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error deleting form: :error', ['error' => $e->getMessage()]));
        }
    }

    public function markAsRead($id)
    {
        try {
            $form = $this->becomeAPartnerFormService->findOrFail($id);
            $this->becomeAPartnerFormService->markAsRead($form);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteSelected(DeleteSelectedBecomeAPartnerFormRequest $request)
    {
        $request->validated();

        try {
            $this->becomeAPartnerFormService->deleteSelected($request->ids);
            return redirect()->route('admin.become-a-partner-forms.index')
                ->with('success', __(':count form(s) deleted successfully.', ['count' => count($request->ids)]));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));
        }
    }
}

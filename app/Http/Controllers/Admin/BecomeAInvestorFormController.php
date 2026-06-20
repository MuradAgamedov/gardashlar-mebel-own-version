<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\BecomeAInvestorForm\DeleteSelectedBecomeAInvestorFormRequest;

use App\Http\Controllers\Controller;
use App\Models\BecomeAInvestorForm;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\BecomeAInvestorFormServiceInterface;

class BecomeAInvestorFormController extends Controller
{
    public function __construct(protected BecomeAInvestorFormServiceInterface $becomeAInvestorFormService)
    {
    }

    public function index(Request $request)
    {
        $forms = $this->becomeAInvestorFormService->getForms($request);
        return view('admin.become-a-investor-forms.index', compact('forms'));
    }

    public function show(BecomeAInvestorForm $becomeAInvestorForm)
    {
        if (!$becomeAInvestorForm->is_read) {
            $this->becomeAInvestorFormService->markAsRead($becomeAInvestorForm);
        }
        return view('admin.become-a-investor-forms.show', compact('becomeAInvestorForm'));
    }

    public function destroy(BecomeAInvestorForm $becomeAInvestorForm)
    {
        try {
            $this->becomeAInvestorFormService->deleteForm($becomeAInvestorForm);
            return redirect()->route('admin.become-a-investor-forms.index')
                ->with('success', __('Form deleted successfully!'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Error deleting form: :error', ['error' => $e->getMessage()]));
        }
    }

    public function markAsRead($id)
    {
        try {
            $form = $this->becomeAInvestorFormService->findOrFail($id);
            $this->becomeAInvestorFormService->markAsRead($form);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteSelected(DeleteSelectedBecomeAInvestorFormRequest $request)
    {
        $request->validated();

        try {
            $this->becomeAInvestorFormService->deleteSelected($request->ids);
            return redirect()->route('admin.become-a-investor-forms.index')
                ->with('success', __(':count form(s) deleted successfully.', ['count' => count($request->ids)]));
        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));
        }
    }
}

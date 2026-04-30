<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceForm;
use Illuminate\Http\Request;

class ServiceFormController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceForm::with('service.translates');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
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

        // Filter by service
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        $forms = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.service-forms.index', compact('forms'));
    }

    public function show(ServiceForm $serviceForm)
    {
        // Mark as read if not already read
        if (!$serviceForm->is_read) {
            $serviceForm->markAsRead();
        }

        $serviceForm->load('service.translates');

        return view('admin.service-forms.show', compact('serviceForm'));
    }

    public function destroy(ServiceForm $serviceForm)
    {
        try {
            $serviceForm->delete();
            return redirect()->route('admin.service-forms.index')
                ->with('success', __('Form deleted successfully!'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Error deleting form: :error', ['error' => $e->getMessage()]));
        }
    }

    public function markAsRead($id)
    {
        try {
            $form = ServiceForm::findOrFail($id);
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
            'ids.*' => 'integer|exists:service_forms,id'
        ]);

        try {
            ServiceForm::whereIn('id', $request->ids)->delete();
            return redirect()->route('admin.service-forms.index')
                ->with('success', __(':count form(s) deleted successfully.', ['count' => count($request->ids)]));
        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));
        }
    }
}

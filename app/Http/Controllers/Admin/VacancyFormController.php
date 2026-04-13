<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VacancyForm;
use App\Models\Vacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VacancyFormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = VacancyForm::with('vacancy');

        // Filter by vacancy_id if provided
        if ($request->has('vacancy_id') && $request->vacancy_id) {
            $query->where('vacancy_id', $request->vacancy_id);
        }

        // Filter by read status if provided
        if ($request->has('is_read') && $request->is_read !== '') {
            $query->where('is_read', $request->boolean('is_read'));
        }

        // Search by name, email, or mobile
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        // Order by created_at desc
        $query->orderBy('created_at', 'desc');

        $forms = $query->paginate(15);
        $vacancies = Vacancy::active()->get();

        return view('admin.vacancy-forms.index', compact('forms', 'vacancies'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $form = VacancyForm::with('vacancy')->findOrFail($id);
        
        // Mark as read when viewing
        if (!$form->is_read) {
            $form->markAsRead();
        }

        return view('admin.vacancy-forms.show', compact('form'));
    }

    /**
     * Mark form as read
     */
    public function markAsRead(string $id)
    {
        $form = VacancyForm::findOrFail($id);
        $form->markAsRead();

        return redirect()->back()->with('success', 'Form marked as read');
    }

    /**
     * Mark form as unread
     */
    public function markAsUnread(string $id)
    {
        $form = VacancyForm::findOrFail($id);
        $form->update(['is_read' => false, 'read_at' => null]);

        return redirect()->back()->with('success', 'Form marked as unread');
    }

    /**
     * Download file
     */
    public function downloadFile(string $id)
    {
        $form = VacancyForm::findOrFail($id);
        
        if (!$form->file) {
            return redirect()->back()->with('error', 'No file attached');
        }

        // Check if file exists in storage
        if (!Storage::exists($form->file)) {
            return redirect()->back()->with('error', 'File not found in storage');
        }

        // Get original filename
        $originalName = $form->file_name ?: basename($form->file);
        
        // Return file download response using Laravel's Storage::download
        return Storage::download($form->file, $originalName);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $form = VacancyForm::findOrFail($id);
        
        // Delete file if exists
        if ($form->file && Storage::exists($form->file)) {
            Storage::delete($form->file);
        }
        
        $form->delete();

        return redirect()->route('admin.vacancy-forms.index')
            ->with('success', 'Form deleted successfully');
    }
}

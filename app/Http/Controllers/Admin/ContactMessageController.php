<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use App\Repositories\ContactMessageRepository;

class ContactMessageController extends Controller
{
    public function __construct(public ContactMessageRepository $repository)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $messages = $this->repository->getMessages($request);

        return view('admin.contact-messages.index', compact('messages'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $message = ContactMessage::findOrFail($id);
        
        // Mark as read if not already read
        if (!$message->is_read) {
            $message->markAsRead();
        }

        return view('admin.contact-messages.show', compact('message'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();

        return redirect()->route('admin.contact-messages.index')
            ->with('success', 'Message deleted successfully');
    }

    /**
     * Mark message as read
     */
    public function markAsRead(string $id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->markAsRead();

        return redirect()->back()->with('success', 'Message marked as read');
    }

    /**
     * Delete multiple messages
     */
    public function deleteSelected(Request $request)
    {
        $ids = $request->input('ids');
        
        // If ids is already an array, use it directly
        if (is_array($ids)) {
            $idsArray = $ids;
        } else {
            // If it's a JSON string, decode it
            $idsArray = json_decode($ids, true);
        }
        
        if ($idsArray && is_array($idsArray) && !empty($idsArray)) {
            ContactMessage::whereIn('id', $idsArray)->delete();
            return response()->json(['success' => true, 'message' => 'Selected messages deleted successfully']);
        }

        return response()->json(['success' => false, 'message' => 'No messages selected'], 400);
    }
}

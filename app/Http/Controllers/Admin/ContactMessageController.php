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

    public function index(Request $request)
    {
        $messages = $this->repository->getMessages($request);
        return view('admin.contact-messages.index', compact('messages'));
    }

    public function show(string $id)
    {
        $message = $this->repository->findMessage($id);
        $this->repository->markAsRead($message);
        return view('admin.contact-messages.show', compact('message'));
    }

    public function destroy(string $id)
    {
        $message = $this->repository->findMessage($id);
        $this->repository->deleteMessage($message);
        return redirect()->route('admin.contact-messages.index')
            ->with('success', __('Message deleted successfully!'));
    }

    public function markAsRead(string $id)
    {
        $message = $this->repository->findMessage($id);
        $this->repository->markAsRead($message);
        return redirect()->back()->with('success', __('Message marked as read!'));
    }

    public function deleteSelected(Request $request)
    {
        $ids = $request->input('ids');
        
        if (is_array($ids)) {
            $idsArray = $ids;
        } else {
            $idsArray = json_decode($ids, true);
        }
        
        if ($idsArray && is_array($idsArray) && !empty($idsArray)) {
            $this->repository->deleteSelected($idsArray);
            return response()->json(['success' => true, 'message' => __('Selected messages deleted successfully')]);
        }

        return response()->json(['success' => false, 'message' => __('No messages selected')], 400);
    }
}

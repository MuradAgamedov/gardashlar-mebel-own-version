<?php
namespace App\Repositories;

use App\Models\ContactMessage;
use App\Repositories\Global\BaseRepository;

class ContactMessageRepository extends BaseRepository
{

    public function __construct(public ContactMessage $contactMessage)
    {
    }

    // Mesajları filtrlə və paginate et
    public function getMessages($request)
    {
        $query = $this->contactMessage->query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        // Filter by read status
        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'unread') {
                $query->unread();
            } elseif ($request->status === 'read') {
                $query->read();
            }
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }
}

<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ContactMessageRepositoryInterface;
use App\Models\ContactMessage;
use App\Repositories\BaseRepository;

class ContactMessageRepository extends BaseRepository implements ContactMessageRepositoryInterface
{
    public function __construct(ContactMessage $model)
    {
        parent::__construct($model);
    }

    public function getMessages($request)
    {
        $query = $this->contactMessage->query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'unread') {
                $query->unread();
            } elseif ($request->status === 'read') {
                $query->read();
            }
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function findMessage(string $id)
    {
        return $this->contactMessage->findOrFail($id);
    }

    public function markAsRead(ContactMessage $message)
    {
        if (!$message->is_read) {
            $message->markAsRead();
        }
        return $message;
    }

    public function deleteMessage(ContactMessage $message)
    {
        return $message->delete();
    }

    public function deleteSelected(array $ids)
    {
        return $this->contactMessage->whereIn('id', $ids)->delete();
    }
}

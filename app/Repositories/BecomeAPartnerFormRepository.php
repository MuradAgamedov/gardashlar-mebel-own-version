<?php
namespace App\Repositories;

use App\Models\BecomeAPartnerForm;
use App\Repositories\Global\BaseRepository;

class BecomeAPartnerFormRepository extends BaseRepository
{

    public function __construct(public BecomeAPartnerForm $form)
    {
    }

    public function getForms($request)
    {
        $query = $this->form->query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->unread();
            } elseif ($request->status === 'read') {
                $query->read();
            }
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function deleteForm(BecomeAPartnerForm $form)
    {
        return $form->delete();
    }

    public function markAsRead(BecomeAPartnerForm $form)
    {
        return $form->markAsRead();
    }

    public function deleteSelected(array $ids)
    {
        return $this->form->whereIn('id', $ids)->delete();
    }
}

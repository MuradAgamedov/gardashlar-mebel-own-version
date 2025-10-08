<?php
namespace App\Repositories;

use App\Models\BecomeAPartnerForm;
use App\Repositories\Global\BaseRepository;

class BecomeAPartnerFormRepository extends BaseRepository
{

    public function __construct(public BecomeAPartnerForm $form)
    {
    }

    // Formları filtrlə və paginate et
    public function getForms($request)
    {
        $query = $this->form->query();

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

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }
}

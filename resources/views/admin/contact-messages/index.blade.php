@extends('admin.layout.app')

@section('title', 'Contact Messages')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Contact Messages</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Contact Messages</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">Success:</h6>
                <p class="mb-0">{{ session('success') }}</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5>Contact Messages</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-danger" id="deleteSelectedBtn" style="display: none;">
                                        <i class="material-icons-outlined">delete</i> 
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search and Filter -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('admin.contact-messages.index') }}" class="d-flex gap-2">
                                    <input type="text" 
                                           class="form-control" 
                                           name="search" 
                                           value="{{ request('search') }}" 
                                           placeholder="Search by name, email, or city...">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="material-icons-outlined">search</i>
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('admin.contact-messages.index') }}" class="d-flex gap-2">
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                    <select name="status" class="form-select" onchange="this.form.submit()">
                                        <option value="">All Messages</option>
                                        <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Unread</option>
                                        <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Read</option>
                                    </select>
                                    @if(request('search') || request('status'))
                                        <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-secondary">
                                            <i class="material-icons-outlined">clear</i>
                                        </a>
                                    @endif
                                </form>
                            </div>
                        </div>

                        <!-- Messages Table -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>City</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($messages as $message)
                                        <tr class="{{ !$message->is_read ? 'table-warning' : '' }}">
                                            <td>
                                                <input type="checkbox" class="form-check-input message-checkbox" value="{{ $message->id }}">
                                            </td>
                                            <td>
                                                <strong>{{ $message->name }}</strong>
                                                @if(!$message->is_read)
                                                    <span class="badge bg-danger ms-2">New</span>
                                                @endif
                                            </td>
                                            <td>{{ $message->email }}</td>
                                            <td>{{ $message->city }}</td>
                                            <td>
                                                @if($message->is_read)
                                                    <span class="badge bg-success">Read</span>
                                                @else
                                                    <span class="badge bg-warning">Unread</span>
                                                @endif
                                            </td>
                                            <td>{{ $message->created_at->format('d.m.Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.contact-messages.show', $message->id) }}" 
                                                       class="btn btn-sm btn-primary" 
                                                       title="View Message">
                                                        <i class="material-icons-outlined">visibility</i>
                                                    </a>
                                                    @if(!$message->is_read)
                                                        <form method="POST" action="{{ route('admin.contact-messages.mark-as-read', $message->id) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success" title="Mark as Read">
                                                                <i class="material-icons-outlined">mark_email_read</i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <form method="POST" action="{{ route('admin.contact-messages.destroy', $message->id) }}" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                                onclick="return confirm('Are you sure you want to delete this message?')"
                                                                title="Delete">
                                                            <i class="material-icons-outlined">delete</i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="material-icons-outlined" style="font-size: 48px;">inbox</i>
                                                    <p class="mt-2">No messages found</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($messages->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $messages->appends(request()->query())->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const messageCheckboxes = document.querySelectorAll('.message-checkbox');
    const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Select all functionality
    selectAll.addEventListener('change', function() {
        messageCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateDeleteButton();
    });

    // Individual checkbox functionality
    messageCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAll();
            updateDeleteButton();
        });
    });

    function updateSelectAll() {
        const checkedBoxes = document.querySelectorAll('.message-checkbox:checked');
        const totalBoxes = messageCheckboxes.length;
        selectAll.checked = checkedBoxes.length === totalBoxes;
        selectAll.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < totalBoxes;
    }

    function updateDeleteButton() {
        const checkedBoxes = document.querySelectorAll('.message-checkbox:checked');
        if (checkedBoxes.length > 0) {
            deleteSelectedBtn.style.display = 'inline-block';
        } else {
            deleteSelectedBtn.style.display = 'none';
        }
    }

    // Delete selected messages
    deleteSelectedBtn.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.message-checkbox:checked');
        const ids = Array.from(checkedBoxes).map(cb => cb.value);

        if (ids.length === 0) {
            alert('Please select messages to delete');
            return;
        }

        if (!confirm(`Are you sure you want to delete ${ids.length} selected message(s)?`)) {
            return;
        }

        fetch('{{ route("admin.contact-messages.delete-selected") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ ids: ids })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error deleting messages');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting messages');
        });
    });
});
</script>
@endsection 
@extends('admin.layout.app')

@section('title', __('Subscribers'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Subscribers') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Subscribers') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>{{ __('Subscribers List') }}</h5>
                            <span class="badge bg-info">{{ __('Total: :count subscribers', ['count' => $subscribers->total()]) }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="material-icons-outlined">check_circle</i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="material-icons-outlined">error</i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Search and Actions -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('admin.subscribers.index') }}" class="d-flex">
                                    <div class="input-group">
                                        <input type="text" 
                                               name="search" 
                                               class="form-control" 
                                               placeholder="{{ __('Search by email...') }}" 
                                               value="{{ request('search') }}">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">search</i>
                                        </button>
                                        @if(request('search'))
                                            <a href="{{ route('admin.subscribers.index') }}" class="btn btn-secondary">
                                                <i class="material-icons-outlined">close</i>
                                            </a>
                                        @endif
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6 text-end">
                                @if($subscribers->count() > 0)
                                    <button type="button" class="btn btn-danger" onclick="confirmBulkDelete()" title="{{ __('Delete Selected') }}">
                                        <i class="material-icons-outlined">delete_sweep</i>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Subscribers Table -->
                        <div class="table-responsive">
                            @if($subscribers->count() > 0)
                            <form id="bulkForm" method="POST" action="{{ route('admin.subscribers.bulk-delete') }}">
                                @csrf
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="50">
                                                <input type="checkbox" id="selectAll" onchange="toggleAll()" class="form-check-input">
                                            </th>
                                            <th>{{ __('Email') }}</th>
                                            <th>{{ __('Subscribed At') }}</th>
                                            <th width="150" class="text-end">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subscribers as $subscriber)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="subscriber_ids[]" value="{{ $subscriber->id }}" class="subscriber-checkbox form-check-input">
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="material-icons-outlined text-primary me-2">email</i>
                                                    {{ $subscriber->email }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="material-icons-outlined text-muted me-2">calendar_today</i>
                                                    {{ $subscriber->created_at->format('M d, Y H:i') }}
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <button type="button" 
                                                        class="btn btn-outline-danger btn-sm" 
                                                        onclick="confirmDelete({{ $subscriber->id }}, '{{ $subscriber->email }}')">
                                                    <i class="material-icons-outlined">delete</i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </form>
                            @else
                                <div class="text-center py-5">
                                    <i class="material-icons-outlined text-muted" style="font-size: 4rem;">inbox</i>
                                    <h5 class="mt-3 text-muted">{{ __('No subscribers found') }}</h5>
                                    <p class="text-muted">{{ __('No email subscribers have been registered yet.') }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Pagination -->
                        @if($subscribers->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $subscribers->appends(request()->query())->links() }}
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">{{ __('Confirm Delete') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('Are you sure you want to delete this subscriber?') }}</p>
                <p><strong>{{ __('Email:') }}</strong> <span id="subscriberEmail"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Delete Confirmation Modal -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-labelledby="bulkDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkDeleteModalLabel">{{ __('Confirm Bulk Delete') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('Are you sure you want to delete the selected subscribers?') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-danger" onclick="submitBulkDelete()">{{ __('Delete Selected') }}</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function confirmDelete(id, email) {
    document.getElementById('subscriberEmail').textContent = email;
    document.getElementById('deleteForm').action = '{{ route("admin.subscribers.destroy", ":id") }}'.replace(':id', id);
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function confirmBulkDelete() {
    const selectedCheckboxes = document.querySelectorAll('.subscriber-checkbox:checked');
    if (selectedCheckboxes.length === 0) {
        alert('{{ __("Please select at least one subscriber to delete.") }}');
        return;
    }
    new bootstrap.Modal(document.getElementById('bulkDeleteModal')).show();
}

function submitBulkDelete() {
    document.getElementById('bulkForm').submit();
}

function toggleAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.subscriber-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

// Update select all checkbox when individual checkboxes change
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.subscriber-checkbox');
    const selectAll = document.getElementById('selectAll');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            const someChecked = Array.from(checkboxes).some(cb => cb.checked);
            
            selectAll.checked = allChecked;
            selectAll.indeterminate = someChecked && !allChecked;
        });
    });
});
</script>
@endsection

@extends('admin.layout.app')

@section('title', __('Subscribers'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Subscribers') }}</h4>
                    <p class="card-title-desc">{{ __('Manage newsletter subscribers') }}</p>
                </div>
                <div class="card-body">
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Search and Actions -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('admin.subscribers.index') }}" class="d-flex">
                                <input type="text" 
                                       name="search" 
                                       class="form-control me-2" 
                                       placeholder="{{ __('Search by email...') }}" 
                                       value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary">{{ __('Search') }}</button>
                                @if(request('search'))
                                    <a href="{{ route('admin.subscribers.index') }}" class="btn btn-secondary ms-2">{{ __('Clear') }}</a>
                                @endif
                            </form>
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="badge bg-info">{{ __('Total: :count subscribers', ['count' => $subscribers->total()]) }}</span>
                        </div>
                    </div>

                    <!-- Bulk Actions -->
                    @if($subscribers->count() > 0)
                    <form id="bulkForm" method="POST" action="{{ route('admin.subscribers.bulk-delete') }}">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-danger" onclick="confirmBulkDelete()">
                                    <i class="fas fa-trash"></i> 
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Subscribers Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>
                                        @if($subscribers->count() > 0)
                                        <input type="checkbox" id="selectAll" onchange="toggleAll()">
                                        @endif
                                    </th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Subscribed At') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subscribers as $subscriber)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="subscriber_ids[]" value="{{ $subscriber->id }}" class="subscriber-checkbox">
                                    </td>
                                    <td>
                                        <i class="fas fa-envelope text-primary me-2"></i>
                                        {{ $subscriber->email }}
                                    </td>
                                    <td>
                                        <i class="fas fa-calendar text-muted me-2"></i>
                                        {{ $subscriber->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-danger btn-sm" 
                                                onclick="confirmDelete({{ $subscriber->id }}, '{{ $subscriber->email }}')">
                                            <i class="fas fa-trash"></i> 
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">{{ __('No subscribers found') }}</h5>
                                        <p class="text-muted">{{ __('No email subscribers have been registered yet.') }}</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($subscribers->count() > 0)
                    </form>
                    @endif

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
                <p class="text-danger">{{ __('This action cannot be undone.') }}</p>
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
                <p class="text-danger">{{ __('This action cannot be undone.') }}</p>
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

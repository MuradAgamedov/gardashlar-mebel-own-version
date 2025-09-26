@extends('admin.layout.app')

@section('title', 'Actions Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Actions Management</h4>
                        <a href="{{ route('admin.actions.create') }}" class="btn btn-primary">
                            <i class="material-icons-outlined">add</i>
                            Add New Action
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search and Filter -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('admin.actions.index') }}" class="d-flex">
                                <input type="text" name="search" class="form-control me-2" 
                                       placeholder="Search actions..." 
                                       value="{{ request('search') }}">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="material-icons-outlined">search</i>
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('admin.actions.index') }}">
                                <select name="company_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">All Companies</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" 
                                                {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                            {{ $company->translate()->first()->title ?? 'No Title' }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>

                    <!-- Actions Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Icon</th>
                                    <th>Title</th>
                                    <th>Company</th>
                                    <th>Status</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Order</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($actions as $action)
                                    <tr>
                                        <td>{{ $action->id }}</td>
                                        <td>
                                            @if($action->icon)
                                                <i class="material-icons-outlined">{{ $action->icon }}</i>
                                            @else
                                                <span class="text-muted">No Icon</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $mainTranslate = $action->translate()->first();
                                            @endphp
                                            {{ $mainTranslate->title ?? 'No Title' }}
                                        </td>
                                        <td>
                                            {{ $action->company->translate()->first()->title ?? 'No Company' }}
                                        </td>
                                        <td>
                                            <span class="badge {{ $action->status ? 'bg-success' : 'bg-danger' }}">
                                                {{ $action->status ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>{{ $action->start_date ? $action->start_date->format('Y-m-d') : '-' }}</td>
                                        <td>{{ $action->end_date ? $action->end_date->format('Y-m-d') : '-' }}</td>
                                        <td>{{ $action->order }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.actions.show', $action) }}" 
                                                   class="btn btn-sm btn-outline-info" title="View">
                                                    <i class="material-icons-outlined">visibility</i>
                                                </a>
                                                <a href="{{ route('admin.actions.products', $action) }}" 
                                                   class="btn btn-sm btn-outline-warning" title="Manage Products">
                                                    <i class="material-icons-outlined">inventory</i>
                                                </a>
                                                <a href="{{ route('admin.actions.edit', $action) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="material-icons-outlined">edit</i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteAction({{ $action->id }})" title="Delete">
                                                    <i class="material-icons-outlined">delete</i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No actions found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $actions->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this action? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteAction(id) {
    document.getElementById('deleteForm').action = '{{ route("admin.actions.destroy", ":id") }}'.replace(':id', id);
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endsection

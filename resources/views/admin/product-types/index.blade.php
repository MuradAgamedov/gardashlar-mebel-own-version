@extends('admin.layout.app')

@section('title', 'Product Types')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Product Types</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Product Types</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">Success!</h6>
                <p class="mb-0">{{ session('success') }}</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">Error occurred:</h6>
                <p class="mb-0">{{ session('error') }}</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Product Types</h5>
                            {{-- <a href="{{ route('admin.product-types.create') }}" class="btn btn-primary">
                                <i class="material-icons-outlined">add</i>
                            </a> --}}
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search Form -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('admin.product-types.index') }}" class="d-flex">
                                    <input type="text" 
                                           name="search" 
                                           class="form-control me-2" 
                                           placeholder="Search by title..."
                                           value="{{ request('search') }}">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="material-icons-outlined">search</i>
                                    </button>
                                    @if(request('search'))
                                        <a href="{{ route('admin.product-types.index') }}" class="btn btn-outline-secondary ms-2">
                                            <i class="material-icons-outlined">clear</i>
                                        </a>
                                    @endif
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">#</th>
                                        <th>Title</th>
                                        <th>Languages</th>
                                        <th>Status</th>
                                        <th width="150">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable-types">
                                    @forelse($types as $type)
                                        <tr data-id="{{ $type->id }}">
                                            <td>
                                                <div class="drag-handle">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <circle cx="8" cy="8" r="1"></circle>
                                                        <circle cx="16" cy="8" r="1"></circle>
                                                        <circle cx="8" cy="16" r="1"></circle>
                                                        <circle cx="16" cy="16" r="1"></circle>
                                                    </svg>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="material-icons-outlined text-muted me-2" style="font-size: 16px;">category</i>
                                                    <div>
                                                        @php
                                                            $firstTranslation = $type->translates->first();
                                                        @endphp
                                                        <strong>{{ $firstTranslation ? $firstTranslation->title : 'No title' }}</strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($type->translates as $translate)
                                                        <span class="badge bg-primary">{{ $translate->language->lang_code }}</span>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td>
                                                @if($type->status)
                                                    <span class="badge badge-light-success">Active</span>
                                                @else
                                                    <span class="badge badge-light-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.product-types.edit', $type) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="Edit">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    {{-- <a href="{{ route('admin.product-types.show', $type) }}" 
                                                       class="btn btn-sm btn-outline-info" 
                                                       title="View">
                                                        <i class="material-icons-outlined">visibility</i>
                                                    </a>
                                                    <form action="{{ route('admin.product-types.destroy', $type) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this type?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-danger" 
                                                                title="Delete">
                                                            <i class="material-icons-outlined">delete</i>
                                                        </button>
                                                    </form> --}}
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="material-icons-outlined" style="font-size: 48px;">inbox</i>
                                                    <p class="mt-2">No product types found</p>
                                                    {{-- <a href="{{ route('admin.product-types.create') }}" class="btn btn-primary">
                                                        <i class="material-icons-outlined">add</i>
                                                        Add First Type
                                                    </a> --}}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.getElementById('sortable-types');
    
    if (tbody) {
        new Sortable(tbody, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const items = Array.from(tbody.querySelectorAll('tr[data-id]')).map(tr => tr.dataset.id);
                
                
                fetch('{{ route("admin.product-types.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ items: items })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Optionally show success message
                        console.log('Order updated successfully');
                    } else {
                        console.error('Error updating order:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });
    }
});
</script>
@endpush 
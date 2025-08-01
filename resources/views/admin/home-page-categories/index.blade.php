@extends('admin.layout.app')

@section('title', 'Home Page Categories')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Home Page Categories</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Home Page Categories</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Categories to Display on Home Page</h5>
                            <a href="{{ route('admin.home-page-categories.create') }}" class="btn btn-primary">
                                <i class="material-icons-outlined">add</i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">#</th>
                                        <th>Category</th>
                                        <th>Parent</th>
                                        <th>Status</th>
                                        <th>Order</th>
                                        <th width="150">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable-categories">
                                    @forelse($homePageCategories as $homePageCategory)
                                        <tr data-id="{{ $homePageCategory->id }}">
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
                                                    @if($homePageCategory->productCategory->parent_id)
                                                        <span class="me-2" style="color: #6c757d;">└─</span>
                                                    @else
                                                        <i class="material-icons-outlined text-muted me-2" style="font-size: 16px;">category</i>
                                                    @endif
                                                    <div>
                                                        @php
                                                            $firstTranslation = $homePageCategory->productCategory->translates->first();
                                                        @endphp
                                                        <strong>{{ $firstTranslation ? $firstTranslation->title : 'No title' }}</strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($homePageCategory->productCategory->parent)
                                                    @php
                                                        $parentTranslation = $homePageCategory->productCategory->parent->translates->first();
                                                    @endphp
                                                    <span class="badge bg-secondary">
                                                        <i class="material-icons-outlined" style="font-size: 12px;">subdirectory_arrow_right</i>
                                                        {{ $parentTranslation ? $parentTranslation->title : 'Untitled Parent' }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-success">
                                                        <i class="material-icons-outlined" style="font-size: 12px;">folder</i>
                                                        Main Category
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($homePageCategory->status)
                                                    <span class="badge badge-light-success">Active</span>
                                                @else
                                                    <span class="badge badge-light-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-light-info">{{ $homePageCategory->order }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.home-page-categories.edit', $homePageCategory) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="Edit">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    <form action="{{ route('admin.home-page-categories.destroy', $homePageCategory) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this category?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-danger" 
                                                                title="Delete">
                                                            <i class="material-icons-outlined">delete</i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="material-icons-outlined" style="font-size: 48px;">category</i>
                                                    <p class="mt-2">No categories selected for home page</p>
                                                    <a href="{{ route('admin.home-page-categories.create') }}" class="btn btn-primary">
                                                    </a>
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
    const sortableList = document.getElementById('sortable-categories');
    
    if (sortableList) {
        new Sortable(sortableList, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const items = Array.from(sortableList.children).map((tr, index) => {
                    return tr.getAttribute('data-id');
                }).filter(id => id !== null);

                // Send reorder request
                fetch('{{ route("admin.home-page-categories.reorder") }}', {
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
                        // Show success message
                        const alert = document.createElement('div');
                        alert.className = 'alert alert-success alert-dismissible fade show';
                        alert.innerHTML = `
                                                         Order updated successfully
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        `;
                        document.querySelector('.container-fluid').insertBefore(alert, document.querySelector('.row'));
                        
                        // Auto remove after 3 seconds
                        setTimeout(() => {
                            alert.remove();
                        }, 3000);
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
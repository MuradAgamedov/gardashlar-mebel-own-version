@extends('admin.layout.app')

@section('title', 'Products')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Products</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Products</li>
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
                            <h5>Products</h5>
                            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                                <i class="material-icons-outlined">add</i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Search and Filter Section -->
                    <div class="card-body border-bottom">
                        <form method="GET" action="{{ route('admin.products.index') }}" class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="Search by title or description...">
                            </div>
                            <div class="col-md-2">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->translate()->title ?? 'No title' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="type" class="form-label">Type</label>
                                <select class="form-select" id="type" name="type">
                                    @if($types->count() > 0)
                                        @php
                                            $selectedType = request('type');
                                            $firstTypeId = $types->first()->id;
                                        @endphp
                                        @foreach($types as $type)
                                            <option value="{{ $type->id }}" {{ ($selectedType == $type->id || (empty($selectedType) && $type->id == $firstTypeId)) ? 'selected' : '' }}>
                                                {{ $type->translate()->title ?? 'No title' }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="">No Types Available</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Status</option>
                                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="d-flex gap-2 w-100">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="material-icons-outlined">search</i>
                                    </button>
                                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="material-icons-outlined">clear</i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <div class="card-body">
                        <!-- Results Summary -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="text-muted">
                                Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results
                            </div>
                            <div class="text-muted">
                                Page {{ $products->currentPage() }} of {{ $products->lastPage() }}
                            </div>
                            <button id="delete-selected-products" class="btn btn-danger btn-sm ms-3" disabled>
                                <i class="material-icons-outlined">delete</i> Seçilənləri sil
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="30">
                                            <input type="checkbox" id="select-all-products">
                                        </th>
                                        <th width="50">#</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Type</th>
                                        <th>Views</th>
                                        <th>Discount</th>
                                        <th>Status</th>
                                        <th width="150">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable-products">
                                    @forelse($products as $product)
                                        <tr data-id="{{ $product->id }}">
                                            <td>
                                                <input type="checkbox" class="product-checkbox" value="{{ $product->id }}">
                                            </td>
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
                                                    <i class="material-icons-outlined text-muted me-2" style="font-size: 16px;">inventory_2</i>
                                                    <div>
                                                        @php
                                                            $firstTranslation = $product->translates->first();
                                                        @endphp
                                                        <strong>{{ $firstTranslation ? $firstTranslation->title : 'No title' }}</strong>
                                                        @if($product->tags->count() > 0)
                                                            <div class="mt-1">
                                                                @foreach($product->tags->take(3) as $tag)
                                                                    <span class="badge bg-light text-dark me-1" style="font-size: 10px;">
                                                                        {{ $tag->translate()->title ?? 'No title' }}
                                                                    </span>
                                                                @endforeach
                                                                @if($product->tags->count() > 3)
                                                                    <span class="badge bg-secondary" style="font-size: 10px;">
                                                                        +{{ $product->tags->count() - 3 }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($product->categories->count() > 0)
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach($product->categories->take(3) as $category)
                                                            <span class="badge badge-light-primary">
                                                                {{ $category->translate()->title ?? 'No title' }}
                                                            </span>
                                                        @endforeach
                                                        @if($product->categories->count() > 3)
                                                            <span class="badge badge-light-secondary">
                                                                +{{ $product->categories->count() - 3 }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="badge badge-light-secondary">No categories</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($product->types->count() > 0)
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach($product->types->take(3) as $type)
                                                            <span class="badge badge-light-info">
                                                                {{ $type->translate()->title ?? 'No title' }}
                                                            </span>
                                                        @endforeach
                                                        @if($product->types->count() > 3)
                                                            <span class="badge badge-light-secondary">
                                                                +{{ $product->types->count() - 3 }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="badge badge-light-secondary">No types</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-light-warning">
                                                    <i class="material-icons-outlined" style="font-size: 12px;">visibility</i>
                                                    {{ $product->views_count }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($product->discount_percent > 0)
                                                    <span class="badge badge-light-danger">
                                                        <i class="material-icons-outlined" style="font-size: 12px;">local_offer</i>
                                                        {{ $product->discount_percent }}%
                                                    </span>
                                                @else
                                                    <span class="badge badge-light-secondary">No discount</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($product->status)
                                                    <span class="badge badge-light-success">Active</span>
                                                @else
                                                    <span class="badge badge-light-danger">Inactive</span>
                                                @endif
                                            </td>
                                          
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.products.accordions.index', $product) }}" 
                                                       class="btn btn-sm btn-outline-info" 
                                                       title="Accordions">
                                                        <i class="material-icons-outlined">expand_more</i>
                                                    </a>
                                                    <a href="{{ route('admin.products.images.index', $product) }}" 
                                                       class="btn btn-sm btn-outline-success" 
                                                       title="Images">
                                                        <i class="material-icons-outlined">image</i>
                                                    </a>
                                                    <a href="{{ route('admin.products.edit', $product) }}?page={{ request('page', 1) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="Edit">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    <form action="{{ route('admin.products.destroy', $product) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this product?')">
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
                                            <td colspan="9" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="material-icons-outlined" style="font-size: 48px;">inventory_2</i>
                                                    <p class="mt-2">No products found</p>
                                                    @if(request('search') || request('category') || request('type') || request('status'))
                                                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-primary me-2">
                                                            Clear Filters
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                                                        Add First Product
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Bootstrap Pagination -->
                        @if($products->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $products->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
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
    // Auto-select first type if no type is selected
    const typeSelect = document.getElementById('type');
    if (typeSelect && typeSelect.options.length > 0) {
        // If no value is selected, select the first option
        if (!typeSelect.value || typeSelect.value === '') {
            typeSelect.selectedIndex = 0;
            // Trigger change event to update any dependent elements
            typeSelect.dispatchEvent(new Event('change'));
        }
    }
    const sortableList = document.getElementById('sortable-products');
    
    if (sortableList) {
        const sortable = new Sortable(sortableList, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const items = Array.from(sortableList.children).map((tr, index) => {
                    return tr.getAttribute('data-id');
                }).filter(id => id !== null);

                // Send reorder request
                fetch('{{ route("admin.products.reorder") }}', {
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
        sortableList._sortable = sortable;
    }
});

// Select all functionality
const selectAll = document.getElementById('select-all-products');
const checkboxes = document.querySelectorAll('.product-checkbox');
const deleteBtn = document.getElementById('delete-selected-products');

if (selectAll) {
    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        toggleDeleteButton();
    });
}

checkboxes.forEach(cb => {
    cb.addEventListener('change', toggleDeleteButton);
});

function toggleDeleteButton() {
    let anyChecked = Array.from(checkboxes).some(cb => cb.checked);
    deleteBtn.disabled = !anyChecked;
}

deleteBtn.addEventListener('click', function() {
    let ids = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
    if (ids.length === 0) return;
    if (!confirm('Seçilən məhsulları silmək istədiyinizə əminsiniz?')) return;
    fetch("{{ route('admin.products.bulk-delete') }}", {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ ids })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) location.reload();
        else alert(data.message);
    });
});
</script>
@endpush 
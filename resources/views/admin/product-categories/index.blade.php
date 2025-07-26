@extends('admin.layout.app')

@section('title', 'Product Categories Management')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Product Categories Management</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Product Categories</li>
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
                            <h5>Product Categories</h5>
                            <div class="d-flex gap-2">
                                <button type="button" id="delete-selected-btn" class="btn btn-danger" style="display: none;">
                                    <i class="material-icons-outlined">delete</i>
                                </button>
                                <a href="{{ route('admin.product-categories.create') }}" class="btn btn-primary">
                                    <i class="material-icons-outlined">add</i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search Form -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('admin.product-categories.index') }}" class="d-flex">
                                    <input type="text" 
                                           name="search" 
                                           class="form-control me-2" 
                                           placeholder="Search by title, slug, or SEO title..."
                                           value="{{ request('search') }}">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="material-icons-outlined">search</i>
                                    </button>
                                    @if(request('search'))
                                        <a href="{{ route('admin.product-categories.index') }}" class="btn btn-outline-secondary ms-2">
                                            <i class="material-icons-outlined">clear</i>
                                        </a>
                                    @endif
                                </form>
                            </div>
                        </div>

                        <!-- Main Categories Container -->
                        <div id="sortable-main-categories" class="sortable-container">
                            @php
                                $mainCategories = $categories->where('parent_id', null);
                                $subCategories = $categories->where('parent_id', '!=', null);
                            @endphp

                            @forelse($mainCategories as $mainCategory)
                                @php
                                    $firstTranslation = $mainCategory->translates->first();
                                    $categorySubs = $subCategories->where('parent_id', $mainCategory->id);
                                @endphp
                                
                                <!-- Main Category Card -->
                                <div class="category-card" data-id="{{ $mainCategory->id }}">
                                    <div class="card mb-3">
                                        <div class="card-header d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <div class="drag-handle me-3">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <circle cx="8" cy="8" r="1"></circle>
                                                        <circle cx="16" cy="8" r="1"></circle>
                                                        <circle cx="8" cy="16" r="1"></circle>
                                                        <circle cx="16" cy="16" r="1"></circle>
                                                    </svg>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <input type="checkbox" 
                                                           class="form-check-input category-checkbox me-3" 
                                                           value="{{ $mainCategory->id }}">
                                                    <div>
                                                        <h6 class="mb-0">
                                                            <i class="material-icons-outlined text-muted me-2" style="font-size: 16px;">category</i>
                                                            <strong>{{ $firstTranslation ? $firstTranslation->title : 'No title' }}</strong>
                                                        </h6>
                                                        <div class="d-flex gap-1 mt-1">
                                                            @foreach($mainCategory->translates as $translate)
                                                                <span class="badge bg-primary">{{ $translate->language->lang_code }}</span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($mainCategory->status)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                                <span class="badge bg-info">{{ $mainCategory->order }}</span>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.product-categories.edit', $mainCategory) }}" 
                                                       class="btn btn-sm btn-warning" 
                                                       title="Edit">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    <a href="{{ route('admin.product-categories.create', ['parent_id' => $mainCategory->id]) }}" 
                                                       class="btn btn-sm btn-success" 
                                                       title="Add Subcategory">
                                                        <i class="material-icons-outlined">add</i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger delete-btn" 
                                                            data-title="{{ $firstTranslation ? $firstTranslation->title : 'Untitled' }}"
                                                            data-url="{{ route('admin.product-categories.destroy', $mainCategory) }}"
                                                            title="Delete">
                                                        <i class="material-icons-outlined">delete</i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Subcategories Container -->
                                        @if($categorySubs->count() > 0)
                                            <div class="card-body pt-0">
                                                <div class="subcategories-container" data-parent-id="{{ $mainCategory->id }}">
                                                    @foreach($categorySubs as $subCategory)
                                                        @php
                                                            $subTranslation = $subCategory->translates->first();
                                                        @endphp
                                                        <div class="subcategory-item" data-id="{{ $subCategory->id }}">
                                                            <div class="d-flex align-items-center justify-content-between p-2 border rounded mb-2 subcategory-indent">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="drag-handle-sub me-3">
                                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                            <circle cx="8" cy="8" r="1"></circle>
                                                                            <circle cx="16" cy="8" r="1"></circle>
                                                                            <circle cx="8" cy="16" r="1"></circle>
                                                                            <circle cx="16" cy="16" r="1"></circle>
                                                                        </svg>
                                                                    </div>
                                                                    <input type="checkbox" 
                                                                           class="form-check-input category-checkbox me-3" 
                                                                           value="{{ $subCategory->id }}">
                                                                    <div>
                                                                        <span class="me-2" style="color: #6c757d;">└─</span>
                                                                        <strong>{{ $subTranslation ? $subTranslation->title : 'No title' }}</strong>
                                                                        <div class="d-flex gap-1 mt-1">
                                                                            @foreach($subCategory->translates as $translate)
                                                                                <span class="badge bg-primary">{{ $translate->language->lang_code }}</span>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex align-items-center gap-2">
                                                                    @if($subCategory->status)
                                                                        <span class="badge bg-success">Active</span>
                                                                    @else
                                                                        <span class="badge bg-secondary">Inactive</span>
                                                                    @endif
                                                                    <span class="badge bg-info">{{ $subCategory->order }}</span>
                                                                    <div class="d-flex gap-2">
                                                                        <a href="{{ route('admin.product-categories.edit', $subCategory) }}" 
                                                                           class="btn btn-sm btn-warning" 
                                                                           title="Edit">
                                                                            <i class="material-icons-outlined">edit</i>
                                                                        </a>
                                                                        <button type="button" 
                                                                                class="btn btn-sm btn-danger delete-btn" 
                                                                                data-title="{{ $subTranslation ? $subTranslation->title : 'Untitled' }}"
                                                                                data-url="{{ route('admin.product-categories.destroy', $subCategory) }}"
                                                                                title="Delete">
                                                                            <i class="material-icons-outlined">delete</i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="material-icons-outlined" style="font-size: 48px;">category</i>
                                        <p class="mt-2">No product categories found</p>
                                        <a href="{{ route('admin.product-categories.create') }}" class="btn btn-primary">
                                            Create First Category
                                        </a>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form for delete selected -->
<form id="delete-selected-form" action="{{ route('admin.product-categories.delete-selected') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="selected_ids" id="selected-ids">
</form>
@endsection

@push('styles')
<style>
    .drag-handle {
        cursor: grab;
        color: #6c757d;
        transition: color 0.2s;
        padding: 3px;
        border-radius: 3px;
    }
    .drag-handle:hover {
        color: #495057;
        background-color: #f8f9fa;
    }
    .drag-handle:active {
        cursor: grabbing;
    }
    .drag-handle-sub {
        cursor: grab;
        color: #6c757d;
        transition: color 0.2s;
        padding: 3px;
        border-radius: 3px;
    }
    .drag-handle-sub:hover {
        color: #495057;
        background-color: #f8f9fa;
    }
    .drag-handle-sub:active {
        cursor: grabbing;
    }
    .sortable-ghost {
        opacity: 0.5;
    }
    .sortable-chosen {
        background-color: #f8f9fa;
    }
    .category-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .category-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .subcategory-indent {
        margin-left: 30px;
        padding-left: 20px;
        border-left: 3px solid #007bff;
        background-color: #f8f9fa;
    }
    .subcategory-item {
        transition: background-color 0.2s;
    }
    .subcategory-item:hover {
        background-color: #e9ecef;
    }
    .subcategories-container {
        min-height: 20px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
    const deleteSelectedBtn = document.getElementById('delete-selected-btn');
    const deleteSelectedForm = document.getElementById('delete-selected-form');
    const selectedIdsInput = document.getElementById('selected-ids');
    
    // Select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            categoryCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateDeleteButton();
        });
    }
    
    // Individual checkbox functionality
    categoryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateDeleteButton();
            updateSelectAllCheckbox();
        });
    });
    
    function updateDeleteButton() {
        const checkedBoxes = document.querySelectorAll('.category-checkbox:checked');
        if (checkedBoxes.length > 0) {
            deleteSelectedBtn.style.display = 'inline-block';
        } else {
            deleteSelectedBtn.style.display = 'none';
        }
    }
    
    function updateSelectAllCheckbox() {
        if (!selectAllCheckbox) return;
        
        const checkedBoxes = document.querySelectorAll('.category-checkbox:checked');
        const totalBoxes = categoryCheckboxes.length;
        
        if (checkedBoxes.length === 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        } else if (checkedBoxes.length === totalBoxes) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        }
    }
    
    // Delete selected functionality
    deleteSelectedBtn.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.category-checkbox:checked');
        const selectedIds = Array.from(checkedBoxes).map(checkbox => checkbox.value);
        
        if (selectedIds.length === 0) {
            alert('Please select categories to delete.');
            return;
        }
        
        if (confirm(`Are you sure you want to delete ${selectedIds.length} selected category(ies)?`)) {
            selectedIdsInput.value = JSON.stringify(selectedIds);
            deleteSelectedForm.submit();
        }
    });
    
    // Drag and drop reordering for main categories
    const sortableMainList = document.getElementById('sortable-main-categories');
    if (sortableMainList) {
        new Sortable(sortableMainList, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const mainCategories = Array.from(sortableMainList.querySelectorAll('.category-card')).map((card, index) => {
                    return card.getAttribute('data-id');
                });

                fetch('{{ route("admin.product-categories.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ 
                        items: mainCategories,
                        type: 'main'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message || 'Failed to reorder main categories',
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });
    }

    // Drag and drop reordering for subcategories within each parent
    document.querySelectorAll('.subcategories-container').forEach(container => {
        const subcategoryItems = container.querySelectorAll('.subcategory-item');
        if (subcategoryItems.length > 1) {
            new Sortable(container, {
                handle: '.drag-handle-sub',
                animation: 150,
                onEnd: function(evt) {
                    const subcategoryIds = Array.from(container.querySelectorAll('.subcategory-item')).map(item => {
                        return item.getAttribute('data-id');
                    });

                    fetch('{{ route("admin.product-categories.reorder") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ 
                            items: subcategoryIds,
                            type: 'sub'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message || 'Failed to reorder subcategories',
                                icon: 'error'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                }
            });
        }
    });

    // Delete confirmation for all delete buttons
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const title = this.getAttribute('data-title');
            const url = this.getAttribute('data-url');

            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to delete "${title}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.innerHTML = `
                        @csrf
                        @method('DELETE')
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
});
</script>
@endpush 
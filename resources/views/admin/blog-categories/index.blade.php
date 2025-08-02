@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Blog Categories</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Blog Categories</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
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

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>All Blog Categories</h5>
                            <div class="d-flex gap-2">
                                                                 <button type="button" class="btn btn-danger" id="deleteSelectedBtn" style="display: none;">
                                     <i class="material-icons-outlined">delete</i>
                                     Delete Selected
                                 </button>
                                <a href="{{ route('admin.blog-categories.create') }}" class="btn btn-primary">
                                    <i class="material-icons-outlined">add</i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search and Filter Section -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="searchInput" placeholder="Search blog categories...">
                                    <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                        <i class="material-icons-outlined">search</i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                    <label class="form-check-label" for="selectAll">
                                        Select All
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" class="form-check-input" id="headerCheckbox">
                                        </th>
                                        <th width="50">Order</th>
                                        <th>Title</th>
                                        <th width="80">Status</th>
                                        <th width="150">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable-categories">
                                    @forelse($categories as $category)
                                        <tr data-id="{{ $category->id }}" class="category-row">
                                            <td>
                                                <input type="checkbox" class="form-check-input category-checkbox" value="{{ $category->id }}">
                                            </td>
                                            <td>
                                                <div class="drag-handle">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <circle cx="8" cy="8" r="1"></circle>
                                                        <circle cx="16" cy="8" r="1"></circle>
                                                        <circle cx="8" cy="16" r="1"></circle>
                                                        <circle cx="16" cy="16" r="1"></circle>
                                                    </svg>
                                                </div>
                                            </td>
                                                                                         <td>
                                                 @php
                                                     $firstTranslation = $category->translates->first();
                                                 @endphp
                                                 {{ $firstTranslation ? $firstTranslation->title : 'No translation' }}
                                             </td>
                                            <td>
                                                @if($category->status)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.blog-categories.edit', $category) }}" class="btn btn-sm btn-warning">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                                            data-title="{{ $firstTranslation ? $firstTranslation->title : 'Blog Category #' . $category->id }}"
                                                            data-url="{{ route('admin.blog-categories.destroy', $category) }}">
                                                        <i class="material-icons-outlined">delete</i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                                                             @empty
                                         <tr>
                                             <td colspan="5" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="material-icons-outlined" style="font-size: 48px;">category</i>
                                                    <p class="mt-2">No blog categories found</p>
                                                    <a href="{{ route('admin.blog-categories.create') }}" class="btn btn-primary">
                                                        <i class="material-icons-outlined">add</i>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');
    const categoryRows = document.querySelectorAll('.category-row');

    function performSearch() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        
        categoryRows.forEach(row => {
            const title = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const status = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            
            if (title.includes(searchTerm) || status.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    searchBtn.addEventListener('click', performSearch);
    searchInput.addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });

    // Select all functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const headerCheckbox = document.getElementById('headerCheckbox');
    const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
    const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');

    function updateDeleteButton() {
        const checkedBoxes = document.querySelectorAll('.category-checkbox:checked');
        if (checkedBoxes.length > 0) {
            deleteSelectedBtn.style.display = 'inline-block';
        } else {
            deleteSelectedBtn.style.display = 'none';
        }
    }

    function updateSelectAllState() {
        const checkedBoxes = document.querySelectorAll('.category-checkbox:checked');
        const totalBoxes = categoryCheckboxes.length;
        
        if (checkedBoxes.length === 0) {
            selectAllCheckbox.checked = false;
            headerCheckbox.checked = false;
        } else if (checkedBoxes.length === totalBoxes) {
            selectAllCheckbox.checked = true;
            headerCheckbox.checked = true;
        } else {
            selectAllCheckbox.checked = false;
            headerCheckbox.checked = false;
        }
    }

    selectAllCheckbox.addEventListener('change', function() {
        categoryCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateDeleteButton();
        updateSelectAllState();
    });

    headerCheckbox.addEventListener('change', function() {
        categoryCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateDeleteButton();
        updateSelectAllState();
    });

    categoryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateDeleteButton();
            updateSelectAllState();
        });
    });

    // Delete selected functionality
    deleteSelectedBtn.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.category-checkbox:checked');
        const selectedIds = Array.from(checkedBoxes).map(checkbox => checkbox.value);
        
        if (selectedIds.length === 0) {
            Swal.fire({
                title: 'Warning!',
                text: 'Please select at least one blog category to delete.',
                icon: 'warning'
            });
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: `Do you want to delete ${selectedIds.length} selected blog category(ies)?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete them!'
        }).then((result) => {
            if (result.isConfirmed) {
                                 const form = document.createElement('form');
                 form.method = 'POST';
                 form.action = '{{ route("admin.blog-categories.bulk-delete") }}';
                 form.innerHTML = `
                     @csrf
                     ${selectedIds.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('')}
                 `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    });

    // Drag and drop reordering
    const sortableList = document.getElementById('sortable-categories');
    if (sortableList) {
        new Sortable(sortableList, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const categories = Array.from(sortableList.children).map((row, index) => {
                    return row.getAttribute('data-id');
                });

                fetch('{{ route("admin.blog-categories.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ categories: categories })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Blog categories reordered successfully!',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message || 'Failed to reorder blog categories',
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

    // Delete confirmation
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

<style>
.drag-handle {
    cursor: grab;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
}

.drag-handle:active {
    cursor: grabbing;
}

.sortable-ghost {
    opacity: 0.5;
    background-color: #f8f9fa;
}

.sortable-chosen {
    background-color: #e3f2fd;
}
</style>
@endpush 
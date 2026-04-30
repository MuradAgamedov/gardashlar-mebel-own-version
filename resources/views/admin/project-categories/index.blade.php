@extends('admin.layout.app')

@section('title', __('Project Categories Management'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Project Categories Management') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Project Categories Management') }}</li>
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
                            <h5>{{ __('Project Categories') }}</h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-danger" id="deleteSelectedBtn" style="display: none;">
                                    <i class="material-icons-outlined">delete</i>
                                </button>
                                <a href="{{ route('admin.project-categories.create') }}" class="btn btn-primary">
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
                                    <input type="text" class="form-control" id="searchInput" placeholder="{{ __('Search project categories...') }}">
                                    <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                        <i class="material-icons-outlined">search</i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                    <label class="form-check-label" for="selectAll">
                                        {{ __('Select All') }}
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
                                        <th width="50">#</th>
                                        <th>{{ __('Title') }}</th>
                                        <th>{{ __('Slug') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th width="150">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable-project-categories">
                                    @forelse($projectCategories as $projectCategory)
                                        <tr data-id="{{ $projectCategory->id }}" class="category-row">
                                            <td>
                                                <input type="checkbox" class="form-check-input category-checkbox" value="{{ $projectCategory->id }}">
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
                                                    <i class="material-icons-outlined text-muted me-2" style="font-size: 16px;">category</i>
                                                    <div>
                                                        @php
                                                            $mainLanguageId = \App\Models\Language::getMainLanguage()->id;
                                                            $firstTranslation = $projectCategory->translates->firstWhere('language_id', $mainLanguageId)
                                                                ?? $projectCategory->translates->first();
                                                        @endphp
                                                        <strong>{{ $firstTranslation ? $firstTranslation->title : __('No title') }}</strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <code>{{ $firstTranslation ? $firstTranslation->slug : __('No slug') }}</code>
                                            </td>
                                         
                                            <td>
                                                @if($projectCategory->status)
                                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.project-categories.edit', $projectCategory) }}" class="btn btn-sm btn-warning">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                                            data-title="{{ $firstTranslation ? $firstTranslation->title : __('Project Category #:id', ['id' => $projectCategory->id]) }}"
                                                            data-url="{{ route('admin.project-categories.destroy', $projectCategory) }}">
                                                        <i class="material-icons-outlined">delete</i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="material-icons-outlined" style="font-size: 48px;">category</i>
                                                    <p class="mt-2">{{ __('No project categories found') }}</p>
                                                    <a href="{{ route('admin.project-categories.create') }}" class="btn btn-primary">
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

@push('styles')
<style>
    .drag-handle {
        cursor: grab;
        color: #6c757d;
        transition: color 0.2s;
        padding: 5px;
        border-radius: 4px;
    }
    .drag-handle:hover {
        color: #495057;
        background-color: #f8f9fa;
    }
    .drag-handle:active {
        cursor: grabbing;
    }
    .sortable-ghost {
        opacity: 0.5;
    }
    .sortable-chosen {
        background-color: #f8f9fa;
    }
</style>
@endpush

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
            const slug = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            const status = row.querySelector('td:nth-child(6)').textContent.toLowerCase();
            
            if (title.includes(searchTerm) || slug.includes(searchTerm) || status.includes(searchTerm)) {
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
                text: 'Please select at least one project category to delete.',
                icon: 'warning'
            });
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: `Do you want to delete ${selectedIds.length} selected project category(ies)?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete them!'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("admin.project-categories.bulk-delete") }}';
                form.innerHTML = `
                    @csrf
                    ${selectedIds.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('')}
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    });

    // Drag and drop reordering for project categories
    const sortableList = document.getElementById('sortable-project-categories');
    if (sortableList) {
        new Sortable(sortableList, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const projectCategories = Array.from(sortableList.querySelectorAll('tr[data-id]')).map((row, index) => {
                    return row.getAttribute('data-id');
                });

                fetch('{{ route("admin.project-categories.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ items: projectCategories })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message || 'Failed to reorder project categories',
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
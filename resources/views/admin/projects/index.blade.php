@extends('admin.layout.app')

@section('title', 'Projects Management')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Projects Management</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Projects Management</li>
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
                            <h5>Projects</h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-danger" id="deleteSelectedBtn" style="display: none;">
                                    <i class="material-icons-outlined">delete</i>
                                </button>
                                <a href="{{ route('admin.projects.create') }}" class="btn btn-primary">
                                    <i class="material-icons-outlined">add</i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filter Section -->
                    <div class="card-body border-bottom">
                        <form method="GET" action="{{ route('admin.projects.index') }}" class="row g-3">
                            <div class="col-md-3">
                                <label for="category_id" class="form-label">Filter by Category</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        @php
                                            $translation = $category->translates->first();
                                        @endphp
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $translation ? $translation->title : 'Category #' . $category->id }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="searchInput" class="form-label">Search Projects</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="searchInput" placeholder="Search projects...">
                                    <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                        <i class="material-icons-outlined">search</i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-outline-primary me-2">
                                    <i class="material-icons-outlined">filter_list</i>
                                    Filter
                                </button>
                                @if(request('category_id'))
                                    <a href="{{ route('admin.projects.index') }}" class="btn btn-outline-secondary">
                                        <i class="material-icons-outlined">clear</i>
                                        Clear
                                    </a>
                                @endif
                            </div>
                            <div class="col-md-3 d-flex align-items-end justify-content-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                    <label class="form-check-label" for="selectAll">
                                        Select All
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" class="form-check-input" id="headerCheckbox">
                                        </th>
                                        <th width="50">#</th>
                                        <th>Title</th>
                                        <th>Slug</th>
                                        <th>Company</th>
                                        <th>Category</th>
                                        <th>Languages</th>
                                        <th>Status</th>
                                        <th>Order</th>
                                        <th width="150">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable-projects">
                                    @forelse($projects as $project)
                                        <tr data-id="{{ $project->id }}" class="project-row">
                                            <td>
                                                <input type="checkbox" class="form-check-input project-checkbox" value="{{ $project->id }}">
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
                                                    <i class="material-icons-outlined text-muted me-2" style="font-size: 16px;">work</i>
                                                    <div>
                                                        @php
                                                            $firstTranslation = $project->translates->first();
                                                        @endphp
                                                        <strong>{{ $firstTranslation ? $firstTranslation->title : 'No title' }}</strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($firstTranslation && $firstTranslation->slug)
                                                    <code class="text-primary">{{ $firstTranslation->slug }}</code>
                                                @else
                                                    <span class="text-muted">No slug</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($firstTranslation && $firstTranslation->company_name)
                                                    <span class="badge bg-info">{{ $firstTranslation->company_name }}</span>
                                                @else
                                                    <span class="text-muted">No company name</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($project->category)
                                                    @php
                                                        $categoryTranslation = $project->category->translates->first();
                                                    @endphp
                                                    <span class="badge bg-secondary">
                                                        {{ $categoryTranslation ? $categoryTranslation->title : 'No category' }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">No category</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    @foreach($project->translates as $translate)
                                                        <span class="badge bg-primary">{{ $translate->language->lang_code }}</span>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td>
                                                @if($project->status)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>{{ $project->order }}</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-sm btn-warning">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    <a href="{{ route('admin.projects.videos.index', $project) }}" class="btn btn-sm btn-info">
                                                        <i class="material-icons-outlined">video_library</i>
                                                    </a>
                                                    <a href="{{ route('admin.projects.images.index', $project) }}" class="btn btn-sm btn-success">
                                                        <i class="material-icons-outlined">image</i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                                            data-title="{{ $firstTranslation ? $firstTranslation->title : 'Project #' . $project->id }}"
                                                            data-url="{{ route('admin.projects.destroy', $project) }}">
                                                        <i class="material-icons-outlined">delete</i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="material-icons-outlined" style="font-size: 48px;">work</i>
                                                    <p class="mt-2">No projects found</p>
                                                    <a href="{{ route('admin.projects.create') }}" class="btn btn-primary">
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
    const projectRows = document.querySelectorAll('.project-row');

    function performSearch() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        
        projectRows.forEach(row => {
            const title = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const slug = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            const company = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
            const category = row.querySelector('td:nth-child(6)').textContent.toLowerCase();
            const status = row.querySelector('td:nth-child(8)').textContent.toLowerCase();
            
            if (title.includes(searchTerm) || slug.includes(searchTerm) || company.includes(searchTerm) || category.includes(searchTerm) || status.includes(searchTerm)) {
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
    const projectCheckboxes = document.querySelectorAll('.project-checkbox');
    const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');

    function updateDeleteButton() {
        const checkedBoxes = document.querySelectorAll('.project-checkbox:checked');
        if (checkedBoxes.length > 0) {
            deleteSelectedBtn.style.display = 'inline-block';
        } else {
            deleteSelectedBtn.style.display = 'none';
        }
    }

    function updateSelectAllState() {
        const checkedBoxes = document.querySelectorAll('.project-checkbox:checked');
        const totalBoxes = projectCheckboxes.length;
        
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
        projectCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateDeleteButton();
        updateSelectAllState();
    });

    headerCheckbox.addEventListener('change', function() {
        projectCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateDeleteButton();
        updateSelectAllState();
    });

    projectCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateDeleteButton();
            updateSelectAllState();
        });
    });

    // Delete selected functionality
    deleteSelectedBtn.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.project-checkbox:checked');
        const selectedIds = Array.from(checkedBoxes).map(checkbox => checkbox.value);
        
        if (selectedIds.length === 0) {
            Swal.fire({
                title: 'Warning!',
                text: 'Please select at least one project to delete.',
                icon: 'warning'
            });
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: `Do you want to delete ${selectedIds.length} selected project(s)?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete them!'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("admin.projects.bulk-delete") }}';
                form.innerHTML = `
                    @csrf
                    ${selectedIds.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('')}
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    });

    // Drag and drop reordering for projects
    const sortableList = document.getElementById('sortable-projects');
    if (sortableList) {
        new Sortable(sortableList, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const projects = Array.from(sortableList.querySelectorAll('tr[data-id]')).map((row, index) => {
                    return row.getAttribute('data-id');
                });

                fetch('{{ route("admin.projects.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ items: projects })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message || 'Failed to reorder projects',
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
@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Pages Management</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Pages</li>
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
                            <h5>Pages List</h5>
                            <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">
                                <i class="material-icons-outlined">add</i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card overflow-hidden">
                            <div class="card-header card-no-border">
                                <h3>Pages Table</h3>
                                <p class="desc mb-0 mt-1">Manage your application pages. Drag and drop to reorder.</p>
                            </div>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr class="border-bottom-secondary border-top-0">
                                            <th scope="col" style="width: 50px;">#</th>
                                            <th scope="col">ID</th>
                                            <th scope="col">Title</th>
                                            <th scope="col">Slug</th>
                                            <th scope="col">Languages</th>
                                            <th scope="col">Order</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sortable-pages">
                                        @forelse($pages as $page)
                                        <tr class="border-bottom-{{ $loop->even ? 'info' : 'success' }}" data-page-id="{{ $page->id }}">
                                            <td class="drag-handle" style="cursor: move;">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M8 6C8 7.10457 7.10457 8 6 8C4.89543 8 4 7.10457 4 6C4 4.89543 4.89543 4 6 4C7.10457 4 8 4.89543 8 6Z" fill="#6c757d"/>
                                                    <path d="M8 12C8 13.1046 7.10457 14 6 14C4.89543 14 4 13.1046 4 12C4 10.8954 4.89543 10 6 10C7.10457 10 8 10.8954 8 12Z" fill="#6c757d"/>
                                                    <path d="M8 18C8 19.1046 7.10457 20 6 20C4.89543 20 4 19.1046 4 18C4 16.8954 4.89543 16 6 16C7.10457 16 8 16.8954 8 18Z" fill="#6c757d"/>
                                                    <path d="M20 6C20 7.10457 19.1046 8 18 8C16.8954 8 16 7.10457 16 6C16 4.89543 16.8954 4 18 4C19.1046 4 20 4.89543 20 6Z" fill="#6c757d"/>
                                                    <path d="M20 12C20 13.1046 19.1046 14 18 14C16.8954 14 16 13.1046 16 12C16 10.8954 16.8954 10 18 10C19.1046 10 20 10.8954 20 12Z" fill="#6c757d"/>
                                                    <path d="M20 18C20 19.1046 19.1046 20 18 20C16.8954 20 16 19.1046 16 18C16 16.8954 16.8954 16 18 16C19.1046 16 20 16.8954 20 18Z" fill="#6c757d"/>
                                                </svg>
                                            </td>
                                            <th scope="row">{{ $page->id }}</th>
                                            <td>
                                                @if($page->translates->count() > 0)
                                                    {{ $page->translates->first()->title }}
                                                @else
                                                    <span class="text-muted">No title</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($page->translates->count() > 0)
                                                    <span class="badge badge-light-primary">{{ $page->translates->first()->slug }}</span>
                                                @else
                                                    <span class="text-muted">No slug</span>
                                                @endif
                                            </td>
                                            <td>
                                                @foreach($page->translates as $translate)
                                                    <span class="badge badge-light-info me-1">{{ $translate->language->lang_code }}</span>
                                                @endforeach
                                            </td>
                                            <td>{{ $page->order }}</td>
                                            <td>
                                                @if($page->status)
                                                    <span class="badge badge-light-success">Active</span>
                                                @else
                                                    <span class="badge badge-light-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger delete-page-btn" 
                                                            data-page-id="{{ $page->id }}"
                                                            data-page-title="{{ $page->translates->first()->title ?? 'Page' }}"
                                                            data-delete-url="{{ route('admin.pages.destroy', $page) }}">
                                                        <i class="material-icons-outlined">delete</i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No pages found</td>
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
</div>

<!-- Hidden form for delete -->
<form id="delete-page-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<!-- SortableJS -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up delete buttons and sortable...');
    
    // Initialize Sortable
    const sortableList = document.getElementById('sortable-pages');
    if (sortableList) {
        new Sortable(sortableList, {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            onEnd: function(evt) {
                const pageIds = [];
                const rows = sortableList.querySelectorAll('tr[data-page-id]');
                
                rows.forEach((row, index) => {
                    const pageId = row.getAttribute('data-page-id');
                    if (pageId) {
                        pageIds.push({
                            id: pageId,
                            order: index + 1
                        });
                    }
                });
                
                // Send the new order to the server
                updatePageOrder(pageIds);
            }
        });
    }
    
    // SweetAlert for delete confirmation
    document.querySelectorAll('.delete-page-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const pageId = this.getAttribute('data-page-id');
            const pageTitle = this.getAttribute('data-page-title');
            const deleteUrl = this.getAttribute('data-delete-url');
            
            console.log('Delete button clicked for:', pageTitle);
            
            // Check if SweetAlert is available
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Are you sure?',
                    text: `Do you want to delete "${pageTitle}"? This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        console.log('SweetAlert confirmed, submitting form...');
                        submitDeleteForm(deleteUrl);
                    }
                });
            } else {
                // Fallback to basic confirm
                console.log('SweetAlert not available, using basic confirm...');
                if (confirm(`Do you want to delete "${pageTitle}"? This action cannot be undone.`)) {
                    submitDeleteForm(deleteUrl);
                }
            }
        });
    });
    
    function submitDeleteForm(deleteUrl) {
        const form = document.getElementById('delete-page-form');
        form.action = deleteUrl;
        form.submit();
    }
    
    function updatePageOrder(pageIds) {
        fetch('{{ route("admin.pages.reorder") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ pages: pageIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Pages reordered successfully.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    alert('Pages reordered successfully.');
                }
                
                // Update order numbers in the table
                const rows = sortableList.querySelectorAll('tr[data-page-id]');
                rows.forEach((row, index) => {
                    const orderCell = row.querySelector('td:nth-child(6)'); // Order column
                    if (orderCell) {
                        orderCell.textContent = index + 1;
                    }
                });
            } else {
                console.error('Error reordering pages:', data.message);
            }
        })
        .catch(error => {
            console.error('Error updating page order:', error);
        });
    }
});
</script>

<style>
.sortable-ghost {
    opacity: 0.5;
    background: #f8f9fa !important;
}

.sortable-chosen {
    background: #e3f2fd !important;
}

.sortable-drag {
    background: #fff !important;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.drag-handle {
    cursor: move;
    user-select: none;
}

.drag-handle:hover {
    background-color: #f8f9fa;
}
</style>
@endpush 
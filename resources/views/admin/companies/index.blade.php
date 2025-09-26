@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Companies</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Companies</li>
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
                            <h5>Companies List</h5>
                            <a href="{{ route('admin.companies.create') }}" class="btn btn-primary">
                                <i class="material-icons-outlined">add</i>
                                Add New
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($companies->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="50">#</th>
                                            <th width="100">Card Image</th>
                                            <th width="100">Inner Image</th>
                                            <th>Title</th>
                                            <th>Languages</th>
                                            <th>Status</th>
                                            <th>Order</th>
                                            <th width="200">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sortable-list">
                                        @foreach($companies as $company)
                                            <tr data-id="{{ $company->id }}" class="sortable-item">
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
                                                        $mainTranslate = $company->translate()->first();
                                                    @endphp
                                                    @if($mainTranslate && $mainTranslate->card_image)
                                                        <img src="{{ asset('storage/' . $mainTranslate->card_image) }}" 
                                                             alt="{{ $mainTranslate->card_image_alt_text ?? 'Card Image' }}" 
                                                             class="img-thumbnail" 
                                                             style="width: 60px; height: 60px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-light d-flex align-items-center justify-content-center" 
                                                             style="width: 60px; height: 60px;">
                                                            <i class="material-icons-outlined text-muted">image</i>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($mainTranslate && $mainTranslate->inner_image)
                                                        <img src="{{ asset('storage/' . $mainTranslate->inner_image) }}" 
                                                             alt="{{ $mainTranslate->inner_image_alt_text ?? 'Inner Image' }}" 
                                                             class="img-thumbnail" 
                                                             style="width: 60px; height: 60px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-light d-flex align-items-center justify-content-center" 
                                                             style="width: 60px; height: 60px;">
                                                            <i class="material-icons-outlined text-muted">image</i>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $mainTranslate?->title ?? 'No title' }}</strong>
                                                        @if($mainTranslate)
                                                            <span class="badge bg-info ms-1">{{ $mainTranslate->language->lang_code }}</span>
                                                        @endif
                                                    </div>
                                                    <small class="text-muted">{{ Str::limit($mainTranslate?->description ?? '', 100) }}</small>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-1">
                                                        @foreach($company->translates as $translate)
                                                            <span class="badge bg-primary">{{ $translate->language->lang_code }}</span>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($company->status)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>{{ $company->order }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.companies.actions', $company->id) }}" 
                                                           class="btn btn-sm btn-outline-success" 
                                                           title="Məhsul Əlavə Et">
                                                            <i class="material-icons-outlined">inventory</i>
                                                        </a>
                                                        <a href="{{ route('admin.companies.edit', $company->id) }}" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="material-icons-outlined">edit</i>
                                                        </a>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-danger delete-btn" 
                                                                data-id="{{ $company->id }}"
                                                                data-title="{{ $mainTranslate?->title ?? 'This company' }}"
                                                                data-url="{{ route('admin.companies.destroy', $company->id) }}">
                                                            <i class="material-icons-outlined">delete</i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="material-icons-outlined text-muted" style="font-size: 4rem;">business</i>
                                <h5 class="mt-3 text-muted">No companies found</h5>
                                <p class="text-muted">Click the button below to add the first company.</p>
                                <a href="{{ route('admin.companies.create') }}" class="btn btn-primary">
                                    <i class="material-icons-outlined">add</i>
                                    Add First Company
                                </a>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Sortable
    const sortableList = document.getElementById('sortable-list');
    if (sortableList) {
        new Sortable(sortableList, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const items = Array.from(sortableList.querySelectorAll('.sortable-item')).map(item => item.dataset.id);
                
                fetch('{{ route("admin.companies.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ order: items })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update order numbers in the table
                        const rows = sortableList.querySelectorAll('.sortable-item');
                        rows.forEach((row, index) => {
                            const orderCell = row.querySelector('td:nth-child(7)');
                            if (orderCell) {
                                orderCell.textContent = index + 1;
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });
    }

    // Delete confirmation with SweetAlert
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const title = this.dataset.title;
            const url = this.dataset.url;
            
            Swal.fire({
                title: 'Are you sure?',
                text: `Are you sure you want to delete "${title}" company?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create form and submit
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
    cursor: move;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}

.drag-handle:hover {
    color: #495057;
}
</style>
@endpush 
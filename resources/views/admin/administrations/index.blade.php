@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Administrations</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Administrations</li>
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
                            <h5>Administration Members</h5>
                            <a href="{{ route('admin.administrations.create') }}" class="btn btn-primary">
                                <i class="material-icons-outlined">add</i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($administrations->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="50">#</th>
                                            <th width="100">Image</th>
                                            <th>Title</th>
                                            <th>Position</th>
                                            <th>Status</th>
                                            <th width="200">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sortable-list">
                                        @foreach($administrations as $administration)
                                            @php
                                                $mainLanguageId = \App\Models\Language::getMainLanguage()->id;
                                                $mainTranslate = $administration->translates->firstWhere('language_id', $mainLanguageId)
                                                    ?? $administration->translates->first();
                                            @endphp
                                            <tr data-id="{{ $administration->id }}" class="sortable-item">
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
                                                    @if($administration->image)
                                                        <img src="{{ asset('storage/' . $administration->image) }}" 
                                                             alt="Administration Image" 
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
                                                </td>
                                                <td>
                                                    <span class="text-muted">{{ $mainTranslate?->position ?? 'No position' }}</span>
                                                </td>
                                                <td>
                                                    @if($administration->status)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.administrations.edit', $administration->id) }}" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="material-icons-outlined">edit</i>
                                                        </a>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-danger delete-btn" 
                                                                data-id="{{ $administration->id }}"
                                                                data-title="{{ $mainTranslate?->title ?? 'This administration member' }}">
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
                                <i class="material-icons-outlined text-muted" style="font-size: 4rem;">people</i>
                                <h5 class="mt-3 text-muted">No administration members found</h5>
                                <p class="text-muted">Click the button below to add the first administration member.</p>
                                <a href="{{ route('admin.administrations.create') }}" class="btn btn-primary">
                                    <i class="material-icons-outlined">add</i>
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
                
                fetch('{{ route("admin.administrations.reorder") }}', {
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
                        console.log('Order updated successfully');
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
            const id = this.dataset.id;
            const title = this.dataset.title;
            
            Swal.fire({
                title: 'Are you sure?',
                text: `Are you sure you want to delete "${title}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/administrations/${id}`;
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
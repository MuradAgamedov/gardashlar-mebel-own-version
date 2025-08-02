@extends('admin.layout.app')

@section('title', 'Contact Management')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Contact Management</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Contact Management</li>
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

        <!-- Phone Types Section -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Phone Types</h5>
                            <a href="{{ route('admin.phone-types.create') }}" class="btn btn-primary">
                                <i class="material-icons-outlined">add</i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="sortable-phone-types" class="sortable-container">
                            @forelse($phoneTypes as $phoneType)
                                @php
                                    $firstTranslation = $phoneType->translates->first();
                                    $typePhones = $phones->where('phone_type_id', $phoneType->id);
                                @endphp
                                
                                <!-- Phone Type Card -->
                                <div class="phone-type-card" data-id="{{ $phoneType->id }}">
                                    <div class="card mb-3 phone-type-indent">
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
                                                <div>
                                                    <h6 class="mb-0">
                                                        <strong>{{ $firstTranslation ? $firstTranslation->title : 'No translation' }}</strong>
                                                    </h6>
                                                    <div class="d-flex gap-1 mt-1">
                                                        @foreach($phoneType->translates as $translate)
                                                            <span class="badge bg-primary">{{ $translate->language->lang_code }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($phoneType->status)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.phone-types.edit', $phoneType) }}" class="btn btn-sm btn-warning">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    <a href="{{ route('admin.phone-types.phones.create', $phoneType->id) }}" class="btn btn-sm btn-success">
                                                        <i class="material-icons-outlined">add</i> 
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                                            data-title="{{ $firstTranslation ? $firstTranslation->title : 'Phone Type #' . $phoneType->id }}"
                                                            data-url="{{ route('admin.phone-types.destroy', $phoneType) }}">
                                                        <i class="material-icons-outlined">delete</i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Phones Container -->
                                        <div class="card-body p-0">
                                            <div class="phones-container" data-parent="{{ $phoneType->id }}">
                                                @if($typePhones->count() > 0)
                                                    @foreach($typePhones as $phone)
                                                        <div class="phone-item" data-phone-id="{{ $phone->id }}">
                                                            <div class="d-flex align-items-center justify-content-between p-2 border-bottom">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="drag-handle-phone me-2">
                                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                            <circle cx="8" cy="8" r="1"></circle>
                                                                            <circle cx="16" cy="8" r="1"></circle>
                                                                            <circle cx="8" cy="16" r="1"></circle>
                                                                            <circle cx="16" cy="16" r="1"></circle>
                                                                        </svg>
                                                                    </div>
                                                                    <div>
                                                                        <div class="d-flex align-items-center">
                                                                            <i class="material-icons-outlined text-muted me-1" style="font-size: 14px;">phone</i>
                                                                            <a href="tel:{{ $phone->phone }}" class="text-decoration-none small">
                                                                                {{ $phone->phone }}
                                                                            </a>
                                                                        </div>
                                                                        <span class="badge bg-info mt-1" style="font-size: 10px;">Phone</span>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex align-items-center gap-1">
                                                                    @if($phone->status)
                                                                        <span class="badge bg-success" style="font-size: 10px;">Active</span>
                                                                    @else
                                                                        <span class="badge bg-secondary" style="font-size: 10px;">Inactive</span>
                                                                    @endif
                                                                                                                                         <div class="d-flex gap-1">
                                                                         <a href="{{ route('admin.phones.edit', $phone) }}" class="btn btn-sm btn-warning" style="padding: 2px 6px;">
                                                                             <i class="material-icons-outlined" style="font-size: 14px;">edit</i>
                                                                         </a>
                                                                         @if($phone->phone_type_id != 3)
                                                                             <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                                                                     style="padding: 2px 6px;"
                                                                                     data-title="{{ $phone->phone }}"
                                                                                     data-url="{{ route('admin.phones.destroy', $phone) }}">
                                                                                 <i class="material-icons-outlined" style="font-size: 14px;">delete</i>
                                                                             </button>
                                                                         @endif
                                                                     </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="p-2 text-muted">
                                                        <i class="material-icons-outlined me-1" style="font-size: 14px;">info</i>
                                                        <small>No phones assigned to this type</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="material-icons-outlined" style="font-size: 48px;">phone</i>
                                        <p class="mt-2">No phone types found</p>
                                        <a href="{{ route('admin.phone-types.create') }}" class="btn btn-primary">
                                            <i class="material-icons-outlined">add</i>
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
    .drag-handle-phone {
        cursor: grab;
        color: #6c757d;
        transition: color 0.2s;
        padding: 3px;
        border-radius: 3px;
    }
    .drag-handle-phone:hover {
        color: #495057;
        background-color: #f8f9fa;
    }
    .drag-handle-phone:active {
        cursor: grabbing;
    }
    .sortable-ghost {
        opacity: 0.5;
    }
    .sortable-chosen {
        background-color: #f8f9fa;
    }
    .phone-type-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .phone-type-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .phone-type-indent {
        margin-left: 30px;
        padding-left: 20px;
        border-left: 3px solid #007bff;
        background-color: #f8f9fa;
    }
    .phone-item {
        transition: background-color 0.2s;
    }
    .phone-item:hover {
        background-color: #e9ecef;
    }
    .phones-container {
        min-height: 20px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Drag and drop reordering for phone types
    const sortableList = document.getElementById('sortable-phone-types');
    if (sortableList) {
        new Sortable(sortableList, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const phoneTypes = Array.from(sortableList.querySelectorAll('.phone-type-card')).map((card, index) => {
                    return card.getAttribute('data-id');
                });

                fetch('{{ route("admin.phone-types.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ phone_types: phoneTypes })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message || 'Failed to reorder phone types',
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

    // Drag and drop reordering for phones within each type
    document.querySelectorAll('.phones-container').forEach(container => {
        const phoneItems = container.querySelectorAll('.phone-item');
        if (phoneItems.length > 1) {
            new Sortable(container, {
                handle: '.drag-handle-phone',
                animation: 150,
                onEnd: function(evt) {
                    const phoneIds = Array.from(container.querySelectorAll('.phone-item')).map(item => {
                        return item.getAttribute('data-phone-id');
                    });

                    fetch('{{ route("admin.phones.reorder") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ phones: phoneIds })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message || 'Failed to reorder phones',
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
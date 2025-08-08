@extends('admin.layout.app')

@section('title', 'Project Images Management')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Project Images Management</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.projects.index') }}">Projects Management</a>
                        </li>
                        <li class="breadcrumb-item active">Project Images</li>
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
                <h6 class="alert-heading">Error occurred:</h6>
                <p class="mb-0">{{ session('error') }}</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Images for Project: 
                                @php
                                    $firstTranslation = $project->translates->first();
                                @endphp
                                <span class="text-primary">{{ $firstTranslation ? $firstTranslation->title : 'Project #' . $project->id }}</span>
                            </h5>
                            <a href="{{ route('admin.projects.images.create', $project) }}" class="btn btn-primary">
                                <i class="material-icons-outlined">add</i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row" id="sortable-images">
                            @forelse($images as $image)
                                <div class="col-md-3 col-sm-6 mb-4" data-id="{{ $image->id }}">
                                    <div class="card h-100">
                                        <div class="card-img-top position-relative">
                                            <img src="{{ $image->image_url }}" 
                                                 class="card-img-top" 
                                                 alt="Project Image"
                                                 style="height: 200px; object-fit: cover;">
                                            <div class="position-absolute top-0 start-0 m-2">
                                                <div class="drag-handle bg-white rounded p-1 shadow-sm">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <circle cx="8" cy="8" r="1"></circle>
                                                        <circle cx="16" cy="8" r="1"></circle>
                                                        <circle cx="8" cy="16" r="1"></circle>
                                                        <circle cx="16" cy="16" r="1"></circle>
                                                    </svg>
                                                </div>
                                            </div>
                                            @if($image->is_main_image)
                                                <div class="position-absolute top-0 end-0 m-2">
                                                    <span class="badge bg-warning">
                                                        <i class="material-icons-outlined" style="font-size: 12px;">star</i>
                                                        Main
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <small class="text-muted">Order: {{ $image->order }}</small>
                                                @if($image->status)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </div>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('admin.projects.images.edit', [$project, $image]) }}" class="btn btn-sm btn-warning action-btn">
                                                    <i class="material-icons-outlined">edit</i>
                                                </a>
                                                @if(!$image->is_main_image)
                                                    <button type="button" class="btn btn-sm btn-primary set-main-btn action-btn" 
                                                            data-title="Image #{{ $image->id }}"
                                                            data-url="{{ route('admin.projects.images.set-main', [$project, $image]) }}">
                                                        <i class="material-icons-outlined">star</i>
                                                    </button>
                                                @endif
                                                <button type="button" class="btn btn-sm btn-danger delete-btn action-btn" 
                                                        data-title="Image #{{ $image->id }}"
                                                        data-url="{{ route('admin.projects.images.destroy', [$project, $image]) }}">
                                                    <i class="material-icons-outlined">delete</i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="material-icons-outlined" style="font-size: 64px;">image</i>
                                            <h5 class="mt-3">No images found for this project</h5>
                                            <p class="mb-4">Start by adding some images to showcase this project</p>
                                            <a href="{{ route('admin.projects.images.create', $project) }}" class="btn btn-primary">
                                                <i class="material-icons-outlined">add</i>
                                            </a>
                                        </div>
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
        cursor: grab !important;
        color: #6c757d;
        transition: all 0.2s ease;
        background: rgba(255, 255, 255, 0.95) !important;
        border: 1px solid rgba(0, 0, 0, 0.15);
        padding: 6px !important;
        border-radius: 6px;
        backdrop-filter: blur(4px);
        z-index: 10;
        position: relative;
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
    }
    .drag-handle:hover {
        color: #495057;
        background: rgba(255, 255, 255, 1) !important;
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        cursor: grabbing !important;
    }
    .drag-handle:active {
        cursor: grabbing !important;
        transform: scale(0.95);
        background: rgba(255, 255, 255, 0.8) !important;
    }
    .drag-handle * {
        pointer-events: none;
    }
    .sortable-ghost {
        opacity: 0.5;
    }
    .sortable-chosen {
        transform: rotate(5deg);
    }
    
    /* Action button styling */
    .action-btn {
        min-width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    
    .action-btn .material-icons-outlined {
        font-size: 18px;
        line-height: 1;
    }
    
    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .action-btn:active {
        transform: translateY(0);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing sortable...');
    
    // Drag and drop reordering for images
    const sortableContainer = document.getElementById('sortable-images');
    console.log('Sortable container:', sortableContainer);
    
    if (sortableContainer) {
        // Check if Sortable is available
        if (typeof Sortable === 'undefined') {
            console.error('Sortable.js is not loaded!');
            // Fallback: Add click handlers to drag handles
            document.querySelectorAll('.drag-handle').forEach(handle => {
                handle.addEventListener('click', function() {
                    Swal.fire({
                        title: 'Drag & Drop Not Available',
                        text: 'Please refresh the page to enable drag and drop functionality.',
                        icon: 'info'
                    });
                });
            });
            return;
        }
        
        console.log('Initializing Sortable with container:', sortableContainer);
        
        // Check if drag handles exist
        const dragHandles = sortableContainer.querySelectorAll('.drag-handle');
        console.log('Found drag handles:', dragHandles.length);
        
        if (dragHandles.length === 0) {
            console.error('No drag handles found!');
            return;
        }
        
        // Test drag handle functionality
        dragHandles.forEach((handle, index) => {
            handle.addEventListener('click', function(e) {
                console.log('Drag handle clicked:', index);
            });
        });
        
        const sortable = new Sortable(sortableContainer, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            forceFallback: false,
            fallbackClass: 'sortable-fallback',
            onStart: function(evt) {
                console.log('Drag started:', evt);
            },
            onEnd: function(evt) {
                console.log('Drag ended:', evt);
                const images = Array.from(sortableContainer.querySelectorAll('[data-id]')).map((item, index) => {
                    return item.getAttribute('data-id');
                });

                fetch('{{ route("admin.projects.images.reorder", $project) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ items: images })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Update order numbers in the UI silently
                        images.forEach((imageId, index) => {
                            const item = sortableContainer.querySelector(`[data-id="${imageId}"]`);
                            if (item) {
                                const orderElement = item.querySelector('.text-muted');
                                if (orderElement) {
                                    orderElement.textContent = `Order: ${index + 1}`;
                                }
                            }
                        });
                        console.log('Project images reordered successfully');
                    } else {
                        console.error('Failed to reorder project images:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error reordering project images:', error);
                });
            }
        });
    }

    // Set main image confirmation for all set-main buttons
    document.querySelectorAll('.set-main-btn').forEach(button => {
        button.addEventListener('click', function() {
            const title = this.getAttribute('data-title');
            const url = this.getAttribute('data-url');

            Swal.fire({
                title: 'Set as Main Image?',
                text: `Do you want to set "${title}" as the main image?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, set as main!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.innerHTML = `
                        @csrf
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
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
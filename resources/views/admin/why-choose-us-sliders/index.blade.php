@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Why Choose Us Sliders</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Why Choose Us Sliders</li>
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
                            <h5>Slider List</h5>
                            <a href="{{ route('admin.why-choose-us-sliders.create') }}" class="btn btn-primary">
                                <i class="material-icons-outlined">add</i>
                                Add New Slider
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($sliders->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="50">Order</th>
                                            <th width="100">Image</th>
                                            <th>Video Link</th>
                                            <th width="80">Status</th>
                                            <th width="120">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sortable-sliders">
                                        @foreach($sliders as $slider)
                                        <tr data-id="{{ $slider->id }}">
                                            <td class="drag-handle" style="cursor: move;">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M8 6C8 7.10457 7.10457 8 6 8C4.89543 8 4 7.10457 4 6C4 4.89543 4.89543 4 6 4C7.10457 4 8 4.89543 8 6Z" fill="#6c757d"/>
                                                    <path d="M8 12C8 13.1046 7.10457 14 6 14C4.89543 14 4 13.1046 4 12C4 10.8954 4.89543 10 6 10C7.10457 10 8 10.8954 8 12Z" fill="#6c757d"/>
                                                    <path d="M8 18C8 19.1046 7.10457 20 6 20C4.89543 20 4 19.1046 4 18C4 16.8954 4.89543 16 6 16C7.10457 16 8 16.8954 8 18Z" fill="#6c757d"/>
                                                    <path d="M20 6C20 7.10457 19.1046 8 18 8C16.8954 8 16 7.10457 16 6C16 4.89543 16.89543 4 18 4C19.1046 4 20 4.89543 20 6Z" fill="#6c757d"/>
                                                    <path d="M20 12C20 13.1046 19.1046 14 18 14C16.8954 14 16 13.1046 16 12C16 10.8954 16.8954 10 18 10C19.1046 10 20 10.8954 20 12Z" fill="#6c757d"/>
                                                    <path d="M20 18C20 19.1046 19.1046 20 18 20C16.8954 20 16 19.1046 16 18C16 16.8954 16.8954 16 18 16C19.1046 16 20 16.8954 20 18Z" fill="#6c757d"/>
                                                </svg>
                                            </td>
                                            <td>
                                                @if($slider->image_url)
                                                    <img src="{{ $slider->image_url }}" alt="Slider Image" class="img-thumbnail" style="max-width: 60px; max-height: 60px;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                        <i class="material-icons-outlined text-muted">image</i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @if($slider->video_link)
                                                    <a href="{{ $slider->video_link }}" target="_blank" class="text-primary">
                                                        <i class="material-icons-outlined">play_circle</i>
                                                        View Video
                                                    </a>
                                                @else
                                                    <span class="text-muted">No video</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($slider->status)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('admin.why-choose-us-sliders.edit', $slider) }}" 
                                                       class="btn btn-sm btn-warning" 
                                                       title="Edit">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger delete-slider" 
                                                            data-id="{{ $slider->id }}"
                                                            data-title="Slider #{{ $slider->id }}"
                                                            title="Delete">
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
                                <i class="material-icons-outlined text-muted" style="font-size: 64px;">image</i>
                                <h5 class="mt-3 text-muted">No sliders found</h5>
                                <p class="text-muted">Create your first slider to get started.</p>
                                <a href="{{ route('admin.why-choose-us-sliders.create') }}" class="btn btn-primary">
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
    // Initialize Sortable for reordering
    const sortableList = document.getElementById('sortable-sliders');
    if (sortableList) {
        new Sortable(sortableList, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const sliders = Array.from(sortableList.querySelectorAll('tr')).map(tr => tr.dataset.id);
                
                fetch('{{ route("admin.why-choose-us-sliders.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ sliders: sliders })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Order Updated!',
                            text: 'Slider order has been updated successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        console.error('Reorder failed:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to update slider order.'
                    });
                });
            }
        });
    }

    // Delete slider functionality
    document.querySelectorAll('.delete-slider').forEach(function(button) {
        button.addEventListener('click', function() {
            const sliderId = this.dataset.id;
            const sliderTitle = this.dataset.title;
            
            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to delete "${sliderTitle}"? This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create and submit delete form
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `{{ url('admin/why-choose-us-sliders') }}/${sliderId}`;
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';
                    
                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
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
    user-select: none;
}

.drag-handle:hover {
    background-color: #f8f9fa;
}

.sortable-ghost {
    opacity: 0.5;
    background-color: #e9ecef;
}

.sortable-chosen {
    background-color: #fff3cd;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.75em;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.btn-sm .material-icons-outlined {
    font-size: 16px;
}
</style>
@endpush 
@extends('admin.layout.app')

@section('title', 'Blog Images Management')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Blog Images Management</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.blog-posts.index') }}">Blog Posts</a>
                        </li>
                        <li class="breadcrumb-item active">Blog Images</li>
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
                            <h5>Blog Images - {{ $blogPost->translate()->title ?? 'No title' }}</h5>
                                                         <div class="d-flex gap-2">
                                 <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadImageModal">
                                     <i class="material-icons-outlined">add_photo_alternate</i>
                                     Upload Image
                                 </button>
                                 <a href="{{ route('admin.blog-posts.index') }}" class="btn btn-secondary">
                                     <i class="material-icons-outlined">arrow_back</i>
                                     Back to Blog Posts
                                 </a>
                             </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        @if($images->count() > 0)
                            <div class="row" id="imagesContainer">
                                @foreach($images as $image)
                                    <div class="col-md-3 col-sm-6 mb-4" data-id="{{ $image->id }}">
                                        <div class="card image-card" style="cursor: move;">
                                            <div class="card-body p-2">
                                                <img src="{{ asset('storage/' . $image->image_url) }}" 
                                                     alt="Blog Image" 
                                                     class="img-fluid rounded" 
                                                     style="width: 100%; height: 200px; object-fit: cover;">
                                                
                                                <div class="mt-2 d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">Order: {{ $image->order }}</small>
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-warning btn-sm" 
                                                                onclick="editImage({{ $image->id }}, '{{ asset('storage/' . $image->image_url) }}')">
                                                            <i class="material-icons-outlined">edit</i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm delete-btn" 
                                                                data-id="{{ $image->id }}"
                                                                data-url="{{ route('admin.blog-posts.images.destroy', [$blogPost, $image]) }}">
                                                            <i class="material-icons-outlined">delete</i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="text-muted">
                                    <i class="material-icons-outlined" style="font-size: 64px;">photo_library</i>
                                    <h5 class="mt-3">No images found</h5>
                                    <p>Upload your first image to get started.</p>
                                                                         <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadImageModal">
                                         <i class="material-icons-outlined">add_photo_alternate</i>
                                         Upload Image
                                     </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Image Modal -->
<div class="modal fade" id="uploadImageModal" tabindex="-1" aria-labelledby="uploadImageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.blog-posts.images.store', $blogPost) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                                         <h5 class="modal-title" id="uploadImageModalLabel">Upload New Images</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                                         <div class="mb-3">
                         <label for="images" class="form-label">Select Images</label>
                         <input type="file" class="form-control @error('images') is-invalid @enderror" 
                                id="images" name="images[]" accept="image/*" multiple required>
                         @error('images')
                             <div class="invalid-feedback">{{ $message }}</div>
                         @enderror
                         @error('images.*')
                             <div class="invalid-feedback">{{ $message }}</div>
                         @enderror
                         <small class="form-text text-muted">Max size: 2MB per image. Supported formats: JPEG, PNG, JPG, GIF, WEBP</small>
                     </div>
                     <div class="mt-2" id="imagePreviews">
                         <!-- Image previews will be shown here -->
                     </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                         <button type="submit" class="btn btn-primary">Upload Images</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Image Modal -->
<div class="modal fade" id="editImageModal" tabindex="-1" aria-labelledby="editImageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editImageForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editImageModalLabel">Edit Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editImage" class="form-label">Select New Image</label>
                        <input type="file" class="form-control @error('editImage') is-invalid @enderror" 
                               id="editImage" name="image" accept="image/*" required>
                        @error('editImage')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Max size: 2MB. Supported formats: JPEG, PNG, JPG, GIF, WEBP</small>
                    </div>
                                         <div class="mt-2">
                         <img id="editImagePreview" src="" alt="Image Preview" 
                              class="img-thumbnail" style="max-width: 100%; display: none;">
                     </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Image</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .image-card {
        transition: transform 0.2s;
    }
    .image-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
    // Image preview for upload
    const imageInput = document.getElementById('images');
    const imagePreviews = document.getElementById('imagePreviews');
    
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const files = e.target.files;
            imagePreviews.innerHTML = ''; // Clear previous previews
            
            if (files.length > 0) {
                Array.from(files).forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewDiv = document.createElement('div');
                        previewDiv.className = 'mb-2';
                        previewDiv.innerHTML = `
                            <img src="${e.target.result}" alt="Image Preview ${index + 1}" 
                                 class="img-thumbnail" style="max-width: 100px; max-height: 100px; object-fit: cover;">
                            <small class="d-block text-muted">${file.name}</small>
                        `;
                        imagePreviews.appendChild(previewDiv);
                    };
                    reader.readAsDataURL(file);
                });
            }
        });
    }

    // Image preview for edit
    const editImageInput = document.getElementById('editImage');
    const editImagePreview = document.getElementById('editImagePreview');
    
    if (editImageInput) {
        editImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    editImagePreview.src = e.target.result;
                    editImagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                editImagePreview.style.display = 'none';
            }
        });
    }

    // Sortable functionality
    const imagesContainer = document.getElementById('imagesContainer');
    if (imagesContainer) {
        new Sortable(imagesContainer, {
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            onEnd: function(evt) {
                const items = Array.from(imagesContainer.children).map((item, index) => ({
                    id: item.dataset.id,
                    order: index + 1
                }));

                fetch('{{ route("admin.blog-posts.images.reorder", $blogPost) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ images: items })
                })
                .then(response => response.json())
                                 .then(data => {
                     if (!data.success) {
                         Swal.fire({
                             title: 'Error!',
                             text: data.message,
                             icon: 'error'
                         });
                     }
                 })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to reorder images',
                        icon: 'error'
                    });
                });
            }
        });
    }

    // Delete confirmation
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const imageId = this.getAttribute('data-id');
            const url = this.getAttribute('data-url');

            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to delete this image?',
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

function editImage(imageId, currentImageUrl) {
    const editImageForm = document.getElementById('editImageForm');
    const editImagePreview = document.getElementById('editImagePreview');
    
    editImageForm.action = '{{ route("admin.blog-posts.images.update", [$blogPost, ":imageId"]) }}'.replace(':imageId', imageId);
    editImagePreview.src = currentImageUrl;
    editImagePreview.style.display = 'block';
    
    const editImageModal = new bootstrap.Modal(document.getElementById('editImageModal'));
    editImageModal.show();
}
</script>
@endpush 
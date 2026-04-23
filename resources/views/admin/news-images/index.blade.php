@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>News Post Images</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.news-posts.index') }}">News Posts</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.news-posts.edit', $news_post) }}">Edit</a>
                        </li>
                        <li class="breadcrumb-item active">Images</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Images for: {{ $news_post->title ?? 'News Post #' . $news_post->id }}</h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                    <i class="material-icons-outlined">add</i>
                                    Add Image
                                </button>
                                <a href="{{ route('admin.news-posts.edit', $news_post) }}" class="btn btn-secondary">
                                    <i class="material-icons-outlined">arrow_back</i>
                                    Back to Post
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($news_post->images->count() > 0)
                            <div class="row" id="sortable-images">
                                @foreach($news_post->images as $image)
                                    <div class="col-md-3 mb-3" data-id="{{ $image->id }}">
                                        <div class="card image-card">
                                            <div class="card-body p-2">
                                                <div class="image-container">
                                                    <img src="{{ $image->image_url }}" alt="News Image" class="img-fluid rounded">
                                                    <div class="image-actions">
                                                        <button type="button" class="btn btn-sm btn-warning edit-btn" 
                                                                data-id="{{ $image->id }}"
                                                                data-url="{{ $image->image_url }}">
                                                            <i class="material-icons-outlined">edit</i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                                                data-id="{{ $image->id }}"
                                                                data-url="{{ route('admin.news-posts.images.destroy', [$news_post, $image]) }}">
                                                            <i class="material-icons-outlined">delete</i>
                                                        </button>
                                                    </div>
                                                    <div class="drag-handle">
                                                        <i class="material-icons-outlined">drag_indicator</i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="material-icons-outlined" style="font-size: 48px; color: #ccc;">photo_library</i>
                                <p class="text-muted mt-2">No images uploaded yet</p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                    <i class="material-icons-outlined">add</i>
                                    Upload First Image
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="uploadForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="image" class="form-label">Select Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                        <small class="text-muted">Supported formats: JPEG, PNG, JPG, GIF, WebP (Max: 2MB)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="editImageId" name="image_id">
                    <div class="mb-3">
                        <label for="editImage" class="form-label">Select New Image</label>
                        <input type="file" class="form-control" id="editImage" name="image" accept="image/*" required>
                        <small class="text-muted">Supported formats: JPEG, PNG, JPG, GIF, WebP (Max: 2MB)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Current Image:</label><br>
                        <img id="currentImage" src="" alt="Current Image" class="img-thumbnail" style="max-height: 150px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const newsPostId = {{ $news_post->id }};

    // Sortable functionality
    const sortableImages = document.getElementById('sortable-images');
    if (sortableImages) {
        new Sortable(sortableImages, {
            animation: 150,
            handle: '.drag-handle',
            onEnd: function(evt) {
                const imageIds = Array.from(sortableImages.children).map(row => row.getAttribute('data-id'));
                
                fetch(`/api/admin/news-posts/${newsPostId}/images/reorder`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ images: imageIds })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Images reordered successfully',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.message || 'Failed to reorder images'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Something went wrong'
                    });
                });
            }
        });
    }

    // Upload form
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch(`/api/admin/news-posts/${newsPostId}/images`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message || 'Failed to upload image'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Something went wrong'
            });
        });
    });

    // Edit buttons
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const imageId = this.getAttribute('data-id');
            const imageUrl = this.getAttribute('data-url');
            
            document.getElementById('editImageId').value = imageId;
            document.getElementById('currentImage').src = imageUrl;
            document.getElementById('editImage').value = '';
            
            new bootstrap.Modal(document.getElementById('editModal')).show();
        });
    });

    // Edit form
    document.getElementById('editForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const imageId = document.getElementById('editImageId').value;
        const formData = new FormData(this);
        
        fetch(`/api/admin/news-posts/${newsPostId}/images/${imageId}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message || 'Failed to update image'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Something went wrong'
            });
        });
    });

    // Delete buttons
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const imageId = this.getAttribute('data-id');
            const deleteUrl = this.getAttribute('data-url');
            
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
                    fetch(deleteUrl, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: data.message || 'Failed to delete image'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Something went wrong'
                        });
                    });
                }
            });
        });
    });
});
</script>

<style>
.image-card {
    transition: transform 0.2s;
}

.image-card:hover {
    transform: translateY(-2px);
}

.image-container {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
}

.image-container img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.image-actions {
    position: absolute;
    top: 8px;
    right: 8px;
    display: flex;
    gap: 4px;
    opacity: 0;
    transition: opacity 0.2s;
}

.image-container:hover .image-actions {
    opacity: 1;
}

.drag-handle {
    position: absolute;
    bottom: 8px;
    right: 8px;
    background: rgba(0,0,0,0.7);
    color: white;
    border-radius: 4px;
    padding: 4px;
    cursor: grab;
    font-size: 16px;
}

.drag-handle:active {
    cursor: grabbing;
}

.sortable-ghost {
    opacity: 0.5;
}

.sortable-chosen {
    transform: scale(1.05);
}
</style>
@endpush

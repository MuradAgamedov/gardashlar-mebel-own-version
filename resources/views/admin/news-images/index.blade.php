@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('News Post Images') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.news-posts.index') }}">{{ __('News Posts') }}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.news-posts.edit', $news_post) }}">{{ __('Edit') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Images') }}</li>
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
                            <h5>{{ __('Images for:') }} {{ $news_post->title ?? __('News Post #') . $news_post->id }}</h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal" title="{{ __('Add Image') }}">
                                    <i class="material-icons-outlined">add</i>
                                </button>
                                <a href="{{ route('admin.news-posts.edit', $news_post) }}" class="btn btn-secondary" title="{{ __('Back to Post') }}">
                                    <i class="material-icons-outlined">arrow_back</i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Upload Form -->
<div class="card mb-4">
    <div class="card-header">
        <h5>{{ __('Upload Images') }}</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.news-posts.images.store', $news_post) }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="images" class="form-label">{{ __('Select Multiple Images') }}</label>
                        <input type="file" class="form-control" id="images" name="images[]" accept="image/*" multiple required>
                        <small class="text-muted">{{ __('Supported formats: JPEG, PNG, JPG, GIF, WebP (Max: 2MB each)') }}</small>
                    </div>
                    <div class="mb-3">
                        <div id="imagePreview" class="row g-2"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="material-icons-outlined">upload</i>
                        {{ __('Upload Images') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Existing Images -->
@if($news_post->images->count() > 0)
    <div class="card">
        <div class="card-header">
            <h5>{{ __('Existing Images') }} ({{ $news_post->images->count() }})</h5>
        </div>
        <div class="card-body">
            <div class="row" id="sortable-images">
                @foreach($news_post->images as $image)
                    <div class="col-md-3 mb-3" data-id="{{ $image->id }}">
                        <div class="card image-card">
                            <div class="card-body p-2">
                                <div class="image-container">
                                    <img src="{{ $image->image_url }}" alt="{{ __('News Image') }}" class="img-fluid rounded">
                                    <div class="image-actions">
                                        <!-- Delete button - top right -->
                                        <form method="POST" action="{{ route('admin.news-posts.images.destroy', [$news_post, $image]) }}" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('{{ __('Are you sure you want to delete this image?') }}')">
                                                <i class="material-icons-outlined">delete</i>
                                            </button>
                                        </form>
                                    </div>
                                    <div class="edit-actions">
                                        <!-- Edit button - bottom left -->
                                        <button type="button" class="btn btn-sm btn-warning edit-btn" 
                                                data-id="{{ $image->id }}"
                                                data-url="{{ $image->image_url }}">
                                            <i class="material-icons-outlined">edit</i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="material-icons-outlined" style="font-size: 48px; color: #ccc;">photo_library</i>
            <p class="text-muted mt-2">{{ __('No images uploaded yet') }}</p>
        </div>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview functionality
    const imageInput = document.getElementById('images');
    const imagePreview = document.getElementById('imagePreview');
    
    imageInput.addEventListener('change', function(e) {
        imagePreview.innerHTML = '';
        const files = Array.from(e.target.files);
        
        files.forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-md-4';
                    col.innerHTML = `
                        <div class="card">
                            <img src="${e.target.result}" class="img-fluid rounded" style="height: 100px; object-fit: cover;">
                            <div class="card-body p-2">
                                <small class="text-muted">${file.name}</small>
                            </div>
                        </div>
                    `;
                    imagePreview.appendChild(col);
                };
                reader.readAsDataURL(file);
            }
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
    opacity: 0;
    transition: opacity 0.2s;
}

.edit-actions {
    position: absolute;
    bottom: 8px;
    left: 8px;
    opacity: 0;
    transition: opacity 0.2s;
}

.image-container:hover .image-actions,
.image-container:hover .edit-actions {
    opacity: 1;
}
</style>
@endpush

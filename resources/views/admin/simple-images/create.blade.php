@extends('admin.layout.app')

@section('title', 'Add Simple Image')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Add Simple Image</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.simple-images.index') }}">Simple Images</a>
                        </li>
                        <li class="breadcrumb-item active">Add</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">Error occurred:</h6>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
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
                        <h5>New Simple Image</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.simple-images.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Keyword -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Keyword <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('keyword') is-invalid @enderror" 
                                           name="keyword" 
                                           value="{{ old('keyword') }}" 
                                           placeholder="Enter keyword (e.g., hero_image, logo)" 
                                           required>
                                    <small class="form-text text-muted">Use lowercase with underscores (e.g., hero_image, logo, banner)</small>
                                    @error('keyword')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Image Upload -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Image <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" 
                                           class="form-control @error('image') is-invalid @enderror" 
                                           name="image" 
                                           accept="image/*"
                                           required>
                                    <small class="form-text text-muted">Supported formats: JPG, PNG, GIF, WebP. Max size: 2MB</small>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Translations -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label">Translations</label>
                                    <div class="card border">
                                        <div class="card-body">
                                            @foreach($languages as $language)
                                                <div class="row mb-3">
                                                    <div class="col-md-2">
                                                        <label class="form-label">
                                                            <span class="badge bg-primary">{{ strtoupper($language->lang_code) }}</span>
                                                            {{ $language->title }}
                                                        </label>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <input type="text" 
                                                               class="form-control @error('translations.' . $language->id . '.image_go_url') is-invalid @enderror" 
                                                               name="translations[{{ $language->id }}][image_go_url]" 
                                                               value="{{ old('translations.' . $language->id . '.image_go_url') }}" 
                                                               placeholder="Enter image go URL for {{ $language->title }}" 
                                                               required>
                                                        <input type="hidden" name="translations[{{ $language->id }}][language_id]" value="{{ $language->id }}">
                                                        @error('translations.' . $language->id . '.image_go_url')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Image Preview -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div id="imagePreview" class="d-none">
                                        <label class="form-label">Preview:</label>
                                        <div class="border rounded p-3">
                                            <img id="previewImg" src="" alt="Preview" class="img-fluid" style="max-height: 200px;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.simple-images.index') }}" class="btn btn-secondary">
                                            <i class="material-icons-outlined">arrow_back</i>
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">add</i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.querySelector('input[name="image"]');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');

    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.classList.add('d-none');
        }
    });
});
</script>
@endsection 
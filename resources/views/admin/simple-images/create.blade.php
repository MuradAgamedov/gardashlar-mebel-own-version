@extends('admin.layout.app')

@section('title', __('Add Simple Image'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Add Simple Image') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.simple-images.index') }}">{{ __('Simple Images') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Add') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">{{ __('Validation Errors:') }}</h6>
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
                <h6 class="alert-heading">Upload Error:</h6>
                <p class="mb-0">{{ session('error') }}</p>
                <hr>
                <p class="mb-0"><strong>Possible solutions:</strong></p>
                <ul class="mb-0">
                    <li>Check if the image file is not corrupted</li>
                    <li>Ensure the image is in JPG, PNG, GIF, or WebP format</li>
                    <li>Try uploading a different image file</li>
                    <li>Check server storage permissions</li>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">{{ __('Success:') }}</h6>
                <p class="mb-0">{{ session('success') }}</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('New Simple Image') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.simple-images.store') }}" method="POST" enctype="multipart/form-data" id="imageUploadForm">
                            @csrf
                            
                            <!-- Keyword -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        {{ __('Keyword') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('keyword') is-invalid @enderror" 
                                           name="keyword" 
                                           value="{{ old('keyword') }}" 
                                           placeholder="{{ __('Enter keyword (e.g., hero_image, logo)') }}" 
                                           required>
                                    <small class="form-text text-muted">{{ __('Use lowercase with underscores (e.g., hero_image, logo, banner)') }}</small>
                                    @error('keyword')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Image Upload -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        {{ __('Image') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" 
                                           class="form-control @error('image') is-invalid @enderror" 
                                           name="image" 
                                           accept="image/jpeg,image/png,image/gif,image/webp"
                                           id="imageInput"
                                           required>
                                    <small class="form-text text-muted">{{ __('Supported formats: JPG, PNG, GIF, WebP') }}</small>
                                    <div id="fileInfo" class="mt-2 d-none">
                                        <small class="text-info">
                                            <span id="fileName"></span> (<span id="fileSize"></span>)
                                        </small>
                                    </div>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Translations -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label">{{ __('Translations') }}</label>
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
                                                    <div class="col-md-5">
                                                        <input type="text" 
                                                               class="form-control @error('translations.' . $language->id . '.image_go_url') is-invalid @enderror" 
                                                               name="translations[{{ $language->id }}][image_go_url]" 
                                                               value="{{ old('translations.' . $language->id . '.image_go_url') }}" 
                                                               placeholder="Enter image go URL for {{ $language->title }}">
                                                        <input type="hidden" name="translations[{{ $language->id }}][language_id]" value="{{ $language->id }}">
                                                        @error('translations.' . $language->id . '.image_go_url')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-5">
                                                        <input type="text" 
                                                               class="form-control @error('translations.' . $language->id . '.image_alt_text') is-invalid @enderror" 
                                                               name="translations[{{ $language->id }}][image_alt_text]" 
                                                               value="{{ old('translations.' . $language->id . '.image_alt_text') }}" 
                                                               placeholder="Enter image alt text for {{ $language->title }}">
                                                        @error('translations.' . $language->id . '.image_alt_text')
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
                                        <label class="form-label">{{ __('Preview:') }}</label>
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
                                        <a href="{{ route('admin.simple-images.index') }}" class="btn btn-secondary" title="{{ __('Cancel') }}">
                                            <i class="material-icons-outlined">arrow_back</i>
                                        </a>
                                        <button type="submit" class="btn btn-primary" id="submitBtn" title="{{ __('Save') }}">
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
    const imageInput = document.querySelector('#imageInput');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const form = document.getElementById('imageUploadForm');

    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        
        if (file) {
            // Show file info
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            fileInfo.classList.remove('d-none');

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                alert('Invalid file type. Please choose a JPG, PNG, GIF, or WebP image.');
                this.value = '';
                fileInfo.classList.add('d-none');
                imagePreview.classList.add('d-none');
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        } else {
            fileInfo.classList.add('d-none');
            imagePreview.classList.add('d-none');
        }
    });

    // Form submission handling
    form.addEventListener('submit', function(e) {
        const file = imageInput.files[0];
        
        if (file) {
            // Show loading state
            submitBtn.disabled = true;
            submitText.textContent = 'Uploading...';
            submitBtn.innerHTML = '<i class="material-icons-outlined">hourglass_empty</i> Uploading...';
        }
    });

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});
</script>
@endsection 
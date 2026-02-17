@extends('admin.layout.app')

@section('title', 'Quick Upload - ' . $keyword)

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Quick Upload - {{ $keyword }}</h3>
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
                        <li class="breadcrumb-item active">Quick Upload - {{ $keyword }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">Validation Errors:</h6>
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
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">Success:</h6>
                <p class="mb-0">{{ session('success') }}</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Quick Upload for Keyword: <span class="text-primary">{{ $keyword }}</span></h5>
                    </div>
                    <div class="card-body">
                        @if($simpleImage)
                            <div class="alert alert-info">
                                <h6>Current Image:</h6>
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <img src="{{ $simpleImage->image_url }}" alt="{{ $simpleImage->keyword }}" class="img-fluid img-thumbnail" style="max-height: 100px;">
                                    </div>
                                    <div class="col-md-10">
                                        <p class="mb-1"><strong>Keyword:</strong> {{ $simpleImage->keyword }}</p>
                                        <p class="mb-1"><strong>Current Image:</strong> {{ $simpleImage->image_path }}</p>
                                        <p class="mb-0"><strong>Created:</strong> {{ $simpleImage->created_at->format('d.m.Y H:i') }}</p>
                                    </div>
                                </div>
                                <hr>
                                <p class="mb-0 text-warning"><i class="material-icons">warning</i> Uploading a new image will replace the current one.</p>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <h6>New Image:</h6>
                                <p class="mb-0">No image exists for keyword "<strong>{{ $keyword }}</strong>". Uploading will create a new entry.</p>
                            </div>
                        @endif

                        <form action="{{ route('admin.simple-images.quick-upload.store', $keyword) }}" method="POST" enctype="multipart/form-data" id="imageUploadForm">
                            @csrf
                            
                            <!-- Image Upload -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Image <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" 
                                           class="form-control @error('image') is-invalid @enderror" 
                                           name="image" 
                                           accept="image/jpeg,image/png,image/gif,image/webp"
                                           id="imageInput"
                                           required>
                                    <small class="form-text text-muted">Supported formats: JPG, PNG, GIF, WebP</small>
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
                                                    <div class="col-md-5">
                                                        <input type="text" 
                                                               class="form-control @error('translations.' . $language->id . '.image_go_url') is-invalid @enderror" 
                                                               name="translations[{{ $language->id }}][image_go_url]" 
                                                               value="{{ $translations->get($language->id)?->image_go_url ?? old('translations.' . $language->id . '.image_go_url') }}" 
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
                                                               value="{{ $translations->get($language->id)?->image_alt_text ?? old('translations.' . $language->id . '.image_alt_text') }}" 
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

                            <!-- Submit Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="material-icons">upload</i> Upload Image
                                    </button>
                                    <a href="{{ route('admin.simple-images.index') }}" class="btn btn-secondary">
                                        <i class="material-icons">arrow_back</i> Back to List
                                    </a>
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
    const imageInput = document.getElementById('imageInput');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');

    imageInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            fileName.textContent = file.name;
            fileSize.textContent = (file.size / 1024).toFixed(2) + ' KB';
            fileInfo.classList.remove('d-none');
        } else {
            fileInfo.classList.add('d-none');
        }
    });
});
</script>
@endsection

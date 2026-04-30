@extends('admin.layout.app')

@section('title', __('Create KIV'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Create KIV') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i> {{ __('Home') }}
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.kivs.index') }}">{{ __('KIVs') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Create KIV') }}</li>
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
                        <h5>{{ __('Create New KIV') }}</h5>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.kivs.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Image Upload Section -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="image" class="form-label">{{ __('Image') }}</label>
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">{{ __('Supported formats: JPG, JPEG, PNG, GIF, WEBP. Max size: 2MB') }}</small>
                                </div>
                                <div class="col-md-6">
                                    <div id="image-preview" class="mt-3" style="display: none;">
                                        <img id="preview-image" src="" alt="{{ __('Image Preview') }}" class="img-thumbnail" style="max-width: 200px; max-height: 150px;">
                                    </div>
                                </div>
                            </div>

                            <!-- Video Link Section -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="video_link" class="form-label">{{ __('Video Link') }}</label>
                                    <input type="url" class="form-control @error('video_link') is-invalid @enderror" id="video_link" name="video_link" value="{{ old('video_link') }}" placeholder="{{ __('Enter video link (optional)') }}">
                                    @error('video_link')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">{{ __('YouTube video URL') }}</small>
                                </div>
                            </div>

                            <!-- Category Selection -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="kiv_category_id" class="form-label">{{ __('Category') }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('kiv_category_id') is-invalid @enderror" id="kiv_category_id" name="kiv_category_id" required>
                                        <option value="">{{ __('Select a category') }}</option>
                                        @foreach($categories as $category)
                                            @php
                                                $categoryTranslation = $category->translates->first();
                                            @endphp
                                            <option value="{{ $category->id }}" {{ old('kiv_category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $categoryTranslation ? $categoryTranslation->title : __('Unknown Category') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kiv_category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status Section -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input type="hidden" name="status" value="0">
                                        <input class="form-check-input" type="checkbox" name="status" id="status" value="1" {{ old('status', '1') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">
                                            {{ __('Active Status') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">save</i>
                                        </button>
                                        <a href="{{ route('admin.kivs.index') }}" class="btn btn-secondary">
                                            <i class="material-icons-outlined">cancel</i>
                                        </a>
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview functionality
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    const previewImage = document.getElementById('preview-image');

    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.style.display = 'none';
        }
    });
});
</script>
@endpush 
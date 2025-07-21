@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Edit Why Choose Us Slider</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.why-choose-us-sliders.index') }}">Why Choose Us Sliders</a>
                        </li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="material-icons-outlined">error</i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="alert-heading">Please fix the following errors:</h6>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Edit Slider</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.why-choose-us-sliders.update', $whyChooseUsSlider) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <!-- Media Section -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h6 class="mb-3">Media</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Slider Image</label>
                                            <div class="image-upload-container">
                                                <input type="file" 
                                                       class="form-control @error('image') is-invalid @enderror image-file-input" 
                                                       name="image" 
                                                       accept="image/*"
                                                       data-preview="image-preview">
                                                @error('image')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <div class="image-preview mt-2" id="image-preview">
                                                    @if($whyChooseUsSlider->image_url)
                                                        <img src="{{ $whyChooseUsSlider->image_url }}" alt="Current Image" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Video Link</label>
                                            <input type="url" 
                                                   class="form-control @error('video_link') is-invalid @enderror" 
                                                   name="video_link" 
                                                   value="{{ old('video_link', $whyChooseUsSlider->video_link) }}"
                                                   placeholder="https://www.youtube.com/watch?v=... or https://vimeo.com/...">
                                            @error('video_link')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">YouTube, Vimeo or any video URL</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Section -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input type="hidden" name="status" value="0">
                                        <input class="form-check-input" type="checkbox" name="status" id="status" value="1" {{ old('status', $whyChooseUsSlider->status) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">
                                            Active Status
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">save</i>
                                            Update Slider
                                        </button>
                                        <a href="{{ route('admin.why-choose-us-sliders.index') }}" class="btn btn-secondary">
                                            <i class="material-icons-outlined">cancel</i>
                                            Cancel
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
    document.querySelectorAll('.image-file-input').forEach(function(input) {
        input.addEventListener('change', function() {
            const file = this.files[0];
            const previewId = this.getAttribute('data-preview');
            const previewContainer = document.getElementById(previewId);
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewContainer.innerHTML = `<img src="${e.target.result}" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">`;
                };
                reader.readAsDataURL(file);
            } else {
                // Keep existing image if no new file selected
                if (!previewContainer.querySelector('img')) {
                    previewContainer.innerHTML = '';
                }
            }
        });
    });
});
</script>

<style>
.invalid-feedback.d-block {
    display: block !important;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}

.image-upload-container {
    position: relative;
}

.image-preview {
    text-align: center;
    min-height: 50px;
    border: 1px dashed #dee2e6;
    border-radius: 0.375rem;
    padding: 0.5rem;
    background-color: #f8f9fa;
}

.image-preview img {
    border-radius: 0.25rem;
}
</style>
@endpush 
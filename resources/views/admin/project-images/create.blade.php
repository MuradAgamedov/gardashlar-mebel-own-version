@extends('admin.layout.app')

@section('title', 'Add Project Images')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Add Project Images</h3>
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
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.projects.images.index', $project) }}">Project Images</a>
                        </li>
                        <li class="breadcrumb-item active">Add Images</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <h6 class="alert-heading mb-1">{{ __('Please fix the following errors:') }}</h6>
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

        @if (session('upload_errors') && count(session('upload_errors')) > 0)
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">Upload Warnings:</h6>
                <ul class="mb-0">
                    @foreach (session('upload_errors') as $error)
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
                        <h5>Add New Images for Project:
                            @php
                                $firstTranslation = $project->translates->first();
                            @endphp
                            <span class="text-primary">{{ $firstTranslation ? $firstTranslation->title : 'Project #' . $project->id }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.projects.images.store', $project) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Multiple Image Upload -->
                            <div style="padding: 15px" class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">
                                        Images <span class="text-danger">*</span>
                                    </label>
                                    <input type="file"
                                           class="form-control @error('images') is-invalid @enderror"
                                           name="images[]"
                                           accept="image/*"
                                           id="imageInput"
                                           multiple
                                           required>
                                    @error('images')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    @error('images.*')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Supported formats: JPEG, PNG, JPG, GIF, WebP. Maximum size: 2MB per image. You can select multiple images at once.
                                    </small>
                                    <div id="selectedFiles" class="mt-3"></div>
                                </div>
                            </div>



                            <!-- Status -->
                            <div style="padding: 15px" class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <input type="hidden" name="status" value="0">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" checked>
                                        <label class="form-check-label" for="status">Active</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">save</i>
                                        </button>
                                        <a href="{{ route('admin.projects.images.index', $project) }}" class="btn btn-secondary">
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
    const imageInput = document.getElementById('imageInput');
    const selectedFiles = document.getElementById('selectedFiles');

    imageInput.addEventListener('change', function() {
        if (this.files && this.files.length > 0) {
            selectedFiles.innerHTML = '';

            // Optional: Add file count warning for very large selections
            if (this.files.length > 100) {
                selectedFiles.innerHTML = `
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>Warning:</strong> You have selected ${this.files.length} images. This may take a while to upload.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
            }

            // Process each file
            Array.from(this.files).forEach((file, index) => {
                const fileName = file.name;
                const fileSize = (file.size / 1024 / 1024).toFixed(2); // Convert to MB

                // Create preview for each file
                const reader = new FileReader();
                reader.onload = function(e) {
                    const filePreview = document.createElement('div');
                    filePreview.className = 'alert alert-info alert-dismissible fade show mb-2';
                    filePreview.innerHTML = `
                        <div class="row">
                            <div class="col-md-8">
                                <strong>Image ${index + 1}:</strong> ${fileName}<br>
                                <strong>Size:</strong> ${fileSize} MB<br>
                                <strong>Type:</strong> ${file.type}
                            </div>
                            <div class="col-md-4 text-center">
                                <img src="${e.target.result}" alt="Preview ${index + 1}" class="img-fluid rounded" style="max-height: 120px; max-width: 100%;">
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    selectedFiles.appendChild(filePreview);
                };
                reader.readAsDataURL(file);
            });

            // Add summary
            const summary = document.createElement('div');
            summary.className = 'alert alert-success alert-dismissible fade show';
            summary.innerHTML = `
                <strong>Summary:</strong> ${this.files.length} image(s) selected and ready for upload.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            selectedFiles.appendChild(summary);

        } else {
            selectedFiles.innerHTML = '';
        }
    });
});
</script>
@endpush

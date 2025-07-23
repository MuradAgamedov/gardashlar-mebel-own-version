@extends('admin.layout.app')

@section('title', 'Add Project Image')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Add Project Image</h3>
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
                        <li class="breadcrumb-item active">Add Image</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
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
                        <h5>Add New Image for Project: 
                            @php
                                $firstTranslation = $project->translates->first();
                            @endphp
                            <span class="text-primary">{{ $firstTranslation ? $firstTranslation->title : 'Project #' . $project->id }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.projects.images.store', $project) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Image Upload -->
                            <div style="padding: 15px" class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">
                                        Image <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" 
                                           class="form-control @error('image') is-invalid @enderror" 
                                           name="image" 
                                           accept="image/*"
                                           id="imageInput"
                                           required>
                                    @error('image')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Supported formats: JPEG, PNG, JPG, GIF. Maximum size: 2MB
                                    </small>
                                    <div id="selectedFileName" class="mt-2"></div>
                                </div>
                            </div>



                            <!-- Status -->
                            <div style="padding: 15px" class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
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
                                            Add Image
                                        </button>
                                        <a href="{{ route('admin.projects.images.index', $project) }}" class="btn btn-secondary">
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
    const imageInput = document.getElementById('imageInput');
    const selectedFileName = document.getElementById('selectedFileName');
    
    imageInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            const fileName = file.name;
            const fileSize = (file.size / 1024 / 1024).toFixed(2); // Convert to MB
            
            // Create preview
            const reader = new FileReader();
            reader.onload = function(e) {
                selectedFileName.innerHTML = `
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <div class="row">
                            <div class="col-md-8">
                                <strong>Selected file:</strong> ${fileName}<br>
                                <strong>Size:</strong> ${fileSize} MB<br>
                                <strong>Type:</strong> ${file.type}
                            </div>
                            <div class="col-md-4 text-center">
                                <img src="${e.target.result}" alt="Preview" class="img-fluid rounded" style="max-height: 150px; max-width: 100%;">
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
            };
            reader.readAsDataURL(file);
        } else {
            selectedFileName.innerHTML = '';
        }
    });
});
</script>
@endpush 
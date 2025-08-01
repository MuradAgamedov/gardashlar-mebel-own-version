@extends('admin.layout.app')

@section('title', 'Add Project Category to Home Page')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Add Project Category to Home Page</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.home-page-project-categories.index') }}">Home Page Project Categories</a></li>
                            <li class="breadcrumb-item active">Add New</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Select Project Category</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.home-page-project-categories.store') }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <!-- Project Category Selection -->
                                    <div class="mb-3">
                                        <label for="project_category_id" class="form-label">Project Category <span class="text-danger">*</span></label>
                                        <select name="project_category_id" id="project_category_id" class="form-select @error('project_category_id') is-invalid @enderror" required>
                                            <option value="">Select a project category</option>
                                            @foreach($projectCategories as $category)
                                                <option value="{{ $category->id }}" 
                                                        {{ old('project_category_id') == $category->id ? 'selected' : '' }}
                                                        data-image="{{ $category->image_url }}"
                                                        data-description="{{ $category->translate()?->description ?? '' }}">
                                                    {{ $category->translate()?->title ?? 'No Title' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('project_category_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Choose a project category to display on the home page.</small>
                                    </div>

                                    <!-- Preview Section -->
                                    <div id="category-preview" class="mb-3" style="display: none;">
                                        <label class="form-label">Preview</label>
                                        <div class="card border">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <img id="preview-image" src="" class="img-fluid rounded" style="max-height: 100px; object-fit: cover;" alt="Category Image">
                                                    </div>
                                                    <div class="col-md-9">
                                                        <h6 id="preview-title" class="mb-2"></h6>
                                                        <p id="preview-description" class="text-muted mb-0 small"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <!-- Info Card -->
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="fas fa-info-circle text-primary"></i>
                                                Information
                                            </h6>
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-2">
                                                    <small class="text-muted">
                                                        <i class="fas fa-check-circle text-success"></i>
                                                        Selected categories will appear on the home page
                                                    </small>
                                                </li>
                                                <li class="mb-2">
                                                    <small class="text-muted">
                                                        <i class="fas fa-sort text-warning"></i>
                                                        Order can be changed later by dragging
                                                    </small>
                                                </li>
                                                <li class="mb-2">
                                                    <small class="text-muted">
                                                        <i class="fas fa-toggle-on text-info"></i>
                                                        Status can be toggled on/off
                                                    </small>
                                                </li>
                                                <li>
                                                    <small class="text-muted">
                                                        <i class="fas fa-exclamation-triangle text-warning"></i>
                                                        Only active project categories are available
                                                    </small>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.home-page-project-categories.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i> Back to List
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Add to Home Page
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('project_category_id');
    const preview = document.getElementById('category-preview');
    const previewImage = document.getElementById('preview-image');
    const previewTitle = document.getElementById('preview-title');
    const previewDescription = document.getElementById('preview-description');

    select.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value) {
            // Show preview
            preview.style.display = 'block';
            
            // Update preview content
            previewTitle.textContent = selectedOption.textContent;
            previewDescription.textContent = selectedOption.dataset.description || 'No description available';
            
            const imageUrl = selectedOption.dataset.image;
            if (imageUrl) {
                previewImage.src = imageUrl;
                previewImage.style.display = 'block';
            } else {
                previewImage.style.display = 'none';
            }
        } else {
            // Hide preview
            preview.style.display = 'none';
        }
    });
});
</script>
@endpush
@endsection 
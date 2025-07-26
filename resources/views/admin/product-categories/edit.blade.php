@extends('admin.layout.app')

@section('title', 'Edit Product Category')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Edit Product Category</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.product-categories.index') }}">Product Categories</a>
                        </li>
                        <li class="breadcrumb-item active">Edit Category</li>
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
                        <h5>Edit Product Category</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.product-categories.update', $productCategory) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <!-- Parent Category -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Parent Category</label>
                                    <select class="form-select @error('parent_id') is-invalid @enderror" name="parent_id" id="parent_id">
                                        <option value="">Select Parent Category (Optional)</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('parent_id', $productCategory->parent_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->translate() ? $category->translate()->title : 'Untitled' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Leave empty to make this a main category</small>
                                    @error('parent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ $productCategory->status ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">Active</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Image -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Category Image</label>
                                    @if($productCategory->image)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $productCategory->image) }}" 
                                                 alt="Current image" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 200px; max-height: 200px;">
                                            <div class="mt-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image" value="1">
                                                    <label class="form-check-label text-danger" for="remove_image">
                                                        <i class="material-icons-outlined">delete</i> Remove current image
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <input type="file" 
                                           class="form-control @error('image') is-invalid @enderror" 
                                           name="image" 
                                           id="imageInput"
                                           accept="image/*">
                                    <small class="form-text text-muted">
                                        @if($productCategory->image)
                                            Upload a new image to replace the current one, or check the box above to remove it
                                        @else
                                            Upload an image for this category (optional)
                                        @endif
                                    </small>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    <!-- Image Preview -->
                                    <div id="imagePreview" class="mt-2" style="display: none;">
                                        <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                        <button type="button" class="btn btn-sm btn-outline-danger mt-1" onclick="removeImage()">
                                            <i class="material-icons-outlined">delete</i> Remove New Image
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Language Tabs -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label">Translations</label>
                                    <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                                        @foreach($languages as $index => $language)
                                        @php
                                            $translate = $productCategory->translates->where('language_id', $language->id)->first();
                                        @endphp
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link {{ $index === 0 ? 'active' : '' }} {{ $errors->has('translates.'.$index.'.*') ? 'border-danger' : '' }}" 
                                                    id="tab-{{ $language->lang_code }}" 
                                                    data-bs-toggle="tab" 
                                                    data-bs-target="#content-{{ $language->lang_code }}" 
                                                    type="button" 
                                                    role="tab" 
                                                    aria-controls="content-{{ $language->lang_code }}" 
                                                    aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                                <span class="badge badge-light-primary me-2">{{ $language->lang_code }}</span>
                                                {{ $language->title }}
                                                @if(old('translates.'.$index.'.title', $translate ? $translate->title : null))
                                                    <i class="material-icons-outlined text-success ms-1" style="font-size: 14px;">check_circle</i>
                                                @else
                                                    <i class="material-icons-outlined text-warning ms-1" style="font-size: 14px;">warning</i>
                                                @endif
                                                @if($errors->has('translates.'.$index.'.*'))
                                                    <i class="material-icons-outlined text-danger ms-1" style="font-size: 14px;">error</i>
                                                @endif
                                            </button>
                                        </li>
                                        @endforeach
                                    </ul>

                                    <!-- Tab Content -->
                                    <div class="tab-content" id="languageTabsContent">
                                        @foreach($languages as $index => $language)
                                        @php
                                            $translate = $productCategory->translates->where('language_id', $language->id)->first();
                                        @endphp
                                        <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                             id="content-{{ $language->lang_code }}" 
                                             role="tabpanel" 
                                             aria-labelledby="tab-{{ $language->lang_code }}">
                                            
                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">
                                                        Title <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" 
                                                           class="form-control @error('translates.'.$index.'.title') is-invalid @enderror" 
                                                           name="translates[{{ $index }}][title]" 
                                                           value="{{ old('translates.'.$index.'.title', $translate ? $translate->title : '') }}"
                                                           placeholder="Enter title for {{ $language->title }}">
                                                    @error('translates.'.$index.'.title')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <label class="form-label">
                                                        Slug <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="text" 
                                                               class="form-control @error('translates.'.$index.'.slug') is-invalid @enderror slug-input" 
                                                               name="translates[{{ $index }}][slug]" 
                                                               value="{{ old('translates.'.$index.'.slug', $translate ? $translate->slug : '') }}"
                                                               placeholder="Enter slug for {{ $language->title }}"
                                                               readonly
                                                               required>
                                                        <button type="button" 
                                                                class="btn btn-outline-secondary edit-slug-btn" 
                                                                onclick="toggleSlugEdit(this, 'translates[{{ $index }}][slug]')">
                                                            <i class="material-icons-outlined">edit</i>
                                                        </button>
                                                    </div>
                                                    @error('translates.'.$index.'.slug')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <label class="form-label">SEO Title</label>
                                                    <input type="text" 
                                                           class="form-control @error('translates.'.$index.'.seo_title') is-invalid @enderror" 
                                                           name="translates[{{ $index }}][seo_title]" 
                                                           value="{{ old('translates.'.$index.'.seo_title', $translate ? $translate->seo_title : '') }}"
                                                           placeholder="Enter SEO title for {{ $language->title }}">
                                                    @error('translates.'.$index.'.seo_title')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <label class="form-label">SEO Keywords</label>
                                                    <textarea class="form-control @error('translates.'.$index.'.seo_keywords') is-invalid @enderror" 
                                                              name="translates[{{ $index }}][seo_keywords]" 
                                                              rows="3"
                                                              placeholder="Enter SEO keywords for {{ $language->title }}">{{ old('translates.'.$index.'.seo_keywords', $translate ? $translate->seo_keywords : '') }}</textarea>
                                                    @error('translates.'.$index.'.seo_keywords')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <label class="form-label">SEO Description</label>
                                                    <textarea class="form-control @error('translates.'.$index.'.seo_description') is-invalid @enderror" 
                                                              name="translates[{{ $index }}][seo_description]" 
                                                              rows="3"
                                                              placeholder="Enter SEO description for {{ $language->title }}">{{ old('translates.'.$index.'.seo_description', $translate ? $translate->seo_description : '') }}</textarea>
                                                    @error('translates.'.$index.'.seo_description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <label class="form-label">Image Alt Text</label>
                                                    <input type="text" 
                                                           class="form-control @error('translates.'.$index.'.image_alt_text') is-invalid @enderror" 
                                                           name="translates[{{ $index }}][image_alt_text]" 
                                                           value="{{ old('translates.'.$index.'.image_alt_text', $translate ? $translate->image_alt_text : '') }}"
                                                           placeholder="Enter alt text for image in {{ $language->title }}">
                                                    @error('translates.'.$index.'.image_alt_text')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <input type="hidden" name="translates[{{ $index }}][language_id]" value="{{ $language->id }}">
                                        </div>
                                        @endforeach
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
                                        <a href="{{ route('admin.product-categories.index') }}" class="btn btn-secondary">
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
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const removeImageCheckbox = document.getElementById('remove_image');

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
            
            // Uncheck remove image checkbox when new image is selected
            if (removeImageCheckbox) {
                removeImageCheckbox.checked = false;
            }
        } else {
            imagePreview.style.display = 'none';
        }
    });

    // Handle remove image checkbox
    if (removeImageCheckbox) {
        removeImageCheckbox.addEventListener('change', function() {
            if (this.checked) {
                // Disable file input when remove is checked
                imageInput.disabled = true;
                imageInput.value = '';
                imagePreview.style.display = 'none';
            } else {
                // Enable file input when remove is unchecked
                imageInput.disabled = false;
            }
        });
    }

    // Lock all slug inputs by default
    document.querySelectorAll('input.slug-input').forEach(function(input) {
        input.readOnly = true;
        input.classList.add('disabled-input-class');
    });

    // Add visual feedback for active tab
    document.querySelectorAll('#languageTabs .nav-link').forEach(function(tab) {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs
            document.querySelectorAll('#languageTabs .nav-link').forEach(t => t.classList.remove('active'));
            // Add active class to clicked tab
            this.classList.add('active');
        });
    });

    // Auto-switch to first tab with errors
    @if($errors->any())
        const errorTabs = document.querySelectorAll('#languageTabs .nav-link.border-danger');
        if (errorTabs.length > 0) {
            // Switch to first tab with errors
            const firstErrorTab = errorTabs[0];
            const tabId = firstErrorTab.getAttribute('data-bs-target');
            
            // Remove active from all tabs and panes
            document.querySelectorAll('#languageTabs .nav-link').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('show', 'active'));
            
            // Activate the error tab
            firstErrorTab.classList.add('active');
            document.querySelector(tabId).classList.add('show', 'active');
            
            // Scroll to the error tab
            firstErrorTab.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    @endif
});

// Remove image function
function removeImage() {
    document.getElementById('imageInput').value = '';
    document.getElementById('imagePreview').style.display = 'none';
}

// Global function to toggle slug edit
window.toggleSlugEdit = function(button, inputName) {
    const input = document.querySelector(`input[name="${inputName}"]`);
    if (!input) return;
    
    if (input.readOnly) {
        // Enable editing
        input.readOnly = false;
        input.classList.remove('disabled-input-class');
        button.classList.remove('btn-secondary');
        button.classList.add('btn-outline-warning');
        button.innerHTML = '<i class="material-icons-outlined">lock</i>';
    } else {
        // Disable editing
        input.readOnly = true;
        input.classList.add('disabled-input-class');
        button.classList.remove('btn-outline-warning');
        button.classList.add('btn-secondary');
        button.innerHTML = '<i class="material-icons-outlined">edit</i>';
    }
};
</script>

<style>
.disabled-input-class {
    background-color: #e9ecef !important;
    color: #6c757d !important;
    cursor: not-allowed !important;
    pointer-events: none;
}
</style>
@endpush

@include('admin.partials.slug-generator') 
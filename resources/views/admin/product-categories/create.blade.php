@extends('admin.layout.app')

@section('title', 'Add Product Category')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Add Product Category</h3>
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
                        <li class="breadcrumb-item active">Add Category</li>
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
                        <h5>Add New Product Category</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.product-categories.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Parent Category -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Parent Category</label>
                                    <select class="form-select @error('parent_id') is-invalid @enderror" name="parent_id" id="parent_id">
                                        <option value="">Select Parent Category (Optional)</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ (old('parent_id', request('parent_id')) == $category->id) ? 'selected' : '' }}>
                                                {{ $category->translate() ? $category->translate()->title : 'Untitled' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Leave empty to create a main category</small>
                                    @error('parent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" checked>
                                        <label class="form-check-label" for="status">Active</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Image -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Category Image</label>
                                    <input type="file" 
                                           class="form-control @error('image') is-invalid @enderror" 
                                           name="image" 
                                           id="imageInput"
                                           accept="image/*">
                                    <small class="form-text text-muted">Upload an image for this category (optional)</small>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    <!-- Image Preview -->
                                    <div id="imagePreview" class="mt-2" style="display: none;">
                                        <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                        <button type="button" class="btn btn-sm btn-outline-danger mt-1" onclick="removeImage()">
                                            <i class="material-icons-outlined">delete</i> Remove Image
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
                                            $translate = null;
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
                                                @if(old('translates.'.$index.'.title'))
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
                                                           value="{{ old('translates.'.$index.'.title') }}"
                                                           placeholder="Enter title for {{ $language->title }}">
                                                    @error('translates.'.$index.'.title')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <label class="form-label">
                                                        Slug <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" 
                                                           class="form-control @error('translates.'.$index.'.slug') is-invalid @enderror slug-input" 
                                                           name="translates[{{ $index }}][slug]" 
                                                           value="{{ old('translates.'.$index.'.slug') }}"
                                                           placeholder="Enter slug for {{ $language->title }}"
                                                           data-language="{{ $language->lang_code }}">
                                                    @error('translates.'.$index.'.slug')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <label class="form-label">SEO Title</label>
                                                    <input type="text" 
                                                           class="form-control @error('translates.'.$index.'.seo_title') is-invalid @enderror" 
                                                           name="translates[{{ $index }}][seo_title]" 
                                                           value="{{ old('translates.'.$index.'.seo_title') }}"
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
                                                              placeholder="Enter SEO keywords for {{ $language->title }}">{{ old('translates.'.$index.'.seo_keywords') }}</textarea>
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
                                                              placeholder="Enter SEO description for {{ $language->title }}">{{ old('translates.'.$index.'.seo_description') }}</textarea>
                                                    @error('translates.'.$index.'.seo_description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <label class="form-label">SEO Text</label>
                                                    <textarea class="form-control ckeditor @error('translates.'.$index.'.seo_text') is-invalid @enderror" 
                                                              name="translates[{{ $index }}][seo_text]" 
                                                              id="seo-text-{{ $language->lang_code }}"
                                                              rows="8"
                                                              placeholder="Enter detailed SEO text for {{ $language->title }}">{{ old('translates.'.$index.'.seo_text') }}</textarea>
                                                    @error('translates.'.$index.'.seo_text')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="form-text text-muted">
                                                        Detailed SEO content with rich text formatting.
                                                    </small>
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <label class="form-label">Image Alt Text</label>
                                                    <input type="text" 
                                                           class="form-control @error('translates.'.$index.'.image_alt_text') is-invalid @enderror" 
                                                           name="translates[{{ $index }}][image_alt_text]" 
                                                           value="{{ old('translates.'.$index.'.image_alt_text') }}"
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
<script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize CKEditor for SEO text fields
    document.querySelectorAll('.ckeditor').forEach(function(textarea) {
        ClassicEditor
            .create(textarea, {
                toolbar: {
                    items: [
                        'undo', 'redo',
                        '|', 'heading',
                        '|', 'bold', 'italic', 'underline',
                        '|', 'fontColor', 'fontBackgroundColor',
                        '|', 'link', 'blockQuote', 'insertTable',
                        '|', 'bulletedList', 'numberedList',
                        '|', 'outdent', 'indent',
                        '|', 'alignment',
                        '|', 'removeFormat'
                    ]
                },
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                        { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                        { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                        { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
                    ]
                },
                language: 'en',
                placeholder: 'Enter detailed SEO content...'
            })
            .then(editor => {
                // Store editor reference
                textarea._ckeditor = editor;
            })
            .catch(error => {
                console.error(error);
            });
    });
    // Image preview functionality
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.style.display = 'none';
        }
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
</script>

<style>
/* CKEditor Styles */
.ck-editor__editable {
    min-height: 200px;
    max-height: 400px;
    overflow-y: auto;
}

.ck.ck-editor__main > .ck-editor__editable {
    background-color: #fff;
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
}

.ck.ck-editor__main > .ck-editor__editable:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.ck.ck-toolbar {
    border: 1px solid #ced4da;
    border-bottom: none;
    border-radius: 0.375rem 0.375rem 0 0;
    background: #f8f9fa;
}

.ck.ck-editor {
    margin-bottom: 1rem;
}
</style>
@endpush

@include('admin.partials.slug-generator') 
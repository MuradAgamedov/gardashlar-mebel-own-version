@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Add New Campaign') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.campaigns.index') }}">{{ __('Campaigns') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Add New') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container-fluid">
        <!-- Error Messages -->
        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="material-icons-outlined me-2">error</i>
                <div>
                    <h6 class="alert-heading mb-1">Please fix the following errors:</h6>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <form action="{{ route('admin.campaigns.store') }}" method="POST" enctype="multipart/form-data" id="companyForm" novalidate>
            @csrf
            
            <div class="row">
                <!-- Main Form Card -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="material-icons-outlined me-2">business</i>
                                    {{ __('Campaign Information') }}
                                </h5>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="status" value="0">
                                    <input class="form-check-input" type="checkbox" name="status" value="1" 
                                           id="status" {{ old('status', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status">
                                        <span class="status-indicator" id="statusText">{{ __('Active') }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <!-- Language Tabs -->
                            <ul class="nav nav-tabs nav-fill mb-4" id="languageTabs" role="tablist">
                                @foreach($languages as $index => $language)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                                id="tab-{{ $language->id }}" 
                                                data-bs-toggle="tab" 
                                                data-bs-target="#content-{{ $language->id }}" 
                                                type="button" 
                                                role="tab"
                                                @if($errors->has("translates.{$language->id}.*"))
                                                    data-has-errors="true"
                                                @endif>
                                            <i class="material-icons-outlined me-1">language</i>
                                            {{ $language->lang_code }}
                                            @if($errors->has("translates.{$language->id}.*"))
                                                <i class="material-icons-outlined text-danger ms-1 error-icon" style="font-size: 16px;">error</i>
                                            @endif
                                        </button>
                                    </li>
                                @endforeach
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content" id="languageTabContent">
                                @foreach($languages as $index => $language)
                                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                         id="content-{{ $language->id }}" 
                                         role="tabpanel" 
                                         aria-labelledby="tab-{{ $language->id }}">
                                        
                                       <!-- Title Section -->
                                        <div class="form-section mb-4">
                                            <h6 class="section-title">
                                                <i class="material-icons-outlined me-2">title</i>
                                                {{ __('Basic Information') }}
                                            </h6>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-floating mb-3">
                                                        <input type="text" 
                                                            class="form-control @error("translates.{$language->id}.title") is-invalid @enderror" 
                                                            name="translates[{{ $language->id }}][title]" 
                                                            id="title-{{ $language->id }}"
                                                            value="{{ old("translates.{$language->id}.title") }}"
                                                            placeholder="{{ __('Enter campaign title') }}"
                                                            @if($index === 0) required @else data-required="true" @endif>
                                                        <label for="title-{{ $language->id }}">{{ __('Campaign Title') }} *</label>
                                                        @error("translates.{$language->id}.title")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- 🔹 Slug Section -->
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-floating mb-3">
                                                        <input type="text" 
                                                            class="form-control @error("translates.{$language->id}.slug") is-invalid @enderror" 
                                                            name="translates[{{ $language->id }}][slug]" 
                                                            id="slug-{{ $language->id }}"
                                                            value="{{ old("translates.{$language->id}.slug") }}"
                                                            placeholder="{{ __('Enter campaign slug') }}"
                                                            @if($index === 0) required @else data-required="true" @endif>
                                                        <label for="slug-{{ $language->id }}">{{ __('Slug') }} *</label>
                                                        @error("translates.{$language->id}.slug")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                        <div class="form-text">{{ __('Slug must be unique (e.g., my-company)') }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Discount Section -->
                                        @if($index === 0)
                                        <div class="form-section mb-4">
                                            <h6 class="section-title">
                                                <i class="material-icons-outlined me-2">discount</i>
                                                {{ __('Discount Settings') }}
                                            </h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <input type="number" 
                                                            class="form-control @error('discount_percent') is-invalid @enderror" 
                                                            name="discount_percent" 
                                                            id="discount_percent"
                                                            value="{{ old('discount_percent') }}"
                                                            placeholder="{{ __('Enter discount percentage') }}"
                                                            step="0.01"
                                                            min="0"
                                                            max="100">
                                                        <label for="discount_percent">{{ __('Discount Percent') }} (%)</label>
                                                        @error('discount_percent')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                        <div class="form-text">{{ __('Enter discount percentage (0-100). Leave empty for no discount.') }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        <!-- Images Section -->
                                        <div class="form-section mb-4">
                                            <h6 class="section-title">
                                                <i class="material-icons-outlined me-2">image</i>
                                                {{ __('Images') }}
                                            </h6>
                                            
                                            <!-- Card Image -->
                                            <div class="row mb-4">
                                                <div class="col-md-6">
                                                    <div class="image-upload-card">
                                                        <div class="upload-header">
                                                            <label class="form-label">{{ __('Card Image') }} *</label>
                                                            <span class="upload-info">{{ __('Main display image for company cards') }}</span>
                                                        </div>
                                                        <input type="file" 
                                                               class="form-control @error("translates.{$language->id}.card_image") is-invalid @enderror" 
                                                               name="translates[{{ $language->id }}][card_image]" 
                                                               id="card-image-{{ $language->id }}"
                                                               accept="image/*" 
                                                               @if($index === 0) required @else data-required="true" @endif>
                                                        @error("translates.{$language->id}.card_image")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                        <div class="form-text">{{ __('Max 2MB • JPEG, PNG, JPG, GIF, WEBP') }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="image-preview-container">
                                                        <div id="card-image-preview-{{ $language->id }}" class="image-preview" style="display: none;">
                                                            <img id="card-preview-img-{{ $language->id }}" class="preview-image">
                                                            <button type="button" class="remove-image-btn" onclick="removeImage('card-image-{{ $language->id }}')">
                                                                <i class="material-icons-outlined">close</i>
                                                            </button>
                                                        </div>
                                                        <div id="card-no-image-{{ $language->id }}" class="no-image-placeholder">
                                                            <i class="material-icons-outlined">add_photo_alternate</i>
                                                            <p>{{ __('Card image preview') }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Inner Image -->
                                            <div class="row mb-4">
                                                <div class="col-md-6">
                                                    <div class="image-upload-card">
                                                        <div class="upload-header">
                                                            <label class="form-label">{{ __('Inner Image') }} *</label>
                                                            <span class="upload-info">{{ __('Detailed image for campaign page') }}</span>
                                                        </div>
                                                        <input type="file" 
                                                               class="form-control @error("translates.{$language->id}.inner_image") is-invalid @enderror" 
                                                               name="translates[{{ $language->id }}][inner_image]" 
                                                               id="inner-image-{{ $language->id }}"
                                                               accept="image/*" 
                                                               @if($index === 0) required @else data-required="true" @endif>
                                                        @error("translates.{$language->id}.inner_image")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                        <div class="form-text">{{ __('Max 2MB • JPEG, PNG, JPG, GIF, WEBP') }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="image-preview-container">
                                                        <div id="inner-image-preview-{{ $language->id }}" class="image-preview" style="display: none;">
                                                            <img id="inner-preview-img-{{ $language->id }}" class="preview-image">
                                                            <button type="button" class="remove-image-btn" onclick="removeImage('inner-image-{{ $language->id }}')">
                                                                <i class="material-icons-outlined">close</i>
                                                            </button>
                                                        </div>
                                                        <div id="inner-no-image-{{ $language->id }}" class="no-image-placeholder">
                                                            <i class="material-icons-outlined">add_photo_alternate</i>
                                                            <p>{{ __('Inner image preview') }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Alt Text Section -->
                                        <div class="form-section mb-4">
                                            <h6 class="section-title">
                                                <i class="material-icons-outlined me-2">accessibility</i>
                                                {{ __('Accessibility') }}
                                            </h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <input type="text" 
                                                               class="form-control @error("translates.{$language->id}.card_image_alt_text") is-invalid @enderror" 
                                                               name="translates[{{ $language->id }}][card_image_alt_text]" 
                                                               id="card-alt-{{ $language->id }}"
                                                               value="{{ old("translates.{$language->id}.card_image_alt_text") }}"
                                                               placeholder="{{ __('Enter card image alt text') }}">
                                                        <label for="card-alt-{{ $language->id }}">{{ __('Card Image Alt Text') }}</label>
                                                        @error("translates.{$language->id}.card_image_alt_text")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <input type="text" 
                                                               class="form-control @error("translates.{$language->id}.inner_image_alt_text") is-invalid @enderror" 
                                                               name="translates[{{ $language->id }}][inner_image_alt_text]" 
                                                               id="inner-alt-{{ $language->id }}"
                                                               value="{{ old("translates.{$language->id}.inner_image_alt_text") }}"
                                                               placeholder="{{ __('Enter inner image alt text') }}">
                                                        <label for="inner-alt-{{ $language->id }}">{{ __('Inner Image Alt Text') }}</label>
                                                        @error("translates.{$language->id}.inner_image_alt_text")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Description Section -->
                                        <div class="form-section">
                                            <h6 class="section-title">
                                                <i class="material-icons-outlined me-2">description</i>
                                                {{ __('Description') }}
                                            </h6>
                                            <div class="editor-container">
                                                <textarea class="form-control ckeditor @error("translates.{$language->id}.description") is-invalid @enderror" 
                                                          name="translates[{{ $language->id }}][description]" 
                                                          id="editor-{{ $language->id }}"
                                                          placeholder="{{ __('Enter detailed company description') }}"
                                                          @if($index === 0) required @else data-required="true" @endif>{{ old("translates.{$language->id}.description") }}</textarea>
                                                @error("translates.{$language->id}.description")
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- SEO Fields Section -->
                                        <div class="form-section">
                                            <h6 class="section-title">
                                                <i class="material-icons-outlined me-2">search</i>
                                                SEO Settings
                                            </h6>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-floating mb-3">
                                                        <input type="text" 
                                                               class="form-control @error("translates.{$language->id}.seo_title") is-invalid @enderror" 
                                                               name="translates[{{ $language->id }}][seo_title]" 
                                                               id="seo-title-{{ $language->id }}"
                                                               value="{{ old("translates.{$language->id}.seo_title") }}"
                                                               placeholder="{{ __('Enter SEO title') }}">
                                                        <label for="seo-title-{{ $language->id }}">{{ __('SEO Title') }}</label>
                                                        @error("translates.{$language->id}.seo_title")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                        <div class="form-text">Recommended length: 50-60 characters</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-floating mb-3">
                                                        <textarea class="form-control @error("translates.{$language->id}.seo_description") is-invalid @enderror" 
                                                                  name="translates[{{ $language->id }}][seo_description]" 
                                                                  id="seo-description-{{ $language->id }}"
                                                                  rows="3"
                                                                  placeholder="{{ __('Enter SEO description') }}">{{ old("translates.{$language->id}.seo_description") }}</textarea>
                                                        <label for="seo-description-{{ $language->id }}">{{ __('SEO Description') }}</label>
                                                        @error("translates.{$language->id}.seo_description")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                        <div class="form-text">Recommended length: 150-160 characters</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-floating mb-3">
                                                        <textarea class="form-control @error("translates.{$language->id}.seo_keywords") is-invalid @enderror" 
                                                                  name="translates[{{ $language->id }}][seo_keywords]" 
                                                                  id="seo-keywords-{{ $language->id }}"
                                                                  rows="2"
                                                                  placeholder="Enter SEO keywords (comma separated)">{{ old("translates.{$language->id}.seo_keywords") }}</textarea>
                                                        <label for="seo-keywords-{{ $language->id }}">{{ __('SEO Keywords') }}</label>
                                                        @error("translates.{$language->id}.seo_keywords")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                        <div class="form-text">Separate keywords with commas</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <input type="hidden" name="translates[{{ $language->id }}][language_id]" value="{{ $language->id }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Action Buttons -->
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary" title="{{ __('Create Campaign') }}">
                                    <i class="material-icons-outlined">save</i>
                                </button>
                                <a href="{{ route('admin.campaigns.index') }}" class="btn btn-outline-secondary" title="{{ __('Back to List') }}">
                                    <i class="material-icons-outlined">arrow_back</i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
@include('admin.partials.slug-generator') 
@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Campaign create script loaded');
    
    // Prevent browser validation
    const form = document.getElementById('companyForm');
    form.setAttribute('novalidate', 'novalidate');
    
    // Status toggle functionality
    const statusCheckbox = document.getElementById('status');
    const statusText = document.getElementById('statusText');
    const statusHidden = document.querySelector('input[type="hidden"][name="status"]');
    
    function updateStatusDisplay() {
        if (statusCheckbox.checked) {
            statusText.textContent = 'Active';
            statusText.style.color = '#28a745';
            statusHidden.disabled = true; // Disable hidden input when checkbox is checked
        } else {
            statusText.textContent = 'Inactive';
            statusText.style.color = '#6c757d';
            statusHidden.disabled = false; // Enable hidden input when checkbox is unchecked
        }
    }
    
    // Initialize status display
    updateStatusDisplay();
    
    // Add event listener for status change
    statusCheckbox.addEventListener('change', updateStatusDisplay);
    
    // Handle form submission to ensure proper status value
    form.addEventListener('submit', function(e) {
        if (statusCheckbox.checked) {
            statusHidden.disabled = true;
        } else {
            statusHidden.disabled = false;
            statusCheckbox.disabled = true; // Disable checkbox to ensure hidden value is sent
        }
    });
    
    // Handle required attributes for tabs
    document.querySelectorAll('input[data-required="true"], textarea[data-required="true"]').forEach(function(input) {
        input.removeAttribute('required');
    });
    
    // Tab switching logic
    document.querySelectorAll('.nav-link[data-bs-toggle="tab"]').forEach(function(tab) {
        tab.addEventListener('shown.bs.tab', function(e) {
            const targetId = e.target.getAttribute('data-bs-target');
            const targetPane = document.querySelector(targetId);
            
            // Remove required from all inputs
            document.querySelectorAll('.tab-pane input[required], .tab-pane textarea[required]').forEach(function(input) {
                input.removeAttribute('required');
                input.dataset.required = 'true';
            });
            
            // Add required to active tab inputs
            if (targetPane) {
                targetPane.querySelectorAll('input[data-required="true"], textarea[data-required="true"]').forEach(function(input) {
                    input.setAttribute('required', 'required');
                    input.removeAttribute('data-required');
                });
            }
        });
    });
    
    // Image preview functionality
    @foreach($languages as $language)
        setupImagePreview('card-image-{{ $language->id }}', 'card-image-preview-{{ $language->id }}', 'card-preview-img-{{ $language->id }}', 'card-no-image-{{ $language->id }}');
        setupImagePreview('inner-image-{{ $language->id }}', 'inner-image-preview-{{ $language->id }}', 'inner-preview-img-{{ $language->id }}', 'inner-no-image-{{ $language->id }}');
    @endforeach
    
    // Check if CKEditor is available
    if (typeof ClassicEditor === 'undefined') {
        console.error('CKEditor ClassicEditor is not available');
        return;
    }
    
    const ckeditorElements = document.querySelectorAll('.ckeditor');
    console.log('Found', ckeditorElements.length, 'CKEditor elements');
    
    // Initialize CKEditor for each textarea
    ckeditorElements.forEach(function(textarea) {
        // Hide the original textarea
        textarea.style.display = 'none';
        
        ClassicEditor
            .create(textarea, {
                toolbar: {
                    items: [
                        'undo', 'redo',
                        '|', 'heading',
                        '|', 'bold', 'italic', 'underline', 'strikethrough',
                        '|', 'fontColor', 'fontBackgroundColor',
                        '|', 'link', 'blockQuote', 'insertTable',
                        '|', 'bulletedList', 'numberedList',
                        '|', 'outdent', 'indent',
                        '|', 'alignment',
                        '|', 'removeFormat'
                    ]
                },
                language: 'en',
                placeholder: 'Enter description...'
            })
            .then(editor => {
                console.log('CKEditor initialized successfully for:', textarea.id);
                // Store editor reference on textarea for later use
                textarea._ckeditor = editor;
                textarea._focusEditor = () => editor.editing.view.focus();

                // Override the focus method to focus on the editor instead of textarea
                textarea.focus = function() {
                    editor.editing.view.focus();
                };

                // Override validation methods to prevent browser validation
                textarea.checkValidity = function() {
                    return true;
                };

                textarea.reportValidity = function() {
                    return true;
                };
            })
            .catch(error => {
                console.error('CKEditor initialization failed:', error);
                // Show textarea as fallback if CKEditor fails
                textarea.style.display = 'block';
                textarea.classList.add('form-control');
            });
    });
    
    // Override form validation to handle CKEditor fields
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Update all CKEditor instances before submission
            document.querySelectorAll('.ckeditor').forEach(function(textarea) {
                if (textarea._ckeditor) {
                    textarea._ckeditor.updateSourceElement();
                }
            });
            
            // Let the form submit normally - browser will handle validation
            console.log('Form submitted successfully');
        });
    }
});

function setupImagePreview(inputId, previewId, imgId, noImageId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    const img = document.getElementById(imgId);
    const noImage = document.getElementById(noImageId);
    
    input.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                preview.style.display = 'block';
                noImage.style.display = 'none';
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
            noImage.style.display = 'block';
        }
    });
}

function removeImage(inputId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(inputId.replace('image', 'image-preview'));
    const noImage = document.getElementById(inputId.replace('image', 'no-image'));
    
    input.value = '';
    preview.style.display = 'none';
    noImage.style.display = 'block';
}
</script>

<style>
/* Form Sections */
.form-section {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1.5rem;
    background: #f8f9fa;
}

.section-title {
    color: #495057;
    font-weight: 600;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #007bff;
    display: flex;
    align-items: center;
}

/* Status Toggle */
.status-indicator {
    font-weight: 600;
    transition: color 0.3s ease;
}

/* CKEditor Styles */
.ck-editor__editable {
    min-height: 200px;
    max-height: 400px;
    overflow-y: auto;
}

.ck.ck-editor {
    margin-bottom: 1rem;
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
}

/* Image Upload Cards */
.image-upload-card {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 1rem;
    background: #fff;
    transition: all 0.3s ease;
}

.image-upload-card:hover {
    border-color: #007bff;
    background: #f8f9ff;
}

.upload-header {
    margin-bottom: 0.5rem;
}

.upload-info {
    font-size: 0.875rem;
    color: #6c757d;
    display: block;
}

/* Image Preview */
.image-preview-container {
    position: relative;
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    min-height: 200px;
    background: #fff;
    overflow: hidden;
}

.image-preview {
    position: relative;
    width: 100%;
    height: 200px;
}

.preview-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.remove-image-btn {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    background: rgba(220, 53, 69, 0.8);
    color: white;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.remove-image-btn:hover {
    background: #dc3545;
    transform: scale(1.1);
}

.no-image-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 200px;
    color: #6c757d;
}

.no-image-placeholder i {
    font-size: 3rem;
    margin-bottom: 0.5rem;
}

/* Editor Container */
.editor-container {
    border: 1px solid #ced4da;
    border-radius: 8px;
    overflow: hidden;
}

/* Info Items */
.info-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.info-item i {
    color: #007bff;
    margin-right: 0.75rem;
    margin-top: 0.25rem;
}

.info-item strong {
    display: block;
    margin-bottom: 0.25rem;
}

.info-item p {
    margin: 0;
    font-size: 0.875rem;
    color: #6c757d;
}

/* Tab Styles */
.nav-tabs .nav-link {
    border: 1px solid transparent;
    border-radius: 0.375rem 0.375rem 0 0;
    margin-right: 0.25rem;
    transition: all 0.3s ease;
}

.nav-tabs .nav-link.active {
    border-color: #dee2e6 #dee2e6 #fff;
    background-color: #fff;
    color: #007bff;
}

.nav-tabs .nav-link:hover:not(.active) {
    border-color: #e9ecef #e9ecef #dee2e6;
    background-color: #f8f9fa;
}

.nav-tabs .nav-link[data-has-errors="true"] {
    border-color: #dc3545;
    color: #dc3545;
}

/* Validation Styles */
.form-control.is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.invalid-feedback {
    display: block !important;
}

/* CKEditor Styles */
.ck-editor__editable {
    min-height: 200px;
    max-height: 400px;
    overflow-y: auto;
}

.ck.ck-editor__main > .ck-editor__editable {
    background-color: #fff;
    border: none;
}

.ck.ck-editor__main > .ck-editor__editable:focus {
    border: none;
    box-shadow: none;
}

.ck.ck-toolbar {
    border: none;
    border-bottom: 1px solid #ced4da;
    background: #f8f9fa;
}

/* Responsive */
@media (max-width: 768px) {
    .form-section {
        padding: 1rem;
    }
    
    .image-preview-container {
        min-height: 150px;
    }
    
    .no-image-placeholder {
        height: 150px;
    }
    
    .no-image-placeholder i {
        font-size: 2rem;
    }
}

/* Animation */
.tab-pane {
    transition: opacity 0.15s linear;
}

.tab-pane.fade {
    opacity: 0;
}

.tab-pane.fade.show {
    opacity: 1;
}
</style>
@endpush
@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Edit Campaign') }}</h3>
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
                        <li class="breadcrumb-item active">{{ __('Edit') }}</li>
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

        <form action="{{ route('admin.campaigns.update', $company->id) }}" method="POST" enctype="multipart/form-data" id="companyForm" novalidate>
            @csrf
            @method('PUT')
            
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
                                           id="status" {{ old('status', $company->status) ? 'checked' : '' }}>
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
                                    @php
                                        $translate = $company->translates->where('language_id', $language->id)->first();
                                    @endphp
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
                                    @php
                                        $translate = $company->translates->where('language_id', $language->id)->first();
                                    @endphp
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
                                                            value="{{ old("translates.{$language->id}.title", $translate?->title) }}"
                                                            placeholder="{{ __('Enter campaign title') }}"
                                                            required>
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
                                                            value="{{ old("translates.{$language->id}.slug", $translate?->slug) }}"
                                                            placeholder="{{ __('Enter campaign slug') }}"
                                                            required>
                                                        <label for="slug-{{ $language->id }}">{{ __('Slug') }} *</label>
                                                        @error("translates.{$language->id}.slug")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                        <div class="form-text">{{ __('Slug must be unique (e.g., my-company)') }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

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
                                                            <label class="form-label">{{ __('Card Image') }}</label>
                                                            <span class="upload-info">{{ __('Main display image for company cards') }}</span>
                                                        </div>
                                                        <input type="file" 
                                                               class="form-control @error("translates.{$language->id}.card_image") is-invalid @enderror" 
                                                               name="translates[{{ $language->id }}][card_image]" 
                                                               id="card-image-{{ $language->id }}"
                                                               accept="image/*">
                                                        @error("translates.{$language->id}.card_image")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                        <div class="form-text">{{ __('Max 2MB • JPEG, PNG, JPG, GIF, WEBP') }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="image-preview-container">
                                                        @if($translate && $translate->card_image)
                                                            <div id="card-image-preview-{{ $language->id }}" class="image-preview">
                                                                <img src="{{ asset('storage/' . $translate->card_image) }}" class="preview-image">
                                                                <button type="button" class="remove-image-btn" onclick="removeImage('card-image-{{ $language->id }}')">
                                                                    <i class="material-icons-outlined">close</i>
                                                                </button>
                                                            </div>
                                                        @else
                                                            <div id="card-image-preview-{{ $language->id }}" class="image-preview" style="display: none;">
                                                                <img id="card-preview-img-{{ $language->id }}" class="preview-image">
                                                                <button type="button" class="remove-image-btn" onclick="removeImage('card-image-{{ $language->id }}')">
                                                                    <i class="material-icons-outlined">close</i>
                                                                </button>
                                                            </div>
                                                        @endif
                                                        <div id="card-no-image-{{ $language->id }}" class="no-image-placeholder" @if($translate && $translate->card_image) style="display: none;" @endif>
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
                                                            <label class="form-label">{{ __('Inner Image') }}</label>
                                                            <span class="upload-info">{{ __('Detailed image for campaign page') }}</span>
                                                        </div>
                                                        <input type="file" 
                                                               class="form-control @error("translates.{$language->id}.inner_image") is-invalid @enderror" 
                                                               name="translates[{{ $language->id }}][inner_image]" 
                                                               id="inner-image-{{ $language->id }}"
                                                               accept="image/*">
                                                        @error("translates.{$language->id}.inner_image")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                        <div class="form-text">{{ __('Max 2MB • JPEG, PNG, JPG, GIF, WEBP') }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="image-preview-container">
                                                        @if($translate && $translate->inner_image)
                                                            <div id="inner-image-preview-{{ $language->id }}" class="image-preview">
                                                                <img src="{{ asset('storage/' . $translate->inner_image) }}" class="preview-image">
                                                                <button type="button" class="remove-image-btn" onclick="removeImage('inner-image-{{ $language->id }}')">
                                                                    <i class="material-icons-outlined">close</i>
                                                                </button>
                                                            </div>
                                                        @else
                                                            <div id="inner-image-preview-{{ $language->id }}" class="image-preview" style="display: none;">
                                                                <img id="inner-preview-img-{{ $language->id }}" class="preview-image">
                                                                <button type="button" class="remove-image-btn" onclick="removeImage('inner-image-{{ $language->id }}')">
                                                                    <i class="material-icons-outlined">close</i>
                                                                </button>
                                                            </div>
                                                        @endif
                                                        <div id="inner-no-image-{{ $language->id }}" class="no-image-placeholder" @if($translate && $translate->inner_image) style="display: none;" @endif>
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
                                                               value="{{ old("translates.{$language->id}.card_image_alt_text", $translate?->card_image_alt_text) }}"
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
                                                               name="translates[{{ $language->id}}][inner_image_alt_text]" 
                                                               id="inner-alt-{{ $language->id }}"
                                                               value="{{ old("translates.{$language->id}.inner_image_alt_text", $translate?->inner_image_alt_text) }}"
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
                                        <div class="form-section mb-4">
                                            <h6 class="section-title">
                                                <i class="material-icons-outlined me-2">description</i>
                                                {{ __('Description') }}
                                            </h6>
                                            <div class="editor-container">
                                                <textarea class="form-control ckeditor @error("translates.{$language->id}.description") is-invalid @enderror" 
                                                          name="translates[{{ $language->id }}][description]" 
                                                          id="editor-{{ $language->id }}"
                                                          placeholder="{{ __('Enter detailed company description') }}"
                                                          required>{{ old("translates.{$language->id}.description", $translate?->description) }}</textarea>
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
                                                               value="{{ old("translates.{$language->id}.seo_title", $translate?->seo_title) }}"
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
                                                                  name="translates[{{ $language->id}}][seo_description]" 
                                                                  id="seo-description-{{ $language->id }}"
                                                                  rows="3"
                                                                  placeholder="{{ __('Enter SEO description') }}">{{ old("translates.{$language->id}.seo_description", $translate?->seo_description) }}</textarea>
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
                                                                  name="translates[{{ $language->id}}][seo_keywords]" 
                                                                  id="seo-keywords-{{ $language->id }}"
                                                                  rows="2"
                                                                  placeholder="Enter SEO keywords (comma separated)">{{ old("translates.{$language->id}.seo_keywords", $translate?->seo_keywords) }}</textarea>
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
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="material-icons-outlined me-2">info</i>
                                {{ __('Information') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="info-item">
                                <i class="material-icons-outlined">help_outline</i>
                                <div>
                                    <strong>{{ __('Required Fields') }}</strong>
                                    <p>{{ __('All fields marked with * are required for each language.') }}</p>
                                </div>
                            </div>
                            <div class="info-item">
                                <i class="material-icons-outlined">image</i>
                                <div>
                                    <strong>{{ __('Image Requirements') }}</strong>
                                    <p>{{ __('Upload high-quality images for better presentation.') }}</p>
                                </div>
                            </div>
                            <div class="info-item">
                                <i class="material-icons-outlined">description</i>
                                <div>
                                    <strong>{{ __('Description') }}</strong>
                                    <p>{{ __('Provide detailed information about the company.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="card">
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="material-icons-outlined me-2">save</i>
                                    {{ __('Update Campaign') }}
                                </button>
                                <a href="{{ route('admin.campaigns.index') }}" class="btn btn-outline-secondary">
                                    <i class="material-icons-outlined me-2">arrow_back</i>
                                    {{ __('Back to List') }}
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
    console.log('Campaign edit script loaded');
    
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
    
    // Form validation
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Update CKEditor instances
        document.querySelectorAll('.ckeditor').forEach(function(textarea) {
            if (textarea._ckeditor) {
                textarea._ckeditor.updateSourceElement();
            }
        });
        
        // Clear previous errors
        document.querySelectorAll('.is-invalid').forEach(function(element) {
            element.classList.remove('is-invalid');
        });
        document.querySelectorAll('.invalid-feedback').forEach(function(element) {
            element.remove();
        });
        
        let hasErrors = false;
        let firstErrorTab = null;
        
        // Validate all required fields
        document.querySelectorAll('input[required], textarea[required]').forEach(function(input) {
            let isValid = true;
            
            if (input.type === 'file') {
                // For edit form, files are not required
                isValid = true;
            } else if (input.tagName === 'TEXTAREA' && input.classList.contains('ckeditor')) {
                if (input._ckeditor) {
                    isValid = input._ckeditor.getData().trim() !== '';
                }
            } else {
                isValid = input.value.trim() !== '';
            }
            
            if (!isValid) {
                hasErrors = true;
                input.classList.add('is-invalid');
                
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                errorDiv.textContent = 'This field is required.';
                input.parentNode.appendChild(errorDiv);
                
                if (!firstErrorTab) {
                    const pane = input.closest('.tab-pane');
                    if (pane) {
                        firstErrorTab = pane.getAttribute('id');
                    }
                }
            }
        });
        
        // Update tab error indicators
        document.querySelectorAll('.nav-link').forEach(function(tab) {
            const targetId = tab.getAttribute('data-bs-target');
            const tabPane = document.querySelector(targetId);
            
            if (tabPane && tabPane.querySelector('.is-invalid')) {
                tab.style.borderColor = '#dc3545';
                tab.style.color = '#dc3545';
                if (!tab.querySelector('.error-icon')) {
                    const errorIcon = document.createElement('i');
                    errorIcon.className = 'material-icons-outlined text-danger ms-1 error-icon';
                    errorIcon.style.fontSize = '16px';
                    errorIcon.textContent = 'error';
                    tab.appendChild(errorIcon);
                }
            } else {
                tab.style.borderColor = '';
                tab.style.color = '';
                const errorIcon = tab.querySelector('.error-icon');
                if (errorIcon) {
                    errorIcon.remove();
                }
            }
        });
        
        if (hasErrors) {
            // Switch to first error tab
            if (firstErrorTab) {
                const tabTrigger = document.querySelector(`[data-bs-target="#${firstErrorTab}"]`);
                if (tabTrigger) {
                    const tab = new bootstrap.Tab(tabTrigger);
                    tab.show();
                }
            }
            return false;
        }
        
        // Submit form if no errors
        form.submit();
    });
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

.upload-header label {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.upload-info {
    font-size: 0.875rem;
    color: #6c757d;
}

/* Image Preview */
.image-preview-container {
    position: relative;
}

.image-preview {
    position: relative;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    padding: 0.5rem;
    background: #fff;
}

.preview-image {
    width: 100%;
    height: 120px;
    object-fit: cover;
    border-radius: 4px;
}

.remove-image-btn {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 12px;
}

.remove-image-btn:hover {
    background: #c82333;
}

.no-image-placeholder {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    color: #6c757d;
    background: #f8f9fa;
}

.no-image-placeholder i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.no-image-placeholder p {
    margin: 0;
    font-size: 0.875rem;
}

/* Info Items */
.info-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1.5rem;
}

.info-item:last-child {
    margin-bottom: 0;
}

.info-item i {
    color: #007bff;
    margin-right: 0.75rem;
    margin-top: 0.25rem;
    flex-shrink: 0;
}

.info-item strong {
    display: block;
    margin-bottom: 0.25rem;
    color: #495057;
}

.info-item p {
    margin: 0;
    color: #6c757d;
    font-size: 0.875rem;
}

/* Tab Styles */
.nav-tabs .nav-link {
    border: 1px solid transparent;
    border-radius: 0.375rem 0.375rem 0 0;
    margin-right: 0.25rem;
}

.nav-tabs .nav-link.active {
    border-color: #dee2e6 #dee2e6 #fff;
    background-color: #fff;
}

.nav-tabs .nav-link:hover {
    border-color: #e9ecef #e9ecef #dee2e6;
}
</style>
@endpush

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
            // Update all CKEditor instances before validation
            document.querySelectorAll('.ckeditor').forEach(function(textarea) {
                if (textarea._ckeditor) {
                    textarea._ckeditor.updateSourceElement();
                }
            });

            // Check if any required CKEditor fields are empty
            let hasEmptyFields = false;
            let firstEmptyField = null;

            document.querySelectorAll('.ckeditor').forEach(function(textarea) {
                if (textarea._ckeditor) {
                    const content = textarea._ckeditor.getData().trim();
                    if (!content) {
                        hasEmptyFields = true;
                        
                        // Store first empty field for focus
                        if (!firstEmptyField) {
                            firstEmptyField = textarea;
                        }
                        
                        // Add error styling
                        textarea.classList.add('is-invalid');
                        if (!textarea.nextElementSibling || !textarea.nextElementSibling.classList.contains('invalid-feedback')) {
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'invalid-feedback';
                            errorDiv.textContent = 'This field is required.';
                            textarea.parentNode.insertBefore(errorDiv, textarea.nextSibling);
                        }
                    } else {
                        textarea.classList.remove('is-invalid');
                        const errorDiv = textarea.nextElementSibling;
                        if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
                            errorDiv.remove();
                        }
                    }
                }
            });

            if (hasEmptyFields) {
                e.preventDefault();
                
                // Focus on first empty field and switch to its tab
                if (firstEmptyField && firstEmptyField._focusEditor) {
                    // Find the tab pane containing this textarea
                    const pane = firstEmptyField.closest('.tab-pane');
                    if (pane) {
                        const tabId = pane.getAttribute('id');
                        const tabTrigger = document.querySelector(`[data-bs-target="#${tabId}"]`);
                        if (tabTrigger) {
                            // Switch to the tab first
                            const tab = new bootstrap.Tab(tabTrigger);
                            tab.show();
                            
                            // Focus on editor after tab is shown
                            setTimeout(() => {
                                firstEmptyField._focusEditor();
                            }, 100);
                        }
                    } else {
                        firstEmptyField._focusEditor();
                    }
                }
                
                return false;
            }
        });
    }
});
</script>

<style>
.invalid-feedback {
    display: block !important;
}
</style>
@endpush
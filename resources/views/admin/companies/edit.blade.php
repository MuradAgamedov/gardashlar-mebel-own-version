@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Edit Company</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.companies.index') }}">Companies</a>
                        </li>
                        <li class="breadcrumb-item active">Edit</li>
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

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Edit Company</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.companies.update', $company->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input type="hidden" name="status" value="0">
                                            <input class="form-check-input" type="checkbox" name="status" value="1" 
                                                   id="status" {{ old('status', $company->status) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="status">
                                                <span class="status-indicator" id="statusText">Active</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Language Tabs -->
                            <ul class="nav nav-tabs" id="languageTabs" role="tablist">
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
                                                    style="border-color: #dc3545; color: #dc3545;"
                                                @endif>
                                            {{ $language->lang_code }}
                                            @if($errors->has("translates.{$language->id}.*"))
                                                <i class="material-icons-outlined text-danger ms-1" style="font-size: 16px;">error</i>
                                            @endif
                                        </button>
                                    </li>
                                @endforeach
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content mt-3" id="languageTabContent">
                                @foreach($languages as $index => $language)
                                    @php
                                        $translate = $company->translates->where('language_id', $language->id)->first();
                                    @endphp
                                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                         id="content-{{ $language->id }}" 
                                         role="tabpanel" 
                                         aria-labelledby="tab-{{ $language->id }}">
                                        
                                         <div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Title *</label>
            <input type="text" 
                   class="form-control @error("translates.{$language->id}.title") is-invalid @enderror" 
                   name="translates[{{ $language->id }}][title]" 
                   value="{{ old("translates.{$language->id}.title", $translate?->title) }}"
                   placeholder="Enter title"
                   required>
            @error("translates.{$language->id}.title")
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- 🔹 Slug field -->
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Slug *</label>
            <input type="text" 
                   class="form-control @error("translates.{$language->id}.slug") is-invalid @enderror" 
                   name="translates[{{ $language->id }}][slug]" 
                   value="{{ old("translates.{$language->id}.slug", $translate?->slug) }}"
                   placeholder="Enter slug"
                   required>
            @error("translates.{$language->id}.slug")
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">Unique slug (e.g. <code>my-company</code>)</div>
        </div>
    </div>
</div>


                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Card Image</label>
                                                    <input type="file" class="form-control @error("translates.{$language->id}.card_image") is-invalid @enderror" 
                                                           name="translates[{{ $language->id }}][card_image]" 
                                                           id="card-image-{{ $language->id }}"
                                                           accept="image/*">
                                                    @error("translates.{$language->id}.card_image")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-text">Maximum size: 2MB. Formats: JPEG, PNG, JPG, GIF, WEBP</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Card Image</label>
                                                    @if($translate && $translate->card_image)
                                                        <div class="border rounded p-3 text-center mb-2">
                                                            <img src="{{ asset('storage/' . $translate->card_image) }}" 
                                                                 alt="Current Card Image" 
                                                                 class="img-fluid" 
                                                                 style="max-height: 120px;">
                                                            <p class="mt-2 text-muted">Current card image</p>
                                                        </div>
                                                    @else
                                                        <div class="border rounded p-3 text-center text-muted mb-2">
                                                            <i class="material-icons-outlined" style="font-size: 2rem;">image</i>
                                                            <p class="mt-2">No card image</p>
                                                        </div>
                                                    @endif
                                                    <div id="card-image-preview-{{ $language->id }}" class="border rounded p-3 text-center" style="min-height: 150px; display: none;">
                                                        <img id="card-preview-img-{{ $language->id }}" class="img-fluid" style="max-height: 120px;">
                                                        <p class="mt-2 text-muted">New card image</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Card Image Alt Text</label>
                                                    <input type="text" class="form-control @error("translates.{$language->id}.card_image_alt_text") is-invalid @enderror" 
                                                           name="translates[{{ $language->id }}][card_image_alt_text]" 
                                                           value="{{ old("translates.{$language->id}.card_image_alt_text", $translate?->card_image_alt_text) }}"
                                                           placeholder="Enter card image alt text">
                                                    @error("translates.{$language->id}.card_image_alt_text")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Inner Image Alt Text</label>
                                                    <input type="text" class="form-control @error("translates.{$language->id}.inner_image_alt_text") is-invalid @enderror" 
                                                           name="translates[{{ $language->id }}][inner_image_alt_text]" 
                                                           value="{{ old("translates.{$language->id}.inner_image_alt_text", $translate?->inner_image_alt_text) }}"
                                                           placeholder="Enter inner image alt text">
                                                    @error("translates.{$language->id}.inner_image_alt_text")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Inner Image</label>
                                                    <input type="file" class="form-control @error("translates.{$language->id}.inner_image") is-invalid @enderror" 
                                                           name="translates[{{ $language->id }}][inner_image]" 
                                                           id="inner-image-{{ $language->id }}"
                                                           accept="image/*">
                                                    @error("translates.{$language->id}.inner_image")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-text">Maximum size: 2MB. Formats: JPEG, PNG, JPG, GIF, WEBP</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <!-- Empty column for layout -->
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Inner Image</label>
                                                    @if($translate && $translate->inner_image)
                                                        <div class="border rounded p-3 text-center mb-2">
                                                            <img src="{{ asset('storage/' . $translate->inner_image) }}" 
                                                                 alt="Current Inner Image" 
                                                                 class="img-fluid" 
                                                                 style="max-height: 120px;">
                                                            <p class="mt-2 text-muted">Current inner image</p>
                                                        </div>
                                                    @else
                                                        <div class="border rounded p-3 text-center text-muted mb-2">
                                                            <i class="material-icons-outlined" style="font-size: 2rem;">image</i>
                                                            <p class="mt-2">No inner image</p>
                                                        </div>
                                                    @endif
                                                    <div id="inner-image-preview-{{ $language->id }}" class="border rounded p-3 text-center" style="min-height: 150px; display: none;">
                                                        <img id="inner-preview-img-{{ $language->id }}" class="img-fluid" style="max-height: 120px;">
                                                        <p class="mt-2 text-muted">New inner image</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <!-- Empty column for layout -->
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Description *</label>
                                            <textarea class="form-control ckeditor @error("translates.{$language->id}.description") is-invalid @enderror" 
                                                      name="translates[{{ $language->id }}][description]" 
                                                      id="editor-{{ $language->id }}"
                                                      rows="8"
                                                      placeholder="Enter description">{{ old("translates.{$language->id}.description", $translate?->description) }}</textarea>
                                            @error("translates.{$language->id}.description")
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- SEO Fields Section -->
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h6 class="mb-3">
                                                    <i class="material-icons-outlined me-2">search</i>
                                                    SEO Settings
                                                </h6>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label">SEO Title</label>
                                                    <input type="text" 
                                                           class="form-control @error("translates.{$language->id}.seo_title") is-invalid @enderror" 
                                                           name="translates[{{ $language->id }}][seo_title]" 
                                                           value="{{ old("translates.{$language->id}.seo_title", $translate?->seo_title) }}"
                                                           placeholder="Enter SEO title">
                                                    @error("translates.{$language->id}.seo_title")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-text">Recommended length: 50-60 characters</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label">SEO Description</label>
                                                    <textarea class="form-control @error("translates.{$language->id}.seo_description") is-invalid @enderror" 
                                                              name="translates[{{ $language->id }}][seo_description]" 
                                                              rows="3"
                                                              placeholder="Enter SEO description">{{ old("translates.{$language->id}.seo_description", $translate?->seo_description) }}</textarea>
                                                    @error("translates.{$language->id}.seo_description")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-text">Recommended length: 150-160 characters</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label">SEO Keywords</label>
                                                    <textarea class="form-control @error("translates.{$language->id}.seo_keywords") is-invalid @enderror" 
                                                              name="translates[{{ $language->id }}][seo_keywords]" 
                                                              rows="2"
                                                              placeholder="Enter SEO keywords (comma separated)">{{ old("translates.{$language->id}.seo_keywords", $translate?->seo_keywords) }}</textarea>
                                                    @error("translates.{$language->id}.seo_keywords")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-text">Separate keywords with commas</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <input type="hidden" name="translates[{{ $language->id }}][language_id]" value="{{ $language->id }}">
                                    </div>
                                @endforeach
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">save</i>
                                            Save
                                        </button>
                                        <a href="{{ route('admin.companies.index') }}" class="btn btn-secondary">
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
<script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
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
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (statusCheckbox.checked) {
                statusHidden.disabled = true;
            } else {
                statusHidden.disabled = false;
                statusCheckbox.disabled = true; // Disable checkbox to ensure hidden value is sent
            }
        });
    }
    
    // Image preview for each language
    @foreach($languages as $language)
        // Card image preview
        const cardImageInput{{ $language->id }} = document.getElementById('card-image-{{ $language->id }}');
        const cardImagePreview{{ $language->id }} = document.getElementById('card-image-preview-{{ $language->id }}');
        const cardPreviewImg{{ $language->id }} = document.getElementById('card-preview-img-{{ $language->id }}');

        if (cardImageInput{{ $language->id }}) {
            cardImageInput{{ $language->id }}.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        cardPreviewImg{{ $language->id }}.src = e.target.result;
                        cardImagePreview{{ $language->id }}.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                } else {
                    cardImagePreview{{ $language->id }}.style.display = 'none';
                }
            });
        }

        // Inner image preview
        const innerImageInput{{ $language->id }} = document.getElementById('inner-image-{{ $language->id }}');
        const innerImagePreview{{ $language->id }} = document.getElementById('inner-image-preview-{{ $language->id }}');
        const innerPreviewImg{{ $language->id }} = document.getElementById('inner-preview-img-{{ $language->id }}');

        if (innerImageInput{{ $language->id }}) {
            innerImageInput{{ $language->id }}.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        innerPreviewImg{{ $language->id }}.src = e.target.result;
                        innerImagePreview{{ $language->id }}.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                } else {
                    innerImagePreview{{ $language->id }}.style.display = 'none';
                }
            });
        }
    @endforeach

    // Initialize CKEditor for each textarea
    document.querySelectorAll('.ckeditor').forEach(function(textarea) {
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
                console.error(error);
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

/* Status Toggle */
.status-indicator {
    font-weight: 600;
    transition: color 0.3s ease;
}
</style>
@endpush 
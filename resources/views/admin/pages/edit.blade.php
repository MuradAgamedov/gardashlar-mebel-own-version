@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Edit Page') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.pages.index') }}">{{ __('Pages') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Edit') }}</li>
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
                        <h5>Edit Page</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.pages.update', $page) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" {{ $page->status ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">
                                            Active
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Keyword</label>
                                    <input type="text" 
                                        class="form-control @error('keyword') is-invalid @enderror" 
                                        name="keyword" 
                                        value="{{ old('keyword', $page->keyword) }}" 
                                        placeholder="Enter page keyword"
                                        readonly>

                                    @error('keyword')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- OpenGraph Image Section -->
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h6 class="mb-3 text-primary">OpenGraph Image</h6>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label">OpenGraph Image</label>
                                            <input type="file" 
                                                   class="form-control @error('opengraph_image') is-invalid @enderror" 
                                                   name="opengraph_image" 
                                                   accept="image/*"
                                                   onchange="previewOpenGraphImage(this)">
                                            <small class="text-muted">Recommended size: 1200x630px</small>
                                            @error('opengraph_image')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label class="form-label">Current Image</label>
                                            <div id="opengraph-preview" class="border rounded p-3" style="min-height: 150px;">
                                                @if($page->opengraph_image)
                                                    <img src="{{ asset('storage/' . $page->opengraph_image) }}" 
                                                         alt="OpenGraph Image" 
                                                         class="img-fluid rounded"
                                                         style="max-height: 150px;">
                                                    <div class="mt-2 d-flex justify-content-between align-items-center">
                                                        <small class="text-muted">
                                                            Size: {{ $page->opengraph_image_width ?? 'N/A' }}x{{ $page->opengraph_image_height ?? 'N/A' }}px
                                                        </small>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-danger"
                                                                onclick="deleteOpenGraphImage({{ $page->id }})"
                                                                title="Delete OpenGraph Image">
                                                            <i class="material-icons-outlined">delete</i>
                                                        </button>
                                                    </div>
                                                @else
                                                    <div class="text-center text-muted py-4">
                                                        <i class="material-icons-outlined" style="font-size: 48px;">image</i>
                                                        <p class="mb-0">No OpenGraph image uploaded</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h6 class="mb-3">Page Translations</h6>
                                    
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
                                    
                                    <!-- Language Tabs -->
                                    <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                                        @foreach($languages as $index => $language)
                                        @php
                                            $translate = $page->translates->where('language_id', $language->id)->first();
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
                                                @if($translate)
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
                                            $translate = $page->translates->where('language_id', $language->id)->first();
                                        @endphp
                                        <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                             id="content-{{ $language->lang_code }}" 
                                             role="tabpanel" 
                                             aria-labelledby="tab-{{ $language->lang_code }}">
                                            
                                            <div class="card mt-3">
                                                <div class="card-header">
                                                    <h6 class="mb-0">{{ $language->title }} Content</h6>
                                                    @if($translate)
                                                        <small class="text-success">Translation exists</small>
                                                    @else
                                                        <small class="text-warning">No translation yet</small>
                                                    @endif
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Title *</label>
                                                            <input type="text" 
                                                                   class="form-control @error('translates.'.$index.'.title') is-invalid @enderror" 
                                                                   name="translates[{{ $index }}][title]" 
                                                                   value="{{ old('translates.'.$index.'.title', $translate->title ?? '') }}" 
                                                                   required>
                                                            <input type="hidden" name="translates[{{ $index }}][language_id]" value="{{ $language->id }}">
                                                            @error('translates.'.$index.'.title')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Slug *</label>
                                                            <div class="input-group">
                                                                <input type="text" 
                                                                       class="form-control @error('translates.'.$index.'.slug') is-invalid @enderror slug-input" 
                                                                       name="translates[{{ $index }}][slug]" 
                                                                       value="{{ old('translates.'.$index.'.slug', $translate->slug ?? '') }}" 
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
                                                        
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">SEO Title</label>
                                                            <input type="text" 
                                                                   class="form-control @error('translates.'.$index.'.seo_title') is-invalid @enderror" 
                                                                   name="translates[{{ $index }}][seo_title]" 
                                                                   value="{{ old('translates.'.$index.'.seo_title', $translate->seo_title ?? '') }}"
                                                                   placeholder="Enter SEO title">
                                                            @error('translates.'.$index.'.seo_title')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">SEO Keywords</label>
                                                            <textarea class="form-control @error('translates.'.$index.'.seo_keywords') is-invalid @enderror" 
                                                                      name="translates[{{ $index }}][seo_keywords]" 
                                                                      rows="3" 
                                                                      placeholder="Enter keywords separated by commas">{{ old('translates.'.$index.'.seo_keywords', $translate->seo_keywords ?? '') }}</textarea>
                                                            @error('translates.'.$index.'.seo_keywords')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">SEO Description</label>
                                                            <textarea class="form-control @error('translates.'.$index.'.seo_description') is-invalid @enderror" 
                                                                      name="translates[{{ $index }}][seo_description]" 
                                                                      rows="3" 
                                                                      placeholder="Enter SEO description">{{ old('translates.'.$index.'.seo_description', $translate->seo_description ?? '') }}</textarea>
                                                            @error('translates.'.$index.'.seo_description')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <hr class="my-4">
                                                        <h6 class="mb-3 text-primary">OpenGraph Settings</h6>
                                                        
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">OpenGraph Title</label>
                                                            <input type="text" 
                                                                   class="form-control @error('translates.'.$index.'.opengraph_title') is-invalid @enderror" 
                                                                   name="translates[{{ $index }}][opengraph_title]" 
                                                                   value="{{ old('translates.'.$index.'.opengraph_title', $translate->opengraph_title ?? '') }}"
                                                                   placeholder="Enter OpenGraph title">
                                                            @error('translates.'.$index.'.opengraph_title')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">OpenGraph Description</label>
                                                            <textarea class="form-control @error('translates.'.$index.'.opengraph_description') is-invalid @enderror" 
                                                                      name="translates[{{ $index }}][opengraph_description]" 
                                                                      rows="3" 
                                                                      placeholder="Enter OpenGraph description">{{ old('translates.'.$index.'.opengraph_description', $translate->opengraph_description ?? '') }}</textarea>
                                                            @error('translates.'.$index.'.opengraph_description')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">OpenGraph Image Alt Text</label>
                                                            <input type="text" 
                                                                   class="form-control @error('translates.'.$index.'.opengraph_image_alt_text') is-invalid @enderror" 
                                                                   name="translates[{{ $index }}][opengraph_image_alt_text]" 
                                                                   value="{{ old('translates.'.$index.'.opengraph_image_alt_text', $translate->opengraph_image_alt_text ?? '') }}"
                                                                   placeholder="Enter OpenGraph image alt text">
                                                            @error('translates.'.$index.'.opengraph_image_alt_text')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">save</i>
                                        </button>
                                        <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">
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
// Global function for toggling slug edit - must be defined before DOMContentLoaded
window.toggleSlugEdit = function(btn, slugInputName) {
    console.log('toggleSlugEdit called with:', slugInputName);
    const slugInput = document.querySelector('input[name="' + slugInputName + '"]');
    console.log('Found slug input:', slugInput);
    console.log('Current readOnly state:', slugInput ? slugInput.readOnly : 'input not found');
    
    if (slugInput) {
        // Toggle readonly state
        if (slugInput.readOnly) {
            // Enable editing - show lock button (warning color)
            slugInput.readOnly = false;
            slugInput.classList.remove('disabled-input-class');
            slugInput.focus();
            btn.innerHTML = '<i class="material-icons-outlined">lock</i>';
            btn.classList.remove('btn-outline-secondary', 'btn-secondary', 'btn-outline-warning');
            btn.classList.add('btn-outline-warning');
            console.log('Slug input is now editable');
        } else {
            // Disable editing - show edit button (gray color)
            slugInput.readOnly = true;
            slugInput.classList.add('disabled-input-class');
            btn.innerHTML = '<i class="material-icons-outlined">edit</i>';
            btn.classList.remove('btn-outline-warning', 'btn-outline-secondary');
            btn.classList.add('btn-secondary');
            console.log('Slug input is now readonly');
        }
    } else {
        console.error('Slug input not found for:', slugInputName);
    }
};

document.addEventListener('DOMContentLoaded', function() {
    // Lock all slug inputs by default
    document.querySelectorAll('input.slug-input').forEach(function(input) {
        input.readOnly = true;
        input.classList.add('disabled-input-class');
        
        // Disable all keyboard input when readonly
        input.addEventListener('keydown', function(e) {
            if (this.readOnly) {
                e.preventDefault();
                return false;
            }
        });
        
        input.addEventListener('keypress', function(e) {
            if (this.readOnly) {
                e.preventDefault();
                return false;
            }
        });
        
        input.addEventListener('input', function(e) {
            if (this.readOnly) {
                e.preventDefault();
                return false;
            }
        });
        
        input.addEventListener('paste', function(e) {
            if (this.readOnly) {
                e.preventDefault();
                return false;
            }
        });
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

    // Show translation status in tabs
    function updateTabStatus() {
        document.querySelectorAll('#languageTabs .nav-link').forEach(function(tab) {
            const tabId = tab.getAttribute('data-bs-target');
            const tabContent = document.querySelector(tabId);
            const titleInput = tabContent.querySelector('input[name*="[title]"]');
            const slugInput = tabContent.querySelector('input[name*="[slug]"]');
            
            if (titleInput.value && slugInput.value) {
                // Has content
                tab.querySelector('.text-success').style.display = 'inline';
                tab.querySelector('.text-warning').style.display = 'none';
            } else {
                // Missing content
                tab.querySelector('.text-success').style.display = 'none';
                tab.querySelector('.text-warning').style.display = 'inline';
            }
        });
    }

    // Update status when inputs change
    document.querySelectorAll('input[name*="[title]"], input[name*="[slug]"]').forEach(function(input) {
        input.addEventListener('input', updateTabStatus);
    });

    // Initial status update
    updateTabStatus();

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

// OpenGraph image preview function
function previewOpenGraphImage(input) {
    const preview = document.getElementById('opengraph-preview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.innerHTML = `
                <img src="${e.target.result}" 
                     alt="OpenGraph Image Preview" 
                     class="img-fluid rounded"
                     style="max-height: 150px;">
                <div class="mt-2">
                    <small class="text-success">New image selected</small>
                </div>
            `;
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Delete OpenGraph image function
function deleteOpenGraphImage(pageId) {
    if (!confirm('Are you sure you want to delete the OpenGraph image?')) {
        return;
    }
    
    fetch(`{{ route('admin.pages.delete-opengraph-image', ':pageId') }}`.replace(':pageId', pageId), {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update preview area
            document.getElementById('opengraph-preview').innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="material-icons-outlined" style="font-size: 48px;">image</i>
                    <p class="mb-0">No OpenGraph image uploaded</p>
                </div>
            `;
            
            // Show success message
            showAlert('success', data.message);
        } else {
            showAlert('danger', data.message || 'Error deleting image');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'Error deleting OpenGraph image');
    });
}

// Show alert function
function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    const container = document.querySelector('.container-fluid');
    container.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}
</script>

<style>
.nav-tabs .nav-link {
    border: 1px solid #dee2e6;
    border-bottom: none;
    border-radius: 0.375rem 0.375rem 0 0;
    margin-right: 2px;
    padding: 0.5rem 1rem;
    color: #6c757d;
    background-color: #f8f9fa;
    position: relative;
}

.nav-tabs .nav-link:hover {
    border-color: #e9ecef #e9ecef #dee2e6;
    background-color: #e9ecef;
}

.nav-tabs .nav-link.active {
    color: #495057;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
}

.nav-tabs .nav-link.border-danger {
    border-color: #dc3545 !important;
    color: #dc3545;
}

.nav-tabs .nav-link.border-danger.active {
    border-color: #dc3545 #dc3545 #fff !important;
    color: #dc3545;
}

.tab-content {
    border: 1px solid #dee2e6;
    border-top: none;
    border-radius: 0 0 0.375rem 0.375rem;
    padding: 1rem;
    background-color: #fff;
}

.tab-pane {
    min-height: 400px;
}

.nav-tabs .nav-link i {
    vertical-align: middle;
}

.invalid-feedback.d-block {
    display: block !important;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}

.disabled-input-class {
    background-color: #e9ecef !important;
    color: #6c757d !important;
    cursor: not-allowed !important;
    pointer-events: none !important;
    user-select: none !important;
    -webkit-user-select: none !important;
    -moz-user-select: none !important;
    -ms-user-select: none !important;
}
</style>
@endpush

@include('admin.partials.slug-generator') 
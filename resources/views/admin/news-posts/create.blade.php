@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Create News Post</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.news-posts.index') }}">News Posts</a>
                        </li>
                        <li class="breadcrumb-item active">Create</li>
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
                        <h5>News Post Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.news-posts.store') }}" enctype="multipart/form-data" novalidate>
                            @csrf
                            
                            <!-- Basic Information -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="news_category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-select" id="news_category_id" name="news_category_id" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            @php
                                                $mainLanguageId = \App\Models\Language::getMainLanguage()->id;
                                                $categoryTranslation = $category->translates->firstWhere('language_id', $mainLanguageId)
                                                    ?? $category->translates->first();
                                            @endphp
                                            <option value="{{ $category->id }}" {{ old('news_category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $categoryTranslation ? $categoryTranslation->title : 'No title' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="published_at" class="form-label">Published At</label>
                                    <input type="datetime-local" 
                                           class="form-control" 
                                           id="published_at" 
                                           name="published_at"
                                           value="{{ old('published_at') }}">
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="status" id="status" value="1" {{ old('status', '1') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">
                                            Active Status
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Images -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="card_image" class="form-label">Card Image</label>
                                    <input type="file" 
                                           class="form-control" 
                                           id="card_image" 
                                           name="card_image" 
                                           accept="image/*">
                                    <small class="form-text text-muted">Recommended size: 400x300px, Max: 2MB</small>
                                    <div class="mt-2">
                                        <img id="card_image_preview" src="" alt="Card Image Preview" 
                                             class="img-thumbnail" style="max-width: 200px; display: none;">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="inner_banner_image" class="form-label">Inner Banner Image</label>
                                    <input type="file" 
                                           class="form-control" 
                                           id="inner_banner_image" 
                                           name="inner_banner_image" 
                                           accept="image/*">
                                    <small class="form-text text-muted">Recommended size: 1200x400px, Max: 2MB</small>
                                    <div class="mt-2">
                                        <img id="inner_banner_image_preview" src="" alt="Inner Banner Image Preview" 
                                             class="img-thumbnail" style="max-width: 200px; display: none;">
                                    </div>
                                </div>
                            </div>

                            <!-- Language Tabs -->
                            <ul class="nav nav-tabs mb-3" id="languageTabs" role="tablist">
                                @foreach($languages as $index => $language)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                                id="lang-{{ $language->id }}-tab" 
                                                data-bs-toggle="tab" 
                                                data-bs-target="#lang-{{ $language->id }}" 
                                                type="button" 
                                                role="tab">
                                            {{ $language->name }}
                                                <span class="badge bg-secondary ms-1">{{ strtoupper($language->lang_code) }}</span>
                                            @if($language->is_default)
                                                <span class="badge bg-primary ms-1">Default</span>
                                            @endif
                                        </button>
                                    </li>
                                @endforeach
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content" id="languageTabContent">
                                @foreach($languages as $index => $language)
                                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                         id="lang-{{ $language->id }}" 
                                         role="tabpanel">
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="title_{{ $language->id }}" class="form-label">
                                                        Title <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" 
                                                           class="form-control" 
                                                           id="title_{{ $language->id }}" 
                                                           name="translations[{{ $language->id }}][title]" 
                                                           value="{{ old("translations.{$language->id}.title") }}" 
                                                           required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="slug_{{ $language->id }}" class="form-label">
                                                        Slug <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" 
                                                           class="form-control" 
                                                           id="slug_{{ $language->id }}" 
                                                           name="translations[{{ $language->id }}][slug]" 
                                                           value="{{ old("translations.{$language->id}.slug") }}" 
                                                           required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="description_{{ $language->id }}" class="form-label">
                                                        Description <span class="text-danger">*</span>
                                                    </label>
                                                    <textarea class="form-control ckeditor" 
                                                              id="description_{{ $language->id }}" 
                                                              name="translations[{{ $language->id }}][description]" 
                                                              rows="10" required>{{ old("translations.{$language->id}.description") }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="translations[{{ $language->id }}][card_image_alt_text]" class="form-label">
                                                        Card Image Alt Text ({{ strtoupper($language->lang_code) }})
                                                    </label>
                                                    <input type="text" 
                                                           class="form-control" 
                                                           name="translations[{{ $language->id }}][card_image_alt_text]" 
                                                           value="{{ old("translations.{$language->id}.card_image_alt_text") }}">
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="translations[{{ $language->id }}][inner_banner_alt_text]" class="form-label">
                                                        Inner Banner Alt Text ({{ strtoupper($language->lang_code) }})
                                                    </label>
                                                    <input type="text" 
                                                           class="form-control" 
                                                           name="translations[{{ $language->id }}][inner_banner_alt_text]" 
                                                           value="{{ old("translations.{$language->id}.inner_banner_alt_text") }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="translations[{{ $language->id }}][card_description]" class="form-label">
                                                        Card Description ({{ strtoupper($language->lang_code) }})
                                                    </label>
                                                    <textarea class="form-control" 
                                                              name="translations[{{ $language->id }}][card_description]" 
                                                              rows="3">{{ old("translations.{$language->id}.card_description") }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- SEO Section -->
                                        <div class="row mt-4">
                                            <div class="col-12">
                                                <h6 class="mb-3">SEO Settings ({{ strtoupper($language->lang_code) }})</h6>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="seo_title_{{ $language->id }}" class="form-label">SEO Title</label>
                                                    <input type="text" 
                                                           class="form-control" 
                                                           id="seo_title_{{ $language->id }}" 
                                                           name="translations[{{ $language->id }}][seo_title]" 
                                                           value="{{ old("translations.{$language->id}.seo_title") }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="seo_keywords_{{ $language->id }}" class="form-label">SEO Keywords</label>
                                                    <textarea class="form-control" 
                                                              id="seo_keywords_{{ $language->id }}" 
                                                              name="translations[{{ $language->id }}][seo_keywords]" 
                                                              rows="3">{{ old("translations.{$language->id}.seo_keywords") }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="seo_description_{{ $language->id }}" class="form-label">SEO Description</label>
                                                    <textarea class="form-control" 
                                                              id="seo_description_{{ $language->id }}" 
                                                              name="translations[{{ $language->id }}][seo_description]" 
                                                              rows="4">{{ old("translations.{$language->id}.seo_description") }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">save</i>
                                            Create News Post
                                        </button>
                                        <a href="{{ route('admin.news-posts.index') }}" class="btn btn-secondary">
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
<script src="https://cdn.ckeditor.com/ckeditor5/27.1.0/classic/ckeditor.js"></script>
<script src="{{ asset('js/slug-generator.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let editors = {};
    
    // Initialize CKEditor for each description field
    @foreach($languages as $language)
        ClassicEditor
            .create(document.querySelector('#description_{{ $language->id }}'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'blockQuote', 'insertTable', 'undo', 'redo'],
                language: '{{ $language->lang_code }}',
                initialData: @js(old("translations.{$language->id}.description"))
            })
            .then(editor => {
                editors['{{ $language->id }}'] = editor;
            })
            .catch(error => {
                console.error(error);
            });
    @endforeach

    // Image preview functionality
    function setupImagePreview(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        
        if (input && preview) {
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.style.display = 'none';
                }
            });
        }
    }

    // Setup image previews
    setupImagePreview('card_image', 'card_image_preview');
    setupImagePreview('inner_banner_image', 'inner_banner_image_preview');

    // Form validation before submission
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            let firstErrorTab = null;
            let firstErrorField = null;
            
            // Validate CKEditor fields
            @foreach($languages as $language)
                const editor{{ $language->id }} = editors['{{ $language->id }}'];
                if (editor{{ $language->id }}) {
                    const content = editor{{ $language->id }}.getData().trim();
                    const textarea = document.getElementById('description_{{ $language->id }}');
                    const editorElement = editor{{ $language->id }}.ui.view.editable.element;
                    
                    if (!content) {
                        isValid = false;
                        textarea.classList.add('is-invalid');
                        editorElement.classList.add('is-invalid');
                        
                        // Switch to the first tab with error
                        if (!firstErrorTab) {
                            firstErrorTab = 'lang-{{ $language->id }}-tab';
                            firstErrorField = editorElement;
                        }
                    } else {
                        textarea.classList.remove('is-invalid');
                        editorElement.classList.remove('is-invalid');
                        // Update textarea value for form submission
                        textarea.value = content;
                    }
                }
            @endforeach
            
            // Validate required title and slug fields
            @foreach($languages as $language)
                const titleInput{{ $language->id }} = document.getElementById('title_{{ $language->id }}');
                const slugInput{{ $language->id }} = document.getElementById('slug_{{ $language->id }}');
                
                if (titleInput{{ $language->id }} && !titleInput{{ $language->id }}.value.trim()) {
                    isValid = false;
                    titleInput{{ $language->id }}.classList.add('is-invalid');
                    if (!firstErrorTab) {
                        firstErrorTab = 'lang-{{ $language->id }}-tab';
                        firstErrorField = titleInput{{ $language->id }};
                    }
                } else if (titleInput{{ $language->id }}) {
                    titleInput{{ $language->id }}.classList.remove('is-invalid');
                }
                
                if (slugInput{{ $language->id }} && !slugInput{{ $language->id }}.value.trim()) {
                    isValid = false;
                    slugInput{{ $language->id }}.classList.add('is-invalid');
                    if (!firstErrorTab) {
                        firstErrorTab = 'lang-{{ $language->id }}-tab';
                        firstErrorField = slugInput{{ $language->id }};
                    }
                } else if (slugInput{{ $language->id }}) {
                    slugInput{{ $language->id }}.classList.remove('is-invalid');
                }
            @endforeach
            
            // Switch to first tab with error and prevent submission if invalid
            if (!isValid) {
                e.preventDefault();
                if (firstErrorTab) {
                    const errorTab = document.getElementById(firstErrorTab);
                    if (errorTab) {
                        const tab = new bootstrap.Tab(errorTab);
                        tab.show();
                    }
                    
                    // Focus on the first error field after tab switch
                    setTimeout(() => {
                        if (firstErrorField) {
                            firstErrorField.focus();
                        }
                    }, 100);
                }
                alert('Please fill in all required fields marked with *');
            }
        });
    }
});
</script>

<style>
    .nav-tabs .nav-link {
        color: #6c757d;
    }
    .nav-tabs .nav-link.active {
        color: #495057;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
    }
    .img-thumbnail {
        border-radius: 4px;
    }
    
    /* CKEditor validation styles */
    .ck-editor__editable.is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }
    
    .ck-editor__editable.is-invalid:focus {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }
</style>
@endpush

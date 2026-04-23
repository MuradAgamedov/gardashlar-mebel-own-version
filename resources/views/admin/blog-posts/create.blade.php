@extends('admin.layout.app')

@section('title', 'Create Blog Post')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Create Blog Post</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.blog-posts.index') }}">Blog Posts</a>
                        </li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
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
                        <h5>Create New Blog Post</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.blog-posts.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                            @csrf
                            
                            <!-- Basic Information -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="blog_category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-select @error('blog_category_id') is-invalid @enderror" id="blog_category_id" name="blog_category_id" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            @php
                                                $translation = $category->translates->first();
                                            @endphp
                                            <option value="{{ $category->id }}" {{ old('blog_category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $translation ? $translation->title : 'Category #' . $category->id }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('blog_category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="published_at" class="form-label">Published At</label>
                                    <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror" 
                                           id="published_at" name="published_at" value="{{ old('published_at') }}">
                                    @error('published_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Images -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="card_image" class="form-label">Card Image</label>
                                    <input type="file" class="form-control @error('card_image') is-invalid @enderror" 
                                           id="card_image" name="card_image" accept="image/*">
                                    <small class="form-text text-muted">Recommended size: 400x300px, Max: 2MB</small>
                                    @error('card_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="mt-2">
                                        <img id="card_image_preview" src="" alt="Card Image Preview" 
                                             class="img-thumbnail" style="max-width: 200px; display: none;">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="inner_banner_image" class="form-label">Inner Banner Image</label>
                                    <input type="file" class="form-control @error('inner_banner_image') is-invalid @enderror" 
                                           id="inner_banner_image" name="inner_banner_image" accept="image/*">
                                    <small class="form-text text-muted">Recommended size: 1200x400px, Max: 2MB</small>
                                    @error('inner_banner_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="mt-2">
                                        <img id="inner_banner_image_preview" src="" alt="Inner Banner Image Preview" 
                                             class="img-thumbnail" style="max-width: 200px; display: none;">
                                    </div>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ old('status', '1') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">Active</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Translations -->
                            <div class="mb-4">
                                <h6 class="mb-3">Translations</h6>
                                <div class="nav nav-tabs" id="translationTabs" role="tablist">
                                    @foreach($languages as $index => $language)
                                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                                id="tab-{{ $language->id }}" 
                                                data-bs-toggle="tab" 
                                                data-bs-target="#content-{{ $language->id }}" 
                                                type="button" 
                                                role="tab">
                                            {{ $language->name }} ({{ strtoupper($language->lang_code) }})
                                        </button>
                                    @endforeach
                                </div>
                                
                                <div class="tab-content" id="translationTabContent">
                                    @foreach($languages as $index => $language)
                                        <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                             id="content-{{ $language->id }}" 
                                             role="tabpanel">
                                            
                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <label for="translations[{{ $language->id }}][title]" class="form-label">
                                                        Title ({{ strtoupper($language->lang_code) }}) <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control @error("translations.{$language->id}.title") is-invalid @enderror" 
                                                           name="translations[{{ $language->id }}][title]" 
                                                           id="title_{{ $language->id }}"
                                                           value="{{ old("translations.{$language->id}.title") }}" required>
                                                    @error("translations.{$language->id}.title")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <label for="translations[{{ $language->id }}][slug]" class="form-label">
                                                        Slug ({{ strtoupper($language->lang_code) }}) <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control @error("translations.{$language->id}.slug") is-invalid @enderror" 
                                                           name="translations[{{ $language->id }}][slug]" 
                                                           id="slug_{{ $language->id }}"
                                                           value="{{ old("translations.{$language->id}.slug") }}" required>
                                                    @error("translations.{$language->id}.slug")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <label for="translations[{{ $language->id }}][description]" class="form-label">
                                                        Description ({{ strtoupper($language->lang_code) }}) <span class="text-danger">*</span>
                                                    </label>
                                                    <textarea class="form-control ckeditor @error("translations.{$language->id}.description") is-invalid @enderror" 
                                                              name="translations[{{ $language->id }}][description]" 
                                                              id="description_{{ $language->id }}">{{ old("translations.{$language->id}.description") }}</textarea>
                                                    @error("translations.{$language->id}.description")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <label for="translations[{{ $language->id }}][card_image_alt_text]" class="form-label">
                                                        Card Image Alt Text ({{ strtoupper($language->lang_code) }})
                                                    </label>
                                                    <input type="text" class="form-control @error("translations.{$language->id}.card_image_alt_text") is-invalid @enderror" 
                                                           name="translations[{{ $language->id }}][card_image_alt_text]" 
                                                           value="{{ old("translations.{$language->id}.card_image_alt_text") }}">
                                                    @error("translations.{$language->id}.card_image_alt_text")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <label for="translations[{{ $language->id }}][inner_banner_alt_text]" class="form-label">
                                                        Inner Banner Alt Text ({{ strtoupper($language->lang_code) }})
                                                    </label>
                                                    <input type="text" class="form-control @error("translations.{$language->id}.inner_banner_alt_text") is-invalid @enderror" 
                                                           name="translations[{{ $language->id }}][inner_banner_alt_text]" 
                                                           value="{{ old("translations.{$language->id}.inner_banner_alt_text") }}">
                                                    @error("translations.{$language->id}.inner_banner_alt_text")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <label for="translations[{{ $language->id }}][card_description]" class="form-label">
                                                        Card Description ({{ strtoupper($language->lang_code) }})
                                                    </label>
                                                    <textarea class="form-control @error("translations.{$language->id}.card_description") is-invalid @enderror" 
                                                              name="translations[{{ $language->id }}][card_description]" 
                                                              rows="3">{{ old("translations.{$language->id}.card_description") }}</textarea>
                                                    @error("translations.{$language->id}.card_description")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
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
                                                    <label for="translations[{{ $language->id }}][seo_title]" class="form-label">
                                                        SEO Title ({{ strtoupper($language->lang_code) }})
                                                    </label>
                                                    <input type="text" class="form-control @error("translations.{$language->id}.seo_title") is-invalid @enderror" 
                                                           name="translations[{{ $language->id }}][seo_title]" 
                                                           value="{{ old("translations.{$language->id}.seo_title") }}">
                                                    @error("translations.{$language->id}.seo_title")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <label for="translations[{{ $language->id }}][seo_keywords]" class="form-label">
                                                        SEO Keywords ({{ strtoupper($language->lang_code) }})
                                                    </label>
                                                    <textarea class="form-control @error("translations.{$language->id}.seo_keywords") is-invalid @enderror" 
                                                              name="translations[{{ $language->id }}][seo_keywords]" 
                                                              rows="3">{{ old("translations.{$language->id}.seo_keywords") }}</textarea>
                                                    @error("translations.{$language->id}.seo_keywords")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <label for="translations[{{ $language->id }}][seo_description]" class="form-label">
                                                        SEO Description ({{ strtoupper($language->lang_code) }})
                                                    </label>
                                                    <textarea class="form-control @error("translations.{$language->id}.seo_description") is-invalid @enderror" 
                                                              name="translations[{{ $language->id }}][seo_description]" 
                                                              rows="3">{{ old("translations.{$language->id}.seo_description") }}</textarea>
                                                    @error("translations.{$language->id}.seo_description")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <input type="hidden" name="translations[{{ $language->id }}][language_id]" value="{{ $language->id }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">save</i>
                                        </button>
                                        <a href="{{ route('admin.blog-posts.index') }}" class="btn btn-secondary">
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

@push('styles')
<style>
    .nav-tabs .nav-link {
        color: #6c757d;
    }
    .nav-tabs .nav-link.active {
        color: #495057;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/27.1.0/classic/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let editors = {};
    
    // Initialize CKEditor for each description field
    @foreach($languages as $language)
        ClassicEditor
            .create(document.querySelector('#description_{{ $language->id }}'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'blockQuote', 'insertTable', 'undo', 'redo'],
                language: '{{ $language->lang_code }}'
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
            
            // Validate CKEditor fields
            @foreach($languages as $language)
                const editor{{ $language->id }} = editors['{{ $language->id }}'];
                if (editor{{ $language->id }}) {
                    const content = editor{{ $language->id }}.getData().trim();
                    const textarea = document.getElementById('description_{{ $language->id }}');
                    
                    if (!content) {
                        isValid = false;
                        textarea.classList.add('is-invalid');
                        
                        // Switch to the first tab with error
                        if (!firstErrorTab) {
                            firstErrorTab = 'tab-{{ $language->id }}';
                        }
                    } else {
                        textarea.classList.remove('is-invalid');
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
                    if (!firstErrorTab) firstErrorTab = 'tab-{{ $language->id }}';
                } else if (titleInput{{ $language->id }}) {
                    titleInput{{ $language->id }}.classList.remove('is-invalid');
                }
                
                if (slugInput{{ $language->id }} && !slugInput{{ $language->id }}.value.trim()) {
                    isValid = false;
                    slugInput{{ $language->id }}.classList.add('is-invalid');
                    if (!firstErrorTab) firstErrorTab = 'tab-{{ $language->id }}';
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
                }
                alert('Please fill in all required fields marked with *');
            }
        });
    }
});
</script>
@endpush

@include('admin.partials.slug-generator') 
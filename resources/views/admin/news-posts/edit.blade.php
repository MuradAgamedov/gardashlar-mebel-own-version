@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Edit News Post</h3>
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
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="material-icons-outlined">check_circle</i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="material-icons-outlined">error</i>
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
                        <h5>Edit News Post</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.news-posts.update', $newsPost) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
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
                                            <option value="{{ $category->id }}" {{ old('news_category_id', $newsPost->news_category_id) == $category->id ? 'selected' : '' }}>
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
                                           value="{{ old('published_at', $newsPost->published_at ? $newsPost->published_at->format('Y-m-d\TH:i') : '') }}">
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="status" id="status" value="1" 
                                               {{ old('status', $newsPost->status ? '1' : '') ? 'checked' : '' }}>
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
                                    @if($newsPost->card_image)
                                        <div class="mb-2">
                                            <img src="{{ $newsPost->card_image_url }}" 
                                                 alt="Current Card Image" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 200px;">
                                        </div>
                                    @endif
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
                                    @if($newsPost->inner_banner_image)
                                        <div class="mb-2">
                                            <img src="{{ $newsPost->inner_banner_image_url }}" 
                                                 alt="Current Inner Banner Image" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 200px;">
                                        </div>
                                    @endif
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
                                    @php
                                        $translation = $newsPost->translates->firstWhere('language_id', $language->id);
                                    @endphp
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
                                                           value="{{ old("translations.{$language->id}.title", $translation?->title ?? '') }}"
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
                                                           value="{{ old("translations.{$language->id}.slug", $translation?->slug ?? '') }}"
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
                                                              rows="10" required>{{ old("translations.{$language->id}.description", $translation?->description ?? '') }}</textarea>
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
                                                           value="{{ old("translations.{$language->id}.card_image_alt_text", $translation?->card_image_alt_text ?? '') }}">
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
                                                           value="{{ old("translations.{$language->id}.inner_banner_alt_text", $translation?->inner_banner_alt_text ?? '') }}">
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
                                                              rows="3">{{ old("translations.{$language->id}.card_description", $translation?->card_description ?? '') }}</textarea>
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
                                                           value="{{ old("translations.{$language->id}.seo_title", $translation?->seo_title ?? '') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="seo_keywords_{{ $language->id }}" class="form-label">SEO Keywords</label>
                                                    <textarea class="form-control" 
                                                              id="seo_keywords_{{ $language->id }}" 
                                                              name="translations[{{ $language->id }}][seo_keywords]" 
                                                              rows="3">{{ old("translations.{$language->id}.seo_keywords", $translation?->seo_keywords ?? '') }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="seo_description_{{ $language->id }}" class="form-label">SEO Description</label>
                                                    <textarea class="form-control" 
                                                              id="seo_description_{{ $language->id }}" 
                                                              name="translations[{{ $language->id }}][seo_description]" 
                                                              rows="4">{{ old("translations.{$language->id}.seo_description", $translation?->seo_description ?? '') }}</textarea>
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
                                            Update News Post
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize CKEditor for each description field
    @foreach($languages as $language)
        ClassicEditor
            .create(document.querySelector('#description_{{ $language->id }}'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'blockQuote', 'insertTable', 'undo', 'redo'],
                language: '{{ $language->lang_code }}'
            })
            .catch(error => {
                console.error(error);
            });
    @endforeach

    // Auto-generate slug from title
    const titleInputs = document.querySelectorAll('input[name*="[title]"]');
    const slugInputs = document.querySelectorAll('input[name*="[slug]"]');
    
    titleInputs.forEach((titleInput, index) => {
        titleInput.addEventListener('input', function() {
            const slug = this.value.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim('-');
            
            if (slugInputs[index]) {
                slugInputs[index].value = slug;
            }
        });
    });

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
</style>
@endpush

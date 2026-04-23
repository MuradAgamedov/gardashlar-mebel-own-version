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
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>News Post Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.news-posts.update', $newsPost) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <!-- Basic Information -->
                            <div class="row mb-3">
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
                                            <option value="{{ $category->id }}" {{ $newsPost->news_category_id == $category->id ? 'selected' : '' }}>
                                                {{ $categoryTranslation ? $categoryTranslation->title : 'No title' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="published_at" class="form-label">Published At</label>
                                    <input type="datetime-local" 
                                           class="form-control" 
                                           id="published_at" 
                                           name="published_at"
                                           value="{{ $newsPost->published_at ? $newsPost->published_at->format('Y-m-d\TH:i') : '' }}">
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check form-switch mt-4">
                                        <input class="form-check-input" type="checkbox" name="status" id="status" value="1" {{ $newsPost->status ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">
                                            Active Status
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Current Images -->
                            @if($newsPost->card_image || $newsPost->inner_banner_image)
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        @if($newsPost->card_image)
                                            <label class="form-label">Current Card Image</label><br>
                                            <img src="{{ $newsPost->card_image_url }}" alt="Card Image" class="img-thumbnail mb-2" style="max-height: 100px;">
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        @if($newsPost->inner_banner_image)
                                            <label class="form-label">Current Banner Image</label><br>
                                            <img src="{{ $newsPost->inner_banner_image_url }}" alt="Banner Image" class="img-thumbnail mb-2" style="max-height: 100px;">
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Images -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="card_image" class="form-label">Card Image</label>
                                    <input type="file" 
                                           class="form-control" 
                                           id="card_image" 
                                           name="card_image" 
                                           accept="image/*">
                                    <small class="text-muted">Recommended size: 800x600px</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="inner_banner_image" class="form-label">Inner Banner Image</label>
                                    <input type="file" 
                                           class="form-control" 
                                           id="inner_banner_image" 
                                           name="inner_banner_image" 
                                           accept="image/*">
                                    <small class="text-muted">Recommended size: 1920x400px</small>
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
                                        
                                        @php
                                            $translation = $newsPost->translates->firstWhere('language_id', $language->id);
                                        @endphp
                                        
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
                                                           value="{{ $translation?->title ?? '' }}"
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
                                                           value="{{ $translation?->slug ?? '' }}"
                                                           required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="card_description_{{ $language->id }}" class="form-label">Card Description</label>
                                                    <textarea class="form-control" 
                                                              id="card_description_{{ $language->id }}" 
                                                              name="translations[{{ $language->id }}][card_description]" 
                                                              rows="3">{{ $translation?->card_description ?? '' }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="description_{{ $language->id }}" class="form-label">Description</label>
                                                    <textarea class="form-control" 
                                                              id="description_{{ $language->id }}" 
                                                              name="translations[{{ $language->id }}][description]" 
                                                              rows="10">{{ $translation?->description ?? '' }}</textarea>
                                                </div>
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
                                                           value="{{ $translation?->seo_title ?? '' }}">
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
                                                              rows="3">{{ $translation?->seo_keywords ?? '' }}</textarea>
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
                                                              rows="4">{{ $translation?->seo_description ?? '' }}</textarea>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
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
});
</script>
@endpush

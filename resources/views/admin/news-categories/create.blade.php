@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Create News Category</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.news-categories.index') }}">News Categories</a>
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
                        <h5>News Category Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.news-categories.store') }}">
                            @csrf
                            
                            <!-- Status Toggle -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="status" id="status" value="1" {{ old('status', '1') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">
                                            Active Status
                                        </label>
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
                                            Create Category
                                        </button>
                                        <a href="{{ route('admin.news-categories.index') }}" class="btn btn-secondary">
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
<script src="{{ asset('js/slug-generator.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Slug generation is now handled by slug-generator.js
});

    // Form validation before submission
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            let firstErrorTab = null;
            let firstErrorField = null;
            
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
@endpush

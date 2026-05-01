@extends('admin.layout.app')

@section('title', 'Add Blog Category')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Add Blog Category</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.blog-categories.index') }}">Blog Categories</a>
                        </li>
                        <li class="breadcrumb-item active">Add</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">Validation Errors:</h6>
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
                        <h5>New Blog Category</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.blog-categories.store') }}" method="POST">
                            @csrf
                            
                            <!-- Status -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="status" value="0">
                                        <input class="form-check-input" type="checkbox" name="status" value="1" {{ old('status', '1') ? 'checked' : '' }}>
                                        <label class="form-check-label">Active</label>
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
                                            {{ $language->title }}
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
                                                           class="form-control @error('translations.' . $language->id . '.title') is-invalid @enderror" 
                                                           id="title_{{ $language->id }}" 
                                                           name="translations[{{ $language->id }}][title]" 
                                                           value="{{ old('translations.' . $language->id . '.title') }}" 
                                                           placeholder="Enter title for {{ $language->title }}"
                                                           required>
                                                    @error('translations.' . $language->id . '.title')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="slug_{{ $language->id }}" class="form-label">
                                                        Slug <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" 
                                                           class="form-control @error('translations.' . $language->id . '.slug') is-invalid @enderror" 
                                                           id="slug_{{ $language->id }}" 
                                                           name="translations[{{ $language->id }}][slug]" 
                                                           value="{{ old('translations.' . $language->id . '.slug') }}" 
                                                           placeholder="Auto-generated from title"
                                                           required>
                                                    @error('translations.' . $language->id . '.slug')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="seo_title_{{ $language->id }}" class="form-label">SEO Title</label>
                                                    <input type="text" 
                                                           class="form-control @error('translations.' . $language->id . '.seo_title') is-invalid @enderror" 
                                                           id="seo_title_{{ $language->id }}" 
                                                           name="translations[{{ $language->id }}][seo_title]" 
                                                           value="{{ old('translations.' . $language->id . '.seo_title') }}" 
                                                           placeholder="Enter SEO title for {{ $language->title }}">
                                                    @error('translations.' . $language->id . '.seo_title')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="seo_keywords_{{ $language->id }}" class="form-label">SEO Keywords</label>
                                                    <textarea class="form-control @error('translations.' . $language->id . '.seo_keywords') is-invalid @enderror" 
                                                              id="seo_keywords_{{ $language->id }}" 
                                                              name="translations[{{ $language->id }}][seo_keywords]" 
                                                              rows="3"
                                                              placeholder="Enter SEO keywords for {{ $language->title }}">{{ old('translations.' . $language->id . '.seo_keywords') }}</textarea>
                                                    @error('translations.' . $language->id . '.seo_keywords')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="seo_description_{{ $language->id }}" class="form-label">SEO Description</label>
                                                    <textarea class="form-control @error('translations.' . $language->id . '.seo_description') is-invalid @enderror" 
                                                              id="seo_description_{{ $language->id }}" 
                                                              name="translations[{{ $language->id }}][seo_description]" 
                                                              rows="4"
                                                              placeholder="Enter SEO description for {{ $language->title }}">{{ old('translations.' . $language->id . '.seo_description') }}</textarea>
                                                    @error('translations.' . $language->id . '.seo_description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
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
                                        <button type="submit" class="btn btn-primary" title="{{ __('Create Category') }}">
                                            <i class="material-icons-outlined">save</i>
                                        </button>
                                        <a href="{{ route('admin.blog-categories.index') }}" class="btn btn-secondary" title="{{ __('Cancel') }}">
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
<script src="{{ asset('js/slug-generator.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Slug generation is now handled by slug-generator.js
});
</script>
@endpush 
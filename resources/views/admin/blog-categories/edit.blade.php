@extends('admin.layout.app')

@section('title', 'Edit Blog Category')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Edit Blog Category</h3>
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
                        <li class="breadcrumb-item active">Edit</li>
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
                        <h5>Edit Blog Category</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.blog-categories.update', $blogCategory->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <!-- Status -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="status" value="1" {{ $blogCategory->status ? 'checked' : '' }}>
                                        <label class="form-check-label">Active</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Translations -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label">Translations</label>
                                    <div class="card border">
                                        <div class="card-body">
                                            @foreach($languages as $language)
                                                @php
                                                    $translation = $translations->get($language->id);
                                                @endphp
                                                <div class="row mb-4">
                                                    <div class="col-md-12">
                                                        <h6 class="mb-3">
                                                            <span class="badge bg-primary">{{ strtoupper($language->lang_code) }}</span>
                                                            {{ $language->title }}
                                                        </h6>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">
                                                            Title <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" 
                                                               class="form-control @error('translations.' . $language->id . '.title') is-invalid @enderror" 
                                                               name="translations[{{ $language->id }}][title]" 
                                                               id="title_{{ $language->id }}"
                                                               value="{{ old('translations.' . $language->id . '.title', $translation?->title) }}" 
                                                               placeholder="Enter title for {{ $language->title }}"
                                                               required>
                                                        @error('translations.' . $language->id . '.title')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Slug</label>
                                                        <input type="text" 
                                                               class="form-control @error('translations.' . $language->id . '.slug') is-invalid @enderror" 
                                                               name="translations[{{ $language->id }}][slug]" 
                                                               id="slug_{{ $language->id }}"
                                                               value="{{ old('translations.' . $language->id . '.slug', $translation?->slug) }}" 
                                                               placeholder="Auto-generated from title">
                                                        @error('translations.' . $language->id . '.slug')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">SEO Title</label>
                                                        <input type="text" 
                                                               class="form-control @error('translations.' . $language->id . '.seo_title') is-invalid @enderror" 
                                                               name="translations[{{ $language->id }}][seo_title]" 
                                                               value="{{ old('translations.' . $language->id . '.seo_title', $translation?->seo_title) }}" 
                                                               placeholder="Enter SEO title for {{ $language->title }}">
                                                        @error('translations.' . $language->id . '.seo_title')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-12 mt-3">
                                                        <label class="form-label">SEO Keywords</label>
                                                        <textarea class="form-control @error('translations.' . $language->id . '.seo_keywords') is-invalid @enderror" 
                                                                  name="translations[{{ $language->id }}][seo_keywords]" 
                                                                  rows="2"
                                                                  placeholder="Enter SEO keywords for {{ $language->title }}">{{ old('translations.' . $language->id . '.seo_keywords', $translation?->seo_keywords) }}</textarea>
                                                        @error('translations.' . $language->id . '.seo_keywords')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-12 mt-3">
                                                        <label class="form-label">SEO Description</label>
                                                        <textarea class="form-control @error('translations.' . $language->id . '.seo_description') is-invalid @enderror" 
                                                                  name="translations[{{ $language->id }}][seo_description]" 
                                                                  rows="3"
                                                                  placeholder="Enter SEO description for {{ $language->title }}">{{ old('translations.' . $language->id . '.seo_description', $translation?->seo_description) }}</textarea>
                                                        @error('translations.' . $language->id . '.seo_description')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                @if(!$loop->last)
                                                    <hr>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.blog-categories.index') }}" class="btn btn-secondary">
                                            <i class="material-icons-outlined">arrow_back</i>
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">save</i>
                                        </button>
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

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Slug generation function
    function generateSlug(text) {
        return text
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '') // Remove special characters
            .replace(/[\s_-]+/g, '-') // Replace spaces and underscores with hyphens
            .replace(/^-+|-+$/g, ''); // Remove leading/trailing hyphens
    }

    // Get all title inputs
    const titleInputs = document.querySelectorAll('input[id^="title_"]');
    
    titleInputs.forEach(function(titleInput) {
        titleInput.addEventListener('input', function() {
            const languageId = this.id.split('_')[1];
            const slugInput = document.getElementById('slug_' + languageId);
            
            if (slugInput && this.value.trim()) {
                slugInput.value = generateSlug(this.value);
            }
        });
    });
});
</script>
@endsection 
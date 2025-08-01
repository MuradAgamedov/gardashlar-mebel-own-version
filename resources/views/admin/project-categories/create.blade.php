@extends('admin.layout.app')

@section('title', 'Create Project Category')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Create Project Category</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.project-categories.index') }}">Project Categories Management</a>
                        </li>
                        <li class="breadcrumb-item active">Create Project Category</li>
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

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Add New Project Category</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.project-categories.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Image Upload -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Category Image (optional)</label>
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div id="image-preview-container" class="mt-2"></div>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" checked>
                                        <label class="form-check-label" for="status">Active</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Language Tabs -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label">Project Category Information</label>
                                    <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                                        @foreach($languages as $index => $language)
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                                        id="tab-{{ $language->id }}" 
                                                        data-bs-toggle="tab" 
                                                        data-bs-target="#content-{{ $language->id }}" 
                                                        type="button" 
                                                        role="tab">
                                                        {{ $language->lang_name }} ({{ $language->lang_code }})
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <div class="tab-content" id="languageTabContent">
                                        @foreach($languages as $index => $language)
                                            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                                 id="content-{{ $language->id }}" 
                                                 role="tabpanel">
                                                
                                                <div class="row mt-3">
                                                    <!-- Title -->
                                                    <div class="col-md-6">
                                                        <label class="form-label">
                                                            Title <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" 
                                                               class="form-control @error("translations.{$language->id}.title") is-invalid @enderror" 
                                                               name="translations[{{ $language->id }}][title]" 
                                                               value="{{ old("translations.{$language->id}.title") }}" 
                                                               placeholder="Enter title in {{ $language->lang_name }}"
                                                               required>
                                                        @error("translations.{$language->id}.title")
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <!-- Slug -->
                                                    <div class="col-md-6">
                                                        <label class="form-label">
                                                            Slug <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" 
                                                               class="form-control @error("translations.{$language->id}.slug") is-invalid @enderror" 
                                                               name="translations[{{ $language->id }}][slug]" 
                                                               value="{{ old("translations.{$language->id}.slug") }}" 
                                                               placeholder="Enter slug in {{ $language->lang_name }}"
                                                               required>
                                                        @error("translations.{$language->id}.slug")
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                        <small class="form-text text-muted">
                                                            URL-friendly version of the title.
                                                        </small>
                                                    </div>
                                                    
                                                    <!-- SEO Title -->
                                                    <div class="col-md-12">
                                                        <label class="form-label">SEO Title</label>
                                                        <input type="text" 
                                                               class="form-control @error("translations.{$language->id}.seo_title") is-invalid @enderror" 
                                                               name="translations[{{ $language->id }}][seo_title]" 
                                                               value="{{ old("translations.{$language->id}.seo_title") }}" 
                                                               placeholder="SEO title for {{ $language->lang_name }}">
                                                        @error("translations.{$language->id}.seo_title")
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <!-- SEO Keywords -->
                                                    <div class="col-md-12">
                                                        <label class="form-label">SEO Keywords</label>
                                                        <textarea class="form-control @error("translations.{$language->id}.seo_keywords") is-invalid @enderror" 
                                                                  name="translations[{{ $language->id }}][seo_keywords]" 
                                                                  rows="3"
                                                                  placeholder="SEO keywords for {{ $language->lang_name }} (comma separated)">{{ old("translations.{$language->id}.seo_keywords") }}</textarea>
                                                        @error("translations.{$language->id}.seo_keywords")
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <!-- SEO Description -->
                                                    <div class="col-md-12">
                                                        <label class="form-label">SEO Description</label>
                                                        <textarea class="form-control @error("translations.{$language->id}.seo_description") is-invalid @enderror" 
                                                                  name="translations[{{ $language->id }}][seo_description]" 
                                                                  rows="3"
                                                                  placeholder="SEO description for {{ $language->lang_name }}">{{ old("translations.{$language->id}.seo_description") }}</textarea>
                                                        @error("translations.{$language->id}.seo_description")
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <!-- Image Alt Text -->
                                                    <div class="col-md-12">
                                                        <label class="form-label">Image Alt Text</label>
                                                        <input type="text" 
                                                               class="form-control @error("translations.{$language->id}.image_alt_text") is-invalid @enderror" 
                                                               name="translations[{{ $language->id }}][image_alt_text]" 
                                                               value="{{ old("translations.{$language->id}.image_alt_text") }}" 
                                                               placeholder="Image alt text for {{ $language->lang_name }}">
                                                        @error("translations.{$language->id}.image_alt_text")
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                
                                                <input type="hidden" name="translations[{{ $language->id }}][language_id]" value="{{ $language->id }}">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">save</i>
                                        </button>
                                        <a href="{{ route('admin.project-categories.index') }}" class="btn btn-secondary">
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
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate slug from title for each language
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    tabPanes.forEach(pane => {
        const titleInput = pane.querySelector('input[name*="[title]"]');
        const slugInput = pane.querySelector('input[name*="[slug]"]');
        
        if (titleInput && slugInput) {
            titleInput.addEventListener('input', function() {
                if (slugInput.value === '') {
                    slugInput.value = this.value.toLowerCase()
                        .replace(/[^a-z0-9\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-')
                        .trim('-');
                }
            });
        }
    });
});

// Image preview for category image
const imageInput = document.getElementById('image');
const previewContainer = document.getElementById('image-preview-container');
if (imageInput) {
    imageInput.addEventListener('change', function(event) {
        previewContainer.innerHTML = '';
        const file = event.target.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewContainer.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded border" style="max-height:120px;object-fit:cover;" alt="Preview">`;
            };
            reader.readAsDataURL(file);
        }
    });
}
</script>
@endpush 
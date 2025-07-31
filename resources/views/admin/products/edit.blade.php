@extends('admin.layout.app')

@section('title', 'Edit Product')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Edit Product</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.products.index') }}">Products</a>
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

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">Error:</h6>
                <p class="mb-0">{{ session('error') }}</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Edit Product</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.products.update', $product) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <!-- Basic Information -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Category <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('product_category_id') is-invalid @enderror" 
                                            name="product_category_id" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                    {{ old('product_category_id', $product->product_category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->translate()->title ?? 'No title' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Product Type <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('product_type_id') is-invalid @enderror" 
                                            name="product_type_id" required>
                                        <option value="">Select Type</option>
                                        @foreach($types as $type)
                                            <option value="{{ $type->id }}" 
                                                    {{ old('product_type_id', $product->product_type_id) == $type->id ? 'selected' : '' }}>
                                                {{ $type->translate()->title ?? 'No title' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_type_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Product Details -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Discount Percent (%)</label>
                                    <input type="number" 
                                           class="form-control @error('discount_percent') is-invalid @enderror" 
                                           name="discount_percent" 
                                           value="{{ old('discount_percent', $product->discount_percent) }}"
                                           min="0" 
                                           max="100" 
                                           step="0.01"
                                           placeholder="0.00">
                                    @error('discount_percent')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Product Tags -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label">Product Tags</label>
                                    <select class="form-select select2 @error('product_tags') is-invalid @enderror" 
                                            name="product_tags[]" 
                                            multiple>
                                        @foreach($tags as $tag)
                                            <option value="{{ $tag->id }}" 
                                                    {{ in_array($tag->id, old('product_tags', $product->tags->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                {{ $tag->translate()->title ?? 'No title' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Select multiple tags</small>
                                    @error('product_tags')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Language Tabs -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label">Product Information</label>
                                    <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                                        @foreach($languages as $index => $language)
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                                        id="tab-{{ $language->id }}" 
                                                        data-bs-toggle="tab" 
                                                        data-bs-target="#content-{{ $language->id }}" 
                                                        type="button" 
                                                        role="tab">
                                                        {{ $language->name }} ({{ $language->lang_code }})
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <div class="tab-content" id="languageTabContent">
                                        @foreach($languages as $index => $language)
                                            @php
                                                $translation = $product->translates->where('language_id', $language->id)->first();
                                            @endphp
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
                                                               class="form-control @error("translations.{$language->id}.title") is-invalid @enderror title-input" 
                                                               name="translations[{{ $language->id }}][title]" 
                                                               value="{{ old("translations.{$language->id}.title", $translation ? $translation->title : '') }}" 
                                                               placeholder="Enter product title"
                                                               required>
                                                        @error("translations.{$language->id}.title")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <!-- Slug -->
                                                    <div class="col-md-6">
                                                        <label class="form-label">
                                                            Slug <span class="text-danger">*</span>
                                                        </label>
                                                        <div class="input-group">
                                                            <input type="text" 
                                                                   class="form-control @error("translations.{$language->id}.slug") is-invalid @enderror slug-input" 
                                                                   name="translations[{{ $language->id }}][slug]" 
                                                                   id="slug_{{ $language->id }}"
                                                                   value="{{ old("translations.{$language->id}.slug", $translation ? $translation->slug : '') }}" 
                                                                   placeholder="Enter slug"
                                                                   data-language="{{ $language->lang_code }}"
                                                                   required>
                                                            <button type="button" 
                                                                    class="btn btn-outline-secondary generate-slug" 
                                                                    data-title-input="translations[{{ $language->id }}][title]"
                                                                    data-slug-input="slug_{{ $language->id }}">
                                                                <i class="material-icons-outlined">auto_fix_high</i>
                                                            </button>
                                                        </div>
                                                        @error("translations.{$language->id}.slug")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <!-- Description -->
                                                    <div class="col-12 mt-3">
                                                        <label class="form-label">Description</label>
                                                        <textarea class="form-control @error("translations.{$language->id}.description") is-invalid @enderror" 
                                                                  name="translations[{{ $language->id }}][description]" 
                                                                  rows="4"
                                                                  placeholder="Enter product description">{{ old("translations.{$language->id}.description", $translation ? $translation->description : '') }}</textarea>
                                                        @error("translations.{$language->id}.description")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <!-- SEO Fields -->
                                                    <div class="col-12 mt-3">
                                                        <h6 class="mb-3">SEO Information</h6>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">SEO Title</label>
                                                        <input type="text" 
                                                               class="form-control @error("translations.{$language->id}.seo_title") is-invalid @enderror" 
                                                               name="translations[{{ $language->id }}][seo_title]" 
                                                               value="{{ old("translations.{$language->id}.seo_title", $translation ? $translation->seo_title : '') }}" 
                                                               placeholder="SEO optimized title">
                                                        @error("translations.{$language->id}.seo_title")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <div class="col-md-4">
                                                        <label class="form-label">SEO Keywords</label>
                                                        <textarea class="form-control @error("translations.{$language->id}.seo_keywords") is-invalid @enderror" 
                                                                  name="translations[{{ $language->id }}][seo_keywords]" 
                                                                  rows="3"
                                                                  placeholder="keyword1, keyword2, keyword3">{{ old("translations.{$language->id}.seo_keywords", $translation ? $translation->seo_keywords : '') }}</textarea>
                                                        @error("translations.{$language->id}.seo_keywords")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <div class="col-md-4">
                                                        <label class="form-label">SEO Description</label>
                                                        <textarea class="form-control @error("translations.{$language->id}.seo_description") is-invalid @enderror" 
                                                                  name="translations[{{ $language->id }}][seo_description]" 
                                                                  rows="3"
                                                                  placeholder="SEO meta description">{{ old("translations.{$language->id}.seo_description", $translation ? $translation->seo_description : '') }}</textarea>
                                                        @error("translations.{$language->id}.seo_description")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <!-- Product Details -->
                                                    <div class="col-12 mt-3">
                                                        <h6 class="mb-3">Product Details</h6>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Weight (kg)</label>
                                                        <input type="text" 
                                                               class="form-control @error("translations.{$language->id}.weight") is-invalid @enderror" 
                                                               name="translations[{{ $language->id }}][weight]" 
                                                               value="{{ old("translations.{$language->id}.weight", $translation ? $translation->weight : '') }}"
                                                               min="0" 
                                                               step="0.001"
                                                               placeholder="0.000">
                                                        @error("translations.{$language->id}.weight")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <div class="col-md-4">
                                                        <label class="form-label">Material</label>
                                                        <textarea class="form-control @error("translations.{$language->id}.material") is-invalid @enderror" 
                                                                  name="translations[{{ $language->id }}][material]" 
                                                                  rows="3"
                                                                  placeholder="e.g., Cotton, Wood, Metal">{{ old("translations.{$language->id}.material", $translation ? $translation->material : '') }}</textarea>
                                                        @error("translations.{$language->id}.material")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <div class="col-md-4">
                                                        <label class="form-label">Size</label>
                                                        <textarea class="form-control @error("translations.{$language->id}.size") is-invalid @enderror" 
                                                                  name="translations[{{ $language->id }}][size]" 
                                                                  rows="3"
                                                                  placeholder="e.g., S, M, L, XL">{{ old("translations.{$language->id}.size", $translation ? $translation->size : '') }}</textarea>
                                                        @error("translations.{$language->id}.size")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                
                                                <input type="hidden" 
                                                       name="translations[{{ $language->id }}][language_id]" 
                                                       value="{{ $language->id }}">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="status" 
                                               name="status" 
                                               value="1" 
                                               {{ old('status', $product->status) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">Active</label>
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
                                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.alert-heading {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.is-invalid {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

.invalid-feedback {
    display: block;
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.nav-tabs .nav-link.active {
    border-color: #dee2e6 #dee2e6 #fff;
    background-color: #fff;
}

.tab-pane.show.active {
    border: 1px solid #dee2e6;
    border-top: none;
    padding: 1rem;
}

.select2-container--default .select2-selection--multiple {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    min-height: 38px;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #007bff;
    border: 1px solid #0056b3;
    color: white;
    border-radius: 0.25rem;
    padding: 2px 8px;
    margin: 2px;
}
</style>
@endpush

@push('scripts')
<script>
console.log('Script loaded!');
alert('Script is working!');
</script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: 'Select tags...',
        allowClear: true,
        width: '100%'
    });
    
    // Generate slug functionality
    document.querySelectorAll('.generate-slug').forEach(button => {
        button.addEventListener('click', function() {
            const titleInput = document.querySelector(`[name="${this.dataset.titleInput}"]`);
            const slugInput = document.getElementById(this.dataset.slugInput);
            
            if (titleInput.value) {
                fetch('{{ route("admin.products.generate-slug") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ title: titleInput.value })
                })
                .then(response => response.json())
                .then(data => {
                    slugInput.value = data.slug;
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });
    });
    
    // Auto-switch tabs on validation errors
    const errorFields = document.querySelectorAll('.is-invalid');
    if (errorFields.length > 0) {
        const firstError = errorFields[0];
        
        // Check if error is in translation field
        const translationMatch = firstError.name.match(/translations\[(\d+)\]/);
        if (translationMatch) {
            const tabId = translationMatch[1];
            const tabButton = document.getElementById(`tab-${tabId}`);
            const tabContent = document.getElementById(`content-${tabId}`);
            
            if (tabButton && tabContent) {
                // Remove active class from all tabs
                document.querySelectorAll('.nav-link').forEach(tab => tab.classList.remove('active'));
                document.querySelectorAll('.tab-pane').forEach(content => content.classList.remove('show', 'active'));
                
                // Activate the tab with error
                tabButton.classList.add('active');
                tabContent.classList.add('show', 'active');
            }
        }
        
        // Scroll to the error field
        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    });
    
    // Cyrillic to Latin character mapping
    const cyrillicMap = {
        // Cyrillic letters
        'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd', 'е': 'e', 'ё': 'yo',
        'ж': 'zh', 'з': 'z', 'и': 'i', 'й': 'y', 'к': 'k', 'л': 'l', 'м': 'm',
        'н': 'n', 'о': 'o', 'п': 'p', 'р': 'r', 'с': 's', 'т': 't', 'у': 'u',
        'ф': 'f', 'х': 'h', 'ц': 'ts', 'ч': 'ch', 'ш': 'sh', 'щ': 'sch',
        'ъ': '', 'ы': 'y', 'ь': '', 'э': 'e', 'ю': 'yu', 'я': 'ya',
        // Azerbaijani specific characters
        'ə': 'e', 'ğ': 'gh', 'ü': 'u', 'ş': 'sh', 'ı': 'i', 'ö': 'o', 'ç': 'ch',
        // Uppercase Cyrillic letters
        'А': 'a', 'Б': 'b', 'В': 'v', 'Г': 'g', 'Д': 'd', 'Е': 'e', 'Ё': 'yo',
        'Ж': 'zh', 'З': 'z', 'И': 'i', 'Й': 'y', 'К': 'k', 'Л': 'l', 'М': 'm',
        'Н': 'n', 'О': 'o', 'П': 'p', 'Р': 'r', 'С': 's', 'Т': 't', 'У': 'u',
        'Ф': 'f', 'Х': 'h', 'Ц': 'ts', 'Ч': 'ch', 'Ш': 'sh', 'Щ': 'sch',
        'Ъ': '', 'Ы': 'y', 'Ь': '', 'Э': 'e', 'Ю': 'yu', 'Я': 'ya',
        // Uppercase Azerbaijani specific characters
        'Ə': 'e', 'Ğ': 'gh', 'Ü': 'u', 'Ş': 'sh', 'I': 'i', 'Ö': 'o', 'Ç': 'ch',
    };

    // Function to generate slug from title with Cyrillic support
    function generateSlug(title) {
        if (!title) return '';
        let slug = title.toLowerCase();
        for (let cyrillic in cyrillicMap) {
            slug = slug.replace(new RegExp(cyrillic, 'g'), cyrillicMap[cyrillic]);
        }
        slug = slug.replace(/[^a-z0-9\s\-]/g, '');
        slug = slug.replace(/[\s\-]+/g, '-');
        slug = slug.replace(/^-+|-+$/g, '');
        return slug;
    }

    // For each title input, update the corresponding slug input
    document.querySelectorAll('input[name^="translations"][name$="[title]"]').forEach(function(titleInput) {
        titleInput.addEventListener('input', function() {
            const match = this.name.match(/translations\[(\d+)\]\[title\]/);
            if (!match) return;
            const languageId = match[1];
            const slugInput = document.querySelector('input[name="translations[' + languageId + '][slug]"]');
            if (slugInput && (!slugInput.dataset.touched || slugInput.value === '' || slugInput.value === generateSlug(this.defaultValue))) {
                slugInput.value = generateSlug(this.value);
            }
        });
    });
    
    // If user manually edits slug, mark it as touched so it won't auto-update anymore
    document.querySelectorAll('input[name^="translations"][name$="[slug]"]').forEach(function(slugInput) {
        slugInput.addEventListener('input', function() {
            this.dataset.touched = 'true';
        });
    });
});
</script>
@endpush 
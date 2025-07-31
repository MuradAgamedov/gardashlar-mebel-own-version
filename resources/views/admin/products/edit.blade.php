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
                                    <label class="form-label">Product Categories</label>
                                    <div class="position-relative">
                                        <input type="text" 
                                               class="form-control categories-input" 
                                               id="categoriesInput" 
                                               placeholder="Type to search categories..."
                                               autocomplete="off">
                                        <div class="categories-dropdown" id="categoriesDropdown" style="display: none;">
                                            <!-- Categories will be loaded here -->
                                        </div>
                                    </div>
                                    <input type="hidden" 
                                           name="product_categories" 
                                           id="categoriesHiddenInput" 
                                           value="{{ old('product_categories', json_encode($product->categories->pluck('id')->toArray())) }}">
                                </div>
                                <small class="form-text text-muted">Click on categories to select/deselect</small>
                                @error('product_categories')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                <div class="col-md-6">
                                    <label class="form-label">Product Types</label>
                                    <div class="position-relative">
                                        <input type="text" 
                                               class="form-control types-input" 
                                               id="typesInput" 
                                               placeholder="Type to search product types..."
                                               autocomplete="off">
                                        <div class="types-dropdown" id="typesDropdown" style="display: none;">
                                            <!-- Types will be loaded here -->
                                        </div>
                                    </div>
                                    <input type="hidden" 
                                           name="product_types" 
                                           id="typesHiddenInput" 
                                           value="{{ old('product_types', json_encode($product->types->pluck('id')->toArray())) }}">
                                </div>
                                <small class="form-text text-muted">Click on types to select/deselect</small>
                                @error('product_types')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                    <div class="custom-tags-container">
                                        <div class="tags-input-wrapper">
                                            <div class="selected-tags" id="selectedTags">
                                                <!-- Selected tags will appear here -->
                                            </div>
                                            <input type="text" 
                                                   class="form-control tags-input" 
                                                   id="tagsInput" 
                                                   placeholder="Type to search tags..."
                                                   autocomplete="off">
                                            <div class="tags-dropdown" id="tagsDropdown" style="display: none;">
                                                <!-- Tags will be loaded here -->
                                            </div>
                                        </div>
                                        <input type="hidden" 
                                               name="product_tags" 
                                               id="tagsHiddenInput" 
                                               value="{{ old('product_tags', json_encode($product->tags->pluck('id')->toArray())) }}">
                                    </div>
                                    <small class="form-text text-muted">Click on tags to select/deselect</small>
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

/* Custom Tags Styling */
.custom-tags-container {
    position: relative;
}

.tags-input-wrapper {
    position: relative;
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    background: white;
    min-height: 38px;
    padding: 5px;
}

.selected-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin-bottom: 5px;
}

.tag-item {
    background: #007bff;
    color: white;
    padding: 2px 8px;
    border-radius: 0.25rem;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 5px;
    cursor: pointer;
}

.tag-item:hover {
    background: #0056b3;
}

.tag-remove {
    background: none;
    border: none;
    color: white;
    font-size: 12px;
    cursor: pointer;
    padding: 0;
    margin-left: 5px;
}

.tags-input {
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    padding: 0;
    margin: 0;
    background: transparent;
    min-height: auto;
}

.tags-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ced4da;
    border-top: none;
    border-radius: 0 0 0.375rem 0.375rem;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.tag-option {
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 1px solid #f8f9fa;
}

.tag-option:hover {
    background: #f8f9fa;
}

.tag-option.selected {
    background: #e3f2fd;
    color: #1976d2;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Custom Tags Functionality
    const tagsInput = document.getElementById('tagsInput');
    const tagsDropdown = document.getElementById('tagsDropdown');
    const selectedTags = document.getElementById('selectedTags');
    const tagsHiddenInput = document.getElementById('tagsHiddenInput');
    
    // Available tags data
    const availableTags = [
        @foreach($tags as $tag)
        {
            id: {{ $tag->id }},
            title: "{{ $tag->translate()->title ?? 'No title' }}"
        },
        @endforeach
    ];
    
    let selectedTagIds = [];
    
    // Load old values if any
    try {
        const oldValues = JSON.parse(tagsHiddenInput.value);
        if (Array.isArray(oldValues)) {
            selectedTagIds = oldValues;
            renderSelectedTags();
        }
    } catch (e) {
        selectedTagIds = [];
    }
    
    function renderSelectedTags() {
        selectedTags.innerHTML = '';
        selectedTagIds.forEach(tagId => {
            const tag = availableTags.find(t => t.id === tagId);
            if (tag) {
                const tagElement = document.createElement('div');
                tagElement.className = 'tag-item';
                tagElement.innerHTML = `
                    ${tag.title}
                    <button type="button" class="tag-remove" onclick="removeTag(${tagId})">×</button>
                `;
                selectedTags.appendChild(tagElement);
            }
        });
        updateHiddenInput();
    }
    
    function removeTag(tagId) {
        selectedTagIds = selectedTagIds.filter(id => id !== tagId);
        renderSelectedTags();
    }
    
    function updateHiddenInput() {
        tagsHiddenInput.value = JSON.stringify(selectedTagIds);
    }
    
    function showDropdown() {
        const filteredTags = availableTags.filter(tag => 
            !selectedTagIds.includes(tag.id) &&
            tag.title.toLowerCase().includes(tagsInput.value.toLowerCase())
        );
        
        if (filteredTags.length > 0) {
            tagsDropdown.innerHTML = '';
            filteredTags.forEach(tag => {
                const option = document.createElement('div');
                option.className = 'tag-option';
                option.textContent = tag.title;
                option.onclick = () => {
                    selectedTagIds.push(tag.id);
                    renderSelectedTags();
                    tagsInput.value = '';
                    hideDropdown();
                };
                tagsDropdown.appendChild(option);
            });
            tagsDropdown.style.display = 'block';
        } else {
            hideDropdown();
        }
    }
    
    function hideDropdown() {
        tagsDropdown.style.display = 'none';
    }
    
    // Event listeners
    tagsInput.addEventListener('input', showDropdown);
    tagsInput.addEventListener('focus', showDropdown);
    
    document.addEventListener('click', function(e) {
        if (!tagsInput.contains(e.target) && !tagsDropdown.contains(e.target)) {
            hideDropdown();
        }
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
    
    // Make removeTag function global
    window.removeTag = removeTag;
});

// Product Types Selection
document.addEventListener('DOMContentLoaded', function() {
    const typesInput = document.getElementById('typesInput');
    const typesDropdown = document.getElementById('typesDropdown');
    const typesHiddenInput = document.getElementById('typesHiddenInput');
    const selectedTypes = document.createElement('div');
    selectedTypes.className = 'selected-types mt-2';
    typesInput.parentNode.appendChild(selectedTypes);
    
    let availableTypes = @json($types->map(function($type) {
        return [
            'id' => $type->id,
            'title' => $type->translate()->title ?? 'No title'
        ];
    }));
    
    let selectedTypeIds = JSON.parse(typesHiddenInput.value || '[]');
    
    function renderSelectedTypes() {
        selectedTypes.innerHTML = '';
        selectedTypeIds.forEach(typeId => {
            const type = availableTypes.find(t => t.id === typeId);
            if (type) {
                const typeElement = document.createElement('div');
                typeElement.className = 'tag-item';
                typeElement.innerHTML = `
                    ${type.title}
                    <button type="button" class="tag-remove" onclick="removeType(${typeId})">×</button>
                `;
                selectedTypes.appendChild(typeElement);
            }
        });
        updateTypesHiddenInput();
    }
    
    function removeType(typeId) {
        selectedTypeIds = selectedTypeIds.filter(id => id !== typeId);
        renderSelectedTypes();
    }
    
    function updateTypesHiddenInput() {
        typesHiddenInput.value = JSON.stringify(selectedTypeIds);
    }
    
    function showTypesDropdown() {
        const filteredTypes = availableTypes.filter(type => 
            !selectedTypeIds.includes(type.id) &&
            type.title.toLowerCase().includes(typesInput.value.toLowerCase())
        );
        
        if (filteredTypes.length > 0) {
            typesDropdown.innerHTML = '';
            filteredTypes.forEach(type => {
                const option = document.createElement('div');
                option.className = 'tag-option';
                option.textContent = type.title;
                option.onclick = () => {
                    selectedTypeIds.push(type.id);
                    renderSelectedTypes();
                    typesInput.value = '';
                    hideTypesDropdown();
                };
                typesDropdown.appendChild(option);
            });
            typesDropdown.style.display = 'block';
        } else {
            hideTypesDropdown();
        }
    }
    
    function hideTypesDropdown() {
        typesDropdown.style.display = 'none';
    }
    
    // Event listeners
    typesInput.addEventListener('input', showTypesDropdown);
    typesInput.addEventListener('focus', showTypesDropdown);
    
    document.addEventListener('click', function(e) {
        if (!typesInput.contains(e.target) && !typesDropdown.contains(e.target)) {
            hideTypesDropdown();
        }
    });
    
    // Initialize selected types
    renderSelectedTypes();
    
    // Make removeType function global
    window.removeType = removeType;
});

// Product Categories Selection
document.addEventListener('DOMContentLoaded', function() {
    const categoriesInput = document.getElementById('categoriesInput');
    const categoriesDropdown = document.getElementById('categoriesDropdown');
    const categoriesHiddenInput = document.getElementById('categoriesHiddenInput');
    const selectedCategories = document.createElement('div');
    selectedCategories.className = 'selected-categories mt-2';
    categoriesInput.parentNode.appendChild(selectedCategories);
    
    let availableCategories = @json($categories->map(function($category) {
        return [
            'id' => $category->id,
            'title' => $category->translate()->title ?? 'No title'
        ];
    }));
    
    let selectedCategoryIds = JSON.parse(categoriesHiddenInput.value || '[]');
    
    function renderSelectedCategories() {
        selectedCategories.innerHTML = '';
        selectedCategoryIds.forEach(categoryId => {
            const category = availableCategories.find(c => c.id === categoryId);
            if (category) {
                const categoryElement = document.createElement('div');
                categoryElement.className = 'tag-item';
                categoryElement.innerHTML = `
                    ${category.title}
                    <button type="button" class="tag-remove" onclick="removeCategory(${categoryId})">×</button>
                `;
                selectedCategories.appendChild(categoryElement);
            }
        });
        updateCategoriesHiddenInput();
    }
    
    function removeCategory(categoryId) {
        selectedCategoryIds = selectedCategoryIds.filter(id => id !== categoryId);
        renderSelectedCategories();
    }
    
    function updateCategoriesHiddenInput() {
        categoriesHiddenInput.value = JSON.stringify(selectedCategoryIds);
    }
    
    function showCategoriesDropdown() {
        const filteredCategories = availableCategories.filter(category => 
            !selectedCategoryIds.includes(category.id) &&
            category.title.toLowerCase().includes(categoriesInput.value.toLowerCase())
        );
        
        if (filteredCategories.length > 0) {
            categoriesDropdown.innerHTML = '';
            filteredCategories.forEach(category => {
                const option = document.createElement('div');
                option.className = 'tag-option';
                option.textContent = category.title;
                option.onclick = () => {
                    selectedCategoryIds.push(category.id);
                    renderSelectedCategories();
                    categoriesInput.value = '';
                    hideCategoriesDropdown();
                };
                categoriesDropdown.appendChild(option);
            });
            categoriesDropdown.style.display = 'block';
        } else {
            hideCategoriesDropdown();
        }
    }
    
    function hideCategoriesDropdown() {
        categoriesDropdown.style.display = 'none';
    }
    
    // Event listeners
    categoriesInput.addEventListener('input', showCategoriesDropdown);
    categoriesInput.addEventListener('focus', showCategoriesDropdown);
    
    document.addEventListener('click', function(e) {
        if (!categoriesInput.contains(e.target) && !categoriesDropdown.contains(e.target)) {
            hideCategoriesDropdown();
        }
    });
    
    // Initialize selected categories
    renderSelectedCategories();
    
    // Make removeCategory function global
    window.removeCategory = removeCategory;
});
</script>
@include('admin.partials.slug-generator')
@endpush 
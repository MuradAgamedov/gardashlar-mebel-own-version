@extends('admin.layout.app')

@section('title', __('Company Products Management'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">
                                <i class="material-icons-outlined me-2">settings_applications</i>
                                {{ __('Products Management') }}
                            </h4>
                            @php
                                $mainLanguageId = \App\Models\Language::getMainLanguage()->id;
                                $companyMainTranslate = $company->translates->firstWhere('language_id', $mainLanguageId)
                                    ?? $company->translates->first();
                            @endphp
                            <p class="text-muted mb-0">
                                <strong>{{ $companyMainTranslate?->title ?? __('No Company') }}</strong>
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.campaigns.index') }}" class="btn btn-secondary" title="{{ __('Back') }}">
                                <i class="material-icons-outlined">arrow_back</i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Categories Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">
                            <i class="material-icons-outlined me-2">category</i>
                            {{ __('Add Products by Category') }}
                        </h5>
                            
                            <div class="card">
                                <div class="card-body">
                                    <form id="categoryForm">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                            <i class="material-icons-outlined me-1">checklist</i>
                                                            {{ __('Select Categories') }}
                                                        </label>
                                                    <div class="category-selection" style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 1rem;">
                                                        @forelse($categories as $category)
                                                            @php
                                                                $mainLanguageId = \App\Models\Language::getMainLanguage()->id;
                                                                $categoryTranslate = $category->translate($mainLanguageId);
                                                            @endphp
                                                            <div class="category-item mb-2">
                                                                <div class="form-check">
                                                                    <input class="form-check-input category-checkbox" 
                                                                           type="checkbox" 
                                                                           value="{{ $category->id }}" 
                                                                           id="category_{{ $category->id }}"
                                                                           data-category-name="{{ $categoryTranslate->title ?? 'Untitled Category' }}">
                                                                    <label class="form-check-label" for="category_{{ $category->id }}">
                                                                        <strong>{{ $categoryTranslate->title ?? 'Untitled Category' }}</strong>
                                                                        @php
                                                                $totalProducts = $category->products()->count();
                                                                foreach($category->children as $child) {
                                                                    $totalProducts += $child->products()->count();
                                                                }
                                                            @endphp
                                                            <span class="text-muted">({{ $totalProducts }} {{ __('products') }})</span>
                                                                    </label>
                                                                </div>
                                                                
                                                                @if($category->children->isNotEmpty())
                                                                    <div class="ms-4 mt-2">
                                                                        @foreach($category->children as $child)
                                                                            @php
                                                                                $childTranslate = $child->translate($mainLanguageId);
                                                                            @endphp
                                                                            <div class="category-item mb-1">
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input category-checkbox subcategory" 
                                                                                           type="checkbox" 
                                                                                           value="{{ $child->id }}" 
                                                                                           id="category_{{ $child->id }}"
                                                                                           data-category-name="{{ $childTranslate->title ?? 'Untitled Subcategory' }}"
                                                                                           data-parent-id="{{ $category->id }}">
                                                                                    <label class="form-check-label" for="category_{{ $child->id }}">
                                                                                        {{ $childTranslate->title ?? 'Untitled Subcategory' }}
                                                                                        <span class="text-muted">({{ $child->products()->count() }} {{ __('products') }})</span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @empty
                                                            <p class="text-muted">{{ __('No categories found') }}</p>
                                                        @endforelse
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="includeSubcategories">
                                                        <label class="form-check-label" for="includeSubcategories">
                                                            {{ __('Include all subcategories when selecting main categories') }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-primary" onclick="addCategoryProducts()" title="{{ __('Add Selected Categories') }}">
                                                        <i class="material-icons-outlined">add_circle</i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger" onclick="removeCategoryProducts()" title="{{ __('Remove Selected Categories') }}">
                                                        <i class="material-icons-outlined">remove_circle</i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Products Section -->
                    <div class="row">
                        <div class="col-12">
                            @php
                                $companyProductsCount = $company->products()->count();
                            @endphp
                            <h5 class="mb-3">
                            <i class="material-icons-outlined me-2">inventory_2</i>
                            {{ __('Products') }} ({{ $products->total() }}) | {{ __('Company Products') }} ({{ $companyProductsCount }})
                        </h5>
                            
                            <!-- Search -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <form method="GET" action="{{ route('admin.campaigns.actions', $company) }}" class="d-flex">
                                        <input type="text" name="search" class="form-control me-2" 
                                               placeholder="{{ __('Search product...') }}" 
                                               value="{{ request('search') }}">
                                        <button type="submit" class="btn btn-outline-primary">
                                            <i class="material-icons-outlined">search</i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Products Grid -->
                            <div class="row">
                                @forelse($products as $product)
                                    @php
                                        $mainLanguageId = \App\Models\Language::getMainLanguage()->id;
                                        $mainTranslate = $product->translates->firstWhere('language_id', $mainLanguageId)
                                            ?? $product->translates->first();
                                        $isInAnyAction = $product->companyProducts->isNotEmpty();
                                    @endphp
                                    <div class="col-md-4 col-lg-3 mb-4">
                                        <div class="card h-100 {{ $isInAnyAction ? 'border-success' : '' }}">
                                            <div class="position-relative">
                                          
                                            @php
                                                $mainImage = $product->images->where('is_main', 1)->first();
                                            @endphp

                                            @if($mainTranslate && $mainImage && $mainImage->image)
                                                <img src="{{ asset('storage/' . $mainImage->image) }}"
                                                    class="card-img-top"
                                                    alt="{{ $mainTranslate->image_alt_text ?? __('Product Image') }}"
                                                    style="height: 200px; object-fit: contain;">
                                            @else
                                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                                    style="height: 200px;">
                                                    <i class="material-icons-outlined text-muted" style="font-size: 3rem;">image</i>
                                                </div>
                                            @endif
                                                
                                                <!-- Action Badge -->
                                                @if($isInAnyAction)
                                                    <div class="position-absolute top-0 end-0 m-2">
                                                        <span class="badge bg-success" title="{{ __('In company') }}">
                                                            <i class="material-icons-outlined" style="font-size: 1rem;">check</i>
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="card-body d-flex flex-column">
                                                <h6 class="card-title">{{ $mainTranslate->title ?? __('No Title') }}</h6>
                                                
                                                
                                                <div class="mt-auto">
                                                    @if($isInAnyAction)
                                                        <button type="button" class="btn btn-danger btn-sm w-100" 
                                                                onclick="removeFromCompany({{ $product->id }})"
                                                                title="{{ __('Remove from company') }}">
                                                            <i class="material-icons-outlined">remove</i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-success btn-sm w-100" 
                                                                onclick="addToCompany({{ $product->id }})"
                                                                title="{{ __('Add to company') }}">
                                                            <i class="material-icons-outlined">add</i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="text-center py-5">
                                            <i class="material-icons-outlined text-muted" style="font-size: 4rem;">inventory</i>
                                            <h5 class="text-muted mt-3">{{ __('No products found') }}</h5>
                                            <p class="text-muted">{{ __('Change your search criteria') }}</p>
                                        </div>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $products->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">{{ __('Loading...') }}</span>
                </div>
                <p class="mb-0">{{ __('Processing...') }}</p>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize category checkboxes when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Add onchange event to all category checkboxes
    const allCheckboxes = document.querySelectorAll('.category-checkbox');
    
    allCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            handleCategoryChange(this);
        });
    });
});

// Handle checkbox changes for categories and subcategories
function handleCategoryChange(checkbox) {
    const isMainCategory = !checkbox.classList.contains('subcategory');
    
    if (isMainCategory) {
        const categoryId = checkbox.value;
        const subcategories = document.querySelectorAll(`.subcategory[data-parent-id="${categoryId}"]`);
        
        if (checkbox.checked) {
            // If main category is checked, check all subcategories
            subcategories.forEach(sub => {
                sub.checked = true;
            });
        } else {
            // If main category is unchecked, uncheck all subcategories
            subcategories.forEach(sub => {
                sub.checked = false;
            });
        }
    }
}

function addCategoryProducts() {
    const selectedCategories = [];
    const categoryCheckboxes = document.querySelectorAll('.category-checkbox:checked');
    
    if (categoryCheckboxes.length === 0) {
        alert('{{ __('Please select at least one category') }}');
        return;
    }
    
    categoryCheckboxes.forEach(checkbox => {
        selectedCategories.push(checkbox.value);
    });
    
    const includeSubcategories = document.getElementById('includeSubcategories').checked;
    
    const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
    loadingModal.show();
    
    fetch('{{ route("admin.campaigns.add-category-products-to-action", $company) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            category_ids: selectedCategories,
            include_subcategories: includeSubcategories
        })
    })
    .then(response => response.json())
    .then(data => {
        loadingModal.hide();
        
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('{{ __('Error') }}: ' + data.message);
        }
    })
    .catch(error => {
        loadingModal.hide();
        console.error('Error:', error);
        alert('{{ __('Error occurred while adding category products') }}');
    });
}

function removeCategoryProducts() {
    const selectedCategories = [];
    const categoryCheckboxes = document.querySelectorAll('.category-checkbox:checked');
    
    if (categoryCheckboxes.length === 0) {
        alert('{{ __('Please select at least one category') }}');
        return;
    }
    
    if (!confirm('{{ __('Are you sure you want to remove products from selected categories?') }}')) {
        return;
    }
    
    categoryCheckboxes.forEach(checkbox => {
        selectedCategories.push(checkbox.value);
    });
    
    const includeSubcategories = document.getElementById('includeSubcategories').checked;
    
    const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
    loadingModal.show();
    
    fetch('{{ route("admin.campaigns.remove-category-products-from-action", $company) }}', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            category_ids: selectedCategories,
            include_subcategories: includeSubcategories
        })
    })
    .then(response => response.json())
    .then(data => {
        loadingModal.hide();
        
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('{{ __('Error') }}: ' + data.message);
        }
    })
    .catch(error => {
        loadingModal.hide();
        console.error('Error:', error);
        alert('{{ __('Error occurred while removing category products') }}');
    });
}


function addToCompany(productId) {
    const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
    loadingModal.show();
    
    fetch('{{ route("admin.campaigns.add-product-to-action", $company) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            product_id: productId
        })
    })
    .then(response => response.json())
    .then(data => {
        loadingModal.hide();
        
        if (data.success) {
            location.reload();
        } else {
            alert('{{ __('Error') }}: ' + data.message);
        }
    })
    .catch(error => {
        loadingModal.hide();
        console.error('Error:', error);
        alert('{{ __('Error occurred while adding product') }}');
    });
}

function removeFromCompany(productId) {
    if (!confirm('{{ __('Are you sure you want to remove this product from the company?') }}')) {
        return;
    }
    
    const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
    loadingModal.show();
    
    fetch('{{ route("admin.campaigns.remove-product-from-action", $company) }}', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            product_id: productId
        })
    })
    .then(response => response.json())
    .then(data => {
        loadingModal.hide();
        
        if (data.success) {
            location.reload();
        } else {
            alert('{{ __('Error') }}: ' + data.message);
        }
    })
    .catch(error => {
        loadingModal.hide();
        console.error('Error:', error);
        alert('{{ __('Error occurred while removing product') }}');
    });
}
</script>
@endsection

@extends('admin.layout.app')

@section('title', 'Add Hero Product')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Add Hero Product</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.hero-products.index') }}">Hero Products</a>
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
                <h6 class="alert-heading">Error occurred:</h6>
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
                <h6 class="alert-heading">Error occurred:</h6>
                <p class="mb-0">{{ session('error') }}</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>New Hero Product</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.hero-products.store') }}" method="POST">
                            @csrf
                            
                            <!-- Product Search -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Search Product <span class="text-danger">*</span>
                                    </label>
                                    <div class="position-relative">
                                        <input type="text" 
                                               class="form-control @error('product_id') is-invalid @enderror" 
                                               id="product_search" 
                                               placeholder="Start typing to search products..."
                                               autocomplete="off">
                                        <input type="hidden" 
                                               name="product_id" 
                                               id="product_id" 
                                               value="{{ old('product_id') }}" 
                                               required>
                                        <div id="search_results" class="position-absolute w-100 bg-white border rounded shadow-sm" style="top: 100%; left: 0; z-index: 1000; max-height: 300px; overflow-y: auto; display: none;"></div>
                                    </div>
                                    <small class="form-text text-muted">Type product name to search. Only active products will be shown.</small>
                                    @error('product_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Selected Product Display -->
                            <div class="row mb-3" id="selected_product_display" style="display: none;">
                                <div class="col-md-6">
                                    <label class="form-label">Selected Product</label>
                                    <div class="card border-success">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <i class="material-icons-outlined text-success me-2">check_circle</i>
                                                <div>
                                                    <h6 class="mb-1" id="selected_product_title"></h6>
                                                    <small class="text-muted" id="selected_product_category"></small>
                                                </div>
                                            </div>
                                        </div>
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
                                               name="status" 
                                               id="status" 
                                               value="1" 
                                               {{ old('status', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">
                                            Active
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Enable or disable this hero product</small>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">save</i>
                                            Add Product
                                        </button>
                                        <a href="{{ route('admin.hero-products.index') }}" class="btn btn-secondary">
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
    const searchInput = document.getElementById('product_search');
    const searchResults = document.getElementById('search_results');
    const productIdInput = document.getElementById('product_id');
    const selectedProductDisplay = document.getElementById('selected_product_display');
    const selectedProductTitle = document.getElementById('selected_product_title');
    const selectedProductCategory = document.getElementById('selected_product_category');
    
    let searchTimeout;

    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch('{{ route("admin.hero-products.search-products") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    search: query,
                    exclude_ids: [] // You can add logic to exclude already selected products
                })
            })
            .then(response => response.json())
            .then(data => {
                displaySearchResults(data);
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }, 300);
    });

    function displaySearchResults(results) {
        if (results.length === 0) {
            searchResults.innerHTML = '<div class="p-3 text-muted">No products found</div>';
        } else {
            searchResults.innerHTML = results.map(product => `
                <div class="search-result-item p-2 border-bottom" 
                     data-id="${product.id}" 
                     data-title="${product.title}" 
                     data-category="${product.category}"
                     style="cursor: pointer;">
                    <div class="d-flex align-items-center">
                        <i class="material-icons-outlined text-muted me-2" style="font-size: 16px;">inventory_2</i>
                        <div>
                            <div class="fw-bold">${product.title}</div>
                            <small class="text-muted">${product.category}</small>
                        </div>
                    </div>
                </div>
            `).join('');
        }
        searchResults.style.display = 'block';
    }

    // Handle search result selection
    searchResults.addEventListener('click', function(e) {
        const resultItem = e.target.closest('.search-result-item');
        if (resultItem) {
            const productId = resultItem.dataset.id;
            const productTitle = resultItem.dataset.title;
            const productCategory = resultItem.dataset.category;

            productIdInput.value = productId;
            searchInput.value = productTitle;
            selectedProductTitle.textContent = productTitle;
            selectedProductCategory.textContent = productCategory;
            
            searchResults.style.display = 'none';
            selectedProductDisplay.style.display = 'block';
        }
    });

    // Hide search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!productIdInput.value) {
            e.preventDefault();
            alert('Please select a product');
            return false;
        }
    });
});
</script>
@endpush 
@extends('admin.layout.app')

@section('title', 'Manage Action Products')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">Manage Products for Action</h4>
                            <p class="text-muted mb-0">
                                <strong>{{ $action->translate()->first()->title ?? 'No Title' }}</strong> 
                                - {{ $action->company->translate()->first()->title ?? 'No Company' }}
                            </p>
                        </div>
                        <a href="{{ route('admin.actions.index') }}" class="btn btn-secondary" title="Back to Actions">
                            <i class="material-icons-outlined">arrow_back</i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('admin.actions.products', $action) }}" class="d-flex">
                                <input type="text" name="search" class="form-control me-2" 
                                       placeholder="Search products..." 
                                       value="{{ request('search') }}">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="material-icons-outlined">search</i>
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="badge bg-info">
                                {{ $products->where('companyProducts', '!=', null)->count() }} products in this action
                            </span>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div class="row">
                        @forelse($products as $product)
                            @php
                                $mainTranslate = $product->translate()->first();
                                $isInAction = $product->companyProducts->isNotEmpty();
                            @endphp
                            <div class="col-md-4 col-lg-3 mb-4">
                                <div class="card h-100 {{ $isInAction ? 'border-success' : '' }}">
                                    <div class="position-relative">
                                        @if($mainTranslate && $mainTranslate->image)
                                            <img src="{{ asset('storage/' . $mainTranslate->image) }}" 
                                                 class="card-img-top" 
                                                 alt="{{ $mainTranslate->image_alt_text ?? 'Product Image' }}"
                                                 style="height: 200px; object-fit: cover;">
                                        @else
                                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                                 style="height: 200px;">
                                                <i class="material-icons-outlined text-muted" style="font-size: 3rem;">image</i>
                                            </div>
                                        @endif
                                        
                                        <!-- Action Badge -->
                                        @if($isInAction)
                                            <div class="position-absolute top-0 end-0 m-2">
                                                <span class="badge bg-success">
                                                    <i class="material-icons-outlined" style="font-size: 1rem;">local_offer</i>
                                                    In Action
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="card-body d-flex flex-column">
                                        <h6 class="card-title">{{ $mainTranslate->title ?? 'No Title' }}</h6>
                                        <p class="card-text text-muted small flex-grow-1">
                                            {{ Str::limit($mainTranslate->description ?? 'No description', 100) }}
                                        </p>
                                        
                                        <div class="mt-auto">
                                            @if($isInAction)
                                                <button type="button" class="btn btn-danger btn-sm w-100" 
                                                        onclick="removeProduct({{ $product->id }})"
                                                        title="Remove from Action">
                                                    <i class="material-icons-outlined">remove</i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-success btn-sm w-100" 
                                                        onclick="addProduct({{ $product->id }})"
                                                        title="Add to Action">
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
                                    <h5 class="text-muted mt-3">No products found</h5>
                                    <p class="text-muted">Try adjusting your search criteria</p>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $products->appends(request()->query())->links() }}
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
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mb-0">Processing...</p>
            </div>
        </div>
    </div>
</div>

<script>
function addProduct(productId) {
    const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
    loadingModal.show();
    
    fetch('{{ route("admin.actions.add-product", $action) }}', {
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
            // Reload the page to update the UI
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        loadingModal.hide();
        console.error('Error:', error);
        alert('An error occurred while adding the product');
    });
}

function removeProduct(productId) {
    if (!confirm('Are you sure you want to remove this product from the action?')) {
        return;
    }
    
    const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
    loadingModal.show();
    
    fetch('{{ route("admin.actions.remove-product", $action) }}', {
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
            // Reload the page to update the UI
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        loadingModal.hide();
        console.error('Error:', error);
        alert('An error occurred while removing the product');
    });
}
</script>
@endsection

@extends('admin.layout.app')

@section('title', 'Products for ' . ($productType->translates->first() ? $productType->translates->first()->title : 'Product Type'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Products for {{ $productType->translates->first() ? $productType->translates->first()->title : 'Product Type' }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.product-types.index') }}">Product Types</a>
                        </li>
                        <li class="breadcrumb-item active">Products</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">Success!</h6>
                <p class="mb-0">{{ session('success') }}</p>
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
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Products</h5>
                            <a href="{{ route('admin.product-types.index') }}" class="btn btn-secondary">
                                <i class="material-icons-outlined">arrow_back</i>
                                Back to Types
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search Form -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('admin.product-type-products.index', $productType) }}" class="d-flex">
                                    <input type="text" 
                                           name="search" 
                                           class="form-control me-2" 
                                           placeholder="Search products by title or description..."
                                           value="{{ request('search') }}">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="material-icons-outlined">search</i>
                                    </button>
                                    @if(request('search'))
                                        <a href="{{ route('admin.product-type-products.index', $productType) }}" class="btn btn-outline-secondary ms-2">
                                            <i class="material-icons-outlined">clear</i>
                                        </a>
                                    @endif
                                </form>
                            </div>
                        </div>

                        <!-- Results Summary -->
                        @if($products->total() > 0)
                            <div class="mb-3">
                                <small class="text-muted">
                                    Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} results
                                    @if(request('search'))
                                        for "{{ request('search') }}"
                                    @endif
                                </small>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">#</th>
                                        <th>Title</th>
                                        <th>Categories</th>
                                        <th>Types</th>
                                        <th>Status</th>
                                        <th width="200">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($products as $product)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="material-icons-outlined text-muted me-2" style="font-size: 16px;">inventory_2</i>
                                                    <div>
                                                        @php
                                                            $firstTranslation = $product->translates->first();
                                                        @endphp
                                                        <strong>{{ $firstTranslation ? $firstTranslation->title : 'No title' }}</strong>
                                                        @if($firstTranslation && $firstTranslation->description)
                                                            <br><small class="text-muted">{{ Str::limit($firstTranslation->description, 50) }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($product->categories->count() > 0)
                                                    @foreach($product->categories->take(3) as $category)
                                                        @php
                                                            $categoryTranslation = $category->translates->first();
                                                        @endphp
                                                        <span class="badge badge-light-primary me-1">
                                                            {{ $categoryTranslation ? $categoryTranslation->title : 'No title' }}
                                                        </span>
                                                    @endforeach
                                                    @if($product->categories->count() > 3)
                                                        <span class="badge badge-light-secondary">+{{ $product->categories->count() - 3 }}</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">No categories</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($product->types->count() > 0)
                                                    @foreach($product->types->take(3) as $type)
                                                        @php
                                                            $typeTranslation = $type->translates->first();
                                                        @endphp
                                                        <span class="badge badge-light-info me-1">
                                                            {{ $typeTranslation ? $typeTranslation->title : 'No title' }}
                                                        </span>
                                                    @endforeach
                                                    @if($product->types->count() > 3)
                                                        <span class="badge badge-light-secondary">+{{ $product->types->count() - 3 }}</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">No types</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($product->status)
                                                    <span class="badge badge-light-success">Active</span>
                                                @else
                                                    <span class="badge badge-light-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button type="button" 
                                                            class="btn btn-sm btn-icon {{ in_array($product->id, $homePageProductIds) ? 'btn-success' : 'btn-outline-success' }}"
                                                            onclick="toggleHomePage({{ $product->id }})"
                                                            title="{{ in_array($product->id, $homePageProductIds) ? 'Remove from homepage' : 'Add to homepage' }}">
                                                        <i class="material-icons-outlined">
                                                            {{ in_array($product->id, $homePageProductIds) ? 'home' : 'home_outline' }}
                                                        </i>
                                                    </button>
                                                    <a href="{{ route('admin.products.edit', $product) }}" 
                                                       class="btn btn-sm btn-icon btn-outline-primary" 
                                                       title="Edit Product">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="material-icons-outlined" style="font-size: 48px;">inbox</i>
                                                    <p class="mt-2">
                                                        @if(request('search'))
                                                            No products found for "{{ request('search') }}"
                                                        @else
                                                            No products found for this type
                                                        @endif
                                                    </p>
                                                    @if(request('search'))
                                                        <a href="{{ route('admin.product-type-products.index', $productType) }}" class="btn btn-outline-secondary">
                                                            <i class="material-icons-outlined">clear</i>
                                                            Clear Search
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($products->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $products->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.btn-icon {
    width: 32px;
    height: 32px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
}

.btn-icon i {
    font-size: 16px;
}
</style>
@endpush

@push('scripts')
<script>
function toggleHomePage(productId) {
    const button = event.target.closest('button');
    const icon = button.querySelector('i');
    
    fetch(`{{ route('admin.product-type-products.toggle-homepage', ['productType' => $productType, 'product' => ':productId']) }}`.replace(':productId', productId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update button appearance
            if (data.isOnHomepage) {
                button.classList.remove('btn-outline-success');
                button.classList.add('btn-success');
                icon.textContent = 'home';
                button.title = 'Remove from homepage';
            } else {
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-success');
                icon.textContent = 'home_outline';
                button.title = 'Add to homepage';
            }
            
            // Show success message
            const alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show';
            alert.innerHTML = `
                <h6 class="alert-heading">Success!</h6>
                <p class="mb-0">${data.message}</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            
            const container = document.querySelector('.container-fluid');
            container.insertBefore(alert, container.firstChild);
            
            // Auto dismiss after 3 seconds
            setTimeout(() => {
                alert.remove();
            }, 3000);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the product.');
    });
}
</script>
@endpush 
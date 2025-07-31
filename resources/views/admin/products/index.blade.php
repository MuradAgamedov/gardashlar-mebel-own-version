@extends('admin.layout.app')

@section('title', 'Products')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Products</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
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
                {{ session('success') }}
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
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Products</h5>
                            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                                <i class="material-icons-outlined">add</i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">#</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Type</th>
                                        <th>Views</th>
                                        <th>Discount</th>
                                        <th>Status</th>
                                        <th width="150">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable-products">
                                    @forelse($products->take(10) as $index => $product)
                                        <tr data-id="{{ $product->id }}">
                                            <td>
                                                <div class="drag-handle">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <circle cx="8" cy="8" r="1"></circle>
                                                        <circle cx="16" cy="8" r="1"></circle>
                                                        <circle cx="8" cy="16" r="1"></circle>
                                                        <circle cx="16" cy="16" r="1"></circle>
                                                    </svg>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="material-icons-outlined text-muted me-2" style="font-size: 16px;">inventory_2</i>
                                                    <div>
                                                        @php
                                                            $firstTranslation = $product->translates->first();
                                                        @endphp
                                                        <strong>{{ $firstTranslation ? $firstTranslation->title : 'No title' }}</strong>
                                                        @if($product->tags->count() > 0)
                                                            <div class="mt-1">
                                                                @foreach($product->tags->take(3) as $tag)
                                                                    <span class="badge bg-light text-dark me-1" style="font-size: 10px;">
                                                                        {{ $tag->translate()->title ?? 'No title' }}
                                                                    </span>
                                                                @endforeach
                                                                @if($product->tags->count() > 3)
                                                                    <span class="badge bg-secondary" style="font-size: 10px;">
                                                                        +{{ $product->tags->count() - 3 }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-light-primary">
                                                    {{ $product->category->translate()->title ?? 'No category' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-light-info">
                                                    {{ $product->type->translate()->title ?? 'No type' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-light-warning">
                                                    <i class="material-icons-outlined" style="font-size: 12px;">visibility</i>
                                                    {{ $product->views_count }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($product->discount_percent > 0)
                                                    <span class="badge badge-light-danger">
                                                        <i class="material-icons-outlined" style="font-size: 12px;">local_offer</i>
                                                        {{ $product->discount_percent }}%
                                                    </span>
                                                @else
                                                    <span class="badge badge-light-secondary">No discount</span>
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
                                                    <a href="{{ route('admin.products.accordions.index', $product) }}" 
                                                       class="btn btn-sm btn-outline-info" 
                                                       title="Accordions">
                                                        <i class="material-icons-outlined">expand_more</i>
                                                    </a>
                                                    <a href="{{ route('admin.products.images.index', $product) }}" 
                                                       class="btn btn-sm btn-outline-success" 
                                                       title="Images">
                                                        <i class="material-icons-outlined">image</i>
                                                    </a>
                                                    <a href="{{ route('admin.products.edit', $product) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="Edit">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    <form action="{{ route('admin.products.destroy', $product) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this product?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-danger" 
                                                                title="Delete">
                                                            <i class="material-icons-outlined">delete</i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        @if(($index + 1) % 10 == 0 && $index < $products->count() - 1)
                                            <tr class="load-more-row" data-page="{{ ceil(($index + 1) / 10) + 1 }}">
                                                <td colspan="8" class="text-center py-3">
                                                    <button class="btn btn-outline-primary load-more-btn" 
                                                            onclick="loadMoreProducts({{ ceil(($index + 1) / 10) + 1 }}, this)">
                                                        <i class="material-icons-outlined">expand_more</i>
                                                        Load More Products
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="material-icons-outlined" style="font-size: 48px;">inventory_2</i>
                                                    <p class="mt-2">No products added yet</p>
                                                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                                                        Add First Product
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        @if($products->count() > 10)
                            <div class="text-center mt-3">
                                <button class="btn btn-primary load-more-btn" 
                                        onclick="loadMoreProducts(2, this)"
                                        id="initial-load-more">
                                    <i class="material-icons-outlined">expand_more</i>
                                    Load More Products
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
let currentPage = 1;
let isLoading = false;

document.addEventListener('DOMContentLoaded', function() {
    const sortableList = document.getElementById('sortable-products');
    
    if (sortableList) {
        const sortable = new Sortable(sortableList, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const items = Array.from(sortableList.children).map((tr, index) => {
                    return tr.getAttribute('data-id');
                }).filter(id => id !== null);

                // Send reorder request
                fetch('{{ route("admin.products.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ items: items })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        const alert = document.createElement('div');
                        alert.className = 'alert alert-success alert-dismissible fade show';
                        alert.innerHTML = `
                            Order updated successfully
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        `;
                        document.querySelector('.container-fluid').insertBefore(alert, document.querySelector('.row'));
                        
                        // Auto remove after 3 seconds
                        setTimeout(() => {
                            alert.remove();
                        }, 3000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });
        sortableList._sortable = sortable;
    }
});

function loadMoreProducts(page, button) {
    if (isLoading) return;
    
    isLoading = true;
    button.disabled = true;
    button.innerHTML = '<i class="material-icons-outlined">hourglass_empty</i> Loading...';
    
    fetch(`{{ route('admin.products.load-more') }}?page=${page}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        const tbody = document.getElementById('sortable-products');
        const loadMoreRow = button.closest('tr');
        
        // Remove the load more button row
        if (loadMoreRow) {
            loadMoreRow.remove();
        }
        
        // Add new products
        data.products.forEach((product, index) => {
            const row = createProductRow(product, data.products.length * (page - 1) + index + 1);
            tbody.appendChild(row);
            
            // Add load more button after every 10th product
            if ((index + 1) % 10 === 0 && index < data.products.length - 1) {
                const loadMoreRow = createLoadMoreRow(page + 1);
                tbody.appendChild(loadMoreRow);
            }
        });
        
        // If there are more pages, add final load more button
        if (data.hasMorePages) {
            const finalLoadMoreRow = createLoadMoreRow(page + 1);
            tbody.appendChild(finalLoadMoreRow);
        }
        
        // Remove initial load more button if it exists
        const initialLoadMore = document.getElementById('initial-load-more');
        if (initialLoadMore) {
            initialLoadMore.closest('.text-center').remove();
        }
        
        // Reinitialize Sortable for new elements
        initializeSortable();
        
    })
    .catch(error => {
        console.error('Error:', error);
        button.innerHTML = '<i class="material-icons-outlined">error</i> Error loading products';
    })
    .finally(() => {
        isLoading = false;
    });
}

function createProductRow(product, index) {
    const row = document.createElement('tr');
    row.setAttribute('data-id', product.id);
    
    const firstTranslation = product.translates && product.translates.length > 0 ? product.translates[0] : null;
    const categoryTitle = product.category && product.category.translate ? product.category.translate().title : 'No category';
    const typeTitle = product.type && product.type.translate ? product.type.translate().title : 'No type';
    
    let tagsHtml = '';
    if (product.tags && product.tags.length > 0) {
        const tags = product.tags.slice(0, 3);
        tags.forEach(tag => {
            const tagTitle = tag.translate ? tag.translate().title : 'No title';
            tagsHtml += `<span class="badge bg-light text-dark me-1" style="font-size: 10px;">${tagTitle}</span>`;
        });
        if (product.tags.length > 3) {
            tagsHtml += `<span class="badge bg-secondary" style="font-size: 10px;">+${product.tags.length - 3}</span>`;
        }
    }
    
    row.innerHTML = `
        <td>
            <div class="drag-handle">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="8" cy="8" r="1"></circle>
                    <circle cx="16" cy="8" r="1"></circle>
                    <circle cx="8" cy="16" r="1"></circle>
                    <circle cx="16" cy="16" r="1"></circle>
                </svg>
            </div>
        </td>
        <td>
            <div class="d-flex align-items-center">
                <i class="material-icons-outlined text-muted me-2" style="font-size: 16px;">inventory_2</i>
                <div>
                    <strong>${firstTranslation ? firstTranslation.title : 'No title'}</strong>
                    ${tagsHtml ? `<div class="mt-1">${tagsHtml}</div>` : ''}
                </div>
            </div>
        </td>
        <td>
            <span class="badge badge-light-primary">${categoryTitle}</span>
        </td>
        <td>
            <span class="badge badge-light-info">${typeTitle}</span>
        </td>
        <td>
            <span class="badge badge-light-warning">
                <i class="material-icons-outlined" style="font-size: 12px;">visibility</i>
                ${product.views_count || 0}
            </span>
        </td>
        <td>
            ${product.discount_percent > 0 ? 
                `<span class="badge badge-light-danger">
                    <i class="material-icons-outlined" style="font-size: 12px;">local_offer</i>
                    ${product.discount_percent}%
                </span>` : 
                '<span class="badge badge-light-secondary">No discount</span>'
            }
        </td>
        <td>
            ${product.status ? 
                '<span class="badge badge-light-success">Active</span>' : 
                '<span class="badge badge-light-danger">Inactive</span>'
            }
        </td>
        <td>
            <div class="d-flex gap-2">
                <a href="/admin/products/${product.id}/accordions" 
                   class="btn btn-sm btn-outline-info" 
                   title="Accordions">
                    <i class="material-icons-outlined">expand_more</i>
                </a>
                <a href="/admin/products/${product.id}/images" 
                   class="btn btn-sm btn-outline-success" 
                   title="Images">
                    <i class="material-icons-outlined">image</i>
                </a>
                <a href="/admin/products/${product.id}/edit" 
                   class="btn btn-sm btn-outline-primary" 
                   title="Edit">
                    <i class="material-icons-outlined">edit</i>
                </a>
                <form action="/admin/products/${product.id}" 
                      method="POST" 
                      class="d-inline"
                      onsubmit="return confirm('Are you sure you want to delete this product?')">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" 
                            class="btn btn-sm btn-outline-danger" 
                            title="Delete">
                        <i class="material-icons-outlined">delete</i>
                    </button>
                </form>
            </div>
        </td>
    `;
    
    return row;
}

function createLoadMoreRow(page) {
    const row = document.createElement('tr');
    row.className = 'load-more-row';
    row.setAttribute('data-page', page);
    
    row.innerHTML = `
        <td colspan="8" class="text-center py-3">
            <button class="btn btn-outline-primary load-more-btn" 
                    onclick="loadMoreProducts(${page}, this)">
                <i class="material-icons-outlined">expand_more</i>
                Load More Products
            </button>
        </td>
    `;
    
    return row;
}

function initializeSortable() {
    const sortableList = document.getElementById('sortable-products');
    if (sortableList && !sortableList._sortable) {
        const sortable = new Sortable(sortableList, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const items = Array.from(sortableList.children).map((tr, index) => {
                    return tr.getAttribute('data-id');
                }).filter(id => id !== null);

                fetch('{{ route("admin.products.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ items: items })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const alert = document.createElement('div');
                        alert.className = 'alert alert-success alert-dismissible fade show';
                        alert.innerHTML = `
                            Order updated successfully
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        `;
                        document.querySelector('.container-fluid').insertBefore(alert, document.querySelector('.row'));
                        
                        setTimeout(() => {
                            alert.remove();
                        }, 3000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });
        sortableList._sortable = sortable;
    }
}
</script>
@endpush 
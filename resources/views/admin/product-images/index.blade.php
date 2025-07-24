@extends('admin.layout.app')

@section('title', 'Product Images')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Product Images</h3>
                    <p class="text-muted">{{ $product->translate()->title ?? 'No title' }}</p>
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
                        <li class="breadcrumb-item active">Images</li>
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
                            <h5>Images for: {{ $product->translate()->title ?? 'No title' }}</h5>
                            <a href="{{ route('admin.products.images.create', $product) }}" class="btn btn-primary">
                                <i class="material-icons-outlined">add</i>
                                Upload Images
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($images->count() > 0)
                            <div class="row" id="sortable-images">
                                @foreach($images as $image)
                                    <div class="col-md-3 col-sm-6 mb-4" data-id="{{ $image->id }}">
                                        <div class="card h-100">
                                            <div class="position-relative">
                                                <img src="{{ $image->image_url }}" 
                                                     class="card-img-top" 
                                                     alt="Product Image"
                                                     style="height: 200px; object-fit: cover;">
                                                
                                                @if($image->is_main)
                                                    <div class="position-absolute top-0 start-0 m-2">
                                                        <span class="badge bg-success">
                                                            <i class="material-icons-outlined" style="font-size: 12px;">star</i>
                                                            Main
                                                        </span>
                                                    </div>
                                                @endif
                                                
                                                <div class="position-absolute top-0 end-0 m-2">
                                                    <div class="drag-handle">
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" style="filter: drop-shadow(1px 1px 2px rgba(0,0,0,0.8));">
                                                            <circle cx="8" cy="8" r="1"></circle>
                                                            <circle cx="16" cy="8" r="1"></circle>
                                                            <circle cx="8" cy="16" r="1"></circle>
                                                            <circle cx="16" cy="16" r="1"></circle>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">Order: {{ $image->order }}</small>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input status-toggle" 
                                                               type="checkbox" 
                                                               data-id="{{ $image->id }}"
                                                               {{ $image->status ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                                
                                                <div class="d-flex gap-1 mt-2">
                                                    @if(!$image->is_main)
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-primary set-main-btn"
                                                                data-id="{{ $image->id }}"
                                                                title="Set as Main">
                                                            <i class="material-icons-outlined">star</i>
                                                        </button>
                                                    @endif
                                                    
                                                    <a href="{{ route('admin.products.images.edit', [$product, $image]) }}" 
                                                       class="btn btn-sm btn-outline-warning"
                                                       title="Edit">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    
                                                    <form action="{{ route('admin.products.images.destroy', [$product, $image]) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this image?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-danger"
                                                                title="Delete">
                                                            <i class="material-icons-outlined">delete</i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="text-muted">
                                    <i class="material-icons-outlined" style="font-size: 64px;">image</i>
                                    <h5 class="mt-3">No images uploaded yet</h5>
                                    <p>Upload images for this product to get started.</p>
                                    <a href="{{ route('admin.products.images.create', $product) }}" class="btn btn-primary">
                                        <i class="material-icons-outlined">upload</i>
                                        Upload First Image
                                    </a>
                                </div>
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
document.addEventListener('DOMContentLoaded', function() {
    const sortableContainer = document.getElementById('sortable-images');
    
    if (sortableContainer) {
        new Sortable(sortableContainer, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const items = Array.from(sortableContainer.children).map((item, index) => ({
                    id: item.dataset.id,
                    order: index + 1
                }));
                
                fetch('{{ route("admin.products.images.reorder", $product) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ items: items.map(item => item.id) })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update order numbers
                        items.forEach((item, index) => {
                            const card = sortableContainer.querySelector(`[data-id="${item.id}"]`);
                            const orderText = card.querySelector('.text-muted');
                            if (orderText) {
                                orderText.textContent = `Order: ${item.order}`;
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error reordering images:', error);
                });
            }
        });
    }

    // Set main image functionality
    document.querySelectorAll('.set-main-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const imageId = this.dataset.id;
            
            fetch(`{{ route('admin.products.images.set-main', ['product' => $product, 'image' => ':imageId']) }}`.replace(':imageId', imageId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error setting main image:', error);
            });
        });
    });
});
</script>
@endpush 
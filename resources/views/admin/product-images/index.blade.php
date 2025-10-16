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
                        <h5 class="mb-0">Images for: {{ $product->translate()->title ?? 'No title' }}</h5>

                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('admin.products.images.create', $product) }}" class="btn btn-primary">
                                <i class="material-icons-outlined">add</i>
                            </a>
                            <a href="{{ route('admin.products.index', ['page' => $currentPage ?? 1]) }}" class="btn btn-secondary">
                                <i class="material-icons-outlined">arrow_back</i>
                            </a>
                        </div>
                    </div>

                    </div>
                    <div class="card-body">
                        <!-- Filter Section -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center gap-3">
                                    <label class="form-label mb-0">Filter by Color:</label>
                                    <div class="color-filter-container">
                                        <a href="{{ route('admin.products.images.index', $product) }}" 
                                           class="color-filter-option {{ !request('color_id') ? 'active' : '' }}"
                                           title="All Colors">
                                            <div class="color-filter-circle" style="background: linear-gradient(45deg, #f0f0f0 25%, transparent 25%), linear-gradient(-45deg, #f0f0f0 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #f0f0f0 75%), linear-gradient(-45deg, transparent 75%, #f0f0f0 75%); background-size: 8px 8px; background-position: 0 0, 0 4px, 4px -4px, -4px 0px;">
                                            </div>
                                        </a>
                                        
                                        <a href="{{ route('admin.products.images.index', $product) }}?color_id=no-color" 
                                           class="color-filter-option {{ request('color_id') === 'no-color' ? 'active' : '' }}"
                                           title="No Color">
                                            <div class="color-filter-circle" style="background: repeating-linear-gradient(45deg, transparent, transparent 2px, #ccc 2px, #ccc 4px);">
                                            </div>
                                        </a>
                                        
                                        @foreach($colors as $color)
                                            <a href="{{ route('admin.products.images.index', $product) }}?color_id={{ $color->id }}" 
                                               class="color-filter-option {{ request('color_id') == $color->id ? 'active' : '' }}"
                                               title="{{ $color->color_code }}">
                                                <div class="color-filter-circle" style="background-color: {{ $color->color_code }};"></div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <small class="text-muted">
                                    Showing {{ $images->count() }} image(s)
                                    @if(request('color_id'))
                                        @if(request('color_id') === 'no-color')
                                            without color
                                        @else
                                            with color: {{ $colors->find(request('color_id'))->color_code ?? '' }}
                                        @endif
                                    @endif
                                </small>
                            </div>
                        </div>
                        
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
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <small class="text-muted">Order: {{ $image->order }}</small>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input status-toggle" 
                                                               type="checkbox" 
                                                               data-id="{{ $image->id }}"
                                                               {{ $image->status ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                                
                                                @if($image->color)
                                                    <div class="mb-2">
                                                        <small class="text-muted">Color:</small>
                                                        <div class="d-flex align-items-center mt-1">
                                                            <div class="color-preview me-2" 
                                                                 style="width: 16px; height: 16px; background-color: {{ $image->color->color_code }}; border-radius: 3px; border: 1px solid #ddd;"></div>
                                                            <small>{{ $image->color->color_code }}</small>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if($image->translates->count() > 0)
                                                    <div class="mb-2">
                                                        <small class="text-muted">Alt Text:</small>
                                                        <div class="mt-1">
                                                            @foreach($image->translates->take(2) as $translate)
                                                                <div class="d-flex align-items-center mb-1">
                                                                    <span class="badge badge-light-primary me-2" style="font-size: 10px;">{{ $translate->language->lang_code ?? 'N/A' }}</span>
                                                                    <small class="text-truncate" style="max-width: 120px;" title="{{ $translate->alt_text }}">{{ $translate->alt_text }}</small>
                                                                </div>
                                                            @endforeach
                                                            @if($image->translates->count() > 2)
                                                                <small class="text-muted">+{{ $image->translates->count() - 2 }} more...</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="mb-2">
                                                        <small class="text-muted">Alt Text:</small>
                                                        <div class="mt-1">
                                                            <small class="text-warning">No alt text</small>
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                                <div class="d-flex gap-1 mt-2">
                                                    @if(!$image->is_main)
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-primary set-main-btn"
                                                                data-id="{{ $image->id }}"
                                                                title="Set as Main">
                                                            <i class="material-icons-outlined">star</i>
                                                        </button>
                                                    @endif
                                                    
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-info alt-text-btn"
                                                            data-id="{{ $image->id }}"
                                                            title="Edit Alt Text">
                                                        <i class="material-icons-outlined">text_fields</i>
                                                    </button>
                                                    
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

<!-- Alt Text Modal -->
<div class="modal fade" id="altTextModal" tabindex="-1" aria-labelledby="altTextModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="altTextModalLabel">Edit Alt Text</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="altTextForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <img id="modalImage" src="" alt="Product Image" class="img-fluid rounded">
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">Alt Text Translations</h6>
                            <div id="languageInputs">
                                @foreach($languages as $index => $language)
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <span class="badge badge-light-primary me-2">{{ $language->lang_code }}</span>
                                            {{ $language->title }}
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               name="translates[{{ $index }}][alt_text]" 
                                               placeholder="Enter alt text for {{ $language->title }}"
                                               data-lang-id="{{ $language->id }}">
                                        <input type="hidden" name="translates[{{ $index }}][language_id]" value="{{ $language->id }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="material-icons-outlined">cancel</i>
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="material-icons-outlined">save</i>
                        Save Alt Text
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.color-filter-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
}

.color-filter-option {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    color: #666;
    padding: 4px;
    border-radius: 4px;
    transition: all 0.2s;
    border: 2px solid transparent;
}

.color-filter-option:hover {
    background-color: #f8f9fa;
    color: #333;
    text-decoration: none;
}

.color-filter-option.active {
    background-color: #e3f2fd;
    border-color: #2196f3;
    color: #2196f3;
}

.color-filter-circle {
    width: 25px;
    height: 25px;
    border-radius: 50%;
    border: 1px solid #ddd;
    display: flex;
    align-items: center;
    justify-content: center;
}

.color-filter-option.active .color-filter-circle {
    border-color: #2196f3;
    box-shadow: 0 0 3px rgba(33, 150, 243, 0.3);
}

/* Drag handle styling */
.drag-handle {
    cursor: grab !important;
    color: #6c757d;
    transition: all 0.2s ease;
    background: rgba(255, 255, 255, 0.95) !important;
    border: 1px solid rgba(0, 0, 0, 0.15);
    padding: 6px !important;
    border-radius: 6px;
    backdrop-filter: blur(4px);
    z-index: 10;
    position: relative;
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}

.drag-handle:hover {
    color: #495057;
    background: rgba(255, 255, 255, 1) !important;
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    cursor: grabbing !important;
}

.drag-handle:active {
    cursor: grabbing !important;
    transform: scale(0.95);
    background: rgba(255, 255, 255, 0.8) !important;
}

.drag-handle * {
    pointer-events: none;
}

.sortable-ghost {
    opacity: 0.5;
}

.sortable-chosen {
    transform: rotate(5deg);
}

</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing product images sortable...');
    
    const sortableContainer = document.getElementById('sortable-images');
    console.log('Product images sortable container:', sortableContainer);
    
    if (sortableContainer) {
        // Check if Sortable is available
        if (typeof Sortable === 'undefined') {
            console.error('Sortable.js is not loaded!');
            return;
        }
        
        // Check if drag handles exist
        const dragHandles = sortableContainer.querySelectorAll('.drag-handle');
        console.log('Found product image drag handles:', dragHandles.length);
        
        if (dragHandles.length === 0) {
            console.error('No drag handles found!');
            return;
        }
        
        const sortable = new Sortable(sortableContainer, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            forceFallback: false,
            fallbackClass: 'sortable-fallback',
            onStart: function(evt) {
                console.log('Product image drag started:', evt);
            },
            onEnd: function(evt) {
                console.log('Product image drag ended:', evt);
                const items = Array.from(sortableContainer.children).map((item, index) => ({
                    id: item.dataset.id,
                    order: index + 1
                }));
                
                fetch('{{ route("admin.products.images.reorder", $product) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ items: items.map(item => item.id) })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Update order numbers silently
                        items.forEach((item, index) => {
                            const card = sortableContainer.querySelector(`[data-id="${item.id}"]`);
                            const orderText = card.querySelector('.text-muted');
                            if (orderText) {
                                orderText.textContent = `Order: ${item.order}`;
                            }
                        });
                        console.log('Product images reordered successfully');
                    } else {
                        console.error('Failed to reorder product images:', data.error);
                    }
                })
                .catch(error => {
                    console.error('Error reordering product images:', error);
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

    // Alt Text Modal functionality
    document.querySelectorAll('.alt-text-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const imageId = this.dataset.id;
            const imageCard = this.closest('.card');
            const imageSrc = imageCard.querySelector('img').src;
            
            // Set modal image
            document.getElementById('modalImage').src = imageSrc;
            
            // Load existing alt texts
            loadAltTexts(imageId);
            
            // Store image ID for form submission
            document.getElementById('altTextForm').dataset.imageId = imageId;
            
            // Show modal
            new bootstrap.Modal(document.getElementById('altTextModal')).show();
        });
    });

    // Load existing alt texts
    function loadAltTexts(imageId) {
        fetch(`{{ route('admin.products.images.get-alt-texts', ['product' => $product, 'image' => ':imageId']) }}`.replace(':imageId', imageId))
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear all inputs first
                    document.querySelectorAll('#languageInputs input[type="text"]').forEach(input => {
                        input.value = '';
                    });
                    
                    // Fill existing translations
                    data.translations.forEach(translation => {
                        const input = document.querySelector(`input[data-lang-id="${translation.language_id}"]`);
                        if (input) {
                            input.value = translation.alt_text || '';
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error loading alt texts:', error);
            });
    }

    // Handle form submission
    document.getElementById('altTextForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const imageId = this.dataset.imageId;
        const formData = new FormData(this);
        
        // Convert FormData to JSON
        const translates = [];
        const inputs = this.querySelectorAll('input[name*="[alt_text]"]');
        const hiddenInputs = this.querySelectorAll('input[name*="[language_id]"]');
        
        inputs.forEach((input, index) => {
            translates.push({
                language_id: hiddenInputs[index].value,
                alt_text: input.value
            });
        });

        fetch(`{{ route('admin.products.images.update-alt-text', ['product' => $product, 'image' => ':imageId']) }}`.replace(':imageId', imageId), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ translates: translates })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hide modal
                bootstrap.Modal.getInstance(document.getElementById('altTextModal')).hide();
                
                // Show success message
                showAlert('success', data.message);
            } else {
                showAlert('danger', data.error || 'An error occurred');
            }
        })
        .catch(error => {
            console.error('Error updating alt text:', error);
            showAlert('danger', 'An error occurred while updating alt text');
        });
    });

    // Show alert function
    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        const container = document.querySelector('.container-fluid .row');
        container.insertAdjacentHTML('beforebegin', alertHtml);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }
});
</script>
@endpush 
@extends('admin.layout.app')

@section('title', 'Edit Image')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Edit Image</h3>
                    <p class="text-muted">Product: {{ $product->translate()->title ?? 'No title' }}</p>
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
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.products.images.index', $product) }}">Images</a>
                        </li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Edit Product Image</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.products.images.update', [$product, $image]) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Current Image</label>
                                        <div class="mb-3">
                                            <img src="{{ $image->image_url }}" 
                                                 class="img-fluid rounded current-image" 
                                                 alt="Current Image"
                                                 style="max-height: 300px;">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="image" class="form-label">Replace Image</label>
                                        <input type="file" 
                                               class="form-control @error('image') is-invalid @enderror" 
                                               id="image" 
                                               name="image" 
                                               accept="image/*">
                                        <div class="form-text">
                                            Leave empty to keep current image. Supported formats: JPEG, PNG, JPG, GIF, WEBP. Max size: 2MB.
                                        </div>
                                        @error('image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group mt-3">
                                        <label for="product_color_id" class="form-label">
                                            Product Color
                                        </label>
                                        <input type="hidden" 
                                               name="product_color_id" 
                                               id="product_color_id" 
                                               value="{{ old('product_color_id', $image->product_color_id) }}">
                                        
                                        <div class="color-selection-container">
                                            <div class="color-option-circle" 
                                                 data-value="" 
                                                 onclick="selectColor('', 'No Color')"
                                                 title="No Color">
                                                <div class="color-circle" style="background: repeating-linear-gradient(45deg, transparent, transparent 2px, #ccc 2px, #ccc 4px);">
                                                    <i class="material-icons-outlined" style="font-size: 16px; color: #999;">close</i>
                                                </div>
                                                <small class="color-label">No Color</small>
                                            </div>
                                            
                                            @foreach($colors as $color)
                                                <div class="color-option-circle" 
                                                     data-value="{{ $color->id }}" 
                                                     onclick="selectColor('{{ $color->id }}', '{{ $color->color_code }}')"
                                                     title="{{ $color->color_code }}">
                                                    <div class="color-circle" style="background-color: {{ $color->color_code }};"></div>
                                                    <small class="color-label">{{ $color->color_code }}</small>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="form-text">
                                            Select a color to associate with this image (optional).
                                        </div>
                                        @error('product_color_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group mt-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ $image->status ? 'checked' : '' }}>
                                            <label class="form-check-label" for="status">
                                                Active
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">save</i>
                                            Update Image
                                        </button>
                                        <a href="{{ route('admin.products.images.index', $product) }}" class="btn btn-secondary">
                                            <i class="material-icons-outlined">arrow_back</i>
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
function selectColor(value, text) {
    document.getElementById('product_color_id').value = value;
    
    // Update visual selection
    document.querySelectorAll('.color-option-circle').forEach(option => {
        option.classList.remove('selected');
    });
    
    const selectedOption = document.querySelector(`[data-value="${value}"]`);
    if (selectedOption) {
        selectedOption.classList.add('selected');
    }
}

// Initialize selected color on page load
document.addEventListener('DOMContentLoaded', function() {
    const selectedValue = document.getElementById('product_color_id').value;
    if (selectedValue) {
        const selectedOption = document.querySelector(`[data-value="${selectedValue}"]`);
        if (selectedOption) {
            selectedOption.classList.add('selected');
        }
    }
    
    // Handle image preview when new image is selected
    const imageInput = document.getElementById('image');
    const currentImage = document.querySelector('.current-image');
    
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                currentImage.src = e.target.result;
                currentImage.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.color-selection-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #f9f9f9;
}

.color-option-circle {
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    padding: 5px;
    border-radius: 5px;
    border: 2px solid transparent;
    min-width: 60px;
}

.color-option-circle:hover {
    background-color: #e9ecef;
}

.color-option-circle.selected {
    background-color: #d1ecf1;
    border-color: #17a2b8;
}

.color-circle {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    border: 2px solid #ccc;
    margin-bottom: 3px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.color-option-circle.selected .color-circle {
    border-color: #17a2b8;
    box-shadow: 0 0 5px rgba(23, 162, 184, 0.5);
}

.color-label {
    font-size: 10px;
    text-align: center;
    color: #555;
    max-width: 50px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.color-option-circle.selected .color-label {
    color: #17a2b8;
    font-weight: bold;
}
</style>
@endpush 
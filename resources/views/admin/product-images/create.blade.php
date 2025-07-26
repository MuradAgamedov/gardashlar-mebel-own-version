@extends('admin.layout.app')

@section('title', 'Upload Images')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Upload Images</h3>
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
                        <li class="breadcrumb-item active">Upload</li>
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
                        <h5>Upload Product Images</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.products.images.store', $product) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="images" class="form-label">
                                            Select Images
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" 
                                               class="form-control @error('images') is-invalid @enderror" 
                                               id="images" 
                                               name="images[]" 
                                               multiple 
                                               accept="image/*"
                                               required>
                                        <div class="form-text">
                                            You can select multiple images. Supported formats: JPEG, PNG, JPG, GIF, WEBP. Max size: 2MB per image.
                                        </div>
                                        @error('images')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @error('images.*')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <!-- Image preview area -->
                                        <div id="image-preview-container" class="row mt-3"></div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="product_color_id" class="form-label">
                                            Product Color
                                        </label>
                                        <input type="hidden" 
                                               name="product_color_id" 
                                               id="product_color_id" 
                                               value="{{ old('product_color_id') }}">
                                        
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
                                            Select a color to associate with these images (optional).
                                        </div>
                                        @error('product_color_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">upload</i>
                                            Upload Images
                                        </button>
                                        <a href="{{ route('admin.products.images.index', $product) }}" class="btn btn-secondary">
                                            <i class="material-icons-outlined">arrow_back</i>
                                            Back to List
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
});

// Image preview functionality
const imagesInput = document.getElementById('images');
const previewContainer = document.getElementById('image-preview-container');

imagesInput.addEventListener('change', function(event) {
    previewContainer.innerHTML = '';
    const files = event.target.files;
    if (files) {
        Array.from(files).forEach(file => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-4 col-md-3 mb-2';
                    col.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded border" style="max-height:120px;object-fit:cover;" alt="Preview">`;
                    previewContainer.appendChild(col);
                };
                reader.readAsDataURL(file);
            }
        });
    }
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
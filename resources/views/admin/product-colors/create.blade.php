@extends('admin.layout.app')

@section('title', 'Add Product Color')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Add Product Color</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.product-colors.index') }}">Product Colors</a>
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
                        <h5>New Product Color</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.product-colors.store') }}" method="POST">
                            @csrf
                            
                            <!-- Color Code -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Color Code <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="color" 
                                               class="form-control form-control-color @error('color_code') is-invalid @enderror" 
                                               id="color_picker"
                                               value="{{ old('color_code', '#000000') }}"
                                               style="width: 60px; height: 38px;"
                                               title="Color picker (hex only)">
                                        <input type="text" 
                                               class="form-control @error('color_code') is-invalid @enderror" 
                                               name="color_code" 
                                               id="color_code" 
                                               value="{{ old('color_code', '#000000') }}"
                                               placeholder="#FF0000 or rgba(255,0,0,0.5)"
                                               required>
                                    </div>
                                    <small class="form-text text-muted">Select a color or enter hex code (e.g., #FF0000) or rgba (e.g., rgba(255,0,0,0.5))</small>
                                    @error('color_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Color Preview</label>
                                    <div class="color-preview" 
                                         id="color_preview"
                                         style="width: 100px; height: 100px; border-radius: 12px; border: 3px solid #ddd; margin-top: 8px;">
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
                                        <a href="{{ route('admin.product-colors.index') }}" class="btn btn-secondary">
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorPicker = document.getElementById('color_picker');
    const colorCode = document.getElementById('color_code');
    const colorPreview = document.getElementById('color_preview');
    
    // Update color code when color picker changes
    colorPicker.addEventListener('input', function() {
        colorCode.value = this.value;
        colorPreview.style.backgroundColor = this.value;
    });
    
    // Update color picker and preview when color code changes
    colorCode.addEventListener('input', function() {
        const value = this.value;
        if (value.match(/^#[0-9A-F]{6}$/i) || value.match(/^rgba?\([^)]+\)$/i)) {
            if (value.match(/^#[0-9A-F]{6}$/i)) {
                colorPicker.value = value;
                colorPicker.style.display = 'block';
            } else if (value.match(/^rgba?\([^)]+\)$/i)) {
                colorPicker.style.display = 'none';
            }
            colorPreview.style.backgroundColor = value;
        }
    });
    
    // Initialize preview and color picker visibility
    colorPreview.style.backgroundColor = colorCode.value;
    if (colorCode.value.match(/^rgba?\([^)]+\)$/i)) {
        colorPicker.style.display = 'none';
    }
});
</script>
@endpush 
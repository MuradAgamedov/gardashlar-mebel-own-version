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
                                <div class="col-md-12">
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
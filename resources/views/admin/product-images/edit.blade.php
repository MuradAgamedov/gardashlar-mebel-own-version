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
                                                 class="img-fluid rounded" 
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
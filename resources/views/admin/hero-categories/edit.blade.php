@extends('admin.layout.app')

@section('title', 'Edit Hero Category')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Edit Hero Category</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.hero-categories.index') }}">Hero Categories</a>
                        </li>
                        <li class="breadcrumb-item active">Edit</li>
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
                        <h5>Edit Hero Category</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.hero-categories.update', $heroCategory) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <!-- Category Selection -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Select Category <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('product_category_id') is-invalid @enderror" 
                                            name="product_category_id" 
                                            id="product_category_id" 
                                            required>
                                        <option value="">Select category</option>
                                        @foreach($categories as $category)
                                            @php
                                                $firstTranslation = $category->translates->first();
                                                $title = $firstTranslation ? $firstTranslation->title : 'No title';
                                                $indent = $category->parent_id ? '└─ ' : '';
                                            @endphp
                                            <option value="{{ $category->id }}" {{ old('product_category_id', $heroCategory->product_category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $indent }}{{ $title }}
                                                @if($category->parent)
                                                    ({{ $category->parent->translates->first() ? $category->parent->translates->first()->title : 'Parent' }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Select category to display in hero section</small>
                                    @error('product_category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="status" 
                                               name="status" 
                                               value="1" 
                                               {{ old('status', $heroCategory->status) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">Active</label>
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
                                        <a href="{{ route('admin.hero-categories.index') }}" class="btn btn-secondary">
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
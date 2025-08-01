@extends('admin.layout.app')

@section('title', 'Edit Home Page Project Category')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Edit Home Page Project Category</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.home-page-project-categories.index') }}">Home Page Project Categories</a>
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
                        <h5>Edit Home Page Project Category</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.home-page-project-categories.update', $homePageProjectCategory->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <!-- Category Selection -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Change Project Category
                                    </label>
                                    <select class="form-select @error('project_category_id') is-invalid @enderror" 
                                            name="project_category_id" 
                                            id="project_category_id">
                                        @foreach($projectCategories as $category)
                                            @php
                                                $firstTranslation = $category->translates->first();
                                                $title = $firstTranslation ? $firstTranslation->title : 'No title';
                                            @endphp
                                            <option value="{{ $category->id }}" 
                                                    {{ old('project_category_id', $homePageProjectCategory->project_category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">You can change the project category if needed</small>
                                    @error('project_category_id')
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
                                               {{ $homePageProjectCategory->status ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">
                                            Active
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Toggle to show/hide this category on the home page</small>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.home-page-project-categories.index') }}" class="btn btn-secondary">
                                            <i class="material-icons-outlined">arrow_back</i>
                                            Back
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">save</i>
                                            Update Changes
                                        </button>
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
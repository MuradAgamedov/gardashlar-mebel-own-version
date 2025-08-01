@extends('admin.layout.app')

@section('title', 'Edit Home Page Project Category')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Edit Home Page Project Category</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.home-page-project-categories.index') }}">Home Page Project Categories</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Edit Project Category Settings</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.home-page-project-categories.update', $homePageProjectCategory->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <!-- Current Category Info -->
                                    <div class="mb-4">
                                        <label class="form-label">Current Project Category</label>
                                        <div class="card border">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        @if($homePageProjectCategory->projectCategory->image_url)
                                                            <img src="{{ $homePageProjectCategory->projectCategory->image_url }}" 
                                                                 class="img-fluid rounded" 
                                                                 style="max-height: 100px; object-fit: cover;" 
                                                                 alt="Category Image">
                                                        @else
                                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                                 style="width: 100px; height: 100px;">
                                                                <i class="fas fa-image text-muted fa-2x"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-9">
                                                        <h6 class="mb-2">{{ $homePageProjectCategory->projectCategory->translate()?->title ?? 'No Title' }}</h6>
                                                        @if($homePageProjectCategory->projectCategory->translate()?->description)
                                                            <p class="text-muted mb-2">{{ $homePageProjectCategory->projectCategory->translate()?->description }}</p>
                                                        @endif
                                                        <div class="d-flex gap-2">
                                                            <span class="badge badge-info">Order: {{ $homePageProjectCategory->order }}</span>
                                                            @if($homePageProjectCategory->status)
                                                                <span class="badge badge-success">Active</span>
                                                            @else
                                                                <span class="badge badge-danger">Inactive</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Project Category Selection -->
                                    <div class="mb-3">
                                        <label for="project_category_id" class="form-label">Change Project Category</label>
                                        <select name="project_category_id" id="project_category_id" class="form-select @error('project_category_id') is-invalid @enderror">
                                            @foreach($projectCategories as $category)
                                                <option value="{{ $category->id }}" 
                                                        {{ old('project_category_id', $homePageProjectCategory->project_category_id) == $category->id ? 'selected' : '' }}
                                                        data-image="{{ $category->image_url }}"
                                                        data-description="{{ $category->translate()?->description ?? '' }}">
                                                    {{ $category->translate()?->title ?? 'No Title' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('project_category_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">You can change the project category if needed.</small>
                                    </div>

                                    <!-- Status Toggle -->
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="status" name="status" 
                                                   {{ $homePageProjectCategory->status ? 'checked' : '' }}>
                                            <label class="form-check-label" for="status">
                                                Active Status
                                            </label>
                                        </div>
                                        <small class="form-text text-muted">Toggle to show/hide this category on the home page.</small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <!-- Info Card -->
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="fas fa-info-circle text-primary"></i>
                                                Information
                                            </h6>
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-2">
                                                    <small class="text-muted">
                                                        <i class="fas fa-sort text-warning"></i>
                                                        Current order: {{ $homePageProjectCategory->order }}
                                                    </small>
                                                </li>
                                                <li class="mb-2">
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar text-info"></i>
                                                        Created: {{ $homePageProjectCategory->created_at->format('M d, Y') }}
                                                    </small>
                                                </li>
                                                <li class="mb-2">
                                                    <small class="text-muted">
                                                        <i class="fas fa-edit text-success"></i>
                                                        Last updated: {{ $homePageProjectCategory->updated_at->format('M d, Y') }}
                                                    </small>
                                                </li>
                                                <li>
                                                    <small class="text-muted">
                                                        <i class="fas fa-exclamation-triangle text-warning"></i>
                                                        Order can be changed by dragging in the list
                                                    </small>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <!-- Quick Actions -->
                                    <div class="card mt-3">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="fas fa-bolt text-warning"></i>
                                                Quick Actions
                                            </h6>
                                            <div class="d-grid gap-2">
                                                <a href="{{ route('admin.project-categories.edit', $homePageProjectCategory->projectCategory->id) }}" 
                                                   class="btn btn-outline-primary btn-sm" target="_blank">
                                                    <i class="fas fa-external-link-alt"></i>
                                                    Edit Project Category
                                                </a>
                                                <a href="{{ route('admin.home-page-project-categories.index') }}" 
                                                   class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-list"></i>
                                                    Back to List
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.home-page-project-categories.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i> 
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> 
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('project_category_id');
    
    // Add change event listener if you want to show preview of selected category
    select.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value !== '{{ $homePageProjectCategory->project_category_id }}') {
            if (confirm('Are you sure you want to change the project category?')) {
                // Option selected, form will submit
            } else {
                // Reset to original value
                this.value = '{{ $homePageProjectCategory->project_category_id }}';
            }
        }
    });
});
</script>
@endpush
@endsection 
@extends('admin.layout.app')

@section('title', __('Add Project to Home Page'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Add Project to Home Page') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.home-page-projects.index') }}">{{ __('Home Page Projects') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Add Project') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">{{ __('Please fix the following errors:') }}</h6>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">{{ __('Error occurred:') }}</h6>
                <p class="mb-0">{{ session('error') }}</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('Add New Project to Home Page') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.home-page-projects.store') }}" method="POST">
                            @csrf
                            
                            <!-- Category Selection -->
                            <div style="padding: 15px" class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">
                                        {{ __('Select Category') }} <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('project_category_id') is-invalid @enderror" 
                                            name="project_category_id" 
                                            id="categorySelect"
                                            required>
                                        <option value="">{{ __('Choose a category...') }}</option>
                                        @foreach($categories as $category)
                                            @php
                                                $translation = $category->translates->first();
                                            @endphp
                                            <option value="{{ $category->id }}" {{ old('project_category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $translation ? $translation->title : __('Category') . ' #' . $category->id }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('project_category_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        {{ __('Select a category to filter projects') }}
                                    </small>
                                </div>
                            </div>

                            <!-- Project Selection -->
                            <div style="padding: 15px" class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">
                                        {{ __('Select Project') }} <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('project_id') is-invalid @enderror" 
                                            name="project_id" 
                                            id="projectSelect"
                                            required>
                                        <option value="">{{ __('First select a category...') }}</option>
                                    </select>
                                    @error('project_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        {{ __('Select a project from the chosen category') }}
                                    </small>
                                </div>
                            </div>

                            <!-- Status -->
                            <div style="padding: 15px" class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Status') }}</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" checked>
                                        <label class="form-check-label" for="status">{{ __('Active') }}</label>
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
                                        <a href="{{ route('admin.home-page-projects.index') }}" class="btn btn-secondary">
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
    const projectSelect = document.getElementById('projectSelect');
    const categorySelect = document.getElementById('categorySelect');
    
    // Load projects when category is selected
    categorySelect.addEventListener('change', function() {
        const categoryId = this.value;
        
        if (categoryId) {
            // Show loading state
            projectSelect.innerHTML = '<option value="">{{ __("Loading projects...") }}</option>';
            projectSelect.disabled = true;
            
            // Fetch projects by category
            fetch(`{{ route('admin.home-page-projects.get-by-category') }}?category_id=${categoryId}`)
                .then(response => response.json())
                .then(projects => {
                    // Clear and populate project select
                    projectSelect.innerHTML = '<option value="">{{ __("Choose a project...") }}</option>';
                    
                    projects.forEach(project => {
                        const translation = project.translates[0];
                        const title = translation ? translation.title : `{{ __("Project") }} #${project.id}`;
                        
                        const option = document.createElement('option');
                        option.value = project.id;
                        option.textContent = title;
                        projectSelect.appendChild(option);
                    });
                    
                    projectSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error loading projects:', error);
                    projectSelect.innerHTML = '<option value="">{{ __("Error loading projects") }}</option>';
                    projectSelect.disabled = false;
                });
        } else {
            // Reset project select when no category is selected
            projectSelect.innerHTML = '<option value="">{{ __("First select a category...") }}</option>';
            projectSelect.disabled = true;
        }
    });
    
    // Set initial state if category is pre-selected (for edit mode)
    if (categorySelect.value) {
        categorySelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
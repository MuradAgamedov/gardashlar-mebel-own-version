@extends('admin.layout.app')

@section('title', 'Create Project')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Create Project</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.projects.index') }}">Projects Management</a>
                        </li>
                        <li class="breadcrumb-item active">Create Project</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">Please fix the following errors:</h6>
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
                        <h5>Add New Project</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.projects.store') }}" method="POST">
                            @csrf
                            

                                
                                <!-- Project Category -->
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Project Category <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('project_category_id') is-invalid @enderror" 
                                            name="project_category_id" 
                                            required>
                                        <option value="">Select a category</option>
                                        @foreach($categories as $category)
                                            @php
                                                $categoryTranslation = $category->translates->first();
                                            @endphp
                                            <option value="{{ $category->id }}" 
                                                    {{ old('project_category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $categoryTranslation ? $categoryTranslation->title : 'Category #' . $category->id }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('project_category_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status -->
                            <div style="padding: 15px" class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" checked>
                                        <label class="form-check-label" for="status">Active</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Language Tabs -->
                            <div style="padding: 15px" class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label">Project Information</label>
                                    <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                                        @foreach($languages as $index => $language)
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                                        id="tab-{{ $language->id }}" 
                                                        data-bs-toggle="tab" 
                                                        data-bs-target="#content-{{ $language->id }}" 
                                                        type="button" 
                                                        role="tab">
                                                        {{ $language->lang_name }} ({{ $language->lang_code }})
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <div class="tab-content" id="languageTabContent">
                                        @foreach($languages as $index => $language)
                                            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                                 id="content-{{ $language->id }}" 
                                                 role="tabpanel">
                                                
                                                <div class="row mt-3" style="padding: 15px;">
                                                    <!-- Title -->
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">
                                                            Title <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" 
                                                               class="form-control @error("translations.{$language->id}.title") is-invalid @enderror" 
                                                               name="translations[{{ $language->id }}][title]" 
                                                               value="{{ old("translations.{$language->id}.title") }}" 
                                                               placeholder="Enter project title in {{ $language->lang_name }}"
                                                               required>
                                                        @error("translations.{$language->id}.title")
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <!-- Company Name -->
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">
                                                            Company Name <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" 
                                                               class="form-control @error("translations.{$language->id}.company_name") is-invalid @enderror" 
                                                               name="translations[{{ $language->id }}][company_name]" 
                                                               value="{{ old("translations.{$language->id}.company_name") }}" 
                                                               placeholder="Enter company name in {{ $language->lang_name }}"
                                                               required>
                                                        @error("translations.{$language->id}.company_name")
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <!-- Slug -->
                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label">
                                                            Slug <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" 
                                                               class="form-control @error("translations.{$language->id}.slug") is-invalid @enderror" 
                                                               name="translations[{{ $language->id }}][slug]" 
                                                               value="{{ old("translations.{$language->id}.slug") }}" 
                                                               placeholder="Enter project slug in {{ $language->lang_name }}"
                                                               required>
                                                        @error("translations.{$language->id}.slug")
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <!-- Description -->
                                                    <div class="col-md-12">
                                                        <label class="form-label">
                                                            Description <span class="text-danger">*</span>
                                                        </label>
                                                        <textarea class="form-control ckeditor @error("translations.{$language->id}.description") is-invalid @enderror" 
                                                                  name="translations[{{ $language->id }}][description]" 
                                                                  placeholder="Enter project description in {{ $language->lang_name }}"
                                                                  required>{{ old("translations.{$language->id}.description") }}</textarea>
                                                        @error("translations.{$language->id}.description")
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                
                                                <input type="hidden" name="translations[{{ $language->id }}][language_id]" value="{{ $language->id }}">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">save</i>
                                            Create Project
                                        </button>
                                        <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">
                                            <i class="material-icons-outlined">cancel</i>
                                            Cancel
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
<script src="https://cdn.ckeditor.com/ckeditor5/27.1.0/classic/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Remove required attribute from CKEditor textareas to prevent focus issues
    document.querySelectorAll('.ckeditor').forEach(textarea => {
        textarea.removeAttribute('required');
    });
    
    // Initialize CKEditor for all textareas with ckeditor class
    const textareas = document.querySelectorAll('.ckeditor');
    const editors = {};
    
    textareas.forEach((textarea, index) => {
        ClassicEditor
            .create(textarea, {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'blockQuote', 'insertTable', 'undo', 'redo'],
                placeholder: textarea.getAttribute('placeholder')
            })
            .then(editor => {
                editors[textarea.name] = editor;
            })
            .catch(error => {
                console.error('CKEditor error:', error);
            });
    });
    
    // Update form data before submit using getData() method
    document.querySelector('form').addEventListener('submit', function() {
        Object.entries(editors).forEach(([name, editor]) => {
            if (editor && editor.getData) {
                const data = editor.getData();
                const textarea = document.querySelector(`[name="${name}"]`);
                if (textarea) {
                    textarea.value = data;
                }
            }
        });
    });
});
</script>
@endpush 
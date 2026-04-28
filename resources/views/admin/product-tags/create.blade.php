@extends('admin.layout.app')

@section('title', 'Add Product Tag')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Add Product Tag</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.product-tags.index') }}">Product Tags</a>
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
                        <h5>New Product Tag</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.product-tags.store') }}" method="POST">
                            @csrf
                            
                            <!-- Language Tabs -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label">Tag Information</label>
                                    <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                                        @foreach($languages as $index => $language)
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                                        id="tab-{{ $language->id }}" 
                                                        data-bs-toggle="tab" 
                                                        data-bs-target="#content-{{ $language->id }}" 
                                                        type="button" 
                                                        role="tab">
                                                        {{ $language->name }} ({{ $language->lang_code }})
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <div class="tab-content" id="languageTabContent">
                                        @foreach($languages as $index => $language)
                                            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                                 id="content-{{ $language->id }}" 
                                                 role="tabpanel">
                                                
                                                <div class="row mt-3">
                                                    <!-- Title -->
                                                    <div class="col-md-6">
                                                        <label class="form-label">
                                                            Title <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" 
                                                               class="form-control @error("translations.{$language->id}.title") is-invalid @enderror" 
                                                               name="translations[{{ $language->id }}][title]" 
                                                               value="{{ old("translations.{$language->id}.title") }}" 
                                                               placeholder="Enter title"
                                                               required>
                                                        @error("translations.{$language->id}.title")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <!-- Slug -->
                                                    <div class="col-md-6">
                                                        <label class="form-label">
                                                            Slug <span class="text-danger">*</span>
                                                        </label>
                                                        <div class="input-group">
                                                            <input type="text" 
                                                                   class="form-control @error("translations.{$language->id}.slug") is-invalid @enderror" 
                                                                   name="translations[{{ $language->id }}][slug]" 
                                                                   id="slug_{{ $language->id }}"
                                                                   value="{{ old("translations.{$language->id}.slug") }}" 
                                                                   placeholder="Enter slug"
                                                                   required>
                                                            <button type="button" 
                                                                    class="btn btn-outline-secondary generate-slug" 
                                                                    data-title-input="translations[{{ $language->id }}][title]"
                                                                    data-slug-input="slug_{{ $language->id }}">
                                                                <i class="material-icons-outlined">auto_fix_high</i>
                                                            </button>
                                                        </div>
                                                        @error("translations.{$language->id}.slug")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                
                                                <input type="hidden" 
                                                       name="translations[{{ $language->id }}][language_id]" 
                                                       value="{{ $language->id }}">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="status" value="0">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="status" 
                                               name="status" 
                                               value="1" 
                                               {{ old('status', '1') ? 'checked' : '' }}>
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
                                            Add Tag
                                        </button>
                                        <a href="{{ route('admin.product-tags.index') }}" class="btn btn-secondary">
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

@push('styles')
<style>
.alert-heading {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.is-invalid {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

.invalid-feedback {
    display: block;
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.nav-tabs .nav-link.active {
    border-color: #dee2e6 #dee2e6 #fff;
    background-color: #fff;
}

.tab-pane.show.active {
    border: 1px solid #dee2e6;
    border-top: none;
    padding: 1rem;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Generate slug functionality
    document.querySelectorAll('.generate-slug').forEach(button => {
        button.addEventListener('click', function() {
            const titleInput = document.querySelector(`[name="${this.dataset.titleInput}"]`);
            const slugInput = document.getElementById(this.dataset.slugInput);
            
            if (titleInput.value) {
                fetch('{{ route("admin.product-tags.generate-slug") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ title: titleInput.value })
                })
                .then(response => response.json())
                .then(data => {
                    slugInput.value = data.slug;
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });
    });
    
    // Auto-switch tabs on validation errors
    const errorFields = document.querySelectorAll('.is-invalid');
    if (errorFields.length > 0) {
        const firstError = errorFields[0];
        
        // Check if error is in translation field
        const translationMatch = firstError.name.match(/translations\[(\d+)\]/);
        if (translationMatch) {
            const tabId = translationMatch[1];
            const tabButton = document.getElementById(`tab-${tabId}`);
            const tabContent = document.getElementById(`content-${tabId}`);
            
            if (tabButton && tabContent) {
                // Remove active class from all tabs
                document.querySelectorAll('.nav-link').forEach(tab => tab.classList.remove('active'));
                document.querySelectorAll('.tab-pane').forEach(content => content.classList.remove('show', 'active'));
                
                // Activate the tab with error
                tabButton.classList.add('active');
                tabContent.classList.add('show', 'active');
            }
        }
        
        // Scroll to the error field
        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});
</script>
@endpush 
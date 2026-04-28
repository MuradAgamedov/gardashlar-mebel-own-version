@extends('admin.layout.app')

@section('title', 'Create Catalog')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Create Catalog</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.catalogs.index') }}">Catalogs</a>
                        </li>
                        <li class="breadcrumb-item active">Create</li>
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
                <h6 class="alert-heading">Error:</h6>
                <p class="mb-0">{{ session('error') }}</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Add New Catalog</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.catalogs.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Status and PDF -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ old('status', '1') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">Active</label>
                                        <input type="hidden" name="status" value="0">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Catalog PDF <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" 
                                           class="form-control @error('catalog_pdf') is-invalid @enderror" 
                                           name="catalog_pdf" 
                                           accept=".pdf"
                                           onchange="previewPDF(this)">
                                    @error('catalog_pdf')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    <!-- PDF Preview -->
                                    <div id="pdf-preview" class="mt-2" style="display: none;">
                                        <div class="alert alert-info">
                                            <i class="material-icons-outlined">file_present</i>
                                            <span id="pdf-name"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Language Tabs -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label">Catalog Information</label>
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
                                                
                                                <div class="row mt-3">
                                                    <!-- Catalog Year -->
                                                    <div class="col-md-12">
                                                        <label class="form-label">
                                                            Catalog Year <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" 
                                                               class="form-control @error("translations.{$language->id}.catalog_year") is-invalid @enderror" 
                                                               name="translations[{{ $language->id }}][catalog_year]" 
                                                               value="{{ old("translations.{$language->id}.catalog_year") }}" 
                                                               placeholder="e.g., 2024, 2023-2024">
                                                        @error("translations.{$language->id}.catalog_year")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
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
                                        </button>
                                        <a href="{{ route('admin.catalogs.index') }}" class="btn btn-secondary">
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
function previewPDF(input) {
    const file = input.files[0];
    const previewDiv = document.getElementById('pdf-preview');
    const nameSpan = document.getElementById('pdf-name');
    
    if (file) {
        // Check file type
        if (file.type !== 'application/pdf') {
            alert('Please select a PDF file.');
            input.value = '';
            previewDiv.style.display = 'none';
            return;
        }
        

        
        nameSpan.textContent = file.name;
        previewDiv.style.display = 'block';
    } else {
        previewDiv.style.display = 'none';
    }
}

// Auto-switch tabs on validation errors
document.addEventListener('DOMContentLoaded', function() {
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
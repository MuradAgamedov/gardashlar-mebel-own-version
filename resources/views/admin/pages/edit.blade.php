@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Edit Page</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.pages.index') }}">Pages</a>
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
                        <h5>Edit Page</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.pages.update', $page) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" {{ $page->status ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">
                                            Active
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <h6 class="mb-3">Page Translations</h6>
                                    
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
                                    
                                    <!-- Language Tabs -->
                                    <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                                        @foreach($languages as $index => $language)
                                        @php
                                            $translate = $page->translates->where('language_id', $language->id)->first();
                                        @endphp
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link {{ $index === 0 ? 'active' : '' }} {{ $errors->has('translates.'.$index.'.*') ? 'border-danger' : '' }}" 
                                                    id="tab-{{ $language->lang_code }}" 
                                                    data-bs-toggle="tab" 
                                                    data-bs-target="#content-{{ $language->lang_code }}" 
                                                    type="button" 
                                                    role="tab" 
                                                    aria-controls="content-{{ $language->lang_code }}" 
                                                    aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                                <span class="badge badge-light-primary me-2">{{ $language->lang_code }}</span>
                                                {{ $language->title }}
                                                @if($translate)
                                                    <i class="material-icons-outlined text-success ms-1" style="font-size: 14px;">check_circle</i>
                                                @else
                                                    <i class="material-icons-outlined text-warning ms-1" style="font-size: 14px;">warning</i>
                                                @endif
                                                @if($errors->has('translates.'.$index.'.*'))
                                                    <i class="material-icons-outlined text-danger ms-1" style="font-size: 14px;">error</i>
                                                @endif
                                            </button>
                                        </li>
                                        @endforeach
                                    </ul>

                                    <!-- Tab Content -->
                                    <div class="tab-content" id="languageTabsContent">
                                        @foreach($languages as $index => $language)
                                        @php
                                            $translate = $page->translates->where('language_id', $language->id)->first();
                                        @endphp
                                        <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                             id="content-{{ $language->lang_code }}" 
                                             role="tabpanel" 
                                             aria-labelledby="tab-{{ $language->lang_code }}">
                                            
                                            <div class="card mt-3">
                                                <div class="card-header">
                                                    <h6 class="mb-0">{{ $language->title }} Content</h6>
                                                    @if($translate)
                                                        <small class="text-success">Translation exists</small>
                                                    @else
                                                        <small class="text-warning">No translation yet</small>
                                                    @endif
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Title *</label>
                                                            <input type="text" 
                                                                   class="form-control @error('translates.'.$index.'.title') is-invalid @enderror" 
                                                                   name="translates[{{ $index }}][title]" 
                                                                   value="{{ old('translates.'.$index.'.title', $translate->title ?? '') }}" 
                                                                   required>
                                                            <input type="hidden" name="translates[{{ $index }}][language_id]" value="{{ $language->id }}">
                                                            @error('translates.'.$index.'.title')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Slug *</label>
                                                            <div class="input-group">
                                                                <input type="text" 
                                                                       class="form-control @error('translates.'.$index.'.slug') is-invalid @enderror slug-input" 
                                                                       name="translates[{{ $index }}][slug]" 
                                                                       value="{{ old('translates.'.$index.'.slug', $translate->slug ?? '') }}" 
                                                                       readonly
                                                                       required>
                                                                <button type="button" 
                                                                        class="btn btn-outline-secondary edit-slug-btn" 
                                                                        data-slug-input="translates[{{ $index }}][slug]">
                                                                    <i class="material-icons-outlined">edit</i>
                                                                </button>
                                                            </div>
                                                            @error('translates.'.$index.'.slug')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">SEO Title</label>
                                                            <input type="text" 
                                                                   class="form-control @error('translates.'.$index.'.seo_title') is-invalid @enderror" 
                                                                   name="translates[{{ $index }}][seo_title]" 
                                                                   value="{{ old('translates.'.$index.'.seo_title', $translate->seo_title ?? '') }}"
                                                                   placeholder="Enter SEO title">
                                                            @error('translates.'.$index.'.seo_title')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">SEO Keywords</label>
                                                            <textarea class="form-control @error('translates.'.$index.'.seo_keywords') is-invalid @enderror" 
                                                                      name="translates[{{ $index }}][seo_keywords]" 
                                                                      rows="3" 
                                                                      placeholder="Enter keywords separated by commas">{{ old('translates.'.$index.'.seo_keywords', $translate->seo_keywords ?? '') }}</textarea>
                                                            @error('translates.'.$index.'.seo_keywords')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">SEO Description</label>
                                                            <textarea class="form-control @error('translates.'.$index.'.seo_description') is-invalid @enderror" 
                                                                      name="translates[{{ $index }}][seo_description]" 
                                                                      rows="3" 
                                                                      placeholder="Enter SEO description">{{ old('translates.'.$index.'.seo_description', $translate->seo_description ?? '') }}</textarea>
                                                            @error('translates.'.$index.'.seo_description')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">save</i>
                                            Update Page
                                        </button>
                                        <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add visual feedback for active tab
    document.querySelectorAll('#languageTabs .nav-link').forEach(function(tab) {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs
            document.querySelectorAll('#languageTabs .nav-link').forEach(t => t.classList.remove('active'));
            // Add active class to clicked tab
            this.classList.add('active');
        });
    });

    // Show translation status in tabs
    function updateTabStatus() {
        document.querySelectorAll('#languageTabs .nav-link').forEach(function(tab) {
            const tabId = tab.getAttribute('data-bs-target');
            const tabContent = document.querySelector(tabId);
            const titleInput = tabContent.querySelector('input[name*="[title]"]');
            const slugInput = tabContent.querySelector('input[name*="[slug]"]');
            
            if (titleInput.value && slugInput.value) {
                // Has content
                tab.querySelector('.text-success').style.display = 'inline';
                tab.querySelector('.text-warning').style.display = 'none';
            } else {
                // Missing content
                tab.querySelector('.text-success').style.display = 'none';
                tab.querySelector('.text-warning').style.display = 'inline';
            }
        });
    }

    // Update status when inputs change
    document.querySelectorAll('input[name*="[title]"], input[name*="[slug]"]').forEach(function(input) {
        input.addEventListener('input', updateTabStatus);
    });

    // Initial status update
    updateTabStatus();

    // Auto-switch to first tab with errors
    @if($errors->any())
        const errorTabs = document.querySelectorAll('#languageTabs .nav-link.border-danger');
        if (errorTabs.length > 0) {
            // Switch to first tab with errors
            const firstErrorTab = errorTabs[0];
            const tabId = firstErrorTab.getAttribute('data-bs-target');
            
            // Remove active from all tabs and panes
            document.querySelectorAll('#languageTabs .nav-link').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('show', 'active'));
            
            // Activate the error tab
            firstErrorTab.classList.add('active');
            document.querySelector(tabId).classList.add('show', 'active');
            
            // Scroll to the error tab
            firstErrorTab.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    @endif

    // Edit slug functionality
    document.querySelectorAll('.edit-slug-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const slugInputName = this.getAttribute('data-slug-input');
            const slugInput = document.querySelector('input[name="' + slugInputName + '"]');
            
            if (slugInput) {
                // Toggle readonly state
                if (slugInput.readOnly) {
                    slugInput.readOnly = false;
                    slugInput.focus();
                    this.innerHTML = '<i class="material-icons-outlined">lock</i>';
                    this.classList.remove('btn-outline-secondary');
                    this.classList.add('btn-outline-warning');
                } else {
                    slugInput.readOnly = true;
                    this.innerHTML = '<i class="material-icons-outlined">edit</i>';
                    this.classList.remove('btn-outline-warning');
                    this.classList.add('btn-outline-secondary');
                }
            }
        });
    });
});
</script>

<style>
.nav-tabs .nav-link {
    border: 1px solid #dee2e6;
    border-bottom: none;
    border-radius: 0.375rem 0.375rem 0 0;
    margin-right: 2px;
    padding: 0.5rem 1rem;
    color: #6c757d;
    background-color: #f8f9fa;
    position: relative;
}

.nav-tabs .nav-link:hover {
    border-color: #e9ecef #e9ecef #dee2e6;
    background-color: #e9ecef;
}

.nav-tabs .nav-link.active {
    color: #495057;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
}

.nav-tabs .nav-link.border-danger {
    border-color: #dc3545 !important;
    color: #dc3545;
}

.nav-tabs .nav-link.border-danger.active {
    border-color: #dc3545 #dc3545 #fff !important;
    color: #dc3545;
}

.tab-content {
    border: 1px solid #dee2e6;
    border-top: none;
    border-radius: 0 0 0.375rem 0.375rem;
    padding: 1rem;
    background-color: #fff;
}

.tab-pane {
    min-height: 400px;
}

.nav-tabs .nav-link i {
    vertical-align: middle;
}

.invalid-feedback.d-block {
    display: block !important;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}
</style>
@endpush

@include('admin.partials.slug-generator') 
@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Why Choose Us</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Why Choose Us</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="material-icons-outlined">check_circle</i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="material-icons-outlined">error</i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Edit Why Choose Us Section</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.why-choose-us.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <!-- Icons Section -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h6 class="mb-3">Icons</h6>
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">First Icon</label>
                                            <div class="icon-upload-container">
                                                <input type="file" 
                                                       class="form-control @error('first_icon') is-invalid @enderror icon-file-input" 
                                                       name="first_icon" 
                                                       accept="image/*"
                                                       data-preview="first-icon-preview">
                                                @error('first_icon')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <div class="icon-preview mt-2" id="first-icon-preview">
                                                    @if($whyChooseUs->first_icon_url)
                                                        <img src="{{ $whyChooseUs->first_icon_url }}" alt="First Icon" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Second Icon</label>
                                            <div class="icon-upload-container">
                                                <input type="file" 
                                                       class="form-control @error('second_icon') is-invalid @enderror icon-file-input" 
                                                       name="second_icon" 
                                                       accept="image/*"
                                                       data-preview="second-icon-preview">
                                                @error('second_icon')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <div class="icon-preview mt-2" id="second-icon-preview">
                                                    @if($whyChooseUs->second_icon_url)
                                                        <img src="{{ $whyChooseUs->second_icon_url }}" alt="Second Icon" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Third Icon</label>
                                            <div class="icon-upload-container">
                                                <input type="file" 
                                                       class="form-control @error('third_icon') is-invalid @enderror icon-file-input" 
                                                       name="third_icon" 
                                                       accept="image/*"
                                                       data-preview="third-icon-preview">
                                                @error('third_icon')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <div class="icon-preview mt-2" id="third-icon-preview">
                                                    @if($whyChooseUs->third_icon_url)
                                                        <img src="{{ $whyChooseUs->third_icon_url }}" alt="Third Icon" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Fourth Icon</label>
                                            <div class="icon-upload-container">
                                                <input type="file" 
                                                       class="form-control @error('fourth_icon') is-invalid @enderror icon-file-input" 
                                                       name="fourth_icon" 
                                                       accept="image/*"
                                                       data-preview="fourth-icon-preview">
                                                @error('fourth_icon')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <div class="icon-preview mt-2" id="fourth-icon-preview">
                                                    @if($whyChooseUs->fourth_icon_url)
                                                        <img src="{{ $whyChooseUs->fourth_icon_url }}" alt="Fourth Icon" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Content Section -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h6 class="mb-3">Content Translations</h6>
                                    
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
                                            $translate = $whyChooseUs->translates->where('language_id', $language->id)->first();
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
                                            $translate = $whyChooseUs->translates->where('language_id', $language->id)->first();
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
                                                    <input type="hidden" name="translates[{{ $index }}][language_id]" value="{{ $language->id }}">
                                                    
                                                    <!-- First Section -->
                                                    <div class="row mb-4">
                                                        <div class="col-md-12">
                                                            <h6 class="text-primary">First Section</h6>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Icon Alt Text</label>
                                                            <input type="text" 
                                                                   class="form-control @error('translates.'.$index.'.first_icon_alt_text') is-invalid @enderror" 
                                                                   name="translates[{{ $index }}][first_icon_alt_text]" 
                                                                   value="{{ old('translates.'.$index.'.first_icon_alt_text', $translate->first_icon_alt_text ?? '') }}"
                                                                   placeholder="Icon alt text">
                                                            @error('translates.'.$index.'.first_icon_alt_text')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Title</label>
                                                            <input type="text" 
                                                                   class="form-control @error('translates.'.$index.'.first_title') is-invalid @enderror" 
                                                                   name="translates[{{ $index }}][first_title]" 
                                                                   value="{{ old('translates.'.$index.'.first_title', $translate->first_title ?? '') }}"
                                                                   placeholder="First section title">
                                                            @error('translates.'.$index.'.first_title')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Description</label>
                                                            <textarea class="form-control @error('translates.'.$index.'.first_description') is-invalid @enderror" 
                                                                      name="translates[{{ $index }}][first_description]" 
                                                                      rows="3" 
                                                                      placeholder="First section description">{{ old('translates.'.$index.'.first_description', $translate->first_description ?? '') }}</textarea>
                                                            @error('translates.'.$index.'.first_description')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!-- Second Section -->
                                                    <div class="row mb-4">
                                                        <div class="col-md-12">
                                                            <h6 class="text-primary">Second Section</h6>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Icon Alt Text</label>
                                                            <input type="text" 
                                                                   class="form-control @error('translates.'.$index.'.second_icon_alt_text') is-invalid @enderror" 
                                                                   name="translates[{{ $index }}][second_icon_alt_text]" 
                                                                   value="{{ old('translates.'.$index.'.second_icon_alt_text', $translate->second_icon_alt_text ?? '') }}"
                                                                   placeholder="Icon alt text">
                                                            @error('translates.'.$index.'.second_icon_alt_text')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Title</label>
                                                            <input type="text" 
                                                                   class="form-control @error('translates.'.$index.'.second_title') is-invalid @enderror" 
                                                                   name="translates[{{ $index }}][second_title]" 
                                                                   value="{{ old('translates.'.$index.'.second_title', $translate->second_title ?? '') }}"
                                                                   placeholder="Second section title">
                                                            @error('translates.'.$index.'.second_title')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Description</label>
                                                            <textarea class="form-control @error('translates.'.$index.'.second_description') is-invalid @enderror" 
                                                                      name="translates[{{ $index }}][second_description]" 
                                                                      rows="3" 
                                                                      placeholder="Second section description">{{ old('translates.'.$index.'.second_description', $translate->second_description ?? '') }}</textarea>
                                                            @error('translates.'.$index.'.second_description')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!-- Third Section -->
                                                    <div class="row mb-4">
                                                        <div class="col-md-12">
                                                            <h6 class="text-primary">Third Section</h6>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Icon Alt Text</label>
                                                            <input type="text" 
                                                                   class="form-control @error('translates.'.$index.'.third_icon_alt_text') is-invalid @enderror" 
                                                                   name="translates[{{ $index }}][third_icon_alt_text]" 
                                                                   value="{{ old('translates.'.$index.'.third_icon_alt_text', $translate->third_icon_alt_text ?? '') }}"
                                                                   placeholder="Icon alt text">
                                                            @error('translates.'.$index.'.third_icon_alt_text')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Title</label>
                                                            <input type="text" 
                                                                   class="form-control @error('translates.'.$index.'.third_title') is-invalid @enderror" 
                                                                   name="translates[{{ $index }}][third_title]" 
                                                                   value="{{ old('translates.'.$index.'.third_title', $translate->third_title ?? '') }}"
                                                                   placeholder="Third section title">
                                                            @error('translates.'.$index.'.third_title')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Description</label>
                                                            <textarea class="form-control @error('translates.'.$index.'.third_description') is-invalid @enderror" 
                                                                      name="translates[{{ $index }}][third_description]" 
                                                                      rows="3" 
                                                                      placeholder="Third section description">{{ old('translates.'.$index.'.third_description', $translate->third_description ?? '') }}</textarea>
                                                            @error('translates.'.$index.'.third_description')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!-- Fourth Section -->
                                                    <div class="row mb-4">
                                                        <div class="col-md-12">
                                                            <h6 class="text-primary">Fourth Section</h6>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Icon Alt Text</label>
                                                            <input type="text" 
                                                                   class="form-control @error('translates.'.$index.'.fourth_icon_alt_text') is-invalid @enderror" 
                                                                   name="translates[{{ $index }}][fourth_icon_alt_text]" 
                                                                   value="{{ old('translates.'.$index.'.fourth_icon_alt_text', $translate->fourth_icon_alt_text ?? '') }}"
                                                                   placeholder="Icon alt text">
                                                            @error('translates.'.$index.'.fourth_icon_alt_text')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Title</label>
                                                            <input type="text" 
                                                                   class="form-control @error('translates.'.$index.'.fourth_title') is-invalid @enderror" 
                                                                   name="translates[{{ $index }}][fourth_title]" 
                                                                   value="{{ old('translates.'.$index.'.fourth_title', $translate->fourth_title ?? '') }}"
                                                                   placeholder="Fourth section title">
                                                            @error('translates.'.$index.'.fourth_title')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Description</label>
                                                            <textarea class="form-control @error('translates.'.$index.'.fourth_description') is-invalid @enderror" 
                                                                      name="translates[{{ $index }}][fourth_description]" 
                                                                      rows="3" 
                                                                      placeholder="Fourth section description">{{ old('translates.'.$index.'.fourth_description', $translate->fourth_description ?? '') }}</textarea>
                                                            @error('translates.'.$index.'.fourth_description')
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
                                            Update Why Choose Us
                                        </button>
                                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
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

    // Image preview functionality
    document.querySelectorAll('.icon-file-input').forEach(function(input) {
        input.addEventListener('change', function() {
            const file = this.files[0];
            const previewId = this.getAttribute('data-preview');
            const previewContainer = document.getElementById(previewId);
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewContainer.innerHTML = `<img src="${e.target.result}" alt="Preview" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">`;
                };
                reader.readAsDataURL(file);
            } else {
                previewContainer.innerHTML = '';
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

.icon-upload-container {
    position: relative;
}

.icon-preview {
    text-align: center;
    min-height: 50px;
    border: 1px dashed #dee2e6;
    border-radius: 0.375rem;
    padding: 0.5rem;
    background-color: #f8f9fa;
}

.icon-preview img {
    border-radius: 0.25rem;
}
</style>
@endpush 
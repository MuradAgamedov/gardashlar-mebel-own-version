@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Edit Testimonial') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.testimonials.index') }}">{{ __('Testimonials') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Edit') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="material-icons-outlined">error</i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="alert-heading">{{ __('Please fix the following errors:') }}</h6>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('Edit Testimonial') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.testimonials.update', $testimonial) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <!-- Media Section -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h6 class="mb-3">{{ __('Media') }}</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ __('Customer Image') }}</label>
                                            <div class="image-upload-container">
                                                <input type="file" 
                                                       class="form-control @error('customer_image') is-invalid @enderror image-file-input" 
                                                       name="customer_image" 
                                                       accept="image/*"
                                                       data-preview="customer-image-preview">
                                                @error('customer_image')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <div class="image-preview mt-2" id="customer-image-preview">
                                                    @if($testimonial->customer_image_url)
                                                        <img src="{{ $testimonial->customer_image_url }}" alt="{{ __('Current Customer Image') }}" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ __('Company Logo') }}</label>
                                            <div class="image-upload-container">
                                                <input type="file" 
                                                       class="form-control @error('company_logo') is-invalid @enderror image-file-input" 
                                                       name="company_logo" 
                                                       accept="image/*"
                                                       data-preview="company-logo-preview">
                                                @error('company_logo')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <div class="image-preview mt-2" id="company-logo-preview">
                                                    @if($testimonial->company_logo_url)
                                                        <img src="{{ $testimonial->company_logo_url }}" alt="{{ __('Current Company Logo') }}" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Content Translations Section -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h6 class="mb-3">{{ __('Content Translations') }}</h6>
                                    
                                    <!-- Language Tabs -->
                                    <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                                        @foreach($languages as $index => $language)
                                            @php
                                                $translation = $testimonial->translates->where('language_id', $language->id)->first();
                                                $hasErrors = $errors->has("translations.{$language->id}.*");
                                                $hasTranslation = $translation !== null;
                                            @endphp
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                                        id="tab-{{ $language->id }}" 
                                                        data-bs-toggle="tab" 
                                                        data-bs-target="#content-{{ $language->id }}" 
                                                        type="button" 
                                                        role="tab"
                                                        @if($hasErrors)
                                                            style="border-color: #dc3545; color: #dc3545;"
                                                        @elseif($hasTranslation)
                                                            style="border-color: #28a745; color: #28a745;"
                                                        @else
                                                            style="border-color: #ffc107; color: #856404;"
                                                        @endif>
                                                    {{ $language->lang_code }}
                                                    @if($hasErrors)
                                                        <i class="material-icons-outlined text-danger ms-1" style="font-size: 16px;">error</i>
                                                    @elseif($hasTranslation)
                                                        <i class="material-icons-outlined text-success ms-1" style="font-size: 16px;">check_circle</i>
                                                    @else
                                                        <i class="material-icons-outlined text-warning ms-1" style="font-size: 16px;">warning</i>
                                                    @endif
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <!-- Tab Content -->
                                    <div class="tab-content mt-3" id="languageTabContent">
                                        @foreach($languages as $index => $language)
                                            @php
                                                $translation = $testimonial->translates->where('language_id', $language->id)->first();
                                            @endphp
                                            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                                 id="content-{{ $language->id }}" 
                                                 role="tabpanel" 
                                                 aria-labelledby="tab-{{ $language->id }}">
                                                
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">{{ __('Full Name') }}</label>
                                                        <input type="text" 
                                                               class="form-control @error("translations.{$language->id}.full_name") is-invalid @enderror" 
                                                               name="translations[{{ $language->id }}][full_name]" 
                                                               value="{{ old("translations.{$language->id}.full_name", $translation->full_name ?? '') }}"
                                                               placeholder="{{ __('Enter full name') }}">
                                                        @error("translations.{$language->id}.full_name")
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">{{ __('Position') }}</label>
                                                        <input type="text" 
                                                               class="form-control @error("translations.{$language->id}.position") is-invalid @enderror" 
                                                               name="translations[{{ $language->id }}][position]" 
                                                               value="{{ old("translations.{$language->id}.position", $translation->position ?? '') }}"
                                                               placeholder="{{ __('Enter position') }}">
                                                        @error("translations.{$language->id}.position")
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label">{{ __('Comment') }}</label>
                                                        <textarea class="form-control @error("translations.{$language->id}.comment") is-invalid @enderror" 
                                                                  name="translations[{{ $language->id }}][comment]" 
                                                                  rows="4"
                                                                  placeholder="{{ __('Enter testimonial comment') }}">{{ old("translations.{$language->id}.comment", $translation->comment ?? '') }}</textarea>
                                                        @error("translations.{$language->id}.comment")
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Status Section -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input type="hidden" name="status" value="0">
                                        <input class="form-check-input" type="checkbox" name="status" id="status" value="1" {{ old('status', $testimonial->status) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">
                                            {{ __('Active Status') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary" title="{{ __('Update Testimonial') }}">
                                            <i class="material-icons-outlined">save</i>
                                        </button>
                                        <a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary" title="{{ __('Cancel') }}">
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
    // Image preview functionality
    document.querySelectorAll('.image-file-input').forEach(function(input) {
        input.addEventListener('change', function() {
            const file = this.files[0];
            const previewId = this.getAttribute('data-preview');
            const previewContainer = document.getElementById(previewId);
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewContainer.innerHTML = `<img src="${e.target.result}" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">`;
                };
                reader.readAsDataURL(file);
            } else {
                // Keep existing image if no new file selected
                if (!previewContainer.querySelector('img')) {
                    previewContainer.innerHTML = '';
                }
            }
        });
    });

    // Auto-switch to first tab with errors on form submission
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const tabsWithErrors = document.querySelectorAll('.nav-link[style*="border-color: #dc3545"]');
        if (tabsWithErrors.length > 0) {
            e.preventDefault();
            const firstTabWithError = tabsWithErrors[0];
            const tabId = firstTabWithError.getAttribute('data-bs-target');
            const tab = new bootstrap.Tab(firstTabWithError);
            tab.show();
        }
    });
});
</script>

<style>
.invalid-feedback.d-block {
    display: block !important;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}

.image-upload-container {
    position: relative;
}

.image-preview {
    text-align: center;
    min-height: 50px;
    border: 1px dashed #dee2e6;
    border-radius: 0.375rem;
    padding: 0.5rem;
    background-color: #f8f9fa;
}

.image-preview img {
    border-radius: 0.25rem;
}

.nav-tabs .nav-link {
    border: 1px solid transparent;
    border-radius: 0.375rem 0.375rem 0 0;
    margin-right: 0.25rem;
}

.nav-tabs .nav-link.active {
    border-color: #dee2e6 #dee2e6 #fff;
    background-color: #fff;
}

.nav-tabs .nav-link:hover {
    border-color: #e9ecef #e9ecef #dee2e6;
}
</style>
@endpush
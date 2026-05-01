@extends('admin.layout.app')

@section('title', __('Home Page Discount'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Home Page Discount') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Home Page Discount') }}</li>
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
                        <h5>{{ __('Edit Home Page Discount Section') }}</h5>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('admin.home-page-discount.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Background Image Section -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6>{{ __('Background Image') }}</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="bg_image" class="form-label">{{ __('Background Image') }}</label>
                                                    <input type="file" 
                                                           class="form-control @error('bg_image') is-invalid @enderror" 
                                                           id="bg_image" 
                                                           name="bg_image" 
                                                           accept="image/*">
                                                    @error('bg_image')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="form-text text-muted">{{ __('Recommended size: 1920x600px. Max size: 2MB.') }}</small>
                                                </div>
                                                <div class="col-md-6">
                                                    @if($homePageDiscount->bg_image)
                                                        <label class="form-label">{{ __('Current Background Image:') }}</label>
                                                        <div class="current-image-container">
                                                            <img src="{{ app('App\Services\ImageService')->getUrl($homePageDiscount->bg_image) }}" 
                                                                 alt="{{ __('Current Background Image') }}" 
                                                                 class="img-fluid rounded" 
                                                                 style="max-height: 200px; width: auto;">
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Multi-language Tabs -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                                        @foreach($languages as $index => $language)
                                            @php
                                                $translation = $homePageDiscount->translates->where('language_id', $language->id)->first();
                                                $hasErrors = $errors->has("translations.{$language->id}.*");
                                                $hasTranslation = $translation !== null;
                                            @endphp
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link @if($index === 0) active @endif" 
                                                        id="tab-{{ $language->id }}" 
                                                        data-bs-toggle="tab" 
                                                        data-bs-target="#content-{{ $language->id }}" 
                                                        type="button" 
                                                        role="tab" 
                                                        aria-controls="content-{{ $language->id }}" 
                                                        aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                                                        style="@if($hasErrors) border-color: #dc3545; @elseif($hasTranslation) border-color: #198754; @else border-color: #ffc107; @endif">
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

                                    <div class="tab-content" id="languageTabContent">
                                        @foreach($languages as $index => $language)
                                            @php
                                                $translation = $homePageDiscount->translates->where('language_id', $language->id)->first();
                                            @endphp
                                            <div class="tab-pane fade @if($index === 0) show active @endif" 
                                                 id="content-{{ $language->id }}" 
                                                 role="tabpanel" 
                                                 aria-labelledby="tab-{{ $language->id }}">
                                                
                                                <div class="row mt-3">
                                                    <div class="col-md-12 mb-3">
                                                        <label for="title_{{ $language->id }}" class="form-label">
                                                            {{ __('Title') }} <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" 
                                                               class="form-control @error("translations.{$language->id}.title") is-invalid @enderror" 
                                                               id="title_{{ $language->id }}" 
                                                               name="translations[{{ $language->id }}][title]" 
                                                               placeholder="{{ __('Enter title for :lang', ['lang' => $language->lang_code]) }}"
                                                               value="{{ old("translations.{$language->id}.title", $translation->title ?? '') }}">
                                                        @error("translations.{$language->id}.title")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <div class="col-md-12 mb-3">
                                                        <label for="subtitle_{{ $language->id }}" class="form-label">
                                                            {{ __('Subtitle') }} <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" 
                                                               class="form-control @error("translations.{$language->id}.subtitle") is-invalid @enderror" 
                                                               id="subtitle_{{ $language->id }}" 
                                                               name="translations[{{ $language->id }}][subtitle]" 
                                                               placeholder="{{ __('Enter subtitle for :lang', ['lang' => $language->lang_code]) }}"
                                                               value="{{ old("translations.{$language->id}.subtitle", $translation->subtitle ?? '') }}">
                                                        @error("translations.{$language->id}.subtitle")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <div class="col-md-12 mb-3">
                                                        <label for="button_link_{{ $language->id }}" class="form-label">
                                                            {{ __('Button Link') }} <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" 
                                                               class="form-control @error("translations.{$language->id}.button_link") is-invalid @enderror" 
                                                               id="button_link_{{ $language->id }}" 
                                                               name="translations[{{ $language->id }}][button_link]" 
                                                               placeholder="{{ __('Enter button link for :lang (e.g., /products, https://example.com)', ['lang' => $language->lang_code]) }}"
                                                               value="{{ old("translations.{$language->id}.button_link", $translation->button_link ?? '') }}">
                                                        @error("translations.{$language->id}.button_link")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                        <small class="form-text text-muted">{{ __('You can use relative URLs (e.g., /products) or absolute URLs (e.g., https://example.com)') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary" title="{{ __('Update Home Page Discount') }}">
                                            <i class="material-icons-outlined">save</i>
                                        </button>
                                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary" title="{{ __('Cancel') }}">
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
.nav-tabs .nav-link {
    border: 1px solid #dee2e6;
    border-bottom: none;
    border-radius: 0.375rem 0.375rem 0 0;
    color: #6c757d;
    background-color: #f8f9fa;
}

.nav-tabs .nav-link.active {
    color: #495057;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
}

.nav-tabs .nav-link:hover {
    border-color: #e9ecef #e9ecef #dee2e6;
}

.current-image-container {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 10px;
    background-color: #f8f9fa;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-switch to first tab with errors on form submission
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const tabsWithErrors = document.querySelectorAll('.nav-link[style*="border-color: #dc3545"]');
        if (tabsWithErrors.length > 0) {
            e.preventDefault();
            const firstTabWithError = tabsWithErrors[0];
            const tab = new bootstrap.Tab(firstTabWithError);
            tab.show();
        }
    });

    // Image preview functionality
    const imageInput = document.getElementById('bg_image');
    const currentImageContainer = document.querySelector('.current-image-container');
    
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Remove existing preview if any
                    const existingPreview = document.querySelector('.image-preview');
                    if (existingPreview) {
                        existingPreview.remove();
                    }
                    
                    // Create new preview
                    const previewContainer = document.createElement('div');
                    previewContainer.className = 'image-preview mt-2';
                    previewContainer.innerHTML = `
                        <label class="form-label">{{ __('Selected Image Preview:') }}</label>
                        <img src="${e.target.result}" alt="{{ __('Selected Image Preview') }}" class="img-fluid rounded" style="max-height: 200px; width: auto;">
                    `;
                    
                    // Insert after the file input
                    imageInput.parentNode.appendChild(previewContainer);
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>
@endpush
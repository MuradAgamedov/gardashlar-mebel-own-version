@extends('admin.layout.app')

@section('title', __('Edit Advantage'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Edit Advantage') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i> {{ __('Home') }}
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.become-a-partner.edit') }}">{{ __('Become A Partner') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Edit Advantage') }}</li>
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
                        <h5>{{ __('Edit Advantage') }}</h5>
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

                        <form action="{{ route('admin.become-a-partner-advantages.update', $becomeAPartnerAdvantage) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Icon Upload Section -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="icon" class="form-label">{{ __('Icon') }}</label>
                                    <input type="file" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" accept="image/*">
                                    @error('icon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">{{ __('Supported formats: JPG, JPEG, PNG, GIF, WEBP, SVG. Max size: 2MB') }}</small>
                                </div>
                                <div class="col-md-6">
                                    <div id="icon-preview" class="mt-3">
                                        @if($becomeAPartnerAdvantage->icon_url)
                                            <img id="preview-image" src="{{ $becomeAPartnerAdvantage->icon_url }}" alt="{{ __('Current Icon') }}" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                                            <p class="text-muted mt-2">{{ __('Current icon') }}</p>
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 150px; height: 150px;">
                                                <i class="material-icons-outlined text-muted">image</i>
                                            </div>
                                            <p class="text-muted mt-2">{{ __('No icon uploaded') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Status Section -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input type="hidden" name="status" value="0">
                                        <input class="form-check-input" type="checkbox" name="status" id="status" value="1" {{ old('status', $becomeAPartnerAdvantage->status) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">
                                            {{ __('Active Status') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Multi-language Tabs -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                                        @foreach($languages as $index => $language)
                                            @php
                                                $translation = $becomeAPartnerAdvantage->translates->where('language_id', $language->id)->first();
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
                                                $translation = $becomeAPartnerAdvantage->translates->where('language_id', $language->id)->first();
                                            @endphp
                                            <div class="tab-pane fade @if($index === 0) show active @endif" 
                                                 id="content-{{ $language->id }}" 
                                                 role="tabpanel" 
                                                 aria-labelledby="tab-{{ $language->id }}">
                                                
                                                <div class="row mt-3">
                                                    <div class="col-md-12">
                                                        <label for="icon_alt_text_{{ $language->id }}" class="form-label">
                                                            {{ __('Icon Alt Text') }} <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" 
                                                               class="form-control @error("translations.{$language->id}.icon_alt_text") is-invalid @enderror" 
                                                               id="icon_alt_text_{{ $language->id }}" 
                                                               name="translations[{{ $language->id }}][icon_alt_text]" 
                                                               value="{{ old("translations.{$language->id}.icon_alt_text", $translation->icon_alt_text ?? '') }}" 
                                                               placeholder="{{ __('Enter icon alt text for :lang', ['lang' => $language->lang_code]) }}">
                                                        @error("translations.{$language->id}.icon_alt_text")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mt-3">
                                                    <div class="col-md-12">
                                                        <label for="title_{{ $language->id }}" class="form-label">
                                                            {{ __('Title') }} <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" 
                                                               class="form-control @error("translations.{$language->id}.title") is-invalid @enderror" 
                                                               id="title_{{ $language->id }}" 
                                                               name="translations[{{ $language->id }}][title]" 
                                                               value="{{ old("translations.{$language->id}.title", $translation->title ?? '') }}" 
                                                               placeholder="{{ __('Enter title for :lang', ['lang' => $language->lang_code]) }}">
                                                        @error("translations.{$language->id}.title")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mt-3">
                                                    <div class="col-md-12">
                                                        <label for="description_{{ $language->id }}" class="form-label">
                                                            {{ __('Description') }} <span class="text-danger">*</span>
                                                        </label>
                                                        <textarea class="form-control @error("translations.{$language->id}.description") is-invalid @enderror" 
                                                                  id="description_{{ $language->id }}" 
                                                                  name="translations[{{ $language->id }}][description]" 
                                                                  rows="4" 
                                                                  placeholder="{{ __('Enter description for :lang', ['lang' => $language->lang_code]) }}">{{ old("translations.{$language->id}.description", $translation->description ?? '') }}</textarea>
                                                        @error("translations.{$language->id}.description")
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
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">save</i>
                                        </button>
                                        <a href="{{ route('admin.become-a-partner.edit') }}" class="btn btn-secondary">
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
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Icon preview functionality
    const iconInput = document.getElementById('icon');
    const iconPreview = document.getElementById('icon-preview');
    const previewImage = document.getElementById('preview-image');

    iconInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                if (previewImage) {
                    previewImage.src = e.target.result;
                } else {
                    // Create new image element if it doesn't exist
                    const newImage = document.createElement('img');
                    newImage.id = 'preview-image';
                    newImage.src = e.target.result;
                    newImage.alt = '{{ __('Icon Preview') }}';
                    newImage.className = 'img-thumbnail';
                    newImage.style = 'max-width: 150px; max-height: 150px;';
                    
                    iconPreview.innerHTML = '';
                    iconPreview.appendChild(newImage);
                    const p = document.createElement('p');
                    p.textContent = '{{ __('New icon preview') }}';
                    p.className = 'text-muted mt-2';
                    iconPreview.appendChild(p);
                }
            };
            reader.readAsDataURL(file);
        }
    });

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
});
</script>
@endpush 
@extends('admin.layout.app')

@section('title', 'Service Static Data')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Service Static Data</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Service Static Data</li>
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
                        <h5>Edit Service Static Data Section</h5>
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

                        <form action="{{ route('admin.service-static-data.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Images Section -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h6 class="mb-3">Images</h6>
                                </div>
                                
                                <!-- Card 1 Image -->
                                <div class="col-md-4 mb-3">
                                    <label for="card_1_image" class="form-label">Card 1 Image</label>
                                    <input type="file" 
                                           class="form-control image-input @error('card_1_image') is-invalid @enderror" 
                                           id="card_1_image" 
                                           name="card_1_image"
                                           accept="image/*"
                                           data-preview="preview_1">
                                    @error('card_1_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="mt-2">
                                        <div id="preview_1" class="image-preview">
                                            @if($serviceStaticData->card_1_image_url)
                                                <img src="{{ $serviceStaticData->card_1_image_url }}" alt="Current Card 1 Image" class="img-thumbnail" style="max-width: 200px;">
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 2 Image -->
                                <div class="col-md-4 mb-3">
                                    <label for="card_2_image" class="form-label">Card 2 Image</label>
                                    <input type="file" 
                                           class="form-control image-input @error('card_2_image') is-invalid @enderror" 
                                           id="card_2_image" 
                                           name="card_2_image"
                                           accept="image/*"
                                           data-preview="preview_2">
                                    @error('card_2_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="mt-2">
                                        <div id="preview_2" class="image-preview">
                                            @if($serviceStaticData->card_2_image_url)
                                                <img src="{{ $serviceStaticData->card_2_image_url }}" alt="Current Card 2 Image" class="img-thumbnail" style="max-width: 200px;">
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 3 Image -->
                                <div class="col-md-4 mb-3">
                                    <label for="card_3_image" class="form-label">Card 3 Image</label>
                                    <input type="file" 
                                           class="form-control image-input @error('card_3_image') is-invalid @enderror" 
                                           id="card_3_image" 
                                           name="card_3_image"
                                           accept="image/*"
                                           data-preview="preview_3">
                                    @error('card_3_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="mt-2">
                                        <div id="preview_3" class="image-preview">
                                            @if($serviceStaticData->card_3_image_url)
                                                <img src="{{ $serviceStaticData->card_3_image_url }}" alt="Current Card 3 Image" class="img-thumbnail" style="max-width: 200px;">
                                            @endif
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
                                                $translation = $serviceStaticData->translates->where('language_id', $language->id)->first();
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
                                                $translation = $serviceStaticData->translates->where('language_id', $language->id)->first();
                                            @endphp
                                            <div class="tab-pane fade @if($index === 0) show active @endif" 
                                                 id="content-{{ $language->id }}" 
                                                 role="tabpanel" 
                                                 aria-labelledby="tab-{{ $language->id }}">
                                                
                                                <div class="row mt-3">
                                                    <!-- Title -->
                                                    <div class="col-md-12 mb-3">
                                                        <label for="title_{{ $language->id }}" class="form-label">
                                                            Title
                                                        </label>
                                                        <input type="text" 
                                                               class="form-control @error("translations.{$language->id}.title") is-invalid @enderror" 
                                                               id="title_{{ $language->id }}" 
                                                               name="translations[{{ $language->id }}][title]" 
                                                               value="{{ old("translations.{$language->id}.title", $translation->title ?? '') }}"
                                                               placeholder="Enter title for {{ $language->lang_code }}">
                                                        @error("translations.{$language->id}.title")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <!-- Description -->
                                                    <div class="col-md-12 mb-3">
                                                        <label for="description_{{ $language->id }}" class="form-label">
                                                            Description
                                                        </label>
                                                        <textarea class="form-control @error("translations.{$language->id}.description") is-invalid @enderror" 
                                                                  id="description_{{ $language->id }}" 
                                                                  name="translations[{{ $language->id }}][description]" 
                                                                  rows="4"
                                                                  placeholder="Enter description for {{ $language->lang_code }}">{{ old("translations.{$language->id}.description", $translation->description ?? '') }}</textarea>
                                                        @error("translations.{$language->id}.description")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <!-- Card 1 -->
                                                    <div class="col-md-12 mb-4">
                                                        <div class="card border-primary">
                                                            <div class="card-header bg-primary text-white">
                                                                <h6 class="mb-0">Card 1</h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="card_1_image_title_{{ $language->id }}" class="form-label">
                                                                            Card 1 Image Title
                                                                        </label>
                                                                        <input type="text" 
                                                                               class="form-control @error("translations.{$language->id}.card_1_image_title") is-invalid @enderror" 
                                                                               id="card_1_image_title_{{ $language->id }}" 
                                                                               name="translations[{{ $language->id }}][card_1_image_title]" 
                                                                               value="{{ old("translations.{$language->id}.card_1_image_title", $translation->card_1_image_title ?? '') }}"
                                                                               placeholder="Enter card 1 image title">
                                                                        @error("translations.{$language->id}.card_1_image_title")
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="card_1_image_alt_text_{{ $language->id }}" class="form-label">
                                                                            Card 1 Image Alt Text
                                                                        </label>
                                                                        <input type="text" 
                                                                               class="form-control @error("translations.{$language->id}.card_1_image_alt_text") is-invalid @enderror" 
                                                                               id="card_1_image_alt_text_{{ $language->id }}" 
                                                                               name="translations[{{ $language->id }}][card_1_image_alt_text]" 
                                                                               value="{{ old("translations.{$language->id}.card_1_image_alt_text", $translation->card_1_image_alt_text ?? '') }}"
                                                                               placeholder="Enter card 1 image alt text">
                                                                        @error("translations.{$language->id}.card_1_image_alt_text")
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Card 2 -->
                                                    <div class="col-md-12 mb-4">
                                                        <div class="card border-success">
                                                            <div class="card-header bg-success text-white">
                                                                <h6 class="mb-0">Card 2</h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="card_2_image_title_{{ $language->id }}" class="form-label">
                                                                            Card 2 Image Title
                                                                        </label>
                                                                        <input type="text" 
                                                                               class="form-control @error("translations.{$language->id}.card_2_image_title") is-invalid @enderror" 
                                                                               id="card_2_image_title_{{ $language->id }}" 
                                                                               name="translations[{{ $language->id }}][card_2_image_title]" 
                                                                               value="{{ old("translations.{$language->id}.card_2_image_title", $translation->card_2_image_title ?? '') }}"
                                                                               placeholder="Enter card 2 image title">
                                                                        @error("translations.{$language->id}.card_2_image_title")
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="card_2_image_alt_text_{{ $language->id }}" class="form-label">
                                                                            Card 2 Image Alt Text
                                                                        </label>
                                                                        <input type="text" 
                                                                               class="form-control @error("translations.{$language->id}.card_2_image_alt_text") is-invalid @enderror" 
                                                                               id="card_2_image_alt_text_{{ $language->id }}" 
                                                                               name="translations[{{ $language->id }}][card_2_image_alt_text]" 
                                                                               value="{{ old("translations.{$language->id}.card_2_image_alt_text", $translation->card_2_image_alt_text ?? '') }}"
                                                                               placeholder="Enter card 2 image alt text">
                                                                        @error("translations.{$language->id}.card_2_image_alt_text")
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Card 3 -->
                                                    <div class="col-md-12 mb-4">
                                                        <div class="card border-warning">
                                                            <div class="card-header bg-warning text-dark">
                                                                <h6 class="mb-0">Card 3</h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="card_3_image_title_{{ $language->id }}" class="form-label">
                                                                            Card 3 Image Title
                                                                        </label>
                                                                        <input type="text" 
                                                                               class="form-control @error("translations.{$language->id}.card_3_image_title") is-invalid @enderror" 
                                                                               id="card_3_image_title_{{ $language->id }}" 
                                                                               name="translations[{{ $language->id }}][card_3_image_title]" 
                                                                               value="{{ old("translations.{$language->id}.card_3_image_title", $translation->card_3_image_title ?? '') }}"
                                                                               placeholder="Enter card 3 image title">
                                                                        @error("translations.{$language->id}.card_3_image_title")
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="card_3_image_alt_text_{{ $language->id }}" class="form-label">
                                                                            Card 3 Image Alt Text
                                                                        </label>
                                                                        <input type="text" 
                                                                               class="form-control @error("translations.{$language->id}.card_3_image_alt_text") is-invalid @enderror" 
                                                                               id="card_3_image_alt_text_{{ $language->id }}" 
                                                                               name="translations[{{ $language->id }}][card_3_image_alt_text]" 
                                                                               value="{{ old("translations.{$language->id}.card_3_image_alt_text", $translation->card_3_image_alt_text ?? '') }}"
                                                                               placeholder="Enter card 3 image alt text">
                                                                        @error("translations.{$language->id}.card_3_image_alt_text")
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
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
                                        <button type="submit" class="btn btn-primary" title="Update Service Static Data">
                                            <i class="material-icons-outlined">save</i>
                                        </button>
                                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary" title="Cancel">
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
    document.querySelectorAll('.image-input').forEach(function(input) {
        input.addEventListener('change', function() {
            const file = this.files[0];
            const previewId = this.getAttribute('data-preview');
            const previewDiv = document.getElementById(previewId);
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewDiv.innerHTML = `<img src="${e.target.result}" alt="Preview" class="img-thumbnail" style="max-width: 200px;">`;
                };
                reader.readAsDataURL(file);
            } else {
                previewDiv.innerHTML = '';
            }
        });
    });
});
</script>
@endpush 
@extends('admin.layout.app')

@section('title', __('Create Advantage'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Create Advantage') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.become-a-investor.edit') }}">{{ __('Become A Investor') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Create Advantage') }}</li>
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
                        <h5>{{ __('Create New Advantage') }}</h5>
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

                        <form action="{{ route('admin.become-a-investor-advantages.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Icon Upload Section -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="icon" class="form-label">{{ __('Icon') }} <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" accept="image/*">
                                    @error('icon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">{{ __('Supported formats: JPG, JPEG, PNG, GIF, WEBP, SVG. Max size: 2MB') }}</small>
                                </div>
                                <div class="col-md-6">
                                    <div id="icon-preview" class="mt-3" style="display: none;">
                                        <img id="preview-image" src="" alt="{{ __('Icon Preview') }}" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                                    </div>
                                </div>
                            </div>

                            <!-- Status Section -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input type="hidden" name="status" value="0">
                                        <input class="form-check-input" type="checkbox" name="status" id="status" value="1" {{ old('status', '1') ? 'checked' : '' }}>
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
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link @if($index === 0) active @endif" 
                                                        id="tab-{{ $language->id }}" 
                                                        data-bs-toggle="tab" 
                                                        data-bs-target="#content-{{ $language->id }}" 
                                                        type="button" 
                                                        role="tab" 
                                                        aria-controls="content-{{ $language->id }}" 
                                                        aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                                                        style="@if($errors->has("translations.{$language->id}.*")) border-color: #dc3545; @endif">
                                                    {{ $language->lang_code }}
                                                    @if($errors->has("translations.{$language->id}.*"))
                                                        <i class="material-icons-outlined text-danger ms-1" style="font-size: 16px;">error</i>
                                                    @endif
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <div class="tab-content" id="languageTabContent">
                                        @foreach($languages as $index => $language)
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
                                                               value="{{ old("translations.{$language->id}.icon_alt_text") }}" 
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
                                                               value="{{ old("translations.{$language->id}.title") }}" 
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
                                                                  placeholder="{{ __('Enter description for :lang', ['lang' => $language->lang_code]) }}">{{ old("translations.{$language->id}.description") }}</textarea>
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
                                        <a href="{{ route('admin.become-a-investor.edit') }}" class="btn btn-secondary">
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
                previewImage.src = e.target.result;
                iconPreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            iconPreview.style.display = 'none';
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
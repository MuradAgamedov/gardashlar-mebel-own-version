@extends('admin.layout.app')

@section('title', __('Add Our Goal'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Add Our Goal') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.our-goals.index') }}">{{ __('Our Goals') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Add Our Goal') }}</li>
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
                        <h5>{{ __('Add New Our Goal') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.our-goals.store') }}" method="POST">
                            @csrf
                            
                            <!-- Status -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ old('status', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">
                                            {{ __('Active Status') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Language Tabs -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>{{ __('Translations') }}</h5>
                                        </div>
                                        <div class="card-body">
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
                                            
                                            <div class="tab-content mt-3" id="languageTabContent">
                                                @foreach($languages as $index => $language)
                                                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                                         id="content-{{ $language->id }}" 
                                                         role="tabpanel" 
                                                         aria-labelledby="tab-{{ $language->id }}">
                                                        <div class="row">
                                                            <!-- Title -->
                                                            <div class="col-md-12">
                                                                <label class="form-label">{{ __('Title') }} <span class="text-danger">*</span></label>
                                                                <input type="text" 
                                                                       class="form-control @error("translations.{$language->id}.title") is-invalid @enderror" 
                                                                       name="translations[{{ $language->id }}][title]" 
                                                                       value="{{ old("translations.{$language->id}.title") }}" 
                                                                       placeholder="{{ __('Title for :lang', ['lang' => $language->lang_name]) }}"
                                                                       required>
                                                                @error("translations.{$language->id}.title")
                                                                    <div class="invalid-feedback">
                                                                        {{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                            
                                                            <!-- Description -->
                                                            <div class="col-md-12">
                                                                <label class="form-label">{{ __('Description') }} <span class="text-danger">*</span></label>
                                                                <textarea class="form-control @error("translations.{$language->id}.description") is-invalid @enderror" 
                                                                          name="translations[{{ $language->id }}][description]" 
                                                                          rows="4"
                                                                          placeholder="{{ __('Description for :lang', ['lang' => $language->lang_name]) }}"
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
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">save</i>
                                        </button>
                                        <a href="{{ route('admin.our-goals.index') }}" class="btn btn-secondary">
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
.invalid-feedback {
    display: block !important;
}
.is-invalid {
    border-color: #dc3545 !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('input[required], textarea[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('{{ __("Please fill in all required fields.") }}');
        }
    });
});
</script>
@endpush 
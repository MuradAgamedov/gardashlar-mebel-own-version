@extends('admin.layout.app')

@section('title', 'Create Vacancy')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Create Vacancy</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('admin/assets/svg/iconly-sprite.svg#stroke-home') }}"></use>
                                </svg>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.vacancies.index') }}">Vacancies</a>
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

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Create New Vacancy</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.vacancies.store') }}" method="POST">
                            @csrf
                            
                            <!-- Basic Information Section -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h6 class="mb-3">Basic Information</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" 
                                                   class="form-control @error('email') is-invalid @enderror" 
                                                   name="email" 
                                                   value="{{ old('email') }}"
                                                   placeholder="Enter contact email">
                                            @error('email')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Last Apply Date <span class="text-danger">*</span></label>
                                            <input type="date" 
                                                   class="form-control @error('last_apply_date') is-invalid @enderror" 
                                                   name="last_apply_date" 
                                                   value="{{ old('last_apply_date') }}"
                                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                            @error('last_apply_date')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Address <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                                      name="address" 
                                                      rows="3"
                                                      placeholder="Enter vacancy address">{{ old('address') }}</textarea>
                                            @error('address')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Content Translations Section -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h6 class="mb-3">Content Translations</h6>
                                    
                                    <!-- Language Tabs -->
                                    <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                                        @foreach($languages as $index => $language)
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                                        id="tab-{{ $language->id }}" 
                                                        data-bs-toggle="tab" 
                                                        data-bs-target="#content-{{ $language->id }}" 
                                                        type="button" 
                                                        role="tab" 
                                                        aria-controls="content-{{ $language->id }}" 
                                                        aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                                    {{ $language->lang_code }}
                                                    @php
                                                        $hasErrors = $errors->has("translations.{$language->id}.*");
                                                    @endphp
                                                    @if($hasErrors)
                                                        <i class="fa fa-exclamation-triangle text-danger ms-1"></i>
                                                    @endif
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <!-- Tab Content -->
                                    <div class="tab-content" id="languageTabContent">
                                        @foreach($languages as $index => $language)
                                            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                                 id="content-{{ $language->id }}" 
                                                 role="tabpanel" 
                                                 aria-labelledby="tab-{{ $language->id }}">
                                                
                                                <div class="row mt-3">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Title <span class="text-danger">*</span></label>
                                                        <input type="text" 
                                                               class="form-control @error("translations.{$language->id}.title") is-invalid @enderror" 
                                                               name="translations[{{ $language->id }}][title]" 
                                                               value="{{ old("translations.{$language->id}.title") }}"
                                                               placeholder="Enter title for {{ $language->lang_code }}">
                                                        @error("translations.{$language->id}.title")
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Slug</label>
                                                        <input type="text" 
                                                               class="form-control @error("translations.{$language->id}.slug") is-invalid @enderror" 
                                                               name="translations[{{ $language->id }}][slug]" 
                                                               value="{{ old("translations.{$language->id}.slug") }}"
                                                               placeholder="Auto-generated slug for {{ $language->lang_code }}">
                                                        @error("translations.{$language->id}.slug")
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                
                                                <div class="row mt-3">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Position <span class="text-danger">*</span></label>
                                                        <input type="text" 
                                                               class="form-control @error("translations.{$language->id}.position") is-invalid @enderror" 
                                                               name="translations[{{ $language->id }}][position]" 
                                                               value="{{ old("translations.{$language->id}.position") }}"
                                                               placeholder="Enter position for {{ $language->lang_code }}">
                                                        @error("translations.{$language->id}.position")
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                
                                                <div class="row mt-3">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Experience <span class="text-danger">*</span></label>
                                                        <input type="text" 
                                                               class="form-control @error("translations.{$language->id}.experience") is-invalid @enderror" 
                                                               name="translations[{{ $language->id }}][experience]" 
                                                               value="{{ old("translations.{$language->id}.experience") }}"
                                                               placeholder="Enter experience for {{ $language->lang_code }}">
                                                        @error("translations.{$language->id}.experience")
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Work Schedule <span class="text-danger">*</span></label>
                                                        <textarea class="form-control @error("translations.{$language->id}.work_schedule") is-invalid @enderror" 
                                                                  name="translations[{{ $language->id }}][work_schedule]" 
                                                                  rows="3"
                                                                  placeholder="Enter work schedule for {{ $language->lang_code }}">{{ old("translations.{$language->id}.work_schedule") }}</textarea>
                                                        @error("translations.{$language->id}.work_schedule")
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!-- SEO Section -->
                                                <div class="row mt-3">
                                                    <div class="col-md-12">
                                                        <h6 class="mb-3">SEO Settings</h6>
                                                    </div>
                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label">SEO Title</label>
                                                        <input type="text" 
                                                               class="form-control @error("translations.{$language->id}.seo_title") is-invalid @enderror" 
                                                               name="translations[{{ $language->id }}][seo_title]" 
                                                               value="{{ old("translations.{$language->id}.seo_title") }}"
                                                               placeholder="Enter SEO title for {{ $language->lang_code }}">
                                                        @error("translations.{$language->id}.seo_title")
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label">SEO Keywords</label>
                                                        <textarea class="form-control @error("translations.{$language->id}.seo_keywords") is-invalid @enderror" 
                                                                  name="translations[{{ $language->id }}][seo_keywords]" 
                                                                  rows="3"
                                                                  placeholder="Enter keywords separated by commas for {{ $language->lang_code }}">{{ old("translations.{$language->id}.seo_keywords") }}</textarea>
                                                        @error("translations.{$language->id}.seo_keywords")
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label">SEO Description</label>
                                                        <textarea class="form-control @error("translations.{$language->id}.seo_description") is-invalid @enderror" 
                                                                  name="translations[{{ $language->id }}][seo_description]" 
                                                                  rows="3"
                                                                  placeholder="Enter SEO description for {{ $language->lang_code }}">{{ old("translations.{$language->id}.seo_description") }}</textarea>
                                                        @error("translations.{$language->id}.seo_description")
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
                                        <input class="form-check-input" type="checkbox" name="status" id="status" value="1" {{ old('status', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">
                                            Active Status
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">save</i>
                                        </button>
                                        <a href="{{ route('admin.vacancies.index') }}" class="btn btn-secondary">
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

@include('admin.partials.slug-generator')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-switch to first tab with errors on page load
    const tabsWithErrors = document.querySelectorAll('.nav-link .fa-exclamation-triangle.text-danger');
    if (tabsWithErrors.length > 0) {
        const firstErrorTab = tabsWithErrors[0].closest('.nav-link');
        const tab = new bootstrap.Tab(firstErrorTab);
        tab.show();
    }

    // Auto-switch to first tab with errors on form submission
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const tabsWithErrors = document.querySelectorAll('.nav-link .fa-exclamation-triangle.text-danger');
        if (tabsWithErrors.length > 0) {
            e.preventDefault();
            const firstErrorTab = tabsWithErrors[0].closest('.nav-link');
            const tab = new bootstrap.Tab(firstErrorTab);
            tab.show();
            
            // Scroll to first error field
            const firstErrorField = document.querySelector('.is-invalid');
            if (firstErrorField) {
                firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });

    // Highlight error fields on focus
    document.querySelectorAll('.is-invalid').forEach(function(field) {
        field.addEventListener('focus', function() {
            this.style.borderColor = '#dc3545';
            this.style.boxShadow = '0 0 0 0.2rem rgba(220, 53, 69, 0.25)';
        });
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

.is-invalid {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

.nav-link .fa-exclamation-triangle.text-danger {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.form-control.is-invalid:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}
</style>
@endpush 
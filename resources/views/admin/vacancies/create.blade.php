@extends('admin.layout.app')

@section('title', __('Create Vacancy'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Create Vacancy') }}</h3>
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
                            <a href="{{ route('admin.vacancies.index') }}">{{ __('Vacancies') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Create') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
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
                        <h5>{{ __('Create New Vacancy') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.vacancies.store') }}" method="POST">
                            @csrf
                            
                            <!-- Basic Information Section -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h6 class="mb-3">{{ __('Basic Information') }}</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ __('Email') }} <span class="text-danger">*</span></label>
                                            <input type="email" 
                                                   class="form-control @error('email') is-invalid @enderror" 
                                                   name="email" 
                                                   value="{{ old('email') }}"
                                                   placeholder="{{ __('Enter contact email') }}">
                                            @error('email')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- ✅ Phone Field -->
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ __('Phone') }}</label>
                                            <input type="text" 
                                                   class="form-control @error('phone') is-invalid @enderror" 
                                                   name="phone" 
                                                   value="{{ old('phone') }}"
                                                   placeholder="{{ __('Enter phone number') }}">
                                            @error('phone')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- ✅ END Phone Field -->

                                        <!-- ✅ Age Field -->
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ __('Age') }}</label>
                                            <input type="number" 
                                                   class="form-control @error('age') is-invalid @enderror" 
                                                   name="age" 
                                                   value="{{ old('age') }}"
                                                   placeholder="{{ __('Enter age requirement') }}">
                                            @error('age')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- ✅ END Age Field -->

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ __('Last Apply Date') }}</label>
                                            <input type="date" 
                                                   class="form-control @error('last_apply_date') is-invalid @enderror" 
                                                   name="last_apply_date" 
                                                   value="{{ old('last_apply_date') }}"
                                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                            @error('last_apply_date')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
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
                                                        <label class="form-label">{{ __('Title') }} <span class="text-danger">*</span></label>
                                                        <input type="text" 
                                                               class="form-control" 
                                                               name="translations[{{ $language->id }}][title]" 
                                                               value="{{ old("translations.{$language->id}.title") }}"
                                                               placeholder="{{ __('Enter title for :lang', ['lang' => $language->lang_code]) }}">
                                                    </div>
                                                    
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">{{ __('Slug') }}</label>
                                                        <input type="text" 
                                                               class="form-control" 
                                                               name="translations[{{ $language->id }}][slug]" 
                                                               value="{{ old("translations.{$language->id}.slug") }}"
                                                               placeholder="{{ __('Auto-generated slug for :lang', ['lang' => $language->lang_code]) }}">
                                                    </div>
                                                </div>

                                                <div class="row mt-3">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">{{ __('Position') }}</label>
                                                        <input type="text" 
                                                               class="form-control" 
                                                               name="translations[{{ $language->id }}][position]" 
                                                               value="{{ old("translations.{$language->id}.position") }}"
                                                               placeholder="{{ __('Enter position for :lang', ['lang' => $language->lang_code]) }}">
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">{{ __('Experience') }}</label>
                                                        <input type="text" 
                                                               class="form-control" 
                                                               name="translations[{{ $language->id }}][experience]" 
                                                               value="{{ old("translations.{$language->id}.experience") }}"
                                                               placeholder="{{ __('Enter experience for :lang', ['lang' => $language->lang_code]) }}">
                                                    </div>
                                                </div>

                                                <div class="row mt-3">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">{{ __('Work Schedule') }}</label>
                                                        <textarea class="form-control" 
                                                                  name="translations[{{ $language->id }}][work_schedule]" 
                                                                  rows="3"
                                                                  placeholder="{{ __('Enter work schedule for :lang', ['lang' => $language->lang_code]) }}">{{ old("translations.{$language->id}.work_schedule") }}</textarea>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">{{ __('Salary') }}</label>
                                                        <input type="text" 
                                                               class="form-control" 
                                                               name="translations[{{ $language->id }}][salary]" 
                                                               value="{{ old("translations.{$language->id}.salary") }}"
                                                               placeholder="{{ __('Enter salary for :lang', ['lang' => $language->lang_code]) }}">
                                                    </div>
                                                </div>

                                                <!-- ✅ Address Field -->
                                                <div class="row mt-3">
                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label">{{ __('Address') }}</label>
                                                        <textarea class="form-control" 
                                                                  name="translations[{{ $language->id }}][address]" 
                                                                  rows="3"
                                                                  placeholder="{{ __('Enter address for :lang', ['lang' => $language->lang_code]) }}">{{ old("translations.{$language->id}.address") }}</textarea>
                                                    </div>
                                                </div>
                                                <!-- ✅ END Address -->

                                                <!-- ✅ Additional Fields -->
                                                <div class="row mt-3">
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">{{ __('Education') }}</label>
                                                        <input type="text" 
                                                               class="form-control" 
                                                               name="translations[{{ $language->id }}][education]" 
                                                               value="{{ old("translations.{$language->id}.education") }}"
                                                               placeholder="{{ __('Enter education requirement for :lang', ['lang' => $language->lang_code]) }}">
                                                    </div>

                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">{{ __('Related Person') }}</label>
                                                        <input type="text" 
                                                               class="form-control" 
                                                               name="translations[{{ $language->id }}][related_person]" 
                                                               value="{{ old("translations.{$language->id}.related_person") }}"
                                                               placeholder="{{ __('Enter related person for :lang', ['lang' => $language->lang_code]) }}">
                                                    </div>

                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">{{ __('City') }}</label>
                                                        <input type="text" 
                                                               class="form-control" 
                                                               name="translations[{{ $language->id }}][city]" 
                                                               value="{{ old("translations.{$language->id}.city") }}"
                                                               placeholder="{{ __('Enter city for :lang', ['lang' => $language->lang_code]) }}">
                                                    </div>
                                                </div>
                                                <!-- ✅ END Additional Fields -->

                                                <!-- SEO Section -->
                                                <div class="row mt-3">
                                                    <div class="col-md-12">
                                                        <h6 class="mb-3">{{ __('SEO Settings') }}</h6>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">{{ __('SEO Title') }}</label>
                                                        <input type="text" 
                                                               class="form-control" 
                                                               name="translations[{{ $language->id }}][seo_title]" 
                                                               value="{{ old("translations.{$language->id}.seo_title") }}">
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">{{ __('SEO Keywords') }}</label>
                                                        <textarea class="form-control" 
                                                                  name="translations[{{ $language->id }}][seo_keywords]" 
                                                                  rows="3">{{ old("translations.{$language->id}.seo_keywords") }}</textarea>
                                                    </div>

                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label">{{ __('SEO Description') }}</label>
                                                        <textarea class="form-control" 
                                                                  name="translations[{{ $language->id }}][seo_description]" 
                                                                  rows="3">{{ old("translations.{$language->id}.seo_description") }}</textarea>
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
                                            {{ __('Active Status') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary" title="{{ __('Create Vacancy') }}">
                                            <i class="material-icons-outlined">save</i>
                                        </button>
                                        <a href="{{ route('admin.vacancies.index') }}" class="btn btn-secondary" title="{{ __('Cancel') }}">
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
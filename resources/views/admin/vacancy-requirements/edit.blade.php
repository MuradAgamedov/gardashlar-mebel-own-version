@extends('admin.layout.app')

@section('title', 'Edit Vacancy Requirement')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Edit Vacancy Requirement</h3>
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
                            <a href="{{ route('admin.vacancy-requirements.index') }}">Vacancy Requirements</a>
                        </li>
                        <li class="breadcrumb-item active">Edit</li>
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
                        <h5>Edit Vacancy Requirement</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.vacancy-requirements.update', $vacancyRequirement) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <!-- Basic Information Section -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h6 class="mb-3">Basic Information</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Vacancy <span class="text-danger">*</span></label>
                                            <select class="form-control @error('vacancy_id') is-invalid @enderror" name="vacancy_id">
                                                <option value="">Select a vacancy</option>
                                                @foreach($vacancies as $vacancy)
                                                    @php
                                                        $vacancyTranslation = $vacancy->translates->where('language.is_default', true)->first() 
                                                            ?? $vacancy->translates->first();
                                                    @endphp
                                                    <option value="{{ $vacancy->id }}" {{ old('vacancy_id', $vacancyRequirement->vacancy_id) == $vacancy->id ? 'selected' : '' }}>
                                                        {{ $vacancyTranslation->title ?? 'N/A' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('vacancy_id')
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
                                            @php
                                                $translation = $vacancyRequirement->translates->where('language_id', $language->id)->first();
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
                                                        aria-controls="content-{{ $language->id }}" 
                                                        aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                                                        style="{{ $hasErrors ? 'border-color: #dc3545;' : '' }}">
                                                    {{ $language->lang_code }}
                                                    @if($hasTranslation)
                                                        <i class="fa fa-check text-success ms-1"></i>
                                                    @elseif($hasErrors)
                                                        <i class="fa fa-exclamation-triangle text-danger ms-1"></i>
                                                    @else
                                                        <i class="fa fa-exclamation-triangle text-warning ms-1"></i>
                                                    @endif
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <!-- Tab Content -->
                                    <div class="tab-content" id="languageTabContent">
                                        @foreach($languages as $index => $language)
                                            @php
                                                $translation = $vacancyRequirement->translates->where('language_id', $language->id)->first();
                                            @endphp
                                            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                                 id="content-{{ $language->id }}" 
                                                 role="tabpanel" 
                                                 aria-labelledby="tab-{{ $language->id }}">
                                                
                                                <div class="row mt-3">
                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label">Title <span class="text-danger">*</span></label>
                                                        <input type="text" 
                                                               class="form-control @error("translations.{$language->id}.title") is-invalid @enderror" 
                                                               name="translations[{{ $language->id }}][title]" 
                                                               value="{{ old("translations.{$language->id}.title", $translation->title ?? '') }}"
                                                               placeholder="Enter title for {{ $language->lang_code }}">
                                                        @error("translations.{$language->id}.title")
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label">Description <span class="text-danger">*</span></label>
                                                        <textarea class="form-control ckeditor @error("translations.{$language->id}.description") is-invalid @enderror" 
                                                                  name="translations[{{ $language->id }}][description]" 
                                                                  id="description-{{ $language->id }}"
                                                                  placeholder="Enter description for {{ $language->lang_code }}">{{ old("translations.{$language->id}.description", $translation->description ?? '') }}</textarea>
                                                        @error("translations.{$language->id}.description")
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
                                        <input class="form-check-input" type="checkbox" name="status" id="status" value="1" {{ old('status', $vacancyRequirement->status) ? 'checked' : '' }}>
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
                                        <a href="{{ route('admin.vacancy-requirements.index') }}" class="btn btn-secondary">
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
<script src="https://cdn.ckeditor.com/ckeditor5/27.1.0/classic/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize CKEditor for each language tab
    @foreach($languages as $language)
        ClassicEditor
            .create(document.querySelector('#description-{{ $language->id }}'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'blockQuote', 'insertTable', 'undo', 'redo'],
                height: 300
            })
            .catch(error => {
                console.error(error);
            });
    @endforeach

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

.ck-editor__editable {
    min-height: 200px;
}
</style>
@endpush 
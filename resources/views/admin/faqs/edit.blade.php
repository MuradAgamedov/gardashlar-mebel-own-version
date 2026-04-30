@extends('admin.layout.app')

@section('title', __('Edit FAQ'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Edit FAQ') }}</h3>
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
                            <a href="{{ route('admin.faqs.index') }}">{{ __('FAQs') }}</a>
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

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('Edit FAQ') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.faqs.update', $faq) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <!-- Status Section -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="status" value="0">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ old('status', $faq->status ? '1' : '0') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">
                                            {{ __('Active') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Content Translations Section -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h6 class="mb-3">{{ __('Content Translations') }}</h6>
                                    
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
                                    
                                    <!-- Language Tabs -->
                                    <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                                        @foreach($languages as $index => $language)
                                        @php
                                            $translate = $faq->translates->where('language_id', $language->id)->first();
                                        @endphp
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link {{ $index === 0 ? 'active' : '' }} {{ $errors->has('translations.'.$index.'.*') ? 'border-danger' : '' }}" 
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
                                                @if($errors->has('translations.'.$index.'.*'))
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
                                            $translate = $faq->translates->where('language_id', $language->id)->first();
                                        @endphp
                                        <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                             id="content-{{ $language->lang_code }}" 
                                             role="tabpanel" 
                                             aria-labelledby="tab-{{ $language->lang_code }}">
                                            
                                            <div class="card mt-3">
                                                <div class="card-header">
                                                    <h6 class="mb-0">{{ __(':lang Content', ['lang' => $language->title]) }}</h6>
                                                    @if($translate)
                                                        <small class="text-success">{{ __('Translation exists') }}</small>
                                                    @else
                                                        <small class="text-warning">{{ __('No translation yet') }}</small>
                                                    @endif
                                                </div>
                                                <div class="card-body">
                                                    <input type="hidden" name="translations[{{ $index }}][language_id]" value="{{ $language->id }}">
                                                    
                                                    <div class="row">
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">{{ __('Question') }} <span class="text-danger">*</span></label>
                                                            <input type="text" 
                                                                   class="form-control @error('translations.'.$index.'.question') is-invalid @enderror" 
                                                                   name="translations[{{ $index }}][question]" 
                                                                   value="{{ old('translations.'.$index.'.question', $translate->question ?? '') }}"
                                                                   placeholder="{{ __('Enter question for :lang', ['lang' => $language->lang_code]) }}">
                                                            @error('translations.'.$index.'.question')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">{{ __('Answer') }} <span class="text-danger">*</span></label>
                                                            <textarea class="form-control ckeditor @error('translations.'.$index.'.answer') is-invalid @enderror" 
                                                                      name="translations[{{ $index }}][answer]" 
                                                                      id="answer-{{ $language->id }}"
                                                                      placeholder="{{ __('Enter answer for :lang', ['lang' => $language->lang_code]) }}">{{ old('translations.'.$index.'.answer', $translate->answer ?? '') }}</textarea>
                                                            @error('translations.'.$index.'.answer')
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
                                        </button>
                                        <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary">
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
            .create(document.querySelector('#answer-{{ $language->id }}'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'blockQuote', 'insertTable', 'undo', 'redo'],
                height: 400
            })
            .catch(error => {
                console.error(error);
            });
    @endforeach

    // Auto-switch to first tab with errors on form submission
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const tabsWithErrors = document.querySelectorAll('.nav-link .fa-exclamation-triangle.text-danger');
        if (tabsWithErrors.length > 0) {
            e.preventDefault();
            const firstErrorTab = tabsWithErrors[0].closest('.nav-link');
            const tab = new bootstrap.Tab(firstErrorTab);
            tab.show();
        }
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

.ck-editor__editable {
    min-height: 300px;
}
</style>
@endpush 
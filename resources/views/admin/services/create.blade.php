@extends('admin.layout.app')

@section('title', __('Create Service'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Create Service') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.services.index') }}">{{ __('Services') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Create') }}</li>
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
                        <h5>{{ __('Create New Service') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.services.store') }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="status" value="0">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ old('status', '1') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">
                                            {{ __('Active') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6>{{ __('Translations') }}</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="nav nav-tabs" id="languageTabs" role="tablist">
                                                @foreach($languages as $index => $language)
                                                    <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                                            id="tab-{{ $language->id }}" 
                                                            data-bs-toggle="tab" 
                                                            data-bs-target="#content-{{ $language->id }}" 
                                                            type="button" 
                                                            role="tab">
                                                        {{ $language->title }}
                                                    </button>
                                                @endforeach
                                            </div>
                                            
                                            <div class="tab-content mt-3" id="languageTabContent">
                                                @foreach($languages as $index => $language)
                                                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                                         id="content-{{ $language->id }}" 
                                                         role="tabpanel">
                                                        <div class="row">
                                                            <input type="hidden" name="translates[{{ $index }}][language_id]" value="{{ $language->id }}">
                                                            
                                                            <div class="col-md-12 mb-3">
                                                                <label class="form-label">{{ __('Title') }} *</label>
                                                                <input type="text" 
                                                                       class="form-control @error('translates.'.$index.'.title') is-invalid @enderror" 
                                                                       name="translates[{{ $index }}][title]" 
                                                                       value="{{ old('translates.'.$index.'.title') }}"
                                                                       placeholder="{{ __('Enter title') }}">
                                                                @error('translates.'.$index.'.title')
                                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            
                                                            <div class="col-md-12 mb-3">
                                                                <label class="form-label">{{ __('Slug') }}</label>
                                                                <input type="text" 
                                                                       class="form-control @error('translates.'.$index.'.slug') is-invalid @enderror" 
                                                                       name="translates[{{ $index }}][slug]" 
                                                                       value="{{ old('translates.'.$index.'.slug') }}"
                                                                       placeholder="{{ __('Enter slug') }}">
                                                                @error('translates.'.$index.'.slug')
                                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            
                                                            <div class="col-md-12 mb-3">
                                                                <label class="form-label">{{ __('Card Description') }} *</label>
                                                                <textarea class="form-control @error('translates.'.$index.'.card_description') is-invalid @enderror" 
                                                                          name="translates[{{ $index }}][card_description]" 
                                                                          rows="3" 
                                                                          placeholder="{{ __('Enter card description') }}">{{ old('translates.'.$index.'.card_description') }}</textarea>
                                                                @error('translates.'.$index.'.card_description')
                                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            
                                                            <div class="col-md-12 mb-3">
                                                                <label class="form-label">{{ __('Main Description') }} *</label>
                                                                <textarea class="form-control ckeditor @error('translates.'.$index.'.main_description') is-invalid @enderror" 
                                                                          name="translates[{{ $index }}][main_description]" 
                                                                          rows="5" 
                                                                          placeholder="{{ __('Enter main description') }}">{{ old('translates.'.$index.'.main_description') }}</textarea>
                                                                @error('translates.'.$index.'.main_description')
                                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            
                                                            <div class="col-md-12 mb-3">
                                                                <label class="form-label">{{ __('Form Top Text') }}</label>
                                                                <textarea class="form-control ckeditor @error('translates.'.$index.'.form_top_text') is-invalid @enderror" 
                                                                          name="translates[{{ $index }}][form_top_text]" 
                                                                          rows="5" 
                                                                          placeholder="{{ __('Enter form top text') }}">{{ old('translates.'.$index.'.form_top_text') }}</textarea>
                                                                @error('translates.'.$index.'.form_top_text')
                                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            
                                                            <div class="col-md-12 mb-3">
                                                                <label class="form-label">{{ __('Advantages Title') }}</label>
                                                                <input type="text" 
                                                                       class="form-control @error('translates.'.$index.'.advantages_title') is-invalid @enderror" 
                                                                       name="translates[{{ $index }}][advantages_title]" 
                                                                       value="{{ old('translates.'.$index.'.advantages_title') }}"
                                                                       placeholder="{{ __('Enter advantages title') }}">
                                                                @error('translates.'.$index.'.advantages_title')
                                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            
                                                            <div class="col-md-12 mb-3">
                                                                <label class="form-label">{{ __('SEO Title') }}</label>
                                                                <input type="text" 
                                                                       class="form-control @error('translates.'.$index.'.seo_title') is-invalid @enderror" 
                                                                       name="translates[{{ $index }}][seo_title]" 
                                                                       value="{{ old('translates.'.$index.'.seo_title') }}"
                                                                       placeholder="{{ __('Enter SEO title') }}">
                                                                @error('translates.'.$index.'.seo_title')
                                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            
                                                            <div class="col-md-12 mb-3">
                                                                <label class="form-label">{{ __('SEO Keywords') }}</label>
                                                                <textarea class="form-control @error('translates.'.$index.'.seo_keywords') is-invalid @enderror" 
                                                                          name="translates[{{ $index }}][seo_keywords]" 
                                                                          rows="3" 
                                                                          placeholder="{{ __('Enter keywords separated by commas') }}">{{ old('translates.'.$index.'.seo_keywords') }}</textarea>
                                                                @error('translates.'.$index.'.seo_keywords')
                                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            
                                                            <div class="col-md-12 mb-3">
                                                                <label class="form-label">{{ __('SEO Description') }}</label>
                                                                <textarea class="form-control @error('translates.'.$index.'.seo_description') is-invalid @enderror" 
                                                                          name="translates[{{ $index }}][seo_description]" 
                                                                          rows="3" 
                                                                          placeholder="{{ __('Enter SEO description') }}">{{ old('translates.'.$index.'.seo_description') }}</textarea>
                                                                @error('translates.'.$index.'.seo_description')
                                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary" title="{{ __('Create Service') }}">
                                            <i class="material-icons-outlined">save</i>
                                        </button>
                                        <a href="{{ route('admin.services.index') }}" class="btn btn-secondary" title="{{ __('Cancel') }}">
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
    // Initialize CKEditor for all ckeditor textareas
    const ckeditorElements = document.querySelectorAll('.ckeditor');
    ckeditorElements.forEach(function(element) {
        ClassicEditor
            .create(element, {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'blockQuote', 'insertTable', 'undo', 'redo'],
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                    ]
                }
            })
            .catch(error => {
                console.error(error);
            });
    });
});
</script>
@endpush 
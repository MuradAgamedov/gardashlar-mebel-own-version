@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Create New SEO Page</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.seo-pages.index') }}">SEO Pages</a>
                        </li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="material-icons-outlined">error</i>
                <strong>Validation Errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
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
                        <h5>Add New SEO Page</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.seo-pages.store') }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="status" value="0">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" checked>
                                        <label class="form-check-label" for="status">
                                            Active
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Category</label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" name="category_id">
                                        <option value="">{{ app()->getLocale() === 'az' ? 'Kateqoriya seçin (İsteğe bağlı)' : 'Select Category (Optional)' }}</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                @php
                                                    $mainLanguageId = \App\Models\Language::getMainLanguage()->id;
                                                    $categoryTranslation = $category->translates->firstWhere('language_id', $mainLanguageId)
                                                        ?? $category->translates->first();
                                                @endphp
                                                @if($categoryTranslation)
                                                    {{ $categoryTranslation->title }}
                                                @else
                                                    {{ app()->getLocale() === 'az' ? 'Kateqoriya #' : 'Category #' }}{{ $category->id }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Language Tabs -->
                            <div class="row">
                                <div class="col-12">
                                    <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                                        @foreach($languages as $index => $language)
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                                        id="lang-{{ $language->id }}-tab" 
                                                        data-bs-toggle="tab" 
                                                        data-bs-target="#lang-{{ $language->id }}" 
                                                        type="button" 
                                                        role="tab">
                                                    {{ $language->name }} ({{ $language->lang_code }})
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="tab-content mt-3" id="languageTabsContent">
                                        @foreach($languages as $index => $language)
                                            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                                 id="lang-{{ $language->id }}" 
                                                 role="tabpanel">
                                                
                                                <input type="hidden" name="translates[{{ $index }}][language_id]" value="{{ $language->id }}">
                                                
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Title</label>
                                                        <input type="text" 
                                                               class="form-control @error('translates.'.$index.'.title') is-invalid @enderror" 
                                                               name="translates[{{ $index }}][title]" 
                                                               value="{{ old('translates.'.$index.'.title') }}">
                                                        @error('translates.'.$index.'.title')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Slug</label>
                                                        <input type="text" 
                                                               class="form-control @error('translates.'.$index.'.slug') is-invalid @enderror" 
                                                               name="translates[{{ $index }}][slug]" 
                                                               value="{{ old('translates.'.$index.'.slug') }}">
                                                        @error('translates.'.$index.'.slug')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Meta Title</label>
                                                        <input type="text" 
                                                               class="form-control @error('translates.'.$index.'.meta_title') is-invalid @enderror" 
                                                               name="translates[{{ $index }}][meta_title]" 
                                                               value="{{ old('translates.'.$index.'.meta_title') }}">
                                                        @error('translates.'.$index.'.meta_title')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Meta Keywords</label>
                                                        <input type="text" 
                                                               class="form-control @error('translates.'.$index.'.meta_keywords') is-invalid @enderror" 
                                                               name="translates[{{ $index }}][meta_keywords]" 
                                                               value="{{ old('translates.'.$index.'.meta_keywords') }}">
                                                        @error('translates.'.$index.'.meta_keywords')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Meta Description</label>
                                                    <textarea class="form-control @error('translates.'.$index.'.meta_description') is-invalid @enderror" 
                                                              name="translates[{{ $index }}][meta_description]" 
                                                              rows="3">{{ old('translates.'.$index.'.meta_description') }}</textarea>
                                                    @error('translates.'.$index.'.meta_description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Page Footer</label>
                                                    <textarea class="form-control ckeditor @error('translates.'.$index.'.page_footer') is-invalid @enderror" 
                                                              id="page_footer_{{ $language->id }}"
                                                              name="translates[{{ $index }}][page_footer]" 
                                                              rows="5">{{ old('translates.'.$index.'.page_footer') }}</textarea>
                                                    @error('translates.'.$index.'.page_footer')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary" title="Save SEO Page">
                                            <i class="material-icons-outlined">save</i>
                                        </button>
                                        <a href="{{ route('admin.seo-pages.index') }}" class="btn btn-secondary" title="Back to List">
                                            <i class="material-icons-outlined">arrow_back</i>
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

@push('styles')
<style>
.alert-danger ul {
    padding-left: 20px;
}
.alert-danger ul li {
    margin-bottom: 5px;
}
.alert-danger i {
    vertical-align: middle;
    margin-right: 8px;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize CKEditor for all page_footer textareas
    document.querySelectorAll('.ckeditor').forEach(function(textarea) {
        ClassicEditor
            .create(textarea, {
                toolbar: [
                    'heading', '|',
                    'bold', 'italic', 'link', '|',
                    'bulletedList', 'numberedList', '|',
                    'outdent', 'indent', '|',
                    'blockQuote', 'insertTable', '|',
                    'undo', 'redo'
                ]
            })
            .catch(error => {
                console.error('CKEditor initialization error:', error);
            });
    });
});
</script>
@endpush
@endsection

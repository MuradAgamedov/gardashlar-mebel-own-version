@extends('admin.layout.app')

@section('title', 'Add Accordion')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Add Accordion</h3>
                    <p class="text-muted">Product: {{ $product->translate()->title ?? 'No title' }}</p>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.products.index') }}">Products</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.products.accordions.index', $product) }}">Accordions</a>
                        </li>
                        <li class="breadcrumb-item active">Add</li>
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
                        <h5>Add New Accordion</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.products.accordions.store', $product) }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" checked>
                                        <label class="form-check-label" for="status">
                                            Active
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Language Tabs -->
                            <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                                @foreach($languages as $index => $language)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                                id="tab-{{ $language->id }}" 
                                                data-bs-toggle="tab" 
                                                data-bs-target="#content-{{ $language->id }}" 
                                                type="button" 
                                                role="tab">
                                            {{ $language->title }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="tab-content mt-3" id="languageTabContent">
                                @foreach($languages as $index => $language)
                                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                         id="content-{{ $language->id }}" 
                                         role="tabpanel">
                                        
                                        <input type="hidden" name="translations[{{ $index }}][language_id]" value="{{ $language->id }}">
                                        
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="title_{{ $language->id }}" class="form-label">
                                                        Title ({{ $language->title }})
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" 
                                                           class="form-control @error('translations.' . $index . '.title') is-invalid @enderror" 
                                                           id="title_{{ $language->id }}" 
                                                           name="translations[{{ $index }}][title]" 
                                                           value="{{ old('translations.' . $index . '.title') }}" 
                                                           required>
                                                    @error('translations.' . $index . '.title')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-12 mt-3">
                                                <div class="form-group">
                                                    <label for="description_{{ $language->id }}" class="form-label">
                                                        Description ({{ $language->title }})
                                                    </label>
                                                    <textarea class="form-control ckeditor @error('translations.' . $index . '.description') is-invalid @enderror" 
                                                              id="description_{{ $language->id }}" 
                                                              name="translations[{{ $index }}][description]" 
                                                              rows="4">{{ old('translations.' . $index . '.description') }}</textarea>
                                                    @error('translations.' . $index . '.description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary" title="{{ __('Save Accordion') }}">
                                            <i class="material-icons-outlined">save</i>
                                        </button>
                                        <a href="{{ route('admin.products.accordions.index', $product) }}" class="btn btn-secondary">
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
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize CKEditor for all description fields
    const descriptionFields = document.querySelectorAll('.ckeditor');
    
    descriptionFields.forEach(function(field) {
        ClassicEditor
            .create(field, {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'undo', 'redo'],
                height: 200
            })
            .catch(error => {
                console.error(error);
            });
    });
});
</script>
@endpush 
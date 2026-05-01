@extends('admin.layout.app')

@section('title', 'Add Image')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Add Image</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.images.index') }}">Images</a>
                        </li>
                        <li class="breadcrumb-item active">Add Image</li>
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
                        <h5>Add New Image</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.images.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Keyword -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Keyword <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('keyword') is-invalid @enderror" 
                                           name="keyword" 
                                           value="{{ old('keyword') }}" 
                                           placeholder="Enter keyword"
                                           required>
                                    @error('keyword')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Image Upload -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Image <span class="text-danger">*</span></label>
                                    <input type="file" 
                                           class="form-control @error('image') is-invalid @enderror" 
                                           id="image" 
                                           name="image" 
                                           accept="image/*"
                                           required>
                                    @error('image')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <div id="image-preview-container" class="mt-2"></div>
                                </div>
                            </div>

                            <!-- Language Tabs -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Translations</h5>
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
                                                            <!-- Image Alt Text -->
                                                            <div class="col-md-12">
                                                                <label class="form-label">Image Alt Text <span class="text-danger">*</span></label>
                                                                <input type="text" 
                                                                       class="form-control @error("translations.{$language->id}.image_alt_text") is-invalid @enderror" 
                                                                       name="translations[{{ $language->id }}][image_alt_text]" 
                                                                       value="{{ old("translations.{$language->id}.image_alt_text") }}" 
                                                                       placeholder="Image alt text for {{ $language->lang_name }}"
                                                                       required>
                                                                @error("translations.{$language->id}.image_alt_text")
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
                                        <button type="submit" class="btn btn-primary" title="{{ __('Create Image') }}">
                                            <i class="material-icons-outlined">save</i>
                                        </button>
                                        <a href="{{ route('admin.images.index') }}" class="btn btn-secondary" title="{{ __('Cancel') }}">
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
    // Image preview
    const imageInput = document.getElementById('image');
    const previewContainer = document.getElementById('image-preview-container');
    
    if (imageInput) {
        imageInput.addEventListener('change', function(event) {
            previewContainer.innerHTML = '';
            const file = event.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewContainer.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded border" style="max-height:120px;object-fit:cover;" alt="Preview">`;
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>
@endpush 
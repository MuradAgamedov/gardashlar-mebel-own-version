
@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>New Company</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.companies.index') }}">Companies</a>
                        </li>
                        <li class="breadcrumb-item active">Add New</li>
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
                        <h5>Add New Company</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.companies.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="status" value="1" 
                                                   id="status" {{ old('status', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="status">
                                                Active
                                            </label>
                                        </div>
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
                                                role="tab"
                                                @if($errors->has("translates.{$language->id}.*"))
                                                    style="border-color: #dc3545; color: #dc3545;"
                                                @endif>
                                            {{ $language->lang_code }}
                                            @if($errors->has("translates.{$language->id}.*"))
                                                <i class="material-icons-outlined text-danger ms-1" style="font-size: 16px;">error</i>
                                            @endif
                                        </button>
                                    </li>
                                @endforeach
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content mt-3" id="languageTabContent">
                                @foreach($languages as $index => $language)
                                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                         id="content-{{ $language->id }}" 
                                         role="tabpanel" 
                                         aria-labelledby="tab-{{ $language->id }}">
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Title *</label>
                                                    <input type="text" class="form-control @error("translates.{$language->id}.title") is-invalid @enderror" 
                                                           name="translates[{{ $language->id }}][title]" 
                                                           value="{{ old("translates.{$language->id}.title") }}"
                                                           placeholder="Enter title"
                                                           required>
                                                    @error("translates.{$language->id}.title")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Card Image Alt Text</label>
                                                    <input type="text" class="form-control @error("translates.{$language->id}.card_image_alt_text") is-invalid @enderror" 
                                                           name="translates[{{ $language->id }}][card_image_alt_text]" 
                                                           value="{{ old("translates.{$language->id}.card_image_alt_text") }}"
                                                           placeholder="Enter card image alt text">
                                                    @error("translates.{$language->id}.card_image_alt_text")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Card Image *</label>
                                                    <input type="file" class="form-control @error("translates.{$language->id}.card_image") is-invalid @enderror" 
                                                           name="translates[{{ $language->id }}][card_image]" 
                                                           id="card-image-{{ $language->id }}"
                                                           accept="image/*" required>
                                                    @error("translates.{$language->id}.card_image")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-text">Maximum size: 2MB. Formats: JPEG, PNG, JPG, GIF, WEBP</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Card Image Preview</label>
                                                    <div id="card-image-preview-{{ $language->id }}" class="border rounded p-3 text-center" style="min-height: 150px; display: none;">
                                                        <img id="card-preview-img-{{ $language->id }}" class="img-fluid" style="max-height: 120px;">
                                                    </div>
                                                    <div id="card-no-image-{{ $language->id }}" class="border rounded p-3 text-center text-muted" style="min-height: 150px;">
                                                        <i class="material-icons-outlined" style="font-size: 2rem;">image</i>
                                                        <p class="mt-2">Card image will appear here when selected</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Inner Image Alt Text</label>
                                                    <input type="text" class="form-control @error("translates.{$language->id}.inner_image_alt_text") is-invalid @enderror" 
                                                           name="translates[{{ $language->id }}][inner_image_alt_text]" 
                                                           value="{{ old("translates.{$language->id}.inner_image_alt_text") }}"
                                                           placeholder="Enter inner image alt text">
                                                    @error("translates.{$language->id}.inner_image_alt_text")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Inner Image *</label>
                                                    <input type="file" class="form-control @error("translates.{$language->id}.inner_image") is-invalid @enderror" 
                                                           name="translates[{{ $language->id }}][inner_image]" 
                                                           id="inner-image-{{ $language->id }}"
                                                           accept="image/*" required>
                                                    @error("translates.{$language->id}.inner_image")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-text">Maximum size: 2MB. Formats: JPEG, PNG, JPG, GIF, WEBP</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Inner Image Preview</label>
                                                    <div id="inner-image-preview-{{ $language->id }}" class="border rounded p-3 text-center" style="min-height: 150px; display: none;">
                                                        <img id="inner-preview-img-{{ $language->id }}" class="img-fluid" style="max-height: 120px;">
                                                    </div>
                                                    <div id="inner-no-image-{{ $language->id }}" class="border rounded p-3 text-center text-muted" style="min-height: 150px;">
                                                        <i class="material-icons-outlined" style="font-size: 2rem;">image</i>
                                                        <p class="mt-2">Inner image will appear here when selected</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <!-- Empty column for layout -->
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Description *</label>
                                            <textarea class="form-control ckeditor @error("translates.{$language->id}.description") is-invalid @enderror" 
                                                      name="translates[{{ $language->id }}][description]" 
                                                      id="editor-{{ $language->id }}"
                                                      rows="8"
                                                      placeholder="Enter description"
                                                      required>{{ old("translates.{$language->id}.description") }}</textarea>
                                            @error("translates.{$language->id}.description")
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <input type="hidden" name="translates[{{ $language->id }}][language_id]" value="{{ $language->id }}">
                                    </div>
                                @endforeach
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">save</i>
                                            Save
                                        </button>
                                        <a href="{{ route('admin.companies.index') }}" class="btn btn-secondary">
                                            <i class="material-icons-outlined">cancel</i>
                                            Cancel
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
<script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview for each language
    @foreach($languages as $language)
        // Card image preview
        const cardImageInput{{ $language->id }} = document.getElementById('card-image-{{ $language->id }}');
        const cardImagePreview{{ $language->id }} = document.getElementById('card-image-preview-{{ $language->id }}');
        const cardPreviewImg{{ $language->id }} = document.getElementById('card-preview-img-{{ $language->id }}');
        const cardNoImage{{ $language->id }} = document.getElementById('card-no-image-{{ $language->id }}');

        cardImageInput{{ $language->id }}.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    cardPreviewImg{{ $language->id }}.src = e.target.result;
                    cardImagePreview{{ $language->id }}.style.display = 'block';
                    cardNoImage{{ $language->id }}.style.display = 'none';
                }
                reader.readAsDataURL(file);
            } else {
                cardImagePreview{{ $language->id }}.style.display = 'none';
                cardNoImage{{ $language->id }}.style.display = 'block';
            }
        });

        // Inner image preview
        const innerImageInput{{ $language->id }} = document.getElementById('inner-image-{{ $language->id }}');
        const innerImagePreview{{ $language->id }} = document.getElementById('inner-image-preview-{{ $language->id }}');
        const innerPreviewImg{{ $language->id }} = document.getElementById('inner-preview-img-{{ $language->id }}');
        const innerNoImage{{ $language->id }} = document.getElementById('inner-no-image-{{ $language->id }}');

        innerImageInput{{ $language->id }}.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    innerPreviewImg{{ $language->id }}.src = e.target.result;
                    innerImagePreview{{ $language->id }}.style.display = 'block';
                    innerNoImage{{ $language->id }}.style.display = 'none';
                }
                reader.readAsDataURL(file);
            } else {
                innerImagePreview{{ $language->id }}.style.display = 'none';
                innerNoImage{{ $language->id }}.style.display = 'block';
            }
        });
    @endforeach

    // Initialize CKEditor for each textarea
    document.querySelectorAll('.ckeditor').forEach(function(textarea) {
        ClassicEditor
            .create(textarea, {
                toolbar: {
                    items: [
                        'undo', 'redo',
                        '|', 'heading',
                        '|', 'bold', 'italic', 'underline', 'strikethrough',
                        '|', 'fontColor', 'fontBackgroundColor',
                        '|', 'link', 'blockQuote', 'insertTable',
                        '|', 'bulletedList', 'numberedList',
                        '|', 'outdent', 'indent',
                        '|', 'alignment',
                        '|', 'removeFormat'
                    ]
                },
                language: 'en',
                placeholder: 'Enter description...'
            })
            .then(editor => {
                // Update textarea value before form submission
                const form = textarea.closest('form');
                form.addEventListener('submit', function() {
                    editor.updateSourceElement();
                });

                // Handle validation errors by focusing on the editor
                textarea.addEventListener('invalid', function() {
                    editor.editing.view.focus();
                });

                // Override the focus method to focus on the editor instead of textarea
                textarea.focus = function() {
                    editor.editing.view.focus();
                };
            })
            .catch(error => {
                console.error(error);
            });
    });
});
</script>

<style>
.invalid-feedback {
    display: block !important;
}

.nav-tabs .nav-link {
    border: 1px solid transparent;
    border-radius: 0.375rem 0.375rem 0 0;
    margin-right: 0.25rem;
}

.nav-tabs .nav-link.active {
    border-color: #dee2e6 #dee2e6 #fff;
    background-color: #fff;
}

.nav-tabs .nav-link:hover {
    border-color: #e9ecef #e9ecef #dee2e6;
}

/* CKEditor Styles */
.ck-editor__editable {
    min-height: 200px;
    max-height: 400px;
    overflow-y: auto;
}

.ck.ck-editor {
    margin-bottom: 1rem;
}

.ck.ck-editor__main > .ck-editor__editable {
    background-color: #fff;
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
}

.ck.ck-editor__main > .ck-editor__editable:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.ck.ck-toolbar {
    border: 1px solid #ced4da;
    border-bottom: none;
    border-radius: 0.375rem 0.375rem 0 0;
}
</style>
@endpush 
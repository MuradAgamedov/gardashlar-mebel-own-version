@extends('admin.layout.app')

@section('title', __('Company Management'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Company Management') }}</h3>
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
                        <li class="breadcrumb-item active">{{ __('Company Management') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="material-icons-outlined">check_circle</i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

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
                        <h5>{{ __('Edit Company Management') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.company-management.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <!-- Content Translations Section -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h6 class="mb-3">{{ __('Content Translations') }}</h6>
                                    
                                    <!-- Custom Image Upload Button -->
                                    <div class="mb-3">
                                        <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('customImageUpload').click();">
                                            <i class="material-icons-outlined">image</i> {{ __('Upload Image (Base64)') }}
                                        </button>
                                        <input type="file" id="customImageUpload" accept="image/*" style="display:none;" onchange="handleCustomImageUpload(event)">
                                        <small class="text-muted ms-2">{{ __('Select image and it will be added to content in Base64 format') }}</small>
                                    </div>
                                    
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
                                            $translate = $companyManagement->translates->where('language_id', $language->id)->first();
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
                                            $translate = $companyManagement->translates->where('language_id', $language->id)->first();
                                        @endphp
                                        <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                             id="content-{{ $language->lang_code }}" 
                                             role="tabpanel" 
                                             aria-labelledby="tab-{{ $language->lang_code }}">
                                            
                                            <div class="card mt-3">
                                                <div class="card-header">
                                                    <h6 class="mb-0">{{ $language->title }} {{ __('Content') }}</h6>
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
                                                            <label class="form-label">{{ __('Content') }} <span class="text-danger">*</span></label>
                                                            <textarea class="form-control ckeditor @error('translations.'.$index.'.content') is-invalid @enderror" 
                                                                      name="translations[{{ $index }}][content]" 
                                                                      id="content-{{ $language->id }}"
                                                                      placeholder="{{ __('Enter content for :lang', ['lang' => $language->lang_code]) }}">{{ old('translations.'.$index.'.content', $translate->content ?? '') }}</textarea>
                                                            @error('translations.'.$index.'.content')
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
                                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
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
    window.editors = {};
    @foreach($languages as $language)
ClassicEditor
  .create(document.querySelector('#content-{{ $language->id }}'), {
    toolbar: [
      'heading', '|',
      'bold', 'italic', 'underline', 'strikethrough', 'link', '|',
      'bulletedList', 'numberedList', '|',
      'imageUpload', 'insertTable', 'blockQuote', 'mediaEmbed', '|',
      'undo', 'redo'
    ],
    image: {
      toolbar: [
        'imageTextAlternative',
        'imageStyle:alignLeft',
        'imageStyle:full',
        'imageStyle:alignRight',
        'linkImage',
        'resizeImage',
        'toggleImageCaption',
        'imageStyle:inline',
        'imageStyle:block'
      ],
      styles: [ 'full', 'alignLeft', 'alignRight', 'inline', 'block' ]
    }
  })
  .then(editor => {
    window.editors['{{ $language->id }}'] = editor;
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

// Custom image upload function
function handleCustomImageUpload(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const base64 = e.target.result;
            
            // Ask for image width
            const imgWidth = prompt("{{ __('Enter image width (px):') }}", "400");
            const imgAlt = prompt("{{ __('Enter alt text:') }}", "{{ __('Uploaded Image') }}");
            
            if (imgWidth) {
                const imgTag = `<img src="${base64}" alt="${imgAlt}" style="width: ${imgWidth}px; height: auto;">`;
                
                // Aktiv textarea-nın ID-sindən CKEditor ID-sini tapırıq
                const activePane = document.querySelector('.tab-pane.active');
                const textarea = activePane.querySelector('textarea');
                const textareaId = textarea.getAttribute('id'); // content-1
                const langId = textareaId.split('-')[1]; // 1

                const editorInstance = window.editors[langId]; // düzgün editor-i tapırıq

                if (editorInstance) {
                    editorInstance.model.change(writer => {
                        const viewFragment = editorInstance.data.processor.toView(imgTag);
                        const modelFragment = editorInstance.data.toModel(viewFragment);
                        editorInstance.model.insertContent(modelFragment, editorInstance.model.document.selection);
                    });
                } else {
                    alert("Editor tapılmadı.");
                }
            }
        };
        reader.readAsDataURL(file);
    }
}


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
</style>
@endpush
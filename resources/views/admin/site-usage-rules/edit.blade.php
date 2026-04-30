@extends('admin.layout.app')

@section('title', __('Site Usage Rules'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Site Usage Rules') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Site Usage Rules') }}</li>
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
                        <h5>Edit Site Usage Rules Section</h5>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('admin.site-usage-rules.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Multi-language Tabs -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                                        @foreach($languages as $index => $language)
                                            @php
                                                $translation = $siteUsageRules->translates->where('language_id', $language->id)->first();
                                                $hasErrors = $errors->has("translations.{$language->id}.*");
                                                $hasTranslation = $translation !== null;
                                            @endphp
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link @if($index === 0) active @endif" 
                                                        id="tab-{{ $language->id }}" 
                                                        data-bs-toggle="tab" 
                                                        data-bs-target="#content-{{ $language->id }}" 
                                                        type="button" 
                                                        role="tab" 
                                                        aria-controls="content-{{ $language->id }}" 
                                                        aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                                                        style="@if($hasErrors) border-color: #dc3545; @elseif($hasTranslation) border-color: #198754; @else border-color: #ffc107; @endif">
                                                    {{ $language->lang_code }}
                                                    @if($hasErrors)
                                                        <i class="material-icons-outlined text-danger ms-1" style="font-size: 16px;">error</i>
                                                    @elseif($hasTranslation)
                                                        <i class="material-icons-outlined text-success ms-1" style="font-size: 16px;">check_circle</i>
                                                    @else
                                                        <i class="material-icons-outlined text-warning ms-1" style="font-size: 16px;">warning</i>
                                                    @endif
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <div class="tab-content" id="languageTabContent">
                                        @foreach($languages as $index => $language)
                                            @php
                                                $translation = $siteUsageRules->translates->where('language_id', $language->id)->first();
                                            @endphp
                                            <div class="tab-pane fade @if($index === 0) show active @endif" 
                                                 id="content-{{ $language->id }}" 
                                                 role="tabpanel" 
                                                 aria-labelledby="tab-{{ $language->id }}">
                                                
                                                <div class="row mt-3">
                                                    <div class="col-md-12">
                                                        <label for="content_{{ $language->id }}" class="form-label">
                                                            Content <span class="text-danger">*</span>
                                                        </label>
                                                        <textarea class="form-control ckeditor @error("translations.{$language->id}.content") is-invalid @enderror" 
                                                                  id="content_{{ $language->id }}" 
                                                                  name="translations[{{ $language->id }}][content]" 
                                                                  placeholder="Enter content for {{ $language->lang_code }}">{{ old("translations.{$language->id}.content", $translation->content ?? '') }}</textarea>
                                                        @error("translations.{$language->id}.content")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">save</i>
                                            Update Site Usage Rules
                                        </button>
                                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
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

@push('styles')
<style>
.nav-tabs .nav-link {
    border: 1px solid #dee2e6;
    border-bottom: none;
    border-radius: 0.375rem 0.375rem 0 0;
    color: #6c757d;
    background-color: #f8f9fa;
}

.nav-tabs .nav-link.active {
    color: #495057;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
}

.nav-tabs .nav-link:hover {
    border-color: #e9ecef #e9ecef #dee2e6;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/27.1.0/classic/ckeditor.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Store editors globally
    window.editors = {};
    
    // Initialize CKEditor for each language tab
    @foreach($languages as $language)
        ClassicEditor
            .create(document.querySelector('#content_{{ $language->id }}'), {
                toolbar: [
                    'heading', '|', 
                    'bold', 'italic', 'link', 
                    'bulletedList', 'numberedList', 
                    '|', 'outdent', 'indent', 
                    '|', 'blockQuote', 'insertTable', 
                    '|', 'imageUpload', 'undo', 'redo'
                ],
                image: {
                    styles: [
                        'alignLeft', 'alignCenter', 'alignRight'
                    ],
                    toolbar: [
                        'imageStyle:alignLeft',
                        'imageStyle:alignCenter',
                        'imageStyle:alignRight',
                        '|',
                        'imageTextAlternative'
                    ]
                },
                simpleUpload: {
                    uploadUrl: '{{ route("admin.upload.image") }}',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }
            })
            .then(editor => {
                // Store editor instance
                window.editors['content_{{ $language->id }}'] = editor;
            })
            .catch(error => {
                console.error(error);
            });
    @endforeach

    // Auto-switch to first tab with errors on form submission
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        // Sync all CKEditor content to textareas before submission
        Object.keys(window.editors).forEach(editorId => {
            const editor = window.editors[editorId];
            const textarea = document.getElementById(editorId);
            if (editor && textarea) {
                textarea.value = editor.getData();
            }
        });
        
        const tabsWithErrors = document.querySelectorAll('.nav-link[style*="border-color: #dc3545"]');
        if (tabsWithErrors.length > 0) {
            e.preventDefault();
            const firstTabWithError = tabsWithErrors[0];
            const tab = new bootstrap.Tab(firstTabWithError);
            tab.show();
        }
    });
});
</script>
@endpush 
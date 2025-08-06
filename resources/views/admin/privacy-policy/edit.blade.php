@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Gizlilik Siyasəti</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Gizlilik Siyasəti</li>
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

        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="alert-heading">Aşağıdakı xətaları düzəldin:</h6>
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
                        <h5>Gizlilik Siyasətini Redaktə Et</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.privacy-policy.update') }}" method="POST" id="privacyPolicyForm">
                            @csrf
                            @method('PUT')
                            
                            <!-- Language Tabs -->
                            <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                                @foreach($languages as $index => $language)
                                    @php
                                        $translate = $privacyPolicy->translates->where('language_id', $language->id)->first();
                                    @endphp
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
                                    @php
                                        $translate = $privacyPolicy->translates->where('language_id', $language->id)->first();
                                    @endphp
                                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                         id="content-{{ $language->id }}" 
                                         role="tabpanel" 
                                         aria-labelledby="tab-{{ $language->id }}">
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Məzmun *</label>
                                                                                         <textarea class="form-control ckeditor @error("translates.{$language->id}.content") is-invalid @enderror" 
                                                       name="translates[{{ $language->id }}][content]" 
                                                       id="editor-{{ $language->id }}"
                                                       rows="15"
                                                       placeholder="Gizlilik siyasəti məzmununu daxil edin"
                                                       required
                                                       style="position: absolute; left: -9999px; width: 1px; height: 1px; opacity: 0;">{{ old("translates.{$language->id}.content", $translate?->content) }}</textarea>
                                            <div id="ckeditor-container-{{ $language->id }}"></div>
                                            @error("translates.{$language->id}.content")
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
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
                                            Yadda Saxla
                                        </button>
                                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                                            <i class="material-icons-outlined">cancel</i>
                                            Ləğv Et
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
    // Form submit event listener
    const form = document.getElementById('privacyPolicyForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form submit event triggered');
            console.log('Form action:', form.action);
            console.log('Form method:', form.method);
            
                         // Update all CKEditor content to textareas before submit
             document.querySelectorAll('.ckeditor').forEach(function(textarea) {
                 if (textarea.editor) {
                     console.log('Updating CKEditor content for:', textarea.name);
                     const content = textarea.editor.getData();
                     textarea.value = content;
                     console.log('CKEditor content updated:', content.substring(0, 100) + '...');
                 }
             });
            
            // Check if form data is valid
            const formData = new FormData(form);
            console.log('Form data:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            
            // Don't prevent default - let it submit normally
        });
    }
    
    // Initialize CKEditor for each textarea
    document.querySelectorAll('.ckeditor').forEach(function(textarea) {
        const containerId = 'ckeditor-container-' + textarea.id.replace('editor-', '');
        const container = document.getElementById(containerId);
        
        ClassicEditor
            .create(container, {
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
                language: 'az',
                placeholder: 'Gizlilik siyasəti məzmununu daxil edin...'
            })
            .then(editor => {
                // Store editor reference
                textarea.editor = editor;
                
                // Set initial content
                editor.setData(textarea.value);
            })
            .catch(error => {
                console.error(error);
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

textarea.form-control {
    font-family: 'Courier New', monospace;
    font-size: 14px;
    line-height: 1.5;
}

/* CKEditor Styles */
.ck-editor__editable {
    min-height: 300px;
    max-height: 500px;
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
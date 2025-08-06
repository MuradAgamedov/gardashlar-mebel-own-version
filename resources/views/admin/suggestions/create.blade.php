@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Yeni Tövsiyyə</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.suggestions.index') }}">Tövsiyyələr</a>
                        </li>
                        <li class="breadcrumb-item active">Yarat</li>
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
                        <h5>Yeni Tövsiyyə Əlavə Et</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.suggestions.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Media Section -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h6 class="mb-3">Media</h6>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Şəkil *</label>
                                            <div class="image-upload-container">
                                                <input type="file" 
                                                       class="form-control @error('image') is-invalid @enderror image-file-input" 
                                                       name="image" 
                                                       accept="image/*"
                                                       data-preview="image-preview"
                                                       required>
                                                @error('image')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text">Maksimum ölçü: 2MB. Formatlar: JPEG, PNG, JPG, GIF</div>
                                                <div class="image-preview mt-2" id="image-preview"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Fayl</label>
                                            <input type="file" 
                                                   class="form-control @error('file') is-invalid @enderror" 
                                                   name="file" 
                                                   accept=".doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar">
                                            @error('file')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Maksimum ölçü: 10MB. Formatlar: DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, ZIP, RAR</div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">PDF</label>
                                            <input type="file" 
                                                   class="form-control @error('pdf') is-invalid @enderror" 
                                                   name="pdf" 
                                                   accept=".pdf">
                                            @error('pdf')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Maksimum ölçü: 10MB. Format: PDF</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Content Translations Section -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h6 class="mb-3">Məzmun Tərcümələri</h6>
                                    
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
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Başlıq *</label>
                                                        <input type="text" 
                                                               class="form-control @error("translates.{$language->id}.title") is-invalid @enderror" 
                                                               name="translates[{{ $language->id }}][title]" 
                                                               value="{{ old("translates.{$language->id}.title") }}"
                                                               placeholder="Başlıq daxil edin"
                                                               required>
                                                        @error("translates.{$language->id}.title")
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Şəkil Alt Mətni</label>
                                                        <input type="text" 
                                                               class="form-control @error("translates.{$language->id}.image_alt_text") is-invalid @enderror" 
                                                               name="translates[{{ $language->id }}][image_alt_text]" 
                                                               value="{{ old("translates.{$language->id}.image_alt_text") }}"
                                                               placeholder="Şəkil alt mətni daxil edin">
                                                        @error("translates.{$language->id}.image_alt_text")
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                
                                                <input type="hidden" name="translates[{{ $language->id }}][language_id]" value="{{ $language->id }}">
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
                                        <input class="form-check-input" type="checkbox" name="status" id="status" value="1" {{ old('status', '1') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">
                                            Aktiv Status
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
                                        <a href="{{ route('admin.suggestions.index') }}" class="btn btn-secondary">
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview functionality
    document.querySelectorAll('.image-file-input').forEach(function(input) {
        input.addEventListener('change', function() {
            const file = this.files[0];
            const previewId = this.getAttribute('data-preview');
            const previewContainer = document.getElementById(previewId);
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewContainer.innerHTML = `<img src="${e.target.result}" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">`;
                };
                reader.readAsDataURL(file);
            } else {
                previewContainer.innerHTML = '';
            }
        });
    });

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

.image-upload-container {
    position: relative;
}

.image-preview {
    text-align: center;
    min-height: 50px;
    border: 1px dashed #dee2e6;
    border-radius: 0.375rem;
    padding: 0.5rem;
    background-color: #f8f9fa;
}

.image-preview img {
    border-radius: 0.25rem;
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
</style>
@endpush 
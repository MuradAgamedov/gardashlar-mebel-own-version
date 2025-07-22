@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>New About Information</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.abouts.index') }}">About Us</a>
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
                        <h5>Add New About Information</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.abouts.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Image *</label>
                                        <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                               name="image" id="image" accept="image/*" required>
                                        @error('image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Maximum size: 2MB. Formats: JPEG, PNG, JPG, GIF, WEBP</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Date *</label>
                                        <input type="text" class="form-control @error('date') is-invalid @enderror" 
                                               name="date" value="{{ old('date') }}" 
                                               placeholder="Example: 2024" required>
                                        @error('date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
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
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Image Preview</label>
                                        <div id="image-preview" class="border rounded p-3 text-center" style="min-height: 200px; display: none;">
                                            <img id="preview-img" class="img-fluid" style="max-height: 180px;">
                                        </div>
                                        <div id="no-image" class="border rounded p-3 text-center text-muted" style="min-height: 200px;">
                                            <i class="material-icons-outlined" style="font-size: 3rem;">image</i>
                                            <p class="mt-2">Image will appear here when selected</p>
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
                                                    <label class="form-label">Image Alt Text</label>
                                                    <input type="text" class="form-control @error("translates.{$language->id}.image_alt_text") is-invalid @enderror" 
                                                           name="translates[{{ $language->id }}][image_alt_text]" 
                                                           value="{{ old("translates.{$language->id}.image_alt_text") }}"
                                                           placeholder="Enter image alt text">
                                                    @error("translates.{$language->id}.image_alt_text")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Description *</label>
                                            <textarea class="form-control @error("translates.{$language->id}.description") is-invalid @enderror" 
                                                      name="translates[{{ $language->id }}][description]" 
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
                                        <a href="{{ route('admin.abouts.index') }}" class="btn btn-secondary">
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    const noImage = document.getElementById('no-image');

    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
                noImage.style.display = 'none';
            }
            reader.readAsDataURL(file);
        } else {
            imagePreview.style.display = 'none';
            noImage.style.display = 'block';
        }
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


</style>
@endpush 
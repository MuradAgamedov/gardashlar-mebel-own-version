@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Create Administration Member</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.administrations.index') }}">Administrations</a>
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
                <ul class="mb-0">
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
                        <h5>Add New Administration Member</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.administrations.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Image *</label>
                                        <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                               id="image" name="image" accept="image/*" required>
                                        @error('image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Upload a profile image (max 2MB)</div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="status" name="status" value="1" checked>
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
                                            <img id="preview-img" src="" alt="Preview" class="img-fluid" style="max-height: 180px;">
                                        </div>
                                        <div id="no-image" class="border rounded p-3 text-center text-muted" style="min-height: 200px;">
                                            <i class="material-icons-outlined" style="font-size: 3rem;">image</i>
                                            <p class="mt-2">No image selected</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <h6 class="mb-3">Translations</h6>
                            
                            @foreach($languages as $language)
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <span class="badge bg-primary me-2">{{ strtoupper($language->lang_code) }}</span>
                                            {{ $language->name }}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="title_{{ $language->id }}" class="form-label">Title *</label>
                                                    <input type="text" class="form-control @error('translates.'.$language->id.'.title') is-invalid @enderror" 
                                                           id="title_{{ $language->id }}" 
                                                           name="translates[{{ $language->id }}][title]" 
                                                           value="{{ old('translates.'.$language->id.'.title') }}" 
                                                           required>
                                                    @error('translates.'.$language->id.'.title')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="position_{{ $language->id }}" class="form-label">Position *</label>
                                                    <input type="text" class="form-control @error('translates.'.$language->id.'.position') is-invalid @enderror" 
                                                           id="position_{{ $language->id }}" 
                                                           name="translates[{{ $language->id }}][position]" 
                                                           value="{{ old('translates.'.$language->id.'.position') }}" 
                                                           required>
                                                    @error('translates.'.$language->id.'.position')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="translates[{{ $language->id }}][language_id]" value="{{ $language->id }}">
                                    </div>
                                </div>
                            @endforeach

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.administrations.index') }}" class="btn btn-secondary">
                                    <i class="material-icons-outlined">arrow_back</i>
                                    Back
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="material-icons-outlined">save</i>
                                    Create Administration Member
                                </button>
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
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.style.display = 'none';
            noImage.style.display = 'block';
        }
    });
});
</script>
@endpush 
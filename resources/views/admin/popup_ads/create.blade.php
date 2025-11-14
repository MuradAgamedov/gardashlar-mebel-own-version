@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">

        <!-- Page Title -->
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Create Popup Ad</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.popup-ads.index') }}">Popup Ads</a>
                        </li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>

    </div>

    <div class="container-fluid">

        <!-- Validation Errors -->
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="material-icons-outlined">error</i>
                <ul class="mb-0">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif


        <div class="row">
            <div class="col-sm-12">
                <div class="card">

                    <div class="card-header">
                        <h5>Add New Popup Ad</h5>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('admin.popup-ads.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <!-- LEFT -->
                                <div class="col-md-6">

                                    <!-- Image Upload -->
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Image *</label>
                                        <input type="file"
                                            name="image"
                                            id="image"
                                            accept="image/*"
                                            class="form-control @error('image') is-invalid @enderror"
                                            required>

                                        @error('image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                        <div class="form-text">Upload popup image (max 2MB)</div>
                                    </div>

                                    <!-- Status -->
                                    <div class="mb-3">
                                        <div class="form-check form-switch">

                                            <input type="hidden" name="status" value="0">

                                            <input class="form-check-input"
                                                type="checkbox"
                                                id="status"
                                                name="status"
                                                value="1"
                                                {{ old('status', 1) ? 'checked' : '' }}>

                                            <label class="form-check-label" for="status">Active</label>
                                        </div>
                                    </div>

                                </div>

                                <!-- RIGHT: IMAGE PREVIEW -->
                                <div class="col-md-6">
                                    <label class="form-label">Image Preview</label>

                                    <div id="image-preview" class="border rounded p-3 text-center"
                                         style="min-height: 200px; display:none;">
                                        <img id="preview-img" src="" class="img-fluid" style="max-height: 180px;">
                                    </div>

                                    <div id="no-image" class="border rounded p-3 text-center text-muted"
                                         style="min-height:200px;">
                                        <i class="material-icons-outlined" style="font-size:3rem;">image</i>
                                        <p class="mt-2">No image selected</p>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- Language Tabs -->
                            <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                                @foreach($languages as $index => $lang)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}"
                                            id="tab-{{ $lang->id }}"
                                            data-bs-toggle="tab"
                                            data-bs-target="#content-{{ $lang->id }}"
                                            type="button"
                                            role="tab">

                                            {{ $lang->lang_code }}

                                            @if($errors->has("translations.{$lang->id}.*"))
                                                <i class="material-icons-outlined text-danger ms-1" style="font-size: 16px;">error</i>
                                            @endif
                                        </button>
                                    </li>
                                @endforeach
                            </ul>

                            <!-- Language Content -->
                            <div class="tab-content mt-3">
                                @foreach($languages as $index => $lang)
                                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                                         id="content-{{ $lang->id }}" role="tabpanel">

                                        <!-- Title -->
                                        <div class="mb-3">
                                            <label class="form-label">Title *</label>
                                            <input type="text"
                                                   name="translations[{{ $lang->id }}][title]"
                                                   value="{{ old("translations.{$lang->id}.title") }}"
                                                   class="form-control @error("translations.{$lang->id}.title") is-invalid @enderror"
                                                   required>

                                            @error("translations.{$lang->id}.title")
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Description -->
                                        <div class="mb-3">
                                            <label class="form-label">Description *</label>
                                            <textarea name="translations[{{ $lang->id }}][description]"
                                                      class="form-control @error("translations.{$lang->id}.description") is-invalid @enderror"
                                                      rows="4"
                                                      required>{{ old("translations.{$lang->id}.description") }}</textarea>

                                            @error("translations.{$lang->id}.description")
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Image Alt Text -->
                                        <div class="mb-3">
                                            <label class="form-label">Image Alt Text</label>
                                            <input type="text"
                                                   name="translations[{{ $lang->id }}][image_alt_text]"
                                                   class="form-control"
                                                   value="{{ old("translations.{$lang->id}.image_alt_text") }}">
                                        </div>

                                        <!-- REQUIRED HIDDEN FIELDS -->
                                        <input type="hidden" name="translations[{{ $lang->id }}][locale]" value="{{ $lang->lang_code }}">
                                        <input type="hidden" name="translations[{{ $lang->id }}][language_id]" value="{{ $lang->id }}">
                                    </div>
                                @endforeach
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('admin.popup-ads.index') }}" class="btn btn-secondary">
                                    <i class="material-icons-outlined">arrow_back</i>
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="material-icons-outlined">save</i>
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
document.addEventListener('DOMContentLoaded', () => {

    const imageInput = document.getElementById('image');
    const preview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    const noImage = document.getElementById('no-image');

    imageInput.addEventListener('change', () => {
        const file = imageInput.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = e => {
                previewImg.src = e.target.result;
                preview.style.display = 'block';
                noImage.style.display = 'none';
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
            noImage.style.display = 'block';
        }
    });

});
</script>
@endpush

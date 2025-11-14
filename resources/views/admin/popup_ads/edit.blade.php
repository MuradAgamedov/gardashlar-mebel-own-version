@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">

        <!-- Page Title -->
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Edit Popup Ad</h3>
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
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>

    </div>

    <div class="container-fluid">

        <!-- ERROR MESSAGES -->
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
                        <h5>Edit Popup Ad</h5>
                    </div>

                    <div class="card-body">

                        <form action="{{ route('admin.popup-ads.update', $popupAd->id) }}"
                              method="POST"
                              enctype="multipart/form-data">

                            @csrf
                            @method('PUT')

                            <div class="row">
                                <!-- LEFT COLUMN -->
                                <div class="col-md-6">

                                    <!-- Image Upload -->
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Image</label>

                                        <input type="file"
                                               id="image"
                                               name="image"
                                               accept="image/*"
                                               class="form-control @error('image') is-invalid @enderror">

                                        @error('image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                        <div class="form-text">Upload new image (optional)</div>
                                    </div>

                                    <!-- Status -->
                                    <div class="mb-3">
                                        <div class="form-check form-switch">

                                            <input type="hidden" name="status" value="0">

                                            <input type="checkbox"
                                                class="form-check-input"
                                                id="status"
                                                name="status"
                                                value="1"
                                                {{ old('status', $popupAd->status) ? 'checked' : '' }}>

                                            <label class="form-check-label" for="status">Active</label>
                                        </div>
                                    </div>

                                </div>

                                <!-- RIGHT COLUMN PREVIEW -->
                                <div class="col-md-6">
                                    <label class="form-label">Current Image</label>

                                    <div class="border rounded p-3 text-center mb-3" style="min-height:200px;">
                                        @if($popupAd->image)
                                            <img src="{{ asset('storage/' . $popupAd->image) }}"
                                                 id="preview-img"
                                                 class="img-fluid"
                                                 style="max-height:180px;">
                                        @else
                                            <div class="text-muted">
                                                <i class="material-icons-outlined" style="font-size:3rem;">image</i>
                                                <p>No image uploaded</p>
                                            </div>
                                        @endif
                                    </div>

                                    <div id="image-preview" class="border rounded p-3 text-center" style="min-height:200px; display:none;">
                                        <img id="new-preview-img" class="img-fluid" style="max-height:180px;">
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- Language Tabs -->
                            <ul class="nav nav-tabs" role="tablist">
                                @foreach($languages as $index => $lang)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $index == 0 ? 'active' : '' }}"
                                                data-bs-toggle="tab"
                                                data-bs-target="#tab-{{ $lang->lang_code }}"
                                                type="button">
                                            {{ $lang->lang_code }}
                                            @if($errors->has("translations.{$lang->lang_code}.*"))
                                                <i class="material-icons-outlined text-danger ms-1" style="font-size:16px;">error</i>
                                            @endif
                                        </button>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="tab-content mt-3">

                                @foreach($languages as $index => $lang)
                                    @php
                                        $translate = $popupAd->translations->where('locale', $lang->lang_code)->first();
                                    @endphp

                                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                                         id="tab-{{ $lang->lang_code }}"
                                         role="tabpanel">

                                        <!-- Title -->
                                        <div class="mb-3">
                                            <label class="form-label">Title *</label>
                                            <input type="text"
                                                   name="translations[{{ $lang->lang_code }}][title]"
                                                   class="form-control @error("translations.{$lang->lang_code}.title") is-invalid @enderror"
                                                   value="{{ old("translations.{$lang->lang_code}.title", $translate?->title) }}"
                                                   required>

                                            @error("translations.{$lang->lang_code}.title")
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Description -->
                                        <div class="mb-3">
                                            <label class="form-label">Description *</label>
                                            <textarea name="translations[{{ $lang->lang_code }}][description]"
                                                      class="form-control @error("translations.{$lang->lang_code}.description") is-invalid @enderror"
                                                      rows="4"
                                                      required>{{ old("translations.{$lang->lang_code}.description", $translate?->description) }}</textarea>

                                            @error("translations.{$lang->lang_code}.description")
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Image Alt Text -->
                                        <div class="mb-3">
                                            <label class="form-label">Image Alt Text</label>
                                            <input type="text"
                                                   name="translations[{{ $lang->lang_code }}][image_alt_text]"
                                                   class="form-control"
                                                   value="{{ old("translations.{$lang->lang_code}.image_alt_text", $translate?->image_alt_text) }}">
                                        </div>

                                        <input type="hidden"
                                               name="translations[{{ $lang->lang_code }}][locale]"
                                               value="{{ $lang->lang_code }}">
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
    const newPreview = document.getElementById('image-preview');
    const newPreviewImg = document.getElementById('new-preview-img');

    imageInput.addEventListener('change', () => {
        const file = imageInput.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => {
                newPreviewImg.src = e.target.result;
                newPreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            newPreview.style.display = 'none';
        }
    });

});
</script>
@endpush

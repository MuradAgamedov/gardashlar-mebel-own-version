@extends('admin.layout.app')

@section('title', 'Edit Blog Post')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Edit Blog Post</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.blog-posts.index') }}">Blog Posts</a>
                        </li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Edit Blog Post</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.blog-posts.update', $blogPost) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <!-- Basic Information -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="blog_category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-select @error('blog_category_id') is-invalid @enderror" id="blog_category_id" name="blog_category_id" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            @php
                                                $translation = $category->translates->first();
                                            @endphp
                                            <option value="{{ $category->id }}" {{ old('blog_category_id', $blogPost->blog_category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $translation ? $translation->title : 'Category #' . $category->id }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('blog_category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="published_at" class="form-label">Published At</label>
                                    <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror" 
                                           id="published_at" name="published_at" 
                                           value="{{ old('published_at', $blogPost->published_at ? $blogPost->published_at->format('Y-m-d\TH:i') : '') }}">
                                    @error('published_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Images -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="card_image" class="form-label">Card Image</label>
                                    @if($blogPost->card_image)
                                        <div class="mb-2">
                                            <img src="{{ asset('uploads/' . $blogPost->card_image) }}" 
                                                 alt="Current Card Image" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 200px;">
                                        </div>
                                    @endif
                                    <input type="file" class="form-control @error('card_image') is-invalid @enderror" 
                                           id="card_image" name="card_image" accept="image/*">
                                    <small class="form-text text-muted">Recommended size: 400x300px, Max: 2MB</small>
                                    @error('card_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="inner_banner_image" class="form-label">Inner Banner Image</label>
                                    @if($blogPost->inner_banner_image)
                                        <div class="mb-2">
                                            <img src="{{ asset('uploads/' . $blogPost->inner_banner_image) }}" 
                                                 alt="Current Inner Banner Image" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 200px;">
                                        </div>
                                    @endif
                                    <input type="file" class="form-control @error('inner_banner_image') is-invalid @enderror" 
                                           id="inner_banner_image" name="inner_banner_image" accept="image/*">
                                    <small class="form-text text-muted">Recommended size: 1200x400px, Max: 2MB</small>
                                    @error('inner_banner_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" 
                                               {{ old('status', $blogPost->status) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">Active</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Translations -->
                            <div class="mb-4">
                                <h6 class="mb-3">Translations</h6>
                                <div class="nav nav-tabs" id="translationTabs" role="tablist">
                                    @foreach($languages as $index => $language)
                                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                                id="tab-{{ $language->id }}" 
                                                data-bs-toggle="tab" 
                                                data-bs-target="#content-{{ $language->id }}" 
                                                type="button" 
                                                role="tab">
                                            {{ $language->name }} ({{ strtoupper($language->lang_code) }})
                                        </button>
                                    @endforeach
                                </div>
                                
                                <div class="tab-content" id="translationTabContent">
                                    @foreach($languages as $index => $language)
                                        @php
                                            $translation = $blogPost->translates->where('language_id', $language->id)->first();
                                        @endphp
                                        <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                             id="content-{{ $language->id }}" 
                                             role="tabpanel">
                                            
                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <label for="translations[{{ $language->id }}][title]" class="form-label">
                                                        Title ({{ strtoupper($language->lang_code) }}) <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control @error("translations.{$language->id}.title") is-invalid @enderror" 
                                                           name="translations[{{ $language->id }}][title]" 
                                                           value="{{ old("translations.{$language->id}.title", $translation->title ?? '') }}" required>
                                                    @error("translations.{$language->id}.title")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <label for="translations[{{ $language->id }}][description]" class="form-label">
                                                        Description ({{ strtoupper($language->lang_code) }}) <span class="text-danger">*</span>
                                                    </label>
                                                    <textarea class="form-control @error("translations.{$language->id}.description") is-invalid @enderror" 
                                                              name="translations[{{ $language->id }}][description]" 
                                                              rows="6" required>{{ old("translations.{$language->id}.description", $translation->description ?? '') }}</textarea>
                                                    @error("translations.{$language->id}.description")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <label for="translations[{{ $language->id }}][card_image_alt_text]" class="form-label">
                                                        Card Image Alt Text ({{ strtoupper($language->lang_code) }})
                                                    </label>
                                                    <input type="text" class="form-control @error("translations.{$language->id}.card_image_alt_text") is-invalid @enderror" 
                                                           name="translations[{{ $language->id }}][card_image_alt_text]" 
                                                           value="{{ old("translations.{$language->id}.card_image_alt_text", $translation->card_image_alt_text ?? '') }}">
                                                    @error("translations.{$language->id}.card_image_alt_text")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <label for="translations[{{ $language->id }}][inner_banner_alt_text]" class="form-label">
                                                        Inner Banner Alt Text ({{ strtoupper($language->lang_code) }})
                                                    </label>
                                                    <input type="text" class="form-control @error("translations.{$language->id}.inner_banner_alt_text") is-invalid @enderror" 
                                                           name="translations[{{ $language->id }}][inner_banner_alt_text]" 
                                                           value="{{ old("translations.{$language->id}.inner_banner_alt_text", $translation->inner_banner_alt_text ?? '') }}">
                                                    @error("translations.{$language->id}.inner_banner_alt_text")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <label for="translations[{{ $language->id }}][card_description]" class="form-label">
                                                        Card Description ({{ strtoupper($language->lang_code) }})
                                                    </label>
                                                    <textarea class="form-control @error("translations.{$language->id}.card_description") is-invalid @enderror" 
                                                              name="translations[{{ $language->id }}][card_description]" 
                                                              rows="3">{{ old("translations.{$language->id}.card_description", $translation->card_description ?? '') }}</textarea>
                                                    @error("translations.{$language->id}.card_description")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- SEO Section -->
                                            <div class="row mt-4">
                                                <div class="col-12">
                                                    <h6 class="mb-3">SEO Settings ({{ strtoupper($language->lang_code) }})</h6>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label for="translations[{{ $language->id }}][seo_title]" class="form-label">
                                                        SEO Title ({{ strtoupper($language->lang_code) }})
                                                    </label>
                                                    <input type="text" class="form-control @error("translations.{$language->id}.seo_title") is-invalid @enderror" 
                                                           name="translations[{{ $language->id }}][seo_title]" 
                                                           value="{{ old("translations.{$language->id}.seo_title", $translation->seo_title ?? '') }}">
                                                    @error("translations.{$language->id}.seo_title")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <label for="translations[{{ $language->id }}][seo_keywords]" class="form-label">
                                                        SEO Keywords ({{ strtoupper($language->lang_code) }})
                                                    </label>
                                                    <textarea class="form-control @error("translations.{$language->id}.seo_keywords") is-invalid @enderror" 
                                                              name="translations[{{ $language->id }}][seo_keywords]" 
                                                              rows="3">{{ old("translations.{$language->id}.seo_keywords", $translation->seo_keywords ?? '') }}</textarea>
                                                    @error("translations.{$language->id}.seo_keywords")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <label for="translations[{{ $language->id }}][seo_description]" class="form-label">
                                                        SEO Description ({{ strtoupper($language->lang_code) }})
                                                    </label>
                                                    <textarea class="form-control @error("translations.{$language->id}.seo_description") is-invalid @enderror" 
                                                              name="translations[{{ $language->id }}][seo_description]" 
                                                              rows="3">{{ old("translations.{$language->id}.seo_description", $translation->seo_description ?? '') }}</textarea>
                                                    @error("translations.{$language->id}.seo_description")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">save</i>
                                            Update Blog Post
                                        </button>
                                        <a href="{{ route('admin.blog-posts.index') }}" class="btn btn-secondary">
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
        color: #6c757d;
    }
    .nav-tabs .nav-link.active {
        color: #495057;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
    }
    .img-thumbnail {
        border-radius: 4px;
    }
</style>
@endpush 
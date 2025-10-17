@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Create New SEO Page</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.seo-pages.index') }}">SEO Pages</a>
                        </li>
                        <li class="breadcrumb-item active">Create</li>
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
                        <h5>Add New SEO Page</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.seo-pages.store') }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" checked>
                                        <label class="form-check-label" for="status">
                                            Active
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Category</label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" name="category_id">
                                        <option value="">Select Category (Optional)</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                @if($category->translates->isNotEmpty())
                                                    {{ $category->translates->first()->title }}
                                                @else
                                                    Category #{{ $category->id }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Language Tabs -->
                            <div class="row">
                                <div class="col-12">
                                    <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                                        @foreach($languages as $index => $language)
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                                        id="lang-{{ $language->id }}-tab" 
                                                        data-bs-toggle="tab" 
                                                        data-bs-target="#lang-{{ $language->id }}" 
                                                        type="button" 
                                                        role="tab">
                                                    {{ $language->name }} ({{ $language->code }})
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="tab-content mt-3" id="languageTabsContent">
                                        @foreach($languages as $index => $language)
                                            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                                 id="lang-{{ $language->id }}" 
                                                 role="tabpanel">
                                                
                                                <input type="hidden" name="translates[{{ $index }}][language_id]" value="{{ $language->id }}">
                                                
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Title <span class="text-danger">*</span></label>
                                                        <input type="text" 
                                                               class="form-control @error('translates.'.$index.'.title') is-invalid @enderror" 
                                                               name="translates[{{ $index }}][title]" 
                                                               value="{{ old('translates.'.$index.'.title') }}"
                                                               required>
                                                        @error('translates.'.$index.'.title')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Slug <span class="text-danger">*</span></label>
                                                        <input type="text" 
                                                               class="form-control @error('translates.'.$index.'.slug') is-invalid @enderror" 
                                                               name="translates[{{ $index }}][slug]" 
                                                               value="{{ old('translates.'.$index.'.slug') }}"
                                                               required>
                                                        @error('translates.'.$index.'.slug')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Meta Title</label>
                                                        <input type="text" 
                                                               class="form-control @error('translates.'.$index.'.meta_title') is-invalid @enderror" 
                                                               name="translates[{{ $index }}][meta_title]" 
                                                               value="{{ old('translates.'.$index.'.meta_title') }}">
                                                        @error('translates.'.$index.'.meta_title')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Meta Keywords</label>
                                                        <input type="text" 
                                                               class="form-control @error('translates.'.$index.'.meta_keywords') is-invalid @enderror" 
                                                               name="translates[{{ $index }}][meta_keywords]" 
                                                               value="{{ old('translates.'.$index.'.meta_keywords') }}">
                                                        @error('translates.'.$index.'.meta_keywords')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Meta Description</label>
                                                    <textarea class="form-control @error('translates.'.$index.'.meta_description') is-invalid @enderror" 
                                                              name="translates[{{ $index }}][meta_description]" 
                                                              rows="3">{{ old('translates.'.$index.'.meta_description') }}</textarea>
                                                    @error('translates.'.$index.'.meta_description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Page Header</label>
                                                    <textarea class="form-control @error('translates.'.$index.'.page_header') is-invalid @enderror" 
                                                              name="translates[{{ $index }}][page_header]" 
                                                              rows="5">{{ old('translates.'.$index.'.page_header') }}</textarea>
                                                    @error('translates.'.$index.'.page_header')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Page Footer</label>
                                                    <textarea class="form-control @error('translates.'.$index.'.page_footer') is-invalid @enderror" 
                                                              name="translates[{{ $index }}][page_footer]" 
                                                              rows="5">{{ old('translates.'.$index.'.page_footer') }}</textarea>
                                                    @error('translates.'.$index.'.page_footer')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">save</i> Save SEO Page
                                        </button>
                                        <a href="{{ route('admin.seo-pages.index') }}" class="btn btn-secondary">
                                            <i class="material-icons-outlined">arrow_back</i> Back to List
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

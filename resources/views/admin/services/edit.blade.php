@extends('admin.layout.app')

@section('title', 'Edit Service')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Edit Service</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.services.index') }}">Services</a>
                        </li>
                        <li class="breadcrumb-item active">Edit</li>
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
                        <h5>Edit Service</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.services.update', $service->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" 
                                               {{ $service->status ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">
                                            Active
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6>Translations</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="nav nav-tabs" id="languageTabs" role="tablist">
                                                @foreach($languages as $index => $language)
                                                    <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                                            id="tab-{{ $language->id }}" 
                                                            data-bs-toggle="tab" 
                                                            data-bs-target="#content-{{ $language->id }}" 
                                                            type="button" 
                                                            role="tab">
                                                        {{ $language->title }}
                                                    </button>
                                                @endforeach
                                            </div>
                                            
                                            <div class="tab-content mt-3" id="languageTabContent">
                                                @foreach($languages as $index => $language)
                                                    @php
                                                        $translate = $service->translates->where('language_id', $language->id)->first();
                                                    @endphp
                                                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                                         id="content-{{ $language->id }}" 
                                                         role="tabpanel">
                                                        <div class="row">
                                                            <input type="hidden" name="translates[{{ $index }}][language_id]" value="{{ $language->id }}">
                                                            
                                                            <div class="col-md-12 mb-3">
                                                                <label class="form-label">Title *</label>
                                                                <input type="text" 
                                                                       class="form-control @error('translates.'.$index.'.title') is-invalid @enderror" 
                                                                       name="translates[{{ $index }}][title]" 
                                                                       value="{{ old('translates.'.$index.'.title', $translate->title ?? '') }}"
                                                                       placeholder="Enter title">
                                                                @error('translates.'.$index.'.title')
                                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            
                                                            <div class="col-md-12 mb-3">
                                                                <label class="form-label">Card Description *</label>
                                                                <textarea class="form-control @error('translates.'.$index.'.card_description') is-invalid @enderror" 
                                                                          name="translates[{{ $index }}][card_description]" 
                                                                          rows="3" 
                                                                          placeholder="Enter card description">{{ old('translates.'.$index.'.card_description', $translate->card_description ?? '') }}</textarea>
                                                                @error('translates.'.$index.'.card_description')
                                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            
                                                            <div class="col-md-12 mb-3">
                                                                <label class="form-label">Main Description *</label>
                                                                <textarea class="form-control @error('translates.'.$index.'.main_description') is-invalid @enderror" 
                                                                          name="translates[{{ $index }}][main_description]" 
                                                                          rows="5" 
                                                                          placeholder="Enter main description">{{ old('translates.'.$index.'.main_description', $translate->main_description ?? '') }}</textarea>
                                                                @error('translates.'.$index.'.main_description')
                                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            
                                                            <div class="col-md-12 mb-3">
                                                                <label class="form-label">SEO Title</label>
                                                                <input type="text" 
                                                                       class="form-control @error('translates.'.$index.'.seo_title') is-invalid @enderror" 
                                                                       name="translates[{{ $index }}][seo_title]" 
                                                                       value="{{ old('translates.'.$index.'.seo_title', $translate->seo_title ?? '') }}"
                                                                       placeholder="Enter SEO title">
                                                                @error('translates.'.$index.'.seo_title')
                                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            
                                                            <div class="col-md-12 mb-3">
                                                                <label class="form-label">SEO Keywords</label>
                                                                <textarea class="form-control @error('translates.'.$index.'.seo_keywords') is-invalid @enderror" 
                                                                          name="translates[{{ $index }}][seo_keywords]" 
                                                                          rows="3" 
                                                                          placeholder="Enter keywords separated by commas">{{ old('translates.'.$index.'.seo_keywords', $translate->seo_keywords ?? '') }}</textarea>
                                                                @error('translates.'.$index.'.seo_keywords')
                                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            
                                                            <div class="col-md-12 mb-3">
                                                                <label class="form-label">SEO Description</label>
                                                                <textarea class="form-control @error('translates.'.$index.'.seo_description') is-invalid @enderror" 
                                                                          name="translates[{{ $index }}][seo_description]" 
                                                                          rows="3" 
                                                                          placeholder="Enter SEO description">{{ old('translates.'.$index.'.seo_description', $translate->seo_description ?? '') }}</textarea>
                                                                @error('translates.'.$index.'.seo_description')
                                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary" title="Update Service">
                                            <i class="material-icons-outlined">save</i>
                                        </button>
                                        <a href="{{ route('admin.services.index') }}" class="btn btn-secondary" title="Cancel">
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
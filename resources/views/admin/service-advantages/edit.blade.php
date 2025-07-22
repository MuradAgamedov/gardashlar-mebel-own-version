@extends('admin.layout.app')

@section('title', 'Edit Service Advantage')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Edit Service Advantage</h3>
                    <p class="text-muted">{{ $service->translate()->title ?? 'Service' }}</p>
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
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.service-advantages.index', $service->id) }}">Advantages</a>
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
                        <h5>Edit Service Advantage</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.service-advantages.update', [$service->id, $advantage->id]) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Icon</label>
                                    <input type="file" 
                                           class="form-control @error('icon') is-invalid @enderror" 
                                           name="icon" 
                                           accept="image/*">
                                    @error('icon')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    @if($advantage->icon_url)
                                        <div class="mt-2">
                                            <img src="{{ $advantage->icon_url }}" alt="Current Icon" style="max-width: 100px; max-height: 100px;">
                                            <small class="text-muted d-block">Current icon</small>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" 
                                               {{ $advantage->status ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">
                                            Active
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Translations -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h6 class="mb-3">Translations</h6>
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
                                                $translate = $advantage->translates->where('language_id', $language->id)->first();
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
                                                        <label class="form-label">Icon Alt Text</label>
                                                        <input type="text" 
                                                               class="form-control @error('translates.'.$index.'.icon_alt_text') is-invalid @enderror" 
                                                               name="translates[{{ $index }}][icon_alt_text]" 
                                                               value="{{ old('translates.'.$index.'.icon_alt_text', $translate->icon_alt_text ?? '') }}"
                                                               placeholder="Enter icon alt text">
                                                        @error('translates.'.$index.'.icon_alt_text')
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label">Description *</label>
                                                        <textarea class="form-control @error('translates.'.$index.'.description') is-invalid @enderror" 
                                                                  name="translates[{{ $index }}][description]" 
                                                                  rows="3" 
                                                                  placeholder="Enter description">{{ old('translates.'.$index.'.description', $translate->description ?? '') }}</textarea>
                                                        @error('translates.'.$index.'.description')
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="material-icons-outlined">save</i>
                                        Update Advantage
                                    </button>
                                    <a href="{{ route('admin.service-advantages.index', $service->id) }}" class="btn btn-secondary">
                                        <i class="material-icons-outlined">cancel</i>
                                        Cancel
                                    </a>
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
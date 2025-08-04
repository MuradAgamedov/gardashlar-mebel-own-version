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
                                           id="iconInput"
                                           accept="image/*">
                                    @error('icon')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div id="iconPreview" class="mt-2" style="display: none;">
                                        <img id="previewImage" src="" alt="Icon Preview" style="max-width: 100px; max-height: 100px; border: 1px solid #ddd; border-radius: 4px;">
                                        <small class="text-muted d-block">New icon preview</small>
                                    </div>
                                    @if($advantage->icon_url)
                                        <div class="mt-2" id="currentIcon">
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
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary" title="Update Advantage">
                                            <i class="material-icons-outlined">save</i>
                                        </button>
                                        <a href="{{ route('admin.service-advantages.index', $service->id) }}" class="btn btn-secondary" title="Cancel">
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Icon preview functionality
    const iconInput = document.getElementById('iconInput');
    const iconPreview = document.getElementById('iconPreview');
    const previewImage = document.getElementById('previewImage');
    const currentIcon = document.getElementById('currentIcon');
    
    if (iconInput) {
        iconInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    iconPreview.style.display = 'block';
                    
                    // Hide current icon when new one is selected
                    if (currentIcon) {
                        currentIcon.style.display = 'none';
                    }
                };
                reader.readAsDataURL(file);
            } else {
                iconPreview.style.display = 'none';
                
                // Show current icon when no new file is selected
                if (currentIcon) {
                    currentIcon.style.display = 'block';
                }
            }
        });
    }
    
    // Show validation errors on page load if they exist
    const invalidInputs = document.querySelectorAll('.is-invalid');
    invalidInputs.forEach(function(input) {
        const feedback = input.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.style.display = 'block';
        }
    });
    
    // Form validation - show errors when form is submitted
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            let hasErrors = false;
            
            // Clear previous error states
            document.querySelectorAll('.is-invalid').forEach(function(input) {
                input.classList.remove('is-invalid');
            });
            document.querySelectorAll('.invalid-feedback').forEach(function(feedback) {
                feedback.style.display = 'none';
            });
            
            // Validate translations
            const languageTabs = document.querySelectorAll('.tab-pane');
            languageTabs.forEach(function(tab, index) {
                const titleInput = tab.querySelector('input[name*="[title]"]');
                const descriptionInput = tab.querySelector('textarea[name*="[description]"]');
                
                if (titleInput && !titleInput.value.trim()) {
                    titleInput.classList.add('is-invalid');
                    const feedback = titleInput.nextElementSibling;
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.textContent = 'Title is required.';
                        feedback.style.display = 'block';
                    }
                    hasErrors = true;
                }
                
                if (descriptionInput && !descriptionInput.value.trim()) {
                    descriptionInput.classList.add('is-invalid');
                    const feedback = descriptionInput.nextElementSibling;
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.textContent = 'Description is required.';
                        feedback.style.display = 'block';
                    }
                    hasErrors = true;
                }
            });
            
            if (hasErrors) {
                e.preventDefault();
                // Show the first tab with errors
                const firstInvalidTab = document.querySelector('.tab-pane .is-invalid').closest('.tab-pane');
                if (firstInvalidTab) {
                    const tabId = firstInvalidTab.id;
                    const tabButton = document.querySelector(`[data-bs-target="#${tabId}"]`);
                    if (tabButton) {
                        tabButton.click();
                    }
                }
            }
        });
    }
});
</script>
@endpush

@endsection 
@extends('admin.layouts.app')

@section('title', 'Edit Action')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Edit Action</h4>
                        <a href="{{ route('admin.actions.index') }}" class="btn btn-secondary">
                            <i class="material-icons-outlined">arrow_back</i>
                            Back to Actions
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.actions.update', $action) }}" method="POST" enctype="multipart/form-data" novalidate>
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Basic Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Company <span class="text-danger">*</span></label>
                                            <select name="company_id" class="form-select @error('company_id') is-invalid @enderror" required>
                                                <option value="">Select Company</option>
                                                @foreach($companies as $company)
                                                    <option value="{{ $company->id }}" 
                                                            {{ old('company_id', $action->company_id) == $company->id ? 'selected' : '' }}>
                                                        {{ $company->translate()->first()->title ?? 'No Title' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('company_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Icon</label>
                                            <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror" 
                                                   placeholder="e.g., local_offer, campaign, flash_on" 
                                                   value="{{ old('icon', $action->icon) }}">
                                            <div class="form-text">Material Icons name (optional)</div>
                                            @error('icon')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Start Date</label>
                                            <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" 
                                                   value="{{ old('start_date', $action->start_date?->format('Y-m-d')) }}">
                                            @error('start_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">End Date</label>
                                            <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" 
                                                   value="{{ old('end_date', $action->end_date?->format('Y-m-d')) }}">
                                            @error('end_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" name="status" class="form-check-input" value="1" 
                                                       {{ old('status', $action->status) ? 'checked' : '' }}>
                                                <label class="form-check-label">Active</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Translations -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Translations</h5>
                                    </div>
                                    <div class="card-body">
                                        @foreach($languages as $language)
                                            @php
                                                $translate = $action->translates->where('language_id', $language->id)->first();
                                            @endphp
                                            <div class="mb-4 p-3 border rounded">
                                                <h6 class="text-primary">{{ $language->name }}</h6>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Title <span class="text-danger">*</span></label>
                                                    <input type="text" name="translates[{{ $language->id }}][title]" 
                                                           class="form-control @error('translates.'.$language->id.'.title') is-invalid @enderror" 
                                                           value="{{ old('translates.'.$language->id.'.title', $translate->title ?? '') }}" required>
                                                    <input type="hidden" name="translates[{{ $language->id }}][language_id]" value="{{ $language->id }}">
                                                    @error('translates.'.$language->id.'.title')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Description</label>
                                                    <textarea name="translates[{{ $language->id }}][description]" 
                                                              class="form-control @error('translates.'.$language->id.'.description') is-invalid @enderror" 
                                                              rows="3">{{ old('translates.'.$language->id.'.description', $translate->description ?? '') }}</textarea>
                                                    @error('translates.'.$language->id.'.description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                @if($translate && $translate->image)
                                                    <div class="mb-3">
                                                        <label class="form-label">Current Image</label>
                                                        <div class="border rounded p-3 text-center">
                                                            <img src="{{ asset('storage/' . $translate->image) }}" 
                                                                 alt="Current Image" 
                                                                 class="img-fluid" 
                                                                 style="max-height: 120px;">
                                                            <p class="mt-2 text-muted">Current image</p>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="mb-3">
                                                    <label class="form-label">Image</label>
                                                    <input type="file" name="translates[{{ $language->id }}][image]" 
                                                           class="form-control @error('translates.'.$language->id.'.image') is-invalid @enderror" 
                                                           accept="image/*">
                                                    @error('translates.'.$language->id.'.image')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Image Alt Text</label>
                                                    <input type="text" name="translates[{{ $language->id }}][image_alt_text]" 
                                                           class="form-control @error('translates.'.$language->id.'.image_alt_text') is-invalid @enderror" 
                                                           value="{{ old('translates.'.$language->id.'.image_alt_text', $translate->image_alt_text ?? '') }}">
                                                    @error('translates.'.$language->id.'.image_alt_text')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.actions.index') }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="material-icons-outlined">save</i>
                                        Update Action
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

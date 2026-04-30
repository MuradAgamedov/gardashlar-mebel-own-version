@extends('admin.layout.app')

@section('title', __('Create Action'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">{{ __('Create New Action') }}</h4>
                        <a href="{{ route('admin.actions.index') }}" class="btn btn-secondary" title="{{ __('Back to Actions') }}">
                            <i class="material-icons-outlined">arrow_back</i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.actions.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                        @csrf
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>{{ __('Basic Information') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Company') }} <span class="text-danger">*</span></label>
                                            <select name="company_id" class="form-select @error('company_id') is-invalid @enderror" required>
                                                <option value="">{{ __('Select Company') }}</option>
                                                @foreach($companies as $company)
                                                    <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                                        {{ $company->translate()->first()->title ?? __('No Title') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('company_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Icon') }}</label>
                                            <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror" 
                                                   placeholder="{{ __('e.g., local_offer, campaign, flash_on') }}" 
                                                   value="{{ old('icon') }}">
                                            <div class="form-text">{{ __('Material Icons name (optional)') }}</div>
                                            @error('icon')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Start Date') }}</label>
                                            <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" 
                                                   value="{{ old('start_date') }}">
                                            @error('start_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">{{ __('End Date') }}</label>
                                            <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" 
                                                   value="{{ old('end_date') }}">
                                            @error('end_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" name="status" class="form-check-input" value="1" 
                                                       {{ old('status', true) ? 'checked' : '' }}>
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
                                        <h5>{{ __('Translations') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        @foreach($languages as $language)
                                            <div class="mb-4 p-3 border rounded">
                                                <h6 class="text-primary">{{ $language->name }}</h6>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('Title') }} <span class="text-danger">*</span></label>
                                                    <input type="text" name="translates[{{ $language->id }}][title]" 
                                                           class="form-control @error('translates.'.$language->id.'.title') is-invalid @enderror" 
                                                           value="{{ old('translates.'.$language->id.'.title') }}" required>
                                                    <input type="hidden" name="translates[{{ $language->id }}][language_id]" value="{{ $language->id }}">
                                                    @error('translates.'.$language->id.'.title')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Description</label>
                                                    <textarea name="translates[{{ $language->id }}][description]" 
                                                              class="form-control @error('translates.'.$language->id.'.description') is-invalid @enderror" 
                                                              rows="3">{{ old('translates.'.$language->id.'.description') }}</textarea>
                                                    @error('translates.'.$language->id.'.description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

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
                                                           value="{{ old('translates.'.$language->id.'.image_alt_text') }}">
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
                                    <a href="{{ route('admin.actions.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="material-icons-outlined">save</i>
                                        {{ __('Create Action') }}
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

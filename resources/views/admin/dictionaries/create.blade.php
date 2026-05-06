@extends('admin.layout.app')

@section('title', __('Add Dictionary Item'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Add Dictionary Item') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dictionaries.index') }}">{{ __('Dictionary') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Add') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">Error occurred:</h6>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">Error occurred:</h6>
                <p class="mb-0">{{ session('error') }}</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('New Dictionary Item') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.dictionaries.store') }}?page={{ request('page', 1) }}" method="POST">
                            @csrf
                            
                            <!-- Keyword -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        {{ __('Keyword') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('keyword') is-invalid @enderror" 
                                           name="keyword" 
                                           value="{{ old('keyword') }}" 
                                           placeholder="{{ __('Enter keyword (e.g., welcome_message)') }}" 
                                           required>
                                    <small class="form-text text-muted">{{ __('Use lowercase with underscores (e.g., welcome_message, about_title)') }}</small>
                                    @error('keyword')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Translations -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label">{{ __('Translations') }}</label>
                                    <div class="card border">
                                        <div class="card-body">
                                            @foreach($languages as $language)
                                                <div class="row mb-3">
                                                    <div class="col-md-2">
                                                        <label class="form-label">
                                                            <span class="badge bg-primary">{{ strtoupper($language->lang_code) }}</span>
                                                            {{ $language->title }}
                                                        </label>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <textarea class="form-control @error('translations.' . $language->id . '.value') is-invalid @enderror" 
                                                                  name="translations[{{ $language->id }}][value]" 
                                                                  rows="3" 
                                                                  placeholder="{{ __('Enter translation for :lang', ['lang' => $language->title]) }}">{{ old('translations.' . $language->id . '.value') }}</textarea>
                                                        <input type="hidden" name="translations[{{ $language->id }}][language_id]" value="{{ $language->id }}">
                                                        @error('translations.' . $language->id . '.value')
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
                                        <a href="{{ route('admin.dictionaries.index') }}" class="btn btn-secondary">
                                            {{ __('Back') }}
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            {{ __('Save') }}
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
</div>
@endsection 
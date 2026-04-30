@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Add New Language</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.languages.index') }}">{{ __('Languages') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Create') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header card-no-border pb-0">
                        <h3>{{ __('Add New Language') }}</h3>
                        <p class="mt-1 mb-0">{{ __('Fill in the form below to add a new language to your application.') }}</p>
                    </div>
                    <div class="card-body basic-form">
                        <div class="card-wrapper border rounded-3">
                            <form class="row" action="{{ route('admin.languages.store') }}" method="POST">
                                @csrf
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="title">{{ __('Title') }}</label>
                                    <input class="form-control @error('title') is-invalid @enderror" 
                                           id="title" 
                                           name="title" 
                                           type="text" 
                                           value="{{ old('title') }}" 
                                           placeholder="{{ __('Enter language title') }}">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="lang_code">{{ __('Language Code') }}</label>
                                    <input class="form-control @error('lang_code') is-invalid @enderror" 
                                           id="lang_code" 
                                           name="lang_code" 
                                           type="text" 
                                           value="{{ old('lang_code') }}" 
                                           placeholder="{{ __('Enter language code (e.g., en, az, ru)') }}">
                                    @error('lang_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="lang_name">{{ __('Language Name') }}</label>
                                    <input class="form-control @error('lang_name') is-invalid @enderror" 
                                           id="lang_name" 
                                           name="lang_name" 
                                           type="text" 
                                           value="{{ old('lang_name') }}" 
                                           placeholder="{{ __('Enter language name (e.g., English, Azerbaijani, Russian)') }}">
                                    @error('lang_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('Status') }}</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               id="status" 
                                               name="status" 
                                               type="checkbox" 
                                               value="1" 
                                               {{ old('status', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">{{ __('Active') }}</label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('Main Language') }}</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               id="is_main_language" 
                                               name="is_main_language" 
                                               type="checkbox" 
                                               value="1" 
                                               {{ old('is_main_language') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_main_language">{{ __('Set as main language') }}</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">save</i>
                                            {{ __('Save Language') }}
                                        </button>
                                        <a href="{{ route('admin.languages.index') }}" class="btn btn-secondary">
                                            <i class="material-icons-outlined">cancel</i>
                                            {{ __('Cancel') }}
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
</div>
@endsection 
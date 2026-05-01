@extends('admin.layout.app')

@section('title', __('Edit Phone'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Edit Phone') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.phones.index') }}">{{ __('Contact Management') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Edit Phone') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">{{ __('Please fix the following errors:') }}</h6>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('Edit Phone') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.phones.update', $phone) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <!-- Phone Type -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        {{ __('Phone Type') }} <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('phone_type_id') is-invalid @enderror" name="phone_type_id" required>
                                        <option value="">{{ __('Select Phone Type') }}</option>
                                        @foreach($phoneTypes as $phoneType)
                                            @php
                                                $firstTranslation = $phoneType->translates->first();
                                            @endphp
                                            <option value="{{ $phoneType->id }}" {{ old('phone_type_id', $phone->phone_type_id) == $phoneType->id ? 'selected' : '' }}>
                                                {{ $firstTranslation ? $firstTranslation->title : __('No translation') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('phone_type_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Phone Number -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        {{ __('Phone Number') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           name="phone" 
                                           value="{{ old('phone', $phone->phone) }}" 
                                           placeholder="{{ __('e.g., +994 50 123 45 67') }}"
                                           required>
                                    @error('phone')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Status') }}</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ $phone->status ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">{{ __('Active') }}</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">save</i>
                                        </button>
                                        <a href="{{ route('admin.phones.index') }}" class="btn btn-secondary">
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus on phone input
    const phoneInput = document.querySelector('input[name="phone"]');
    if (phoneInput) {
        phoneInput.focus();
    }
});
</script>
@endpush
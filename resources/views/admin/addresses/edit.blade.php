@extends('admin.layout.app')

@section('title', 'Edit Address')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Edit Address</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.addresses.index') }}">Address Management</a>
                        </li>
                        <li class="breadcrumb-item active">Edit Address</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">Please fix the following errors:</h6>
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
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Edit Address</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.addresses.update', $address) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <!-- Address Type -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Address Type <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('address_type_id') is-invalid @enderror" name="address_type_id" required>
                                        <option value="">Select Address Type</option>
                                        @foreach($addressTypes as $addressType)
                                            @php
                                                $firstTranslation = $addressType->translates->first();
                                            @endphp
                                            <option value="{{ $addressType->id }}" {{ old('address_type_id', $address->address_type_id) == $addressType->id ? 'selected' : '' }}>
                                                {{ $firstTranslation ? $firstTranslation->title : 'No translation' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('address_type_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ $address->status ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">Active</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Language Tabs -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label">Address Information</label>
                                    <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                                        @foreach($languages as $index => $language)
                                            @php
                                                $translation = $address->translates->where('language_id', $language->id)->first();
                                            @endphp
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                                        id="tab-{{ $language->id }}" 
                                                        data-bs-toggle="tab" 
                                                        data-bs-target="#content-{{ $language->id }}" 
                                                        type="button" 
                                                        role="tab">
                                                        {{ $language->lang_name }} ({{ $language->lang_code }})
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <div class="tab-content" id="languageTabContent">
                                        @foreach($languages as $index => $language)
                                            @php
                                                $translation = $address->translates->where('language_id', $language->id)->first();
                                            @endphp
                                            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                                 id="content-{{ $language->id }}" 
                                                 role="tabpanel">
                                                
                                                <div class="row mt-3">
                                                    <!-- Address -->
                                                    <div class="col-md-12">
                                                        <label class="form-label">
                                                            Address <span class="text-danger">*</span>
                                                        </label>
                                                        <textarea class="form-control @error("translations.{$language->id}.address") is-invalid @enderror" 
                                                                  name="translations[{{ $language->id }}][address]" 
                                                                  rows="3"
                                                                  placeholder="Enter full address">{{ old("translations.{$language->id}.address", $translation->address ?? '') }}</textarea>
                                                        @error("translations.{$language->id}.address")
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">save</i>
                                            Update Address
                                        </button>
                                        <a href="{{ route('admin.addresses.index') }}" class="btn btn-secondary">
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus on first textarea
    const firstTextarea = document.querySelector('textarea[name*="[address]"]');
    if (firstTextarea) {
        firstTextarea.focus();
    }
});
</script>
@endpush 
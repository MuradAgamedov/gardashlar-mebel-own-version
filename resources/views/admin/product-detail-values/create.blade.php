@extends('admin.layout.app')

@section('title', 'Add Detail Value - ' . ($product->translate()->title ?? 'Product'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Add Detail Value</h3>
                    <p class="text-muted">{{ $product->translate()->title ?? 'Product' }}</p>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.products.index') }}">Products</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.products.detail-values.index', $product) }}">Detail Values</a>
                        </li>
                        <li class="breadcrumb-item active">Add Detail Value</li>
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
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Add New Detail Value for: <strong>{{ $product->translate()->title ?? 'Product' }}</strong></h5>
                            <a href="{{ route('admin.products.detail-values.index', $product) }}" class="btn btn-outline-secondary">
                                <i class="material-icons-outlined">arrow_back</i> Back to Detail Values
                            </a>
                        </div>
                    </div>
                    
                    <form action="{{ route('admin.products.detail-values.store', $product) }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <!-- Keyword Field -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Keyword</label>
                                        <input type="text" 
                                               class="form-control @error('keyword') is-invalid @enderror" 
                                               name="keyword" 
                                               placeholder="Enter keyword (e.g., dimensions, material, color)"
                                               value="{{ old('keyword') }}"
                                               required>
                                        @error('keyword')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">This keyword will be used to identify this detail value.</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Language Tabs -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <label class="form-label">Detail Value Translations</label>
                                    <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                                        @foreach($languages as $index => $language)
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                                    id="tab-{{ $language->id }}" 
                                                    data-bs-toggle="tab" 
                                                    data-bs-target="#content-{{ $language->id }}" 
                                                    type="button" 
                                                    role="tab">
                                                    {{ $language->name ?? $language->lang_name ?? $language->title }} ({{ $language->lang_code }})
                                            </button>
                                        </li>
                                        @endforeach
                                    </ul>

                                    <!-- Tab Content -->
                                    <div class="tab-content" id="languageTabContent">
                                        @foreach($languages as $index => $language)
                                        <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                             id="content-{{ $language->id }}" 
                                             role="tabpanel" 
                                             aria-labelledby="tab-{{ $language->id }}">
                                            
                                            <div class="card mt-3">
                                                <div class="card-header">
                                                    <h6 class="mb-0">{{ $language->name ?? $language->lang_name ?? $language->title }} Content</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Value</label>
                                                        <input type="text" 
                                                               class="form-control @error('translates.'.$index.'.value') is-invalid @enderror" 
                                                               name="translates[{{ $index }}][value]" 
                                                               placeholder="Enter value for {{ $language->lang_code ?? 'this language' }}"
                                                               value="{{ old('translates.'.$index.'.value') }}"
                                                               required>
                                                        @error('translates.'.$index.'.value')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <!-- Hidden language_id field -->
                                                    <input type="hidden" name="translates[{{ $index }}][language_id]" value="{{ $language->id }}">
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">save</i> Save Detail Value
                                        </button>
                                        <a href="{{ route('admin.products.detail-values.index', $product) }}" class="btn btn-outline-secondary">
                                            <i class="material-icons-outlined">cancel</i> Cancel
                                        </a>
                                    </div>
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

@push('styles')
<style>
.nav-tabs .nav-link {
    color: #495057;
    border: 1px solid #dee2e6;
    border-bottom: none;
    border-radius: 0.375rem 0.375rem 0 0;
    margin-right: 2px;
}

.nav-tabs .nav-link.active {
    color: #007bff;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
}

.nav-tabs .nav-link:hover {
    border-color: #e9ecef #e9ecef #dee2e6;
}
</style>
@endpush

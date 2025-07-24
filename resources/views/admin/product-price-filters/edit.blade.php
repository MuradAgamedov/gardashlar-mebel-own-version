@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Qiymət Filtirini Redaktə Et</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.product-price-filters.index') }}">Qiymət Filtrləri</a>
                        </li>
                        <li class="breadcrumb-item active">Redaktə Et</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="material-icons-outlined">error</i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Qiymət Filtirini Redaktə Et</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.product-price-filters.update', $productPriceFilter->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="min_price" class="form-label">Min Qiymət (₼)</label>
                                        <input type="number" 
                                               class="form-control @error('min_price') is-invalid @enderror" 
                                               id="min_price" 
                                               name="min_price" 
                                               value="{{ old('min_price', $productPriceFilter->min_price ?? '') }}" 
                                               step="0.01" 
                                               min="0" 
                                               placeholder="0.00">
                                        @error('min_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Boş buraxa bilərsiniz</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="max_price" class="form-label">Max Qiymət (₼)</label>
                                        <input type="number" 
                                               class="form-control @error('max_price') is-invalid @enderror" 
                                               id="max_price" 
                                               name="max_price" 
                                               value="{{ old('max_price', $productPriceFilter->max_price ?? '') }}" 
                                               step="0.01" 
                                               min="0" 
                                               placeholder="0.00">
                                        @error('max_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Boş buraxa bilərsiniz</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="status" 
                                           name="status" 
                                           value="1" 
                                           {{ old('status', $productPriceFilter->status ? '1' : '0') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status">
                                        Aktiv
                                    </label>
                                </div>
                            </div>

                            <!-- Language Tabs -->
                            <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                                @foreach($languages as $index => $language)
                                    @php
                                        $translate = $productPriceFilter->translates->where('language_id', $language->id)->first();
                                    @endphp
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                                id="tab-{{ $language->id }}" 
                                                data-bs-toggle="tab" 
                                                data-bs-target="#content-{{ $language->id }}" 
                                                type="button" 
                                                role="tab"
                                                @if($errors->has("translates.{$language->id}.*"))
                                                    style="border-color: #dc3545; color: #dc3545;"
                                                @endif>
                                            {{ $language->lang_code }}
                                            @if($errors->has("translates.{$language->id}.*"))
                                                <i class="material-icons-outlined text-danger ms-1" style="font-size: 16px;">error</i>
                                            @endif
                                        </button>
                                    </li>
                                @endforeach
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content mt-3" id="languageTabContent">
                                @foreach($languages as $index => $language)
                                    @php
                                        $translate = $productPriceFilter->translates->where('language_id', $language->id)->first();
                                        $title = old("translates.{$language->id}.title", $translate ? $translate->title : '');
                                    @endphp
                                    
                                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                         id="content-{{ $language->id }}" 
                                         role="tabpanel" 
                                         aria-labelledby="tab-{{ $language->id }}">
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Başlıq *</label>
                                            <input type="text" 
                                                   class="form-control @error("translates.{$language->id}.title") is-invalid @enderror" 
                                                   name="translates[{{ $language->id }}][title]" 
                                                   value="{{ $title }}"
                                                   placeholder="Qiymət aralığı başlığı"
                                                   required>
                                            @error("translates.{$language->id}.title")
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        

                                    </div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('admin.product-price-filters.index') }}" class="btn btn-secondary">
                                    <i class="material-icons-outlined">arrow_back</i>
                                    Geri
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="material-icons-outlined">save</i>
                                    Yenilə
                                </button>
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
$(document).ready(function() {
    // Price validation
    $('#min_price, #max_price').on('input', function() {
        var minPrice = parseFloat($('#min_price').val()) || 0;
        var maxPrice = parseFloat($('#max_price').val()) || 0;
        
        if (maxPrice > 0 && minPrice > maxPrice) {
            $('#max_price').addClass('is-invalid');
            $('#max_price').next('.invalid-feedback').remove();
            $('#max_price').after('<div class="invalid-feedback">Max qiymət min qiymətdən böyük olmalıdır</div>');
        } else {
            $('#max_price').removeClass('is-invalid');
            $('#max_price').next('.invalid-feedback').remove();
        }
    });
});
</script>
@endpush
@endsection 
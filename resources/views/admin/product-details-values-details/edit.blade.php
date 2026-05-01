@extends('admin.layout.app')

@section('title', 'Edit Detail - ' . ($detailValue->translate()->keyword ?? 'Detail Value'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Edit Detail</h3>
                    <p class="text-muted">
                        {{ $product->translate()->title ?? 'Product' }} > 
                        {{ $detailValue->translate()->keyword ?? 'Detail Value' }}
                    </p>
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
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.products.detail-values.details.index', [$product, $detailValue]) }}">Details</a>
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
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Edit Detail</h5>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.products.detail-values.details.index', [$product, $detailValue]) }}" class="btn btn-outline-secondary">
                                    <i class="material-icons-outlined">arrow_back</i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <form action="{{ route('admin.products.detail-values.details.update', [$product, $detailValue, $detail]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0">Translations</h6>
                                        </div>
                                        <div class="card-body">
                                            <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                                                @foreach($languages as $index => $language)
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                                                id="tab-{{ $language->id }}" 
                                                                data-bs-toggle="tab" 
                                                                data-bs-target="#content-{{ $language->id }}" 
                                                                type="button" 
                                                                role="tab" 
                                                                aria-controls="content-{{ $language->id }}" 
                                                                aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                                            {{ $language->lang_code }}
                                                        </button>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            
                                            <div class="tab-content" id="languageTabContent">
                                                @foreach($languages as $index => $language)
                                                    @php
                                                        $translation = $detail->translates->where('language_id', $language->id)->first();
                                                        $keyword = $translation ? $translation->keyword : '';
                                                        $value = $translation ? $translation->value : '';
                                                    @endphp
                                                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                                         id="content-{{ $language->id }}" 
                                                         role="tabpanel" 
                                                         aria-labelledby="tab-{{ $language->id }}">
                                                        <div class="p-3">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Keyword</label>
                                                                        <input type="text" class="form-control" 
                                                                               name="translates[{{ $language->id }}][keyword]" 
                                                                               placeholder="Enter keyword for {{ $language->lang_code }}"
                                                                               value="{{ old('translates.'.$language->id.'.keyword', $keyword) }}"
                                                                               required>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Value</label>
                                                                        <textarea class="form-control" 
                                                                                  name="translates[{{ $language->id }}][value]" 
                                                                                  placeholder="Enter value for {{ $language->lang_code }}"
                                                                                  rows="3"
                                                                                  required>{{ old('translates.'.$language->id.'.value', $value) }}</textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.products.detail-values.details.index', [$product, $detailValue]) }}" class="btn btn-outline-secondary" title="{{ __('Cancel') }}">
                                    <i class="material-icons-outlined">cancel</i>
                                </a>
                                <button type="submit" class="btn btn-primary" title="{{ __('Update Detail') }}">
                                    <i class="material-icons-outlined">save</i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Add language_id to form data when submitting
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        // Add language_id to each translation field
        const tabPanes = document.querySelectorAll('.tab-pane');
        tabPanes.forEach(tabPane => {
            const languageId = tabPane.id.replace('content-', '');
            const keywordInput = tabPane.querySelector('input[name*="[keyword]"]');
            const valueTextarea = tabPane.querySelector('textarea[name*="[value]"]');
            
            if (keywordInput && valueTextarea) {
                // Create hidden input for language_id
                const languageIdInput = document.createElement('input');
                languageIdInput.type = 'hidden';
                languageIdInput.name = keywordInput.name.replace('[keyword]', '[language_id]');
                languageIdInput.value = languageId;
                form.appendChild(languageIdInput);
            }
        });
    });
});
</script>
@endpush

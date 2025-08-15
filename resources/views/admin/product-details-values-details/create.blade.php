@extends('admin.layout.app')

@section('title', 'Create Detail - ' . ($detailValue->translate()->keyword ?? 'Detail Value'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Create Detail</h3>
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
                        <li class="breadcrumb-item active">Create</li>
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
                            <h5>Create New Detail</h5>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.products.detail-values.details.index', [$product, $detailValue]) }}" class="btn btn-outline-secondary">
                                    <i class="material-icons-outlined">arrow_back</i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <form action="{{ route('admin.products.detail-values.details.store', [$product, $detailValue]) }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0">Translations</h6>
                                                <button type="button" class="btn btn-sm btn-primary" onclick="addTranslationPair()">
                                                    <i class="material-icons-outlined">add</i> Add Language
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div id="translations-container">
                                                <!-- Translation pairs will be added here -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.products.detail-values.details.index', [$product, $detailValue]) }}" class="btn btn-secondary">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="material-icons-outlined">save</i> Create Detail
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
let translationIndex = 0;

// Add initial translation pair
document.addEventListener('DOMContentLoaded', function() {
    addTranslationPair();
});

function addTranslationPair() {
    const container = document.getElementById('translations-container');
    const pairDiv = document.createElement('div');
    pairDiv.className = 'translation-pair border rounded p-3 mb-3';
    pairDiv.id = `translation-pair-${translationIndex}`;
    
    pairDiv.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Language ${translationIndex + 1}</h6>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeTranslationPair(${translationIndex})">
                <i class="material-icons-outlined">delete</i>
            </button>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Language</label>
                    <select class="form-select @error('translates.${translationIndex}.language_id') is-invalid @enderror" 
                            name="translates[${translationIndex}][language_id]" required>
                        <option value="">Select Language</option>
                        @foreach($languages as $language)
                            <option value="{{ $language->id }}" {{ old('translates.${translationIndex}.language_id') == $language->id ? 'selected' : '' }}>
                                {{ $language->lang_code }} - {{ $language->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('translates.${translationIndex}.language_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Keyword</label>
                    <input type="text" class="form-control @error('translates.${translationIndex}.keyword') is-invalid @enderror" 
                           name="translates[${translationIndex}][keyword]" 
                           placeholder="Enter keyword for this language"
                           value="{{ old('translates.${translationIndex}.keyword') }}"
                           required>
                    @error('translates.${translationIndex}.keyword')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Value</label>
                    <textarea class="form-control @error('translates.${translationIndex}.value') is-invalid @enderror" 
                              name="translates[${translationIndex}][value]" 
                              placeholder="Enter value for this language"
                              rows="3"
                              required>{{ old('translates.${translationIndex}.value') }}</textarea>
                    @error('translates.${translationIndex}.value')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    `;
    
    container.appendChild(pairDiv);
    translationIndex++;
}

function removeTranslationPair(index) {
    const pairDiv = document.getElementById(`translation-pair-${index}`);
    if (pairDiv) {
        pairDiv.remove();
    }
    
    // Reindex remaining pairs
    const pairs = document.querySelectorAll('.translation-pair');
    pairs.forEach((pair, newIndex) => {
        pair.id = `translation-pair-${newIndex}`;
        pair.querySelector('h6').textContent = `Language ${newIndex + 1}`;
        
        // Update form field names
        const languageSelect = pair.querySelector('select[name*="[language_id]"]');
        const keywordInput = pair.querySelector('input[name*="[keyword]"]');
        const valueTextarea = pair.querySelector('textarea[name*="[value]"]');
        
        if (languageSelect) languageSelect.name = `translates[${newIndex}][language_id]`;
        if (keywordInput) keywordInput.name = `translates[${newIndex}][keyword]`;
        if (valueTextarea) valueTextarea.name = `translates[${newIndex}][value]`;
        
        // Update remove button onclick
        const removeBtn = pair.querySelector('button[onclick*="removeTranslationPair"]');
        if (removeBtn) removeBtn.setAttribute('onclick', `removeTranslationPair(${newIndex})`);
    });
    
    translationIndex = pairs.length;
}
</script>
@endpush

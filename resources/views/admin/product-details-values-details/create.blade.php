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
                    
                    <form action="{{ route('admin.products.detail-values.details.store', [$product, $detailValue]) }}" method="POST" id="createDetailForm">
                        @csrf
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0">Translations</h6>
                                                <button type="button" class="btn btn-sm btn-primary" onclick="addKeywordValuePair()">
                                                    <i class="material-icons-outlined">add</i> Add Keyword & Value
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div id="translations-container">
                                                <!-- Translation tabs will be added here -->
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
                                <button type="submit" class="btn btn-primary" id="submitBtn">
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

// Add initial language tabs
document.addEventListener('DOMContentLoaded', function() {
    createLanguageTabs();
    // Add initial keyword-value pair to the first language
    setTimeout(() => {
        addKeywordValuePair();
    }, 100);
});

function createLanguageTabs() {
    const container = document.getElementById('translations-container');
    
    // Create tab navigation
    const tabNav = document.createElement('ul');
    tabNav.className = 'nav nav-tabs mb-3';
    tabNav.id = 'language-tabs';
    
    // Create tab content container
    const tabContent = document.createElement('div');
    tabContent.className = 'tab-content';
    tabContent.id = 'language-tab-content';
    
    // Create tabs for each language
    @foreach($languages as $index => $language)
        const tabButton{{ $index }} = document.createElement('li');
        tabButton{{ $index }}.className = 'nav-item';
        tabButton{{ $index }}.innerHTML = `
            <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                    id="tab-{{ $language->id }}" 
                    data-bs-toggle="tab" 
                    data-bs-target="#content-{{ $language->id }}" 
                    type="button" 
                    role="tab">
                {{ $language->lang_code }}
            </button>
        `;
        
        const tabPane{{ $index }} = document.createElement('div');
        tabPane{{ $index }}.className = 'tab-pane fade {{ $index === 0 ? 'show active' : '' }}';
        tabPane{{ $index }}.id = 'content-{{ $language->id }}';
        tabPane{{ $index }}.innerHTML = `
            <div class="p-3">
                <div id="pairs-{{ $language->id }}">
                    <!-- Keyword & Value pairs will be added here -->
                </div>
            </div>
        `;
        
        tabNav.appendChild(tabButton{{ $index }});
        tabContent.appendChild(tabPane{{ $index }});
    @endforeach
    
    container.appendChild(tabNav);
    container.appendChild(tabContent);
}

function addKeywordValuePair() {
    // Get active tab
    const activeTab = document.querySelector('.nav-link.active');
    if (!activeTab) return;
    
    const languageId = activeTab.getAttribute('data-bs-target').replace('#content-', '');
    const pairsContainer = document.getElementById(`pairs-${languageId}`);
    
    // Check if this language already has a pair
    const existingPairs = pairsContainer.querySelectorAll('.keyword-value-pair');
    if (existingPairs.length > 0) {
        alert('Bu dil üçün artıq keyword-value pair mövcuddur. Hər dil üçün yalnız bir pair əlavə edə bilərsiniz.');
        return;
    }
    
    const pairDiv = document.createElement('div');
    pairDiv.className = 'keyword-value-pair border rounded p-3 mb-3';
    pairDiv.id = `pair-${translationIndex}`;
    
    pairDiv.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Pair ${translationIndex + 1}</h6>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeKeywordValuePair(${translationIndex})">
                <i class="material-icons-outlined">delete</i>
            </button>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Keyword <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" 
                           name="translates[${translationIndex}][keyword]" 
                           placeholder="Enter keyword"
                           required>
                    <input type="hidden" name="translates[${translationIndex}][language_id]" value="${languageId}">
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Value <span class="text-danger">*</span></label>
                    <textarea class="form-control" 
                              name="translates[${translationIndex}][value]" 
                              placeholder="Enter value"
                              rows="3"
                              required></textarea>
                </div>
            </div>
        </div>
    `;
    
    pairsContainer.appendChild(pairDiv);
    translationIndex++;
}

function removeKeywordValuePair(index) {
    const pairDiv = document.getElementById(`pair-${index}`);
    if (pairDiv) {
        pairDiv.remove();
    }
    
    // Reindex remaining pairs
    const pairs = document.querySelectorAll('.keyword-value-pair');
    pairs.forEach((pair, newIndex) => {
        pair.id = `pair-${newIndex}`;
        pair.querySelector('h6').textContent = `Pair ${newIndex + 1}`;
        
        // Update form field names
        const keywordInput = pair.querySelector('input[name*="[keyword]"]');
        const valueTextarea = pair.querySelector('textarea[name*="[value]"]');
        const languageInput = pair.querySelector('input[name*="[language_id]"]');
        
        if (keywordInput) keywordInput.name = `translates[${newIndex}][keyword]`;
        if (valueTextarea) valueTextarea.name = `translates[${newIndex}][value]`;
        if (languageInput) languageInput.name = `translates[${newIndex}][language_id]`;
        
        // Update remove button onclick
        const removeBtn = pair.querySelector('button[onclick*="removeKeywordValuePair"]');
        if (removeBtn) removeBtn.setAttribute('onclick', `removeKeywordValuePair(${newIndex})`);
    });
    
    translationIndex = pairs.length;
}

// Form validation before submit
document.getElementById('createDetailForm').addEventListener('submit', function(e) {
    const pairs = document.querySelectorAll('.keyword-value-pair');
    if (pairs.length === 0) {
        e.preventDefault();
        alert('Zəhmət olmasa ən azı bir keyword-value pair əlavə edin.');
        return false;
    }
    
    // Check if all required fields are filled
    let isValid = true;
    const languageIds = new Set();
    
    pairs.forEach(pair => {
        const keyword = pair.querySelector('input[name*="[keyword]"]').value.trim();
        const value = pair.querySelector('textarea[name*="[value]"]').value.trim();
        const languageId = pair.querySelector('input[name*="[language_id]"]').value;
        
        if (!keyword || !value) {
            isValid = false;
        }
        
        // Check for duplicate language IDs
        if (languageIds.has(languageId)) {
            isValid = false;
            alert('Eyni dil üçün birdən çox pair əlavə edə bilməzsiniz.');
            return false;
        }
        languageIds.add(languageId);
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Zəhmət olmasa bütün tələb olunan sahələri doldurun (keyword və value).');
        return false;
    }
    
    // Disable submit button to prevent double submission
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').innerHTML = '<i class="material-icons-outlined">hourglass_empty</i> Creating...';
});
</script>
@endpush

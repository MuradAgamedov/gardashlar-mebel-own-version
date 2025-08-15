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
                                            <h6 class="mb-0">Translations</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0">Translation Pairs</h6>
                                                <button type="button" class="btn btn-sm btn-primary" onclick="addTranslationTab()">
                                                    <i class="material-icons-outlined">add</i> Add Language Tab
                                                </button>
                                            </div>
                                            
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

// Add initial translation tab
document.addEventListener('DOMContentLoaded', function() {
    addTranslationTab();
});

function addTranslationTab() {
    const container = document.getElementById('translations-container');
    
    // Create tab navigation
    const tabNav = document.createElement('ul');
    tabNav.className = 'nav nav-tabs mb-3';
    tabNav.id = `tab-nav-${translationIndex}`;
    
    // Create tab content container
    const tabContent = document.createElement('div');
    tabContent.className = 'tab-content';
    tabContent.id = `tab-content-${translationIndex}`;
    
    // Create tab button
    const tabButton = document.createElement('li');
    tabButton.className = 'nav-item';
    tabButton.innerHTML = `
        <button class="nav-link active" 
                id="tab-${translationIndex}" 
                data-bs-toggle="tab" 
                data-bs-target="#content-${translationIndex}" 
                type="button" 
                role="tab">
            Language ${translationIndex + 1}
            <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="removeTranslationTab(${translationIndex})">
                <i class="material-icons-outlined">delete</i>
            </button>
        </button>
    `;
    
    // Create tab content
    const tabPane = document.createElement('div');
    tabPane.className = 'tab-pane fade show active';
    tabPane.id = `content-${translationIndex}`;
    tabPane.innerHTML = `
        <div class="p-3">
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Language</label>
                        <select class="form-select" name="translates[${translationIndex}][language_id]" required>
                            <option value="">Select Language</option>
                            @foreach($languages as $language)
                                <option value="{{ $language->id }}">
                                    {{ $language->lang_code }} - {{ $language->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Keyword</label>
                        <input type="text" class="form-control" 
                               name="translates[${translationIndex}][keyword]" 
                               placeholder="Enter keyword for this language"
                               required>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Value</label>
                        <textarea class="form-control" 
                                  name="translates[${translationIndex}][value]" 
                                  placeholder="Enter value for this language"
                                  rows="3"
                                  required></textarea>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Append elements
    tabNav.appendChild(tabButton);
    tabContent.appendChild(tabPane);
    container.appendChild(tabNav);
    container.appendChild(tabContent);
    
    translationIndex++;
}

function removeTranslationTab(index) {
    // Remove tab navigation
    const tabNav = document.getElementById(`tab-nav-${index}`);
    if (tabNav) tabNav.remove();
    
    // Remove tab content
    const tabContent = document.getElementById(`tab-content-${index}`);
    if (tabContent) tabContent.remove();
    
    // Reindex remaining tabs
    const remainingTabs = document.querySelectorAll('[id^="tab-nav-"]');
    remainingTabs.forEach((tabNav, newIndex) => {
        const oldIndex = tabNav.id.replace('tab-nav-', '');
        const tabContent = document.getElementById(`tab-content-${oldIndex}`);
        
        if (tabContent) {
            // Update IDs
            tabNav.id = `tab-nav-${newIndex}`;
            tabContent.id = `tab-content-${newIndex}`;
            
            // Update tab button
            const tabButton = tabNav.querySelector('button');
            tabButton.id = `tab-${newIndex}`;
            tabButton.setAttribute('data-bs-target', `#content-${newIndex}`);
            tabButton.innerHTML = tabButton.innerHTML.replace(`Language ${parseInt(oldIndex) + 1}`, `Language ${newIndex + 1}`);
            
            // Update remove button onclick
            const removeBtn = tabButton.querySelector('button[onclick*="removeTranslationTab"]');
            if (removeBtn) removeBtn.setAttribute('onclick', `removeTranslationTab(${newIndex})`);
            
            // Update form field names
            const languageSelect = tabContent.querySelector('select[name*="[language_id]"]');
            const keywordInput = tabContent.querySelector('input[name*="[keyword]"]');
            const valueTextarea = tabContent.querySelector('textarea[name*="[value]"]');
            
            if (languageSelect) languageSelect.name = `translates[${newIndex}][language_id]`;
            if (keywordInput) keywordInput.name = `translates[${newIndex}][keyword]`;
            if (valueTextarea) valueTextarea.name = `translates[${newIndex}][value]`;
        }
    });
    
    translationIndex = remainingTabs.length;
}
</script>
@endpush

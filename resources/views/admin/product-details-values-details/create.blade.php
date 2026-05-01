@extends('admin.layout.app')

@section('title', 'Create Details - ' . ($detailValue->translate()->keyword ?? 'Detail Value'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Create Details</h3>
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
                            <h5>Create New Details</h5>
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
                                                <h6 class="mb-0">Details by Language</h6>
                                                <small class="text-muted">Each language has its own form fields</small>
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
                                <a href="{{ route('admin.products.detail-values.details.index', [$product, $detailValue]) }}" class="btn btn-outline-secondary" title="{{ __('Cancel') }}">
                                    <i class="material-icons-outlined">cancel</i>
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn" title="{{ __('Create Details') }}">
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
let translationIndex = 0;

// Manual tab switching function (fallback for Bootstrap issues)
function manualTabSwitch(tabElement) {
    const targetId = tabElement.getAttribute('data-bs-target');
    const targetPane = document.querySelector(targetId);
    
    if (!targetPane) {
        console.error('Target pane not found:', targetId);
        return;
    }
    
    // Remove active class from all tabs and panes
    document.querySelectorAll('.nav-link').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(pane => {
        pane.classList.remove('show', 'active');
    });
    
    // Add active class to clicked tab and corresponding pane
    tabElement.classList.add('active');
    targetPane.classList.add('show', 'active');
    
    // Force display the target pane
    targetPane.style.display = 'block';
    targetPane.style.opacity = '1';
    
    console.log('Tab switched to:', targetId);
    console.log('Target pane found:', targetPane);
    console.log('Target pane classes:', targetPane.className);
}

// Wait for Bootstrap to be fully loaded
function waitForBootstrap() {
    return new Promise((resolve) => {
        if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {
            console.log('Bootstrap is ready!');
            resolve();
        } else {
            console.log('Waiting for Bootstrap...');
            setTimeout(() => waitForBootstrap().then(resolve), 100);
        }
    });
}

// Add initial language tabs
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, creating language tabs...');
    
    // Wait for Bootstrap to be ready, then create tabs
    waitForBootstrap().then(() => {
        createLanguageTabs();
        
        // Add initial keyword-value pair to all languages
        setTimeout(() => {
            @foreach($languages as $language)
                addKeywordValuePairForLanguage({{ $language->id }});
            @endforeach
        }, 200);
    });
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
                    role="tab"
                    onclick="manualTabSwitch(this)">
                {{ $language->lang_code }}
            </button>
        `;
        
        const tabPane{{ $index }} = document.createElement('div');
        tabPane{{ $index }}.className = 'tab-pane fade {{ $index === 0 ? 'show active' : '' }}';
        tabPane{{ $index }}.id = 'content-{{ $language->id }}';
        tabPane{{ $index }}.innerHTML = `
            <div class="p-3">
                <div class="mb-3">
                    <h6>Language: {{ $language->lang_code }}</h6>
                    <p class="text-muted">Add keyword-value pairs for {{ $language->lang_code }} language</p>
                </div>
                <div id="pairs-{{ $language->id }}">
                    <!-- Keyword & Value pairs will be added here -->
                </div>
                <div class="mt-3">
                    <button type="button" class="btn btn-sm btn-primary" onclick="addKeywordValuePairForLanguage({{ $language->id }})" title="{{ __('Add Keyword & Value for :lang', ['lang' => $language->lang_code]) }}">
                        <i class="material-icons-outlined">add</i>
                    </button>
                </div>
            </div>
        `;
        
        tabNav.appendChild(tabButton{{ $index }});
        tabContent.appendChild(tabPane{{ $index }});
    @endforeach
    
    container.appendChild(tabNav);
    container.appendChild(tabContent);
    
    // Initialize Bootstrap tabs
    const tabElements = container.querySelectorAll('[data-bs-toggle="tab"]');
    console.log('Found tab elements:', tabElements.length);
    
    let bootstrapInitialized = false;
    
    tabElements.forEach((tabElement, index) => {
        try {
            new bootstrap.Tab(tabElement);
            console.log(`Tab ${index + 1} initialized successfully with Bootstrap`);
            bootstrapInitialized = true;
        } catch (error) {
            console.error(`Error initializing tab ${index + 1} with Bootstrap:`, error);
        }
    });
    
    // If Bootstrap failed, ensure manual switching works
    if (!bootstrapInitialized) {
        console.log('Bootstrap initialization failed, using manual tab switching');
        // Remove data-bs-toggle to prevent Bootstrap interference
        tabElements.forEach(tabElement => {
            tabElement.removeAttribute('data-bs-toggle');
        });
    }
    
    // Debug: Log that tabs were created
    console.log('Language tabs created successfully');
}

// Add keyword-value pair for specific language
function addKeywordValuePairForLanguage(languageId) {
    const pairsContainer = document.getElementById(`pairs-${languageId}`);
    
    if (!pairsContainer) {
        console.error('Pairs container not found for language:', languageId);
        console.log('Available containers:', document.querySelectorAll('[id^="pairs-"]'));
        return;
    }
    
    console.log('Adding keyword-value pair to language:', languageId);
    createKeywordValuePair(pairsContainer, languageId);
}

// Legacy function for backward compatibility
function addKeywordValuePair() {
    // Get active tab
    const activeTab = document.querySelector('.nav-link.active');
    if (!activeTab) {
        console.error('No active tab found');
        return;
    }
    
    const languageId = activeTab.getAttribute('data-bs-target').replace('#content-', '');
    addKeywordValuePairForLanguage(languageId);
}

// Create keyword-value pair in specific container
function createKeywordValuePair(pairsContainer, languageId) {
    
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
    
    pairs.forEach(pair => {
        const keyword = pair.querySelector('input[name*="[keyword]"]').value.trim();
        const value = pair.querySelector('textarea[name*="[value]"]').value.trim();
        
        if (!keyword || !value) {
            isValid = false;
        }
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

<style>
.keyword-value-pair {
    background-color: #f8f9fa;
    border-left: 4px solid #007bff !important;
}

.keyword-value-pair:hover {
    background-color: #e9ecef;
}

/* Force tab content to be visible */
.tab-pane {
    display: none !important;
}

.tab-pane.show.active {
    display: block !important;
    opacity: 1 !important;
}

/* Ensure tab content is visible */
.tab-content {
    min-height: 200px;
    border: 1px solid #dee2e6;
    border-top: none;
    border-radius: 0 0 0.375rem 0.375rem;
    background-color: #fff;
}
</style>
@endpush

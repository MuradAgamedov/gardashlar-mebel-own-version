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
                                                <h6 class="mb-0">Details</h6>
                                                <button type="button" class="btn btn-sm btn-primary" onclick="addDetail()">
                                                    <i class="material-icons-outlined">add</i> Add Detail
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div id="details-container">
                                                <!-- Details will be added here -->
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
                                    <i class="material-icons-outlined">save</i> Create Details
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
let detailIndex = 0;

// Add initial detail
document.addEventListener('DOMContentLoaded', function() {
    addDetail();
});

function addDetail() {
    const container = document.getElementById('details-container');
    
    const detailDiv = document.createElement('div');
    detailDiv.className = 'detail-item border rounded p-4 mb-4';
    detailDiv.id = `detail-${detailIndex}`;
    
    detailDiv.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Detail ${detailIndex + 1}</h6>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeDetail(${detailIndex})">
                <i class="material-icons-outlined">delete</i>
            </button>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Language <span class="text-danger">*</span></label>
                    <select class="form-select" name="translates[${detailIndex}][language_id]" required>
                        <option value="">Select Language</option>
                        @foreach($languages as $language)
                            <option value="{{ $language->id }}">{{ $language->lang_code }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Keyword <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" 
                           name="translates[${detailIndex}][keyword]" 
                           placeholder="Enter keyword"
                           required>
                </div>
            </div>
            
            <div class="col-12">
                <div class="mb-3">
                    <label class="form-label">Value <span class="text-danger">*</span></label>
                    <textarea class="form-control" 
                              name="translates[${detailIndex}][value]" 
                              placeholder="Enter value"
                              rows="3"
                              required></textarea>
                </div>
            </div>
        </div>
    `;
    
    container.appendChild(detailDiv);
    detailIndex++;
}

function removeDetail(index) {
    const detailDiv = document.getElementById(`detail-${index}`);
    if (detailDiv) {
        detailDiv.remove();
    }
    
    // Reindex remaining details
    const details = document.querySelectorAll('.detail-item');
    details.forEach((detail, newIndex) => {
        detail.id = `detail-${newIndex}`;
        detail.querySelector('h6').textContent = `Detail ${newIndex + 1}`;
        
        // Update form field names
        const languageSelect = detail.querySelector('select[name*="[language_id]"]');
        const keywordInput = detail.querySelector('input[name*="[keyword]"]');
        const valueTextarea = detail.querySelector('textarea[name*="[value]"]');
        
        if (languageSelect) languageSelect.name = `translates[${newIndex}][language_id]`;
        if (keywordInput) keywordInput.name = `translates[${newIndex}][keyword]`;
        if (valueTextarea) valueTextarea.name = `translates[${newIndex}][value]`;
        
        // Update remove button onclick
        const removeBtn = detail.querySelector('button[onclick*="removeDetail"]');
        if (removeBtn) removeBtn.setAttribute('onclick', `removeDetail(${newIndex})`);
    });
    
    detailIndex = details.length;
}

// Form validation before submit
document.getElementById('createDetailForm').addEventListener('submit', function(e) {
    const details = document.querySelectorAll('.detail-item');
    if (details.length === 0) {
        e.preventDefault();
        alert('Zəhmət olmasa ən azı bir detail əlavə edin.');
        return false;
    }
    
    // Check if all required fields are filled
    let isValid = true;
    
    details.forEach(detail => {
        const language = detail.querySelector('select[name*="[language_id]"]').value;
        const keyword = detail.querySelector('input[name*="[keyword]"]').value.trim();
        const value = detail.querySelector('textarea[name*="[value]"]').value.trim();
        
        if (!language || !keyword || !value) {
            isValid = false;
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Zəhmət olmasa bütün tələb olunan sahələri doldurun.');
        return false;
    }
    
    // Disable submit button to prevent double submission
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').innerHTML = '<i class="material-icons-outlined">hourglass_empty</i> Creating...';
});
</script>

<style>
.detail-item {
    background-color: #f8f9fa;
    border-left: 4px solid #007bff !important;
}

.detail-item:hover {
    background-color: #e9ecef;
}
</style>
@endpush

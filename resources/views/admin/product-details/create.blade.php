@extends('admin.layout.app')

@section('title', 'Add Product Detail - ' . ($product->translate()->title ?? 'Product'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Add Product Detail</h3>
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
                            <a href="{{ route('admin.products.details.index', $product) }}">Product Details</a>
                        </li>
                        <li class="breadcrumb-item active">Add Detail</li>
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
                            <h5>Add New Detail for: <strong>{{ $product->translate()->title ?? 'Product' }}</strong></h5>
                            <a href="{{ route('admin.products.details.index', $product) }}" class="btn btn-outline-secondary">
                                <i class="material-icons-outlined">arrow_back</i> Back to Details
                            </a>
                        </div>
                    </div>
                    
                    <form action="{{ route('admin.products.details.store', $product) }}" method="POST" id="productDetailForm">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <i class="material-icons-outlined">info</i>
                                        <strong>Instructions:</strong> Use the buttons below to add keyword-value pairs. You can add as many as you need.
                                    </div>
                                </div>
                            </div>

                            <!-- Dynamic Input Fields Container -->
                            <div id="dynamicInputsContainer">
                                <!-- Initial input group will be added here -->
                            </div>

                            <!-- Action Buttons -->
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-success" id="addSingleInput">
                                            <i class="material-icons-outlined">add</i> Add Single Input
                                        </button>
                                        <button type="button" class="btn btn-primary" id="addMultipleInputs">
                                            <i class="material-icons-outlined">add_circle</i> Add Multiple Inputs
                                        </button>
                                        <button type="button" class="btn btn-warning" id="clearAllInputs">
                                            <i class="material-icons-outlined">clear_all</i> Clear All
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                            <i class="material-icons-outlined">save</i> Save Details
                                        </button>
                                        <a href="{{ route('admin.products.details.index', $product) }}" class="btn btn-outline-secondary">
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
.input-group-wrapper {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    background: #f8f9fa;
    position: relative;
}

.input-group-wrapper:hover {
    border-color: #007bff;
    box-shadow: 0 2px 8px rgba(0,123,255,0.1);
}

.input-group-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #dee2e6;
}

.input-group-title {
    font-weight: 600;
    color: #495057;
    margin: 0;
}

.remove-input-group {
    background: #dc3545;
    border: none;
    color: white;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.remove-input-group:hover {
    background: #c82333;
    transform: scale(1.1);
}

.keyword-value-row {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
    align-items: flex-start;
}

.keyword-input {
    flex: 1;
}

.value-input {
    flex: 2;
}

.remove-row-btn {
    background: #6c757d;
    border: none;
    color: white;
    border-radius: 4px;
    padding: 8px 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 5px;
}

.remove-row-btn:hover {
    background: #5a6268;
}

.add-row-btn {
    background: #28a745;
    border: none;
    color: white;
    border-radius: 4px;
    padding: 8px 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 10px;
}

.add-row-btn:hover {
    background: #218838;
}

.input-counter {
    position: absolute;
    top: 10px;
    right: 50px;
    background: #007bff;
    color: white;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
}

.empty-state {
    text-align: center;
    padding: 40px;
    color: #6c757d;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 15px;
}

.submit-btn-disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let inputGroupCounter = 0;
    let totalInputs = 0;
    
    const container = document.getElementById('dynamicInputsContainer');
    const addSingleBtn = document.getElementById('addSingleInput');
    const addMultipleBtn = document.getElementById('addMultipleInputs');
    const clearAllBtn = document.getElementById('clearAllBtn');
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('productDetailForm');

    // Add single input group
    addSingleBtn.addEventListener('click', function() {
        addInputGroup(1);
    });

    // Add multiple input groups
    addMultipleBtn.addEventListener('click', function() {
        const count = prompt('How many input groups would you like to add?', '3');
        if (count && !isNaN(count) && count > 0) {
            addInputGroup(parseInt(count));
        }
    });

    // Clear all inputs
    clearAllBtn.addEventListener('click', function() {
        if (confirm('Are you sure you want to clear all inputs?')) {
            container.innerHTML = '';
            inputGroupCounter = 0;
            totalInputs = 0;
            updateSubmitButton();
            showEmptyState();
        }
    });

    // Add input group(s)
    function addInputGroup(count = 1) {
        for (let i = 0; i < count; i++) {
            inputGroupCounter++;
            const inputGroup = createInputGroup(inputGroupCounter);
            container.appendChild(inputGroup);
        }
        updateSubmitButton();
        hideEmptyState();
    }

    // Create input group
    function createInputGroup(groupId) {
        const wrapper = document.createElement('div');
        wrapper.className = 'input-group-wrapper';
        wrapper.dataset.groupId = groupId;

        const counter = document.createElement('div');
        counter.className = 'input-counter';
        counter.textContent = '1';

        const header = document.createElement('div');
        header.className = 'input-group-header';

        const title = document.createElement('h6');
        title.className = 'input-group-title';
        title.textContent = `Input Group ${groupId}`;

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'remove-input-group';
        removeBtn.innerHTML = '<i class="material-icons-outlined">close</i>';
        removeBtn.onclick = () => removeInputGroup(wrapper);

        header.appendChild(title);
        header.appendChild(removeBtn);

        const inputsContainer = document.createElement('div');
        inputsContainer.className = 'inputs-container';

        // Add initial keyword-value row
        const initialRow = createKeywordValueRow();
        inputsContainer.appendChild(initialRow);

        const addRowBtn = document.createElement('button');
        addRowBtn.type = 'button';
        addRowBtn.className = 'add-row-btn';
        addRowBtn.innerHTML = '<i class="material-icons-outlined">add</i> Add Another Row';
        addRowBtn.onclick = () => addRowToGroup(inputsContainer, wrapper);

        wrapper.appendChild(counter);
        wrapper.appendChild(header);
        wrapper.appendChild(inputsContainer);
        wrapper.appendChild(addRowBtn);

        return wrapper;
    }

    // Create keyword-value row
    function createKeywordValueRow() {
        const row = document.createElement('div');
        row.className = 'keyword-value-row';

        const keywordDiv = document.createElement('div');
        keywordDiv.className = 'keyword-input';

        const keywordLabel = document.createElement('label');
        keywordLabel.className = 'form-label';
        keywordLabel.textContent = 'Keyword';

        const keywordInput = document.createElement('input');
        keywordInput.type = 'text';
        keywordInput.className = 'form-control';
        keywordInput.name = `translates[${totalInputs}][keyword]`;
        keywordInput.placeholder = 'e.g., dimensions, materials, colors';
        keywordInput.required = true;

        keywordDiv.appendChild(keywordLabel);
        keywordDiv.appendChild(keywordInput);

        const valueDiv = document.createElement('div');
        valueDiv.className = 'value-input';

        const valueLabel = document.createElement('label');
        valueLabel.className = 'form-label';
        valueLabel.textContent = 'Value (JSON)';

        const valueInput = document.createElement('textarea');
        valueInput.className = 'form-control';
        valueInput.name = `translates[${totalInputs}][value]`;
        valueInput.rows = '3';
        valueInput.placeholder = '{"key": "value"} or ["item1", "item2"]';
        valueInput.required = true;

        valueDiv.appendChild(valueLabel);
        valueDiv.appendChild(valueInput);

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'remove-row-btn';
        removeBtn.innerHTML = '<i class="material-icons-outlined">remove</i>';
        removeBtn.onclick = () => removeRow(row);

        row.appendChild(keywordDiv);
        row.appendChild(valueDiv);
        row.appendChild(removeBtn);

        totalInputs++;
        return row;
    }

    // Add row to existing group
    function addRowToGroup(container, groupWrapper) {
        const newRow = createKeywordValueRow();
        container.appendChild(newRow);
        updateGroupCounter(groupWrapper);
    }

    // Remove row
    function removeRow(row) {
        if (row.parentElement.children.length > 1) {
            row.remove();
            totalInputs--;
            updateAllGroupCounters();
        } else {
            alert('At least one row must remain in each group.');
        }
    }

    // Remove input group
    function removeInputGroup(wrapper) {
        if (container.children.length > 1) {
            const rows = wrapper.querySelectorAll('.keyword-value-row');
            totalInputs -= rows.length;
            wrapper.remove();
            updateSubmitButton();
            updateAllGroupCounters();
            
            if (container.children.length === 0) {
                showEmptyState();
            }
        } else {
            alert('At least one input group must remain.');
        }
    }

    // Update group counter
    function updateGroupCounter(groupWrapper) {
        const counter = groupWrapper.querySelector('.input-counter');
        const rows = groupWrapper.querySelectorAll('.keyword-value-row');
        counter.textContent = rows.length;
    }

    // Update all group counters
    function updateAllGroupCounters() {
        document.querySelectorAll('.input-group-wrapper').forEach(group => {
            updateGroupCounter(group);
        });
    }

    // Update submit button state
    function updateSubmitButton() {
        if (totalInputs > 0) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('submit-btn-disabled');
        } else {
            submitBtn.disabled = true;
            submitBtn.classList.add('submit-btn-disabled');
        }
    }

    // Show empty state
    function showEmptyState() {
        if (container.children.length === 0) {
            const emptyState = document.createElement('div');
            emptyState.className = 'empty-state';
            emptyState.innerHTML = `
                <i class="material-icons-outlined">add_circle_outline</i>
                <p>No input fields added yet.</p>
                <p class="text-muted">Use the buttons above to add keyword-value pairs.</p>
            `;
            container.appendChild(emptyState);
        }
    }

    // Hide empty state
    function hideEmptyState() {
        const emptyState = container.querySelector('.empty-state');
        if (emptyState) {
            emptyState.remove();
        }
    }

    // Form validation
    form.addEventListener('submit', function(e) {
        if (totalInputs === 0) {
            e.preventDefault();
            alert('Please add at least one input field before submitting.');
            return false;
        }

        // Validate JSON format for value fields
        const valueInputs = document.querySelectorAll('textarea[name*="[value]"]');
        for (let input of valueInputs) {
            try {
                JSON.parse(input.value);
            } catch (error) {
                e.preventDefault();
                alert('Invalid JSON format in one or more value fields. Please check your input.');
                input.focus();
                return false;
            }
        }
    });

    // Show initial empty state
    showEmptyState();
});
</script>
@endpush

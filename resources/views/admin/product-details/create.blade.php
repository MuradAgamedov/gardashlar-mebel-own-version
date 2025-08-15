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
                                        <strong>Instructions:</strong> Use the buttons below to add input fields. First button adds 2 inputs, second button adds keyword with multiple keyvalue-value pairs.
                                    </div>
                                </div>
                            </div>

                            <!-- Dynamic Input Fields Container -->
                            <div id="dynamicInputsContainer">
                                <!-- Input fields will be added here -->
                            </div>

                            <!-- Action Buttons -->
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-success" id="addTwoInputs">
                                            <i class="material-icons-outlined">add</i> Add Two Inputs (Key + Value)
                                        </button>
                                        <button type="button" class="btn btn-primary" id="addThreeInputs">
                                            <i class="material-icons-outlined">add_circle</i> Add Keyword with KeyValue-Value Pairs
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
    justify-content-between;
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

.input-row {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
    align-items: flex-start;
}

.input-field {
    flex: 1;
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

.add-more-btn {
    background: #28a745;
    border: none;
    color: white;
    border-radius: 4px;
    padding: 8px 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 10px;
}

.add-more-btn:hover {
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

.keyword-container {
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 15px;
    margin-bottom: 15px;
    background: white;
}

.keyword-header {
    display: flex;
    justify-content-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 1px solid #e9ecef;
}

.keyword-title {
    font-weight: 500;
    color: #495057;
    margin: 0;
    font-size: 14px;
}

.remove-keyword {
    background: #dc3545;
    border: none;
    color: white;
    border-radius: 4px;
    padding: 4px 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 12px;
}

.remove-keyword:hover {
    background: #c82333;
}

.keyvalue-value-row {
    display: flex;
    gap: 15px;
    margin-bottom: 10px;
    align-items: flex-start;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 4px;
}

.keyvalue-field {
    flex: 1;
}

.value-field {
    flex: 1;
}

.remove-keyvalue-value {
    background: #6c757d;
    border: none;
    color: white;
    border-radius: 4px;
    padding: 6px 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 5px;
    font-size: 12px;
}

.remove-keyvalue-value:hover {
    background: #5a6268;
}

.add-keyvalue-value-btn {
    background: #17a2b8;
    border: none;
    color: white;
    border-radius: 4px;
    padding: 6px 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 10px;
    font-size: 12px;
}

.add-keyvalue-value-btn:hover {
    background: #138496;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let inputGroupCounter = 0;
    let totalInputs = 0;
    
    const container = document.getElementById('dynamicInputsContainer');
    const addTwoBtn = document.getElementById('addTwoInputs');
    const addThreeBtn = document.getElementById('addThreeInputs');
    const clearAllBtn = document.getElementById('clearAllInputs');
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('productDetailForm');

    // Add two inputs (Key + Value)
    addTwoBtn.addEventListener('click', function() {
        addInputGroup(2);
    });

    // Add keyword with keyvalue-value pairs
    addThreeBtn.addEventListener('click', function() {
        addInputGroup(3);
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

    // Add input group
    function addInputGroup(inputCount) {
        inputGroupCounter++;
        const inputGroup = createInputGroup(inputGroupCounter, inputCount);
        container.appendChild(inputGroup);
        updateSubmitButton();
        hideEmptyState();
    }

    // Create input group
    function createInputGroup(groupId, inputCount) {
        const wrapper = document.createElement('div');
        wrapper.className = 'input-group-wrapper';
        wrapper.dataset.groupId = groupId;

        const counter = document.createElement('div');
        counter.className = 'input-counter';
        counter.textContent = inputCount;

        const header = document.createElement('div');
        header.className = 'input-group-header';

        const title = document.createElement('h6');
        title.className = 'input-group-title';
        title.textContent = `Input Group ${groupId} (${inputCount} inputs)`;

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'remove-input-group';
        removeBtn.innerHTML = '<i class="material-icons-outlined">close</i>';
        removeBtn.onclick = () => removeInputGroup(wrapper);

        header.appendChild(title);
        header.appendChild(removeBtn);

        const inputsContainer = document.createElement('div');
        inputsContainer.className = 'inputs-container';

        // Add inputs based on count
        if (inputCount === 2) {
            // Key + Value
            const keyRow = createInputRow('key', 'Key', 'Enter key name');
            const valueRow = createInputRow('value', 'Value', 'Enter value');
            inputsContainer.appendChild(keyRow);
            inputsContainer.appendChild(valueRow);
        } else if (inputCount === 3) {
            // Keyword with multiple keyvalue-value pairs
            const keywordContainer = createKeywordContainer();
            inputsContainer.appendChild(keywordContainer);
        }

        wrapper.appendChild(counter);
        wrapper.appendChild(header);
        wrapper.appendChild(inputsContainer);

        return wrapper;
    }

    // Create keyword container
    function createKeywordContainer() {
        const keywordDiv = document.createElement('div');
        keywordDiv.className = 'keyword-container';

        const header = document.createElement('div');
        header.className = 'keyword-header';

        const title = document.createElement('h6');
        title.className = 'keyword-title';
        title.textContent = 'Keyword Group';

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'remove-keyword';
        removeBtn.textContent = 'Remove Keyword';
        removeBtn.onclick = () => removeKeywordContainer(keywordDiv);

        header.appendChild(title);
        header.appendChild(removeBtn);

        // Keyword input
        const keywordRow = document.createElement('div');
        keywordRow.className = 'input-row';
        const keywordField = document.createElement('div');
        keywordField.className = 'input-field';
        const keywordLabel = document.createElement('label');
        keywordLabel.className = 'form-label';
        keywordLabel.textContent = 'Keyword';
        const keywordInput = document.createElement('input');
        keywordInput.type = 'text';
        keywordInput.className = 'form-control';
        keywordInput.name = `translates[${totalInputs}][keyword]`;
        keywordInput.placeholder = 'Enter keyword (e.g., specifications)';
        keywordInput.required = true;
        keywordField.appendChild(keywordLabel);
        keywordField.appendChild(keywordInput);
        keywordRow.appendChild(keywordField);

        // KeyValue-Value pairs container
        const pairsContainer = document.createElement('div');
        pairsContainer.className = 'pairs-container';

        // Add initial keyvalue-value pair
        const initialPair = createKeyValuePair();
        pairsContainer.appendChild(initialPair);

        // Add more pairs button
        const addMorePairsBtn = document.createElement('button');
        addMorePairsBtn.type = 'button';
        addMorePairsBtn.className = 'add-keyvalue-value-btn';
        addMorePairsBtn.innerHTML = '<i class="material-icons-outlined">add</i> Add More KeyValue-Value Pairs';
        addMorePairsBtn.onclick = () => addKeyValuePair(pairsContainer, keywordDiv);

        keywordDiv.appendChild(header);
        keywordDiv.appendChild(keywordRow);
        keywordDiv.appendChild(pairsContainer);
        keywordDiv.appendChild(addMorePairsBtn);

        totalInputs++;
        return keywordDiv;
    }

    // Create keyvalue-value pair
    function createKeyValuePair() {
        const pairDiv = document.createElement('div');
        pairDiv.className = 'keyvalue-value-row';

        // KeyValue input
        const keyvalueDiv = document.createElement('div');
        keyvalueDiv.className = 'keyvalue-field';
        const keyvalueLabel = document.createElement('label');
        keyvalueLabel.className = 'form-label';
        keyvalueLabel.textContent = 'KeyValue';
        const keyvalueInput = document.createElement('input');
        keyvalueInput.type = 'text';
        keyvalueInput.className = 'form-control';
        keyvalueInput.name = `translates[${totalInputs}][keyvalue]`;
        keyvalueInput.placeholder = 'Enter keyvalue (e.g., size, color)';
        keyvalueInput.required = true;
        keyvalueDiv.appendChild(keyvalueLabel);
        keyvalueDiv.appendChild(keyvalueInput);

        // Value input
        const valueDiv = document.createElement('div');
        valueDiv.className = 'value-field';
        const valueLabel = document.createElement('label');
        valueLabel.className = 'form-label';
        valueLabel.textContent = 'Value';
        const valueInput = document.createElement('input');
        valueInput.type = 'text';
        valueInput.className = 'form-control';
        valueInput.name = `translates[${totalInputs}][value]`;
        valueInput.placeholder = 'Enter value (e.g., large, white)';
        valueInput.required = true;
        valueDiv.appendChild(valueLabel);
        valueDiv.appendChild(valueInput);

        // Remove button
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'remove-keyvalue-value';
        removeBtn.textContent = 'Remove';
        removeBtn.onclick = () => removeKeyValuePair(pairDiv);

        pairDiv.appendChild(keyvalueDiv);
        pairDiv.appendChild(valueDiv);
        pairDiv.appendChild(removeBtn);

        totalInputs++;
        return pairDiv;
    }

    // Add more keyvalue-value pairs
    function addKeyValuePair(container, keywordContainer) {
        const newPair = createKeyValuePair();
        container.appendChild(newPair);
        updateGroupCounter(keywordContainer.closest('.input-group-wrapper'));
    }

    // Remove keyvalue-value pair
    function removeKeyValuePair(pairDiv) {
        if (pairDiv.parentElement.children.length > 1) {
            pairDiv.remove();
            totalInputs--;
            updateAllGroupCounters();
        } else {
            alert('At least one KeyValue-Value pair must remain.');
        }
    }

    // Remove keyword container
    function removeKeywordContainer(keywordDiv) {
        const pairs = keywordDiv.querySelectorAll('.keyvalue-value-row');
        totalInputs -= (pairs.length + 1); // +1 for keyword input
        keywordDiv.remove();
        updateAllGroupCounters();
    }

    // Create input row
    function createInputRow(fieldName, labelText, placeholder) {
        const row = document.createElement('div');
        row.className = 'input-row';

        const inputDiv = document.createElement('div');
        inputDiv.className = 'input-field';

        const label = document.createElement('label');
        label.className = 'form-label';
        label.textContent = labelText;

        const input = document.createElement('input');
        input.type = 'text';
        input.className = 'form-control';
        input.name = `translates[${totalInputs}][${fieldName}]`;
        input.placeholder = placeholder;
        input.required = true;

        inputDiv.appendChild(label);
        inputDiv.appendChild(input);

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'remove-row-btn';
        removeBtn.innerHTML = '<i class="material-icons-outlined">remove</i>';
        removeBtn.onclick = () => removeRow(row);

        row.appendChild(inputDiv);
        row.appendChild(removeBtn);

        totalInputs++;
        return row;
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
            const rows = wrapper.querySelectorAll('.input-row, .keyword-container');
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

    // Update all group counters
    function updateAllGroupCounters() {
        document.querySelectorAll('.input-group-wrapper').forEach(group => {
            const counter = group.querySelector('.input-counter');
            const rows = group.querySelectorAll('.input-row, .keyword-container');
            counter.textContent = rows.length;
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
                <p class="text-muted">Use the buttons above to add input fields.</p>
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
    });

    // Show initial empty state
    showEmptyState();
});
</script>
@endpush

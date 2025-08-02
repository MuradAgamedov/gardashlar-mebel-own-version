@extends('admin.layout.app')

@section('title', 'Currencies')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Currencies</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Currencies</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Currencies</h5>
                            <div class="d-flex gap-2">
                                <button type="button" id="toggleStatusBtn" class="btn btn-warning" style="display: none;">
                                    <i class="material-icons-outlined">swap_horiz</i>
                                    Toggle Status
                                </button>
                                <button type="button" id="deleteSelectedBtn" class="btn btn-danger" style="display: none;">
                                    <i class="material-icons-outlined">delete</i>
                                </button>
                                <a href="{{ route('admin.currencies.create') }}" class="btn btn-primary">
                                    <i class="material-icons-outlined">add</i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search Section -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="material-icons-outlined">search</i>
                                    </span>
                                    <input type="text" 
                                           id="searchInput" 
                                           class="form-control" 
                                           placeholder="Search by currency code...">
                                </div>
                            </div>
                            <div class="col-md-6 text-end">
                                <button type="button" id="selectAllBtn" class="btn btn-outline-secondary btn-sm">
                                    <i class="material-icons-outlined">select_all</i>
                                    Select All
                                </button>
                                <button type="button" id="deselectAllBtn" class="btn btn-outline-secondary btn-sm" style="display: none;">
                                    <i class="material-icons-outlined">deselect</i>
                                    Deselect All
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">
                                            <input type="checkbox" id="selectAllCheckbox" class="form-check-input">
                                        </th>
                                        <th style="width: 5%;">Order</th>
                                        <th style="width: 20%;">Code</th>
                                        <th style="width: 25%;">Course</th>
                                        <th style="width: 15%;">Status</th>
                                        <th style="width: 20%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable">
                                    @forelse($currencies as $currency)
                                        <tr data-id="{{ $currency->id }}" class="sortable-item currency-row">
                                            <td>
                                                <input type="checkbox" class="form-check-input currency-checkbox" value="{{ $currency->id }}">
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="material-icons-outlined text-muted me-2" style="cursor: move;">drag_handle</i>
                                                    <span class="badge bg-secondary">{{ $currency->order }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-primary fs-6">{{ $currency->code }}</span>
                                                    <small class="text-muted ms-2">ID: {{ $currency->id }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <strong class="text-success">{{ number_format($currency->course, 4) }}</strong>
                                                <br>
                                                <small class="text-muted">Exchange Rate</small>
                                            </td>
                                            <td>
                                                <form action="{{ route('admin.currencies.toggle-status', $currency->id) }}" 
                                                      method="POST" 
                                                      style="display: inline;">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="btn btn-sm {{ $currency->status ? 'btn-success' : 'btn-secondary' }}"
                                                            title="{{ $currency->status ? 'Active' : 'Inactive' }}"
                                                            onclick="return confirm('Are you sure you want to {{ $currency->status ? 'deactivate' : 'activate' }} this currency?')">
                                                        <i class="material-icons-outlined">{{ $currency->status ? 'check_circle' : 'cancel' }}</i>
                                     
                                                    </button>
                                                </form>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('admin.currencies.edit', $currency->id) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="Edit">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    <form action="{{ route('admin.currencies.destroy', $currency->id) }}" 
                                                          method="POST" 
                                                          onsubmit="return confirm('Are you sure you want to delete this currency?')"
                                                          style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-danger" 
                                                                title="Delete">
                                                            <i class="material-icons-outlined">delete</i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <i class="material-icons-outlined" style="font-size: 48px; color: #6c757d;">attach_money</i>
                                                <h5 class="text-muted mt-3">No Currencies</h5>
                                                <p class="text-muted">Start by adding currencies.</p>
                                                <a href="{{ route('admin.currencies.create') }}" class="btn btn-primary">
                                                    <i class="material-icons-outlined">add</i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sortable functionality
    const sortable = document.getElementById('sortable');
    if (sortable) {
        new Sortable(sortable, {
            animation: 150,
            handle: '.sortable-item',
            onEnd: function(evt) {
                const items = Array.from(sortable.querySelectorAll('.sortable-item')).map((item, index) => ({
                    id: item.dataset.id,
                    order: index
                }));

                fetch('{{ route("admin.currencies.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ orders: items.map(item => item.id) })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });
    }

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const currencyRows = document.querySelectorAll('.currency-row');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        currencyRows.forEach(row => {
            const code = row.querySelector('.badge.bg-primary').textContent.toLowerCase();
            if (code.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Select All functionality
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const currencyCheckboxes = document.querySelectorAll('.currency-checkbox');
    const selectAllBtn = document.getElementById('selectAllBtn');
    const deselectAllBtn = document.getElementById('deselectAllBtn');
    const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
    const toggleStatusBtn = document.getElementById('toggleStatusBtn');

    function updateActionButtons() {
        const checkedBoxes = document.querySelectorAll('.currency-checkbox:checked');
        if (checkedBoxes.length > 0) {
            deleteSelectedBtn.style.display = 'inline-block';
            toggleStatusBtn.style.display = 'inline-block';
        } else {
            deleteSelectedBtn.style.display = 'none';
            toggleStatusBtn.style.display = 'none';
        }
    }

    function updateSelectAllState() {
        const visibleCheckboxes = Array.from(currencyCheckboxes).filter(cb => 
            cb.closest('.currency-row').style.display !== 'none'
        );
        const checkedVisibleCheckboxes = visibleCheckboxes.filter(cb => cb.checked);
        
        if (checkedVisibleCheckboxes.length === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (checkedVisibleCheckboxes.length === visibleCheckboxes.length) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
        }
    }

    // Individual checkbox change
    currencyCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateActionButtons();
            updateSelectAllState();
        });
    });

    // Select All checkbox
    selectAllCheckbox.addEventListener('change', function() {
        const visibleCheckboxes = Array.from(currencyCheckboxes).filter(cb => 
            cb.closest('.currency-row').style.display !== 'none'
        );
        
        visibleCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        
        updateActionButtons();
        updateSelectAllState();
    });

    // Select All button
    selectAllBtn.addEventListener('click', function() {
        const visibleCheckboxes = Array.from(currencyCheckboxes).filter(cb => 
            cb.closest('.currency-row').style.display !== 'none'
        );
        
        visibleCheckboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
        
        selectAllCheckbox.checked = true;
        selectAllCheckbox.indeterminate = false;
        updateActionButtons();
        updateSelectAllState();
    });

    // Deselect All button
    deselectAllBtn.addEventListener('click', function() {
        currencyCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
        updateActionButtons();
        updateSelectAllState();
    });

    // Toggle Status functionality
    toggleStatusBtn.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.currency-checkbox:checked');
        const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);
        
        if (selectedIds.length === 0) {
            alert('Please select currencies to toggle status.');
            return;
        }

        if (!confirm(`Are you sure you want to toggle status for ${selectedIds.length} selected currency(ies)?`)) {
            return;
        }

        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.currencies.bulk-toggle-status") }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);

        selectedIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    });

    // Delete Selected functionality
    deleteSelectedBtn.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.currency-checkbox:checked');
        const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);
        
        if (selectedIds.length === 0) {
            alert('Please select currencies to delete.');
            return;
        }

        if (!confirm(`Are you sure you want to delete ${selectedIds.length} selected currency(ies)?`)) {
            return;
        }

        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.currencies.bulk-delete") }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);

        selectedIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    });

    // Show/hide deselect button based on selections
    function updateDeselectButton() {
        const checkedBoxes = document.querySelectorAll('.currency-checkbox:checked');
        if (checkedBoxes.length > 0) {
            deselectAllBtn.style.display = 'inline-block';
        } else {
            deselectAllBtn.style.display = 'none';
        }
    }

    // Update deselect button on checkbox changes
    currencyCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateDeselectButton);
    });
});
</script>
@endsection 
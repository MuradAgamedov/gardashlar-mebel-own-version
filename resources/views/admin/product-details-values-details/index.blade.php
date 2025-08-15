@extends('admin.layout.app')

@section('title', 'Details - ' . ($detailValue->translate()->keyword ?? 'Detail Value'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Details</h3>
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
                        <li class="breadcrumb-item active">Details</li>
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
                            <h5>Details for: <strong>{{ $detailValue->translate()->keyword ?? 'Detail Value' }}</strong></h5>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.products.detail-values.details.create', [$product, $detailValue]) }}" class="btn btn-primary">
                                    <i class="material-icons-outlined">add</i> Add Detail
                                </a>
                                <a href="{{ route('admin.products.detail-values.index', $product) }}" class="btn btn-outline-secondary">
                                    <i class="material-icons-outlined">arrow_back</i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        @if($details->count() > 0)
                            <!-- Results Summary -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="text-muted">
                                    <i class="material-icons-outlined">list</i>
                                    Total: {{ $details->count() }} details
                                </div>
                                <div class="d-flex gap-2">
                                    <button id="delete-selected-details" class="btn btn-danger btn-sm" disabled>
                                        <i class="material-icons-outlined">delete</i> Delete Selected
                                    </button>
                                    <input type="checkbox" id="select-all-details" class="form-check-input">
                                    <label for="select-all-details" class="form-check-label">Select All</label>
                                </div>
                            </div>
                            
                            <!-- Details Table -->
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="30">
                                                <input type="checkbox" id="select-all-details">
                                            </th>
                                            <th width="50">#</th>
                                            <th width="80">Order</th>
                                            <th width="100">Language</th>
                                            <th>Keyword</th>
                                            <th>Value</th>
                                            <th width="150">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sortable-details">
                                        @foreach($details as $index => $detail)
                                            @foreach($detail->translates as $translate)
                                                <tr data-id="{{ $detail->id }}" data-detail-id="{{ $detail->id }}">
                                                    <td>
                                                        <input type="checkbox" class="detail-checkbox" value="{{ $detail->id }}">
                                                    </td>
                                                    <td>
                                                        <div class="drag-handle" style="cursor: move;">
                                                            <i class="material-icons-outlined text-muted">drag_indicator</i>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-primary">{{ $detail->order }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-light-primary">
                                                            {{ $translate->language->lang_code ?? 'N/A' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <strong class="text-primary">{{ $translate->keyword }}</strong>
                                                    </td>
                                                    <td>
                                                        <div class="text-truncate" style="max-width: 300px;" title="{{ $translate->value }}">
                                                            {{ $translate->value }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <a href="{{ route('admin.products.detail-values.details.edit', [$product, $detailValue, $detail]) }}" 
                                                               class="btn btn-sm btn-outline-primary" 
                                                               title="Edit">
                                                                <i class="material-icons-outlined">edit</i>
                                                            </a>
                                                            
                                                            <form action="{{ route('admin.products.detail-values.details.destroy', [$product, $detailValue, $detail]) }}" 
                                                                  method="POST" 
                                                                  class="d-inline"
                                                                  onsubmit="return confirm('Are you sure you want to delete this detail?')">
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
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="text-muted">
                                    <i class="material-icons-outlined" style="font-size: 64px; color: #e0e0e0;">list</i>
                                    <h5 class="mt-3">No details found</h5>
                                    <p class="text-muted">Start by adding your first detail</p>
                                    <a href="{{ route('admin.products.detail-values.details.create', [$product, $detailValue]) }}" class="btn btn-primary">
                                        <i class="material-icons-outlined">add</i> Add First Detail
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sortableList = document.getElementById('sortable-details');
    
    if (sortableList) {
        const sortable = new Sortable(sortableList, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const items = Array.from(sortableList.children).map((tr, index) => {
                    return tr.getAttribute('data-detail-id');
                }).filter(id => id !== null);

                // Remove duplicates (same detail might have multiple rows for different languages)
                const uniqueItems = [...new Set(items)];

                // Send reorder request
                fetch('{{ route("admin.products.detail-values.details.reorder", [$product, $detailValue]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ items: uniqueItems })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        const alert = document.createElement('div');
                        alert.className = 'alert alert-success alert-dismissible fade show';
                        alert.innerHTML = `
                            <i class="material-icons-outlined">check_circle</i>
                            Order updated successfully
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        `;
                        document.querySelector('.container-fluid').insertBefore(alert, document.querySelector('.row'));
                        
                        // Auto remove after 3 seconds
                        setTimeout(() => {
                            alert.remove();
                        }, 3000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });
        sortableList._sortable = sortable;
    }

    // Select all functionality
    const selectAll = document.getElementById('select-all-details');
    const checkboxes = document.querySelectorAll('.detail-checkbox');
    const deleteBtn = document.getElementById('delete-selected-details');

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            toggleDeleteButton();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', toggleDeleteButton);
    });

    function toggleDeleteButton() {
        let anyChecked = Array.from(checkboxes).filter(cb => cb.checked).length > 0;
        deleteBtn.disabled = !anyChecked;
    }

    deleteBtn.addEventListener('click', function() {
        let ids = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
        if (ids.length === 0) return;
        if (!confirm('Are you sure you want to delete the selected details?')) return;
        
        fetch("{{ route('admin.products.detail-values.details.bulk-delete', [$product, $detailValue]) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ids })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error occurred while deleting');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error occurred while deleting');
        });
    });
});
</script>

<style>
.drag-handle:hover {
    color: #007bff !important;
}

.badge {
    font-size: 0.75rem;
}

.table td {
    vertical-align: middle;
}

.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>
@endpush

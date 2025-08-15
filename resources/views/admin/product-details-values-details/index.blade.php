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
                                    <i class="material-icons-outlined">add</i>
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
                                    Total: {{ $details->count() }} details
                                </div>
                                <button id="delete-selected-details" class="btn btn-danger btn-sm" disabled>
                                    <i class="material-icons-outlined">delete</i>
                                </button>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="30">
                                                <input type="checkbox" id="select-all-details">
                                            </th>
                                            <th width="50">#</th>
                                            <th>Keywords & Values</th>
                                            <th width="150">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sortable-details">
                                        @foreach($details as $detail)
                                            <tr data-id="{{ $detail->id }}">
                                                <td>
                                                    <input type="checkbox" class="detail-checkbox" value="{{ $detail->id }}">
                                                </td>
                                                <td>
                                                    <div class="drag-handle">
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <circle cx="8" cy="8" r="1"></circle>
                                                            <circle cx="16" cy="8" r="1"></circle>
                                                            <circle cx="8" cy="16" r="1"></circle>
                                                            <circle cx="16" cy="16" r="1"></circle>
                                                        </svg>
                                                    </div>
                                                </td>
                                                <td>
                                                    @foreach($detail->translates as $translate)
                                                        <div class="mb-2">
                                                            <span class="badge badge-light-primary me-2">
                                                                {{ $translate->language->lang_code ?? 'N/A' }}
                                                            </span>
                                                            <span class="fw-medium">{{ $translate->keyword }}</span>
                                                            <span class="text-muted ms-2">: {{ $translate->value }}</span>
                                                        </div>
                                                    @endforeach
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
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="text-muted">
                                    <i class="material-icons-outlined" style="font-size: 48px;">list</i>
                                    <p class="mt-2">No details found</p>
                                    <a href="{{ route('admin.products.detail-values.details.create', [$product, $detailValue]) }}" class="btn btn-primary">
                                        <i class="material-icons-outlined">add</i>
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
                    return tr.getAttribute('data-id');
                }).filter(id => id !== null);

                // Send reorder request
                fetch('{{ route("admin.products.detail-values.details.reorder", [$product, $detailValue]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ items: items })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        const alert = document.createElement('div');
                        alert.className = 'alert alert-success alert-dismissible fade show';
                        alert.innerHTML = `
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
        let anyChecked = Array.from(checkboxes).some(cb => cb.checked);
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
@endpush

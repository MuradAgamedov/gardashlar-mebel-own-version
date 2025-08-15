@extends('admin.layout.app')

@section('title', 'Product Detail Values - ' . ($product->translate()->title ?? 'Product'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Product Detail Values</h3>
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
                        <li class="breadcrumb-item active">Detail Values</li>
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
                            <h5>Detail Values for: <strong>{{ $product->translate()->title ?? 'Product' }}</strong></h5>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.products.detail-values.create', $product) }}" class="btn btn-primary">
                                    <i class="material-icons-outlined">add</i>
                                    Add New Detail Value
                                </a>
                                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                                    <i class="material-icons-outlined">arrow_back</i>
                                    Back to Products
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        @if($detailValues->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th width="50">#</th>
                                            <th width="100">Order</th>
                                            <th>Keyword</th>
                                            <th>Value</th>
                                            <th>Languages</th>
                                            <th width="150">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sortableTable">
                                        @foreach($detailValues as $detailValue)
                                        <tr data-id="{{ $detailValue->id }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $detailValue->order }}</span>
                                            </td>
                                            <td>
                                                @if($detailValue->translates->count() > 0)
                                                    <strong>{{ $detailValue->translates->first()->keyword }}</strong>
                                                @else
                                                    <span class="text-muted">No keyword</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($detailValue->translates->count() > 0)
                                                    {{ Str::limit($detailValue->translates->first()->value, 50) }}
                                                @else
                                                    <span class="text-muted">No value</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    @foreach($detailValue->translates as $translate)
                                                        <span class="badge bg-info" title="{{ $translate->language->name ?? $translate->language->lang_name ?? 'Unknown' }}">
                                                            {{ $translate->language->lang_code ?? 'N/A' }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('admin.products.detail-values.edit', [$product, $detailValue]) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="Edit">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    
                                                    <a href="#" 
                                                       class="btn btn-sm btn-outline-info" 
                                                       title="View Values"
                                                       onclick="viewDetailValues({{ $detailValue->id }})">
                                                        <i class="material-icons-outlined">visibility</i>
                                                    </a>
                                                    
                                                    <form action="{{ route('admin.products.detail-values.destroy', [$product, $detailValue]) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this detail value?')">
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
                            <div class="text-center py-5">
                                <i class="material-icons-outlined text-muted" style="font-size: 4rem;">inbox</i>
                                <h5 class="text-muted mt-3">No Detail Values Found</h5>
                                <p class="text-muted">This product doesn't have any detail values yet.</p>
                                <a href="{{ route('admin.products.detail-values.create', $product) }}" class="btn btn-primary">
                                    <i class="material-icons-outlined">add</i>
                                    Add First Detail Value
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Values Modal -->
<div class="modal fade" id="detailValuesModal" tabindex="-1" aria-labelledby="detailValuesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailValuesModalLabel">Detail Values</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailValuesModalBody">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function viewDetailValues(detailValueId) {
    // This function will be implemented later to show detailed values
    alert('View detail values functionality will be implemented here for ID: ' + detailValueId);
}

// Enable drag and drop reordering
$(document).ready(function() {
    $("#sortableTable").sortable({
        items: "tr",
        cursor: "move",
        opacity: 0.6,
        update: function() {
            const items = [];
            $("#sortableTable tr").each(function(index) {
                items.push($(this).data('id'));
            });
            
            // Send reorder request
            fetch('{{ route("admin.products.detail-values.reorder", $product) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ items: items })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload page to show new order
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error reordering items');
            });
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.table th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}

.badge {
    font-size: 0.75rem;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
}

.material-icons-outlined {
    font-size: 1rem;
}

#sortableTable tr {
    cursor: move;
}

#sortableTable tr:hover {
    background-color: #f8f9fa;
}
</style>
@endpush

@extends('admin.layout.app')

@section('title', 'Product Accordions')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Product Accordions</h3>
                    <p class="text-muted">{{ $product->translate()->title ?? 'No title' }}</p>
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
                        <li class="breadcrumb-item active">Accordions</li>
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
                            <h5>Accordions for: {{ $product->translate()->title ?? 'No title' }}</h5>
                            <a href="{{ route('admin.products.accordions.create', $product) }}" class="btn btn-primary">
                                <i class="material-icons-outlined">add</i>
                                Add Accordion
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">#</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Order</th>
                                        <th width="150">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable-accordions">
                                    @forelse($accordions as $accordion)
                                        <tr data-id="{{ $accordion->id }}">
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
                                                <div class="d-flex align-items-center">
                                                    <i class="material-icons-outlined text-muted me-2" style="font-size: 16px;">expand_more</i>
                                                    <div>
                                                        @php
                                                            $firstTranslation = $accordion->translates->first();
                                                        @endphp
                                                        <strong>{{ $firstTranslation ? $firstTranslation->title : 'No title' }}</strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($firstTranslation && $firstTranslation->description)
                                                    <div class="text-truncate" style="max-width: 300px;" title="{{ strip_tags($firstTranslation->description) }}">
                                                        {!! Str::limit(strip_tags($firstTranslation->description), 100) !!}
                                                    </div>
                                                @else
                                                    <span class="text-muted">No description</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($accordion->status)
                                                    <span class="badge badge-light-success">Active</span>
                                                @else
                                                    <span class="badge badge-light-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-light-info">
                                                    <i class="material-icons-outlined" style="font-size: 12px;">sort</i>
                                                    {{ $accordion->order }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.products.accordions.edit', [$product, $accordion]) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="Edit">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    <form action="{{ route('admin.products.accordions.destroy', [$product, $accordion]) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this accordion?')">
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
                                            <td colspan="6" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="material-icons-outlined" style="font-size: 48px;">expand_more</i>
                                                    <p class="mt-2">No accordions added yet</p>
                                                    <a href="{{ route('admin.products.accordions.create', $product) }}" class="btn btn-primary">
                                                        Add First Accordion
                                                    </a>
                                                </div>
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
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sortableList = document.getElementById('sortable-accordions');
    
    if (sortableList) {
        new Sortable(sortableList, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const items = Array.from(sortableList.children).map((tr, index) => ({
                    id: tr.dataset.id,
                    order: index + 1
                }));
                
                fetch('{{ route("admin.products.accordions.reorder", $product) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ items: items.map(item => item.id) })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update order numbers in the table
                        items.forEach((item, index) => {
                            const tr = sortableList.querySelector(`[data-id="${item.id}"]`);
                            const orderCell = tr.querySelector('td:nth-child(5) .badge');
                            if (orderCell) {
                                orderCell.innerHTML = `<i class="material-icons-outlined" style="font-size: 12px;">sort</i> ${item.order}`;
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error reordering accordions:', error);
                });
            }
        });
    }
});
</script>
@endpush 
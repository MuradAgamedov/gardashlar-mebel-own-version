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
                            <a href="{{ route('admin.currencies.create') }}" class="btn btn-primary">
                                <i class="material-icons-outlined">add</i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">Order</th>
                                        <th style="width: 20%;">Code</th>
                                        <th style="width: 30%;">Course</th>
                                        <th style="width: 15%;">Status</th>
                                        <th style="width: 30%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable">
                                    @forelse($currencies as $currency)
                                        <tr data-id="{{ $currency->id }}" class="sortable-item">
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
                                                            title="{{ $currency->status ? 'Active' : 'Inactive' }}">
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
                                            <td colspan="5" class="text-center py-5">
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
});
</script>
@endsection 
@extends('admin.layout.app')

@section('title', 'Services')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Services</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Services</li>
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
                            <h5>All Services</h5>
                            <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
                                <i class="material-icons-outlined">add</i>
                                Add New Service
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Status</th>
                                        <th>Title</th>
                                        <th>Card Description</th>
                                        <th>Languages</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable-services">
                                    @forelse($services as $service)
                                        <tr data-id="{{ $service->id }}">
                                            <td>
                                                <i class="fas fa-grip-vertical drag-handle" style="cursor: move;"></i>
                                                {{ $service->order }}
                                            </td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input status-toggle" type="checkbox" 
                                                           data-service-id="{{ $service->id }}"
                                                           {{ $service->status ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $mainTranslate = $service->translate();
                                                @endphp
                                                {{ $mainTranslate ? $mainTranslate->title : 'No title' }}
                                            </td>
                                            <td>
                                                @if($mainTranslate)
                                                    {{ Str::limit($mainTranslate->card_description, 100) }}
                                                @else
                                                    No description
                                                @endif
                                            </td>
                                            <td>
                                                @foreach($service->translates as $translate)
                                                    <span class="badge bg-primary me-1">{{ $translate->language->title }}</span>
                                                @endforeach
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.service-advantages.index', $service->id) }}" 
                                                       class="btn btn-sm btn-info">
                                                        Advantages
                                                    </a>
                                                    <a href="{{ route('admin.services.edit', $service->id) }}" 
                                                       class="btn btn-sm btn-warning">
                                                        Edit
                                                    </a>
                                                    <form action="{{ route('admin.services.destroy', $service->id) }}" 
                                                          method="POST" class="d-inline" 
                                                          onsubmit="return confirm('Are you sure you want to delete this service?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No services found.</td>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.getElementById('sortable-services');
    
    if (tbody) {
        new Sortable(tbody, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const rows = Array.from(tbody.querySelectorAll('tr[data-id]'));
                const services = rows.map((row, index) => ({
                    id: row.getAttribute('data-id'),
                    order: index + 1
                }));
                
                fetch('{{ route("admin.services.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ services: services })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update order numbers in the table
                        rows.forEach((row, index) => {
                            const orderCell = row.querySelector('td:first-child');
                            const orderText = orderCell.textContent.replace(/\d+$/, (index + 1).toString());
                            orderCell.innerHTML = orderCell.innerHTML.replace(/\d+$/, (index + 1).toString());
                        });
                    }
                })
                .catch(error => {
                    console.error('Error updating order:', error);
                });
            }
        });
    }

    // Status toggle functionality
    document.querySelectorAll('.status-toggle').forEach(function(toggle) {
        toggle.addEventListener('change', function() {
            const serviceId = this.getAttribute('data-service-id');
            const status = this.checked;
            
            fetch(`/admin/services/${serviceId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: status })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    // Revert the toggle if failed
                    this.checked = !status;
                    alert('Error updating status: ' + data.message);
                }
            })
            .catch(error => {
                // Revert the toggle if failed
                this.checked = !status;
                console.error('Error updating status:', error);
                alert('Error updating status');
            });
        });
    });
});
</script>
@endpush
@endsection 
@extends('admin.layout.app')

@section('title', 'Home Page Project Categories')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Home Page Project Categories</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Home Page Project Categories</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Selected Project Categories for Home Page</h5>
                            <a href="{{ route('admin.home-page-project-categories.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> 
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

                        @if($homePageProjectCategories->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="sortable-table">
                                    <thead>
                                        <tr>
                                            <th width="50">Order</th>
                                            <th width="80">Image</th>
                                            <th>Category Name</th>
                                            <th width="100">Status</th>
                                            <th width="150">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($homePageProjectCategories as $item)
                                            <tr data-id="{{ $item->id }}">
                                                <td class="text-center">
                                                    <i class="fas fa-grip-vertical handle" style="cursor: move; color: #6c757d;"></i>
                                                    <span class="ms-2">{{ $item->order }}</span>
                                                </td>
                                                <td>
                                                    @if($item->projectCategory->image_url)
                                                        <img src="{{ $item->projectCategory->image_url }}" 
                                                             class="img-fluid rounded" 
                                                             style="max-height: 50px; object-fit: cover;" 
                                                             alt="Category Image">
                                                    @else
                                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                             style="width: 50px; height: 50px;">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <strong>{{ $item->projectCategory->translate()?->title ?? 'No Title' }}</strong>
                                                    @if($item->projectCategory->translate()?->description)
                                                        <br><small class="text-muted">{{ Str::limit($item->projectCategory->translate()?->description, 100) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($item->status)
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.home-page-project-categories.edit', $item->id) }}" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('admin.home-page-project-categories.destroy', $item->id) }}" 
                                                              method="POST" 
                                                              onsubmit="return confirm('Are you sure you want to remove this project category from home page?')"
                                                              style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                <i class="fas fa-trash"></i>
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
                                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No Project Categories Selected</h5>
                                <p class="text-muted">Start by adding project categories to display on the home page.</p>
                                <a href="{{ route('admin.home-page-project-categories.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> 
                                </a>
                            </div>
                        @endif
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
    const tbody = document.querySelector('#sortable-table tbody');
    if (tbody) {
        new Sortable(tbody, {
            handle: '.handle',
            animation: 150,
            onEnd: function(evt) {
                const items = [];
                const rows = tbody.querySelectorAll('tr');
                
                rows.forEach((row, index) => {
                    const id = row.getAttribute('data-id');
                    items.push({
                        id: id,
                        order: index + 1
                    });
                });

                // Update order in database
                fetch('{{ route("admin.home-page-project-categories.reorder") }}', {
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
                        // Update order numbers in the table
                        rows.forEach((row, index) => {
                            const orderSpan = row.querySelector('td:first-child span');
                            if (orderSpan) {
                                orderSpan.textContent = index + 1;
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error updating order:', error);
                    location.reload(); // Reload page if there's an error
                });
            }
        });
    }
});
</script>
@endpush
@endsection 
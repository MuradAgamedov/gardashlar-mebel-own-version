@extends('admin.layout.app')

@section('title', 'Product Colors')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Product Colors</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Product Colors</li>
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
                            <h5>Product Colors</h5>
                            <a href="{{ route('admin.product-colors.create') }}" class="btn btn-primary">
                                <i class="material-icons-outlined">add</i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Search Section -->
                    <div class="card-body border-bottom">
                        <form method="GET" action="{{ route('admin.product-colors.index') }}" class="row g-3">
                            <div class="col-md-6">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="Search by color code...">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="d-flex gap-2 w-100">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="material-icons-outlined">search</i>
                                    </button>
                                    <a href="{{ route('admin.product-colors.index') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="material-icons-outlined">clear</i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <div class="card-body">
                        <!-- Results Summary -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="text-muted">
                                Showing {{ $productColors->firstItem() ?? 0 }} to {{ $productColors->lastItem() ?? 0 }} of {{ $productColors->total() }} results
                            </div>
                            <div class="text-muted">
                                Page {{ $productColors->currentPage() }} of {{ $productColors->lastPage() }}
                            </div>
                            <button id="delete-selected-colors" class="btn btn-danger btn-sm ms-3" disabled>
                                <i class="material-icons-outlined">delete</i> Seçilənləri sil
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="30">
                                            <input type="checkbox" id="select-all-colors">
                                        </th>
                                        <th width="50">#</th>
                                        <th>Color Preview</th>
                                        <th>Color Code</th>
                                        <th>Created At</th>
                                        <th width="150">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($productColors as $productColor)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="color-checkbox" value="{{ $productColor->id }}">
                                            </td>
                                            <td>{{ $productColor->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="color-preview me-3" 
                                                         style="width: 40px; height: 40px; border-radius: 8px; background-color: {{ $productColor->color_code }}; border: 2px solid #ddd;">
                                                    </div>
                                                    <span class="fw-bold">{{ $productColor->color_code }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <code>{{ $productColor->color_code }}</code>
                                            </td>
                                            <td>
                                                {{ $productColor->created_at->format('M d, Y H:i') }}
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.product-colors.edit', $productColor) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="Edit">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    <form action="{{ route('admin.product-colors.destroy', $productColor) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this color?')">
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
                                                    <i class="material-icons-outlined" style="font-size: 48px;">palette</i>
                                                    <p class="mt-2">No colors added yet</p>
                                                    <a href="{{ route('admin.product-colors.create') }}" class="btn btn-primary">
                                                        Add First Color
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Bootstrap Pagination -->
                        @if($productColors->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $productColors->links('pagination::bootstrap-5') }}
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all functionality
    const selectAll = document.getElementById('select-all-colors');
    const checkboxes = document.querySelectorAll('.color-checkbox');
    const deleteBtn = document.getElementById('delete-selected-colors');

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
        if (!confirm('Seçilən rəngləri silmək istədiyinizə əminsiniz?')) return;
        
        fetch("{{ route('admin.product-colors.bulk-delete') }}", {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ids })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) location.reload();
            else alert(data.message);
        });
    });
});
</script>
@endpush 